<?php
require 'config.php';

header("Content-Type: application/json");

if (!isset($_GET['id_user']) || !isset($_GET['day'])) {
    echo json_encode(["success" => false, "message" => "Missing parameters"]);
    exit();
}

$userId = intval($_GET['id_user']);
$dayName = ucfirst(strtolower($_GET['day']));

$validDays = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
if (!in_array($dayName, $validDays)) {
    echo json_encode(["success" => false, "message" => "Invalid day name"]);
    exit();
}

$sql = "
    SELECT o.id_outfit, o.outfit_name, o.created_at,
           c.type, c.photo
    FROM outfit o
    LEFT JOIN outfit_has_clothing ohc ON o.id_outfit = ohc.id_outfit
    LEFT JOIN clothing c ON ohc.id_clothing = c.id
    WHERE o.id_user = ? AND DAYNAME(o.created_at) = ?
    ORDER BY o.created_at DESC
    LIMIT 100
";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    echo json_encode(["success" => false, "message" => "Prepare failed", "details" => $conn->error]);
    exit();
}

$stmt->bind_param("is", $userId, $dayName);
$stmt->execute();
$result = $stmt->get_result();

$outfits = [];
while ($row = $result->fetch_assoc()) {
    $outfitId = $row['id_outfit'];

    if (!isset($outfits[$outfitId])) {
        $outfits[$outfitId] = [
            "idOutfit" => (int)$row["id_outfit"],
            "outfitName" => $row["outfit_name"],
            "createdAt" => $row["created_at"],
            "clothingItems" => []
        ];
    }

    if (!empty($row["photo"])) {
        $outfits[$outfitId]["clothingItems"][] = [
            "type" => $row["type"],
            "photo" => $row["photo"]
        ];
    }
}

echo json_encode([
    "success" => true,
    "outfits" => array_values($outfits)
]);

$stmt->close();
$conn->close();
