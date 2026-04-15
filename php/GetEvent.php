<?php
header('Content-Type: application/json; charset=utf-8');
$conn = new mysqli('localhost', 'root', '', 'winepainting');

// КРИТИЧНО: Задаваме UTF-8 за връзката, за да работи кирилицата
$conn->set_charset("utf8mb4");

if ($conn->connect_error) {
    die(json_encode(['error' => 'Connection failed']));
}

$userId = isset($_GET['userId']) ? intval($_GET['userId']) : 0;

if ($userId > 0) {
    // Извличаме заглавието и комбинираме дата и час в ISO формат (YYYY-MM-DDTHH:mm:ss)
    $sql = "SELECT title, CONCAT(event_date, 'T', event_time) as start FROM events WHERE user_id = $userId";
    $result = $conn->query($sql);
    
    $events = [];
    if ($result) {
        while($row = $result->fetch_assoc()) {
            $events[] = $row;
        }
    }
    echo json_encode($events);
} else {
    echo json_encode([]);
}
$conn->close();
?>