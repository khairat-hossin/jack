"use strict"

var cart_items = [];

$(function() {
    filter_data();

    $(document).on('click', '.add_cart', function(event) {
        var button = $(this);
        var quantity = $(this).parents('.input_data').find('input[name="quantity"]').val();
        var max = $(this).parents('.input_data').find('input[name="quantity"]').attr('max');
        var variation_max = $(this).parents('.product-row').find('input.variation_quantity').attr('max');
        if (quantity <= 0 || !$.isNumeric(quantity)) {
            alert_float("danger", "Quantity Must Be Greater Than 0 ");
            return false;
        }
        var product_id = $(this).parents('.input_data').find('input[name="product_id"]').val();
        var product_variation_id = $(this).parents('.input_data').find('input[name="product_variation_id"]').val();
        if (!$(this).parents('.product-row').hasClass('without-variations') && !product_variation_id) {
            // alert_float("danger", `Please choose the variation for this Product`);
            // return false;
        }
        if (product_variation_id) {
            if (parseInt(quantity) > parseInt(variation_max)) {
                alert_float("danger", `Only ${variation_max} Items are in stock for this Product`);
                return false;
            }
        } else {
            if (parseInt(quantity) > parseInt(max)) {
                alert_float("danger", `Only ${max} Items are in stock for this Product`);
                return false;
            }
        }
        $.post(site_url+'products/client/add_cart', {quantity: quantity, product_id: product_id, product_variation_id: product_variation_id}, function(data, textStatus, xhr) {
            cart_items = $.parseJSON(data);
            let totalQuantity = 0;

            cart_items.forEach(item => {
                totalQuantity += parseInt(item.quantity);
            });
            $(".cart-badge").html(totalQuantity);
            button.text("UPDATE CART");
            alert_float("success", "Item Added to Cart");
        });
    });

    $(document).on('change', '#product_categories', function(event) {
        filter_data({'p_category_id': $(this).val()});
    });
});

function filter_data(post_data = {}) {
    $(".no_product").addClass('hidden');
    $("#filter_html").html("");
    $(".image_loader").show();
    $.ajax({
        url: site_url+'products/client/filter',
        type: 'POST',
        dataType: 'json',
        data : post_data,
        success : function (data) {
            render_product_data(data);
        }
    })
}

