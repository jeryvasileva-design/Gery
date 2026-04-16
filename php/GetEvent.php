<?php
header('Content-Type: application/json; charset=utf-8');

$conn = new mysqli('localhost', 'root', '', 'winepainting');

// КРИТИЧНО: Задаваме UTF-8 за връзката ПРЕДИ каквито и да е queries
$conn->set_charset("utf8mb4");

if ($conn->connect_error) {
    http_response_code(500);
    die(json_encode(['error' => 'Connection failed: ' . $conn->connect_error]));
}

$userId = isset($_GET['userId']) ? intval($_GET['userId']) : 0;

if ($userId > 0) {
    // Извличаме заглавието и комбинираме дата и час в ISO 8601 формат
    // FORMAT: YYYY-MM-DDTHH:mm:ss (което е стандартът за FullCalendar)
    $sql = "SELECT 
                CONCAT(event_date, 'T', TIME_FORMAT(event_time, '%H:%i:%s')) as start,
                title
            FROM events 
            WHERE user_id = ? 
            ORDER BY event_date ASC, event_time ASC";
    
    $stmt = $conn->prepare($sql);
    
    if (!$stmt) {
        http_response_code(500);
        die(json_encode(['error' => 'Prepare failed: ' . $conn->error]));
    }
    
    $stmt->bind_param('i', $userId);
    
    if (!$stmt->execute()) {
        http_response_code(500);
        die(json_encode(['error' => 'Execute failed: ' . $stmt->error]));
    }
    
    $result = $stmt->get_result();
    
    $events = [];
    if ($result) {
        while($row = $result->fetch_assoc()) {
            $events[] = $row;
        }
    }
    
    echo json_encode($events, JSON_UNESCAPED_UNICODE);
    $stmt->close();
} else {
    echo json_encode([]);
}

$conn->close();
?>
