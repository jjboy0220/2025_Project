<?php
header("Content-Type: application/json; charset=UTF-8");

$uploadDir = 'uploads/';
if (!file_exists($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

if (isset($_POST['name']) && isset($_POST['price']) && isset($_POST['stock']) && 
    isset($_POST['category']) && isset($_POST['description']) && isset($_FILES['image'])) {
    // 檢查欄位是否為空
    if ($_POST['name'] != "" && $_POST['price'] != "" && $_POST['stock'] != "" && 
        $_POST['category'] != "" && $_POST['description'] != "" && $_FILES['image']['error'] === UPLOAD_ERR_OK) {

        $name = $_POST['name'];
        $price = $_POST['price'];
        $stock = $_POST['stock'];
        $category = $_POST['category'];
        $description = $_POST['description'];

        // 處理圖片上傳
        $fileTmpPath = $_FILES['image']['tmp_name'];
        $fileName = $_FILES['image']['name'];
        $fileSize = $_FILES['image']['size'];
        $fileType = $_FILES['image']['type'];
        $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        $allowedExts = ['jpg', 'jpeg', 'png', 'gif'];

        if (in_array($fileExt, $allowedExts)) {
            if ($fileSize < 5 * 1024 * 1024) { // 限制 5MB
                $newFileName = md5(time() . $fileName) . '.' . $fileExt;
                $destPath = $uploadDir . $newFileName;
                if (move_uploaded_file($fileTmpPath, $destPath)) {
                    $image_url = $destPath;
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

        // 資料庫連線
        $servername = "localhost";
        $username = "owner01";
        $password = "123456";
        $dbname = "testdb";

        $conn = mysqli_connect($servername, $username, $password, $dbname);
        if (!$conn) {
            die(json_encode(["state" => false, "message" => "連線錯誤: " . mysqli_connect_error()]));
        }

        // 插入資料
        $sql = "INSERT INTO products (name, price, stock, category, description, image_url) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sdisss", $name, $price, $stock, $category, $description, $image_url);

        if ($stmt->execute()) {
            echo json_encode(["state" => true, "message" => "新增成功"]);
        } else {
            echo json_encode(["state" => false, "message" => "新增失敗: " . mysqli_error($conn)]);
        }

        $stmt->close();
        mysqli_close($conn);
    } else {
        echo json_encode(["state" => false, "message" => "欄位不得為空白"]);
    }
} else {
    echo json_encode(["state" => false, "message" => "欄位錯誤或缺少必要欄位"]);
}
?>