<?php
header('Content-Type: application/json');

$conn = new mysqli('localhost', 'root', '', 'winepainting');

if ($conn->connect_error) {
    echo json_encode(['status' => 'Error', 'message' => 'Връзката пропадна']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Вземаме данните от AJAX заявката
    $userId    = isset($_POST['userId']) ? $_POST['userId'] : null;
    $firstName = isset($_POST['firstName']) ? $_POST['firstName'] : '';
    $lastName  = isset($_POST['lastName']) ? $_POST['lastName'] : '';
    $type      = isset($_POST['eventType']) ? $_POST['eventType'] : '';
    $date      = isset($_POST['eventDate']) ? $_POST['eventDate'] : '';
    $phone     = isset($_POST['phone']) ? $_POST['phone'] : '';
    $email     = isset($_POST['email']) ? $_POST['email'] : '';
    $notes     = isset($_POST['notes']) ? $_POST['notes'] : '';

    // Валидация
    if (empty($userId) || empty($firstName) || empty($date)) {
        echo json_encode(['status' => 'Error', 'message' => 'Моля, попълнете всички задължителни полета.']);
        exit;
    }

    // Новата заявка със съответстващи колони:
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