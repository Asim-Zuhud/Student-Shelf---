<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json; charset=utf-8');

$response = array(
    "message" => "مرحباً في نظام شبكة الكتب - جامعة النجاح",
    "version" => "1.0",
    "endpoints" => array(
        "GET /api/colleges.php" => "الحصول على جميع الكليات",
        "POST /api/colleges.php" => "البحث عن كلية بالاسم", 
        "GET /api/posts.php?college_id=1" => "الحصول على منشورات كلية معينة",
        "POST /api/posts.php" => "إنشاء منشور جديد",
        "POST /api/auth.php?action=register" => "تسجيل مستخدم جديد",
        "POST /api/auth.php?action=login" => "تسجيل الدخول",
        "GET /api/messages.php?post_id=1" => "الحصول على رسائل منشور معين",
        "POST /api/messages.php" => "إرسال رسالة جديدة"
    ),
    "instructions" => "لبدء الاستخدام، قم أولاً بإنشاء قاعدة البيانات باستخدام ملف database.sql"
);

echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
?>