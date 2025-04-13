<?php
header("Content-Type: application/json");
require 'db.php';

$result = $conn->query("SELECT name, student_id, department, major, email FROM students");

$students = [];

while ($row = $result->fetch_assoc()) {
    $students[] = $row;
}

if (count($students) > 0) {
    echo json_encode(["success" => true, "students" => $students]);
} else {
    echo json_encode(["success" => false, "message" => "No data in the table"]);
}

$conn->close();
?>
