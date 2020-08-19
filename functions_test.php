<?php

function err_outputs($funcNameErr) {
    $dataPostTable = array(//change
        'post_title' => 'fail',
        'post_type' => 'testData',
        'post_content' => $funcNameErr . ' has returned empty'
    );
    wp_insert_post($dataPostTable);
}

function test_get_exam_name($exam) {
    $examinationID = $exam->ID;
    $examinationNameRaw = $exam->name;
    $examinationNameFilter = str_ireplace('quiz', '', $examinationNameRaw);
    $examinationName = str_ireplace('test', '', $examinationNameFilter);

    $examFormatting['examinationID'] = $examinationID;
    $examFormatting['examinationName'] = $examinationName;
    if ((empty($examFormatting))) {
        err_outputs('get_exam_name');
    }

    return $examFormatting;
}

function test_get_specific_quiz_data($quizDataBaseName, $user_ID, $examID) {
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

function test_get_specific_postmeta_data($postmetaDatabaseName, $userEmail) {
    global $wpdb;
    $postmetaDatabaseNameMetaVal = $postmetaDatabaseName . ".meta_value";
    $postIdSearch = "SELECT * FROM $postmetaDatabaseName where $postmetaDatabaseNameMetaVal = '$userEmail' LIMIT 1";
    $postIdSearchObj = $wpdb->get_row($postIdSearch, OBJECT);
    if ((empty($postIdSearchObj))) {
        err_outputs('get_specific_postmeta_data - postIdSearchObjEmpty');
    }
    $postedID = $postIdSearchObj->post_id;
    if ((empty($postedID))) {
        err_outputs('get_specific_postmeta_data - postedIDEmpty');
    }
    return $postedID;
}

function test_valExistence_check($postedID, $examinationName) {

    $errorVal = true;
    $valDoesExist = true;
    $valExistence1 = get_post_meta($postedID, 'wpsl_certification_url1', true);
    $valExistence2 = get_post_meta($postedID, 'wpsl_certification_url2', true);
    $valExistence3 = get_post_meta($postedID, 'wpsl_certification_url3', true);

    if (!empty($valExistence1) || !empty($valExistence2) || !empty($valExistence3)) {//here checkers
        $valDoesExist = true;
        if (empty($valExistence1)) {
            if (($valExistence2 != $examinationName) && ($valExistence3 != $examinationName)) {
                $errorVal = update_post_meta($postedID, 'wpsl_certification_url1', $examinationName);
                if ($errorVal == false) {
                    err_outputs('test_valExistence_check');
                }
            }
        } elseif (empty($valExistence2)) {
            if (($valExistence1 != $examinationName) && ($valExistence3 != $examinationName)) {
                $errorVal = update_post_meta($postedID, 'wpsl_certification_url2', $examinationName);
                if ($errorVal == false) {
                    err_outputs('test_valExistence_check');
                }
            }
        } elseif (empty($valExistence3)) {
            if (($valExistence1 != $examinationName) && ($valExistence2 != $examinationName)) {
                $errorVal = update_post_meta($postedID, 'wpsl_certification_url3', $examinationName);
                if ($errorVal == false) {
                    err_outputs('test_valExistence_check');
                }
            }
        }
    } else {
        $valDoesExist = false;
    }

    return $valDoesExist;
}

function test_update_all_data_to_wp_store_locator($idOfUser, $examName, $userEmail) {
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

    $insert_checker = wp_insert_post($dataPostTable);
    if ($insert_checker == 0) {
                    err_outputs('test_update_all_data_to_wp_store_locator');
                }
    
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

function test_check_quiz_pass($taking_id, $exam, $user_ID, $achieved, $g_id) {

    $quizDataBaseName = 'wpmn_watu_takings';
    $postmetaDatabaseName = 'wpmn_postmeta';
    $examInfo = test_get_exam_name($exam);
    $examID = $examInfo['examinationID'];
    $examName = $examInfo['examinationName'];
    $watuObj = test_get_specific_quiz_data($quizDataBaseName, $user_ID, $examID);
    $resultPassKeyword = 'Congratulations'; //change to 'pass' in development

    if (strpos($watuObj->result, $resultPassKeyword) !== false) {
        //initialized user info from usermeta
        $idOfUser = $watuObj->user_id;
        if ((empty($idOfUser))) {
            err_outputs('idOfUser');
        }
        $userEmail = get_user_meta($idOfUser, 'billing_email', true);
        if ((empty($userEmail))) {
            err_outputs('userEmail');
        }
        $postedID = test_get_specific_postmeta_data($postmetaDatabaseName, $userEmail);

        if (test_valExistence_check($postedID, $examName) == false) {
            test_update_all_data_to_wp_store_locator($idOfUser, $examName, $userEmail);
        }
    }
}
