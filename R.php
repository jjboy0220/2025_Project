<?php
require_once "dbtools.php";

header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");

$link = create_connection();

// 獲取所有商品資料
$sql = "SELECT id, name, description, price, stock, image_url FROM products";
$result = execute_sql($link, "testdb", $sql);

$products = [];
while ($row = mysqli_fetch_assoc($result)) {
    $products[] = $row;
}

if ($products) {
    echo json_encode(["state" => true, "data" => $products]);
} else {
    echo json_encode(["state" => false, "message" => "無商品資料"]);
}

mysqli_close($link);
?>