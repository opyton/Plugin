<?php

include 'functions_test.php';

/* Implementing Certification to Store Locator */
/**
 * Store Locator Certifications Label
 */
add_filter('wpsl_meta_box_fields', 'custom_meta_box_fields');

function custom_meta_box_fields($meta_fields) {

    $meta_fields[__('Additional Information', 'wpsl')] = array(
        'phone' => array(
            'label' => __('Tel', 'wpsl')
        ),
        'fax' => array(
            'label' => __('Fax', 'wpsl')
        ),
        'email' => array(
            'label' => __('Email', 'wpsl')
        ),
        'url' => array(
            'label' => __('Url', 'wpsl')
        ),
        'name_url' => array(
            'label' => __('Name', 'wpsl'),
        ),
        'certification_url1' => array(
            'label' => __('Certification1', 'wpsl'),
        ),
        'certification_url2' => array(
            'label' => __('Certification2', 'wpsl'),
        ),
        'certification_url3' => array(
            'label' => __('Certification3', 'wpsl'),
        ),
    );

    return $meta_fields;
}

/**
 * Adds Certification Label Data into JSON Response
 */
add_filter('wpsl_frontend_meta_fields', 'custom_frontend_meta_fields');

function custom_frontend_meta_fields($store_fields) {

    $store_fields['wpsl_name_url'] = array(
        'name' => 'name_url',
        'type' => 'text'
    );
    $store_fields['wpsl_certification_url1'] = array(
        'name' => 'certification_url1',
        'type' => 'text'
    );
    $store_fields['wpsl_certification_url2'] = array(
        'name' => 'certification_url2',
        'type' => 'text'
    );
    $store_fields['wpsl_certification_url3'] = array(
        'name' => 'certification_url3',
        'type' => 'text'
    );

    return $store_fields;
}

/**
 * Lists Items to Front End
 */
add_filter('wpsl_listing_template', 'custom_listing_template');

function custom_listing_template() {

    global $wpsl_settings;

    $listing_template = '<li data-store-id="<%= id %>">' . "\r\n";
    $listing_template .= "\t\t" . '<div>' . "\r\n";
    $listing_template .= "\t\t\t" . '<p><%= thumb %>' . "\r\n";
    $listing_template .= "\t\t\t\t" . wpsl_store_header_template('listing') . "\r\n";
    $listing_template .= "\t\t\t\t" . '<span class="wpsl-street"><%= address %></span>' . "\r\n";
    $listing_template .= "\t\t\t\t" . '<% if ( address2 ) { %>' . "\r\n";
    $listing_template .= "\t\t\t\t" . '<span class="wpsl-street"><%= address2 %></span>' . "\r\n";
    $listing_template .= "\t\t\t\t" . '<% } %>' . "\r\n";
    $listing_template .= "\t\t\t\t" . '<span>' . wpsl_address_format_placeholders() . '</span>' . "\r\n";
    $listing_template .= "\t\t\t\t" . '<span class="wpsl-country"><%= country %></span>' . "\r\n";
    $listing_template .= "\t\t\t" . '</p>' . "\r\n";

    // Set Name
    $listing_template .= "\t\t\t" . '<% if ( name_url ) { %>' . "\r\n";
    $listing_template .= "\t\t\t\t" . '<span class="wpsl-street"><%= name_url %></span>' . "\r\n";
    $listing_template .= "\t\t\t" . '<% } %>' . "\r\n";

    // Check if the 'certification_url' contains data before including it.
    $listing_template .= "\t\t\t" . '<% if ( certification_url1 ) { %>' . "\r\n";
    //$listing_template .= "\t\t\t\t" . '<span class="wpsl-street"> <b><u>Certifications:</u></b> </span>' . "\r\n"; Used for labeling
    $listing_template .= "\t\t\t\t" . '<span class="wpsl-street"><%= certification_url1 %></span>' . "\r\n";
    $listing_template .= "\t\t\t" . '<% } %>' . "\r\n";
    $listing_template .= "\t\t\t" . '<% if ( certification_url2 ) { %>' . "\r\n";
    $listing_template .= "\t\t\t\t" . '<span class="wpsl-street"><%= certification_url2 %></span>' . "\r\n";
    $listing_template .= "\t\t\t" . '<% } %>' . "\r\n";
    $listing_template .= "\t\t\t" . '<% if ( certification_url3 ) { %>' . "\r\n";
    $listing_template .= "\t\t\t\t" . '<span class="wpsl-street"><%= certification_url3 %></span>' . "\r\n";
    $listing_template .= "\t\t\t" . '<% } %>' . "\r\n";

    $listing_template .= "\t\t\t" . '</p>' . "\r\n";
    $listing_template .= "\t\t" . '</div>' . "\r\n";

    // Show the phone, fax or email data if they exist.
    $listing_template .= "\t\t\t" . '<p class="wpsl-contact-details">' . "\r\n";
    $listing_template .= "\t\t\t" . '<% if ( phone ) { %>' . "\r\n";
    $listing_template .= "\t\t\t\t" . '<span class="wpsl-street"><%= phone %></span>' . "\r\n";
    $listing_template .= "\t\t\t" . '<% } %>' . "\r\n";
    $listing_template .= "\t\t\t" . '<% if ( fax ) { %>' . "\r\n";
    $listing_template .= "\t\t\t\t" . '<span class="wpsl-street"><%= fax %></span>' . "\r\n";
    $listing_template .= "\t\t\t" . '<% } %>' . "\r\n";
    $listing_template .= "\t\t\t" . '<% if ( email ) { %>' . "\r\n";
    $listing_template .= "\t\t\t\t" . '<span class="wpsl-street"><a href="mailto:<%= email %>"><%= email %></a></span>' . "\r\n";
    $listing_template .= "\t\t\t" . '<% } %>' . "\r\n";
    $listing_template .= "\t\t\t" . '</p>' . "\r\n";

    // Check if we need to show the distance.
    if (!$wpsl_settings['hide_distance']) {
        $listing_template .= "\t\t" . '<%= distance %> ' . esc_html($wpsl_settings['distance_unit']) . '' . "\r\n";
    }

    $listing_template .= "\t\t" . '<%= createDirectionUrl() %>' . "\r\n";
    $listing_template .= "\t" . '</li>' . "\r\n";

    return $listing_template;
}

