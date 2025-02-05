<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php if (isset($client)) { ?>
<h4 class="customer-profile-group-heading"><?php echo _l('subscriptions_gpw'); ?></h4>
<?php if (has_permission('subscriptions', '', 'create')) { ?>
<a href="<?php echo admin_url('subscriptions_gpw/create?customer_id=' . $client->userid); ?>"
    class="btn btn-primary mbot15<?php echo $client->active == 0 ? ' disabled' : ''; ?>">
    <i class="fa-regular fa-plus tw-mr-1"></i>
    <?php echo _l('new_subscription'); ?>
</a>
<?php } ?>
<?php $this->load->view('admin/subscriptions_gpw/table_html', ['url' => admin_url('subscriptions_gpw/table?client_id=' . $client->userid)]); ?>
<?php } ?>