<?php

require_once '../includes/DbOperations.php';

$response = array();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    if (!verifyRequiredParams(array('user_id','mobile','userprofile_image_link'))) {
        //getting values
        $user_id = $_POST['user_id'];
        $mobile = $_POST['mobile'];
        $userprofile_image_link = $_POST['userprofile_image_link'];

        //creating $db operation object
        $db = new DbOperation();

        //adding user to database
        $result = $db->update_userprofile_photolink($user_id,$mobile,$userprofile_image_link);

        //making the response accordingly
        if ($result == USER_PROFILE_PHOTO_UPDATED) {
            $response['error'] = false;
            $response['message'] = 'profile photo updated successfully';
        } elseif ($result == USER_PROFILE_PHOTO_NOT_UPDATED) {
            $response['error'] = true;
            $response['message'] = 'profile photo not updated successfully';
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