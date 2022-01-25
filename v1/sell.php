<?php

require_once '../includes/DbOperations.php';

$response = array();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    if (!verifyRequiredParams(array('user_id','commodity','variety','expected_price','quantity','unit','product_description','commodity_type','post_date','post_time'))) {
        //getting values
        $user_id = $_POST['user_id'];
        $commodity = $_POST['commodity'];
        $variety = $_POST['variety'];
        $expected_price = $_POST['expected_price'];
        $quantity = $_POST['quantity'];
        $unit = $_POST['unit'];
        $product_description = $_POST['product_description'];
        $commodity_type = $_POST['commodity_type'];
        $post_date = $_POST['post_date'];
        $post_time = $_POST['post_time'];
        $commodity_image_link = $_POST['commodity_image_link'];

        //creating $db operation object
        $db = new DbOperation();

        //adding user to database
        $result = $db->add_sell_post($user_id,$commodity,$variety,$expected_price,$quantity,$unit,$product_description,$commodity_type,$post_date,$post_time,$commodity_image_link);

        //making the response accordingly
        if ($result == SELL_POST_CREATED) {
            $response['error'] = false;
            $response['message'] = 'sell Post created successfully';
        } elseif ($result == SELL_POS_NOT_CREATED) {
            $response['error'] = true;
            $response['message'] = 'sell Post not created';
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