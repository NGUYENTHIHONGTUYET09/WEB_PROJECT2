<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$_SESSION['rights'] = "default";
$_SESSION['limit'] = 8;

// Kết nối cơ sở dữ liệu
$conn = mysqli_connect('localhost', 'root', '280704', 'qlbh') or die('Không thể kết nối!');
mysqli_set_charset($conn, 'utf8');

// Khởi tạo và thực hiện truy vấn
$_SESSION['sql'] = "SELECT * FROM sanpham";
$sql = "SELECT * FROM sanpham";
$result = mysqli_query($conn, $sql);
if (!$result) {
    die('Query failed: ' . mysqli_error($conn)); // Kiểm tra lỗi truy vấn
}
$_SESSION['total'] = mysqli_num_rows($result);

// Gọi backend-index.php
require_once 'backend-index.php';

// Kiểm tra và khởi tạo giỏ hàng
if (!isset($_SESSION['client_cart'])) {
    $_SESSION['client_cart'] = []; // Khởi tạo là mảng
    $_SESSION['client_cart'][0] = "tmp"; // Thêm phần tử vào giỏ hàng
}

$_SESSION['user_cart'] = []; // Khởi tạo là mảng
$_SESSION['user_cart'][0] = "tmp";

if (isset($_SESSION['user'])) {
    $_SESSION['rights'] = "user";
    $_SESSION['like'] = []; // Khởi tạo là mảng
    $_SESSION['like'][0] = "tmp";

    // Lấy giỏ hàng của người dùng
    $sql = "SELECT masp, soluong FROM giohang WHERE user_id = '" . $_SESSION['user']['id'] . "'";
    $result = mysqli_query($conn, $sql);
    if (!$result) {
        die('Query failed: ' . mysqli_error($conn)); // Kiểm tra lỗi truy vấn
    }
    while ($row = mysqli_fetch_assoc($result)) {
        $_SESSION['user_cart'][] = $row['masp']; // Thêm sản phẩm vào giỏ hàng
    }

    // Lấy sản phẩm yêu thích của người dùng
    $sql = "SELECT masp FROM sanphamyeuthich WHERE user_id = '" . $_SESSION['user']['id'] . "'";
    $result = mysqli_query($conn, $sql);
    if (!$result) {
        die('Query failed: ' . mysqli_error($conn)); // Kiểm tra lỗi truy vấn
    }
    while ($row = mysqli_fetch_assoc($result)) {
        $_SESSION['like'][] = $row['masp']; // Thêm sản phẩm yêu thích
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title> Clothes - Thể hiện phong cách đa dạng! </title>
    <meta charset="utf-8">
    <!-- <link rel="SHORTCUT ICON"  href=> -->
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <script type="text/javascript" src="libs/script/script.js"></script>
    <link rel="stylesheet" href="libs/css/style.css">

    <!-- File css -> file js -> file jquery -->
    <link rel="stylesheet" href="libs/bootstrap/css/bootstrap.css">
    <script src="libs/jquery/jquery-latest.js"></script>
    <script type="text/javascript" src="libs/bootstrap/js/bootstrap.min.js"></script>

    <!-- font used in this site -->
    <link href="https://fonts.googleapis.com/css?family=Kaushan+Script" rel="stylesheet">
</head>

<body>
    <header>
        <a href="index.php"><img src="images/header.png">
            <h2 class="logo">
                <div class="">CLothes</div>
            </h2>
        </a>
        <div class="header-detail">
            <p>113 Hoàng Sa, Đa Kao, Tân Bình, Hồ Chí Minh, Việt Nam<br>
                <i>8h - 22h Hằng ngày, kể cả Ngày lễ và Chủ nhật</i>
            </p>
        </div>
    </header>