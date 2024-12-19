<?php
session_start();

// Lấy thông tin user ID từ session
$userId = isset($_SESSION['user']['id']) ? $_SESSION['user']['id'] : null;

// Kiểm tra tính hợp lệ của $userId
if (!is_string($userId) || empty($userId)) {
    die("Lỗi: User không hợp lệ.");
}

// Kết nối cơ sở dữ liệu
$conn = mysqli_connect('localhost', 'root', '280704', 'qlbh');
if (!$conn) {
    die("Kết nối thất bại: " . mysqli_connect_error());
}

// Kiểm tra và xử lý yêu cầu "Đã nhận hàng"
if (isset($_POST['action']) && $_POST['action'] == 'da_nhan' && isset($_POST['magd'])) {
    $magd = $_POST['magd'];
    $tinhtrang = 1;
    date_default_timezone_set('Asia/Ho_Chi_Minh');
    $now = date("Y-m-d H:i:s");

    $update_query = "UPDATE giaodich SET tinhtrang = '$tinhtrang', ngaynhan = '$now' WHERE magd = '$magd' AND user_id = '$userId'";
    if (mysqli_query($conn, $update_query)) {
        echo "Cập nhật thành công!";
    } else {
        echo "Lỗi khi cập nhật: " . mysqli_error($conn);
    }
}

// Truy vấn lịch sử giao dịch
$query = "SELECT magd, date, tongtien, tinhtrang, ngaynhan FROM giaodich WHERE user_id = '$userId'";
$result = mysqli_query($conn, $query);
if (!$result) {
    die("Lỗi truy vấn: " . mysqli_error($conn));
}
?>



<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lịch sử giao dịch</title>
    <style>
    body {
        font-family: 'Arial', sans-serif;
        margin: 20px;
        background-color: #f8f9fa;
        color: #333;
    }

    h2 {
        text-align: center;
        color: #007bff;
        font-size: 24px;
        margin-bottom: 20px;
    }

    a.btn {
        padding: 10px 15px;
        border: none;
        border-radius: 5px;
        color: white;
        background-color: #007bff;
        text-decoration: none;
        font-size: 16px;
        display: inline-block;
        margin-bottom: 20px;
    }

    a.btn:hover {
        background-color: #0056b3;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin: 20px 0;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    th,
    td {
        padding: 15px;
        text-align: center;
        font-size: 16px;
    }

    th {
        background-color: #007bff;
        color: white;
        font-weight: bold;
    }

    tr:nth-child(even) {
        background-color: #f9f9f9;
    }

    tr:hover {
        background-color: #f1f1f1;
    }

    td {
        background-color: #fff;
        border: 1px solid #ddd;
    }

    button {
        padding: 8px 15px;
        margin: 5px;
        border: none;
        border-radius: 4px;
        font-size: 14px;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }

    button[type="submit"] {
        background-color: #28a745;
        color: white;
    }

    button[type="submit"]:hover {
        background-color: #218838;
    }

    button[type="submit"]:nth-of-type(2) {
        background-color: #dc3545;
    }

    button[type="submit"]:nth-of-type(2):hover {
        background-color: #c82333;
    }

    button:focus {
        outline: none;
    }

    form {
        display: inline-block;
    }

    form input[type="hidden"] {
        display: none;
    }
    </style>
</head>

<body>
    <a href="index.php" class="btn btn-primary">Quay lại</a>
    <h2>Lịch sử giao dịch</h2>
    <table>
        <tr>
            <th>Mã giao dịch</th>
            <th>Ngày đặt</th>
            <th>Tổng tiền</th>
            <th>Trạng thái</th>
            <th>Ngày Nhận</th>
            <th>Hành động</th>
        </tr>
        <?php while ($row = mysqli_fetch_assoc($result)) { ?>
        <tr>
            <td><?php echo $row['magd']; ?></td>
            <td><?php echo $row['date']; ?></td>
            <td><?php echo $row['tongtien']; ?></td>
            <td><?php echo $row['tinhtrang'] == 1 ? 'Đã nhận' : 'Chưa nhận'; ?></td>
            <td><?php echo $row['ngaynhan'] ?: ''; ?></td>
            <td>
                <form method="POST" action="thongtindonhang.php">
                    <input type="hidden" name="magd" value="<?php echo $row['magd']; ?>">
                    <button type="submit" name="action" value="chi_tiet">Thông tin giao dịch</button>
                    <button type="submit" name="action" value="da_nhan">Đã nhận hàng</button>
                </form>
            </td>
        </tr>
        <?php } ?>
    </table>
</body>

</html>

<?php
// Đóng kết nối
mysqli_close($conn);
?>