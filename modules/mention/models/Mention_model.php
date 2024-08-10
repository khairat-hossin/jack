<?php
defined('BASEPATH') or exit('No direct script access allowed');
/**
 * Mention model
 */
class Mention_model extends App_Model
{
	public $post_likes_limit = 6;

    public $post_comment_likes_limit = 6;

    public $newsfeed_posts_limit = 10;
	public function __construct()
    {
        parent::__construct();
    }

    /**
     * add post
     * @param $data
     */
    public function add_post($data){
    	$data['datecreated'] = date('Y-m-d H:i:s');
        if(!isset($data['creator'])){
            $data['creator']     = get_staff_user_id();
        }
        $data['content']   = nl2br($data['content']);

        $this->db->insert(db_prefix() . 'mention_posts', $data);
        $insert_id = $this->db->insert_id();

        
        if($insert_id){
            $this->load->model('emails_model');
        	$content = explode ('data_staff_id_',$data['content']);
	        $list_staff = [];
	        $note = [];
	        $link = 'mention?postid=' . $insert_id;
	        foreach ($content as $key => $value) {
	        	$note = explode('"', $value);
	        	if(isset($note[0]) && is_numeric($note[0])){
	        		$mes = 'mentioned_you';
	                $notified = add_notification([
	                'description'     => $mes,
	                'touserid'        => $note[0],
	                'link'            => $link,
	                'additional_data' => serialize([
	                        get_staff_full_name(get_staff_user_id()),
	                    ]),
	                ]);
	                if ($notified) {
	                    pusher_trigger_notification([$note[0]]);
	                }

                    $email = $this->get_staff_email_by_id($note[0]);
                    $body = '<span class="fontsize12">Hi '.get_staff_full_name($note[0]).'</span><br /><br /><span class="fontsize12">'._l('mention_to_you').' <a href="'.admin_url($link).'">Link</a></span><br /><br />';
                    $this->emails_model->send_simple_email($email, _l('mentioned_you'), $body);
	        	}

	        }

        	return $insert_id;
        }
        return false;
    }

    /**
     * load mention post
     * @param  string  $post_id
     * @param  integer $offset
     * @return interger  
     */
    public function load_mention_post($post_id = '', $offset = 1)
    {
        $offset = ($offset - 1) * 5;

        if ($post_id != '' && $post_id != 0) {
            $this->db->where('id', $post_id);
            return $this->db->get( 'mention_posts')->row();
        }
        $this->db->where("POSITION('data_staff_id_".get_staff_user_id()."' IN content) > 0 or (select count(*) from ".db_prefix() ."mention_post_comments where POSITION('data_staff_id_".get_staff_user_id()."' IN content) > 0 and postid = ".db_prefix() ."mention_posts.id) > 0 or creator = ".get_staff_user_id());
        $this->db->order_by('datecreated', 'desc');
        $this->db->limit(5, $offset);
        return $this->db->get(db_prefix() . 'mention_posts')->result_array();
    }

    /**
     * Get all comments from post / using loader
     * @param  mixed $postid psot id
     * @param  mixed $offset page
     * @return array
     */
    public function get_post_comments($postid, $offset)
    {
        $this->db->where('postid', $postid);
        $this->db->order_by('dateadded', 'desc');

        return $this->db->get(db_prefix() . 'mention_post_comments')->result_array();
    }

    /**
     * Get post comment by id
     * @param  mixed $id comment id
     * @return objetc
     */
    public function get_comment($id, $return_as_array = false)
    {
        $this->db->where('id', $id);
        if ($return_as_array == false) {
            return $this->db->get(db_prefix() . 'mention_post_comments')->row();
        }

        return $this->db->get(db_prefix() . 'mention_post_comments')->row_array();
    }

    /**
     * Add new post comment
     * @param array $data comment data
     */
    public function add_comment($data)
    {
        $data['dateadded'] = date('Y-m-d H:i:s');
        $data['userid']    = get_staff_user_id();
        $data['content']   = nl2br($data['content']);
        $this->db->insert(db_prefix() . 'mention_post_comments', $data);
        $insert_id = $this->db->insert_id();
        if ($insert_id) {
            $this->load->model('emails_model');
            $content = explode ('data_staff_id_',$data['content']);
            $list_staff = [];
            $note = [];
            $link = 'mention?postid=' . $data['postid'];
            foreach ($content as $key => $value) {
                $note = explode('"', $value);
                if(isset($note[0]) && is_numeric($note[0])){
                    $mes = 'mentioned_you';
                    $notified = add_notification([
                    'description'     => $mes,
                    'touserid'        => $note[0],
                    'link'            => $link,
                    'additional_data' => serialize([
                            get_staff_full_name(get_staff_user_id()),
                        ]),
                    ]);
                    if ($notified) {
                        pusher_trigger_notification([$note[0]]);
                    }

                    $email = $this->get_staff_email_by_id($note[0]);
                    $body = '<span class="fontsize12">Hi '.get_staff_full_name($note[0]).'</span><br /><br /><span class="fontsize12">Someone mentioned you <a href="'.admin_url($link).'">Link</a></span><br /><br />';
                    $this->emails_model->send_simple_email($email, _l('mentioned_you'), $body);
                }

            }
            return $insert_id;
        }

        return false;
    }

