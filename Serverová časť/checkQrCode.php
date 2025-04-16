<?php
include 'config.php';

$user_id = $_GET['user_id'];
$qr_code = $_GET['qr_code'];

$query = "SELECT COUNT(*) as count FROM clothing WHERE id_user = ? AND id_qr = ? and deleted = 0";
$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $user_id, $qr_code);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

echo json_encode($row['count'] > 0);
?>
