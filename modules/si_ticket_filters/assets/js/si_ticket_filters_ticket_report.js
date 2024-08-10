(function($) {
"use strict";
var _rel_id = $('#si_tf_rel_id'),_rel_type = $('#si_tf_rel_type'),_si_tf_rel_id_wrapper = $('#si_tf_rel_id_wrapper'),data = {};
$('.si_tf_rel_id_label').html(_rel_type.find('option:selected').text());
_rel_type.on('change', function() {
	 var clonedSelect = _rel_id.html('').clone();
	 _rel_id.selectpicker('destroy').remove();
	 _rel_id = clonedSelect;
	 $('#si_tf_rel_id_select').append(clonedSelect);
	 $('.si_tf_rel_id_label').html(_rel_type.find('option:selected').text());
	 si_tf_rel_select();
	 if($(this).val() != ''){
	  _si_tf_rel_id_wrapper.removeClass('hide');
	} else {
	  _si_tf_rel_id_wrapper.addClass('hide');
	}
});
si_tf_rel_select();
function si_tf_rel_select(){
	var serverData = {};
	serverData.rel_id = _rel_id.val();
	data.type = _rel_type.val();
	if(_rel_type.val()=='customer')
	{
		$('#si_tf_group_id_wrapper').removeClass('hide');
	} else {
		$('#si_tf_group_id_wrapper').addClass('hide');
	}
	init_ajax_search(_rel_type.val(),_rel_id,serverData);
}
$('#report_months').on('change', function() {
     var val = $(this).val();
	 var report_from = $('#report_from');
	 var report_to = $('#report_to');
	 var date_range = $('#date-range');
	 
     report_to.val('');
     report_from.val('');
     if (val == 'custom') {
       date_range.addClass('fadeIn').removeClass('hide');
       return;
     } else {
       if (!date_range.hasClass('hide')) {
         date_range.removeClass('fadeIn').addClass('hide');
       }
     }
	 	
});
$('#si_save_filter').on('click',function(){
	var checked = this.checked;
	$('#si_filter_name').attr('disabled',!checked);
});
$('#si_form_ticket_filter').on('submit',function(){
	if($('#si_save_filter').is(":checked") && $('#si_filter_name').val()=='')
	{
		$('#si_filter_name').focus();
		return false;
	}
});
$(document).ready(function() {
	$('.dt-table').each(function(i,a) {
		var table = $(a).DataTable();
		var hide_view = [];
		$('.dt-table thead tr th').each(function(i,a) { 
			if( $(this).hasClass('not-export'))
				hide_view.push($(this).index());	
		});
		table.button().add( 0, 'colvis' );
		table.columns( hide_view ).visible( false );
		$('.buttons-colvis').addClass('btn-sm');//for Perfex version 3.0
	});
});
$(".buttons-colvis").text("Columns");
})(jQuery);	