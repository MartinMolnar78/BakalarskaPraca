<?php
include "config.php";
header("Content-Type: application/json");

if (!isset($_GET['id_user'])) {
    echo json_encode(["success" => false, "message" => "User ID is required"]);
    exit();
}

$id_user = intval($_GET['id_user']);
$year = isset($_GET['year']) ? intval($_GET['year']) : null;
$month = isset($_GET['month']) ? intval($_GET['month']) : null;

$sql = "SELECT o.id_outfit, o.outfit_name, o.created_at,
               c.type, c.photo
        FROM outfit o
        LEFT JOIN outfit_has_clothing ohc ON o.id_outfit = ohc.id_outfit
        LEFT JOIN clothing c ON ohc.id_clothing = c.id
        WHERE o.id_user = ?";

$params = [$id_user];
$types = "i";

if ($year !== null && $month !== null) {
    $sql .= " AND YEAR(o.created_at) = ? AND MONTH(o.created_at) = ?";
    $types .= "ii";
    $params[] = $year;
    $params[] = $month;
}

$sql .= " ORDER BY o.created_at DESC";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    echo json_encode(["success" => false, "message" => "Failed to prepare statement"]);
    exit();
}

$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();

$outfits = [];
while ($row = $result->fetch_assoc()) {
    $outfitId = $row['id_outfit'];

    if (!isset($outfits[$outfitId])) {
        $outfits[$outfitId] = [
            "idOutfit" => (int) $row["id_outfit"],
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

echo json_encode(["success" => true, "outfits" => array_values($outfits)]);
$conn->close();
?>