var product_variations = [];
function render_product_data(data) {
    var html = "";
    var total_taxes = "";
    cart_items = [];
    $.each(data, function(index, val) {
        var cart_data_quantity = "";
        var button = "";
        var product_class = "";

        if (val.cart_data) {
            cart_items.push(val.cart_data);
        }

        if (val.total_tax != 0) {
            total_taxes = `<span class='total_taxes text-warning'>(+ ${val.total_tax}% taxes)</span>`;
        } else {
            total_taxes = "";
        }

        if (parseInt(val.quantity_number) < 1 && val.is_digital != 1) {
            button = `<button class="btn btn-danger pull-right">${val.out_of_stock}</button>`;
        } else {
            var label  = val.add_to_cart;
            if (val.cart_data && !val.cart_data.product_variation_id && val.cart_data.quantity) {
                label  = val.update_cart;
                cart_data_quantity = val.cart_data.quantity;
            }
            button = `<button class="btn btn-warning add_cart pull-right">${label}</button>`
        }

        var max_attr = "";
        if (val.is_digital != 1) {
            max_attr = `max="${val.quantity_number}"`;
        }

        var recurring_type = "";
        var cycles_text = "&nbsp;";
        if (val.recurring != 0) {
            recurring_type = val.recurring_type;
            if (val.recurring_type == "") {
                recurring_type = "month";
            }
            recurring_type = "/ "+ ((val.recurring != 1) ? val.recurring:"") + ' '+ recurring_type;
            if (val.cycles == 0) {
                cycles_text = "Infinite recurring";
            }
            if (val.cycles == 1) {
                cycles_text = "1 time totally";
            }
            if (val.cycles > 1) {
                cycles_text = val.cycles+" times totally";
            }
        }
        var variations_content = "";
        var product_variation_ids = [];
        if (val.variations) {
            if (val.variations.length) {
                variations_content += '<div class="row variations">';
                variations_content += '<div class="col-md-6 col-sm-6 col-xs-6">';
                variations_content += '<select class="selectpicker variation_id">';
                variations_content += '<option value=""></option>';
            }
            for (var variation_index = 0; variation_index < val.variations.length; variation_index++) {
              if (!product_variation_ids.includes(val.variations[variation_index]['variation_id'])) {
                product_variation_ids.push(val.variations[variation_index]['variation_id']);
                variations_content += '<option value="' + val.variations[variation_index]['variation_id'] + '">' + val.variations[variation_index]['variation_name'] + '</option>';
              }
            }
            if (val.variations.length) {
                variations_content += '</select>';
                variations_content += '</div>';
                variations_content += '<div class="col-md-6 col-sm-6 col-xs-6">';
                variations_content += '<select class="selectpicker variation_value_id">';
                variations_content += '</select>';
                variations_content += '</div>';
                variations_content += '</div>';
            }
        } else {
            product_class = 'without-variations';
        }
        html += `<div class="col-lg-4 col-md-4 col-sm-4 col-xs-12 product-row ${product_class}">
            <div class="thumbnail shadow">
                <div>
                    <a href="${site_url}products/client/view_product/${val.id}"><img src="${val.product_image_url}" alt="${val.product_image}" class="img1" onerror="this.src='${val.no_image_url}'">
                    <br>
                    <div class="title text-center text-warning">
                        <h4>${val.product_name}</h4>
                    </div>
                    <div class="description">
                        <span><center>${val.product_description}</center></span>
                    </div></a>
                    <br>
                    <div>
                        <div class="text-center">${val.p_category_name}</div>
                    </div>
                    <div class="rates products-pricing">
                        <h4>${val.base_currency_name} ${val.rate} ${recurring_type} ${total_taxes}</h4>
                        <h5>${cycles_text} </h5>
                    </div>
                    ${variations_content}
                    <div class="row input_data" id="">
                        <div class="col-md-6 col-sm-6 col-xs-6 products-pricing">
                            <input type="number" name="quantity" min="1" ${max_attr} value="${cart_data_quantity}" class="form-control" placeholder="${val.qty}">
                            <input type="hidden" name="product_id" value="${val.id}" class="form-control">
                            <input type="hidden" name="product_variation_id" value="" class="form-control">
                            <input type="hidden" min="1" ${max_attr} class="form-control variation_quantity">
                        </div>
                        <div class="col-md-6 col-sm-6 col-xs-6 products-pricing">
                            ${button}
                        </div>
                    </div>
                </div>
            </div>   
        </div>`;
    });
    $("#filter_html").hide();
    $(".image_loader").hide();
    $("#filter_html").html(html).fadeIn('slow');
    if (html=="") {
        $(".no_product").removeClass('hidden');
    }
    
    $(document).on('change', '.selectpicker.variation_id' , function () {
        var that = this;
        $(this).parents('.product-row').find('input[name="product_variation_id"]').val('');
        $.ajax({
            url: site_url+'products/client/variation_values',
            type: 'POST',
            dataType: 'json',
            data : {
                'product_id': $(this).parents('.product-row').find('input[name="product_id"]').val(),
                'variation_id': $(this).parents('.product-row').find('.selectpicker.variation_id').val()
            },
            success : function (data) {
                render_product_variation_values_data(that, data);
            }
        });
    });
    
    //appSelectPicker();
}

function render_product_variation_values_data(el, data) {
    if (data) {
        var variation_values_html = '';
        if (data.length) variation_values_html += '<option value=""></option>';
        for (var variation_index = 0; variation_index < data.length; variation_index++) {
          if (data[variation_index]['variation_id'] == $(el).parents('.product-row').find('.selectpicker.variation_id').val()) {
            variation_values_html += '<option value="' + data[variation_index]['id'] + '" data-quantity="' + data[variation_index]['quantity_number'] + '">' + data[variation_index]['variation_value'] + '</option>';
          }
        }
    }
    $(el).parents('.product-row').find('.selectpicker.variation_value_id').html(variation_values_html);
    $(el).parents('.product-row').find('.selectpicker.variation_value_id').selectpicker("refresh");
    $(el).parents('.product-row').find('.add_cart').text("ADD TO CART");
    
    $(document).on('change', '.selectpicker.variation_value_id' , function () {
        var product_id = $(this).parents('.product-row').find('input[name="product_id"]').val();
        var product_variation_id = $(this).val();
        $(this).parents('.product-row').find('input[name="product_variation_id"]').val(product_variation_id);
        var variation_options = $(this).find('option');
        for (var variation_index = 0; variation_index < variation_options.length; variation_index++) {
            if ($(variation_options[variation_index]).val() == product_variation_id) {
                $(this).parents('.product-row').find('input.variation_quantity').attr('max', $(variation_options[variation_index]).data('quantity'));
            }
        }
        var variation_added = false;
        for (var cart_item_index = 0; cart_item_index < cart_items.length; cart_item_index++) {
            if (cart_items[cart_item_index].product_id == product_id && 
                cart_items[cart_item_index].product_variation_id == product_variation_id) {
                variation_added = true;
            }
        }
        if (variation_added) {
            $(this).parents('.product-row').find('.add_cart').text("UPDATE CART");
        } else {
            $(this).parents('.product-row').find('.add_cart').text("ADD TO CART");
        }
    });
}