    /**
     * Remove post comment from database
     * @param  mixed $id     comment id
     * @param  mixed $postid post id
     * @return boolean
     */
    public function remove_post_comment($id, $postid, $contact_id = '')
    {
        // First check if this user created the comment
        if($contact_id == ''){
            $userid = get_staff_user_id();
        }else{
            $userid = $contact_id;
        }
        if (total_rows(db_prefix() . 'mention_post_comments', [
            'postid' => $postid,
            'userid' => $userid,
            'id' => $id,
        ]) > 0) {
            $this->db->where('id', $id);
            $this->db->where('postid', $postid);
            $this->db->where('userid', $userid);
            $this->db->delete(db_prefix() . 'mention_post_comments');
            if ($this->db->affected_rows() > 0) {
                return true;
            }
            return false;
        }

        return false;
    }

    /**
     * Delete all and all connections
     * @param  mixed $postid post id
     * @return boolean
     */
    public function delete_post($postid)
    {
        // First check if this user creator of the post
        $this->db->where('id', $postid);
        $post = $this->db->get(db_prefix() .'mention_posts')->row();

        if($post->is_contact == 1){
            if (total_rows(db_prefix() . 'mention_posts', [
                'id' => $postid,
                'creator' => get_contact_user_id(),
                'is_contact' => 1,
            ]) > 0 || is_admin()) {
                $this->db->where('id', $postid);
                $this->db->delete(db_prefix() . 'mention_posts');
                if ($this->db->affected_rows() > 0) {
                    $this->db->where('postid', $postid);
                    $this->db->delete(db_prefix() . 'mention_post_comments');
                    return true;
                }
            }
        }else{
            if (total_rows(db_prefix() . 'mention_posts', [
                'id' => $postid,
                'creator' => get_staff_user_id(),
            ]) > 0 || is_admin()) {
                $this->db->where('id', $postid);
                $this->db->delete(db_prefix() . 'mention_posts');
                if ($this->db->affected_rows() > 0) {
                    $this->db->where('postid', $postid);
                    $this->db->delete(db_prefix() . 'mention_post_comments');
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * Gets the staff email by identifier.
     *
     * @param      int   $id     The identifier
     *
     * @return     String  The staff email by identifier.
     */
    public function get_staff_email_by_id($id)
    {
        $this->db->where('staffid', $id);
        $staff = $this->db->select('email')->from(db_prefix() . 'staff')->get()->row();

        return ($staff ? $staff->email : '');
    }

    /**
     * load mention post client
     * @param  string  $post_id
     * @param  integer $offset
     * @return interger  
     */
    public function load_mention_post_client($post_id = '', $offset = 1, $contact = [])
    {   
        $offset = ($offset - 1) * 5;

        if ($post_id != '' && $post_id != 0) {
            $this->db->where('id', $post_id);
            return $this->db->get( 'mention_posts')->row();
        }
        if($contact != []){
            $this->db->where("POSITION('data_client_id_".$contact->userid."' IN content) > 0 or POSITION('data_contact_id_".$contact->id."' IN content) > 0 or (select count(*) from ".db_prefix() ."mention_post_comments where (POSITION('data_client_id_".$contact->userid."' IN content) > 0 or POSITION('data_contact_id_".$contact->id."' IN content)) and postid = ".db_prefix() ."mention_posts.id) > 0 or(creator = ".$contact->id." and is_contact = 1)");
        }
        $this->db->order_by('datecreated', 'desc');
        $this->db->limit(5, $offset);
        return $this->db->get(db_prefix() . 'mention_posts')->result_array();
    }

    /**
     * Add new post comment client
     * @param array $data comment data
     */
    public function add_comment_client($data, $contact)
    {
        $data['dateadded'] = date('Y-m-d H:i:s');
        $data['userid']    = $contact->id;
        $data['is_contact'] = 1;
        $data['content']   = nl2br($data['content']);
        $this->db->insert(db_prefix() . 'mention_post_comments', $data);
        $insert_id = $this->db->insert_id();
        if ($insert_id) {
            $this->load->model('emails_model');
            $content = explode ('data_staff_id_',$data['content']);
            $list_staff = [];
            $note = [];
            $link = 'mention?postid=' . $data['postid'];
            foreach ($content as $key => $value) {
                $note = explode('"', $value);
                if(isset($note[0]) && is_numeric($note[0])){
                    $mes = 'mentioned_you';
                    $notified = add_notification([
                    'description'     => $mes,
                    'touserid'        => $note[0],
                    'link'            => $link,
                    'additional_data' => serialize([
                            $contact->firstname.' '.$contact->lastname,
                        ]),
                    ]);

                    if ($notified) {
                        pusher_trigger_notification([$note[0]]);
                    }

                    $email = $this->get_staff_email_by_id($note[0]);
                    $body = '<span class="fontsize12">Hi '.get_staff_full_name($note[0]).'</span><br /><br /><span class="fontsize12">Someone mentioned you <a href="'.admin_url($link).'">Link</a></span><br /><br />';
                    $this->emails_model->send_simple_email($email, _l('mentioned_you'), $body);
                }

            }
            return $insert_id;
        }

        return false;
    }
}