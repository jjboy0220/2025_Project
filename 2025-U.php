<?php
header("Content-Type: application/json; charset=UTF-8");

$uploadDir = 'uploads/';
if (!file_exists($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

if (isset($_POST['id']) && isset($_POST['price']) && isset($_POST['stock']) && 
    isset($_POST['category']) && isset($_POST['description'])) {

    $id = $_POST['id'];
    $price = $_POST['price'];
    $stock = $_POST['stock'];
    $category = $_POST['category'];
    $description = $_POST['description'];

    $image_url = null;
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['image']['tmp_name'];
        $fileName = $_FILES['image']['name'];
        $fileSize = $_FILES['image']['size'];
        $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        $allowedExts = ['jpg', 'jpeg', 'png', 'gif'];

        if (in_array($fileExt, $allowedExts)) {
            if ($fileSize < 5 * 1024 * 1024) {
                $newFileName = md5(time() . $fileName) . '.' . $fileExt;
                $destPath = $uploadDir . $newFileName;
                if (move_uploaded_file($fileTmpPath, $destPath)) {
                    $image_url = $destPath;
                    $servername = "localhost";
                    $username = "owner01";
                    $password = "123456";
                    $dbname = "testdb";

                    $conn = mysqli_connect($servername, $username, $password, $dbname);
                    if (!$conn) {
                        die(json_encode(["state" => false, "message" => "連線錯誤: " . mysqli_connect_error()]));
                    }

                    $sql = "SELECT image_url FROM products WHERE id = ?";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("i", $id);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    $oldImage = $result->fetch_assoc()['image_url'];
                    if ($oldImage && file_exists($oldImage)) {
                        unlink($oldImage);
                    }
                    mysqli_close($conn);
                } else {
                    echo json_encode(["state" => false, "message" => "圖片上傳失敗"]);
                    exit;
                }
            } else {
                echo json_encode(["state" => false, "message" => "圖片檔案過大，限制 5MB"]);
                exit;
            }
        } else {
            echo json_encode(["state" => false, "message" => "僅允許上傳 jpg、jpeg、png、gif 格式的圖片"]);
            exit;
        }
    }

    $servername = "localhost";
    $username = "owner01";
    $password = "123456";
    $dbname = "testdb";

    $conn = mysqli_connect($servername, $username, $password, $dbname);
    if (!$conn) {
        die(json_encode(["state" => false, "message" => "連線錯誤: " . mysqli_connect_error()]));
    }

    if ($image_url) {
        $sql = "UPDATE products SET price = ?, stock = ?, category = ?, description = ?, image_url = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("disssi", $price, $stock, $category, $description, $image_url, $id);
    } else {
        $sql = "UPDATE products SET price = ?, stock = ?, category = ?, description = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("disssi", $price, $stock, $category, $description, $id);
    }

    if ($stmt->execute()) {
        echo json_encode(["state" => true, "message" => "更新成功"]);
    } else {
        echo json_encode(["state" => false, "message" => "更新失敗: " . mysqli_error($conn)]);
    }

    $stmt->close();
    mysqli_close($conn);
} else {
    echo json_encode(["state" => false, "message" => "欄位錯誤或空白"]);
}
?>