"use strict";
$(function() {
    var update_cart, rate, quantity, max, product_id, product_variation_id, amount_text, coupon_code, removing = false;
    $(".quantity").change(function(event) {
        var that = $(this);
        setTimeout(function() {
            if (removing) return;
    
            quantity = that.val();
            max = that.attr('max');
            if (quantity <= 0 || !$.isNumeric(quantity)) {
                alert_float("danger","Quantity Must Be Greater Than 0 ");
                that.val(that.data('old-qty'));
                return false;
            }
            if (parseInt(quantity) > parseInt(max)) {
                alert_float("danger",`Only ${max} Items are in stock for this Product`);
                that.val(that.data('old-qty'));
                return false;
            }
            if (update_cart) {
                update_cart.abort();
            }
            product_id = that.data('product_id');
            product_variation_id = that.data('product_variation_id');
            update_cart = $.post(site_url+'products/client/add_cart', {quantity: quantity, product_id: product_id, product_variation_id: product_variation_id}, function(data, textStatus, xhr) {});
            rate = that.data('rate');
            that.data('old-qty',quantity);
            amount_text = rate * quantity;
            that.parents('tr').children('.amount').text(format_money(amount_text));
            var total = 0; 
            var subtotal = 0;
            var tax_values = []; 
            var taxes = {}; 
            var amount = 0;
            var tax_row = '';
            var apply_shipping_count = 0;
            $('.tax-area').remove();
            $(".quantity").each(function(index, el) {
                rate = $(this).data('rate');
                var apply_shipping = $(this).data('apply_shipping');
                if (apply_shipping) {
                    apply_shipping_count++;
                }
                tax_values = $(this).parents('td').find('.product_taxes').selectpicker('val');
                amount = rate * $(this).val();
                subtotal+= amount;
                total += amount;
                $.each(tax_values, function(i, taxname) {
                    var tax_name = taxname.split('|');
                    var calculated_tax = (amount / 100 * tax_name[1]);
                    if (taxes[taxname]) {
                        taxes[taxname] = parseFloat(taxes[taxname]) + parseFloat(calculated_tax);
                    } else {
                        taxes[taxname] = parseFloat(calculated_tax);
                    }
                });
            });
    
            $.each(taxes, function(taxname, total_tax) {
                total += total_tax;
                var tax_name = taxname.split('|');
                total_tax = format_money(total_tax);
                tax_row += `<tr class="tax-area">
                                <td>
                                    <span class="bold">${tax_name[0]}(${tax_name[1]}%)</span>
                                </td>
                                <td id="tax_id_${slugify(taxname)}">${total_tax}</td>
                            </tr>`;
                $('#tax_id_' + slugify(taxname)).html(total_tax);
            });
            $('#subtotal').after(tax_row);
    
            var shipping_costs = $("#shipping_costs").find(`input[name="shipping_cost"]`).val();
            if (typeof shipping_costs==="undefined") {
                shipping_costs=0;
            }
            if (apply_shipping_count >= 1) {
                total = total + parseInt(shipping_costs);
                $("#shipping_costs").show();
            }
            if (apply_shipping_count == 0) {
                $("#shipping_costs").hide();
            }
    
            $(".total").text(format_money(total))
            $(".subtotal").text(format_money(subtotal));
        }, 500);
    });
    $(".remove_cart").on('click', function(event) {
        event.preventDefault();

        removing = true;
        
        product_id = $(this).data('product_id');
        product_variation_id = $(this).data('product_variation_id');
        var button = $(this);
        $.post(site_url+'products/client/remove_cart', {product_id: product_id, product_variation_id: product_variation_id}, function(data, textStatus, xhr) {
            removing = false;

            data = $.parseJSON(data);
            let totalQuantity = 0;

            data.cart_data.forEach(item => {
                totalQuantity += parseInt(item.quantity);
            });
            $(".cart-badge").html(totalQuantity);
            if (data.status == false) {
                window.location.href = site_url+'products/client';
                return false;
            }
            var next_tr = button.parents('tbody').children('tr');
            if (next_tr.length != 0) {
                button.parents('tr').remove();
                next_tr.find('.quantity').change();
                alert_float("success","Item Removed from Cart");
            }
        });
    });
    $(".apply_coupon").on('click', function(event) {
        var button;
        button = $(this);
        coupon_code = $('#coupon_code').val();
        $.post(site_url+'products/client/apply_coupon', {coupon_code: coupon_code}, function(data, textStatus, xhr) {
            data = $.parseJSON(data);
            if (data.status == false) {
                $('#coupon_discount').addClass('hide');
                $('#coupon_discount input[name="coupon_id"]').val('');
                alert_float("danger", data.message);
                return false;
            }
            $('#coupon_discount input[name="coupon_id"]').val(data.coupon_id);
            $('#coupon_discount .coupon_discount').html(data.coupon_discount);
            $('.total').html(data.total);
            $('#coupon_discount').removeClass('hide');
            $('button.apply_coupon').addClass('hide');
            $('button.remove_coupon').removeClass('hide');
        });
    });
    $(".remove_coupon").on('click', function(event) {
        var button;
        button = $(this);
        $.post(site_url+'products/client/remove_coupon', function(data, textStatus, xhr) {
            data = $.parseJSON(data);
            $('#coupon_code').val('');
            $('#coupon_discount input[name="coupon_id"]').val('');
            $('#coupon_discount .coupon_discount').html('');
            $('.total').html(data.total);
            $('#coupon_discount').addClass('hide');
            $('button.apply_coupon').removeClass('hide');
            $('button.remove_coupon').addClass('hide');
        });
    });
});
init_currency();
function format_money(total, excludeSymbol) {
    if (typeof(excludeSymbol) != 'undefined' && excludeSymbol) {
        return accounting.formatMoney(total, { symbol: '' });
    }
    return accounting.formatMoney(total);
}
function init_currency() {
    $.get(site_url + 'products/client/get_currency/'+base_currency)
        .done(function(currency) {
            currency = $.parseJSON(currency);
            accounting.settings.currency.decimal = currency.decimal_separator;
            accounting.settings.currency.thousand = currency.thousand_separator;
            accounting.settings.currency.symbol = currency.symbol;
            accounting.settings.currency.format = currency.placement == 'after' ? '%v %s' : '%s%v';
        });
}