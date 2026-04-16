<?php
header('Content-Type: application/json; charset=utf-8');
date_default_timezone_set('Europe/Sofia');

$conn = new mysqli('localhost', 'root', '', 'winepainting');
$conn->set_charset("utf8mb4");

if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(['error' => 'Връзката с базата данни пропадна: ' . $conn->connect_error]);
    exit;
}

$userId = isset($_GET['userId']) ? intval($_GET['userId']) : 0;

if ($userId <= 0) {
    http_response_code(400);
    echo json_encode(['error' => 'Невалидно потребителско ID']);
    exit;
}

try {
    // ВАЖНО: Вече взимаме и колоната 'id' която е необходима за CRUD операциите
    // Също взимаме всички полета, за да можем да покажем информация в модала при редактиране
    $sql = "SELECT 
                id,
                title,
                CONCAT(event_date, 'T', TIME_FORMAT(event_time, '%H:%i:%s')) as start,
                event_date as date,
                event_time as time,
                first_name,
                last_name,
                phone,
                notes
            FROM events 
            WHERE user_id = ? 
            ORDER BY event_date ASC, event_time ASC";

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        throw new Exception('Prepare failed: ' . $conn->error);
    }

    $stmt->bind_param('i', $userId);

    if (!$stmt->execute()) {
        throw new Exception('Execute failed: ' . $stmt->error);
    }

    $result = $stmt->get_result();

    $events = [];
    while ($row = $result->fetch_assoc()) {
        $events[] = $row;
    }

    // За FullCalendar трябват само id, title, start полета
    // Но е хубаво да имаме и другите данни достъпни за по-детайлиран модал
    echo json_encode($events, JSON_UNESCAPED_UNICODE);

    $stmt->close();
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Техническа грешка: ' . $e->getMessage()]);
}

$conn->close();
?>
