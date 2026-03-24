<?php
header('Content-Type: application/json');
$conn = new mysqli('localhost', 'root', '', 'winepainting');

$userId = $_GET['userId'];

$result = $conn->query("SELECT title, event_date as start FROM events WHERE user_id = $userId");
$events = [];

while($row = $result->fetch_assoc()) {
    $events[] = $row;
}

echo json_encode($events);
$conn->close();
?>