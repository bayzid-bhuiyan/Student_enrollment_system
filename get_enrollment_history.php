<?php
header("Content-Type: application/json");
require 'db.php';

$data = json_decode(file_get_contents("php://input"));
$student_id_input = $data->student_id ?? '';

if (!$student_id_input) {
    echo json_encode(["success" => false, "message" => "Student ID is required."]);
    exit;
}

// Get internal student ID
$student_stmt = $conn->prepare("SELECT id FROM students WHERE student_id = ?");
$student_stmt->bind_param("s", $student_id_input);
$student_stmt->execute();
$student_result = $student_stmt->get_result();

if ($student_result->num_rows === 0) {
    echo json_encode(["success" => false, "message" => "Student not found."]);
    exit;
}
$student = $student_result->fetch_assoc();
$student_id = $student['id'];
$student_stmt->close();

// Fetch enrollment history
$query = "
    SELECT c.course_code, c.course_title, e.semester, e.grade
    FROM enrollments e
    JOIN courses c ON e.course_id = c.id
    WHERE e.student_id = ?
";
$history_stmt = $conn->prepare($query);
$history_stmt->bind_param("i", $student_id);
$history_stmt->execute();
$result = $history_stmt->get_result();

$history = [];
while ($row = $result->fetch_assoc()) {
    $history[] = $row;
}

if (count($history) > 0) {
    echo json_encode(["success" => true, "history" => $history]);
} else {
    echo json_encode(["success" => false, "message" => "No data available."]);
}

$history_stmt->close();
$conn->close();
?>
