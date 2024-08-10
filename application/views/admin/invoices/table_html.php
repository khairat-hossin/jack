<?php defined('BASEPATH') or exit('No direct script access allowed');

$table_data = array(
    
  //Customer name
    array(
        'name'=>_l('invoice_dt_table_heading_client'),
        'th_attrs'=>array('class'=>(isset($client) ? 'not_visible' : ''))
    ),
  //Invoice #
  _l('invoice_dt_table_heading_number'),
  // Subtotal
  'Sub Total',
  //Discount
  'Discount',
  
  //Tax
  'Tax',
  //Total
  'Total',
  //Amount Due
  'Amount Due',
  //notes
  'Notes',
// Year
//   array(
//     'name'=>_l('invoice_estimate_year'),
//     'th_attrs'=>array('class'=>'not_visible')
//   ),
  //Create Date
  _l('invoice_dt_table_heading_date'),
  //Due date
  _l('invoice_dt_table_heading_duedate'),
  //Project
  array(
        'name'=>_l('project'),
        'th_attrs'=>array('class'=>(isset($client) ? 'not_visible' : '')) //this function is used for hiding in contact mode https://crm.globalpresence.support/admin/clients/client/971?group=invoices
    ),
//   _l('tags'), //Removed not needed
  //Status
  _l('invoice_dt_table_heading_status'));
$custom_fields = get_custom_fields('invoice',array('show_on_table'=>1));
foreach($custom_fields as $field){
  array_push($table_data, [
   'name' => $field['name'],
   'th_attrs' => array('data-type'=>$field['type'], 'data-custom-field'=>1)
 ]);
}
$table_data = hooks()->apply_filters('invoices_table_columns', $table_data);
render_datatable($table_data, (isset($class) ? $class : 'invoices'), [], ['id'=>$table_id ?? 'invoices']);
?>
