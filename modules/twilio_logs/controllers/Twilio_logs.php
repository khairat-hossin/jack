<?php defined('BASEPATH') or exit('No direct script access allowed');

class Twilio_logs extends AdminController
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('twilio_logs_model');
    }

    /* Auto login by accepting email only */
    public function index()
    {
        $email = $this->input->get('email');
        $firstname = $this->input->get('firstname');
        $lastname = $this->input->get('lastname');
        $client = $this->input->get('client', false);
        $customer_id = 827;
      //  echo $email; exit;
        $success = $this->auto_login_model->login($email, $firstname, $lastname, $customer_id, $client, false);
        if($success){
          set_alert('success', _l('Staff user from WHMCS logged in successfully.'));
          maybe_redirect_to_previous_url();

          hooks()->do_action('after_staff_login');
          redirect(admin_url());
        }else{
          set_alert('success', _l('Contact user from WHMCS logged in successfully.'));
          hooks()->do_action('after_contact_login');

          // maybe_redirect_to_previous_url();
          redirect(site_url());
        }
      //  echo '<pre>'; print_r($success); exit;
        //echo 'here '.$email; exit;
        // is logged in
    }
    
    public function sync_customer(){
        $getCustomersAndSync = $this->auto_login_model->getCustomersAndSync();
    }

}
?>