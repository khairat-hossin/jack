<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Mail_to_new_contact extends App_mail_template
{
    protected $for = 'contact';

    protected $contact;

    public $slug = 'team-password-mail-to-new-contact';

    public function __construct($contact)
    {
        parent::__construct();

        $this->contact = $contact;

        // For SMS and merge fields for email
        $this->set_merge_fields('mail_to_new_contact_merge_fields', $this->contact);
    }
    public function build()
    {
        $this->to($this->contact->email);
    }
}
