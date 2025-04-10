<?php
require_once "dbtools.php";

header('Content-Type: application/json; charset=UTF-8'); // 明確指定 UTF-8
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

$action = $_GET['action'] ?? '';
$input = json_decode(file_get_contents("php://input"), true);

$link = create_connection();
// 統一設置編碼，放在所有查詢之前
mysqli_set_charset($link, "utf8mb4") or die("無法設置字符集: " . mysqli_error($link));

switch ($action) {
    case 'add_to_cart':
        $uid = $input['uid'] ?? '';
        $product_id = $input['product_id'] ?? '';
        $quantity = $input['quantity'] ?? 1;

        if (!$uid || !$product_id || !$quantity) {
            echo json_encode(["state" => false, "message" => "缺少必要參數"]);
            mysqli_close($link);
            exit;
        }

        // 檢查商品是否存在並有足夠庫存
        $sql = "SELECT name, price, stock FROM products WHERE id = $product_id";
        $result = execute_sql($link, "if0_38646806_jjboy0220", $sql);
        if (!$result || mysqli_num_rows($result) === 0) {
            echo json_encode(["state" => false, "message" => "商品不存在"]);
            mysqli_close($link);
            exit;
        }

        $product = mysqli_fetch_assoc($result);
        if ($product['stock'] < $quantity) {
            echo json_encode(["state" => false, "message" => "庫存不足"]);
            mysqli_close($link);
            exit;
        }

        // 檢查購物車中是否已有該商品
        $sql = "SELECT id, quantity FROM cart WHERE user_uid = '$uid' AND product_id = $product_id";
        $result = execute_sql($link, "if0_38646806_jjboy0220", $sql);

        if (mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);
            $new_quantity = $row['quantity'] + $quantity;
            $sql = "UPDATE cart SET quantity = $new_quantity WHERE id = " . $row['id'];
        } else {
            $sql = "INSERT INTO cart (user_uid, product_id, quantity) VALUES ('$uid', $product_id, $quantity)";
        }

        $result = execute_sql($link, "if0_38646806_jjboy0220", $sql);
        if ($result) {
            echo json_encode(["state" => true, "message" => "成功加入購物車"]);
        } else {
            echo json_encode(["state" => false, "message" => "加入購物車失敗"]);
        }
        break;

    case 'get_cart':
        $uid = $_GET['uid'] ?? '';
        if (!$uid) {
            echo json_encode(["state" => false, "message" => "缺少會員 UID"]);
            mysqli_close($link);
            exit;
        }

        $sql = "SELECT p.name, p.price, c.quantity, c.product_id 
                FROM cart c 
                JOIN products p ON c.product_id = p.id 
                WHERE c.user_uid = '$uid'";
        $result = execute_sql($link, "if0_38646806_jjboy0220", $sql);

        $items = [];
        $total_amount = 0;
        while ($row = mysqli_fetch_assoc($result)) {
            $items[] = $row;
            $total_amount += $row['price'] * $row['quantity'];
        }

        echo json_encode([
            "state" => true,
            "data" => [
                "items" => $items,
                "total_amount" => $total_amount
            ],
            JSON_UNESCAPED_UNICODE // 確保中文不被轉義
        ]);
        break;

    default:
        echo json_encode(["state" => false, "message" => "無效的操作"]);
}

mysqli_close($link);
?>