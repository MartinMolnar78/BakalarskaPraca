<?php
require 'config.php';

$data = json_decode(file_get_contents("php://input"));

if(isset($data->id_user) && isset($data->outfit_name) && isset($data->clothing_items)) {
    $id_user = $data->id_user;
    $outfit_name = $data->outfit_name;
    $clothing_items = $data->clothing_items;

    $stmt = $conn->prepare("INSERT INTO outfit (id_user, outfit_name, created_at, updated_at) VALUES (?, ?, NOW(), NOW())");
    $stmt->bind_param("is", $id_user, $outfit_name);
    
    if ($stmt->execute()) {
        $id_outfit = $stmt->insert_id;
        $stmt->close();

        foreach ($clothing_items as $id_clothing) {
            $stmt2 = $conn->prepare("INSERT INTO outfit_has_clothing (id_outfit, id_clothing, weared_at) VALUES (?, ?, NOW())");
            $stmt2->bind_param("ii", $id_outfit, $id_clothing);
            $stmt2->execute();
            $stmt2->close();
        }

        echo json_encode(["success" => true, "message" => "Outfit saved successfully", "id_outfit" => $id_outfit]);
    } else {
        echo json_encode(["success" => false, "message" => "Error saving outfit"]);
    }
}
?>
