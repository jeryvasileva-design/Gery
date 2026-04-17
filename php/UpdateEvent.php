<?php
header('Content-Type: application/json; charset=utf-8');
date_default_timezone_set('Europe/Sofia');

$conn = new mysqli('localhost', 'root', '', 'winepainting');
$conn->set_charset("utf8mb4");

if ($conn->connect_error) {
    echo json_encode(['status' => 'Error', 'message' => 'Грешка при връзка с базата: ' . $conn->connect_error]);
    exit;
}

$id    = isset($_POST['id']) ? intval($_POST['id']) : 0;
$title = isset($_POST['title']) ? trim($_POST['title']) : '';
$date  = isset($_POST['date']) ? $_POST['date'] : '';
$time  = isset($_POST['time']) ? $_POST['time'] : '';

if ($id <= 0 || empty($title) || empty($date) || empty($time)) {
    echo json_encode(['status' => 'Error', 'message' => 'Всички полета са задължителни.']);
    exit;
}

if (!preg_match('/^\d{2}:\d{2}(:\d{2})?$/', $time)) {
    echo json_encode(['status' => 'Error', 'message' => 'Невалиден формат на часа.']);
    exit;
}

$timeWithSeconds = (strlen($time) == 5) ? $time . ':00' : $time;

$currentDate = date('Y-m-d');
$currentTime = date('H:i:s');

if ($date < $currentDate || ($date == $currentDate && $timeWithSeconds <= $currentTime)) {
    echo json_encode(['status' => 'Error', 'message' => 'Изберете дата, който не е в миналото.']);
    exit;
}

try {
    $stmt = $conn->prepare("UPDATE events SET title = ?, event_date = ?, event_time = ? WHERE id = ?");
    $stmt->bind_param("sssi", $title, $date, $timeWithSeconds, $id);

    if ($stmt->execute()) {
        echo json_encode(['status' => 'Success', 'message' => 'Промените са запазени успешно.']);
    } else {
        echo json_encode(['status' => 'Error', 'message' => 'Грешка при запис: ' . $stmt->error]);
    }
    $stmt->close();
} catch (Exception $e) {
    echo json_encode(['status' => 'Error', 'message' => 'Техническа грешка: ' . $e->getMessage()]);
}

$conn->close();
?>