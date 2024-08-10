<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Mail_to_new_contact_merge_fields extends App_merge_fields
{
    public function build()
    {
        return [
            [
                'name'      => 'Contact name',
                'key'       => '{contact_name}',
                'available' => [
                    'teampassword',
                ],
            ],
           
            [
                'name'      => 'Link',
                'key'       => '{link}',
                'available' => [
                    'teampassword',
                ],
            ],
        ];
    }

    /**
     * Merge field for appointments
     * @param  mixed $teampassword 
     * @return array
     */
    public function format($contact)
    {
        
        $fields['{contact_name}']                  = $contact->firstname.' '.$contact->lastname;
        $fields['{link}']                  = site_url('team_password/team_password_client/team_password_mgt');
        
        return $fields;
    }
}
