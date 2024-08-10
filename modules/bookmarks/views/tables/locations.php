<?php defined('BASEPATH') or exit('No direct script access allowed');
$aColumns = ['location_name', 'description', 'ordering', db_prefix() . 'bookmark_locations.staff_id', 'created_date', db_prefix() . 'bookmark_locations.only_me'];
$sIndexColumn = 'id';
$sTable = db_prefix() . 'bookmark_locations';
$where = [];
$filter = [];
$join = ['LEFT JOIN ' . db_prefix() . 'staff ON ' . db_prefix() . 'staff.staffid = ' . db_prefix() . 'bookmark_locations.staff_id', ];
$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, [], ['id']);
$output = $result['output'];
$rResult = $result['rResult'];
foreach ($rResult as $aRow)
{
    if (($aRow[db_prefix() . 'bookmark_locations.staff_id'] == get_staff_user_id()) || ($aRow[db_prefix() . 'bookmark_locations.staff_id'] != get_staff_user_id() && $aRow[db_prefix() . 'bookmark_locations.only_me'] != 1))
    {
        $row = [];
        for ($i = 0;$i < count($aColumns);$i++)
        {
            $_data = $aRow[$aColumns[$i]];
            if ($aColumns[$i] == 'location_name')
            {
                $_data = $aRow['location_name'];
                $_data .= '<div class="row-options">';
                if ($aRow[db_prefix() . 'bookmark_locations.staff_id'] == get_staff_user_id() || (has_permission('bookmarks', '', 'edit') && $aRow[db_prefix() . 'bookmark_locations.staff_id'] != get_staff_user_id()))
                {
                    $_data .= '<a href="#" onclick="init_location_modal(' . $aRow['id'] . '); return false;">' . _l('edit') . '</a>';
                }
                if ($aRow[db_prefix() . 'bookmark_locations.staff_id'] == get_staff_user_id() || (has_permission('bookmarks', '', 'delete') && $aRow[db_prefix() . 'bookmark_locations.staff_id'] != get_staff_user_id()))
                {
                    $_data .= ' | <a href="' . admin_url('bookmarks/delete_location/' . $aRow['id']) . '" class="_delete text-danger">' . _l('delete') . '</a></div>';
                }
            }
            if ($aColumns[$i] == 'created_date')
            {
                $_data = _dt($aRow['created_date']);
            }
            if ($aColumns[$i] == db_prefix() . 'bookmark_locations.staff_id')
            {
                $_data = $_data = '<a data-toggle="tooltip" data-title="' . get_staff_full_name($aRow[db_prefix() . 'bookmark_locations.staff_id']) . '" href="' . admin_url('profile/' . $aRow[db_prefix() . 'bookmark_locations.staff_id']) . '">' . staff_profile_image($aRow[db_prefix() . 'bookmark_locations.staff_id'], ['staff-profile-image-small']) . ' ' . get_staff_full_name($aRow[db_prefix() . 'bookmark_locations.staff_id']) . '</a>';
            }
            if ($aColumns[$i] == db_prefix() . 'bookmark_locations.only_me')
            {
                if ($aRow[db_prefix() . 'bookmark_locations.only_me'] == 0)
                {
                    $_data = _l('bookmark_public');
                }
                else
                {
                    $_data = _l('bookmark_only_me');
                }
            }
            $row[] = $_data;
        }
        $row['DT_RowClass'] = 'has-row-options';
        $output['aaData'][] = $row;
    }
}

