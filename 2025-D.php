<?php
header("Content-Type: application/json; charset=UTF-8");

$data = file_get_contents("php://input");
$mydata = json_decode($data, true);

if (isset($mydata["id"])) {
    $id = $mydata["id"];

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
    $product = $result->fetch_assoc();
    if ($product && file_exists($product['image_url'])) {
        unlink($product['image_url']);
    }

    $sql = "DELETE FROM products WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo json_encode(["state" => true, "message" => "刪除成功"]);
    } else {
        echo json_encode(["state" => false, "message" => "刪除失敗: " . mysqli_error($conn)]);
    }

    $stmt->close();
    mysqli_close($conn);
} else {
    echo json_encode(["state" => false, "message" => "欄位錯誤或空白"]);
}
?>
