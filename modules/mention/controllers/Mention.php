<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Memtion Controller
 */
class Mention extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('mention_model');
        $this->load->model('staff_model');
        $this->load->model('projects_model');

    }

    /**
     * index page
     * @return 
     */
    public function index(){
        $data['title'] = _l('mention');
        if($this->input->get('postid') != null){
            $data['postid'] = $this->input->get('postid');
        }else {
            $data['postid'] = 0;
        }
        $data['list_mention'] = $this->load_mention();
        $this->load->view('manage', $data);
    }

    /**
     * get staff mentions description
     * @return [json] [userdata]
     */
    public function get_staff_mentions(){
       	$user_data = array();
		$users = $this->staff_model->get();
		foreach($users as $key => $val)
		{
			$user_data[$key]['id'] = intval($val['staffid']);
			$user_data[$key]['name'] = trim($val['firstname'] . ' ' . $val['lastname']);
			if ($val['profile_image']) {

                $url = site_url().'/uploads/staff_profile_images/'. $val['staffid'].'/thumb_'.$val['profile_image'];
  
                // Use get_headers() function 
                $headers = get_headers($url); 
                  
                // Use condition to check the existence of URL 
                if($headers && strpos( $headers[0], '200')) { 
                    $user_data[$key]['avatar'] = site_url().'/uploads/staff_profile_images/'. $val['staffid'].'/thumb_'.$val['profile_image'];
                } 
                else { 
                    $user_data[$key]['avatar'] = site_url().'/assets/images/user-placeholder.jpg';
                } 
                
            }else{
                $user_data[$key]['avatar'] = site_url().'/assets/images/user-placeholder.jpg';
            }
			$user_data[$key]['type'] = 'staff';
		}
		echo json_encode($user_data);
        die();
    }

    /**
     * get object mentions description
     * @return [json] [userdata]
     */
    public function get_object_mentions(){
        $user_data = array();
        if (has_permission('projects', '', 'view')){
            $this->load->model('projects_model');
            $projects = $this->projects_model->get();
            foreach($projects as $key => $val)
            {
                $node = [];
                $node['id'] = intval($val['id']);
                $node['name'] = _l('project').': '.$val['name'];
                $node['type'] = 'project';
                $node['link'] = '<a href="'.admin_url('projects/view/' . $val['id']).'" data-project-id="data_project_id_'.$val['id'].'">'.$node['name'].'</a>';
                $user_data[] = $node;
            }
        }
        if (has_permission('tasks', '', 'view')){
            $this->load->model('tasks_model');
            $tasks = $this->db->get(db_prefix() . 'tasks')->result_array();
            foreach($tasks as $key => $val)
            {
                $node = [];
                $node['id'] = intval($val['id']);
                $node['name'] = _l('task').': '.$val['name'];
                $node['type'] = 'task';
                $node['link'] = '<a href="'.admin_url('tasks/view/'.$val['id']).'" data-task-id="data_task_id_'.$val['id'].'">'.$node['name'].'</a>';
                $user_data[] = $node;
            }
        }
        $clients = $this->projects_model->get();
        if (has_permission('clients', '', 'view')){
            $this->load->model('clients_model');
            $clients = $this->clients_model->get();
            foreach($clients as $key => $val)
            {
                $node = [];
                $node['id'] = intval($val['userid']);
                $node['name'] = _l('client').': '.$val['company'];
                $node['type'] = 'client';
                $node['link'] = '<a href="'.admin_url('clients/client/' . $val['userid']).'" data-client-id="data_client_id_'.$val['userid'].'">'.$node['name'].'</a>';
                $user_data[] = $node;
            }
        }
        if (has_permission('leads', '', 'view')){
            $this->load->model('leads_model');
            $leads = $this->leads_model->get();
            foreach($leads as $key => $val)
            {
                $node = [];
                $node['id'] = intval($val['id']);
                $node['name'] = _l('lead').': '.$val['name'];
                $node['type'] = 'lead';
                $node['link'] = '<a href="' . admin_url('leads/index/' . $val['id']) . '" data-lead-id="data_lead_id_'.$val['id'].'">'.$node['name'].'</a>';
                $user_data[] = $node;
            }
        }
        if (has_permission('tickets', '', 'view')){
            $this->load->model('tickets_model');
            $tickets = $this->tickets_model->get();
            foreach($tickets as $key => $val)
            {
                $node = [];
                $node['id'] = intval($val['ticketid']);
                $node['name'] = _l('ticket').': '.$val['subject'];
                $node['type'] = 'ticket';
                $node['link'] = '<a href="'.admin_url('tickets/ticket/' . $val['ticketid']).'" data-ticket-id="data_ticket_id_'.$val['ticketid'].'">'.$node['name'].'</a>';
                $user_data[] = $node;
            }
        }      
        if (has_permission('proposals', '', 'view')){
            $this->load->model('proposals_model');
            $proposals = $this->proposals_model->get();
            foreach($proposals as $key => $val)
            {
                $node = [];
                $node['id'] = intval($val['id']);
                $node['name'] = _l('proposal').': '.format_proposal_number($val['id']);
                $node['type'] = 'proposal';
                $node['link'] = '<a href="' . admin_url('proposals/list_proposals/' . $val['id']) . '" data-proposal-id="data_proposal_id_'.$val['id'].'">'.$node['name'].'</a>';
                $user_data[] = $node;
            }
        }
        if (has_permission('invoices', '', 'view')){
            $this->load->model('invoices_model');
            $invoices = $this->invoices_model->get();
            foreach($invoices as $key => $val)
            {
                $node = [];
                $node['id'] = intval($val['id']);
                $node['name'] = _l('invoice').': '.format_invoice_number($val['id']);
                $node['type'] = 'invoice';
                $node['link'] = '<a href="' . admin_url('invoices/list_invoices/' . $val['id']) . '" data-invoice-id="data_invoice_id_'.$val['id'].'">'.$node['name'].'</a>';
                $user_data[] = $node;
            }
        }
        if (has_permission('credit_notes', '', 'view')){
            $this->load->model('credit_notes_model');
            $credit_notes = $this->credit_notes_model->get();
            foreach($credit_notes as $key => $val)
            {
                $node = [];
                $node['id'] = intval($val['id']);
                $node['name'] = _l('credit_note').': '.format_credit_note_number($val['id']);
                $node['type'] = 'credit_note';
                $node['link'] = '<a href="' . admin_url('credit_notes/list_credit_notes/' . $val['id']) . '" data-credit-note-id="data_credit_note_id_'.$val['id'].'">'.$node['name'].'</a>';
                $user_data[] = $node;
            }
        }
        if (has_permission('estimates', '', 'view')){
            $this->load->model('estimates_model');
            $estimates = $this->estimates_model->get();
            foreach($estimates as $key => $val)
            {
                $node = [];
                $node['id'] = intval($val['id']);
                $node['name'] = _l('estimate').': '.format_estimate_number($val['id']);
                $node['type'] = 'estimate';
                $node['link'] = '<a href="' . admin_url('estimates/list_estimates/' . $val['id']) . '" data-estimate-id="data_estimate_id_'.$val['id'].'">'.$node['name'].'</a>';
                $user_data[] = $node;
            }
        }
        if (has_permission('expenses', '', 'view')){
            $this->load->model('expenses_model');
            $expenses = $this->expenses_model->get();
            foreach($expenses as $key => $val)
            {
                $node = [];
                $node['id'] = intval($val['id']);
                $node['name'] = _l('expense').': '.$val['expense_name'];
                $node['type'] = 'expense';
                $node['link'] = '<a href="' . admin_url('expenses/list_expenses/' . $val['id']) . '" data-expense-id="data_expense_id_'.$val['id'].'">'.$node['name'].'</a>';
                $user_data[] = $node;
            }
        }
        if (has_permission('contracts', '', 'view')){
            $this->load->model('contracts_model');
            $contracts = $this->contracts_model->get();
            foreach($contracts as $key => $val)
            {
                $node = [];
                $node['id'] = intval($val['id']);
                $node['name'] = _l('contract').': '.$val['subject'];
                $node['type'] = 'contract';
                $node['link'] = '<a href="' . admin_url('contracts/contract/' . $val['id']) . '" data-contract-id="data_contract_id_'.$val['id'].'">'.$node['name'].'</a>';
                $user_data[] = $node;
            }
        }
        if (has_permission('payments', '', 'view')){
            $this->load->model('payments_model');
            $this->db->select('*,' . db_prefix() . 'invoicepaymentrecords.id as paymentid');
            $this->db->from(db_prefix() . 'invoicepaymentrecords');
            $this->db->join(db_prefix() . 'payment_modes', '' . db_prefix() . 'invoicepaymentrecords.paymentmode = ' . db_prefix() . 'payment_modes.id', 'LEFT');
            $this->db->join(db_prefix() . 'invoices', '' . db_prefix() . 'invoices.id = ' . db_prefix() . 'invoicepaymentrecords.invoiceid');
            $payments = $this->db->get()->result_array();
            foreach($payments as $key => $val)
            {
                $node = [];
                $node['id'] = intval($val['id']);
                $node['name'] = _l('payment').': '.$val['name'].' #'. format_invoice_number($val['invoiceid']).' #'. _d($val['date']);
                $node['type'] = 'payment';
                $node['link'] = '<a href="' .admin_url('payments/payment/' . $val['id']) . '" data-payment-id="data_payment_id_'.$val['id'].'">'.$node['name'].'</a>';
                $user_data[] = $node;
            }
        }

        if (has_permission('contact', '', 'view')){
            $this->load->model('clients_model');
            $contacts = $this->clients_model->get_contacts();

            foreach($contacts as $key => $val)
            {
                $node = [];
                $node['id'] = intval($val['id']);
                $node['name'] = _l('contact').': '.$val['firstname'].' '.$val['lastname'];
                $node['type'] = 'contact';
                $node['link'] = '<a href="' .admin_url('clients/all_contacts/' . $val['userid']) . '?group=contacts" data-contact-id="data_contact_id_'.$val['id'].'">'.$node['name'].'</a>';
                $user_data[] = $node;
            }
        }

        echo json_encode($user_data);
        die();
    }

    /**
     * url exists
     * @param  [string] $url 
     * @return [bool]     
     */
    function url_exists($url) {
        if (!$fp = curl_init($url)) return false;
        return true;
    }    

    /**
     * add_post description
     */
    public function add_post(){
        $data = $this->input->post();
        $success = $this->mention_model->add_post($data);

        if ($success) {
            echo json_encode([
                'message' => _l('added_mention_successfully'),
                'success' => true,
            ]);
        } else {
            echo json_encode([
                'message' => _l('added_mention_failed'),
                'success' => false,
            ]);
        }
        die();
    }

    /**
     * load mention description
     * @param  string  $id   
     * @param  integer $page 
     * @return [string]  
     */
    public function load_mention($id = '', $page = 1){
        $this->load->model('departments_model');
        $data['departments'] = $this->departments_model->get();
        
        $posts    = $this->mention_model->load_mention_post($id ,$page);
        $count_posts = count($posts);
        $response = '';
        
        foreach ($posts as $post) {

            if($post['is_contact'] == 1){
                $this->load->model('clients_model');
                $contact = $this->clients_model->get_contact($post['creator']);
            }
            $date_current = date('Y-m-d H:i:s');

            $count_comment = $this->db->query('SELECT * FROM tblmention_post_comments where postid = '.$post['id'].'')->result_array();

            $visible_departments = '';

            $pinned_class = '';
            $pinned_style = '';
            if ($post['pinned'] == 1) {
                $pinned_class = ' pinned';
                $pinned_style = ' pinned_style_class';
            }
            
            $response .= '<div class="panel_s newsfeed_post newsfeed_post_cus bg-white' . $pinned_class . '" data-main-postid="' . $post['id'] . '" id="post-'.$post['id'].'">';
            $response .= '<div class="panel-body post-content pb-0'.$pinned_style .'">';
            $response .= '<div class="media">';
            $response .= '<div class="media-left">';
            $response .= '';

            if($post['is_contact'] == 1){
                $response .= '<a href="' . admin_url('clients/client/' . $contact->userid) . '"><img src="'. contact_profile_image_url($contact->id,'thumb').'" data-toggle="tooltip" class="client-profile-image-small"></a>';
            }else{
                $response .= '<a  data-id="'.$post['creator'].'-'.$post['id'].'" href="' .admin_url('internal_social_network/personal_wall/1/'.$post['creator']) . '">' . staff_profile_image($post['creator'], [
                    'staff-profile-image-small',
                ]) . '</a>';
            }

            $response .= '</div>';
            $response .= '<div class="media-body">';
                if($post['is_contact'] == 1){
                    $response .= '<p class="media-heading no-mbot"><a href="' . admin_url('internal_social_network/personal_wall/1/'.$post['creator']) . '">' . $contact->firstname . ' ' .$contact->lastname . '</a></p>';
                }else{
                    $response .= '<p class="media-heading no-mbot"><a href="' . admin_url('internal_social_network/personal_wall/1/'.$post['creator']) . '">' . get_staff_full_name($post['creator']) . '</a></p>';
                }
            $timediff = strtotime($post['datecreated']) + 86400;
            $timenow = strtotime(time_ago($post['datecreated']));

            if($timenow < $timediff){
                $response .= '<small class="text-muted">' . time_ago($post['datecreated']) . '</small>';
            }else{
                $response .= '<small class="text-muted">' . _dt($post['datecreated']) . '</small>';
            }

            
            $response .= '</div>';

            $response .= '</div>'; // media end
            if (($post['creator'] == get_staff_user_id() && $post['is_contact'] == 1) || is_admin()) {
                $response .= '<div class="dropdown pull-right btn-post-options-wrapper dropdown_style">';
                $response .= '<button class="btn btn-default dropdown-toggle btn-post-options btn-icon" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true"><i class="fa fa-globe"></i></button>';
                $response .= '<ul class="dropdown-menu my-2">';
                $response .= '<li><a href="#" onclick="see_details_isn(' . $post['id'] . '); return false;"><i class="fa fa-search" aria-hidden="true"></i> ' . _l('post_see_details') . '</a></li>';
                $response .= '<li><a href="#" onclick="delete_post_isn(' . $post['id'] . '); return false;"><i class="fa fa-trash" aria-hidden="true"></i> ' . _l('delete') . '</a></li>';
                $response .= '</ul>';
                $response .= '</div>';
            }
            $response .= '<div class="post-content mtop20 display-block">';
            
            $response .= $post['content'];

            $response .= '<div class="row">';
            $response .= '<div class="col-md-6"></div>';
            $response .= '<div class="pointer col-md-6" onclick="toggle_comment('. $post['id'] .');">';
            $response .= '<div class=" pull-right"><small><i class="fa fa-comments"></i> <div id="count_comment_'. $post['id'] .'" class="inline_style">'.count($count_comment). '</div> '._l('comments').'</small></div>';
            $response .= '</div>';
            $response .= '<hr/>';
            $response .= '</div>';
            $response .= '</div>'; // panel body end

             
            // Comments
            $response .= '<div class="js-commnet-hidden_' . $post['id'] . ' displaynone_style">';
            $response .= '<div class="post_comments_wrapper post_comment_style" data-comments-postid="' . $post['id'] . '">';
            $response .= $this->init_post_comments($post['id']);
            $response .= '</div>';
            $response .= '<div class="panel-footer user-comment panel_footer_style">';
            $response .= form_open_multipart('admin/internal_social_network/add_comment',array('id'=>'ins-comment-attachment-'. $post['id'])); 
            $response .= '<div class="row position-relative"> ';
            $response .= '<hr/>';
            $response .= '<div class="pull-left comment-image">';
            $response .= '<a href="' . admin_url('profile/' . $post['creator']) . '">' . staff_profile_image(get_staff_user_id(), [
                'staff-profile-image-small',
            ]) . '</a>';
            $response .= '</div>'; // end comment-image
            $response .= '<div class="media-body comment-input">';
            $response .= '<div id="new_comment_post_'.$post['id'].'" data-postid="' . $post['id'] . '" class="inputor new_comment" contentEditable="true">
              </div>';
            $response .= '</div>';            
            $response .= '<a href="#" onclick="add_comment_isn('.$post['id'].'); return false;" class="px-0 pull-right add_comment_style"><i class="fa fa-paper-plane" aria-hidden="true"></i></a>';
            $response .= '<div class="justify-content-center">';
            $response .= '<div class="row">';
            $response .= '<div></div></div>';
            $response .= form_close();    
            $response .='</div>'; 
            $response .= '</div>'; 
            $response .= '</div>'; // end comment-textarea
            $response .= '</div>'; // end user-comment
            $response .= '</div>'; // panel end
            $response .= '</div>';
            
            }
        return $response;
    }

    /**
     * get post data
     * @param  [integer] $id 
     * @return [string]     
     */
    public function get_post_data($id){
        $this->load->model('departments_model');        
        $post   = $this->mention_model->load_mention_post($id);
        if($post->is_contact == 1){
            $this->load->model('clients_model');
            $contact = $this->clients_model->get_contact($post->creator);
        }
        $response = '';        
        $date_current = date('Y-m-d H:i:s');
        $count_comment = $this->db->query('SELECT * FROM tblmention_post_comments where postid = '.$post->id.'')->result_array();
        $visible_departments = '';
        $pinned_class = '';
        $pinned_style = '';         
        
        $response .= '<div class="panel_s newsfeed_post newsfeed_post_cus bg-white' . $pinned_class . '" data-main-postid="' . $post->id . '" id="post-'.$post->id.'">';
        $response .= '<div class="panel-body post-content pb-0"'.$pinned_style .'>';
        $response .= '<div class="media">';
        $response .= '<div class="media-left">';
        $response .= '';
        if($post->is_contact == 1){
                $response .= '<a href="' . admin_url('clients/client/' . $contact->userid) . '"><img src="'. contact_profile_image_url($contact->id,'thumb').'" data-toggle="tooltip" class="client-profile-image-small"></a>';
            }else{
                $response .= '<a  data-id="'.$post->creator.'-'.$post->id.'" href="' .admin_url('internal_social_network/personal_wall/1/'.$post->creator) . '">' . staff_profile_image($post->creator, [
                    'staff-profile-image-small',
                ]) . '</a>';
            }
       
        $response .= '</div>';
        $response .= '<div class="media-body">';
            $response .= '<p class="media-heading no-mbot"><a href="' . admin_url('internal_social_network/personal_wall/1/'.$post->creator) . '">' . get_staff_full_name($post->creator) . '</a></p>';
        $timediff = strtotime($post->datecreated) + 86400;
        $timenow = strtotime(time_ago($post->datecreated));

        if($timenow < $timediff){
            $response .= '<small class="text-muted">' . time_ago($post->datecreated) . '</small>';
        }else{
            $response .= '<small class="text-muted">' . _dt($post->datecreated) . '</small>';
        }

        
        $response .= '</div>';

        $response .= '</div>'; // media end
            if ($post->creator == get_staff_user_id() || is_admin()) {
            $response .= '<div class="dropdown pull-right btn-post-options-wrapper dropdown_style">';
            $response .= '<button class="btn btn-default dropdown-toggle btn-post-options btn-icon" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true"><i class="fa fa-globe"></i></button>';
            $response .= '<ul class="dropdown-menu my-2">';
            $response .= '<li><a href="#" onclick="delete_post_isn(' . $post->id . '); return false;"><i class="fa fa-trash" aria-hidden="true"></i> ' . _l('delete') . '</a></li>';
            $response .= '</ul>';
            $response .= '</div>';
        }
        $response .= '<div class="post-content mtop20 display-block">';

        $response .= $post->content;

        $response .= '<div class="row">';
        $response .= '<div class="col-md-6"></div>';
        $response .= '<div class="pointer col-md-6" onclick="toggle_comment('. $post->id .');">';
        $response .= '<div class=" pull-right"><small><i class="fa fa-comments"></i> <div id="count_comment_detail_'. $post->id .'" class="inline_style" >'.count($count_comment). '</div> '._l('comments').'</small></div>';
        $response .= '</div>';
        $response .= '</div>';
        $response .= '<hr/>';
        $response .= '</div>';
        
        $response .= '</div>'; // panel body end

         
        // Comments
        $response .= '<div class="js-commnet-hidden_' . $post->id . ' displaynone_style"';
        $response .= '<div class="post_comments_wrapper post_comment_style" data-comments-detail-postid="' . $post->id . '">';
        $response .= $this->init_post_comments($post->id);
        $response .= '</div>';
        $response .= '<div class="panel-footer user-comment panel_footer_style">';
        $response .= form_open_multipart('admin/internal_social_network/add_comment',array('id'=>'ins-comment-attachment-'. $post->id)); 
        $response .= '<div class="row position-relative margin_auto"> ';
        $response .= '<hr/>';
        $response .= '<div class="pull-left comment-image">';
        $response .= '<a href="' . admin_url('profile/' . $post->creator) . '">' . staff_profile_image(get_staff_user_id(), [
            'staff-profile-image-small',
        ]) . '</a>';
        $response .= '</div>'; // end comment-image
        $response .= '<div class="media-body comment-input">';
        $response .= '<div id="new_comment_detail_post_'.$post->id.'" data-postid="' . $post->id . '" class="inputor new_comment" contentEditable="true">
          </div>';
        $response .= '</div>';            
        $response .= '<a href="#" onclick="add_comment_detail_isn('.$post->id.'); return false;" class="px-0 pull-right add_comment_style"><i class="fa fa-paper-plane" aria-hidden="true"></i></a>';
        $response .= '<div class="justify-content-center">';
        $response .= '<div class="row">';
        $response .= '<div></div></div>';
        $response .= form_close();  
        $response .= '</div>'; 
        $response .= '</div>'; 
        $response .= '</div>'; // end comment-textarea
        $response .= '</div>'; // end user-comment
        $response .= '</div>'; // panel end
        $response .= '</div>';        
        $data = [];
        $data['response']= $response;
        $this->load->view('see_detail_post', $data);
    }

    /**
     * load mention ajax 
     * @param  string  $id  
     * @param  integer $page 
     * @return string        
     */
    public function load_mention_ajax($id = '', $page = 1){
        $this->load->model('departments_model');
        $data['departments'] = $this->departments_model->get();
        
        $posts    = $this->mention_model->load_mention_post($id ,$page);
        $count_posts = count($posts);
        $response = '';       
        foreach ($posts as $post) {
            if($post['is_contact'] == 1){
                $this->load->model('clients_model');
                $contact = $this->clients_model->get_contact($post['creator']);
            }


            $date_current = date('Y-m-d H:i:s');

            $count_comment = $this->db->query('SELECT * FROM tblmention_post_comments where postid = '.$post['id'].'')->result_array();

            $visible_departments = '';

            $pinned_class = '';
            $pinned_style = '';
            if ($post['pinned'] == 1) {
                $pinned_class = ' pinned';
                $pinned_style = ' pinned_style_class';
            }
            
            $response .= '<div class="panel_s newsfeed_post newsfeed_post_cus bg-white' . $pinned_class . '" data-main-postid="' . $post['id'] . '" id="post-'.$post['id'].'">';
            $response .= '<div class="panel-body post-content pb-0"'.$pinned_style .'>';
            $response .= '<div class="media">';
            $response .= '<div class="media-left">';
            $response .= '';
            if($post['is_contact'] == 1){
                $response .= '<a href="' . admin_url('clients/client/' . $contact->userid) . '"><img src="'. contact_profile_image_url($contact->id,'thumb').'" data-toggle="tooltip" class="client-profile-image-small"></a>';
            }else{
                $response .= '<a  data-id="'.$post['creator'].'-'.$post['id'].'" href="' .admin_url('internal_social_network/personal_wall/1/'.$post['creator']) . '">' . staff_profile_image($post['creator'], [
                    'staff-profile-image-small',
                ]) . '</a>';
            }
            
            $response .= '</div>';
            $response .= '<div class="media-body">';
            if($post['is_contact'] == 1){
                $response .= '<p class="media-heading no-mbot"><a href="' . admin_url('internal_social_network/personal_wall/1/'.$post['creator']) . '">' . $contact->firstname . ' ' .$contact->lastname . '</a></p>';
            }else{
                $response .= '<p class="media-heading no-mbot"><a href="' . admin_url('internal_social_network/personal_wall/1/'.$post['creator']) . '">' . get_staff_full_name($post['creator']) . '</a></p>';
            }
            $timediff = strtotime($post['datecreated']) + 86400;
            $timenow = strtotime(time_ago($post['datecreated']));

            if($timenow < $timediff){
                $response .= '<small class="text-muted">' . time_ago($post['datecreated']) . '</small>';
            }else{
                $response .= '<small class="text-muted">' . _dt($post['datecreated']) . '</small>';
            }

            
            $response .= '</div>';

            $response .= '</div>'; // media end

            if(($post['creator'] == get_staff_user_id() && $post['creator'] == 1) || is_admin()) {
                $response .= '<div class="dropdown pull-right btn-post-options-wrapper dropdown_style">';
                $response .= '<button class="btn btn-default dropdown-toggle btn-post-options btn-icon" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true"><i class="fa fa-globe"></i></button>';
                $response .= '<ul class="dropdown-menu my-2">';
                $response .= '<li><a href="#" onclick="see_details_isn(' . $post['id'] . '); return false;"><i class="fa fa-search" aria-hidden="true"></i> ' . _l('post_see_details') . '</a></li>';
                $response .= '<li><a href="#" onclick="delete_post_isn(' . $post['id'] . '); return false;"><i class="fa fa-trash" aria-hidden="true"></i> ' . _l('delete') . '</a></li>';
                $response .= '</ul>';
                $response .= '</div>';
            }

            $response .= '<div class="post-content mtop20 display-block">';

            $response .= $post['content'];

            $response .= '<div class="row">';
            $response .= '<div class="col-md-6"></div>';
            $response .= '<div class="pointer col-md-6" onclick="toggle_comment('. $post['id'] .');">';
            $response .= '<div class=" pull-right"><small><i class="fa fa-comments"></i> <div id="count_comment_'. $post['id'] .'" class="inline_style">'.count($count_comment). '</div> '._l('comments').'</small></div>';
            $response .= '</div>';
            $response .= '<hr/>';
            $response .= '</div>';
            
            $response .= '</div>'; // panel body end

            // Comments
            $response .= '<div class="js-commnet-hidden_' . $post['id'] . ' displaynone_style">';
            $response .= '<div class="post_comments_wrapper post_comment_style" data-comments-postid="' . $post['id'] . '">';
            $response .= $this->init_post_comments($post['id']);
            $response .= '</div>';
            $response .= '<div class="panel-footer user-comment panel_footer_style">';
            $response .= form_open_multipart('admin/internal_social_network/add_comment',array('id'=>'ins-comment-attachment-'. $post['id'])); 
            $response .= '<div class="row position-relative"> ';
            $response .= '<hr/>';
            $response .= '<div class="pull-left comment-image">';
            $response .= '<a href="' . admin_url('profile/' . $post['creator']) . '">' . staff_profile_image(get_staff_user_id(), [
                'staff-profile-image-small',
            ]) . '</a>';
            $response .= '</div>'; // end comment-image
            $response .= '<div class="media-body comment-input">';
            $response .= '<div id="new_comment_post_'.$post['id'].'" data-postid="' . $post['id'] . '" class="inputor new_comment" contentEditable="true">
              </div>';
            $response .= '</div>';            
            $response .= '<a href="#" onclick="add_comment_isn('.$post['id'].'); return false;" class="px-0 pull-right add_comment_style"><i class="fa fa-paper-plane" aria-hidden="true"></i></a>';
            $response .= '<div class="justify-content-center">';
            $response .= '<div class="row">';
            $response .= '<div></div></div>';
            $response .= form_close();      
            $response .='</div>'; 
            $response .= '</div>'; 
            $response .= '</div>'; // end comment-textarea
            $response .= '</div>'; // end user-comment
            $response .= '</div>'; // panel end
            $response .= '</div>';
              
            }
        echo json_encode([ 
                $response 
            ]);
        die();
    }

    /* Post new comment by staff */
    public function add_comment()
    {   
        $data = $this->input->post();
        unset($data['url']);
        $data['postid'] = $data['postid'];
        $comment_id = $this->mention_model->add_comment($data);        
        $success = false;            
        $success    = ($comment_id !== false ? true : false);
        $comment    = '';
        if ($comment_id) {
            $comment = $this->comment_single($this->mention_model->get_comment($comment_id, true));
        }

        echo json_encode([
            'success' => $success,
            'comment' => $comment,
            'comment_id' => $comment_id
        ]);
    }

    /* Init post comments */
    public function init_post_comments($id)
    {
        $_comments      = '';
        $total_comments = total_rows(db_prefix() . 'mention_post_comments', [
            'postid' => $id,
        ]);
        if ($total_comments > 0) {
            $page = $this->input->post('page');
            if (!$this->input->post('page')) {
                $_comments .= '<div class="panel-footer post-comment post-comment">';
            }
            $comments = $this->mention_model->get_post_comments($id, $page);
            // Add +1 becuase the first page is already inited
            
            foreach ($comments as $comment) {
                $_comments .= $this->comment_single($comment);
            }
            
            if (!$this->input->post('page')) {
                $_comments .= '</div>';
            }
        }else{
            $_comments .= '<div class="panel-footer post-comment post-comment">';
            $_comments .= '</div>'; // end comments footer
        }
        if (($this->input->is_ajax_request() && $this->input->get('refresh_post_comments')) || ($this->input->is_ajax_request() && $this->input->post('page'))) {
            echo html_entity_decode($_comments);
        } else {
            return $_comments;
        }
    }

    /**
     * comment single
     *
     * @param      onject  $comment  The comment
     *
     * @return     string 
     */
    public function comment_single($comment)
    {
        if($comment['is_contact'] == 1){
            $this->load->model('clients_model');
            $contact = $this->clients_model->get_contact($comment['userid']);
        }
        $_comments = '';
        $_comments .= '<div class="comment comment_single_style" data-commentid="' . $comment['id'] . '">';
        $_comments .= '<div class="pull-left comment-image">';
        if($comment['is_contact'] == 1){
            $contact = $this->clients_model->get_contact($comment['userid']);
            $_comments .= '<a href="' . admin_url('clients/client/' . $contact->userid) . '"><img src="'. contact_profile_image_url($contact->id,'thumb').'" data-toggle="tooltip" class="client-profile-image-small"></a>';
        }else{
            $_comments .= '<a href="' . admin_url('profile/' . $comment['userid']) . '">' . staff_profile_image($comment['userid'], [
                'staff-profile-image-small',
            ]) . '</a>';
        }
        $_comments .= '</div>'; // end comment-image

        if($comment['is_contact'] == 1){
            $_comments .= '<div class="media-body">';
            $_comments .= '<p class="no-margin"><a href="' . admin_url('clients/client/' . $contact->userid) . '">' . html_escape($contact->firstname . ' ' .$contact->lastname) . '</a> <small>' ._dt($comment['dateadded']) . '</small></p><p styte="overflow: auto;" class="no-margin comment-content">' . $comment['content'] . '</p>';
        }else{
            if ($comment['userid'] == get_staff_user_id() || is_admin()) {
                $_comments .= '<span class="pull-right"><a href="#" class="remove-post-comment" onclick="remove_post_comment_isn(' . $comment['id'] . ',' . $comment['postid'] . '); return false;"><i class="fa fa-remove bold"></i></span></a>';
            }
            $_comments .= '<div class="media-body">';
            $_comments .= '<p class="no-margin"><a href="' . admin_url('profile/' . $comment['userid']) . '">' . get_staff_full_name($comment['userid']) . '</a> <small>' ._dt($comment['dateadded']) . '</small></p><p styte="overflow: auto;" class="no-margin comment-content">' . $comment['content'] . '</p>';
        }
        
        $_comments .= '';       
        $_comments .= '</div>';
        $_comments .= '</div>';
        $_comments .= '<div class="clearfix"></div>';

        return $_comments;
    }

    /* Delete post comment */
    public function remove_post_comment($id, $postid)
    {
        echo json_encode([
            'success' => $this->mention_model->remove_post_comment($id, $postid),
        ]);
    }
    
    /* Delete all post */
    public function delete_post($postid)
    {
        hooks()->do_action('before_delete_post', $postid);
        echo json_encode([
            'success' => $this->mention_model->delete_post($postid),
        ]);
    }
}