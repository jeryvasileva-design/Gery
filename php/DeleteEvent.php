<?php
header('Content-Type: application/json; charset=utf-8');
date_default_timezone_set('Europe/Sofia');

$conn = new mysqli('localhost', 'root', '', 'winepainting');
$conn->set_charset("utf8mb4");

if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(['status' => 'Error', 'message' => 'Грешка с връзка с базата.']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['status' => 'Error', 'message' => 'Невалидна заявка.']);
    exit;
}

$eventId = isset($_POST['id']) ? intval($_POST['id']) : 0;

// Валидация на ID
if ($eventId <= 0) {
    http_response_code(400);
    echo json_encode(['status' => 'Error', 'message' => 'Невалидно ID на събитие.']);
    exit;
}

try {
    $stmt = $conn->prepare("DELETE FROM events WHERE id = ?");
    $stmt->bind_param("i", $eventId);

    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            echo json_encode(['status' => 'Success', 'message' => 'Резервацията е изтрита успешно.']);
        } else {
            http_response_code(404);
            echo json_encode(['status' => 'Error', 'message' => 'Събитието не е намерено.']);
        }
    } else {
        throw new Exception($stmt->error);
    }
    $stmt->close();
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['status' => 'Error', 'message' => 'Грешка при изтриване: ' . $e->getMessage()]);
}

$conn->close();
?>