<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json; charset=utf-8');

include_once '../config/database.php';
include_once '../models/User.php';

$database = new Database();
$db = $database->getConnection();

$user = new User($db);

if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_GET['action']) && $_GET['action'] == 'register') {
    $data = json_decode(file_get_contents("php://input"));
    
    if(!empty($data->name) && !empty($data->email) && !empty($data->password) && !empty($data->college_id)) {
        
        if($user->emailExists($data->email)) {
            respond(array("message" => "البريد الإلكتروني مسجل مسبقاً"), 400);
        }
        
        $user->name = $data->name;
        $user->email = $data->email;
        $user->password = $data->password;
        $user->college_id = $data->college_id;
        $user->phone = $data->phone ?? '';
        
        if($user->register()) {
            respond(array("message" => "تم تسجيل المستخدم بنجاح", "success" => true));
        } else {
            respond(array("message" => "حدث خطأ أثناء التسجيل"), 500);
        }
    } else {
        respond(array("message" => "بيانات غير مكتملة"), 400);
    }
}

if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_GET['action']) && $_GET['action'] == 'login') {
    $data = json_decode(file_get_contents("php://input"));
    
    if(!empty($data->email) && !empty($data->password)) {
        $user_data = $user->login($data->email, $data->password);
        
        if($user_data) {
            respond(array(
                "message" => "تم تسجيل الدخول بنجاح",
                "success" => true,
                "user" => array(
                    "id" => $user_data['id'],
                    "name" => $user_data['name'],
                    "email" => $user_data['email'],
                    "college_id" => $user_data['college_id']
                )
            ));
        } else {
            respond(array("message" => "البريد الإلكتروني أو كلمة المرور غير صحيحة"), 401);
        }
    } else {
        respond(array("message" => "يرجى إدخال البريد الإلكتروني وكلمة المرور"), 400);
    }
}
?>