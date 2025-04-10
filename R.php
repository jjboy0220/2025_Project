<?php
require_once "dbtools.php";

header("Content-Type: application/json; charset=UTF-8"); // 修正為 UTF-8

$link = create_connection();

// 設置 MySQL 連線編碼
mysqli_set_charset($link, "utf8mb4") or die("無法設置字符集: " . mysqli_error($link));

// 獲取所有商品資料
$sql = "SELECT id, name, description, price, stock, image_url FROM products";
$result = execute_sql($link, "if0_38646806_jjboy0220", $sql);

$products = [];
while ($row = mysqli_fetch_assoc($result)) {
    $products[] = $row;
}

if ($products) {
    echo json_encode(["state" => true, "data" => $products], JSON_UNESCAPED_UNICODE); // 確保不轉義 Unicode
} else {
    echo json_encode(["state" => false, "message" => "無商品資料"], JSON_UNESCAPED_UNICODE);
}

mysqli_close($link);
?>