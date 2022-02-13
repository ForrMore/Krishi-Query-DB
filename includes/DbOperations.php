<?php

class DbOperation{
    private $conn;
    
    //Constructor
    function __construct(){
        require_once dirname(__FILE__) . '/Constants.php';
        require_once dirname(__FILE__) . '/DbConnect.php';
        // opening db connection
        $db = new DbConnect();
        $this->conn = $db->connect();
    }
// CREATE ----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
    //Functions create user
    public function create_user($username, $mobile, $state, $district, $taluka){
        $stmt = $this->conn->prepare("INSERT INTO users (username, mobile_number, state, district, taluka) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss",$username, $mobile, $state, $district, $taluka);
        if ($stmt->execute()) {
            return USER_CREATED;
        } else {
            return USER_NOT_CREATED;
        }
    }

    public function add_buy_post($user_id,$commodity,$variety,$expected_price,$quantity,$unit,$product_description,$commodity_type,$post_date,$post_time){
        $stmt = $this->conn->prepare("INSERT INTO `buyer`(`user_id`, `commodity`, `variety`, `expected_price`, `quantity`, `unit`, `product_discription`, `commodity_is_type`, `post_date`, `post_time`) VALUES (?,?,?,?,?,?,?,?,?,?)");
        $stmt->bind_param("ssssssssss",$user_id,$commodity,$variety,$expected_price,$quantity,$unit,$product_description,$commodity_type,$post_date,$post_time);
        if ($stmt->execute()){
            return BUY_POST_CREATED;
        }else {
            return BUY_POST_NOT_CREATED;
        }
    }

    public function add_sell_post($user_id,$commodity,$variety,$expected_price,$quantity,$unit,$product_description,$commodity_type,$post_date,$post_time,$commodity_image_link){
        $stmt = $this->conn->prepare("INSERT INTO `seller`(`user_id`, `commodity`, `variety`, `expected_price`, `quantity`, `unit`, `product_discription`, `commodity_is_type`, `post_date`, `post_time`,`commodity_image_link`) VALUES (?,?,?,?,?,?,?,?,?,?,?)");
        $stmt->bind_param("sssssssssss",$user_id,$commodity,$variety,$expected_price,$quantity,$unit,$product_description,$commodity_type,$post_date,$post_time,$commodity_image_link);
        if ($stmt->execute()){
            return SELL_POST_CREATED;
        }else {
            return SELL_POS_NOT_CREATED ;
        }
    }

    //Function to create a auth
    public function create_auth($mobile,$otp){
        if ($this->isAuthExist($mobile)){
            $stmt = $this->conn->prepare("UPDATE `authentication` SET `verification_code`= ? WHERE authentication.mobile_number = ?");
            $stmt->bind_param("ss",$otp, $mobile);
            if ($stmt->execute()){
                return AUTH_CREATED;
            }
            else{
                return AUTH_NOT_CREATED;
            }
        }
        else{
            $stmt = $this->conn->prepare("INSERT INTO authentication(mobile_number, verification_code) VALUES (?,?)");
            $stmt->bind_param("ss",$mobile, $otp);
            if ($stmt->execute()){
                    return AUTH_CREATED;
                }else{
                return AUTH_NOT_CREATED;
            }
        }   
    }
    
    // create merchant contacts
    public function add_contacts($user_id,$merchant_type,$merchant_name,$merchant_mobile){
        if ($this->is_contact_exit($user_id,$merchant_mobile)){
            return USER_CONTACTS_EXIST;
        }
        else{
            $stmt = $this->conn->prepare("INSERT INTO `user_contacts`(`user_id`, `merchant_name`, `merchant_mobile`, `merchant_type`) VALUES (?,?,?,?)");
            $stmt->bind_param("ssss",$user_id,$merchant_name,$merchant_mobile,$merchant_type);
            if ($stmt->execute()){
                return USER_CONTACTS_CREATED;
            }else {
                return USER_CONTACTS_NOT_CREATED ;
            }
        }   
    }

    
//DELETE -----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
    // delete merchant contacts
    public function delete_contacts($user_id,$merchant_mobile){
        $stmt = $this->conn->prepare("DELETE FROM `user_contacts` WHERE user_contacts.user_id = ? AND user_contacts.merchant_mobile = ?");
        $stmt->bind_param("ss",$user_id,$merchant_mobile);
        if ($stmt->execute()){
            return USER_CONTACTS_DELETED;
        }else {
            return USER_CONTACTS_NOT_DELETED ;
        }
    }
    // delete sell post
    public function delete_buy_post($id){
        $stmt = $this->conn->prepare("DELETE FROM `buyer` WHERE buyer.id = ?");
        $stmt->bind_param("s",$id);
        if ($stmt->execute()){
            return POST_DELETED;
        }else {
            return POST_NOT_DELETED ;
        }
    }

