<?php
header('Content-Type: application/json');
$conn = new mysqli('localhost', 'root', '', 'winepainting');

if ($conn->connect_error) {
    die(json_encode([]));
}

// Вземаме userId от URL параметъра (напр. GetEvent.php?userId=5)
$userId = isset($_GET['userId']) ? intval($_GET['userId']) : 0;

if ($userId > 0) {
    // Използваме prepare за сигурност
    $stmt = $conn->prepare("SELECT title, event_date AS start FROM events WHERE user_id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $events = [];
    while($row = $result->fetch_assoc()) {
        $events[] = $row;
    }
    
    echo json_encode($events);
    $stmt->close();
} else {
    echo json_encode([]);
}

$conn->close();
?>