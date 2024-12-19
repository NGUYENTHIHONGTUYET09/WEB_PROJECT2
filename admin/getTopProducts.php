<?php
// Kết nối cơ sở dữ liệu
$conn = new mysqli("localhost", "root", "280704", "qlbh");

// Kiểm tra kết nối
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Truy vấn top 5 sản phẩm
$sql = "SELECT 
            sanpham.tensp AS ten_san_pham,
            SUM(chitietgiaodich.soluong) AS tong_so_luong_ban
        FROM 
            chitietgiaodich
        JOIN 
            sanpham ON chitietgiaodich.masp = sanpham.masp
        GROUP BY 
            sanpham.tensp
        ORDER BY 
            tong_so_luong_ban DESC
        LIMIT 10";

$result = $conn->query($sql);

$data = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
}

// Trả về dữ liệu JSON
echo json_encode($data);

$conn->close();