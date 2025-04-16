<?php
include 'config.php'; 
header('Content-Type: application/json');

$data = json_decode(file_get_contents("php://input"));

if ($data === null) {
    echo json_encode([
        "success" => false,
        "message" => "Invalid or missing JSON input"
    ]);
    exit;
}

$first_name = trim($data->first_name ?? '');
$last_name  = trim($data->last_name ?? '');
$email      = trim($data->email ?? '');
$password   = $data->password ?? '';

$nameRegex = "/^[a-zA-Zá-žÁ-Ž\s\-']{2,50}$/u"; 
$emailRegex = "/^[^\s@]+@[^\s@]+\.[^\s@]+$/";
$passwordRegex = "/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{6,}$/"; 


if (
    empty($first_name) || 
    empty($last_name) || 
    empty($email) || 
    empty($password)
) {
    echo json_encode([
        "success" => false,
        "message" => "All fields are required"
    ]);
    exit;
}

if (!preg_match($nameRegex, $first_name) ) {
    echo json_encode([
        "success" => false,
        "message" => "Name can contain only letters"
    ]);
    exit;
}

if (!preg_match($nameRegex, $last_name)) {
    echo json_encode([
        "success" => false,
        "message" => "Last name can contain only letters"
    ]);
    exit;
}



if (!preg_match($emailRegex, $email)) {
    echo json_encode([
        "success" => false,
        "message" => "Invalid email format"
    ]);
    exit;
}

if (!preg_match($passwordRegex, $password)) {
    echo json_encode([
        "success" => false,
        "message" => "Password must be at least 6 characters long and include uppercase, lowercase, digit, and special character"
    ]);
    exit;
}

$query = $conn->prepare("SELECT id FROM users WHERE email = ?");
$query->bind_param("s", $email);
$query->execute();
$result = $query->get_result();

if ($result->num_rows > 0) {
    echo json_encode([
        "success" => false,
        "message" => "Email already exists"
    ]);
    exit;
}

$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

$query = $conn->prepare("INSERT INTO users (first_name, last_name, email, password) VALUES (?, ?, ?, ?)");
$query->bind_param("ssss", $first_name, $last_name, $email, $hashedPassword);

if ($query->execute()) {
    echo json_encode([
        "success" => true,
        "message" => "Registration successful"
    ]);
} else {
    echo json_encode([
        "success" => false,
        "message" => "Failed to register user",
        "error" => $conn->error
    ]);
}
?>
