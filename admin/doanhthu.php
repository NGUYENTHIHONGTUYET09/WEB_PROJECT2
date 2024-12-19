<?php
// Kết nối cơ sở dữ liệu
$conn = new mysqli("localhost", "root", "280704", "qlbh");

// Kiểm tra kết nối
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

// Lấy năm từ request
$year = isset($_GET['year']) ? intval($_GET['year']) : date('Y');

// Câu truy vấn SQL
$sql = "SELECT 
            MONTH(date) AS month, 
            SUM(tongtien) AS total_amount 
        FROM 
            giaodich 
        WHERE 
            tinhtrang = 1 AND YEAR(date) = ?
        GROUP BY 
            MONTH(date)
        ORDER BY 
            month ASC";

// Chuẩn bị và thực thi câu truy vấn
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $year);
$stmt->execute();
$result = $stmt->get_result();

// Chuẩn bị dữ liệu trả về JSON
$months = [];
$totals = [];
while ($row = $result->fetch_assoc()) {
    $months[] = $row['month'];
    $totals[] = $row['total_amount'];
}

// Đóng kết nối
$stmt->close();
$conn->close();

// Trả về JSON
echo json_encode(['months' => $months, 'totals' => $totals]);