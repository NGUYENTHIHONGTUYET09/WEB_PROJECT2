<?php
session_start();
$conn = mysqli_connect('localhost', 'root', '280704', 'qlbh');
if (!$conn) {
    die("Kết nối thất bại: " . mysqli_connect_error());
}

if (isset($_POST['action']) && isset($_POST['magd'])) {
    $magd = $_POST['magd'];

    if ($_POST['action'] == 'da_nhan') {
        $tinhtrang = 1;
        date_default_timezone_set('Asia/Ho_Chi_Minh');
        $now = date('Y-m-d H:i:s');

        $update_query = "UPDATE giaodich SET tinhtrang = '$tinhtrang', ngaynhan = '$now' WHERE magd = '$magd'";
        if (mysqli_query($conn, $update_query)) {
            echo "<script>
                alert('Cập nhật thành công!');
                window.location.href = 'lich_su_mua_hang.php';
            </script>";
        } else {
            $error_message = mysqli_error($conn);
            echo "<script>
                alert('Lỗi cập nhật: $error_message');
            </script>";
        }
    } elseif ($_POST['action'] == 'chi_tiet') {
        $detail_query = "
            SELECT 
                gv.ten AS ten_khach_hang, gv.diachi, gv.sodt, gv.email, gd.date AS ngay_dat, gd.magd, 
                sp.tensp, sp.anhchinh, sp.gia, ctdg.soluong, (sp.gia * ctdg.soluong) AS tonggia, sp.masp
            FROM giaodich gd
            INNER JOIN thanhvien gv ON gd.user_id = gv.id
            INNER JOIN chitietgiaodich ctdg ON gd.magd = ctdg.magd
            INNER JOIN sanpham sp ON ctdg.masp = sp.masp
            WHERE gd.magd = '$magd'
        ";
        $detail_result = mysqli_query($conn, $detail_query);
        if (!$detail_result) {
            die("Lỗi truy vấn chi tiết: " . mysqli_error($conn));
        }

        echo "<h3>Chi tiết giao dịch</h3>";

        // Hiển thị thông tin khách hàng và giao dịch
        $row = mysqli_fetch_assoc($detail_result);
        echo "<div class='customer-info'>
                <p><strong>Tên khách hàng:</strong> {$row['ten_khach_hang']}</p>
                <p><strong>Địa chỉ nhận hàng:</strong> {$row['diachi']}</p>
                <p><strong>Số điện thoại:</strong> {$row['sodt']}</p>
                <p><strong>Email:</strong> {$row['email']}</p>
                <p><strong>Ngày đặt:</strong> {$row['ngay_dat']}</p>
              </div>";

        // Bảng chi tiết sản phẩm
        echo "<table class='table'>
                <tr>
                    <th>Sản phẩm</th>
                    <th>Ảnh</th>
                    <th>Giá</th>
                    <th>Số lượng</th>
                    <th>Tổng giá</th>
                </tr>";

        // Hiển thị chi tiết các sản phẩm
        mysqli_data_seek($detail_result, 0); // Reset pointer
        while ($row = mysqli_fetch_assoc($detail_result)) {
            echo "<tr>
                    <td>{$row['tensp']}</td>
                    <td><img src='{$row['anhchinh']}' width='50'></td>
                    <td>{$row['gia']}</td>
                    <td>{$row['soluong']}</td>
                    <td>{$row['tonggia']}</td>
                </tr>";
        }
        echo "</table>";

        // Lưu các sản phẩm vào session để truyền sang order.php
        $selected_products = [];
        mysqli_data_seek($detail_result, 0); // Reset pointer
        while ($row = mysqli_fetch_assoc($detail_result)) {
            // Thêm mã sản phẩm vào mảng
            $selected_products[] = $row['masp'];
        }
        $_SESSION['selected_products'] = $selected_products;

        // Thêm nút "Đặt lại" chuyển đến order.php
        echo "<div class='btn-container'>
                <form action='order.php' method='POST'>
                    <input type='hidden' name='selected_products[]' value='" . implode("' /><input type='hidden' name='selected_products[]' value='", $selected_products) . "' />
                    <button type='submit' class='btn'>Đặt lại</button>
                </form>
              </div>";
    }
}

mysqli_close($conn);
?>


<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chi tiết giao dịch</title>
    <style>
    body {
        font-family: Arial, sans-serif;
        background-color: #f4f4f9;
        margin: 0;
        padding: 0;
    }

    h3 {
        text-align: center;
        color: #333;
        margin-top: 30px;
    }

    .customer-info {
        background-color: #fff;
        padding: 20px;
        margin: 20px auto;
        border-radius: 8px;
        width: 80%;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .customer-info p {
        font-size: 16px;
        margin: 8px 0;
    }

    .customer-info p strong {
        color: #333;
    }

    table {
        width: 80%;
        margin: 20px auto;
        border-collapse: collapse;
        border-radius: 8px;
        overflow: hidden;
    }

    th,
    td {
        padding: 10px;
        text-align: left;
        border-bottom: 1px solid #ddd;
    }

    th {
        background-color: #f2f2f2;
        font-size: 18px;
        color: #333;
    }

    tr:nth-child(even) {
        background-color: #f9f9f9;
    }

    td img {
        border-radius: 4px;
        margin: 0 auto;
        display: block;
    }

    .table tr:hover {
        background-color: #eaeaea;
    }

    .btn {
        background-color: #4CAF50;
        color: white;
        padding: 10px 20px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        text-align: center;
        text-decoration: none;
    }

    .btn:hover {
        background-color: #45a049;
    }

    .btn-container {
        text-align: center;
        margin-top: 20px;
    }
    </style>
</head>

<body>
</body>

</html>