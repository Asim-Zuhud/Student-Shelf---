<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json; charset=utf-8');

include_once '../config/database.php';
include_once '../models/Post.php';
include_once '../models/College.php';

$database = new Database();
$db = $database->getConnection();

$post = new Post($db);
$college = new College($db);

if($_SERVER['REQUEST_METHOD'] == 'GET') {
    if(isset($_GET['college_id'])) {
        $college_id = $_GET['college_id'];
        $stmt = $post->getPostsByCollege($college_id);
        
        $posts_arr = array();
        while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $posts_arr[] = $row;
        }
        
        respond($posts_arr);
    } else {
        respond(array("message" => "يرجى إرسال معرف الكلية"), 400);
    }
}

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $data = json_decode(file_get_contents("php://input"));
    
    if(!empty($data->user_id) && !empty($data->college_id) && !empty($data->title) && 
       !empty($data->book_type) && !empty($data->contact_info)) {
        
        $post->user_id = $data->user_id;
        $post->college_id = $data->college_id;
        $post->title = $data->title;
        $post->description = $data->description ?? '';
        $post->book_type = $data->book_type;
        $post->price = $data->price ?? null;
        $post->image_url = $data->image_url ?? '';
        $post->contact_info = $data->contact_info;
        
        if($post->create()) {
            respond(array("message" => "تم إنشاء المنشور بنجاح", "success" => true));
        } else {
            respond(array("message" => "حدث خطأ أثناء إنشاء المنشور"), 500);
        }
    } else {
        respond(array("message" => "بيانات غير مكتملة"), 400);
    }
}

if($_SERVER['REQUEST_METHOD'] == 'PUT') {
    $data = json_decode(file_get_contents("php://input"));
    
    if(!empty($data->id) && !empty($data->status)) {
        if($post->updateStatus($data->id, $data->status)) {
            respond(array("message" => "تم تحديث حالة المنشور بنجاح", "success" => true));
        } else {
            respond(array("message" => "حدث خطأ أثناء التحديث"), 500);
        }
    } else {
        respond(array("message" => "بيانات غير مكتملة"), 400);
    }
}
?>