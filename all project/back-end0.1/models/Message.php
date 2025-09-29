<?php
class Message {
    private $conn;
    private $table = "messages";

    public $id;
    public $post_id;
    public $sender_id;
    public $receiver_id;
    public $message;
    public $created_at;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create() {
        $query = "INSERT INTO " . $this->table . " 
                 SET post_id=:post_id, sender_id=:sender_id, 
                     receiver_id=:receiver_id, message=:message";

        $stmt = $this->conn->prepare($query);

        $this->message = htmlspecialchars(strip_tags($this->message));

        $stmt->bindParam(":post_id", $this->post_id);
        $stmt->bindParam(":sender_id", $this->sender_id);
        $stmt->bindParam(":receiver_id", $this->receiver_id);
        $stmt->bindParam(":message", $this->message);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function getMessagesByPost($post_id) {
        $query = "SELECT m.*, u1.name as sender_name, u2.name as receiver_name
                  FROM " . $this->table . " m
                  LEFT JOIN users u1 ON m.sender_id = u1.id
                  LEFT JOIN users u2 ON m.receiver_id = u2.id
                  WHERE m.post_id = :post_id
                  ORDER BY m.created_at ASC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":post_id", $post_id);
        $stmt->execute();
        return $stmt;
    }
}
?>