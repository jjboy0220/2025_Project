<?php
header("Content-Type: application/json; charset=UTF-8");

$servername = "localhost";
$username = "owner01";
$password = "123456";
$dbname = "testdb";

$conn = mysqli_connect($servername, $username, $password, $dbname);
if (!$conn) {
    die(json_encode(["state" => false, "message" => "連線錯誤: " . mysqli_connect_error()]));
}

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