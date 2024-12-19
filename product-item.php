<?php
// Kết nối cơ sở dữ liệu
$conn = mysqli_connect('localhost', 'root', '280704', 'qlbh'); // Thay 'qlbh' bằng tên cơ sở dữ liệu của bạn
if (!$conn) {
    die("Kết nối thất bại: " . mysqli_connect_error());
}

// Lấy thông tin sản phẩm từ cơ sở dữ liệu
$sql = "SELECT * FROM sanpham"; // Kiểm tra lại tên bảng 'sanpham'
$result = mysqli_query($conn, $sql);

// Kiểm tra và hiển thị sản phẩm
if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
?>
<div class="product-container" onclick="hien_sanpham('<?php echo $row['masp']; ?>')">
    <a href="sanpham.php?masp=<?php echo $row['masp']; ?>" data-toggle="modal" data-target="#modal-id">
        <img src="<?php echo $row['anhchinh']; ?>" class="product-img" alt="<?php echo $row['tensp']; ?>">
        <img src="images/km_logo.png" class="km-logo" alt="Khuyến mãi">

        <div class="product-detail">
            <p>✔ <?php echo $row['chatlieu']; ?></p>
            <span>Khuyến mãi</span>
            <p>✔ Giảm ngay <?php echo $row['khuyenmai']; ?>&#37;</p>
        </div>

        <div class="product-info">
            <h4><b><?php echo $row['tensp']; ?></b></h4>
            <i><?php echo $row['xuatsu']; ?></i><br>
            <b class="price">Giá: <?php echo number_format($row['gia'], 0, ',', '.'); ?> VND</b>
        </div>

        <div class="buy">
            <a class="btn btn-default btn-lg unlike-container">
                <i class="glyphicon glyphicon-heart unlike"></i>
            </a>

            <a class="btn btn-primary btn-lg cart-container 
                        <?php echo ($_SESSION['rights'] == "default" && in_array($row['masp'], $_SESSION['client_cart'])) ||
                            ($_SESSION['rights'] != "default" && in_array($row['masp'], $_SESSION['user_cart']))
                            ? 'cart-ordered' : ''; ?>" data-masp="<?php echo $row['masp']; ?>">
                <i title="Thêm vào giỏ hàng" class="glyphicon glyphicon-shopping-cart cart-item"></i>
            </a>

            <a class="btn btn-success btn-lg" href="order.php?masp=<?php echo $row['masp']; ?>">Mua ngay</a>
        </div>
    </a>
</div>
<?php
    }
} else {
    echo "Không có sản phẩm nào.";
}

// Đóng kết nối
mysqli_close($conn);
?>

<script src="cart.js"></script> <!-- Đường dẫn tới file cart.js -->