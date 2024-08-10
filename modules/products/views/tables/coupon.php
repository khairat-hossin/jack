<?php

defined('BASEPATH') or exit('No direct script access allowed');
$aColumns = [
    'code',
    'type',
    'amount',
    'max_uses',
    'max_uses_per_client',
    'start_date',
    'end_date',
];
$sIndexColumn = 'id';
$sTable       = db_prefix().'coupons';
$filter       = [];
$where        = [];
$statusIds    = [];
$join         = [];
$result  = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, ['id']);
$output  = $result['output'];
$rResult = $result['rResult'];

foreach ($rResult as $aRow) {
    $row        = [];
    $outputCode = '<a href="#">'.$aRow['code'].'</a>';
    $outputCode .= '<div class="row-options">';
    if (has_permission('products', '', 'delete')) {
        $outputCode .= ' <a href="'.admin_url('products/coupons/edit/'.$aRow['id']).'" class="_edit">'._l('edit').'</a>';
        $outputCode .= '| <a href="'.admin_url('products/coupons/delete/'.$aRow['id']).'" class="text-danger _delete">'._l('delete').'</a>';
    }
    $outputCode .= '</div>';
    $row[]              = $outputCode;
    $row[]              = $aRow['type'];
    $row[]              = $aRow['amount'];
    $row[]              = $aRow['max_uses'];
    $row[]              = $aRow['max_uses_per_client'];
    $row[]              = _d($aRow['start_date']);
    $row[]              = _d($aRow['end_date']);
    $row[]              = get_coupon_used_times($aRow['id']);
    $row['DT_RowClass'] = 'has-row-options';
    $output['aaData'][] = $row;
}