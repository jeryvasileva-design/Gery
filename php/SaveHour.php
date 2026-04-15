<?php
header('Content-Type: application/json; charset=utf-8');
date_default_timezone_set('Europe/Sofia'); // Задължително за точно сравнение на часа

$conn = new mysqli('localhost', 'root', '', 'winepainting');

if ($conn->connect_error) {
    echo json_encode(['status' => 'Error', 'message' => 'Връзката с базата данни пропадна.']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $userId = isset($_POST['userId']) ? intval($_POST['userId']) : 0;
    
    // Проверка за валиден потребител
    if ($userId <= 0) {
        echo json_encode(['status' => 'Error', 'message' => 'Невалидно потребителско ID. Моля, влезте отново.']);
        exit;
    }

    $firstName = $_POST['firstName'] ?? '';
    $lastName  = $_POST['lastName'] ?? '';
    $type      = $_POST['eventType'] ?? '';
    $date      = $_POST['eventDate'] ?? '';
    $time      = $_POST['eventTime'] ?? ''; 
    $phone     = $_POST['phone'] ?? '';
    $notes     = $_POST['notes'] ?? '';

    // Валидация за минало време
    $currentDate = date('Y-m-d');
    $currentTime = date('H:i:s');

    if ($date < $currentDate || ($date == $currentDate && $time <= $currentTime)) {
        echo json_encode(['status' => 'Error', 'message' => 'Избраният час вече е минал.']);
        exit;
    }

    // Подготвяме заявката с ВСИЧКИ полета от формата
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