<?php
session_start();

function connect()
{
    $conn = mysqli_connect('localhost', 'root', '280704', 'qlbh');
    if (!$conn) {
        die('Kết nối thất bại: ' . mysqli_connect_error());
    }
    mysqli_set_charset($conn, 'utf8');
    return $conn;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] === 'delete_from_like') {
    $masp = intval($_POST['masp']);
    $like_count = 0;

    if (isset($_SESSION['user']['id'])) {
        // Đăng nhập: Xóa sản phẩm trong CSDL
        $user_id = $_SESSION['user']['id'];
        $conn = connect();

        $delete_query = "DELETE FROM sanphamyeuthich WHERE user_id = ? AND masp = ?";
        $stmt = mysqli_prepare($conn, $delete_query);
        mysqli_stmt_bind_param($stmt, 'ii', $user_id, $masp);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        // Đếm lại số lượng like_count từ CSDL
        $count_query = "SELECT COUNT(*) AS like_count FROM sanphamyeuthich WHERE user_id = ?";
        $stmt = mysqli_prepare($conn, $count_query);
        mysqli_stmt_bind_param($stmt, 'i', $user_id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result($stmt, $like_count);
        mysqli_stmt_fetch($stmt);

        mysqli_stmt_close($stmt);
        mysqli_close($conn);
    } else {
        // Chưa đăng nhập: Xóa sản phẩm trong session
        if (isset($_SESSION['like'])) {
            if (($key = array_search($masp, $_SESSION['like'])) !== false) {
                unset($_SESSION['like'][$key]);
                $_SESSION['like'] = array_values($_SESSION['like']);
            }
        }
        $like_count = count($_SESSION['like']);
    }

    // Trả về kết quả JSON
    echo json_encode([
        'status' => 'success',
        'message' => 'Sản phẩm đã được xóa khỏi danh sách yêu thích.',
        'like_count' => $like_count
    ]);
    exit;
}