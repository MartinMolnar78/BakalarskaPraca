<?php
require 'config.php'; 

if (!isset($_GET['number']) || !isset($_GET['user_id'])) {
    echo json_encode(["success" => false, "message" => "Number and user ID are required"]);
    exit();
}

$qr_number = $_GET['number'];
$user_id = $_GET['user_id'];

if (!ctype_digit($qr_number) || $qr_number <= 0) {
    echo json_encode(["success" => false, "message" => "Invalid QR number"]);
    exit();
}

if (!ctype_digit($user_id) || $user_id <= 0) {
    echo json_encode(["success" => false, "message" => "Invalid user ID"]);
    exit();
}

$qr_number = (int)$qr_number;
$user_id = (int)$user_id;

$sql = "SELECT id, brand, type, category, season, color, photo 
        FROM clothing 
        WHERE id_qr = ? AND id_user = ? and deleted = 0";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    echo json_encode(["success" => false, "message" => "Database error"]);
    exit();
}

$stmt->bind_param("ii", $qr_number, $user_id);
if (!$stmt->execute()) {
    echo json_encode(["success" => false, "message" => "Database error"]);
    exit();
}

$result = $stmt->get_result();
if (!$result) {
    echo json_encode(["success" => false, "message" => "Database error"]);
    exit();
}

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();

    $response = [
        "success" => true,
        "id_clothing" => $row["id"],
        "brand" => $row["brand"],
        "type" => $row["type"],
        "category" => $row["category"],
        "season" => $row["season"],
        "color" => $row["color"],
        "photo" => $row["photo"] 
    ];
    echo json_encode($response);
} else {
    echo json_encode(["success" => false, "message" => "Clothing not found for this user"]);
}

$stmt->close();
$conn->close();
?>