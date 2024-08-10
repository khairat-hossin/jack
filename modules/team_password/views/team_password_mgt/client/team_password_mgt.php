<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

    <div class="col-md-3">
      <div id="tree"></div> 
    </div>
    <div class="col-md-9">
     <div class="panel_s">
      <div class="panel-body">

        <div class="horizontal-scrollable-tabs  mb-5">
          <div class="horizontal-tabs mb-4">
            <ul class="nav nav-tabs nav-tabs-horizontal">
               <li <?php if($type == 'all_password'){ echo 'class="active"'; } ?> >
                <a href="<?php echo site_url('team_password/team_password_client/team_password_mgt?cate='.$cate.'&type=all_password'); ?>" data-group="profile">
                 <i class="fa fa-bars menu-icon"></i>&nbsp;<?php echo _l('all_password'); ?></a>
               </li>
               <li <?php if($type == 'normal'){ echo 'class="active"'; } ?> >
                <a href="<?php echo site_url('team_password/team_password_client/team_password_mgt?cate='.$cate.'&type=normal'); ?>" data-group="profile">
                 <i class="fa fa-user-circle menu-icon"></i>&nbsp;<?php echo _l('normal'); ?></a>
               </li>
               <li <?php if($type == 'bank_account'){ echo 'class="active"'; } ?> >
                <a href="<?php echo site_url('team_password/team_password_client/team_password_mgt?cate='.$cate.'&type=bank_account'); ?>" data-group="contacts">
                 <i class="fa fa-university menu-icon"></i>&nbsp;<?php echo _l('bank_account'); ?></a>
               </li>
               <li <?php if($type == 'credit_card'){ echo 'class="active"'; } ?> >
                <a href="<?php echo site_url('team_password/team_password_client/team_password_mgt?cate='.$cate.'&type=credit_card'); ?>" data-group="notes">
                 <i class="fa fa-credit-card menu-icon"></i>&nbsp;<?php echo _l('credit_card'); ?></a>
               </li>
               <li <?php if($type == 'email'){ echo 'class="active"'; } ?> >
                <a href="<?php echo site_url('team_password/team_password_client/team_password_mgt?cate='.$cate.'&type=email'); ?>" data-group="reminders">
                 <i class="fa fa-envelope menu-icon"></i>&nbsp;<?php echo _l('email'); ?></a>
               </li>
               <li <?php if($type == 'server'){ echo 'class="active"'; } ?> >
                <a href="<?php echo site_url('team_password/team_password_client/team_password_mgt?cate='.$cate.'&type=server'); ?>" data-group="attachments">
                 <i class="fa fa-server menu-icon"></i>&nbsp;<?php echo _l('server'); ?></a>
               </li>
               <li class="<?php if($type == 'software_license'){ echo 'active'; } ?>" >
                <a href="<?php echo site_url('team_password/team_password_client/team_password_mgt?cate='.$cate.'&type=software_license'); ?>" data-group="attachments">
                 <i class="fa fa-pagelines menu-icon"></i>&nbsp;<?php echo _l('software_license'); ?></a>
               </li>

                </ul>
              </div>
              
               <?php $this->load->view('team_password_mgt/client/type_password/password'); ?>                   
             
              
            </div>

          </div>
        </div>


<script>
  (function(){
    "use strict";
    $('#tree').treeview({
      data:  <?php echo html_entity_decode($tree_cate); ?>,
      enableLinks: true,
    });
})(jQuery); 
</script>