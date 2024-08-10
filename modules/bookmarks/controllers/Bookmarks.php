<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Bookmarks extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this
            ->load
            ->model('bookmarks_model');
    }
    public function index()
    {
        if ($this
            ->input
            ->is_ajax_request())
        {
            $this
                ->app
                ->get_table_data(module_views_path(BOOKMARKS_MODULE_NAME, 'tables/bookmarks'));
        }
        $data['title'] = _l('bookmarks');
        $data['locations'] = $this
            ->bookmarks_model
            ->get_location();
        $data['filtered_locations'] = $this
            ->bookmarks_model
            ->get_filtered_locations();
        $data['filtered_staffs'] = $this
            ->bookmarks_model
            ->get_filtered_staffs();
        $this
            ->load
            ->view('bookmarks', $data);
    }
    public function save()
    {
        $data = $this
            ->input
            ->post();
        if (empty($data['id']))
        {
            $id = $this
                ->bookmarks_model
                ->add($data);
            if ($id)
            {
                set_alert('success', _l('bookmark_has_been_added_successfully', _l('bookmark')));
                redirect(admin_url('bookmarks'));
            }
        }
        else
        {
            $success = $this
                ->bookmarks_model
                ->update($data, $data['id']);
            if ($success)
            {
                set_alert('success', _l('bookmark_has_been_updated_successfully', _l('bookmark')));
            }
            redirect(admin_url('bookmarks'));
        }
    }
    /* Delete bookmark from database */
    public function delete($id = '')
    {
        if (!$id)
        {
            redirect(admin_url('bookmarks'));
        }
        $response = $this
            ->bookmarks_model
            ->delete($id);
        if ($response == true)
        {
            set_alert('success', _l('bookmark_has_been_deleted_successfully', _l('bookmark')));
        }
        else
        {
            set_alert('warning', _l('problem_deleting', _l('bookmark')));
        }
        redirect(admin_url('bookmarks'));
    }
    public function locations()
    {
        if ($this
            ->input
            ->is_ajax_request())
        {
            $this
                ->app
                ->get_table_data(module_views_path(BOOKMARKS_MODULE_NAME, 'tables/locations'));
        }
        $data['title'] = _l('bookmark_locations');
        $this
            ->load
            ->view('locations', $data);
    }
    public function bookmark_form($id = '')
    {
        $bookmark = $this
            ->bookmarks_model
            ->get($id);
        $locations = $this
            ->bookmarks_model
            ->get_location();
        $bookmark_url = '';
        if (!empty($bookmark)) $bookmark_url = $bookmark->url;
        echo render_input('url', 'bookmark_url', $bookmark_url, 'text');
        $bookmark_title = '';
        if (!empty($bookmark)) $bookmark_title = $bookmark->title;
        echo render_input('title', 'bookmark_title', $bookmark_title, 'text');
        $bookmark_description = '';
        if (!empty($bookmark)) $bookmark_description = $bookmark->description;
        echo render_textarea('description', 'bookmark_description', $bookmark_description);
        if (!empty($bookmark))
        {
            $selected = $bookmark->location_id;
        }
        else
        {
            $selected = '';
        }
        echo render_select('location_id', $locations, array(
            'id',
            'location_name'
        ) , 'bookmark_location', $selected);
        $onlyme_1 = '';
        $onlyme_0 = '';
        if (!empty($bookmark))
        {
            if ($bookmark->only_me == 1)
            {
                $onlyme_1 = 'checked';
                $onlyme_0 = '';
            }
            else
            {
                $onlyme_1 = '';
                $onlyme_0 = 'checked';
            }
        }
        echo '
			<div class="form-group">
				<label for="only_me" class="control-label clearfix">' . _l('bookmark_privacy') . '</label>
				<div class="radio radio-primary radio-inline">
					<input ' . $onlyme_1 . ' type="radio" id="bookmark_privacy_1" name="only_me" value="1">
					<label for="bookmark_privacy_1">' . _l('bookmark_only_me') . '</label>
				</div>
				<div class="radio radio-primary radio-inline">
					<input ' . $onlyme_0 . ' type="radio" id="bookmark_privacy_0" name="only_me" value="0">
					<label for="bookmark_privacy_0">' . _l('bookmark_public') . '</label>
				</div>
			</div>
		';
        $tags = [];
        if (!empty($bookmark))
        {
            $tags = get_tags_in($bookmark->id, 'bookmark');
        }
        echo '
			<div class="form-group no-mbot">
			   <label for="tags" class="control-label"><i class="fa fa-tag" aria-hidden="true"></i> ' . _l('tags') . '</label>
			   <input type="text" class="tagsinput" id="tags" name="tags" value="' . html_escape(implode(', ', $tags)) . '" data-role="tagsinput">
			</div>
		';
        echo form_hidden('id', $id);
    }
    public function location_form($id = '')
    {
        $location = $this
            ->bookmarks_model
            ->get_location($id);
        $location_name = '';
        if (!empty($location)) $location_name = $location->location_name;
        echo render_input('location_name', 'bookmark_location_name', $location_name, 'text');
        $location_description = '';
        if (!empty($location)) $location_description = $location->description;
        echo render_textarea('description', 'bookmark_location_description', $location_description);
        $location_order = '';
        if (!empty($location)) $location_order = $location->ordering;
        echo render_input('ordering', 'bookmark_location_ordering', $location_order, 'number');
        echo form_hidden('id', $id);
    }
    public function save_location()
    {
        $data = $this
            ->input
            ->post();
        if (empty($data['id']))
        {
            $id = $this
                ->bookmarks_model
                ->add_location($data);
            if ($id)
            {
                set_alert('success', _l('bookmark_has_been_added_successfully', _l('bookmark_location')));
                redirect(admin_url('bookmarks/locations'));
            }
        }
        else
        {
            $success = $this
                ->bookmarks_model
                ->update_location($data, $data['id']);
            if ($success)
            {
                set_alert('success', _l('bookmark_has_been_updated_successfully', _l('bookmark_location')));
            }
            redirect(admin_url('bookmarks/locations'));
        }
    }
    /* Delete bookmark location from database */
    public function delete_location($id = '')
    {
        if (!$id)
        {
            redirect(admin_url('locations'));
        }
        $response = $this
            ->bookmarks_model
            ->delete_location($id);
        if ($response == true)
        {
            set_alert('success', _l('bookmark_has_been_deleted_successfully', _l('bookmark_location')));
        }
        else
        {
            set_alert('warning', _l('problem_deleting', _l('bookmark_location')));
        }
        redirect(admin_url('bookmarks/locations'));
    }
    public function bulk_action()
    {
        if (!is_staff_member())
        {
            ajax_access_denied();
        }
        hooks()->do_action('before_do_bulk_action_for_bookmarks');
        $total_deleted = 0;
        if ($this
            ->input
            ->post())
        {
            $ids = $this
                ->input
                ->post('ids');
            $location = $this
                ->input
                ->post('location');
            $visibility = $this
                ->input
                ->post('visibility');
            $tags = $this
                ->input
                ->post('tags');
            $has_permission_delete = has_permission('bookmarks', '', 'delete');
            if (is_array($ids))
            {
                foreach ($ids as $id)
                {
                    if ($this
                        ->input
                        ->post('mass_delete'))
                    {
                        if ($has_permission_delete)
                        {
                            if ($this
                                ->bookmarks_model
                                ->delete($id))
                            {
                                $total_deleted++;
                            }
                        }
                    }
                    else
                    {
                        if ($location || $visibility)
                        {
                            $update = [];
                            if ($location)
                            {
                                $update['location_id'] = $location;
                            }
                            if ($visibility)
                            {
                                if ($visibility == 'public')
                                {
                                    $update['only_me'] = 0;
                                }
                                else
                                {
                                    $update['only_me'] = 1;
                                }
                            }
                            if (count($update) > 0)
                            {
                                $this
                                    ->db
                                    ->where('id', $id);
                                $this
                                    ->db
                                    ->update(db_prefix() . 'bookmarks', $update);
                            }
                        }
                        if ($tags)
                        {
                            handle_tags_save($tags, $id, 'bookmark');
                        }
                    }
                }
            }
        }
        if ($this
            ->input
            ->post('mass_delete'))
        {
            set_alert('success', _l('bookmark_total_bookmarks_deleted', $total_deleted));
        }
    }
}