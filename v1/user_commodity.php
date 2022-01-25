<?php

require_once '../includes/DbOperations.php';

$response = array();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    if (!verifyRequiredParams(array('user_id','user_commodity','mobile'))) {
        //getting values
        $user_id = $_POST['user_id'];
        $user_commodity = $_POST['user_commodity'];
        $mobile = $_POST['mobile'];

        //creating db operation object
        $db = new DbOperation();

        //adding user to database
        $result = $db->update_user_commodity($user_id, $user_commodity);
        
        //making the response accordingly
        if ($result == COMMODITY_UPDATED) {
            $db = new DbOperation();
            $data = $db->get_user_data($mobile);
            $response['userExist'] = 0;
            $response['data'] = $data;
            $response['error'] = false;
            $response['message'] = 'user exist and verified AND COMMODITY UPDATED';
        } elseif ($result == COMMODITY_NOT_UPDATED) {
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
function verifyRequiredParams($required_fields)
{

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