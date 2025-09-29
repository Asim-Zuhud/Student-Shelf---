CREATE DATABASE IF NOT EXISTS university_books;
USE university_books;

CREATE TABLE colleges (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    college_id INT,
    phone VARCHAR(20),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (college_id) REFERENCES colleges(id)
);

CREATE TABLE posts (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    college_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    book_type ENUM('بيع', 'استعارة') NOT NULL,
    price DECIMAL(10,2) DEFAULT NULL,
    image_url VARCHAR(500),
    contact_info VARCHAR(255) NOT NULL,
    status ENUM('نشط', 'مباع', 'غير نشط') DEFAULT 'نشط',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (college_id) REFERENCES colleges(id)
);

CREATE TABLE messages (
    id INT PRIMARY KEY AUTO_INCREMENT,
    post_id INT NOT NULL,
    sender_id INT NOT NULL,
    receiver_id INT NOT NULL,
    message TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (post_id) REFERENCES posts(id),
    FOREIGN KEY (sender_id) REFERENCES users(id),
    FOREIGN KEY (receiver_id) REFERENCES users(id)
);

INSERT INTO colleges (name) VALUES 
('كلية الزراعة والطب البيطري'),
('كلية الأعمال والاتصال'),
('كلية العلوم الإنسانية والتربوية'),
('كلية العلوم'),
('كلية الهندسة وتكنولوجيا المعلومات'),
('كلية الشريعة'),
('كلية الطب وعلوم الصحة'),
('كلية القانون'),
('كلية الفنون الجميلة');

-- إضافة بعض البيانات التجريبية
INSERT INTO users (name, email, password, college_id, phone) VALUES 
('أحمد محمد', 'ahmed@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1, '0599123456'),
('فاطمة علي', 'fatima@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 2, '0599765432');

INSERT INTO posts (user_id, college_id, title, description, book_type, price, contact_info) VALUES 
(1, 1, 'مقدمة في علم النبات', 'كتاب ممتاز لحضور محاضرات النباتات', 'استعارة', NULL, 'ahmed@example.com'),
(2, 2, 'مبادئ التسويق', 'كتاب شامل لمبادئ التسويق الحديث', 'بيع', 50.00, 'fatima@example.com');