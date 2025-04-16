<?php
include 'config.php'; 

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_user = $_POST['id_user'] ?? null;
    $id_qr = $_POST['id_qr'] ?? null;
    $type = $_POST['type'] ?? null;
    $brand = $_POST['brand'] ?? null;
    $category = $_POST['category'] ?? null;
    $season = $_POST['season'] ?? null;
    $color = $_POST['color'] ?? null;

    if (!$id_user || !$type || !$brand || !$category || !$season || !$color) {
        echo json_encode(['success' => false, 'message' => 'Missing required fields.']);
        exit;
    }

    if (empty($id_qr)) {
        $id_qr = 0;
    } else {
        $checkSql = "SELECT COUNT(*) as count FROM clothing WHERE id_qr = ? AND id_user = ?";
        $checkStmt = $conn->prepare($checkSql);
        $checkStmt->bind_param("ii", $id_qr, $id_user);
        $checkStmt->execute();
        $result = $checkStmt->get_result();
        $row = $result->fetch_assoc();
        $checkStmt->close();

        if ($row['count'] > 0) {
            echo json_encode(['success' => false, 'message' => 'QR code is already used by this user.']);
            exit;
        }
    }

    $fileName = null; 
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = 'uploads/'; 
        $fileName = uniqid() . '_' . basename($_FILES['photo']['name']);
        $uploadFilePath = $uploadDir . $fileName;

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $imageType = exif_imagetype($_FILES['photo']['tmp_name']);
        if ($imageType === IMAGETYPE_JPEG) {
            $sourceImage = imagecreatefromjpeg($_FILES['photo']['tmp_name']);
        } elseif ($imageType === IMAGETYPE_PNG) {
            $sourceImage = imagecreatefrompng($_FILES['photo']['tmp_name']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Unsupported image format.']);
            exit;
        }

        $origWidth = imagesx($sourceImage);
        $origHeight = imagesy($sourceImage);
        $newWidth = $origWidth / 2;
        $newHeight = $origHeight / 2;

        $resizedImage = imagecreatetruecolor($newWidth, $newHeight);

        if ($imageType === IMAGETYPE_PNG) {
            imagealphablending($resizedImage, false);
            imagesavealpha($resizedImage, true);
        }

        imagecopyresampled(
            $resizedImage,
            $sourceImage,
            0, 0, 0, 0,
            $newWidth, $newHeight,
            $origWidth, $origHeight
        );

        if ($imageType === IMAGETYPE_JPEG) {
            imagejpeg($resizedImage, $uploadFilePath, 50);
        } elseif ($imageType === IMAGETYPE_PNG) {
            imagepng($resizedImage, $uploadFilePath, 6);
        }

        imagedestroy($sourceImage);
        imagedestroy($resizedImage);
    }

    $sql = "INSERT INTO clothing (id_user, id_qr, type, brand, category, season, color, photo) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param(
        "iissssss", 
        $id_user, 
        $id_qr, 
        $type, 
        $brand, 
        $category, 
        $season, 
        $color, 
        $fileName
    );

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Clothing added successfully.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to insert data into database.']);
    }

    $stmt->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}
?>
