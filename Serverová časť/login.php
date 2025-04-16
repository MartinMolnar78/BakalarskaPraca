<?php
include 'config.php'; 
header('Content-Type: application/json');

$data = json_decode(file_get_contents("php://input"));

if ($data === null) {
    echo json_encode([
        "success" => false,
        "message" => "No JSON data received or invalid JSON format"
    ]);
    exit;
}

if (empty($data->email) || empty($data->password)) {
    echo json_encode([
        "success" => false,
        "message" => "Email and password are required"
    ]);
    exit;
}

$email = $data->email;
$password = $data->password;

if ($conn->connect_error) {
    echo json_encode([
        "success" => false,
        "message" => "Database connection failed: " . $conn->connect_error
    ]);
    exit;
}

$query = $conn->prepare("SELECT id, password FROM users WHERE email = ?");
$query->bind_param("s", $email);
$query->execute();
$result = $query->get_result();
$user = $result->fetch_assoc();

if ($user && password_verify($password, $user['password'])) {
    echo json_encode([
        "success" => true,
        "message" => "Login successful",
        "userId" => $user['id']
    ]);
} else {
    echo json_encode([
        "success" => false,
        "message" => "Invalid email or password"
    ]);
}
?>
