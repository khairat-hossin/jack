<?php defined('BASEPATH') or exit('No direct script access allowed');

class Google_authenticator extends AdminController
{
	public function __construct()
	{
		parent::__construct();
    $this->load->library(MODULE_GOOGLE_AUTHENTICATOR . '/GoogleAuthenticator');
  }

    /* Auto login by accepting email only */
    public function index()
    {
      if ($this->input->post()) {
        $_post     = $this->input->post();
        // echo '<pre>'; print_r($_post); exit;
        // also, you can get a code to test the service
        // $oneCode = $this->googleauthenticator->getCode($secret);
        $code = $this->input->post('code');
        $secret = $this->input->post('secret');
        // echo $secret."  | ".$code.'<br/>';

        // get the user's phone code and the secret code that was generated, and verify
        $checkResult = $this->googleauthenticator->verifyCode($secret, $code, 2); // 2 = 2*30sec clock tolerance

        if ($checkResult) {
            // echo 'OK';
            redirect(admin_url(''));
        } else {
          set_alert('danger', _l('Code is incorrect'));
            // echo 'FAILED';
            /* $this->load->model('authentication_model');
            $this->authentication_model->logout(false);
            hooks()->do_action('after_client_logout');
            redirect(admin_url('authentication')); */
        }
        // exit;

      }

      // generates the secret code
	    // $secret = $this->googleauthenticator->createSecret();
	    $secret = "G2Z7XKNCM3OBXNLC";

	    // generates the QR code for the link the user's phone with the service
	    $qrCodeUrl = $this->googleauthenticator->getQRCodeGoogleUrl('GP CRM', 'sales@gp.marketing', $secret);
      $data['title'] = 'Verify your 2FA using Google Authenticator';
      $data['qr_code_url'] = $qrCodeUrl;
      $data['secret'] = $secret;
// echo $qrCodeUrl;
      $this->load->view('google_authenticator', $data);
      
      // echo "here ".$qrCodeUrl; exit;
    }

}
?>