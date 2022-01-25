<?php

require_once '../includes/DbOperations.php';

$response = array();

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    
    if (!verifyRequiredParams(array('mobile','otp'))) {
        //getting values
        $mobile = $_GET['mobile'];
        $otp = $_GET['otp'];

        //creating db operation object
        $db = new DbOperation();

        $result = $db->verify_otp($mobile,$otp);
        
        //making the response accordingly
        if ($result == USER_VERIFIED){
            $response['userExist'] = 1;
            $response['data'] = NUll;
            $response['error'] = false;
            $response['message'] = 'user verified successfully';
        } 
        elseif ($result == USER_ALREADY_EXIST) {
            $db = new DbOperation();
            $data = $db->get_user_data($mobile);
            $response['userExist'] = 0;
            $response['data'] = $data;
            $response['error'] = false;
            $response['message'] = 'user exist and verified';
        } 
        elseif ($result == USER_NOT_VERIFIED) {
            $response['error'] = true;
            $response['message'] = 'please enter correct otp';
        }


    } else {
        $response['error'] = true;
        $response['message'] = 'Required parameters are missing';
    }
} else {
    $response['error'] = true;
    $response['message'] = 'Invalid request';
}

//function to validate the required parameter in request
function verifyRequiredParams($required_fields){

    //Getting the request parameters
    $request_params = $_REQUEST;

    //Looping through all the parameters
    foreach ($required_fields as $field) {
        //if any requred parameter is missing
        if (!isset($request_params[$field]) || strlen(trim($request_params[$field])) <= 0) {

            //returning true;
            return true;
        }
    }
    return false;
}

echo json_encode($response);