<?php
// Lấy thông tin từ POST
$data = json_decode(file_get_contents('php://input'), true);
$id = $data['id'];
// Kiểm tra ngày dự kiến nhận hàng
$conn = mysqli_connect('localhost', 'root', '280704', 'qlbh');
$sql = "SELECT ngay_du_kien_nhan FROM lich_su_mua_hang WHERE id = $id";
$result = mysqli_query($conn, $sql);
$row = mysqli_fetch_assoc($result);
$ngay_du_kien_nhan = $row['ngay_du_kien_nhan'];
$ngay_hien_tai = date('Y-m-d');
// Kiểm tra điều kiện trả hàng
if (strtotime($ngay_du_kien_nhan) < strtotime($ngay_hien_tai . ' - 3 days')) {
    echo json_encode(['message' => 'Không thể yêu cầu hoàn trả']);
} else {
    // Cập nhật trạng thái trả hàng
    $sql_update = "UPDATE lich_su_mua_hang SET trang_thai = 'Yêu cầu trả' WHERE id = $id";
    mysqli_query($conn, $sql_update);
    echo json_encode(['message' => 'Đã yêu cầu trả hàng']);
}
mysqli_close($conn);