    //delete buy post
    public function delete_sell_post($id){
        $stmt = $this->conn->prepare("DELETE FROM `seller` WHERE seller.id = ?");
        $stmt->bind_param("s",$id);
        if ($stmt->execute()){
            return POST_DELETED;
        }else {
            return POST_NOT_DELETED ;
        }
    }


//UPDATE ---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
   
//Function to update_user_commodity
    public function update_user_commodity($user_id, $user_commodity){
        if ($this->isUserIdExist($user_id)){
            $stmt = $this->conn->prepare("UPDATE `commodity` SET user_commodity = ? WHERE user_id = ?");
            $stmt->bind_param("ss",$user_commodity, $user_id);
            if ($stmt->execute()){
                return COMMODITY_UPDATED;
            }
            else{
                return COMMODITY_NOT_UPDATED;
            }
        }else{
            $stmt = $this->conn->prepare("INSERT INTO `commodity`(`user_id`, `user_commodity`) VALUES (?,?)");
            $stmt->bind_param("ss",$user_id,$user_commodity);
            if ($stmt->execute()){
                    return COMMODITY_UPDATED;
                }else{
                    return COMMODITY_NOT_UPDATED;
            }
        }   
    }

    // update user profile 
    public function update_user($username,$state,$district,$taluka,$id,$mobile){
        $stmt = $this->conn->prepare("UPDATE `users` SET `username` = ?,`state` = ?,`district` = ?,`taluka` = ? WHERE `id` = ? AND `mobile_number` = ?");
        $stmt->bind_param("ssssss",$username,$state,$district,$taluka,$id,$mobile);
        if ($stmt->execute()){
            return USER_UPDATED;
        }
        else{
            return USER_NOT_UPDATED;
        }
    }

    public function update_userprofile_photolink($user_id,$mobile,$userprofile_image_link){
        print($user_id);
        print($mobile);
        print($userprofile_image_link);
        $stmt = $this->conn->prepare("UPDATE `users` SET users.`userprofile_image_link` ='".$userprofile_image_link."' WHERE `id` =".$user_id." AND `mobile_number` = '".$mobile."'");
        if ($stmt->execute()){
            return USER_PROFILE_PHOTO_UPDATED;
        }
        else{
            return USER_PROFILE_PHOTO_NOT_UPDATED;
        }
    }
    
// USER VERIFY OR EXIST---------------------------------------------------------------------------------------------------------------------------------------------------------
    // verify_auth
    public function verify_otp($mobile,$otp){
        $stmt = $this->conn->prepare("SELECT * FROM `authentication` WHERE authentication.mobile_number = ? AND authentication.verification_code = ?");
        $stmt->bind_param("ss",$mobile,$otp);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0){
            if ($this->isUserExist($mobile)) {
                return USER_ALREADY_EXIST;
            }else{
                return USER_VERIFIED;
            }
        }else{
            return USER_NOT_VERIFIED;
        }
    }

    //is user exist in users
    private function isUserExist($mobile){
        $stmt = $this->conn->prepare("SELECT * FROM users WHERE users.mobile_number = ?");
        $stmt->bind_param("s",$mobile);
        $stmt->execute();
        $stmt->store_result();
        return $stmt->num_rows > 0;
    }
    // is auth exist 
    private function isAuthExist($mobile){
        $stmt = $this->conn->prepare("SELECT * FROM `authentication` WHERE authentication.mobile_number = ?");
        $stmt->bind_param("s",$mobile);
        $stmt->execute();
        $stmt->store_result();
        return $stmt->num_rows > 0;
    }
    
    // is user id exist
    private function isUserIdExist($user_id){
        $stmt = $this->conn->prepare("SELECT * FROM `commodity` WHERE commodity.user_id = ?");
        $stmt->bind_param("s",$user_id);
        $stmt->execute();
        $stmt->store_result();
        return $stmt->num_rows > 0;
    }

    public function user_post_data_exist($user_id,$process){
        if ($process=='buy'){
            $stmt = $this->conn->prepare("SELECT * FROM `buyer` WHERE buyer.user_id = ?");
            $stmt->bind_param("s",$user_id);
            $stmt->execute();
            $stmt->store_result();
            return $stmt->num_rows > 0;

        }elseif ($process=='sell'){
            $stmt = $this->conn->prepare("SELECT * FROM `seller` WHERE seller.user_id = ?");
            $stmt->bind_param("s",$user_id);
            $stmt->execute();
            $stmt->store_result();
            return $stmt->num_rows > 0;
        }
    }

    // buyer post data exist
    public function buyer_post_data_exist($state,$process){
        if ($process=='near'){
            $stmt = $this->conn->prepare("SELECT * FROM `buyer` LEFT JOIN users ON buyer.user_id = users.id WHERE users.state = ?");
            $stmt->bind_param("s",$state);
            $stmt->execute();
            $stmt->store_result();
            return $stmt->num_rows > 0;

        }elseif ($process=='all'){
            $stmt = $this->conn->prepare("SELECT * FROM `buyer`");
            $stmt->execute();
            $stmt->store_result();
            return $stmt->num_rows > 0;
        }
    }
    
     // buyer post data exist
     public function seller_post_data_exist($state,$process){
        if ($process=='near'){
            $stmt = $this->conn->prepare("SELECT * FROM `seller` LEFT JOIN users ON seller.user_id = users.id WHERE users.state = ?");
            $stmt->bind_param("s",$state);
            $stmt->execute();
            $stmt->store_result();
            return $stmt->num_rows > 0;

        }elseif ($process=='all'){
            $stmt = $this->conn->prepare("SELECT * FROM `seller`");
            $stmt->execute();
            $stmt->store_result();
            return $stmt->num_rows > 0;
        }
    }

    //contacts exist 
    private function is_contact_exit($user_id,$merchant_mobile){
        $stmt = $this->conn->prepare("SELECT * FROM `user_contacts` WHERE user_contacts.user_id = ? AND user_contacts.merchant_mobile = ?");
        $stmt->bind_param("ss",$user_id,$merchant_mobile);
        $stmt->execute();
        $stmt->store_result();
        return $stmt->num_rows > 0;
    }
    
    public function user_contact_exist($user_id){
        $stmt = $this->conn->prepare("SELECT * FROM `user_contacts` WHERE user_contacts.user_id = ? ");
        $stmt->bind_param("s",$user_id);
        $stmt->execute();
        $stmt->store_result();
        return $stmt->num_rows > 0;
    }



