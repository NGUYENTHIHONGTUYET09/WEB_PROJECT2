<?php
$id = $_GET['id']; // Lấy giá trị id từ URL
if (!$id) {
    echo "<script>alert(''ID không hợp lệ');</script>";
    // echo json_encode(['message' => 'ID không hợp lệ']);
    exit;
}

$conn = mysqli_connect('localhost', 'root', '280704', 'qlbh');
if (!$conn) {
    echo json_encode(['message' => 'Không thể kết nối cơ sở dữ liệu: ' . mysqli_connect_error()]);
    exit;
}
// Kiểm tra ngày dự kiến nhận hàng
$sql = "SELECT ngay_du_kien_nhan FROM lich_su_mua_hang WHERE id = $id";
$result = mysqli_query($conn, $sql);
if (!$result) {
    echo json_encode(['message' => 'Lỗi truy vấn SQL: ' . mysqli_error($conn)]);
    exit;
}
$row = mysqli_fetch_assoc($result);
if (!$row) {
    echo json_encode(['message' => 'Không tìm thấy đơn hàng với ID: ' . $id]);
    exit;
}
$ngay_du_kien_nhan = $row['ngay_du_kien_nhan'];
$ngay_hien_tai = date('Y-m-d');
// Kiểm tra điều kiện trả hàng
if (strtotime($ngay_du_kien_nhan) < strtotime($ngay_hien_tai . ' - 3 days')) {
    echo json_encode(['message' => 'Không thể yêu cầu hoàn trả']);
} else {
    // Cập nhật trạng thái trả hàng
    $sql_update = "UPDATE lich_su_mua_hang SET trang_thai = 'Yêu cầu trả' WHERE id = $id";
    if (mysqli_query($conn, $sql_update)) {
        echo json_encode(['message' => 'Đã yêu cầu trả hàng']);
    } else {
        echo json_encode(['message' => 'Lỗi khi cập nhật trạng thái: ' . mysqli_error($conn)]);
    }
}
mysqli_close($conn);