/* Implementing Certification to Store Locator */

/**
 * extracts name and id from (object)exam 
 * @param type object $exam
 * @return type array containing formatted id and name
 */
function get_exam_name($exam) {
    $examinationID = $exam->ID;
    $examinationNameRaw = $exam->name;
    $examinationNameFilter = str_ireplace('quiz', '', $examinationNameRaw);
    $examinationName = str_ireplace('test', '', $examinationNameFilter);

    $examFormatting['examinationID'] = $examinationID;
    $examFormatting['examinationName'] = $examinationName;
    return $examFormatting;
}

/**
 * 
 * @param type $quizDataBaseName
 * @param type $user_ID
 * @param type $examID
 * @return type object object containing first row data from $quizDataBaseName
 */
function get_specific_quiz_data($quizDataBaseName, $user_ID, $examID) {
    global $wpdb;
    $watuStr = "SELECT * FROM $quizDataBaseName
        WHERE user_id = $user_ID
        AND exam_id = $examID
        AND start_time > DATE_SUB(NOW(),INTERVAL 1 YEAR)
        ORDER BY ID DESC
        LIMIT 1";
    $watuObj = $wpdb->get_row($watuStr, OBJECT); //exam_id, user_id, date, result, start_time
    return $watuObj;
}

/**
 * 
 * @param type $postmetaDatabaseName
 * @param type $userEmail
 * @return type object object containing all data corresponding to a unique email
 */
function get_specific_postmeta_data($postmetaDatabaseName, $userEmail) {
    global $wpdb;
    $postmetaDatabaseNameMetaVal = $postmetaDatabaseName . ".meta_value";
    $postIdSearch = "SELECT * FROM $postmetaDatabaseName where $postmetaDatabaseNameMetaVal = '$userEmail' LIMIT 1"; //might fail postmeta.metavalue or missing ' for useremail
    $postIdSearchObj = $wpdb->get_row($postIdSearch, OBJECT);
    $postedID = $postIdSearchObj->post_id;
    return $postedID;
}

/**
 * 
 * @param type $postedID
 * @param type $examinationName
 * @return boolean
 */
function valExistence_check($postedID, $examinationName) {

    $valDoesExist = true;
    $valExistence1 = get_post_meta($postedID, 'wpsl_certification_url1', true);
    $valExistence2 = get_post_meta($postedID, 'wpsl_certification_url2', true);
    $valExistence3 = get_post_meta($postedID, 'wpsl_certification_url3', true);

    if (!empty($valExistence1) || !empty($valExistence2) || !empty($valExistence3)) {//here checkers
        $valDoesExist = true;
        if (empty($valExistence1)) {
            if (($valExistence2 != $examinationName) && ($valExistence3 != $examinationName)) {
                update_post_meta($postedID, 'wpsl_certification_url1', $examinationName);
            }
        } elseif (empty($valExistence2)) {
            if (($valExistence1 != $examinationName) && ($valExistence3 != $examinationName)) {
                update_post_meta($postedID, 'wpsl_certification_url2', $examinationName);
            }
        } elseif (empty($valExistence3)) {
            if (($valExistence1 != $examinationName) && ($valExistence2 != $examinationName)) {
                update_post_meta($postedID, 'wpsl_certification_url3', $examinationName);
            }
        }
    } else {
        $valDoesExist = false;
    }

    return $valDoesExist;
}

