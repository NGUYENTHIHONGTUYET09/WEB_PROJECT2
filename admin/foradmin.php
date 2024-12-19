<?php

session_start();
if (!isset($_SESSION['admin']) || isset($_SESSION['user']) || isset($_SESSION['user'])) {
    echo "<script>window.location.replace('../index.php');</script>";
}
$conn = mysqli_connect('localhost', 'root', '280704', 'qlbh') or die('Không thể kết nối!');
$sql = "SELECT * FROM sanpham";
$result = mysqli_query($conn, $sql);
$_SESSION['total'] = mysqli_num_rows($result);

$sql = "SELECT * FROM giaodich";
$result = mysqli_query($conn, $sql);
$_SESSION['gd_all'] = mysqli_num_rows($result);

$sql = "SELECT * FROM giaodich WHERE tinhtrang = 1";
$result = mysqli_query($conn, $sql);
$_SESSION['gd_dagd'] = mysqli_num_rows($result);

$sql = "SELECT * FROM giaodich WHERE tinhtrang = 0";
$result = mysqli_query($conn, $sql);
$_SESSION['gd_chua'] = mysqli_num_rows($result);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Trang quản trị admin</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" type="text/css" href="libs/style.css">
    <link rel="stylesheet" href="../libs/bootstrap/css/bootstrap.css">
    <link rel="stylesheet" href="style.css">
    <script type="text/javascript" src="script.js"></script>
    <script src="../libs/jquery/jquery-latest.js"></script>
    <script src="../libs/bootstrap/js/bootstrap.min.js"></script>
    <script src="ajax.js"></script>
    <script src="js/jquery.min.js"></script>


    <script type="text/javascript">
    $(document).ready(function() {
        // Ẩn các phần tử ban đầu
        $('#add-pr').hide();
        $('#add-dm').hide();
        $('#add-admin-area').hide();
        $('#sua_sp-area').hide();
        $('#tbl-sanpham').hide();
        $('#tbl-sanpham-combobox').hide();




        // Hiệu ứng mượt mà cho các nút thêm sản phẩm
        $('#addspbtn').click(function() {
            $('#add-pr').slideToggle(300); // Dùng slideToggle thay vì toggle
            $('#tbl-sanpham-list').slideToggle(300);
            $('#loadmorebtn').toggle(300); // Giữ nguyên nếu cần nút tải thêm

        });

        // Hiệu ứng mượt mà cho các phần thêm danh mục sản phẩm
        $('#adddmbtn').click(function() {
            $('#add-dm').show(300);
        });

        /// Chức năng tìm kiếm
        $('#btn-info').click(function() {
            var textSearch = $('#src-v').val(); // Lấy giá trị tìm kiếm
            if (textSearch) {
                $.get('search.php', {
                    textSearch: textSearch
                }, function(response) {
                    $('#product-list-timkiem').html(
                        response); // Cập nhật kết quả tìm kiếm vào table
                    $('#tbl-sanpham').show(); // Hiển thị bảng tìm kiếm
                    $('#tbl-sanpham-combobox').hide(); // Ẩn bảng danh mục
                    $('#loadmorebtn').hide(); // Ẩn nút tải thêm
                    $('#tbl-sanpham-list').hide();
                });
            } else {
                $('#product-list-timkiem').html(
                    '<tr><td colspan="4" class="text-center">Vui lòng nhập từ khóa tìm kiếm</td></tr>'
                );
            }
        });

        // Xử lý chọn danh mục sản phẩm
        $('#category-select').change(function() {
            var categoryId = $(this).val(); // Lấy giá trị danh mục đã chọn
            if (categoryId) {
                $.get('fetch_products_by_category.php', {
                    category_id: categoryId
                }, function(response) {
                    $('#product-list-combobox').html(
                        response); // Hiển thị kết quả sản phẩm theo danh mục
                    $('#tbl-sanpham-combobox').show(); // Hiển thị bảng danh mục
                    $('#tbl-sanpham').hide(); // Ẩn bảng tìm kiếm
                    $('#loadmorebtn').hide(); // Ẩn nút tải thêm
                    $('#tbl-sanpham-list').hide();
                });
            } else {
                $('#product-list-combobox').html(''); // Xóa danh sách sản phẩm nếu không chọn danh mục
                $('#tbl-sanpham-combobox').hide(); // Ẩn bảng danh mục khi không chọn
            }
        });

        // Đảm bảo khi trang tải lại hoặc không có tìm kiếm hay chọn danh mục, các bảng đều được ẩn
        $(document).ready(function() {
            $('#tbl-sanpham').hide();
            $('#tbl-sanpham-combobox').hide();
        });


        // Nút tải thêm sản phẩm
        $('#loadmorebtn').click(function() {
            var current = 0; // Thiết lập chỉ số ban đầu nếu cần
            load_more(current, 'product-list'); // Gọi hàm tải thêm
        });




    });

    function load_more(current, where) {
        var fname = 'load_more';
        var x = current + 1;
        $('#loadmorebtn').attr('onclick', 'load_more(' + x + ', `' + where + '`)');
        $.ajax({
            url: "for-ajax.php",
            type: "get",
            dataType: "text",
            data: {
                fname,
                current
            },
            success: function(result) {
                if (result.indexOf('Đã hết mục để hiển thị') != -1) {
                    alert('Đã hết mục để hiển thị!');
                    return;
                }
                $('#' + where).append(result); // Thêm kết quả vào phần tử đích
            }
        });
    }
    </script>
