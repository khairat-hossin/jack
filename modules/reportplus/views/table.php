<?php

defined('BASEPATH') or exit('No direct script access allowed');

$aColumns = [
    'email_to',
    'generate_dates',
    'created_at',
];

$sIndexColumn = 'id';
$sTable       = db_prefix() . 'reportplus_generated_reports';

$join = [];
$where = [];

$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
    'id'
]);

$output  = $result['output'];
$rResult = $result['rResult'];


foreach ($rResult as $aRow) {
    $row = [];
    for ($i = 0; $i < count($aColumns); $i++) {

        $row[] = $aRow['email_to'];

        $row[] = $aRow['generate_dates'];

        $row[] = $aRow['created_at'];

        $options = '<div class="tw-flex tw-items-center tw-space-x-3">';
        $options .= '<a href="' . admin_url('reportplus/view_report/' . $aRow['id']) . '" class="tw-text-neutral-500 hover:tw-text-neutral-700 focus:tw-text-neutral-700">
        <i class="fas fa-eye fa-lg"></i>
    </a>';

        $options .= '<a href="' . admin_url('reportplus/delete_report/' . $aRow['id']) . '"
    class="tw-mt-px tw-text-neutral-500 hover:tw-text-neutral-700 focus:tw-text-neutral-700 _delete">
        <i class="fa-regular fa-trash-can fa-lg"></i>
    </a>';

        $options .= '</div>';

        $row[]              = $options;
    }

    $output['aaData'][] = $row;
}
