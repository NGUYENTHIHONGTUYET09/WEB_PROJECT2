<?php
// Kết nối cơ sở dữ liệu
$conn = new mysqli("localhost", "root", "280704", "qlbh");

// Kiểm tra kết nối
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Truy vấn SQL
$sql = "SELECT user_name, SUM(tongtien) AS total_spent
        FROM giaodich
        WHERE tinhtrang = 1
        GROUP BY user_name
        ORDER BY total_spent DESC
        LIMIT 10";

$result = $conn->query($sql);

// Tạo mảng kết quả
$topCustomers = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $topCustomers[] = $row;
    }
}

// Trả về dữ liệu dưới dạng JSON
echo json_encode($topCustomers);

// Đóng kết nối
$conn->close();