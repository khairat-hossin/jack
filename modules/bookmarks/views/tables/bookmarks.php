<?php defined('BASEPATH') or exit('No direct script access allowed');

$aColumns = ['1', 'title', 'url', db_prefix() . 'bookmarks.description', db_prefix() . 'bookmark_locations.location_name', db_prefix() . 'bookmarks.staff_id', db_prefix() . 'bookmarks.created_date', '(SELECT GROUP_CONCAT(name SEPARATOR ",") FROM ' . db_prefix() . 'taggables JOIN ' . db_prefix() . 'tags ON ' . db_prefix() . 'taggables.tag_id = ' . db_prefix() . 'tags.id WHERE rel_id = ' . db_prefix() . 'bookmarks.id and rel_type="bookmark" ORDER by tag_order ASC) as tags', db_prefix() . 'bookmarks.only_me', ];
$sIndexColumn = 'id';
$sTable = db_prefix() . 'bookmarks';
$where = [];
$filter = [];
$filtered_locations = $this
    ->ci
    ->bookmarks_model
    ->get_filtered_locations();
$location_ids = [];
foreach ($filtered_locations as $location)
{
    if ($this
        ->ci
        ->input
        ->post('location_' . $location['id']))
    {
        array_push($location_ids, $location['id']);
    }
}
if (count($location_ids) > 0)
{
    array_push($filter, 'AND location_id IN (' . implode(',', $location_ids) . ')');
}
$filtered_staffs = $this
    ->ci
    ->bookmarks_model
    ->get_filtered_staffs();
$staff_ids = [];
foreach ($filtered_staffs as $staff)
{
    if ($this
        ->ci
        ->input
        ->post('staff_' . $staff['staffid']))
    {
        array_push($staff_ids, $staff['staffid']);
    }
}
if (count($staff_ids) > 0)
{
    array_push($filter, 'AND ' . db_prefix() . 'bookmarks.staff_id IN (' . implode(',', $staff_ids) . ')');
}
if (count($filter) > 0)
{
    array_push($where, 'AND (' . prepare_dt_filter($filter) . ')');
}
$join = ['LEFT JOIN ' . db_prefix() . 'staff ON ' . db_prefix() . 'staff.staffid = ' . db_prefix() . 'bookmarks.staff_id', 'LEFT JOIN ' . db_prefix() . 'bookmark_locations ON ' . db_prefix() . 'bookmark_locations.id = ' . db_prefix() . 'bookmarks.location_id', ];
$additional_select = [db_prefix() . 'bookmarks.id', ];
$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, $additional_select);
$output = $result['output'];
$rResult = $result['rResult'];
foreach ($rResult as $aRow)
{
    if (($aRow[db_prefix() . 'bookmarks.staff_id'] == get_staff_user_id()) || ($aRow[db_prefix() . 'bookmarks.staff_id'] != get_staff_user_id() && $aRow[db_prefix() . 'bookmarks.only_me'] != 1))
    {
        $row = [];
        $row[] = '<div class="checkbox"><input type="checkbox" value="' . $aRow['id'] . '"><label></label></div>';
        $title = '<a target="_blank" href="' . $aRow['url'] . '">' . $aRow['title'] . '</a>';
        $title .= '<div class="row-options">';
        $title .= '<a href="#" onclick="ClipboardHelper.copyText(\'' . $aRow['url'] . '\'); return false;">' . _l('bookmark_copy_url') . '</a>';
        if ($aRow[db_prefix() . 'bookmarks.staff_id'] == get_staff_user_id() || (has_permission('bookmarks', '', 'edit') && $aRow[db_prefix() . 'bookmarks.staff_id'] != get_staff_user_id()))
        {
            $title .= ' | <a href="#" onclick="init_bookmark_modal(' . $aRow['id'] . '); return false;">' . _l('edit') . '</a>';
        }
        if ($aRow[db_prefix() . 'bookmarks.staff_id'] == get_staff_user_id() || (has_permission('bookmarks', '', 'delete') && $aRow[db_prefix() . 'bookmarks.staff_id'] != get_staff_user_id()))
        {
            $title .= ' | <a href="' . admin_url('bookmarks/delete/' . $aRow['id']) . '" class="_delete text-danger">' . _l('delete') . '</a></div>';
        }
        $row[] = $title;
        $row[] = '<a target="_blank" href="' . $aRow['url'] . '">' . $aRow['url'] . '</a>';
        $row[] = $aRow[db_prefix() . 'bookmarks.description'];
        $row[] = $aRow[db_prefix() . 'bookmark_locations.location_name'];
        $row[] = '<a data-toggle="tooltip" data-title="' . get_staff_full_name($aRow[db_prefix() . 'bookmarks.staff_id']) . '" href="' . admin_url('profile/' . $aRow[db_prefix() . 'bookmarks.staff_id']) . '">' . staff_profile_image($aRow[db_prefix() . 'bookmarks.staff_id'], ['staff-profile-image-small']) . ' ' . get_staff_full_name($aRow[db_prefix() . 'bookmarks.staff_id']) . '</a>';
        $row[] = _dt($aRow[db_prefix() . 'bookmarks.created_date']);
        $row[] = render_tags($aRow['tags']);
        if ($aRow[db_prefix() . 'bookmarks.only_me'] == 0)
        {
            $row[] = _l('bookmark_public');
        }
        else
        {
            $row[] = _l('bookmark_only_me');
        }
        $row['DT_RowClass'] = 'has-row-options';
        $output['aaData'][] = $row;
    }
}

