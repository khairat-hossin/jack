<?php

defined('BASEPATH') or exit('No direct script access allowed');
$aColumns = [
    'name',
];
$sIndexColumn = 'id';
$sTable       = db_prefix().'variations';
$filter       = [];
$where        = [];
$statusIds    = [];
$join         = [];
$result  = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, ['id']);
$output  = $result['output'];
$rResult = $result['rResult'];

foreach ($rResult as $aRow) {
    $row        = [];
    $outputName = '<a href="#">'.$aRow['name'].'</a>';
    $outputName .= '<div class="row-options">';
    if (has_permission('products', '', 'delete')) {
        $outputName .= ' <a href="'.admin_url('products/variations/edit/'.$aRow['id']).'" class="_edit">'._l('edit').'</a>';
        $outputName .= '| <a href="'.admin_url('products/variations/delete/'.$aRow['id']).'" class="text-danger _delete">'._l('delete').'</a>';
    }
    $outputName .= '</div>';
    $row[]              = $outputName;
    $row[]              = get_variation_values($aRow['id']);
    $row['DT_RowClass'] = 'has-row-options';
    $output['aaData'][] = $row;
}