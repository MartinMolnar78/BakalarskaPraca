<?php
require 'config.php';

if (!isset($_GET['id_clothing'])) {
    echo json_encode(["success" => false, "message" => "Clothing ID is required"]);
    exit();
}

$id_clothing = $_GET['id_clothing'];

$sql = "SELECT DATE(weared_at) as date FROM outfit_has_clothing 
        WHERE id_clothing = ? AND weared_at >= DATE_SUB(CURDATE(), INTERVAL 14 DAY)";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_clothing);
$stmt->execute();
$result = $stmt->get_result();

$worn_dates = [];
while ($row = $result->fetch_assoc()) {
    $worn_dates[] = $row['date'];
}

echo json_encode(["success" => true, "dates" => $worn_dates]);

$stmt->close();
$conn->close();
?>
