<?php
session_start();

// Hàm kết nối database
function connect()
{
    $conn = mysqli_connect('localhost', 'root', '280704', 'qlbh');
    if (!$conn) {
        die('Kết nối thất bại: ' . mysqli_connect_error());
    }
    mysqli_set_charset($conn, 'utf8');
    return $conn;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] === 'add_to_cart') {
    $masp = intval($_POST['masp']);

    if (!isset($_SESSION['user']['id'])) {
        echo "Bạn cần đăng nhập để thực hiện chức năng này!";
        exit;
    }

    $user_id = $_SESSION['user']['id'];

    // Kết nối database
    $conn = connect();

    // Thêm sản phẩm vào bảng 'giohang'
    $insert_query = "INSERT INTO giohang (user_id, masp, soluong) VALUES (?, ?, 1)";
    $stmt = mysqli_prepare($conn, $insert_query);
    mysqli_stmt_bind_param($stmt, 'ii', $user_id, $masp);
    mysqli_stmt_execute($stmt);

    if (mysqli_stmt_affected_rows($stmt) > 0) {
        // Xóa sản phẩm khỏi bảng 'sanphamyeuthich'
        $delete_query = "DELETE FROM sanphamyeuthich WHERE user_id = ? AND masp = ?";
        $stmt = mysqli_prepare($conn, $delete_query);
        mysqli_stmt_bind_param($stmt, 'ii', $user_id, $masp);
        mysqli_stmt_execute($stmt);

        if (mysqli_stmt_affected_rows($stmt) > 0) {
            echo "Sản phẩm đã được chuyển vào giỏ hàng và xóa khỏi danh sách yêu thích!";
        } else {
            echo "Lỗi khi xóa sản phẩm khỏi danh sách yêu thích.";
        }
    } else {
        echo "Lỗi khi thêm sản phẩm vào giỏ hàng.";
    }

    // Đóng kết nối
    mysqli_stmt_close($stmt);
    mysqli_close($conn);
    exit;
}