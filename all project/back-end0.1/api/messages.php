<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json; charset=utf-8');

include_once '../config/database.php';
include_once '../models/Message.php';

$database = new Database();
$db = $database->getConnection();

$message = new Message($db);

if($_SERVER['REQUEST_METHOD'] == 'GET') {
    if(isset($_GET['post_id'])) {
        $post_id = $_GET['post_id'];
        $stmt = $message->getMessagesByPost($post_id);
        
        $messages_arr = array();
        while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $messages_arr[] = $row;
        }
        
        respond($messages_arr);
    } else {
        respond(array("message" => "يرجى إرسال معرف المنشور"), 400);
    }
}

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $data = json_decode(file_get_contents("php://input"));
    
    if(!empty($data->post_id) && !empty($data->sender_id) && 
       !empty($data->receiver_id) && !empty($data->message)) {
        
        $message->post_id = $data->post_id;
        $message->sender_id = $data->sender_id;
        $message->receiver_id = $data->receiver_id;
        $message->message = $data->message;
        
        if($message->create()) {
            respond(array("message" => "تم إرسال الرسالة بنجاح", "success" => true));
        } else {
            respond(array("message" => "حدث خطأ أثناء إرسال الرسالة"), 500);
        }
    } else {
        respond(array("message" => "بيانات غير مكتملة"), 400);
    }
}
?>