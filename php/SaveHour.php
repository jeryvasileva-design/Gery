<?php
header('Content-Type: application/json; charset=utf-8');
date_default_timezone_set('Europe/Sofia'); 

$conn = new mysqli('localhost', 'root', '', 'winepainting');

if ($conn->connect_error) {
    echo json_encode(['status' => 'Error', 'message' => 'Грешка при връзка с базата: ' . $conn->connect_error]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $userId = isset($_POST['userId']) ? intval($_POST['userId']) : 0;
    
    if ($userId <= 0) {
        echo json_encode(['status' => 'Error', 'message' => 'Невалидно потребителско ID']);
        exit;
    }

    $firstName = $_POST['firstName'] ?? '';
    $lastName  = $_POST['lastName'] ?? '';
    $type      = $_POST['eventType'] ?? '';
    $date      = $_POST['eventDate'] ?? '';
    $time      = $_POST['eventTime'] ?? ''; 
    $phone     = $_POST['phone'] ?? '';
    $notes     = $_POST['notes'] ?? '';

//validaciq za minalo vreme
    $currentDate = date('Y-m-d');
    $currentTime = date('H:i:s');

    if ($date < $currentDate || ($date == $currentDate && $time <= $currentTime)) {
        echo json_encode(['status' => 'Error', 'message' => 'Изберете час, който не е в миналото.']);
        exit;
    }

    $stmt = $conn->prepare("INSERT INTO events (user_id, title, event_date, event_time, first_name, last_name, phone, notes) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("isssssss", $userId, $type, $date, $time, $firstName, $lastName, $phone, $notes);

    if ($stmt->execute()) {
        echo json_encode(['status' => 'Success']);
    } else {
        echo json_encode(['status' => 'Error', 'message' => 'Грешка в базата: ' . $stmt->error]);
    }
    $stmt->close();
}
$conn->close();
?>