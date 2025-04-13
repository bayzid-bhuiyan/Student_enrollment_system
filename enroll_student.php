<?php
header("Content-Type: application/json");
require 'db.php';

$data = json_decode(file_get_contents("php://input"));

if (!$data->student_id || !$data->course_code || !$data->course_title || !$data->semester) {
    echo json_encode(["success" => false, "message" => "All fields are required."]);
    exit;
}

// Check if course exists or insert
$course_stmt = $conn->prepare("SELECT id FROM courses WHERE course_code = ?");
$course_stmt->bind_param("s", $data->course_code);
$course_stmt->execute();
$course_result = $course_stmt->get_result();

if ($course_result->num_rows > 0) {
    $course = $course_result->fetch_assoc();
    $course_id = $course['id'];
} else {
    $insert_course = $conn->prepare("INSERT INTO courses (course_code, course_title) VALUES (?, ?)");
    $insert_course->bind_param("ss", $data->course_code, $data->course_title);
    $insert_course->execute();
    $course_id = $insert_course->insert_id;
    $insert_course->close();
}
$course_stmt->close();

// Get student internal ID
$student_stmt = $conn->prepare("SELECT id FROM students WHERE student_id = ?");
$student_stmt->bind_param("s", $data->student_id);
$student_stmt->execute();
$student_result = $student_stmt->get_result();

if ($student_result->num_rows === 0) {
    echo json_encode(["success" => false, "message" => "Student ID not found."]);
    exit;
}
$student = $student_result->fetch_assoc();
$student_id = $student['id'];
$student_stmt->close();

// Insert into enrollments
$enroll_stmt = $conn->prepare("INSERT INTO enrollments (student_id, course_id, semester) VALUES (?, ?, ?)");
$enroll_stmt->bind_param("iis", $student_id, $course_id, $data->semester);

if ($enroll_stmt->execute()) {
    echo json_encode(["success" => true, "message" => "Enrollment successful."]);
} else {
    echo json_encode(["success" => false, "message" => "Enrollment failed."]);
}
$enroll_stmt->close();
$conn->close();
?>
