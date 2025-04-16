<?php
require_once 'config.php';

if (!isset($_GET['user_id'])) {
    http_response_code(400);
    echo json_encode(["error" => "Missing user_id parameter"]);
    exit;
}

$user_id = $_GET['user_id'];

$sql = "SELECT id, id_user, id_qr, brand, type, category, season, color, photo FROM clothing WHERE id_user = ? and deleted = 0 ORDER BY id DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$clothes = [];

while ($row = $result->fetch_assoc()) {
    $clothes[] = [
        "id" => $row["id"],
        "id_user" => $row["id_user"],
        "id_qr" => $row["id_qr"],
        "brand" => $row["brand"],
        "type" => $row["type"],
        "category" => $row["category"],
        "season" => $row["season"],
        "color" => $row["color"],
        "photo" => $row["photo"]
    ];
}

header('Content-Type: application/json');
echo json_encode($clothes);

$stmt->close();
$conn->close();
