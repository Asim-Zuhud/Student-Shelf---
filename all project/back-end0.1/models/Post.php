<?php
class Post {
    private $conn;
    private $table = "posts";

    public $id;
    public $user_id;
    public $college_id;
    public $title;
    public $description;
    public $book_type;
    public $price;
    public $image_url;
    public $contact_info;
    public $status;
    public $created_at;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create() {
        $query = "INSERT INTO " . $this->table . " 
                 SET user_id=:user_id, college_id=:college_id, title=:title, 
                     description=:description, book_type=:book_type, price=:price,
                     image_url=:image_url, contact_info=:contact_info, status='نشط'";

        $stmt = $this->conn->prepare($query);

        $this->title = htmlspecialchars(strip_tags($this->title));
        $this->description = htmlspecialchars(strip_tags($this->description));
        $this->contact_info = htmlspecialchars(strip_tags($this->contact_info));

        $stmt->bindParam(":user_id", $this->user_id);
        $stmt->bindParam(":college_id", $this->college_id);
        $stmt->bindParam(":title", $this->title);
        $stmt->bindParam(":description", $this->description);
        $stmt->bindParam(":book_type", $this->book_type);
        $stmt->bindParam(":price", $this->price);
        $stmt->bindParam(":image_url", $this->image_url);
        $stmt->bindParam(":contact_info", $this->contact_info);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function getPostsByCollege($college_id) {
        $query = "SELECT p.*, u.name as user_name, c.name as college_name 
                  FROM " . $this->table . " p
                  LEFT JOIN users u ON p.user_id = u.id
                  LEFT JOIN colleges c ON p.college_id = c.id
                  WHERE p.college_id = :college_id AND p.status = 'نشط'
                  ORDER BY p.created_at DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":college_id", $college_id);
        $stmt->execute();
        return $stmt;
    }

    public function getPostById($id) {
        $query = "SELECT p.*, u.name as user_name, c.name as college_name 
                  FROM " . $this->table . " p
                  LEFT JOIN users u ON p.user_id = u.id
                  LEFT JOIN colleges c ON p.college_id = c.id
                  WHERE p.id = :id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        return $stmt;
    }

    public function updateStatus($id, $status) {
        $query = "UPDATE " . $this->table . " SET status = :status WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":status", $status);
        $stmt->bindParam(":id", $id);
        return $stmt->execute();
    }
}
?>