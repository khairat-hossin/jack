<?php



defined('BASEPATH') or exit('No direct script access allowed');





/**

 * Check if can have permissions then apply new tab in settings

 */

if (staff_can('view', 'settings')) {

    hooks()->add_action('admin_init', 'call_logs_add_settings_tab');

}



/**

 * [call_logs_add_settings_tab net menu item in setup->settings]

 * @return void

 */

function call_logs_add_settings_tab()

{

    $CI = &get_instance();

    $CI->app_tabs->add_settings_tab('call_logs', [

        'name'     => '' . _l('call_logs_settings_name') . '',

        'view'     => 'call_logs/admin/call_logs_settings',

        'position' => 36,

    ]);

}



/**

 * Function used to get related data based on rel_id and rel_type

 * Eq in the tasks section there is field where this task is related eq invoice with number INV-0005

 * @param  string $type

 * @param  string $rel_id

 * @return mixed

 */

function get_relation_data_for_cl($type, $rel_id = '')

{

    $CI = & get_instance();

    $q  = '';

    if ($CI->input->post('q')) {

        $q = $CI->input->post('q');

        $q = trim($q);

    }



    $data = [];

    if ($type == 'proposal') {

        if ($rel_id != '') {

            $CI->load->model('proposals_model');

            $data = $CI->proposals_model->get($rel_id);

        } else {

            $search = $CI->call_logs_model->_search_proposals($q, $CI->input->post('rel_type'), $CI->input->post('rel_id'));

            $data   = $search['result'];

        }

    } elseif ($type == 'estimate') {

        if ($rel_id != '') {

            $CI->load->model('estimates_model');

            $data = $CI->estimates_model->get($rel_id);

        } else {

            $search = $CI->call_logs_model->_search_estimates($q);

            $data   = $search['result'];

        }

    }



    return $data;

}

/* Prepare the data/result for the query. */

function get_cl_list_query($aColumns, $sIndexColumn, $sTable, $join = [], $where = [], $additionalSelect = [], $sGroupBy = '', $searchAs = [])

