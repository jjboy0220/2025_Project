<?php
header("Content-Type: application/json; charset=UTF-8");

$servername = "sql102.infinityfree.com";
$username = "if0_38646806";
$password = "Apple897897";
$dbname = "if0_38646806_jjboy0220";

$conn = mysqli_connect($servername, $username, $password, $dbname);
if (!$conn) {
    die(json_encode(["state" => false, "message" => "連線錯誤: " . mysqli_connect_error()]));
}

mysqli_query($conn, "SET NAMES UTF8");
$sql = "SELECT id, name, price, stock, category, description, image_url, created_at FROM products ORDER BY id DESC";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) > 0) {
    $mydata = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $mydata[] = $row;
    }
    echo json_encode(["state" => true, "message" => "讀取成功", "data" => $mydata]);
} else {
    echo json_encode(["state" => false, "message" => "讀取失敗"]);
}

mysqli_close($conn);
?>