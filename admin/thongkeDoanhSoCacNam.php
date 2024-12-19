<?php
// Kết nối cơ sở dữ liệu
$conn = new mysqli("localhost", "root", "280704", "qlbh");

// Kiểm tra kết nối
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

// Truy vấn doanh số theo năm cho trạng thái = 1
$sql = "SELECT YEAR(date) AS year, SUM(tongtien) AS total_sales 
        FROM giaodich 
        WHERE tinhtrang = 1 
        GROUP BY YEAR(date)";
$result = $conn->query($sql);

$data = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
}

$conn->close();

// Trả về dữ liệu dưới dạng JSON
header('Content-Type: application/json');
echo json_encode($data);