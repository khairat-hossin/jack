<!doctype html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <title>Simple Transactional Email</title>
    <style>
        /* -------------------------------------
            GLOBAL RESETS
        ------------------------------------- */

        /*All the styling goes here*/

        img {
            border: none;
            -ms-interpolation-mode: bicubic;
            max-width: 100%;
        }

        body {
            background-color: #f6f6f6;
            font-family: sans-serif;
            -webkit-font-smoothing: antialiased;
            font-size: 14px;
            line-height: 1.4;
            margin: 0;
            padding: 0;
            -ms-text-size-adjust: 100%;
            -webkit-text-size-adjust: 100%;
        }

        table {
            border-collapse: separate;
            mso-table-lspace: 0pt;
            mso-table-rspace: 0pt;
            width: 100%;
        }

        table td {
            font-family: sans-serif;
            font-size: 14px;
            vertical-align: top;
        }

        /* -------------------------------------
            BODY & CONTAINER
        ------------------------------------- */

        .body {
            background-color: #f6f6f6;
            width: 100%;
        }

        /* Set a max-width, and make it display as block so it will automatically stretch to that width, but will also shrink down on a phone or something */
        .container {
            display: block;
            margin: 0 auto !important;
            /* makes it centered */
            max-width: 580px;
            padding: 10px;
            width: 580px;
        }

        /* This should also be a block element, so that it will fill 100% of the .container */
        .content {
            box-sizing: border-box;
            display: block;
            margin: 0 auto;
            max-width: 580px;
            padding: 10px;
        }

        /* -------------------------------------
            HEADER, FOOTER, MAIN
        ------------------------------------- */
        .main {
            background: #ffffff;
            border-radius: 3px;
            width: 100%;
        }

        .wrapper {
            box-sizing: border-box;
            padding: 20px;
        }

        .content-block {
            padding-bottom: 10px;
            padding-top: 10px;
        }

        .footer {
            clear: both;
            margin-top: 10px;
            text-align: center;
            width: 100%;
        }

        .footer td,
        .footer p,
        .footer span,
        .footer a {
            color: #999999;
            font-size: 12px;
            text-align: center;
        }

        /* -------------------------------------
            TYPOGRAPHY
        ------------------------------------- */
        h1,
        h2,
        h3,
        h4 {
            color: #000000;
            font-family: sans-serif;
            font-weight: 400;
            line-height: 1.4;
            margin: 0;
            margin-bottom: 30px;
        }

        h1 {
            font-size: 35px;
            font-weight: 300;
            text-align: center;
            text-transform: capitalize;
        }

        p,
        ul,
        ol {
            font-family: sans-serif;
            font-size: 14px;
            font-weight: normal;
            margin: 0;
            margin-bottom: 15px;
        }

        p li,
        ul li,
        ol li {
            list-style-position: inside;
            margin-left: 5px;
        }

        a {
            color: #3498db;
            text-decoration: underline;
        }

        /* -------------------------------------
            BUTTONS
        ------------------------------------- */
        .btn {
            box-sizing: border-box;
            width: 100%;
        }

        .btn > tbody > tr > td {
            padding-bottom: 15px;
        }

        .btn table {
            width: auto;
        }

        .btn table td {
            background-color: #ffffff;
            border-radius: 5px;
            text-align: center;
        }

        .btn a {
            background-color: #ffffff;
            border: solid 1px #3498db;
            border-radius: 5px;
            box-sizing: border-box;
            color: #3498db;
            cursor: pointer;
            display: inline-block;
            font-size: 14px;
            font-weight: bold;
            margin: 0;
            padding: 12px 25px;
            text-decoration: none;
            text-transform: capitalize;
        }

        .btn-primary table td {
            background-color: #3498db;
        }

        .btn-primary a {
            background-color: #3498db;
            border-color: #3498db;
            color: #ffffff;
        }

        /* -------------------------------------
            OTHER STYLES THAT MIGHT BE USEFUL
        ------------------------------------- */
        .last {
            margin-bottom: 0;
        }

        .first {
            margin-top: 0;
        }

        .align-center {
            text-align: center;
        }

        .align-right {
            text-align: right;
        }

        .align-left {
            text-align: left;
        }

        .clear {
            clear: both;
        }

        .mt0 {
            margin-top: 0;
        }

        .mb0 {
            margin-bottom: 0;
        }

        .preheader {
            color: transparent;
            display: none;
            height: 0;
            max-height: 0;
            max-width: 0;
            opacity: 0;
            overflow: hidden;
            mso-hide: all;
            visibility: hidden;
            width: 0;
        }

        .powered-by a {
            text-decoration: none;
        }

        hr {
            border: 0;
            border-bottom: 1px solid #f6f6f6;
            margin: 20px 0;
        }

        /* -------------------------------------
            RESPONSIVE AND MOBILE FRIENDLY STYLES
        ------------------------------------- */
        @media only screen and (max-width: 620px) {
            table.body h1 {
                font-size: 28px !important;
                margin-bottom: 10px !important;
            }

            table.body p,
            table.body ul,
            table.body ol,
            table.body td,
            table.body span,
            table.body a {
                font-size: 16px !important;
            }

            table.body .wrapper,
            table.body .article {
                padding: 10px !important;
            }

            table.body .content {
                padding: 0 !important;
            }

            table.body .container {
                padding: 0 !important;
                width: 100% !important;
            }

            table.body .main {
                border-left-width: 0 !important;
                border-radius: 0 !important;
                border-right-width: 0 !important;
            }

            table.body .btn table {
                width: 100% !important;
            }

            table.body .btn a {
                width: 100% !important;
            }

            table.body .img-responsive {
                height: auto !important;
                max-width: 100% !important;
                width: auto !important;
            }
        }

        /* -------------------------------------
            PRESERVE THESE STYLES IN THE HEAD
        ------------------------------------- */
        @media all {
            .ExternalClass {
                width: 100%;
            }

            .ExternalClass,
            .ExternalClass p,
            .ExternalClass span,
            .ExternalClass font,
            .ExternalClass td,
            .ExternalClass div {
                line-height: 100%;
            }

            .apple-link a {
                color: inherit !important;
                font-family: inherit !important;
                font-size: inherit !important;
                font-weight: inherit !important;
                line-height: inherit !important;
                text-decoration: none !important;
            }

            #MessageViewBody a {
                color: inherit;
                text-decoration: none;
                font-size: inherit;
                font-family: inherit;
                font-weight: inherit;
                line-height: inherit;
            }

            .btn-primary table td:hover {
                background-color: #34495e !important;
            }

            .btn-primary a:hover {
                background-color: #34495e !important;
                border-color: #34495e !important;
            }
        }

    </style>
