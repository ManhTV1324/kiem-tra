<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Import dữ liệu vào Database</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container my-3">
        <h2>Import dữ liệu vào Database</h2>

        <?php
        // Database connection variables
        $host = "localhost";
        $db = "db_tran_van_manh";
        $user = "root"; // Adjust if necessary
        $pass = "";     // Adjust if necessary

        // Check if form is submitted
        if (isset($_POST['submit'])) {
            if (isset($_FILES['file']) && $_FILES['file']['error'] == 0) {
                $fileTmpPath = $_FILES['file']['tmp_name'];
                $fileType = $_FILES['file']['type'];

                if ($fileType == 'text/plain') {
                    // Open the uploaded file
                    $file = fopen($fileTmpPath, 'r');

                    try {
                        // Connect to the database
                        $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
                        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                        // Create the database and table if they don't exist
                        $pdo->exec("CREATE DATABASE IF NOT EXISTS $db");
                        $pdo->exec("USE $db");
                        $pdo->exec("
                            CREATE TABLE IF NOT EXISTS courses (
                                id INT AUTO_INCREMENT PRIMARY KEY,
                                title VARCHAR(255) NOT NULL,
                                description TEXT,
                                imageUrl VARCHAR(255)
                            )
                        ");

                        // Insert data into the table
                        $successfulInserts = 0;
                        $failedInserts = 0;

                        while (($line = fgets($file)) !== false) {
                            $data = str_getcsv($line, ',', '"');
                            if (count($data) == 3) {
                                $title = trim($data[0]);
                                $description = trim($data[1]);
                                $imageUrl = trim($data[2]);

                                // Check if the course title already exists
                                $stmt = $pdo->prepare("SELECT COUNT(*) FROM courses WHERE title = :title");
                                $stmt->execute([':title' => $title]);

                                if ($stmt->fetchColumn() == 0) {
                                    // Insert the course if it doesn't exist
                                    $insertStmt = $pdo->prepare("INSERT INTO courses (title, description, imageUrl) VALUES (?, ?, ?)");
                                    if ($insertStmt->execute([$title, $description, $imageUrl])) {
                                        $successfulInserts++;
                                    } else {
                                        $failedInserts++;
                                    }
                                } else {
                                    $failedInserts++;
                                }
                            }
                        }

                        fclose($file);

                        // Display results of the import process
                        echo "<div class='alert alert-info mt-2' role='alert'>";
                        echo "$successfulInserts bản ghi chèn thành công, $failedInserts bản ghi bị trùng hoặc lỗi.";
                        echo "</div>";
                    } catch (PDOException $e) {
                        echo "<p>Kết nối thất bại: " . $e->getMessage() . "</p>";
                    }
                } else {
                    echo '<div class="alert alert-warning" role="alert">Vui lòng tải lên file .txt!</div>';
                }
            } else {
                echo '<div class="alert alert-danger" role="alert">Lỗi tải file!</div>';
            }
        }
        ?>

        <!-- Upload form -->
        <form class="row" method="POST" enctype="multipart/form-data">
            <div class="col">
                <div class="mb-3">
                    <input type="file" accept=".txt" class="form-control" name="file">
                </div>
                <button type="submit" class="btn btn-primary" name="submit">Import database</button>
            </div>
        </form>

        <hr>

        <h3>Danh sách khóa học</h3>
        <?php
        // Display the courses as cards
        try {
            $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $stmt = $pdo->query("SELECT * FROM courses");
            $courses = $stmt->fetchAll(PDO::FETCH_ASSOC);

            echo '<div class="row row-cols-1 row-cols-md-2 g-4">';
            if (count($courses) > 0) {
                foreach ($courses as $course) {
                    echo '<div class="col">';
                    echo '<div class="card h-100">';
                    echo '<img src="' . htmlspecialchars($course['ImageUrl']) . '" class="card-img-top" alt="' . htmlspecialchars($course['title']) . '" style="max-height: 200px; object-fit: cover;">';
                    echo '<div class="card-body">';
                    echo '<h5 class="card-title">' . htmlspecialchars($course['title']) . '</h5>';
                    echo '<p class="card-text">' . htmlspecialchars($course['description']) . '</p>';
                    echo '</div>';
                    echo '</div>';
                    echo '</div>';
                }
            } else {
                echo "<p>Không có khóa học nào.</p>";
            }
            echo '</div>';
        } catch (PDOException $e) {
            echo "<p>Kết nối thất bại: " . $e->getMessage() . "</p>";
        }
        ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