{

    $CI          = & get_instance();

    $__post      = $CI->input->post();

    $havingCount = '';

    /*

     * Paging

     */

    $sLimit = '';

    if ((is_numeric($CI->input->post('start'))) && $CI->input->post('length') != '-1') {

        $sLimit = 'LIMIT ' . intval($CI->input->post('start')) . ', ' . intval($CI->input->post('length'));

    }

    $_aColumns = [];

    foreach ($aColumns as $column) {

        // if found only one dot

        if (substr_count($column, '.') == 1 && strpos($column, ' as ') === false) {

            $_column = explode('.', $column);

            if (isset($_column[1])) {

                if (startsWith($_column[0], db_prefix())) {

                    $_prefix = prefixed_table_fields_wildcard($_column[0], $_column[0], $_column[1]);

                    array_push($_aColumns, $_prefix);

                } else {

                    array_push($_aColumns, $column);

                }

            } else {

                array_push($_aColumns, $_column[0]);

            }

        } else {

            array_push($_aColumns, $column);

        }

    }



    /*

     * Ordering

     */

    $nullColumnsAsLast = get_null_columns_that_should_be_sorted_as_last();



    $sOrder = '';

    if ($CI->input->post('order')) {

        $sOrder = 'ORDER BY ';

        foreach ($CI->input->post('order') as $key => $val) {

            $columnName = $aColumns[intval($__post['order'][$key]['column'])];

            $dir        = strtoupper($__post['order'][$key]['dir']);



            if (strpos($columnName, ' as ') !== false) {

                $columnName = strbefore($columnName, ' as');

            }



            // first checking is for eq tablename.column name

            // second checking there is already prefixed table name in the column name

            // this will work on the first table sorting - checked by the draw parameters

            // in future sorting user must sort like he want and the duedates won't be always last

            if ((in_array($sTable . '.' . $columnName, $nullColumnsAsLast)

                || in_array($columnName, $nullColumnsAsLast))

        ) {

                $sOrder .= $columnName . ' IS NULL ' . $dir . ', ' . $columnName;

        } else {

            $sOrder .= hooks()->apply_filters('datatables_query_order_column', $columnName, $sTable);

        }

        $sOrder .= ' ' . $dir . ', ';

    }

    if (trim($sOrder) == 'ORDER BY') {

        $sOrder = '';

    }

    $sOrder = rtrim($sOrder, ', ');



    if (get_option('save_last_order_for_tables') == '1'

        && $CI->input->post('last_order_identifier')

        && $CI->input->post('order')) {

            // https://stackoverflow.com/questions/11195692/json-encode-sparse-php-array-as-json-array-not-json-object



        $indexedOnly = [];

    foreach ($CI->input->post('order') as $row) {

        $indexedOnly[] = array_values($row);

    }



    $meta_name = $CI->input->post('last_order_identifier') . '-table-last-order';



    update_staff_meta(get_staff_user_id(), $meta_name, json_encode($indexedOnly, JSON_NUMERIC_CHECK));

}

}

    /*

     * Filtering

     * NOTE this does not match the built-in DataTables filtering which does it

     * word by word on any field. It's possible to do here, but concerned about efficiency

     * on very large tables, and MySQL's regex functionality is very limited

     */

    $sWhere = '';

    if ((isset($__post['search'])) && $__post['search'] != '') {

        $search_value = $__post['search'];

        $search_value = trim($search_value);



        $sWhere             = 'WHERE (';

        $sMatchCustomFields = [];

        // Not working, do not use it

        $useMatchForCustomFieldsTableSearch = hooks()->apply_filters('use_match_for_custom_fields_table_search', 'false');



        for ($i = 0; $i < count($aColumns); $i++) {

            $columnName = $aColumns[$i];

            if (strpos($columnName, ' as ') !== false) {

                $columnName = strbefore($columnName, ' as');

            }



            if (stripos($columnName, 'AVG(') !== false || stripos($columnName, 'SUM(') !== false) {

            } else {

                if (isset($searchAs[$i])) {

                    $columnName = $searchAs[$i];

                }

                    // Custom fields values are FULLTEXT and should be searched with MATCH

                    // Not working ATM

                if ($useMatchForCustomFieldsTableSearch === 'true' && startsWith($columnName, 'ctable_')) {

                    $sMatchCustomFields[] = $columnName;

                } else {

                    $sWhere .= 'convert(' . $columnName . ' USING utf8)' . " LIKE '%" . $CI->db->escape_like_str($search_value) . "%' OR ";

                }

            }

        }



        if (count($sMatchCustomFields) > 0) {

            $s = $CI->db->escape_like_str($search_value);

            foreach ($sMatchCustomFields as $matchCustomField) {

                $sWhere .= "MATCH ({$matchCustomField}) AGAINST (CONVERT(BINARY('{$s}') USING utf8)) OR ";

            }

        }



        if (count($additionalSelect) > 0) {

            foreach ($additionalSelect as $searchAdditionalField) {

                if (strpos($searchAdditionalField, ' as ') !== false) {

                    $searchAdditionalField = strbefore($searchAdditionalField, ' as');

                }

                if (stripos($columnName, 'AVG(') !== false || stripos($columnName, 'SUM(') !== false) {

                } else {

                    // Use index

                    $sWhere .= 'convert(' . $searchAdditionalField . ' USING utf8)' . " LIKE '%" . $CI->db->escape_like_str($search_value) . "%' OR ";

                }

            }

        }

        $sWhere = substr_replace($sWhere, '', -3);

        $sWhere .= ')';

    }



    /*

     * SQL queries

     * Get data to display

     */

    $_additionalSelect = '';

    if (count($additionalSelect) > 0) {

        $_additionalSelect = ',' . implode(',', $additionalSelect);

    }

    $where = implode(' ', $where);

    if ($sWhere == '') {

        $where = trim($where);

        if (startsWith($where, 'AND') || startsWith($where, 'OR')) {

            if (startsWith($where, 'OR')) {

                $where = substr($where, 2);

            } else {

                $where = substr($where, 3);

            }

            $where = 'WHERE ' . $where;

        }

    }



    $join = implode(' ', $join);



    $sQuery = '

    SELECT SQL_CALC_FOUND_ROWS ' . str_replace(' , ', ' ', implode(', ', $_aColumns)) . ' ' . $_additionalSelect . "

    FROM $sTable

    " . $join . "

    $sWhere

    " . $where . "

    $sGroupBy

    $sOrder

    $sLimit

    ";



    $rResult = $CI->db->query($sQuery)->result_array();

    $str =  $CI->db->last_query();;



    $rResult = hooks()->apply_filters('datatables_sql_query_results', $rResult, [

        'table' => $sTable,

        'limit' => $sLimit,

        'order' => $sOrder,

    ]);



    /* Data set length after filtering */

    $sQuery = '

    SELECT FOUND_ROWS()

    ';

    $_query         = $CI->db->query($sQuery)->result_array();

    $iFilteredTotal = $_query[0]['FOUND_ROWS()'];

    if (startsWith($where, 'AND')) {

        $where = 'WHERE ' . substr($where, 3);

    }

    /* Total data set length */

    $sQuery = '

    SELECT COUNT(' . $sTable . '.' . $sIndexColumn . ")

    FROM $sTable " . $join . ' ' . $where;



    $_query = $CI->db->query($sQuery)->result_array();

    $iTotal = $_query[0]['COUNT(' . $sTable . '.' . $sIndexColumn . ')'];

    /*

     * Output

     */

    $output = [

        'draw'                 => $__post['draw'] ? intval($__post['draw']) : 0,

        'iTotalRecords'        => $iTotal,

        'iTotalDisplayRecords' => $iFilteredTotal,

        'aaData'               => [],

    ];



    return [

        'rResult' => $rResult,

        'output'  => $output,

        "query"     =>  $str

    ];

}

function twilio_setting()

{

    //$data['account_sid'] = 'AC02418a2ae0177ff7e49abbb968dc1222';
    $data['account_sid'] = get_option('sms_twilio_account_sid');

    //$data['auth_token'] = '378cc0f6917eee446221398153027a3f';
    $data['auth_token'] = get_option('sms_twilio_auth_token');

    //$data['twilio_number'] = "+12565619656";
    $data['twilio_number'] = get_option('sms_twilio_phone_number');

    $data['twiml_app_sid'] = get_option('twiml_app_sid');    

    $data['twiml_app_friendly_name'] = get_option('twiml_app_friendly_name');   
     
    $data['twiml_app_voice_request_url'] = get_option('twiml_app_voice_request_url');    

    $data['sms_twilio_active'] = get_option('sms_twilio_active');    

    return $data;

}

function clean_post($data)
{
    $errors = [];

    $data['message'] = strip_tags($data['message']);
    if (empty($data['message'])) {
        $errors['message'] = 'Message is required';
    }

    $phone_numbers = [];
    foreach ($data['phone_number'] as $each) {
        str_replace(' ', '', $each);
        str_replace('+', '', $each);
        if (empty($each)) continue;
        $phone_numbers[] = '+'.$each;
    }

    if (empty($phone_numbers)) {
        $errors['phone_number'] = 'Phone Number cannot be empty';
    }

    return [
        'errors' => $errors,
        'phone_numbers' => $phone_numbers,
        'message' => $data['message'],
        'call_back_url' => rawurlencode($data['call_back_url'])
    ];
}

function send_sms($array = [])
{

    $post = clean_post($array);
    if (!empty($post['error'])) {
            echo json_encode([
                'success' => false,
                'errors' => $post['error']
            ]);
            exit;
        }

        $conf = twilio_setting();
        // Your Account SID and Auth Token from twilio.com/console
        $account_sid = $conf["account_sid"];
        $auth_token = $conf["auth_token"];

        // A Twilio number you own with SMS capabilities
        $twilio_number = $conf["twilio_number"];

        $client = new Twilio\Rest\Client($account_sid, $auth_token);

        $sms_ids = [];
        $errors = [];
        $call_back_url = str_replace("\\", "" , rawurldecode($post['call_back_url']));
        foreach ($post['phone_numbers'] as $phone_number) {
            try {
                $message = $client->messages->create(
                    $phone_number,
                    array(
                        'from' => $twilio_number,
                        'body' => $post['message'],
                        'statusCallback' => $call_back_url
                    )
                );
                $sms_ids[] = $message->sid;
            } catch (\Throwable $e) {
                $errors[] = [
                    'phone_number' => $phone_number,
                    'message' => $e->getMessage()
                ];
            }
        }

        return json_encode([
            'success' => true,
            'sent' => $sms_ids,
            'errors'  => $errors
        ]);
}