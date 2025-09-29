<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json; charset=utf-8');

include_once '../config/database.php';
include_once '../models/College.php';

$database = new Database();
$db = $database->getConnection();

$college = new College($db);

if($_SERVER['REQUEST_METHOD'] == 'GET') {
    $stmt = $college->getAllColleges();
    $colleges_arr = array();
    
    while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $colleges_arr[] = $row;
    }
    
    respond($colleges_arr);
}

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $data = json_decode(file_get_contents("php://input"));
    
    if(isset($data->name)) {
        $stmt = $college->getCollegeByName($data->name);
        
        if($stmt->rowCount() > 0) {
            $college_data = $stmt->fetch(PDO::FETCH_ASSOC);
            respond($college_data);
        } else {
            respond(array("message" => "الكليّة غير موجودة"), 404);
        }
    } else {
        respond(array("message" => "يرجى إرسال اسم الكلية"), 400);
    }
}
?>c