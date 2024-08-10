<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Bookmarks_model extends App_Model
{
    public function __construct()
    {
        parent::__construct();
    }
    /**
     * @param integer (optional)
     * @return object
     * Get single bookmark
     */
    public function get($id = '')
    {
        if (is_numeric($id))
        {
            $this
                ->db
                ->where('id', $id);
            return $this
                ->db
                ->get(db_prefix() . 'bookmarks')
                ->row();
        }
        return $this
            ->db
            ->get(db_prefix() . 'bookmarks')
            ->result_array();
    }
    /**
     * Add new bookmark
     * @param mixed $data All $_POST dat
     * @return mixed
     */
    public function add($data)
    {
        $tags = isset($data['tags']) ? $data['tags'] : '';
        unset($data['tags']);
        $data['staff_id'] = get_staff_user_id();
        $data['created_date'] = date('Y-m-d H:i:s');
        $this
            ->db
            ->insert(db_prefix() . 'bookmarks', $data);
        $insert_id = $this
            ->db
            ->insert_id();
        handle_tags_save($tags, $insert_id, 'bookmark');
        if ($insert_id)
        {
            return $insert_id;
        }
        return false;
    }
    /**
     * Update bookmark
     * @param mixed $data All $_POST data
     * @param mixed $id bookmark id
     * @return boolean
     */
    public function update($data, $id)
    {
        $tags = isset($data['tags']) ? $data['tags'] : '';
        unset($data['tags']);
        $this
            ->db
            ->where('id', $id);
        $this
            ->db
            ->update(db_prefix() . 'bookmarks', $data);
        $bookmark_updated = $this
            ->db
            ->affected_rows();
        handle_tags_save($tags, $id, 'bookmark');
        if ($bookmark_updated > 0)
        {
            return true;
        }
        return false;
    }
    /**
     * Delete bookmark
     * @param mixed $id bookmark id
     * @return boolean
     */
    public function delete($id)
    {
        $this
            ->db
            ->where('id', $id);
        $this
            ->db
            ->delete(db_prefix() . 'bookmarks');
        if ($this
            ->db
            ->affected_rows() > 0)
        {
            return true;
        }
        return false;
    }
    /**
     * @param integer (optional)
     * @return object
     * Get single location
     */
    public function get_location($id = '')
    {
        if (is_numeric($id))
        {
            $this
                ->db
                ->where('id', $id);
            return $this
                ->db
                ->get(db_prefix() . 'bookmark_locations')
                ->row();
        }
        return $this
            ->db
            ->get(db_prefix() . 'bookmark_locations')
            ->result_array();
    }
    /**
     * Add new location
     * @param mixed $data All $_POST dat
     * @return mixed
     */
    public function add_location($data)
    {
        $data['staff_id'] = get_staff_user_id();
        $data['created_date'] = date('Y-m-d H:i:s');
        $this
            ->db
            ->insert(db_prefix() . 'bookmark_locations', $data);
        $insert_id = $this
            ->db
            ->insert_id();
        if ($insert_id)
        {
            return $insert_id;
        }
        return false;
    }
    /**
     * Update location
     * @param mixed $data All $_POST data
     * @param mixed $id location id
     * @return boolean
     */
    public function update_location($data, $id)
    {
        $this
            ->db
            ->where('id', $id);
        $this
            ->db
            ->update(db_prefix() . 'bookmark_locations', $data);
        if ($this
            ->db
            ->affected_rows() > 0)
        {
            return true;
        }
        return false;
    }
    /**
     * Delete location
     * @param mixed $id location id
     * @return boolean
     */
    public function delete_location($id)
    {
        $this
            ->db
            ->where('id', $id);
        $this
            ->db
            ->delete(db_prefix() . 'bookmark_locations');
        if ($this
            ->db
            ->affected_rows() > 0)
        {
            return true;
        }
        return false;
    }
    public function get_filtered_locations()
    {
        $location_ids = $this
            ->db
            ->query('SELECT DISTINCT(location_id) as filtered_locations FROM ' . db_prefix() . 'bookmarks')
            ->result_array();
        foreach ($location_ids as $location)
        {
            $location_data[] = $location['filtered_locations'];
        }
        if (!empty($location_data))
        {
            return $this
                ->db
                ->query('SELECT * FROM ' . db_prefix() . 'bookmark_locations WHERE id IN (' . implode(',', $location_data) . ') ORDER BY ordering ASC')->result_array();
        }
        else
        {
            return [];
        }
    }
    public function get_filtered_staffs()
    {
        $staff_ids = $this
            ->db
            ->query('SELECT DISTINCT(staff_id) as filtered_staffs FROM ' . db_prefix() . 'bookmarks')
            ->result_array();
        foreach ($staff_ids as $staff)
        {
            $staff_data[] = $staff['filtered_staffs'];
        }
        if (!empty($staff_data))
        {
            return $this
                ->db
                ->query('SELECT * FROM ' . db_prefix() . 'staff WHERE staffid IN (' . implode(',', $staff_data) . ')')->result_array();
        }
        else
        {
            return [];
        }
    }
}