</head>
<body>
<span class="preheader">This is preheader text. Some clients will show this text as a preview.</span>
<table role="presentation" border="0" cellpadding="0" cellspacing="0" class="body">
    <tr>
        <td>&nbsp;</td>
        <td class="container">
            <div class="content">

                <!-- START CENTERED WHITE CONTAINER -->
                <table role="presentation" class="main">

                    <!-- START MAIN CONTENT AREA -->
                    <tr>
                        <td class="wrapper">
                            <table role="presentation" border="0" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td>
                                        <p>Hi there,</p>
                                        <p>This is automated generated report regarding your CRM system. Please find
                                            below a comprehensive overview of the reports associated with your <strong><?php echo get_option('companyname') ?>
                                                CRM</strong>. </p>
                                        <p><u><em>Report generated for dates <?php echo $generatedDates; ?></em></u></p>
                                        <table role="presentation" border="1" cellpadding="10" cellspacing="0"
                                               class="responsive-table" style="width: 100%; margin-top: 20px;">
                                            <thead>
                                            <tr>
                                                <th>Report Type</th>
                                                <th>Report</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <?php
                                            if (array_key_exists('total_customers', $generatedReport)) {
                                                ?>
                                                <tr>
                                                    <td>Total Customers</td>
                                                    <td style="font-weight: bold"><?php echo $generatedReport['total_customers'] ?>
                                                        Customer/s
                                                    </td>
                                                </tr>
                                                <?php
                                            }
                                            ?>
                                            <?php
                                            if (array_key_exists('total_expenses', $generatedReport)) {
                                                ?>
                                                <tr>
                                                    <td>Total Expenses</td>
                                                    <td style="font-weight: bold"><?php echo $generatedReport['total_expenses'] ?>
                                                        Expense/s
                                                    </td>
                                                </tr>
                                                <?php
                                            }
                                            ?>
                                            <?php
                                            if (array_key_exists('total_contracts', $generatedReport)) {
                                                ?>
                                                <tr>
                                                    <td>Total Contracts</td>
                                                    <td style="font-weight: bold"><?php echo $generatedReport['total_contracts'] ?>
                                                        Contract/s
                                                    </td>
                                                </tr>
                                                <?php
                                            }
                                            ?>
                                            <?php
                                            if (array_key_exists('total_leads', $generatedReport)) {
                                                ?>
                                                <tr>
                                                    <td>Total Leads</td>
                                                    <td style="font-weight: bold"><?php echo $generatedReport['total_leads'] ?>
                                                        Lead/s
                                                    </td>
                                                </tr>
                                                <?php
                                            }
                                            ?>
                                            <?php
                                            if (array_key_exists('total_converted_leads', $generatedReport)) {
                                                ?>
                                                <tr>
                                                    <td>Total Converted Leads</td>
                                                    <td style="font-weight: bold"><?php echo $generatedReport['total_converted_leads'] ?>
                                                        Lead/s
                                                    </td>
                                                </tr>
                                                <?php
                                            }
                                            ?>
                                            </tbody>
                                        </table>
                                        <?php
                                        if (array_key_exists('income_sum', $generatedReport) && array_key_exists('expense_sum', $generatedReport)) {

                                            $totalColor = 'green';
                                            $totalSum = $generatedReport['income_sum'] - $generatedReport['expense_sum'];

                                            if ($totalSum < 0) {
                                                $totalColor = 'darkred';
                                            }

                                            ?>
                                            <table role="presentation" border="1" cellpadding="10" cellspacing="0"
                                                   class="responsive-table" style="width: 100%; margin-top: 20px;">
                                                <thead>
                                                <tr>
                                                    <th>Payments</th>
                                                    <th>Expenses</th>
                                                    <th>Total</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <tr>
                                                    <td style="color: green; font-weight: bold"><?php echo app_format_money($generatedReport['income_sum'], get_base_currency()->id); ?></td>
                                                    <td style="color: darkred; font-weight: bold"><?php echo app_format_money($generatedReport['expense_sum'], get_base_currency()->id); ?></td>
                                                    <td style="color: <?php echo $totalColor; ?>; font-weight: bold"><?php echo app_format_money($totalSum, get_base_currency()->id); ?></td>
                                                </tr>
                                                </tbody>
                                            </table>
                                            <?php
                                        }
                                        ?>
                                        <?php
                                        if (array_key_exists('total_staff_converted_leads', $generatedReport)) {
                                            ?>
                                            <br>
                                            <h3>Top 10 Staff Members On Lead Conversions</h3>
                                            <table role="presentation" border="1" cellpadding="10" cellspacing="0"
                                                   class="responsive-table" style="width: 100%; margin-top: 20px;">
                                                <thead>
                                                <tr>
                                                    <th>Name</th>
                                                    <th>Total Converted Leads</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <?php
                                                foreach ($generatedReport['total_staff_converted_leads'] as $lead) {
                                                    ?>
                                                    <tr>
                                                        <td><?php echo $lead['staff']; ?></td>
                                                        <td style="font-weight: bold"><?php echo $lead['total_converted_leads']; ?></td>
                                                    </tr>
                                                    <?php
                                                }
                                                ?>
                                                </tbody>
                                            </table>
                                            <?php
                                        }
                                        ?>
                                        <?php
                                        if (array_key_exists('total_staff_tasks_completed', $generatedReport)) {
                                            ?>
                                            <br>
                                            <h3>Top 10 Staff Members With Completed Tasks</h3>
                                            <table role="presentation" border="1" cellpadding="10" cellspacing="0"
                                                   class="responsive-table" style="width: 100%; margin-top: 20px;">
                                                <thead>
                                                <tr>
                                                    <th>Name</th>
                                                    <th>Total Completed Tasks</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <?php
                                                foreach ($generatedReport['total_staff_tasks_completed'] as $task) {
                                                    ?>
                                                    <tr>
                                                        <td><?php echo $task['staff']; ?></td>
                                                        <td style="font-weight: bold"><?php echo $task['total_completed_tasks']; ?></td>
                                                    </tr>
                                                    <?php
                                                }
                                                ?>
                                                </tbody>
                                            </table>
                                            <?php
                                        }
                                        ?>
                                        <?php
                                        if (array_key_exists('total_staff_closed_tickets', $generatedReport)) {
                                            ?>
                                            <br>
                                            <h3>Top 10 Staff Members With Closed Tickets</h3>
                                            <table role="presentation" border="1" cellpadding="10" cellspacing="0"
                                                   class="responsive-table" style="width: 100%; margin-top: 20px;">
                                                <thead>
                                                <tr>
                                                    <th>Name</th>
                                                    <th>Total Closed Tickets</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <?php
                                                foreach ($generatedReport['total_staff_closed_tickets'] as $ticket) {
                                                    ?>
                                                    <tr>
                                                        <td><?php echo $ticket['staff']; ?></td>
                                                        <td style="font-weight: bold"><?php echo $ticket['total_closed_tickets']; ?></td>
                                                    </tr>
                                                    <?php
                                                }
                                                ?>
                                                </tbody>
                                            </table>
                                            <?php
                                        }
                                        ?>
                                        <?php
                                        if (array_key_exists('total_expenses_based_on_categories', $generatedReport)) {
                                            ?>
                                            <br>
                                            <h3>Total Expenses Based On Categories</h3>
                                            <table role="presentation" border="1" cellpadding="10" cellspacing="0"
                                                   class="responsive-table" style="width: 100%; margin-top: 20px;">
                                                <thead>
                                                <tr>
                                                    <th>Category</th>
                                                    <th>Total Expenses</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <?php
                                                foreach ($generatedReport['total_expenses_based_on_categories'] as $expense) {
                                                    ?>
                                                    <tr>
                                                        <td><?php echo $expense['category_name']; ?></td>
                                                        <td style="font-weight: bold"><?php echo $expense['total_expenses']; ?></td>
                                                    </tr>
                                                    <?php
                                                }
                                                ?>
                                                </tbody>
                                            </table>
                                            <?php
                                        }
                                        ?>
                                        <?php
                                        if (array_key_exists('count_total_payment_modes_payments', $generatedReport)) {
                                            ?>
                                            <br>
                                            <h3>Total Payments Based On Categories</h3>
                                            <table role="presentation" border="1" cellpadding="10" cellspacing="0"
                                                   class="responsive-table" style="width: 100%; margin-top: 20px;">
                                                <thead>
                                                <tr>
                                                    <th>Category</th>
                                                    <th>Total Payments</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <?php
                                                foreach ($generatedReport['count_total_payment_modes_payments'] as $payment) {
                                                    ?>
                                                    <tr>
                                                        <td><?php echo $payment['payment_mode']; ?></td>
                                                        <td style="font-weight: bold"><?php echo $payment['total_payments']; ?></td>
                                                    </tr>
                                                    <?php
                                                }
                                                ?>
                                                </tbody>
                                            </table>
                                            <?php
                                        }
                                        ?>
                                        <?php
                                        if (array_key_exists('tasks_report', $generatedReport) && array_key_exists('total_tasks_report', $generatedReport)) {
                                            ?>
                                            <br>
                                            <h2>Total Tasks: <u><?php echo $generatedReport['total_tasks_report'] ?>
                                                    Task/s</u></h2>
                                            <table role="presentation" border="1" cellpadding="10" cellspacing="0"
                                                   class="responsive-table" style="width: 100%; margin-top: 20px;">
                                                <thead>
                                                <tr>
                                                    <th>Status</th>
                                                    <th>Total</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <?php
                                                foreach ($generatedReport['tasks_report'] as $task) {
                                                    ?>
                                                    <tr>
                                                        <td><?php echo $task['status_name']; ?></td>
                                                        <td style="font-weight: bold"><?php echo $task['total_tasks']; ?></td>
                                                    </tr>
                                                    <?php
                                                }
                                                ?>
                                                </tbody>
                                            </table>
                                            <?php
                                        }
                                        ?>
                                        <?php
                                        if (array_key_exists('tickets_report', $generatedReport) && array_key_exists('total_tickets_report', $generatedReport)) {
                                            ?>
                                            <br>
                                            <h2>Total Tickets: <u><?php echo $generatedReport['total_tickets_report']; ?> Ticket/s</u></h2>
                                            <table role="presentation" border="1" cellpadding="10" cellspacing="0"
                                                   class="responsive-table" style="width: 100%; margin-top: 20px;">
                                                <thead>
                                                <tr>
                                                    <th>Status</th>
                                                    <th>Total</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <?php
                                                foreach ($generatedReport['tickets_report'] as $ticket) {
                                                    ?>
                                                    <tr>
                                                        <td><?php echo $ticket['status_name']; ?></td>
                                                        <td style="font-weight: bold"><?php echo $ticket['total_tickets']; ?></td>
                                                    </tr>
                                                    <?php
                                                }
                                                ?>
                                                </tbody>
                                            </table>
                                            <br>
                                            <?php
                                        }
                                        ?>
                                        <?php
                                        if (array_key_exists('invoices_report', $generatedReport) && array_key_exists('total_invoices_report', $generatedReport)) {
                                            ?>
                                            <br>
                                            <h2>Total Invoices: <u><?php echo $generatedReport['total_invoices_report']; ?> Invoice/s</u></h2>
                                            <table role="presentation" border="1" cellpadding="10" cellspacing="0"
                                                   class="responsive-table" style="width: 100%; margin-top: 20px;">
                                                <thead>
                                                <tr>
                                                    <th>Status</th>
                                                    <th>Total</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <?php
                                                foreach ($generatedReport['invoices_report'] as $invoice) {
                                                    ?>
                                                    <tr>
                                                        <td><?php echo $invoice['status_name']; ?></td>
                                                        <td style="font-weight: bold"><?php echo $invoice['total_invoice']; ?></td>
                                                    </tr>
                                                    <?php
                                                }
                                                ?>
                                                </tbody>
                                            </table>
                                            <?php
                                        }
                                        ?>
                                        <?php
                                        if (array_key_exists('proposals_report', $generatedReport) && array_key_exists('total_proposals_report', $generatedReport)) {
                                            ?>
                                            <br>
                                            <h2>Total Proposals: <u><?php echo $generatedReport['total_proposals_report']; ?> Proposal/s</u></h2>
                                            <table role="presentation" border="1" cellpadding="10" cellspacing="0"
                                                   class="responsive-table" style="width: 100%; margin-top: 20px;">
                                                <thead>
                                                <tr>
                                                    <th>Status</th>
                                                    <th>Total</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <?php
                                                foreach ($generatedReport['proposals_report'] as $proposal) {
                                                    ?>
                                                    <tr>
                                                        <td><?php echo $proposal['status_name']; ?></td>
                                                        <td style="font-weight: bold"><?php echo $proposal['total_proposals']; ?></td>
                                                    </tr>
                                                    <?php
                                                }
                                                ?>
                                                </tbody>
                                            </table>
                                            <?php
                                        }
                                        ?>
                                        <?php
                                        if (array_key_exists('estimates_report', $generatedReport) && array_key_exists('total_estimates_report', $generatedReport)) {
                                            ?>
                                            <br>
                                            <h2>Total Estimates: <u><?php echo $generatedReport['total_estimates_report']; ?> Estimate/s</u> </h2>
                                            <table role="presentation" border="1" cellpadding="10" cellspacing="0"
                                                   class="responsive-table" style="width: 100%; margin-top: 20px;">
                                                <thead>
                                                <tr>
                                                    <th>Status</th>
                                                    <th>Total</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <?php
                                                foreach ($generatedReport['estimates_report'] as $estimate) {
                                                    ?>
                                                    <tr>
                                                        <td><?php echo $estimate['status_name']; ?></td>
                                                        <td style="font-weight: bold"><?php echo $estimate['total_estimates']; ?></td>
                                                    </tr>
                                                    <?php
                                                }
                                                ?>
                                                </tbody>
                                            </table>
                                            <?php
                                        }
                                        ?>
                                        <?php
                                        if (array_key_exists('projects_report', $generatedReport) && array_key_exists('total_projects_report', $generatedReport)) {
                                            ?>
                                            <br>
                                            <h2>Total Projects: <u><?php echo $generatedReport['total_projects_report']; ?> Project/s</u></h2>
                                            <table role="presentation" border="1" cellpadding="10" cellspacing="0"
                                                   class="responsive-table" style="width: 100%; margin-top: 20px;">
                                                <thead>
                                                <tr>
                                                    <th>Status</th>
                                                    <th>Total</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <?php
                                                foreach ($generatedReport['projects_report'] as $project) {
                                                    ?>
                                                    <tr>
                                                        <td><?php echo $project['status_name']; ?></td>
                                                        <td style="font-weight: bold"><?php echo $project['total_projects']; ?></td>
                                                    </tr>
                                                    <?php
                                                }
                                                ?>
                                                </tbody>
                                            </table>
                                            <?php
                                        }
                                        ?>
                                        <?php
                                        if (array_key_exists('project_logged_hours_and_expenses_report', $generatedReport) && array_key_exists('total_projects_report', $generatedReport)) {
                                            ?>
                                            <br>
                                            <h2>Projects Logged Hours And Expenses</u></h2>
                                            <table role="presentation" border="1" cellpadding="10" cellspacing="0"
                                                   class="responsive-table" style="width: 100%; margin-top: 20px;">
                                                <thead>
                                                <tr>
                                                    <th>Project</th>
                                                    <th>Total Logged Hours</th>
                                                    <th>Total Billed Hours</th>
                                                    <th>Total Expenses</th>
                                                    <th>Total Billed Expenses</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <?php
                                                foreach ($generatedReport['project_logged_hours_and_expenses_report'] as $projectId => $data) {
                                                    ?>
                                                    <tr>
                                                        <td><?php echo $data['project_name']; ?></td>
                                                        <td style="font-weight: bold"><?php echo $data['logged_hours']['hours']; ?> (<?php echo $data['logged_hours']['amount']; ?>)</td>
                                                        <td style="font-weight: bold"><?php echo $data['billed_hours']['hours']; ?> (<?php echo $data['billed_hours']['amount']; ?>)</td>
                                                        <td style="font-weight: bold"><?php echo $data['expenses']; ?></td>
                                                        <td style="font-weight: bold"><?php echo $data['billed_expenses']; ?></td>
                                                    </tr>
                                                    <?php
                                                }
                                                ?>
                                                </tbody>
                                            </table>
                                            <?php
                                        }
                                        ?>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- END MAIN CONTENT AREA -->
                </table>
                <!-- END CENTERED WHITE CONTAINER -->
                <div class="footer">
                    <table role="presentation" border="0" cellpadding="0" cellspacing="0">
                        <tr>
                            <td class="content-block powered-by">
                                Powered by <a href="https://codecanyon.net/user/lenzcreativee/portfolio">ReportPlus</a>.
                            </td>
                        </tr>
                    </table>
                </div>

            </div>
        </td>
        <td>&nbsp;</td>
    </tr>
</table>
</body>
</html>