/**
 * updates all information obtained to wp store locator data
 * @param type $idOfUser
 * @param type $examName
 * @param type $userEmail
 */
function update_all_data_to_wp_store_locator($idOfUser, $examName, $userEmail) {
    global $wpdb;
    $userFirstName = get_user_meta($idOfUser, 'billing_first_name', true);
    $userLastName = get_user_meta($idOfUser, 'billing_last_name', true);
    $userFullName = $userFirstName . ' ' . $userLastName;
    $userPhone = get_user_meta($idOfUser, 'billing_phone', true);
    $userAddress = get_user_meta($idOfUser, 'billing_address_1', true);
    $userCity = get_user_meta($idOfUser, 'billing_city', true);
    $userState = get_user_meta($idOfUser, 'billing_state', true);
    $userZip = get_user_meta($idOfUser, 'billing_postcode', true);
    $userCompany = get_user_meta($idOfUser, 'billing_company', true); //title

    $dataPostTable = array(//change
        'post_author' => $idOfUser,
        'post_title' => $userCompany,
        'post_type' => 'wpsl_stores'
    );

    wp_insert_post($dataPostTable);

    $justCreatedPostId = $wpdb->insert_id;

    add_post_meta($justCreatedPostId, 'wpsl_address', $userAddress);
    add_post_meta($justCreatedPostId, 'wpsl_city', $userCity);
    add_post_meta($justCreatedPostId, 'wpsl_state', $userState);
    add_post_meta($justCreatedPostId, 'wpsl_zip', $userZip);
    add_post_meta($justCreatedPostId, 'wpsl_country', 'United States');
    add_post_meta($justCreatedPostId, 'wpsl_email', $userEmail);
    add_post_meta($justCreatedPostId, 'wpsl_name_url', $userFullName);
    add_post_meta($justCreatedPostId, 'wpsl_phone', $userPhone);
    add_post_meta($justCreatedPostId, 'wpsl_certification_url1', $examName); //default to cert1

    wp_publish_post($justCreatedPostId);

    $address = $userCity . " " . $userState . " " . $userZip;
    $latlng = wpsl_get_address_latlng($address);
    $coordinates = explode(',', $latlng);
    $lat = $coordinates[0];
    $lng = $coordinates [1];

    add_post_meta($justCreatedPostId, 'wpsl_lat', $lat);
    add_post_meta($justCreatedPostId, 'wpsl_lng', $lng);
    wp_publish_post($justCreatedPostId);
}

/**
 * Hook to watu_exam_submitted_detailed
 * That fills out WP Store Locator info when quiz is passed
 * @global type $wpdb
 * @param type $taking_id
 * @param type $exam
 * @param type $user_ID
 * @param type $achieved
 * @param type $g_id
 */
function check_quiz_pass($taking_id, $exam, $user_ID, $achieved, $g_id) {
    $quizDataBaseName = 'wpmn_watu_takings';
    $postmetaDatabaseName = 'wpmn_postmeta';
    $examInfo = get_exam_name($exam);
    $examID = $examInfo['examinationID'];
    $examName = $examInfo['examinationName'];
    $watuObj = get_specific_quiz_data($quizDataBaseName, $user_ID, $examID);
    $resultPassKeyword = 'Congratulations'; //change to 'pass' in development
    //result keyword check
    if (strpos($watuObj->result, $resultPassKeyword) !== false) {
        //initialized user info from usermeta
        $idOfUser = $watuObj->user_id;
        $userEmail = get_user_meta($idOfUser, 'billing_email', true);
        $postedID = get_specific_postmeta_data($postmetaDatabaseName, $userEmail);

        if (valExistence_check($postedID, $examName) == false) {
            update_all_data_to_wp_store_locator($idOfUser, $examName, $userEmail);
        }
    }
}

//add_action('watu_exam_submitted_detailed', 'check_quiz_pass', 10, 5);
add_action('watu_exam_submitted_detailed', 'test_check_quiz_pass', 10, 5);
