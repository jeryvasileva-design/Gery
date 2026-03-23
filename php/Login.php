<?php
// Казваме на браузъра, че отговорът ще бъде само JSON
header('Content-Type: application/json');

// Взимаме данните от POST заявката
$email = isset($_POST['email']) ? $_POST['email'] : '';
$passowrd = isset($_POST['password']) ? $_POST['password'] : '';

// Свързваме се с базата данни
$conn = new mysqli('localhost', 'root', '', 'winepainting');

if ($conn->connect_error) {
    echo json_encode(array("status" => "Error", "message" => "Connection failed"));
    exit;
}

// 1. Поправена заявка: Търсим по имейл (колоните са id, email, password1)
$log = $conn->prepare("SELECT id, email, password1 FROM users WHERE email = ?");

if ($log) {
    $log->bind_param('s', $email);
    $log->execute();
    $get = $log->get_result();

    if ($get->num_rows > 0) {
        $data = $get->fetch_assoc();
        
        // 2. Сравняваме паролата с колоната password1
        if ($data['password1'] === $passowrd) {
            // 3. Поправени сесии: start и SESSION
            session_start();
            $_SESSION['id'] = $data['id'];
            
            // Връщаме Success статус в JSON формат
            echo json_encode(array("status" => "Success", "id" => $data['id']));
        } else {
            echo json_encode(array("status" => "Error", "message" => "Wrong password"));
        }
    } else {
        echo json_encode(array("status" => "Error", "message" => "User not found"));
    }
    $log->close();
} else {
    echo json_encode(array("status" => "Error", "message" => "SQL Error"));
}

$conn->close();
?>