<?php
 if($_SERVER['REQUEST_METHOD'] == "POST"){
    $userName = $_POST['userName'];
    $email = $_POST['email'];
    $passowrd = $_POST['password']; 
 }

 $conn = new mysqli('localhost','root','','winepainting'); 

 if ($conn->connect_error) {
    die('Connection failed' .$conn->connect_error);
 }
 else{

 $state = $conn->prepare('INSERT INTO users (userName, email, password1)VALUES(?,?,?)');
    if ($state){
        $state->bind_param('sss', $userName, $email, $passowrd);
        if ($state->execute()) {
            echo 'Success';
        }
        else{
            echo 'Error'. $state->error;
        }
        $state->close();
    }
    else {
        echo 'Error'. $conn->error;
    }
    $conn ->close();
 }
?>