<?php
require_once '../../app/Core/init.php';
$user = Guard::requireAdmin();
?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Bulk Import Courses</title>
</head>
<body>
    <h1>Bulk Import Courses</h1>
    <p>Welcome, <?php echo escape($user->data()->username); ?>!</p>
    <form action="admin_courses_import.php" method="post" enctype="multipart/form-data">
        <label for="csv_file">Select CSV file:</label>
        <input type="file" name="csv_file" id="csv_file" accept=".csv" required>
        <button type="submit" name="import">Import Courses</button>
    </form>
    <?php
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['csv_file'])) {
        require_once '../../app/Models/CourseModel.php';

        $file = $_FILES['csv_file']['tmp_name'];
        $handle = fopen($file, 'r');
        if ($handle !== false) {
            $header = fgetcsv($handle); // Read header row
            $imported = 0;
            $errors = [];
            while (($row = fgetcsv($handle)) !== false) {
                // Map CSV columns: adjust indices as needed
                $code = trim($row[0]);
                $title = trim($row[1]);
                $description = trim($row[2]);
                $status = trim($row[3]);
                $department = trim($row[4]);
                $coordinator_user_id = trim($row[5]);

                // Basic validation
                if (!$code || !$title) {
                    $errors[] = "Missing code or title in row: " . implode(',', $row);
                    continue;
                }

                // Insert course (implement this in your CourseModel)
                $result = CourseModel::create([
                    'code' => $code,
                    'title' => $title,
                    'description' => $description,
                    'status' => $status,
                    'department' => $department,
                    'coordinator_user_id' => $coordinator_user_id
                ]);
                if ($result) {
                    $imported++;
                } else {
                    $errors[] = "Failed to import course: $code";
                }
            }
            fclose($handle);
            echo "<p>Imported $imported courses.</p>";
            if ($errors) {
                echo "<ul style='color:red;'>";
                foreach ($errors as $err) echo "<li>$err</li>";
                echo "</ul>";
            }
        } else {
            echo "<p style='color:red;'>Failed to open uploaded file.</p>";
        }
    }
    ?>
</body>
</html>
