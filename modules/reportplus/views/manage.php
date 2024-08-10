<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">

        <?php
        if (has_permission('reportplus', '', 'create')) {
            ?>
            <div class="row">
                <div class="col-md-12">
                    <h4 class="tw-mt-0 tw-font-semibold tw-text-lg tw-text-neutral-700">
                        <?php echo $title; ?>
                    </h4>
                    <div class="panel_s">
                        <div class="panel-body">
                            <?php echo form_open(admin_url('reportplus/generate_report'), ['id' => 'reportplus-form']); ?>

                            <div class="col-md-6">
                                <i class="fa-regular fa-circle-question pull-left tw-mt-0.5 tw-mr-1" data-toggle="tooltip"
                                   data-title="<?php echo _l('reportplus_email_to_helper'); ?>"></i>
                                <?php echo render_input('email_to', 'reportplus_email_to') ?>
                            </div>

                            <div class="col-md-6">
                                <?php echo render_select('assigned_reports[]', reportplus_report_types(), ['value', 'name'], 'reportplus_report_types', '', ['multiple' => true, 'data-actions-box' => true],[],'','',false); ?>
                            </div>

                            <div class="col-md-6">
                                <?php echo render_date_input('generate_from_date', 'reportplus_generate_from_date') ?>
                            </div>

                            <div class="col-md-6">
                                <?php echo render_date_input('generate_to_date', 'reportplus_generate_to_date') ?>
                            </div>

                            <div class="col-md-12">
                                <button type="submit"
                                        class="btn btn-primary pull-right generate-rep-btn"><?php echo _l('reportplus_generate_report'); ?></button>
                            </div>

                            <?php echo form_close(); ?>

                        </div>
                    </div>
                </div>
            </div>
            <?php
        }
        ?>

        <div class="row">
            <div class="col-md-12">
                <h4 class="tw-mt-0 tw-font-semibold tw-text-lg tw-text-neutral-700">
                    <?php echo _l('reportplus_generated_reports'); ?>
                </h4>
                <div class="panel_s">
                    <div class="panel-body">

                        <?php render_datatable([
                            _l('reportplus_email_to'),
                            _l('reportplus_report_dates'),
                            _l('created_at'),
                            _l('options'),
                        ], 'reportplus-generated'); ?>

                    </div>
                </div>
            </div>
        </div>

        <div class="btn-bottom-pusher"></div>
    </div>
</div>
<?php init_tail(); ?>
<script>
    $(function () {
        initDataTable('.table-reportplus-generated', window.location.href, [2], [2], [], [2, 'desc']);
    });

    appValidateForm($('#reportplus-form'), {
        'email_to': 'required',
        'assigned_reports': 'required',
        'generate_from_date': 'required',
        'generate_to_date': 'required'
    }, reportPlusSubmitHandler);

    function reportPlusSubmitHandler(form) {
        $('.generate-rep-btn').prop('disabled', false);
        $.post(form.action, $(form).serialize()).done(function (response) {
            response = JSON.parse(response);

            window.location.assign(response.url);
        });

    }

</script>
</body>
</html>
