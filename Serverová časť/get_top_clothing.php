<?php
require 'config.php';

if (!isset($_GET['user_id'])) {
    echo json_encode(["success" => false, "message" => "User ID is required"]);
    exit();
}

$user_id = $_GET['user_id'];

$sql = "
    SELECT c.*, COUNT(*) AS wear_count
    FROM outfit_has_clothing ohc
    JOIN outfit o ON ohc.id_outfit = o.id_outfit
    JOIN clothing c ON ohc.id_clothing = c.id
    WHERE o.id_user = ? and c.deleted=0
    GROUP BY c.id
    ORDER BY wear_count DESC
    LIMIT 4
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$top_clothes = [];
while ($row = $result->fetch_assoc()) {
    $top_clothes[] = $row;
}

header('Content-Type: application/json');
echo json_encode($top_clothes); 

$stmt->close();
$conn->close();
