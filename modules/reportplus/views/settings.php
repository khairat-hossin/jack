
<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">

            <?php echo form_open(admin_url('reportplus/settings')) ; ?>
            <div class="col-md-12">
                <h4 class="tw-mt-0 tw-font-semibold tw-text-lg tw-text-neutral-700">
                    <?php echo $title; ?>
                </h4>
                <div class="panel_s">
                    <div class="panel-body">

                        <div class="col-md-12">
                            <i class="fa-regular fa-circle-question pull-left tw-mt-0.5 tw-mr-1" data-toggle="tooltip"
                               data-title="<?php echo _l('reportplus_email_to_helper'); ?>"></i>
                            <?php echo render_input('settings[reportplus_settings_emails_to]', 'reportplus_email_to', get_option('reportplus_settings_emails_to')) ?>
                        </div>

                        <div class="col-md-6">
                                <?php echo render_select('settings[reportplus_automatic_assigned_reports][]', reportplus_report_types(), ['value', 'name'], 'reportplus_report_types', json_decode(get_option('reportplus_automatic_assigned_reports')),['multiple' => true, 'data-actions-box' => true],[],'','',false); ?>
                        </div>

                        <div class="col-md-6">
                            <?php echo render_yes_no_option('reportplus_enable_automatic_reports', 'reportplus_enable_automatic_reports'); ?>
                        </div>

                        <div class="col-md-12">
                            <?php echo render_select('settings[reportplus_automatic_report_interval]', reportplus_report_interval(), ['value', 'name'], 'reportplus_automatic_report_interval', get_option('reportplus_automatic_report_interval')); ?>
                        </div>

                        <div class="btn-bottom-toolbar text-right">
                            <button type="submit" class="btn btn-primary"><?php echo _l('save'); ?></button>
                        </div>
                    </div>
                </div>
            </div>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>
<?php init_tail(); ?>

