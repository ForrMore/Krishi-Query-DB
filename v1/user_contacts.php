<?php

require_once '../includes/DbOperations.php';

$response = array();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    if (!verifyRequiredParams(array('user_id','merchant_type','merchant_name','merchant_mobile','process'))) {
        //getting values
        $user_id = $_POST['user_id'];
        $merchant_name = $_POST['merchant_name'];
        $merchant_mobile = $_POST['merchant_mobile'];
        $merchant_type = $_POST['merchant_type'];
        $process = $_POST['process'];
        

        //creating $db operation object
        $db = new DbOperation();

        //adding user to database
        if ($process=='create'){
            $result = $db->add_contacts($user_id,$merchant_type,$merchant_name,$merchant_mobile);

            //making the response accordingly
            if ($result == USER_CONTACTS_CREATED) {
                $response['error'] = false;
                $response['message'] = 'contacts created successfully';
            } elseif ($result == USER_CONTACTS_NOT_CREATED) {
                $response['error'] = true;
                $response['message'] = 'contacts not created';
            }elseif ($result == USER_CONTACTS_EXIST){
                $response['error'] = false;
                $response['message'] = 'contact already eixst';
            }
        }elseif($process=='delete'){
            $result = $db->delete_contacts($user_id,$merchant_mobile);

            //making the response accordingly
            if ($result == USER_CONTACTS_DELETED) {
                $response['error'] = false;
                $response['message'] = 'contacts DELETED successfully';
            } elseif ($result == USER_CONTACTS_NOT_DELETED) {
                $response['error'] = true;
                $response['message'] = 'contacts not DELETED';
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