//GET USER DATA --------------------------------------------------------------------------------------------------------------------------------------------
    //get user id
    public function get_user_id($mobile){
        $stmt = $this->conn->prepare("SELECT id FROM users where users.mobile_number = ? ");
        $stmt->bind_param("s",$mobile);
        $stmt->execute();
        $stmt->bind_result($id);
        $stmt->fetch();
        return [$id,$mobile]; 
    }

    //get user data
    public function get_user_data($mobile){

        $stmt = $this->conn->prepare("SELECT * FROM users LEFT JOIN commodity ON users.id = commodity.user_id WHERE users.mobile_number = ?");
        $stmt->bind_param("s",$mobile);
        $stmt->execute();
        $stmt->bind_result($id,$username, $mobile, $state, $district, $taluka,$id,$user_id,$user_commodity,$commodity_image_link);
        
        $json_array = array();
        while($stmt->fetch()){
            $json_array = array(
                'id'=>$user_id,
                'username'=>$username,            
                'mobile'=>$mobile,
                'state'=>$state,
                'district'=>$district,
                'taluka'=>$taluka,
                'commodity'=>$user_commodity,
                'commodity_image_link'=>$commodity_image_link
            );// array
        }
        return json_encode($json_array); 
    }
    
    //user post data
    public function get_user_post_data($user_id,$mobile,$process){
        if ($process=="sell"){
            $stmt = $this->conn->prepare("SELECT seller.id,seller.user_id,users.username,users.mobile_number,users.state,users.district,users.taluka,seller.commodity,seller.variety,seller.expected_price,seller.quantity,seller.unit,seller.product_discription,seller.commodity_is_type,seller.post_date FROM `seller` LEFT JOIN users ON seller.user_id = users.id WHERE seller.user_id = ? AND users.mobile_number = ? ORDER BY seller.id DESC");
            $stmt->bind_param("ss",$user_id,$mobile);
            $stmt->execute();
            $stmt->bind_result($id,$user_id,$username, $mobile, $state, $district, $taluka,$commodity,$variety,$expected_price,$quantity,$unit,$product_description,$commodity_type,$post_date);
            
            $all_array = array();
            $json_array = array();
            while($stmt->fetch()){

                $json_array = array(
                        'post_id'=>$id,
                        'user_id'=>$user_id,
                        'username'=>$username,            
                        'mobile'=>$mobile,
                        'state'=>$state,
                        'district'=>$district,
                        'taluka'=>$taluka,
                        'commodity'=>$commodity,
                        'variety'=>$variety,
                        'expected_price'=>$expected_price,
                        'quantity'=>$quantity,
                        'unit'=>$unit,
                        'product_description'=>$product_description,
                        'commodity_type'=>$commodity_type,
                        'post_date'=>$post_date,
                );// array
                array_push($all_array,$json_array);
            }       
        }elseif ($process=="buy"){
            $stmt = $this->conn->prepare("SELECT buyer.id,buyer.user_id,users.username,users.mobile_number,users.state,users.district,users.taluka,buyer.commodity,buyer.variety,buyer.expected_price,buyer.quantity,buyer.unit,buyer.product_discription,buyer.commodity_is_type,buyer.post_date FROM `buyer` LEFT JOIN users ON buyer.user_id = users.id WHERE buyer.user_id = ? AND users.mobile_number = ? ORDER BY buyer.id DESC");
            $stmt->bind_param("ss",$user_id,$mobile);
            $stmt->execute();
            $stmt->bind_result($id,$user_id,$username, $mobile, $state, $district, $taluka,$commodity,$variety,$expected_price,$quantity,$unit,$product_description,$commodity_type,$post_date);
            
            $all_array = array();
            $json_array = array();
            while($stmt->fetch()){

                $json_array = array(
                        'post_id'=>$id,
                        'user_id'=>$user_id,
                        'username'=>$username,            
                        'mobile'=>$mobile,
                        'state'=>$state,
                        'district'=>$district,
                        'taluka'=>$taluka,
                        'commodity'=>$commodity,
                        'variety'=>$variety,
                        'expected_price'=>$expected_price,
                        'quantity'=>$quantity,
                        'unit'=>$unit,
                        'product_description'=>$product_description,
                        'commodity_type'=>$commodity_type,
                        'post_date'=>$post_date,
                );// array
                array_push($all_array,$json_array);
            }
        }        
        return $all_array; 
    }


    // get merchant data for buyer 
    public function get_buyers_posts($state,$district,$taluka,$process){
        if ($process=="all"){
            $stmt = $this->conn->prepare("SELECT buyer.id,buyer.user_id,users.username,users.mobile_number,users.state,users.district,users.taluka,buyer.commodity,buyer.variety,buyer.expected_price,buyer.quantity,buyer.unit,buyer.product_discription,buyer.commodity_is_type,buyer.post_date FROM `buyer` LEFT JOIN users ON buyer.user_id = users.id ORDER BY buyer.post_date DESC");
            $stmt->execute();
            $stmt->bind_result($id,$user_id,$username, $mobile, $state, $district, $taluka,$commodity,$variety,$expected_price,$quantity,$unit,$product_description,$commodity_type,$post_date);
            
            $all_array = array();
            $json_array = array();
            while($stmt->fetch()){

                $json_array = array(
                        'post_id'=>$id,
                        'user_id'=>$user_id,
                        'username'=>$username,            
                        'mobile'=>$mobile,
                        'state'=>$state,
                        'district'=>$district,
                        'taluka'=>$taluka,
                        'commodity'=>$commodity,
                        'variety'=>$variety,
                        'expected_price'=>$expected_price,
                        'quantity'=>$quantity,
                        'unit'=>$unit,
                        'product_description'=>$product_description,
                        'commodity_type'=>$commodity_type,
                        'post_date'=>$post_date,
                );// array
                array_push($all_array,$json_array);
            }       
        }elseif ($process=="near"){
            $stmt = $this->conn->prepare("SELECT buyer.id,buyer.user_id,users.username,users.mobile_number,users.state,users.district,users.taluka,buyer.commodity,buyer.variety,buyer.expected_price,buyer.quantity,buyer.unit,buyer.product_discription,buyer.commodity_is_type,buyer.post_date FROM `buyer` LEFT JOIN users ON buyer.user_id = users.id WHERE users.state = ? OR users.district = ? OR users.taluka = ? ORDER BY buyer.post_date DESC");
            $stmt->bind_param("sss",$state,$district,$taluka);
            $stmt->execute();
            $stmt->bind_result($id,$user_id,$username, $mobile, $state, $district, $taluka,$commodity,$variety,$expected_price,$quantity,$unit,$product_description,$commodity_type,$post_date);
            
            $all_array = array();
            $json_array = array();
            while($stmt->fetch()){

                $json_array = array(
                        'post_id'=>$id,
                        'user_id'=>$user_id,
                        'username'=>$username,            
                        'mobile'=>$mobile,
                        'state'=>$state,
                        'district'=>$district,
                        'taluka'=>$taluka,
                        'commodity'=>$commodity,
                        'variety'=>$variety,
                        'expected_price'=>$expected_price,
                        'quantity'=>$quantity,
                        'unit'=>$unit,
                        'product_description'=>$product_description,
                        'commodity_type'=>$commodity_type,
                        'post_date'=>$post_date,
                );// array
                array_push($all_array,$json_array);
            }
        }return $all_array; 
    }

  
  //get merchant data for seller 
    
    public function get_sellers_posts($state,$district,$taluka,$process){
        if ($process=="all"){
            $stmt = $this->conn->prepare("SELECT seller.id,seller.user_id,users.username,users.mobile_number,users.state,users.district,users.taluka,seller.commodity,seller.variety,seller.expected_price,seller.quantity,seller.unit,seller.product_discription,seller.commodity_is_type,seller.post_date,seller.commodity_image_link FROM `seller` LEFT JOIN users ON seller.user_id = users.id ORDER BY seller.post_date DESC");
            $stmt->execute();
            $stmt->bind_result($id,$user_id,$username, $mobile, $state, $district, $taluka,$commodity,$variety,$expected_price,$quantity,$unit,$product_description,$commodity_type,$post_date,$commodity_image_link);
            
            $all_array = array();
            $json_array = array();
            while($stmt->fetch()){

                $json_array = array(
                        'post_id'=>$id,
                        'user_id'=>$user_id,
                        'username'=>$username,            
                        'mobile'=>$mobile,
                        'state'=>$state,
                        'district'=>$district,
                        'taluka'=>$taluka,
                        'commodity'=>$commodity,
                        'variety'=>$variety,
                        'expected_price'=>$expected_price,
                        'quantity'=>$quantity,
                        'unit'=>$unit,
                        'product_description'=>$product_description,
                        'commodity_type'=>$commodity_type,
                        'post_date'=>$post_date,
                        'commodity_image_link'=>$commodity_image_link,
                );// array
                array_push($all_array,$json_array);
            }       
        }elseif ($process=="near"){
            $stmt = $this->conn->prepare("SELECT seller.id,seller.user_id,users.username,users.mobile_number,users.state,users.district,users.taluka,seller.commodity,seller.variety,seller.expected_price,seller.quantity,seller.unit,seller.product_discription,seller.commodity_is_type,seller.post_date,seller.commodity_image_link FROM `seller` LEFT JOIN users ON seller.user_id = users.id WHERE users.state = ? OR users.district = ? OR users.taluka = ? ORDER BY seller.post_date DESC");
            $stmt->bind_param("sss",$state,$district,$taluka);
            $stmt->execute();
            $stmt->bind_result($id,$user_id,$username, $mobile, $state, $district, $taluka,$commodity,$variety,$expected_price,$quantity,$unit,$product_description,$commodity_type,$post_date,$commodity_image_link);
            
            $all_array = array();
            $json_array = array();
            while($stmt->fetch()){

                $json_array = array(
                        'post_id'=>$id,
                        'user_id'=>$user_id,
                        'username'=>$username,            
                        'mobile'=>$mobile,
                        'state'=>$state,
                        'district'=>$district,
                        'taluka'=>$taluka,
                        'commodity'=>$commodity,
                        'variety'=>$variety,
                        'expected_price'=>$expected_price,
                        'quantity'=>$quantity,
                        'unit'=>$unit,
                        'product_description'=>$product_description,
                        'commodity_type'=>$commodity_type,
                        'post_date'=>$post_date,
                        'commodity_image_link'=>$commodity_image_link,
                );// array
                array_push($all_array,$json_array);
            }
        } return $all_array; 
    }

    // get user_contacts
    public function get_user_contacts($user_id){
        $stmt = $this->conn->prepare("SELECT * FROM user_contacts WHERE user_contacts.user_id = ? ORDER BY user_contacts.merchant_name ASC");
        $stmt->bind_param("s",$user_id);
        $stmt->execute();
        $stmt->bind_result($id,$user_id,$merchant_name,$merchant_mobile,$merchant_type);
        
        $all_array = array();
        $json_array = array();
        while($stmt->fetch()){

            $json_array = array(
                'merchant_name'=>$merchant_name,
                'merchant_mobile'=>$merchant_mobile,
                'merchant_type'=>$merchant_type,
            );// array
            array_push($all_array,$json_array);
        }
        return $all_array; 
    }    
}
?>
