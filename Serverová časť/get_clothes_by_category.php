<?php
require 'config.php';

header('Content-Type: application/json');

if (!isset($_GET['user_id']) || !isset($_GET['type'])) {
    echo json_encode([]);
    exit();
}

$user_id = $_GET['user_id'];
$type = $_GET['type'];

if (!ctype_digit($user_id) || $user_id <= 0) {
    echo json_encode([]);
    exit();
}

$sql = "SELECT id, brand, type, category, season, color, photo 
        FROM clothing 
        WHERE id_user = ? AND type = ? AND deleted = 0";

$stmt = $conn->prepare($sql);
$stmt->bind_param("is", $user_id, $type);
$stmt->execute();
$result = $stmt->get_result();

$clothingItems = [];

while ($row = $result->fetch_assoc()) {
    $clothingItems[] = [
        "id" => (int)$row["id"],
        "brand" => $row["brand"],
        "type" => $row["type"],
        "category" => $row["category"],
        "season" => $row["season"],
        "color" => $row["color"],
        "photo" => $row["photo"]
    ];
}

$stmt->close();
$conn->close();

echo json_encode($clothingItems);
?>
