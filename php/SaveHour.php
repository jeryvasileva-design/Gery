<?php

header('Content-Type: application/json');

$conn = new mysqli('localhost', 'root', '', 'winepainting');

if ($conn->connect_error) {
    echo json_encode(['status' => 'Error', 'message' => 'Connection failed']);
    exit;
}

$userId = isset($_POST['userId']) ? $_POST['userId'] :'';


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // В реална ситуация тук трябва да използвате session_start(), 
    // но базирано на вашия код, ще вземем userId от POST/localStorage
    $userId = $_POST['userId']; 
    $firstName = $_POST['firstName'];
    $lastName = $_POST['lastName'];
    $type = $_POST['eventType'];
    $date = $_POST['eventDate'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $notes = $_POST['notes'];

    $stmt = $conn->prepare("INSERT INTO events (user_id, title, event_date, first_name, last_name, phone, email, notes) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("isssssss", $userId, $type, $date, $firstName, $lastName, $phone, $email, $notes);

    if ($stmt->execute()) {
        echo json_encode(['status' => 'Success']);
    } else {
        echo json_encode(['status' => 'Error', 'message' => $stmt->error]);
    }
    $stmt->close();
}
$conn->close();
?>