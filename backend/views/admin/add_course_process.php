<?php
require_once '../../app/Core/init.php';
require_once '../../models/CourseMode.php';
$user = Guard::requireAdmin();
$courseModel = new CourseMode(DB::getInstance());


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_course'])) {
    $fields = [
        'title' => $_POST['title'],
        'code' => $_POST['code'],
        'description' => $_POST['description'],
        'department' => $_POST['department'],
        'coordinator_user_id' => $_POST['coordinator_user_id'],
        'materials' => $_POST['materials'],
        'status' => $_POST['status'],
        'created_by_user_id' => $user->data()->id
    ];
    
    $result = $courseModel->createCourse($fields);
    
    if ($result) {
        // Redirect only on success
        header('Location: admin_courses.php?message=Course added successfully!');
        exit;
    } else {
        // Use the public getter method to get the error
        echo "Failed to add course: " . $courseModel->getDatabaseError(); 
    }
}
    
    // Redirect back to the main page to show the updated table
    // header('Location: admin_courses.php?message=Course added successfully!');
    // exit;

