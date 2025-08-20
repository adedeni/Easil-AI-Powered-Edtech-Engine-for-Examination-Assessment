<?php
require_once '../../app/Core/init.php';
$user = Guard::requireAdmin();
require_once '../../app/Models/CourseModel.php';
require_once '../../app/Models/EnrollmentModel.php';

// Get course ID from query
$course_id = isset($_GET['course_id']) ? intval($_GET['course_id']) : 0;
$course = CourseModel::find($course_id);

if (!$course) {
    echo "<p>Invalid course selected.</p>";
    exit;
}

// Handle bulk enrollment
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['csv_file'])) {
    $file = $_FILES['csv_file']['tmp_name'];
    $handle = fopen($file, 'r');
    $imported = 0;
    $errors = [];
    if ($handle !== false) {
        $header = fgetcsv($handle); // Read header row
        while (($row = fgetcsv($handle)) !== false) {
            $student_identifier = trim($row[0]); // Could be ID or email

            // Find student by ID or email
            $student = EnrollmentModel::findStudent($student_identifier);
            if (!$student) {
                $errors[] = "Student not found: $student_identifier";
                continue;
            }

            // Enroll student
            $result = EnrollmentModel::enroll($student['id'], $course_id);
            if ($result) {
                $imported++;
            } else {
                $errors[] = "Failed to enroll student: $student_identifier";
            }
        }
        fclose($handle);
        echo "<p>Enrolled $imported students.</p>";
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

<h1>Enrolled Students for <?php echo htmlspecialchars($course['title']); ?></h1>
<p><a href="admin_courses.php">&larr; Back to Courses</a></p>

<!-- Bulk enroll form -->
<form action="admin_courses_enrolled.php?course_id=<?php echo $course_id; ?>" method="post" enctype="multipart/form-data">
    <label for="csv_file">Bulk Enroll Students (CSV: student_id or email):</label>
    <input type="file" name="csv_file" id="csv_file" accept=".csv" required>
    <button type="submit" name="import">Enroll Students</button>
</form>

<!-- Filter form -->
<form method="get" action="admin_courses_enrolled.php">
    <input type="hidden" name="course_id" value="<?php echo $course_id; ?>">
    <input type="text" name="keyword" placeholder="Search by name or email" value="<?php echo isset($_GET['keyword']) ? htmlspecialchars($_GET['keyword']) : ''; ?>">
    <button type="submit">Filter</button>
</form>

<?php
// Fetch enrolled students with optional filter
$keyword = isset($_GET['keyword']) ? trim($_GET['keyword']) : '';
$students = EnrollmentModel::getEnrolledStudents($course_id, $keyword);
?>

<table border="1" cellpadding="5">
    <tr>
        <th>#</th>
        <th>Student ID</th>
        <th>Name</th>
        <th>Email</th>
        <th>Date Enrolled</th>
    </tr>
    <?php foreach ($students as $i => $student): ?>
    <tr>
        <td><?php echo $i + 1; ?></td>
        <td><?php echo htmlspecialchars($student['student_id']); ?></td>
        <td><?php echo htmlspecialchars($student['name']); ?></td>
        <td><?php echo htmlspecialchars($student['email']); ?></td>
        <td><?php echo htmlspecialchars($student['enrolled_at']); ?></td>
    </tr>
    <?php endforeach; ?>
</table>
