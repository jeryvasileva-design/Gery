<?php

header('Content-Type: application/json');


$email = isset($_POST['email']) ? $_POST['email'] : '';
$passowrd = isset($_POST['password']) ? $_POST['password'] : '';


$conn = new mysqli('localhost', 'root', '', 'winepainting');

if ($conn->connect_error) {
    echo json_encode(array("status" => "Error", "message" => "Connection failed"));
    exit;
}


$log = $conn->prepare("SELECT id, email, password1 FROM users WHERE email = ?");

if ($log) {
    $log->bind_param('s', $email);
    $log->execute();
    $get = $log->get_result();

    if ($get->num_rows > 0) {
        $data = $get->fetch_assoc();
        
        
        if ($data['password1'] === $passowrd) {
            session_start();
            $_SESSION['id'] = $data['id'];
            
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