<?php

require_once '../includes/DbOperations.php';

$response = array();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    if (!verifyRequiredParams(array('id','process'))) {
        //getting values
        $id = $_POST['id'];
    
        $process = $_POST['process'];
       
    
        //creating $db operation object
        $db = new DbOperation();

        //adding user to database
        if ($process=='sell'){
            $result = $db->delete_sell_post($id);

            //making the response accordingly
            if ($result == POST_DELETED) {
                $response['error'] = false;
                $response['message'] = 'sell post deleted successfully';
            } elseif ($result == POST_NOT_DELETED) {
                $response['error'] = true;
                $response['message'] = 'sell post not deleted';
            }
        }elseif($process=='buy'){
            $result = $db->delete_buy_post($id);

            //making the response accordingly
            if ($result == POST_DELETED) {
                $response['error'] = false;
                $response['message'] = 'buy post deleted successfully';
            } elseif ($result == POST_NOT_DELETED){
                $response['error'] = true;
                $response['message'] = 'buy post not deleted';
            }
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