<?php

require_once '../includes/DbOperations.php';

$response = array();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    if (!verifyRequiredParams(array('id','username', 'mobile', 'state', 'district', 'taluka'))) {
        //getting values
        $id = $_POST['id'];
        $username = $_POST['username'];
        $mobile = $_POST['mobile'];
        $state = $_POST['state'];
        $district = $_POST['district'];
        $taluka = $_POST['taluka'];

        //creating db operation object
        $db = new DbOperation();

        //adding user to database
        $result = $db->update_user($username,$state,$district,$taluka,$id,$mobile);

        //making the response accordingly
        if ($result == USER_UPDATED) {
            $response['error'] = false;
            $response['message'] = 'User updated successfully!';
        } elseif ($result == USER_NOT_UPDATED) {
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