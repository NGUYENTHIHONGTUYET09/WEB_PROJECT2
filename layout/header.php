<?php
session_start();
$_SESSION['rights'] = "default";
$_SESSION['limit'] = 8;
$cartCount = 0;
// Định nghĩa hàm connect()
function connect()
{
    $conn = mysqli_connect('localhost', 'root', '280704', 'qlbh');
    if (!$conn) {
        die('Kết nối thất bại: ' . mysqli_connect_error());
    }
    mysqli_set_charset($conn, 'utf8'); // Đặt charset
    return $conn; // Trả về kết nối
}
$conn = connect(); // Gọi hàm connect() đã định nghĩa
//Khi đăng nhập thì tự động sẽ sinh ra 1 usidtf và kiểm tra trong csdl có tài khoản đó không
if (isset($_COOKIE['usidtf'])) {
    $s = "SELECT * FROM thanhvien WHERE id = '" . $_COOKIE['usidtf'] . "'";
    $result = mysqli_query($conn, $s);
    while ($row = mysqli_fetch_assoc($result)) {
        $_SESSION['user'] = $row;
    }
}
$_SESSION['sql'] = "SELECT * FROM sanpham";
$sql = "SELECT * FROM sanpham";
$result = mysqli_query($conn, $sql);
if (!$result) {
    die('Query failed: ' . mysqli_error($conn));
}
$_SESSION['total'] = mysqli_num_rows($result);
$_SESSION['user_cart'] = []; // Khởi tạo mảng cho giỏ hàng
$myString = []; // Khởi tạo mảng cho myString
$myString[0] = "tmp"; // Gán giá trị vào vị trí 0 của mảng
if (isset($_SESSION['user'])) {
    $_SESSION['rights'] = "user";
    $_SESSION['like'] = []; // Khởi tạo mảng cho sở thích
    $_SESSION['like'][0] = "tmp";
    $user = $_SESSION['user'];
    $user_id = $user['id'];


    //$_SESSION['user']['id'] là mã định danh (ID) của người dùng đó, giúp phân biệt người này với những người dùng khác.
    $sql = "SELECT *
                FROM 
                    lich_su_mua_hang lsmh
                INNER JOIN 
                    lich_su_mua_hang_sanpham lsmh_sp ON lsmh.id = lsmh_sp.maLSmuahang
                INNER JOIN 
                    sanpham sp ON lsmh_sp.sanpham_id = sp.masp
                WHERE 
                    lsmh.user_id = $user_id AND lsmh.trang_thai = 'Giỏ hàng'
                ";
    $result = mysqli_query($conn, $sql);
    // count($result)
    if ($result) {
        $cartCount = mysqli_num_rows($result);
    }

    //$_SESSION['user']['id'] là mã định danh (ID) của người dùng đó, giúp phân biệt người này với những người dùng khác.
    $sql = "SELECT masp, soluong FROM giohang WHERE user_id = '" . $_SESSION['user']['id'] . "'";
    $result = mysqli_query($conn, $sql);
    while ($row = mysqli_fetch_assoc($result)) {
        $_SESSION['user_cart'][] = $row['masp']; // Thêm sản phẩm vào giỏ hàng
    }

    $sql = "SELECT masp FROM sanphamyeuthich WHERE user_id = '" . $_SESSION['user']['id'] . "'";
    $result = mysqli_query($conn, $sql);
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
    <link rel="stylesheet" type="text/css" href="libs/animate.css">

    <script type="text/javascript">
        $(document).ready(function() {
            $(".cart-container").click(function() {
                $(this).toggleClass('cart-ordered');
            });
            $(".unlike-container").click(function() {
                if ($('#s-s').data("stt") == "alreadysignin") {
                    $(this).toggleClass('liked');
                }
            });
        });
        $(document).on("click", function() {
            $(".showup").hide();
        });
        window.onkeyup = function(e) {
            var x = $('#srch-val').val();
            var y = $("#srch-val").is(":focus");
            if (e.keyCode == 13 && y && x != "") {
                ajax_search();
            }
        }
    </script>
</head>

<body>
    <header id='header'>
        <a href="index.php"><img src="images/logo5.png"><br>
            <h2 class="logo"> 💎Fashion Katy </h2>
        </a>
        <ul class="header-menu">



            <?php
            if ($_SESSION['rights'] == "default") { ?>
                <li><a onclick='ajax_dangnhap()' id="s-s" data-stt='nosignin'>Đăng nhập</a>
                    <div class='mn-ef'></div>
                </li>
                <li><a onclick='ajax_dangky()'>Đăng ký</a>
                    <div class='mn-ef'></div>
                </li>
            <?php } else { ?>
                <li><a onclick="$('#user-setting').toggle()" id="s-s" data-stt='alreadysignin'>Chào
                        <?php echo $_SESSION['user']['ten'] ?></a>
                    <div class='mn-ef'></div>
                </li>
                <div id='user-setting'>
                    <ul>
                        <li onclick="call_to_thongtin();$('#user-setting').toggle()">Thông tin tài khoản</li>
                        <br />
                        <li onclick='call_to_dangxuat()'>Đăng xuất</li>
                    </ul>
                </div>
            <?php }
            ?>

            <li data-userid="<?php echo isset($_SESSION['user']) ? $_SESSION['user']['id'] : ''; ?>"
                onclick="ajax_giohang()">
                <i class="glyphicon glyphicon-shopping-cart"></i> Giỏ hàng
                <div class="mn-ef"></div>

            </li>
            <?php if (isset($_SESSION['rights']) && $_SESSION['rights'] !== "default" && isset($_SESSION['user'])) { ?>
                <li>
                    <a href="lich_su_mua_hang.php" class="btn btn-info"
                        style="margin-top: 5px; color: white; font-size: 16px; padding: 10px 20px; border-radius: 5px;">
                        Lịch sử mua hàng
                    </a>
                </li>
            <?php } ?>

            <div class="mn-ef"></div>
            </li>
        </ul>
        <div class="header-detail">
            <p>Khu phố 6, Phường Linh Trung, TP Thủ Đức, Việt Nam<br>
                <i>8h - 22h Hằng ngày, kể cả Ngày lễ và Chủ nhật</i>
            </p>
        </div>
    </header>

    </header>

    <nav class="navbar navbar-default" role="navigation" id="nav">
        <div class="container-fluid">
            <!-- Brand and toggle get grouped for better mobile display -->
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-ex1-collapse">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand logo" href="index.php">Clothes</a>
                <div id="custom-search-input">
                    <div class="input-group col-md-12" style="background-color: white;">
                        <input type="text" class="form-control input-lg" placeholder="Bạn tìm gì?" id='src-v' />
                        <span class="input-group-btn">
                            <button class="btn btn-info btn-lg" type="button" onclick="ajax_search()">
                                <i class="glyphicon glyphicon-search"></i>
                            </button>
                        </span>
                    </div>
                </div>
            </div>
            <!-- Collect the nav links, forms, and other content for toggling -->
            <div class="collapse navbar-collapse navbar-ex1-collapse">
                <ul class="nav navbar-nav">
                    <li class="dropdown menu-name">
                        <a class="dropdown-toggle" data-toggle="dropdown" style="cursor: pointer;">Danh mục sản phẩm <b
                                class="caret"></b></a>
                        <ul class="dropdown-menu">
                            <li><a onclick="ajax_danhmucsp('all')">Tất cả sản phẩm</a></li>
                            <li><a onclick="ajax_danhmucsp('ao_khoac')">Áo Khoác</a></li>
                            <li><a onclick="ajax_danhmucsp('ao_thun')">Áo Thun</a></li>
                            <li><a onclick="ajax_danhmucsp('ao_so_mi')">Áo Sơ Mi</a></li>
                            <li><a onclick="ajax_danhmucsp('ao_hoodie')">Áo Hoodie</a></li>
                            <li><a onclick="ajax_danhmucsp('quan')">Quần</a></li>
                            <li><a onclick="ajax_danhmucsp('dam')">Đầm</a></li>
                            <li><a onclick="ajax_danhmucsp('phu_kien')">Phụ kiện</a></li>
                        </ul>

                    </li>

                    <li class="menu-name" id="dgg"><a onclick="ajax_saling()">Đang giảm giá</a></li>
                    <li class="menu-name" id="spm"><a onclick="ajax_new()">Sản phẩm mới</a></li>
                    <li class="menu-name" id="mntq"><a onclick="ajax_buy()">Mua nhiều tuần qua</a></li>

                </ul>
                <div class="like-container" onclick="ajax_like()" style="cursor: pointer;">
                    <i class="glyphicon glyphicon-heart navbar-right btn-lg" id="like_count">
                        <?php
                        // Kiểm tra nếu người dùng đã đăng nhập và có ID người dùng trong session
                        if (isset($_SESSION['user']['id'])) {
                            // Lấy số lượng sản phẩm yêu thích từ cơ sở dữ liệu
                            $conn = connect(); // Kết nối cơ sở dữ liệu
                            if ($conn) {
                                $user_id = $_SESSION['user']['id'];
                                $sql = "SELECT COUNT(*) AS like_count FROM sanphamyeuthich WHERE user_id = ?";
                                $stmt = $conn->prepare($sql);
                                $stmt->bind_param("s", $user_id);
                                $stmt->execute();
                                $result = $stmt->get_result()->fetch_assoc();
                                echo $result['like_count'] > 0 ? $result['like_count'] : "0"; // Mặc định là 0 nếu không có sản phẩm yêu thích
                            } else {
                                echo "0";
                            }
                        } else {
                            echo "0"; // Nếu người dùng chưa đăng nhập
                        }
                        ?>
                    </i>
                </div>

                <div onclick="ajax_giohang()" style="cursor: pointer;">
                    <i class="glyphicon glyphicon-shopping-cart navbar-right btn-lg" id="cart_count">
                        <?php

                        if ($_SESSION['rights'] == "default") {
                            if (isset($_SESSION['client_cart'])) {
                                echo count($_SESSION['client_cart']);
                            } else {
                                echo "0";  // Mặc định là 0 nếu không có giỏ hàng
                            }
                        } else {
                            echo max(count($_SESSION['user_cart']), 0);  // Tránh số lượng giỏ hàng âm
                        }
                        ?>
                    </i>
                </div>

                <div class="navbar-form navbar-right searchbox-desktop">
                    <div class="form-group">
                        <input type="text" class="form-control" placeholder="Bạn tìm gì?" id='srch-val'>
                    </div>
                    <span class="btn btn-default" onclick="ajax_search()">Tìm</span>
                </div>
            </div><!-- /.navbar-collapse -->
        </div>
    </nav>