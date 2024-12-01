<?php 

session_start();
if(!isset($_SESSION['admin']) || isset($_SESSION['user']) || isset($_SESSION['user'])){
	echo "<script>window.location.replace('../index.php');</script>";
}
$conn = mysqli_connect('localhost','root','280704','qlbh') or die('Không thể kết nối!');
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
    <script type="text/javascript">
    $(document).ready(function() {
        $('#add-pr').hide();
        $('#add-dm').hide();
        $('#add-admin-area').hide();
        $('#sua_sp-area').hide();
        $('#addspbtn').click(function() {
            $('#add-pr').toggle(300);
            $('#tbl-sanpham-list').toggle(300);
            $('#loadmorebtn').toggle(300);
        });
        $('#adddmbtn').click(function() {
            $('#add-dm').toggle(300);
        });
        $('.xong').click(function() {
            $(this).closest('tr').children("td:nth-child(2)").html(
                '<h4 class="label label-success">Đã giao hàng</h4>');
            $(this).remove();
        });
        $('#add-admin-btn').click(function() {
            $('#add-admin-area').toggle(300);
        });
    });

    function load_more(current, where) {
        var fname = 'load_more';
        var x = current + 1;
        $('#loadmorebtn').attr('onclick', 'load_more(' + x + ',`' + where + '`)');
        $.ajax({
            url: "for-ajax.php",
            type: "get",
            dataType: "text",
            data: {
                fname,
                current
            },
            success: function(result) {
                if (result.search('Đã hết mục để hiển thị') != -1) {
                    alert('Đã hết mục để hiển thị!');
                    return;
                }
                $('#' + where).append(result);
            }
        });
    }

    function load_more_gd(current, where, stt) {
        var fname = 'load_more_gd';
        var x = current + 1;
        $('#loadmorebtngd').attr('onclick', 'load_more_gd(' + x + ',`' + where + '`,`' + stt + '`)');
        $.ajax({
            url: "for-ajax.php",
            type: "get",
            dataType: "text",
            data: {
                fname,
                current,
                stt
            },
            success: function(result) {
                if (result.search('Đã hết mục để hiển thị') != -1) {
                    alert("Đã hết mục để hiển thị!");
                    return;
                }
                $('#' + where).append(result);
            }
        });
    }
    </script>
</head>

