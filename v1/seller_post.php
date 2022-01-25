<?php

require_once '../includes/DbOperations.php';

$response = array();

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    
    if (!verifyRequiredParams(array('state','district','taluka','process'))) {
        //getting values
        $state = $_GET['state'];
        $district = $_GET['district'];
        $taluka = $_GET['taluka'];
        $process = $_GET['process'];
        //creating $db operation object
        $db = new DbOperation();

        //adding user to database
        $result = $db->seller_post_data_exist($state,$process);
        $data = $db->get_sellers_posts($state,$district,$taluka,$process);

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