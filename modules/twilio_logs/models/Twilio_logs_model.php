<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Auto_login_model extends App_Model
{
    public function __construct()
    {
        parent::__construct();
    }

     /**
     * @param  string Email address for login
     * @param  boolean Is Staff Or Client
     * @return boolean if not redirect url found, if found redirect to the url
     */
    public function login($email, $firstname, $lastname, $customer_id, $client, $staff)
    {
        
        if ( (!empty($email)) ) {

            if($client == 'true' || $client == true){
                $table = db_prefix() . 'contacts';
                $_id   = 'id';
                $this->db->where('email', $email);
                $user = $this->db->get($table)->row();
            }else {
                $table = db_prefix() . 'staff';
                $_id   = 'staffid';

                $this->db->where('email', $email);
                $user = $this->db->get($table)->row();
                $staff = true;
            }
            
            if ($user) {
            } else {
                if($client == 'true' || $client == true){
                    $url = site_url()."api/contacts";
                
                    $permissions_arr = ['1', '2', '3', '4', '5', '6' ];
                    $fields = array(
                        'customer_id' => $customer_id,
                        'firstname' => $firstname,
                        'lastname' => $lastname,
                        'email' => $email,
                        'password' => $email,
                        'is_primary' => 'on',
                        'permissions' => $permissions_arr
                    );
                }else {
                    // Create new staff user
                    $url = site_url()."api/staffs";

                    $fields = array(
                        'firstname' => $firstname,
                        'email' => $email,
                        'password' => $email
                    );
            
                    
                }
                
// echo "here"; exit;
                /*  */
                // echo $url;
                $fields_string = http_build_query($fields);
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_POST, 1);
                /* curl_setopt($ch, CURLOPT_POSTFIELDS,
                            "customer_id=".$customer_id."&firstname=".$firstname."&lastname=".$lastname."&email=".$email.""); */
                /* curl_setopt($ch, CURLOPT_POSTFIELDS,
                            "customer_id=".$customer_id."&firstname=".$firstname."&lastname=".$lastname."&email=".$email."&password=".$email."&is_primary=on&permissions=".$permissions_arr.""); */


                curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
                $headers = [
                    'authtoken: eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJ1c2VyIjoic2hhaGlkIiwibmFtZSI6InNoYWhpZCIsIkFQSV9USU1FIjoxNjU5NDYyMTQ5fQ.AMLyOxVPL67luMPU6bwPkxsPeOKGQ0Rf8OEF47D6Mk0'
                ];
                
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

                // In real life you should use something like:
                // curl_setopt($ch, CURLOPT_POSTFIELDS, 
                //          http_build_query(array('postvar1' => 'value1')));

                // Receive server response ...
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

                $server_output = curl_exec($ch);

                $get_info = curl_getinfo($ch);

                // echo '<pre>'; print_r($server_output); exit;
                curl_close ($ch);

                /* hooks()->do_action('non_existent_user_login_attempt', [
                    'email'           => $email,
                    'is_staff_member' => $staff,
                ]); */
// echo "here"; exit;
                // log_activity('Non Existing User Tried to Login [Email: ' . $email . ', Is Staff Member: ' . ($staff == true ? 'Yes' : 'No') . ', IP: ' . $this->input->ip_address() . ']');

                if($client == 'true' || $client == true){
                    $table = db_prefix() . 'contacts';
                    $_id   = 'id';
                    $this->db->where('email', $email);
                    $user = $this->db->get($table)->row();
                    $staff = false;
                }else{
                    $table = db_prefix() . 'staff';
                    $_id   = 'staffid';
                    $this->db->where('email', $email);
                    $user = $this->db->get($table)->row();
                    $staff = true;
                }
                // return false;
                
            }

            if ($user->active == 0) {
                hooks()->do_action('inactive_user_login_attempt', [
                    'user'            => $user,
                    'is_staff_member' => $staff,
                ]);
                log_activity('Inactive User Tried to Login [Email: ' . $email . ', Is Staff Member: ' . ($staff == true ? 'Yes' : 'No') . ', IP: ' . $this->input->ip_address() . ']');

                return [
                    'memberinactive' => true,
                ];
            }

            if ($staff == true) {

                hooks()->do_action('before_staff_login', [
                    'email'  => $email,
                    'userid' => $user->$_id,
                ]);

                $user_data = [
                    'staff_user_id'   => $user->$_id,
                    'staff_logged_in' => true,
                ];
                
            } else {
                hooks()->do_action('before_client_login', [
                    'email'           => $email,
                    'userid'          => $user->userid,
                    'contact_user_id' => $user->$_id,
                ]);

                $user_data = [
                    'client_user_id'   => $user->userid,
                    'contact_user_id'  => $user->$_id,
                    'client_logged_in' => true,
                ];
            }
          //  echo '<pre>'; print_r($user_data); exit;
            $this->session->set_userdata($user_data);

            $this->update_login_info($user->$_id, $staff);
            

            if ($staff == true) {
                return true;
            }else{
                return false;
            }
            
        }

        return false;
    }
    
    public function getCustomersAndSync() {
        $otherdb = $this->load->database('otherdb', TRUE); // the TRUE paramater tells CI that you'd like to return the database object.
      //  echo '<pre>'; print_r($otherdb); exit;
      $table = db_prefix() . 'cron';
        $_cron_key   = 'clients';
        $this->db->where('cron_key', $_cron_key);
        $cron_row = $this->db->get($table)->row();
        $last_run = $cron_row->last_run;
        $currentTime = date('Y-m-d H:i:s');

       // echo '<pre>'; print_r($cron_row); exit;

        $otherdb->where('updated_at >=', $last_run);
        $otherdb->where('updated_at <', $currentTime);
      //  $otherdb->where('id', 620);
        
        
        $otherdb->order_by('updated_at', 'DESC');

        $query = $otherdb->select('id, firstname, lastname, companyname, phonenumber, address1, city, email, state, postcode, country, updated_at')->get('tblclients');
        
        // company name, phone number, address, city, website, state, zip code, country
       // $otherdb->select('*');
         //  $query = $otherdb->get('tblclients');
        $companies = $query->result_array();
   //     print_r($otherdb->last_query());
 //      echo '<pre>'; print_r($companies); exit; 
        
        $this->load->model('clients_model');
        
        $names = json_decode(file_get_contents('http://country.io/names.json'), true);
        //echo '<pre>'; print_r($companies); exit;

        foreach($companies as $company){
            $table_name = db_prefix() . 'clients';
            $this->db->where('email_address', $company['email']);
            $client_exists = $this->db->get($table_name)->row();
            
            $table_name = db_prefix() . 'contacts';
            $this->db->where('email', $company['email']);
            $contact_exists = $this->db->get($table_name)->row();
            //echo '<pre>'; print_r($client_exists); //exit;
          //  echo '<pre>'; print_r($contact_exists); exit;
            if(isset($client_exists) || isset($contact_exists)){
                continue;
            }
            $data = array();
            $country     = $company['country'];
            $country_name = $names[$country];
            //echo $country_name; exit;
            $data['company']     = $company['companyname'];
            $data['phonenumber']     = $company['phonenumber'];
            $data['address']     = $company['address1'];
            $data['city']     = $company['city'];
            $data['email_address']     = $company['email'];
            $data['state']     = $company['state'];
            $data['zip']     = $company['postcode'];
            $data['datecreated']     = date('Y-m-d H:i:s');
            $data['whmcs_sync']     = 'yes';
            $countries = $this->clients_model->get_clients_distinct_countries();
            foreach($countries as $country) {
                if($country['short_name'] == $country_name){
                    $data['country'] = $country['country_id'];
                    break;
                }
            }
            // $data['country']     = $company['country'];
           // echo '<pre>'; print_r($data); //exit; 
            $this->db->insert(db_prefix() . 'clients', $data);
            $contact_id = $this->db->insert_id();
            
            $data_contact = array();
            $data_contact['is_primary'] = 1;
            $data_contact['userid'] = $contact_id;
            $data_contact['firstname'] = $company['firstname'];
            $data_contact['lastname'] = $company['lastname'];
            $data_contact['email'] = $company['email'];
            $data_contact['phonenumber'] = $company['phonenumber'];
            $data_contact['datecreated'] = date('Y-m-d H:i:s');
            $data_contact['password'] = 'test123';
            $data_contact['whmcs_sync']     = 'yes';
            $this->db->insert(db_prefix() . 'contacts', $data_contact);
        }
        //exit;
  // $response = array("response1"=>$result1,"response2"=>$result2);
  
  /*
    Sync projects to CRM
  */
  
        $otherdb->where('date_created >=', $last_run);
        $otherdb->where('date_created <', $currentTime);
      //  $otherdb->where('id', 620);
        
        
        $otherdb->order_by('date_created', 'DESC');
        $query = $otherdb->select('project_id, project_name, project_detail, project_status, date_start, date_finish, client_userid, tblclients.email')->join('tblclients', 'tblclients.id=tbladdon_wbteampro_project.client_userid')->get('tbladdon_wbteampro_project');
        $projects = $query->result_array();
   //     print_r($otherdb->last_query());
//        echo '<pre>'; print_r($projects); exit; 
$this->load->model('projects_model');
        foreach($projects as $project) {
            //$emailToSearch = $project['email'];
            
            $table_name = db_prefix() . 'projects';
            $this->db->where('name', $project['project_name']);
            $project_exists = $this->db->get($table_name)->row();
            if(isset($project_exists)){
                continue;
            }
            
            $table_name = db_prefix() . 'clients';
            $this->db->where('email_address', $project['email']);
            $client_exists = $this->db->get($table_name)->row();
            if(isset($client_exists)){
                $user_id = $client_exists->id;
            }
            
            $table_name = db_prefix() . 'contacts';
            $this->db->where('email', $project['email']);
            $contact_exists = $this->db->get($table_name)->row();
            if(isset($contact_exists)){
                $user_id = $contact_exists->userid;
            }
            
            $project_status = $project['project_status'];
            if($project_status == 'Active'){
                $status = 1;
            }else if($project_status == 'Cancelled'){
                $status = 5;
            }else if($project_status == 'Paused'){
                $status = 3;
            }else if($project_status == 'Completed'){
                $status = 4;
            }else if($project_status == 'In Production'){
                $status = 4;
            }
            
            $data = array();
           // $data[''] = $project['project_id'];
            $data['name'] = $project['project_name'];
            $data['description'] = $project['project_detail'];
            $data['status'] = $status;
            $data['start_date'] = $project['date_start'];
            $data['deadline'] = $project['date_finish'];
            $data['project_created'] = date('Y-m-d H:i:s');
            $data['clientid'] = $user_id;
            $data['whmcs_sync']     = 'yes';
           // $data[''] = $project['email'];
          // echo '<pre>'; print_r($data); //exit;
          
           $this->db->insert(db_prefix() . 'projects', $data);
        }
     //   exit;
  


$this->db->where('cron_key', $_cron_key);
        $this->db->update(db_prefix() . 'cron', [
            'last_run' => $currentTime,
        ]);
        echo sizeof($companies)." companies processed done"."<br/>";
        echo sizeof($projects)." projects processed done"."<br/>"; exit;
       // echo '<pre>'; print_r($result2); exit;
    }
    
    /**
     * @param  integer ID
     * @param  boolean Is Client or Staff
     * @return none
     * Update login info on autologin
     */
    private function update_login_info($user_id, $staff)
    {
        $table = db_prefix() . 'contacts';
        $_id   = 'id';
        if ($staff == true) {
            $table = db_prefix() . 'staff';
            $_id   = 'staffid';
        }
        $this->db->set('last_ip', $this->input->ip_address());
        $this->db->set('last_login', date('Y-m-d H:i:s'));
        $this->db->where($_id, $user_id);
        $this->db->update($table);

        log_activity('User Successfully Logged In [User Id: ' . $user_id . ', Is Staff Member: ' . ($staff == true ? 'Yes' : 'No') . ', IP: ' . $this->input->ip_address() . ']');
    }
}
