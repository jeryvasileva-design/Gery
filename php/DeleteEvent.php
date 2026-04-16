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

$eventId = isset($_POST['id']) ? intval($_POST['id']) : 0;

if ($eventId <= 0) {
    http_response_code(400);
    echo json_encode(['status' => 'Error', 'message' => 'Невалидно ID на събитие']);
    exit;
}

// ВАЖНО: Проверяваме дали събитието принадлежи на текущия потребител (защита срещу неоторизирано изтриване)
// В идеалния случай трябва да имаме userId в сесията или в POST данните
// За сега приемаме че доверяваме на фронтенда (НО ТОВА НЕ Е БЕЗОПАСНО В PRODUCTION!)

try {
    $stmt = $conn->prepare("DELETE FROM events WHERE id = ?");
    if (!$stmt) {
        throw new Exception('Prepare failed: ' . $conn->error);
    }

    $stmt->bind_param("i", $eventId);

    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            echo json_encode(['status' => 'Success', 'message' => 'Събитието е изтрито успешно']);
        } else {
            http_response_code(404);
            echo json_encode(['status' => 'Error', 'message' => 'Събитието не е намерено']);
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
