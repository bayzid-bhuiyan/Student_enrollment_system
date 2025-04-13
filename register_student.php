<?php
header("Content-Type: application/json");
require 'db.php';

$data = json_decode(file_get_contents("php://input"));

if (!$data->name || !$data->email) {
    echo json_encode(["success" => false, "message" => "Name and Email are required."]);
    exit;
}

$stmt = $conn->prepare("INSERT INTO students (name, email, student_id, department, major, dob, address) VALUES (?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("sssssss", $data->name, $data->email, $data->student_id, $data->department, $data->major, $data->dob, $data->address);

if ($stmt->execute()) {
    echo json_encode(["success" => true, "message" => "Student registered successfully."]);
} else {
    echo json_encode(["success" => false, "message" => "Failed to register student."]);
}
$stmt->close();
$conn->close();
?>
