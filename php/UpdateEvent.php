<?php
header('Content-Type: application/json; charset=utf-8');
date_default_timezone_set('Europe/Sofia');

$conn = new mysqli('localhost', 'root', '', 'winepainting');
$conn->set_charset("utf8mb4");

if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(['status' => 'Error', 'message' => 'Връзката с базата данни пропадна']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['status' => 'Error', 'message' => 'Invalid request method']);
    exit;
}

$id = isset($_POST['id']) ? intval($_POST['id']) : 0;
$title = isset($_POST['title']) ? trim($_POST['title']) : '';
$date = isset($_POST['date']) ? $_POST['date'] : '';
$time = isset($_POST['time']) ? $_POST['time'] : '';

// Валидация на входните данни
if ($id <= 0) {
    http_response_code(400);
    echo json_encode(['status' => 'Error', 'message' => 'Невалидно ID на събитие']);
    exit;
}

if (empty($title)) {
    http_response_code(400);
    echo json_encode(['status' => 'Error', 'message' => 'Видът на събитието е задължителен']);
    exit;
}

if (empty($date) || empty($time)) {
    http_response_code(400);
    echo json_encode(['status' => 'Error', 'message' => 'Дата и час са задължителни']);
    exit;
}

// Валидираме датата
if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
    http_response_code(400);
    echo json_encode(['status' => 'Error', 'message' => 'Невалиден формат на дата']);
    exit;
}

// Валидираме часа (HH:mm)
if (!preg_match('/^\d{2}:\d{2}$/', $time)) {
    http_response_code(400);
    echo json_encode(['status' => 'Error', 'message' => 'Невалиден формат на час']);
    exit;
}

// Преобразуваме времето в HH:mm:ss за базата
$timeWithSeconds = $time . ':00';

// Проверка за минало време
$currentDate = date('Y-m-d');
$currentTime = date('H:i:s');

if ($date < $currentDate || ($date == $currentDate && $timeWithSeconds <= $currentTime)) {
    http_response_code(400);
    echo json_encode(['status' => 'Error', 'message' => 'Не можете да запазите събитие за минал час!']);
    exit;
}

try {
    $stmt = $conn->prepare("UPDATE events SET title = ?, event_date = ?, event_time = ? WHERE id = ?");
    if (!$stmt) {
        throw new Exception('Prepare failed: ' . $conn->error);
    }

    $stmt->bind_param("sssi", $title, $date, $timeWithSeconds, $id);

    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            echo json_encode(['status' => 'Success', 'message' => 'Събитието е обновено успешно']);
        } else {
            http_response_code(404);
            echo json_encode(['status' => 'Error', 'message' => 'Събитието не е намерено или няма промени']);
        }
    } else {
        throw new Exception('Execute failed: ' . $stmt->error);
    }

    $stmt->close();
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['status' => 'Error', 'message' => 'Техническа грешка: ' . $e->getMessage()]);
}

$conn->close();
?>
