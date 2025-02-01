<?php
// Đường dẫn tới file database
$databaseFile = 'database.txt';

// Hàm đọc dữ liệu từ file
function readDatabase($file) {
    if (!file_exists($file)) {
        return [];
    }
    $data = file_get_contents($file);
    return $data ? json_decode($data, true) : [];
}

// Hàm ghi dữ liệu vào file
function writeDatabase($file, $data) {
    file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT));
}

// Xử lý các hành động
$action = $_GET['action'] ?? '';

if ($action === 'fetch') {
    // Lấy danh sách sản phẩm
    $products = readDatabase($databaseFile);
    header('Content-Type: application/json');
    echo json_encode($products);
} elseif ($action === 'add') {
    // Thêm sản phẩm mới
    $input = json_decode(file_get_contents('php://input'), true);
    $products = readDatabase($databaseFile);

    // Kiểm tra sản phẩm đã tồn tại hay chưa
    $existingProduct = null;
    foreach ($products as &$product) {
        if ($product['code'] === $input['code']) {
            $existingProduct = $product;
            $product['quantity'] += $input['quantity'];
            break;
        }
    }

    if (!$existingProduct) {
        $products[] = $input;
    }

    writeDatabase($databaseFile, $products);
    echo 'Sản phẩm đã được thêm!';
} elseif ($action === 'delete') {
    // Xóa sản phẩm theo mã hàng
    $code = $_GET['code'] ?? '';
    $products = readDatabase($databaseFile);

    // Lọc bỏ sản phẩm cần xóa
    $products = array_filter($products, function($product) use ($code) {
        return $product['code'] !== $code;
    });

    writeDatabase($databaseFile, array_values($products));
    echo 'Sản phẩm đã được xóa!';
} else {
    echo 'Hành động không hợp lệ!';
}
?>
