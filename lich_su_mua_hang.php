<?php
session_start();
// Access the user session variable
$user = isset($_SESSION['user']) ? $_SESSION['user'] : null;
// Kết nối cơ sở dữ liệu
$conn = mysqli_connect('localhost', 'root', '280704', 'qlbh');
if (!$conn) {
    die("Kết nối thất bại: " . mysqli_connect_error());
}
// Xử lý trạng thái nếu có yêu cầu POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = intval($_POST['id']);
    $action = $_POST['action'];
    if ($action === 'chi_tiet') {
        header("Location: lich_su_mua_hang_chitiet.php?id=$id");
    }
    if ($action === 'da_nhan') {
        // Cập nhật trạng thái đơn hàng là 'Đã nhận hàng'
        $sql_update = "UPDATE lich_su_mua_hang SET trang_thai = 'Đã nhận' WHERE id = $id";
        if (mysqli_query($conn, $sql_update)) {
            echo "<script>alert('Đã xác nhận nhận hàng thành công!');</script>";
        } else {
            echo "<script>alert('Xảy ra lỗi, vui lòng thử lại!!!');</script>";
        }
    } elseif ($action === 'tra_hang') {
        // Kiểm tra ngày dự kiến nhận hàng trước khi cho phép yêu cầu trả hàng
        $sql_check_date = "SELECT ngay_du_kien_nhan FROM lich_su_mua_hang WHERE id = $id";
        $result = mysqli_query($conn, $sql_check_date);
        $row = mysqli_fetch_assoc($result);
        $ngay_du_kien_nhan = $row['ngay_du_kien_nhan'];
        $ngay_hien_tai = date('Y-m-d');
        // Tính số ngày giữa ngày dự kiến nhận hàng và ngày hiện tại
        $datediff = strtotime($ngay_du_kien_nhan) - strtotime($ngay_hien_tai);
        $days_diff = round($datediff / (60 * 60 * 24));
        // Nếu ngày dự kiến nhận hàng hơn 3 ngày so với ngày hiện tại, không cho phép trả hàng
        if ($days_diff < 3) {
            echo "<script>alert('Không thể yêu cầu hoàn trả!');</script>";
        } else {
            // Chuyển hướng đến trang yêu cầu trả hàng
            $sql_update = "UPDATE lich_su_mua_hang SET trang_thai = 'Yêu cầu trả' WHERE id = $id";
            if (mysqli_query($conn, $sql_update)) {
                echo "<script>alert('Yêu cầu trả hàng thành công!');</script>";
            } else {
                echo "<script>alert('Xảy ra lỗi, vui lòng thử lại!!!');</script>";
            }
            // header("Location: yeu_cau_tra_hang.php?id=$id");
            // exit();
        }
    }
}
// Lấy dữ liệu từ bảng lịch sử mua hàng chỉ với những đơn hàng đã hoàn thành hoặc giao hàng
//$sql = "SELECT * FROM lich_su_mua_hang WHERE trang_thai IN ('Đã giao hàng', 'Hoàn tất')";
$sql = "SELECT * FROM lich_su_mua_hang WHERE user_id = " . intval($user['id']);
$result = mysqli_query($conn, $sql);


// Hiển thị dữ liệu
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lịch sử mua hàng</title>
    <style>
    body {
        font-family: Arial, sans-serif;
        margin: 20px;
        background-color: #f8f9fa;
    }

    h2 {
        text-align: center;
        color: #007bff;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin: 20px 0;
    }

    table,
    th,
    td {
        border: 1px solid #dee2e6;
    }

    th,
    td {
        padding: 10px;
        text-align: center;
    }

    th {
        background-color: #007bff;
        color: white;
    }

    tr:nth-child(even) {
        background-color: #f2f2f2;
    }

    .btn {
        padding: 5px 10px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
    }

    .btn-info {
        background-color: #17a2b8;
        color: white;
    }

    .btn-primary {
        background-color: #007bff;
        color: white;
    }

    .btn-danger {
        background-color: #dc3545;
        color: white;
    }

    .btn:hover {
        opacity: 0.9;
        cursor: pointer;
        text-decoration: underline;
    }
    </style>
</head>

<body>
    <a href="index.php" class="btn btn-primary">Quay lại</a>
    <h2>Lịch sử mua hàng</h2>
    <table>
        <tr>
            <th>Mã đơn hàng</th>
            <th>Ngày đặt</th>
            <th>Ngày dự kiến nhận</th>
            <th>Tổng tiền</th>
            <th>Trạng thái</th>
            <th>Hành động</th>
        </tr>
        <?php while ($row = mysqli_fetch_assoc($result)) { ?>
        <tr>
            <td><?php echo htmlspecialchars($row['id']); ?></td>
            <td><?php echo htmlspecialchars($row['ngay_dat']); ?></td>
            <td><?php echo htmlspecialchars($row['ngay_du_kien_nhan']); ?></td>
            <td><?php echo htmlspecialchars($row['tong_tien']); ?></td>
            <td><?php echo htmlspecialchars($row['trang_thai']); ?></td>
            <td>
                <form method="POST" action="">
                    <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                    <button type="submit" name="action" value="chi_tiet" class="btn btn-info">Thông tin đơn
                        hàng</button>
                    <button type="submit" name="action" value="da_nhan" class="btn btn-primary">Đã nhận hàng</button>
                    <button type="submit" name="action" value="tra_hang" class="btn btn-danger">Yêu cầu trả
                        hàng</button>
                </form>
            </td>
        </tr>
        <?php } ?>
    </table>
</body>

</html>
<?php mysqli_close($conn); ?>