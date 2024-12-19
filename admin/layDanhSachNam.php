<?php
// Kết nối cơ sở dữ liệu
$conn = new mysqli("localhost", "root", "280704", "qlbh");

// Kiểm tra kết nối
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


// Lấy danh sách các năm từ giao dịch
$sql = "SELECT DISTINCT YEAR(date) AS year FROM giaodich ORDER BY year DESC";
$result = $conn->query($sql);

$years = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $years[] = $row['year'];
    }
}

// Trả về danh sách các năm dưới dạng JSON
echo json_encode($years);
$conn->close();