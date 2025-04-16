<?php
require_once 'config.php';

if (!isset($_GET['clothing_id']) || !isset($_GET['qr_code'])) {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "Missing parameters."]);
    exit;
}

$clothing_id = intval($_GET['clothing_id']);
$qr_code = intval($_GET['qr_code']);

$sql = "UPDATE clothing SET id_qr = ? WHERE id = ?";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Database prepare failed."]);
    exit;
}

$stmt->bind_param("ii", $qr_code, $clothing_id);

if ($stmt->execute()) {
    echo json_encode(["success" => true, "message" => "QR code updated successfully."]);
} else {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Failed to update QR code."]);
}

$stmt->close();
$conn->close();
