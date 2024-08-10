<?php

defined('BASEPATH') or exit('No direct script access allowed');

class mention_client extends ClientsController
{
    public function index()
    {
        if(is_client_logged_in()){
            $data['list_mention'] = $this->load_mention();
            $data['title']            = _l('mention');
            $this->data($data);

            $this->view('manage_client');
            $this->layout();
        }else{
            redirect(site_url());
        }
    }

    /**
     * load mention description
     * @param  string  $id   
     * @param  integer $page 
     * @return [string]  
     */
    public function load_mention($id = '', $page = 1){
        $this->load->model('departments_model');
        $this->load->model('mention_model');
        $data['departments'] = $this->departments_model->get();
        $contact = $this->clients_model->get_contact(get_contact_user_id());
        $posts    = $this->mention_model->load_mention_post_client($id ,$page, $contact);
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
                $response .= '<img src="'. contact_profile_image_url($contact->id,'thumb').'" data-toggle="tooltip" class="client-profile-image-small">';
            }else{
                $response .= staff_profile_image($post['creator'], [
                    'staff-profile-image-small',
                ]);
            }
            $response .= '</div>';
            $response .= '<div class="media-body">';
                if($post['is_contact'] == 1){
                    $response .= '<p class="media-heading no-mbot"><h5>' . $contact->firstname . ' ' .$contact->lastname . '</h5></p>';
                }else{
                    $response .= '<p class="media-heading no-mbot"><h5>' . get_staff_full_name($post['creator']) . '</h5></p>';
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
            if($post['is_contact'] == 1){
                if ($post['creator'] == get_contact_user_id()) {
                    $response .= '<div class="dropdown pull-right btn-post-options-wrapper dropdown_style">';
                    $response .= '<button class="btn btn-default dropdown-toggle btn-post-options btn-icon" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true"><i class="fa fa-globe"></i></button>';
                    $response .= '<ul class="dropdown-menu my-2">';
                    $response .= '<li><a href="#" onclick="delete_post_isn(' . $post['id'] . '); return false;"><i class="fa fa-trash" aria-hidden="true"></i> ' . _l('delete') . '</a></li>';
                    $response .= '</ul>';
                    $response .= '</div>';
                }
            }
            $response .= '<div class="post-content mtop20 display-block">';
            
            $response .= $this->convertAll($post['content']);

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
            $response .= '<a href="' . site_url('clients/profile') . '"><img src="'. contact_profile_image_url($contact->id,'thumb').'" data-toggle="tooltip" class="client-profile-image-small"></a>';
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

    /* Init post comments */
    public function init_post_comments($id)
    {
        $this->load->model('mention_model');
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

    public function comment_single($comment)
    {
        $this->load->model('mention_model');

        if($comment['is_contact'] == 1){
            $this->load->model('clients_model');
            $contact = $this->clients_model->get_contact($comment['userid']);
        }

        $_comments = '';
        $_comments .= '<div class="comment comment_single_style" data-commentid="' . $comment['id'] . '">';
        $_comments .= '<div class="pull-left comment-image">';


        if($comment['is_contact'] == 1){
            $contact = $this->clients_model->get_contact($comment['userid']);
            $_comments .= '<img src="'. contact_profile_image_url($contact->id,'thumb').'" data-toggle="tooltip" class="client-profile-image-small">';
        }else{
            $_comments .= staff_profile_image($comment['userid'], [
                'staff-profile-image-small',
            ]);
        }

        $_comments .= '</div>';  // end comment-image
        if($comment['is_contact'] == 1){
            if ($comment['userid'] == get_contact_user_id()) {
                $_comments .= '<span class="pull-right"><a href="#" class="remove-post-comment" onclick="remove_post_comment_isn(' . $comment['id'] . ',' . $comment['postid'] . '); return false;"><i class="fa fa-remove bold"></i></span></a>';
            }
            $_comments .= '<div class="media-body">';
            $_comments .= '<p class="no-margin"><a>' . html_escape($contact->firstname . ' ' .$contact->lastname) . '</a> <small>' ._dt($comment['dateadded']) . '</small></p><p styte="overflow: auto;" class="no-margin comment-content">' . $this->convertAll($comment['content']) . '</p>';
        }else{
            $_comments .= '<div class="media-body">';
            $_comments .= '<p class="no-margin"><a>' . get_staff_full_name($comment['userid']) . '</a> <small>' ._dt($comment['dateadded']) . '</small></p><p styte="overflow: auto;" class="no-margin comment-content">' . $this->convertAll($comment['content']) . '</p>';
        }
        
        $_comments .= '</div>';
        $_comments .= '</div>';
        $_comments .= '<div class="clearfix"></div>';

        return $_comments;
    }

    /* Delete post comment */
    public function remove_post_comment($id, $postid)
    {
        $this->load->model('mention_model');

        echo json_encode([
            'success' => $this->mention_model->remove_post_comment($id, $postid, get_contact_user_id()),
        ]);
    }

    /**
     * get staff mentions description
     * @return [json] [userdata]
     */
    public function get_staff_mentions(){
        $user_data = array();
        //$users = $this->staff_model->get();
        $this->load->model('clients_model');
        $users = $this->clients_model->get_admins(get_client_user_id());
        foreach($users as $key => $val)
        {
            $staff = $this->staff_model->get($val['staff_id']);
            $user_data[$key]['id'] = intval($val['staff_id']);
            $user_data[$key]['name'] = get_staff_full_name($val['staff_id']);
            if ($staff->profile_image) {

                $url = site_url().'/uploads/staff_profile_images/'. $val['staffid'].'/thumb_'.$staff->profile_image;
  
                // Use get_headers() function 
                $headers = get_headers($url); 
                  
                // Use condition to check the existence of URL 
                if($headers && strpos( $headers[0], '200')) { 
                    $user_data[$key]['avatar'] = site_url().'/uploads/staff_profile_images/'. $val['staffid'].'/thumb_'.$staff->profile_image;
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
        $this->load->model('clients_model');
        $contact = $this->clients_model->get_contact(get_contact_user_id());
        $user_data = array();

        $this->load->model('projects_model');
        $projects = $this->projects_model->get('', ['clientid' => get_client_user_id()]);
        foreach($projects as $key => $val)
        {
            $node = [];
            $node['id'] = intval($val['id']);
            $node['name'] = _l('project').': '.$val['name'];
            $node['type'] = 'project';
            $node['link'] = '<a href="'.site_url('clients/project/' . $val['id']).'" data-project-id="data_project_id_'.$val['id'].'">'.$node['name'].'</a>';
            $user_data[] = $node;
        }

        $this->db->where('rel_id', $contact->userid);
        $this->db->where('rel_type', 'customer');
        $tasks = $this->db->get(db_prefix() . 'tasks')->result_array();
        foreach($tasks as $key => $val)
        {
            $node = [];
            $node['id'] = intval($val['id']);
            $node['name'] = _l('task').': '.$val['name'];
            $node['type'] = 'task';
            $node['link'] = '<a href="#task' . $val['id'] . '" data-task-id="data_task_id_'.$val['id'].'">'.$node['name'].'</a>';
            $user_data[] = $node;
        }

        $clients = $this->clients_model->get($contact->userid);
        if($clients){
            $node = [];
            $node['id'] = intval($clients->userid);
            $node['name'] = _l('client').': '.$clients->company;
            $node['type'] = 'client';
            $node['link'] = '<a href="#client' . $clients->userid.'" data-client-id="data_client_id_'.$clients->userid.'">'.$node['name'].'</a>';
            $user_data[] = $node;
            
            $this->load->model('leads_model');
            if($clients->leadid != ''){
                $leads = $this->leads_model->get($clients->leadid);
                if($leads){
                    $node = [];
                    $node['id'] = intval($leads->id);
                    $node['name'] = _l('lead').': '.$leads->name;
                    $node['type'] = 'lead';
                    $node['link'] = '<a href="#lead' . $leads->id . '" data-lead-id="data_lead_id_'.$leads->id.'">'.$node['name'].'</a>';
                    $user_data[] = $node;
                }
            }
            
        }
        
        $this->load->model('tickets_model');
        $tickets = $this->tickets_model->get('', ['contactid' => $contact->id]);
        foreach($tickets as $key => $val)
        {
            $node = [];
            $node['id'] = intval($val['ticketid']);
            $node['name'] = _l('ticket').': '.$val['subject'];
            $node['type'] = 'ticket';
            $node['link'] = '<a href="'.site_url('clients/ticket/' . $val['ticketid']).'" data-ticket-id="data_ticket_id_'.$val['ticketid'].'">'.$node['name'].'</a>';
            $user_data[] = $node;
        }

        $this->load->model('proposals_model');
        $proposals = $this->proposals_model->get('', ['rel_id' => $contact->userid, 'rel_type' => 'customer']);
        foreach($proposals as $key => $val)
        {
            $node = [];
            $node['id'] = intval($val['id']);
            $node['name'] = _l('proposal').': '.format_proposal_number($val['id']);
            $node['type'] = 'proposal';
            $node['link'] = '<a href="' . site_url('clients/proposals#' . $val['id']) . '" data-proposal-id="data_proposal_id_'.$val['id'].'">'.$node['name'].'</a>';
            $user_data[] = $node;
        }

        $this->load->model('invoices_model');
        $invoices = $this->invoices_model->get('', ['clientid' => $contact->userid]);
        foreach($invoices as $key => $val)
        {
            $node = [];
            $node['id'] = intval($val['id']);
            $node['name'] = _l('invoice').': '.format_invoice_number($val['id']);
            $node['type'] = 'invoice';
            $node['link'] = '<a href="' . site_url('clients/invoices#' . $val['id']) . '" data-invoice-id="data_invoice_id_'.$val['id'].'">'.$node['name'].'</a>';
            $user_data[] = $node;
        }

        $this->load->model('credit_notes_model');
        $credit_notes = $this->credit_notes_model->get('', ['clientid' => $contact->userid]);
        foreach($credit_notes as $key => $val)
        {
            $node = [];
            $node['id'] = intval($val['id']);
            $node['name'] = _l('credit_note').': '.format_credit_note_number($val['id']);
            $node['type'] = 'credit_note';
            $node['link'] = '<a href="#credit_note' . $val['id'] . '" data-credit_note-id="data_credit_note_id_'.$val['id'].'">'.$node['name'].'</a>';
            $user_data[] = $node;
        }

        $this->load->model('estimates_model');
        $estimates = $this->estimates_model->get('', ['clientid' => $contact->userid]);
        foreach($estimates as $key => $val)
        {
            $node = [];
            $node['id'] = intval($val['id']);
            $node['name'] = _l('estimate').': '.format_estimate_number($val['id']);
            $node['type'] = 'estimate';
            $node['link'] = '<a href="' . site_url('clients/estimates#' . $val['id']) . '" data-estimate-id="data_estimate_id_'.$val['id'].'">'.$node['name'].'</a>';
            $user_data[] = $node;
        }

        $this->load->model('expenses_model');
        $expenses = $this->expenses_model->get('', ['clientid' => $contact->userid]);
        foreach($expenses as $key => $val)
        {
            $node = [];
            $node['id'] = intval($val['id']);
            $node['name'] = _l('expense').': '.$val['expense_name'];
            $node['type'] = 'expense';
            $node['link'] = '<a href="#expenses' . $val['id'] . '" data-expense-id="data_expense_id_'.$val['id'].'">'.$node['name'].'</a>';
            $user_data[] = $node;
        }

        $this->load->model('contracts_model');
        $contracts = $this->contracts_model->get('', ['client' => $contact->userid]);
        foreach($contracts as $key => $val)
        {
            $node = [];
            $node['id'] = intval($val['id']);
            $node['name'] = _l('contract').': '.$val['subject'];
            $node['type'] = 'contract';
            $node['link'] = '<a href="' . site_url('clients/contracts#' . $val['id']) . '" data-contract-id="data_contract_id_'.$val['id'].'">'.$node['name'].'</a>';
            $user_data[] = $node;
        }

        $this->load->model('payments_model');
        $this->db->select('*,' . db_prefix() . 'invoicepaymentrecords.id as paymentid');
        $this->db->from(db_prefix() . 'invoicepaymentrecords');
        $this->db->join(db_prefix() . 'payment_modes', '' . db_prefix() . 'invoicepaymentrecords.paymentmode = ' . db_prefix() . 'payment_modes.id', 'LEFT');
        $this->db->join(db_prefix() . 'invoices', '' . db_prefix() . 'invoices.id = ' . db_prefix() . 'invoicepaymentrecords.invoiceid');
        $this->db->where(db_prefix() . 'invoices.clientid', get_client_user_id());

        
        $payments = $this->db->get()->result_array();
        foreach($payments as $key => $val)
        {
            $node = [];
            $node['id'] = intval($val['id']);
            $node['name'] = _l('payment').': '.$val['name'].' #'. format_invoice_number($val['invoiceid']).' #'. _d($val['date']);
            $node['type'] = 'payment';
            $node['link'] = '<a href="#payment' . $val['id'] . '" data-payment-id="data_payment_id_'.$val['id'].'">'.$node['name'].'</a>';
            $user_data[] = $node;
        }

        $contacts = $this->clients_model->get_contacts($contact->userid);

        foreach($contacts as $key => $val)
        {
            $node = [];
            $node['id'] = intval($val['id']);
            $node['name'] = _l('contact').': '.$val['firstname'].' '.$val['lastname'];
            $node['type'] = 'contact';
            $node['link'] = '<a href="#contact' . $val['id'] . '?group=contacts">'.$node['name'].'</a>';
            $user_data[] = $node;
        }

        echo json_encode($user_data);
        die();
    }

    /* Post new comment by contact */
    public function add_comment()
    {   
        $this->load->model('mention_model');
        $contact = $this->clients_model->get_contact(get_contact_user_id());

        $data = $this->input->post();
        unset($data['url']);
        $data['postid'] = $data['postid'];
        $data['content'] = $this->reconvertAll($data['content']);

        $comment_id = $this->mention_model->add_comment_client($data, $contact);        
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

    /**
     * load mention ajax 
     * @param  string  $id  
     * @param  integer $page 
     * @return string        
     */
    public function load_mention_ajax($id = '', $page = 1){
        $this->load->model('mention_model');
        $contact = $this->clients_model->get_contact(get_contact_user_id());
        $posts    = $this->mention_model->load_mention_post_client($id ,$page, $contact);
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
                $response .= '<img src="'. contact_profile_image_url($contact->id,'thumb').'" data-toggle="tooltip" class="client-profile-image-small">';
            }else{
                $response .= staff_profile_image($post['creator'], [
                    'staff-profile-image-small',
                ]);
            }
            
            $response .= '</div>';
            $response .= '<div class="media-body">';
                if($post['is_contact'] == 1){
                    $response .= '<p class="media-heading no-mbot"><h5>' . $contact->firstname . ' ' .$contact->lastname . '</h5></p>';
                }else{
                    $response .= '<p class="media-heading no-mbot"><h5>' . get_staff_full_name($post['creator']) . '</h5></p>';
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
            if($post['is_contact'] == 1){
                if ($post['creator'] == get_contact_user_id()) {
                    $response .= '<div class="dropdown pull-right btn-post-options-wrapper dropdown_style">';
                    $response .= '<button class="btn btn-default dropdown-toggle btn-post-options btn-icon" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true"><i class="fa fa-globe"></i></button>';
                    $response .= '<ul class="dropdown-menu my-2">';
                    $response .= '<li><a href="#" onclick="delete_post_isn(' . $post['id'] . '); return false;"><i class="fa fa-trash" aria-hidden="true"></i> ' . _l('delete') . '</a></li>';
                    $response .= '</ul>';
                    $response .= '</div>';
                }
            }
            $response .= '<div class="post-content mtop20 display-block">';
            
            $response .= $this->convertAll($post['content']);

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
            $response .= '<a><img src="'. contact_profile_image_url($contact->id,'thumb').'" data-toggle="tooltip" class="client-profile-image-small"></a>';
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

    /**
     * convert All
     * @param  [string] $str
     * @return [string]     
     */
    function convertAll($str) { 
        $patterns = array();
        $patterns[] = admin_url('projects/view/');
        $patterns[] = admin_url('clients/client/');
        $patterns[] = admin_url('payments/payment/');
        $patterns[] = admin_url('clients/all_contacts/');
        $patterns[] = admin_url('invoices/list_invoices/');
        $patterns[] = admin_url('proposals/list_proposals/');
        $patterns[] = admin_url('estimates/list_estimates/');
        $patterns[] = admin_url('contracts/contract/');
        $patterns[] = admin_url('tickets/ticket/');
        $patterns[] = admin_url('tasks/view/');
        $patterns[] = admin_url('credit_notes/list_credit_notes/');
        $patterns[] = admin_url('expenses/list_expenses/');
        $patterns[] = admin_url('leads/index/');
        $patterns[] = admin_url('staff/profile/');

        $replacements = array();
        $replacements[] = site_url('clients/project/');
        $replacements[] = '#client';
        $replacements[] = '#payment';
        $replacements[] = '#contact';
        $replacements[] = site_url('clients/invoices#');
        $replacements[] = site_url('clients/proposals#');
        $replacements[] = site_url('clients/estimates#');
        $replacements[] = site_url('clients/contracts#');
        $replacements[] = site_url('clients/ticket/');
        $replacements[] = '#task';
        $replacements[] = '#credit_note';
        $replacements[] = '#expenses';
        $replacements[] = '#lead';
        $replacements[] = '#staff';

        $result = str_replace($patterns, $replacements, $str);

        return $result;
    }

    /**
     * reconvert All
     * @param  [string] $str
     * @return [string]     
     */
    function reconvertAll($str) { 
        $patterns = array();
        $patterns[] = site_url('clients/project/');
        $patterns[] = '#client';
        $patterns[] = '#payment';
        $patterns[] = '#contact';
        $patterns[] = site_url('clients/invoices#');
        $patterns[] = site_url('clients/proposals#');
        $patterns[] = site_url('clients/estimates#');
        $patterns[] = site_url('clients/contracts#');
        $patterns[] = site_url('clients/ticket/');
        $patterns[] = '#task';
        $patterns[] = '#credit_note';
        $patterns[] = '#expenses';
        $patterns[] = '#lead';
        $patterns[] = '#staff';

        $replacements = array();
        $replacements[] = admin_url('projects/view/');
        $replacements[] = admin_url('clients/client/');
        $replacements[] = admin_url('payments/payment/');
        $replacements[] = admin_url('clients/all_contacts/');
        $replacements[] = admin_url('invoices/list_invoices/');
        $replacements[] = admin_url('proposals/list_proposals/');
        $replacements[] = admin_url('estimates/list_estimates/');
        $replacements[] = admin_url('contracts/contract/');
        $replacements[] = admin_url('tickets/ticket/');
        $replacements[] = admin_url('tasks/view/');
        $replacements[] = admin_url('credit_notes/list_credit_notes/');
        $replacements[] = admin_url('expenses/list_expenses/');
        $replacements[] = admin_url('leads/index/');
        $replacements[] = admin_url('staff/profile/');

        $result = str_replace($patterns, $replacements, $str);

        return $result;
    }

    /**
     * add post
     */
    public function add_post(){
        $this->load->model('mention_model');
        $data = $this->input->post();
        $data['is_contact'] = 1;
        $data['creator'] = get_contact_user_id();
        $data['content'] = $this->reconvertAll($data['content']);
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

    /* Delete all post */
    public function delete_post($postid)
    {
        $this->load->model('mention_model');

        hooks()->do_action('before_delete_post', $postid);
        echo json_encode([
            'success' => $this->mention_model->delete_post($postid),
        ]);
    }

}
