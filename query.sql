-- Create the database
CREATE DATABASE IF NOT EXISTS db_tran_van_manh;

-- Switch to the newly created database
USE db_tran_van_manh;

-- Create the table `courses` with the necessary fields
CREATE TABLE IF NOT EXISTS courses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    ImageUrl VARCHAR(255)
);

-- Optional: Insert some sample data for testing (these should match your file records)
INSERT INTO courses (title, description, ImageUrl) VALUES
('Laravel Programming', 'This is a longer card with supporting text below as a natural lead-in to additional content. This content is a little bit longer.', 'images/laravel.png'),
('.NET Programming', 'This is a longer card with supporting text below as a natural lead-in to additional content. This content is a little bit longer.', 'images/dot-net.png'),
('Spring Boot Programming', 'This is a longer card with supporting text below as a natural lead-in to additional content.', 'images/spring-boot.png'),
('Angular Programming', 'This is a longer card with supporting text below as a natural lead-in to additional content. This content is a little bit longer.', 'images/angular.png');
