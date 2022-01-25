<?php

require_once '../includes/DbOperations.php';

$response = array();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    if (!verifyRequiredParams(array('mobile'))) {
        //getting values
        $mobile = $_POST['mobile'];
        $otp = random_int(1000,9999);


        //creating db operation object
        $db = new DbOperation();

        //adding user to database
        $result = $db->create_auth($mobile, $otp);

        //making the response accordingly
        if ($result == AUTH_CREATED){
            $response['error'] = false;
            $response['otp'] = $otp;
            $response['message'] = 'Auth created successfully';
        }elseif ($result == AUTH_NOT_CREATED) {
            $response['error'] = true;
            $response['message'] = 'Some error occurred';
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