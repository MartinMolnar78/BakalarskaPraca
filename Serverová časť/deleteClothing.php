<?php
require_once 'config.php';

if (!isset($_GET['clothing_id'])) {
    http_response_code(400);
    echo json_encode(["error" => "Missing clothing_id"]);
    exit;
}

$clothing_id = intval($_GET['clothing_id']);

$sql = "UPDATE clothing SET deleted = 1 WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $clothing_id);
if ($stmt->execute()) {
    http_response_code(200);
    echo json_encode(["message" => "Item marked as deleted"]);
} else {
    http_response_code(500);
    echo json_encode(["error" => "Failed to update"]);
}
$stmt->close();
$conn->close();
