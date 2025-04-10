<?php
header("Content-Type: application/json");

$conn = new mysqli("localhost", "owner01", "123456", "testdb");
if ($conn->connect_error) {
    die(json_encode(["state" => false, "message" => "資料庫連接失敗"]));
}

$user_id = $_GET['user_id'];
$sql = "SELECT od.product_id, od.quantity, p.name, p.price, p.image_url 
        FROM order_details od 
        JOIN products p ON od.product_id = p.id 
        WHERE od.user_id = ? AND od.order_id IS NULL";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$cart_items = [];
while ($row = $result->fetch_assoc()) {
    $cart_items[] = $row;
}

echo json_encode(["state" => true, "data" => $cart_items]);
$conn->close();