</head>

<body>
    <!-- Thêm vào phần trên của trang HTML -->

    <div class="container-fluid">
        <h2>Clothes - Trang quản trị dành cho admin</h2>
        <h3 id="big-error"></h3>
        <div role="tabpanel">
            <!-- Nav tabs -->
            <ul class="nav nav-tabs" role="tablist" id="admin-tabs">
                <li role="presentation" class="active">
                    <a href="#sanpham" aria-controls="sanpham" role="tab" data-toggle="tab">Sản phẩm</a>
                </li>
                <li role="presentation">
                    <a href="#thanhvien" aria-controls="thanhvien" role="tab" data-toggle="tab">Thành viên</a>
                </li>
                <li role="presentation">
                    <a href="#giaodich" aria-controls="giaodich" role="tab" data-toggle="tab">Giao dịch</a>
                </li>
                <li role="presentation">
                    <a href="#danhmuc" aria-controls="danhmuc" role="tab" data-toggle="tab">Danh mục</a>
                </li>
            </ul>

            <!-- Tab panes -->
            <div class="tab-content">
                <div role="tabpanel" class="tab-pane active" id="sanpham">
                    <div class="container-fluid">
                        <h3 class="text-center text-primary mb-4">Quản lý sản phẩm</h3>
                        <!-- Nút thêm sản phẩm và xem thống kê -->
                        <div class="d-flex justify-content-end mb-3 gap-2">
                            <button class="btn btn-success" id="addspbtn">
                                <i class="glyphicon glyphicon-plus"></i> Thêm sản phẩm
                            </button>
                            <button class="btn btn-success" onclick="window.location.href='index.php';">
                                <i class="glyphicon glyphicon-plus"></i> Xem thống kê
                            </button>
                        </div>

                        <div class="d-flex align-items-center">
                            <!-- Form tìm kiếm sản phẩm -->
                            <div id="custom-search-input" class="mr-3 mb-0">
                                <div class="input-group col-md-12" style="background-color: white;">
                                    <input type="text" class="form-control input-lg" placeholder="Bạn tìm gì?"
                                        id='src-v' />
                                    <span class="input-group-btn">
                                        <button class="btn btn-info btn-lg" type="button" id='btn-info'>
                                            <i class="glyphicon glyphicon-search"></i>
                                        </button>
                                    </span>
                                </div>
                            </div>

                            <!-- Combobox hiển thị danh mục sản phẩm -->
                            <div id="category-dropdown" class="mb-0">
                                <label for="category-select">Chọn loại sản phẩm:</label>
                                <select id="category-select" class="form-control" name="category_id"
                                    style="font-size: 1.5rem; height: 5rem;">
                                    <option value="">Tất cả các loại sản phẩm</option>
                                    <?php
                                    // Kết nối cơ sở dữ liệu và lấy danh mục sản phẩm
                                    require_once 'function.php';
                                    $conn = connect();
                                    mysqli_set_charset($conn, 'utf8');
                                    $sql = "SELECT madm, tendm FROM danhmucsp"; // Lấy mã và tên danh mục
                                    $result = mysqli_query($conn, $sql);
                                    while ($row = mysqli_fetch_assoc($result)) {
                                        $category_id = htmlspecialchars($row['madm']);
                                        $category_name = htmlspecialchars($row['tendm']);
                                        echo "<option value='$category_id'>$category_name</option>";
                                    }
                                    disconnect($conn);
                                    ?>
                                </select>
                            </div>
                        </div>

                        <!-- Danh sách sản phẩm -->
                        <div class="table-responsive-combobox">
                            <table class="table table-hover table-striped table-bordered" id="tbl-sanpham-combobox">
                                <thead>
                                    <tr>
                                        <th>Tên sản phẩm</th>
                                        <th>Giá</th>
                                        <th>Hình ảnh</th>
                                        <th>Hành động</th>
                                    </tr>
                                </thead>
                                <tbody id="product-list-combobox">
                                    <!-- Sản phẩm sẽ được hiển thị ở đây -->
                                </tbody>
                            </table>
                        </div>



                        <!-- Danh sách sản phẩm -->
                        <div class="table-responsive-timkiem">
                            <table class="table table-hover table-striped table-bordered" id="tbl-sanpham">
                                <thead>
                                    <tr>
                                        <th>Tên sản phẩm</th>
                                        <th>Giá</th>
                                        <th>Hình ảnh</th>
                                        <th>Hành động</th>
                                    </tr>
                                </thead>
                                <tbody id="product-list-timkiem">
                                    <!-- Sản phẩm sẽ được hiển thị ở đây -->
                                </tbody>
                            </table>
                        </div>


                        <!-- Form sửa sản phẩm -->
                        <div id='sua_sp-area' class="card shadow-sm p-4 mb-4 d-none">
                            <h4 class="text-center text-warning mb-4">Sửa Sản Phẩm</h4>
                            <div class="mb-3">
                                <label for="tensp-edit" class="form-label">Tên sản phẩm</label>
                                <input type="text" id='tensp-edit' class="form-control">
                            </div>
                            <div class="mb-3">
                                <label for="gia-edit" class="form-label">Giá</label>
                                <input type="text" id='gia-edit' class="form-control">
                            </div>
                            <div class="mb-3">
                                <label for="khuyenmai-edit" class="form-label">Khuyến mãi</label>
                                <input type="text" id='khuyenmai-edit' class="form-control">
                            </div>
                            <div class="d-flex justify-content-end">
                                <button class="btn btn-success me-2" id="edit_sp_btn">Xong</button>
                                <button class="btn btn-secondary" onclick="$('#sua_sp-area').hide(300)">Hủy</button>
                            </div>
                        </div>


                        <!-- Danh sách sản phẩm -->
                        <div class="table-responsive">
                            <table class="table table-hover table-striped table-bordered" id="tbl-sanpham-list">
                                <?php require_once 'function.php';
                                product_list(); ?>
                            </table>
                        </div>

                        <!-- Form thêm sản phẩm -->
                        <div id="add-pr" class="card shadow-sm p-4 mb-4 d-none">
                            <h3 class="text-center text-primary mb-4">Thêm sản phẩm</h3>
                            <form method="POST" action="add-product.post.php" id="add-product-form"
                                enctype="multipart/form-data">
                                <div class="row mb-3">
                                    <label for="tensp" class="col-md-4 col-form-label">Tên sản phẩm</label>
                                    <div class="col-md-8">
                                        <input type="text" class="form-control" id="tensp" name="tensp"
                                            placeholder="Nhập tên sản phẩm">
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <label for="gia" class="col-md-4 col-form-label">Giá</label>
                                    <div class="col-md-8">
                                        <input type="number" class="form-control" id="gia" name="gia"
                                            placeholder="Nhập giá sản phẩm">
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <label for="danhcho" class="col-md-4 col-form-label">Dành cho</label>
                                    <div class="col-md-8">
                                        <input type="text" class="form-control" id="danhcho" name="danhcho"
                                            placeholder="Dành cho nam/nữ">
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <label for="khuyenmai" class="col-md-4 col-form-label">Khuyến mãi</label>
                                    <div class="col-md-8">
                                        <input type="text" class="form-control" id="khuyenmai" name="khuyenmai"
                                            placeholder="Thông tin khuyến mãi">
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <label for="chatlieu" class="col-md-4 col-form-label">Chất liệu</label>
                                    <div class="col-md-8">
                                        <input type="text" class="form-control" id="chatlieu" name="chatlieu"
                                            placeholder="Bạc, Inox, Vàng,...">
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <label for="mau" class="col-md-4 col-form-label">Màu sắc</label>
                                    <div class="col-md-8">
                                        <input type="text" class="form-control" id="mau" name="mau"
                                            placeholder="Đen, trắng,...">
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <label for="madm" class="col-md-4 col-form-label">Loại</label>
                                    <div class="col-md-8">
                                        <?php displayCategorySelect(); ?>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <label for="anhchinh" class="col-md-4 col-form-label">Hình ảnh</label>
                                    <div class="col-md-8">
                                        <input type="file" class="form-control" id="anhchinh" name="anhchinh"
                                            accept="image/*">
                                    </div>
                                </div>
                                <div class="text-center">
                                    <button type="submit" class="btn btn-success">Thêm sản phẩm</button>
                                    <button type="button" class="btn btn-danger"
                                        onclick="$('#add-pr').hide(300);$('#tbl-sanpham-list').show(300);">Hủy</button>
                                </div>

                            </form>
                        </div>
                    </div>
                </div>




                <!-- THÀNH VIÊN -->
                <div role="tabpanel" class="tab-pane" id="thanhvien">
                    <div class="container-fluid py-4">
                        <h3 class="text-primary text-center mb-4">Danh sách thành viên</h3>
                        <div class="d-flex justify-content-end mb-3">
                            <button class="btn btn-success" id="add-admin-btn"
                                onclick="$('#add-admin-area').toggle(300);">
                                <i class="glyphicon glyphicon-plus"></i> Thêm admin

                        </div>
                        <div id="add-admin-area" class="card shadow-sm p-4 mb-4 d-none">
                            <h4 class="text-primary mb-4">Thêm Admin</h4>
                            <form id="add-admin-form" class="row g-3">
                                <div class="col-md-4">
                                    <label for="admin-name" class="form-label">Tên</label>
                                    <input type="text" class="form-control" id="admin-name" placeholder="Nhập tên"
                                        required>
                                </div>
                                <div class="col-md-4">
                                    <label for="admin-username" class="form-label">Tên tài khoản</label>
                                    <input type="text" class="form-control" id="admin-username"
                                        placeholder="Nhập tên tài khoản" required>
                                </div>
                                <div class="col-md-4">
                                    <label for="admin-password" class="form-label">Mật khẩu</label>
                                    <input type="password" class="form-control" id="admin-password"
                                        placeholder="Nhập mật khẩu" required>
                                </div>
                                <div class="col-md-4">
                                    <label for="admin-address" class="form-label">Địa chỉ</label>
                                    <input type="text" class="form-control" id="admin-address"
                                        placeholder="Nhập địa chỉ">
                                </div>
                                <div class="col-md-4">
                                    <label for="admin-phonenumber" class="form-label">Số điện thoại</label>
                                    <input type="text" class="form-control" id="admin-phonenumber"
                                        placeholder="Nhập số điện thoại">
                                </div>
                                <div class="col-md-4">
                                    <label for="admin-email" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="admin-email" placeholder="Nhập email">
                                </div>
                                <div class="col-12 text-center mt-4">
                                    <button type="button" class="btn btn-success" onclick="them_admin()">Tạo</button>
                                    <button type="button" class="btn btn-danger"
                                        onclick="$('#add-admin-area').toggleClass('d-none')">Hủy</button>
                                </div>
                            </form>

                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover table-bordered table-striped align-middle"
                                id="tbl-thanhvien-list">
                                <thead class="table-dark">

                                </thead>
                                <tbody>
                                    <?php require_once 'function.php';
                                    member_list(); ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>




                <!-- GIAO DỊCH -->
                <div role="tabpanel" class="tab-pane" id="giaodich">
                    <div class="container-fluid py-4">
                        <h3 class="text-center text-primary mb-4">Danh Sách Giao Dịch</h3>
                        <div class="d-flex justify-content-center mb-3">
                            <button class="btn btn-info mx-2" onclick="list_chuagh()">Chưa hoàn tất</button>
                            <button class="btn btn-info mx-2" onclick="list_dagh()">Đã hoàn tất</button>
                            <button class="btn btn-info mx-2" onclick="list_tatcagh()">Tất cả</button>
                        </div>
                        <h5 class="text-center">
                            <b>Sắp xếp theo: </b><span id="loai_gd" class="text-success">Chưa hoàn tất</span>
                        </h5>
                        <div class="table-responsive mt-4">
                            <table class="table table-hover table-bordered table-striped align-middle"
                                id="tbl-giaodich-list">
                                <thead class="table-dark">
                                </thead>
                                <tbody>
                                    <?php require_once 'function.php';
                                    exchange_list(); ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>




                <!-- DANH MỤC -->
                <div role="tabpanel" class="tab-pane" id="danhmuc">
                    <div class="container py-4">
                        <h3 class="text-center text-primary mb-4">Danh Mục Sản Phẩm</h3>

                        <!-- Form Thêm Danh Mục -->
                        <div id="add-dm" class="card shadow-sm p-4 mb-4">
                            <h4 class="text-center text-success mb-3">Thêm Danh Mục</h4>
                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <label for="tendm" class="form-label">Tên danh mục:</label>
                                </div>
                                <div class="col-md-8">
                                    <input type="text" class="form-control" id="tendm" placeholder="Nhập tên danh mục">
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <label for="xuatsu" class="form-label">Xuất xứ:</label>
                                </div>
                                <div class="col-md-8">
                                    <input type="text" class="form-control" id="xuatsu" placeholder="Nhập xuất xứ">
                                </div>
                            </div>
                            <div class="text-center">
                                <button class="btn btn-success" onclick="them_dm()">Thêm</button>
                                <button class="btn btn-danger" onclick="$('#add-dm').toggle(300);">Hủy</button>
                            </div>
                        </div>

                        <!-- Danh Sách Danh Mục -->
                        <div class="table-responsive">
                            <table class="table table-hover table-bordered table-striped align-middle">
                                <thead class="table-dark">
                                </thead>
                                <tbody>
                                    <?php require_once 'function.php';
                                    type_list(); ?>
                                </tbody>
                            </table>
                        </div>

                        <!-- Nút Thêm Danh Mục -->
                        <div class="text-center mt-4">
                            <button id="adddmbtn" class="btn btn-primary" onclick="$('#add-dm').toggle(300);">
                                <i class="glyphicon glyphicon-plus"></i> Thêm Danh Mục
                            </button>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</body>

</html>