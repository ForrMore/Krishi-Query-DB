<?php

require_once '../includes/DbOperations.php';

$response = array();

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    
    if (!verifyRequiredParams(array('user_id'))) {
        //getting values
        $user_id = $_GET['user_id'];

        //creating $db operation object
        $db = new DbOperation();

        //adding user to database
        $result = $db->user_contact_exist($user_id);
        $data = $db->get_user_contacts($user_id);
        //making the response accordingly
        if ($result > 0) {
            $response['data'] = $data;
            $response['data_exist'] = 1;
            $response['error'] = false;
            $response['message'] = 'data exist';
        } else{
            $response['data'] = $data;
            $response['error'] = false;
            $response['data_exist'] = 0;
            $response['message'] = 'no data';
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