<body>
    <div class="container-fluid">
        <h2>Clothes - Trang quản trị dành cho admin</h2>
        <h3 id="big-error"></h3>
        <div role="tabpanel">
            <!-- Nav tabs -->
            <ul class="nav nav-tabs" role="tablist">
                <li role="presentation" class="active">
                    <a href="#sanpham" aria-controls="home" role="tab" data-toggle="tab">Sản phẩm</a>
                </li>
                <li role="presentation">
                    <a href="#thanhvien" aria-controls="tab" role="tab" data-toggle="tab">Thành viên</a>
                </li>
                <li role="presentation">
                    <a href="#giaodich" aria-controls="tab" role="tab" data-toggle="tab">Giao dịch</a>
                </li>
                <li role="presentation">
                    <a href="#danhmuc" aria-controls="tab" role="tab" data-toggle="tab">Danh mục</a>
                </li>
            </ul>

            <!-- Tab panes -->
            <div class="tab-content">
                <div role="tabpanel" class="tab-pane active" id="sanpham">
                    <div class="container-fluid">
                        <h3>Danh sách sản phẩm</h3>
                        <span class="glyphicon glyphicon-plus btn btn-success pull-right" id="addspbtn"
                            style="z-index: 2;"></span>
                        <div class="container-fluid">
                            <div id='sua_sp-area'>
                                <h4>Sửa Sản Phẩm</h4>
                                <label>Tên sản phẩm</label>
                                <input type="text" id='tensp-edit' class="form-control">
                                <label>Giá</label>
                                <input type="text" id='gia-edit' class="form-control">
                                <label>Khuyến mãi</label>
                                <input type="text" id='khuyenmai-edit' class="form-control">
                                <span class="btn btn-success" id="edit_sp_btn">Xong</span>
                                <span class="btn btn-default" onclick="$('#sua_sp-area').hide(300)">Hủy</span>
                            </div>

                            <table class="table table-hover" id="tbl-sanpham-list">
                                <?php require_once 'function.php'; product_list(); ?>
                                <div class="container-fluid text-center lmbtnctn">
                                    <button onclick="load_more(0,'body-sp-list','sp')" id="loadmorebtn">Load
                                        more</button>
                                </div>
                            </table>

                        </div>
                        <!-- VÙNG LÀM VIỆC -->
                        <div class="work-space">
                            <!-- Thêm sản phẩm -->
                            <div id="add-pr" class="card shadow-sm p-4 mb-4">
                                <h3 class="text-center text-primary mb-4">Thêm sản phẩm</h3>
                                <form method="POST" action="add-product.post.php" id="add-product-form"
                                    enctype="multipart/form-data">
                                    <div class="row mb-3">
                                        <div class="col-md-4">
                                            <label for="tensp" class="form-label">Tên sản phẩm</label>
                                        </div>
                                        <div class="col-md-8">
                                            <input type="text" class="form-control" id="tensp" name="tensp"
                                                placeholder="Nhập tên sản phẩm">
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-md-4">
                                            <label for="gia" class="form-label">Giá</label>
                                        </div>
                                        <div class="col-md-8">
                                            <input type="number" class="form-control" id="gia" name="gia"
                                                placeholder="Nhập giá sản phẩm">
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-md-4">
                                            <label for="danhcho" class="form-label">Dành cho</label>
                                        </div>
                                        <div class="col-md-8">
                                            <input type="text" class="form-control" id="danhcho" name="danhcho"
                                                placeholder="Dành cho nam/nữ">
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-md-4">
                                            <label for="khuyenmai" class="form-label">Khuyến mãi</label>
                                        </div>
                                        <div class="col-md-8">
                                            <input type="text" class="form-control" id="khuyenmai" name="khuyenmai"
                                                placeholder="Thông tin khuyến mãi">
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-md-4">
                                            <label for="chatlieu" class="form-label">Chất liệu</label>
                                        </div>
                                        <div class="col-md-8">
                                            <input type="text" class="form-control" id="chatlieu" name="chatlieu"
                                                placeholder="Bạc, Inox, Vàng,...">
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-md-4">
                                            <label for="mau" class="form-label">Màu sắc</label>
                                        </div>
                                        <div class="col-md-8">
                                            <input type="text" class="form-control" id="mau" name="mau"
                                                placeholder="Đen, trắng,...">
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-md-4">
                                            <label for="madm" class="form-label">Loại</label>
                                        </div>
                                        <div class="col-md-8">
                                            <?php
                                            // Gọi hàm displayCategorySelect() để hiển thị combobox danh mục sản phẩm
                                            displayCategorySelect();
                                            ?>
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-md-4">
                                            <label for="anhchinh" class="form-label">Hình ảnh</label>
                                        </div>
                                        <div class="col-md-8">
                                            <input type="file" class="form-control" id="anhchinh" name="anhchinh"
                                                accept="image/*">
                                        </div>
                                    </div>
                                    <div class="text-center">
                                        <button type="submit" name="submit">Thêm sản phẩm</button>
                                        <button type="button" class="btn btn-danger"
                                            onclick="$('#add-pr').toggle(300);$('#tbl-sanpham-list').toggle(300);">Hủy</button>
                                        <div id="sp_error" class="text-danger mt-2"></div>
                                    </div>
                                </form>

                            </div>
                        </div>


                    </div>
                </div>



                <!-- THÀNH VIÊN -->
                <div role="tabpanel" class="tab-pane" id="thanhvien">
                    <h3>Danh sách thành viên</h3>
                    <span class="btn btn-success" id="add-admin-btn"><i class="glyphicon glyphicon-plus"></i> Thêm
                        admin</span>
                    <div id="add-admin-area" class="form-inline">
                        <h3>Thêm Admin</h3>
                        <label>Tên:</label>
                        <input type="text" class="form-control" id="admin-name">

                        <label>Tên tài khoản:</label>
                        <input type="text" class="form-control" id="admin-username">

                        <label>Mật khẩu:</label>
                        <input type="password" class="form-control" id="admin-password">

                        <span class="btn btn-success" onclick="them_admin()">Tạo</span>
                        <span class="btn btn-default" onclick="$('#add-admin-area').toggle(300)">Hủy</span><br>
                    </div>
                    <div class="container-fluid">
                        <table class="table table-hover" id="tbl-thanhvien-list">
                            <?php require_once 'function.php'; member_list(); ?>
                        </table>
                    </div>
                </div>



                <!-- GIAO DỊCH -->
                <div role="tabpanel" class="tab-pane" id="giaodich">
                    <h3>DANH SÁCH GIAO DỊCH</h3>
                    <span class="btn btn-info" onclick="list_chuagh()">Chưa giao hàng</span>
                    <span class="btn btn-info" onclick="list_dagh()">Đã giao hàng</span>
                    <span class="btn btn-info" onclick="list_tatcagh()">Tất cả</span>
                    <h4><b>Sắp xếp theo: </b><span id="loai_gd">Chưa giao hàng</span></h4>
                    <div class="container-fluid" style="padding-bottom: 20px;">
                        <table class="table table-hover" id="tbl-giaodich-list">
                            <?php require_once 'function.php'; exchange_list(); ?>
                        </table>
                    </div>
                </div>



                <!-- DANH MỤC -->
                <div role="tabpanel" class="tab-pane" id="danhmuc">
                    <h3>DANH MỤC SẢN PHẨM</h3>
                    <div class="container">
                        <div class="form-inline" id="add-dm">
                            <h3>Thêm Danh Mục</h3>
                            <label>Tên danh mục:</label>
                            <input type="text" class="form-control" id="tendm">

                            <label>Xuất xứ</label>
                            <input type="text" class="form-control" id="xuatsu">

                            <span class="btn btn-success" onclick="them_dm()">Thêm</span>
                            <span class="btn btn-default" onclick="$('#add-dm').toggle(300);">Hủy</span>
                        </div>

                        <table class="table table-hover">
                            <?php require_once 'function.php'; type_list(); ?>
                            <h3 class="glyphicon glyphicon-plus btn btn-success pull-right" id="adddmbtn"></h3>
                        </table>

                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>