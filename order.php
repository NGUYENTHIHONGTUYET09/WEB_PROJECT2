<?php 
require_once 'layout/second_header.php';
require_once 'backend-index.php';
$masp = $q = "";
$_SESSION['cost'] = array(); // Khởi tạo là một mảng

if(isset($_GET['masp'])){
    $masp = $_GET['masp'];
    $_SESSION['buynow'] = $masp;
}

if(isset($_GET['q'])){
    $q = $_GET['q'];
    if($q == 'multi'){
        if($_SESSION['rights'] == "default"){
            if(isset($_SESSION['client_cart']) && count($_SESSION['client_cart']) > 0){
                $tmpArr = $_SESSION['client_cart'];
                $x = '('.implode(',',$tmpArr).')';
                $sql = "SELECT * FROM sanpham WHERE masp in ".$x."";
            } else {
                echo "<script>alert('Giỏ hàng trống!')</script>";
                return 0;
            }
        } else {
            $tmpArr = $_SESSION['user_cart'];
            array_shift($tmpArr);
            $x = '('.implode(',',$tmpArr).')';
            $sql = "SELECT * FROM sanpham WHERE masp in ".$x."";
        }
    } elseif($q == 'buylikepr'){ // Đã sửa điều kiện từ '=' thành '=='
        $tmpArr = $_SESSION['like'];
        array_shift($tmpArr);
        $x = '('.implode(',',$tmpArr).')';
        $sql = "SELECT * FROM sanpham WHERE masp in ".$x."";
    } else {
        $_SESSION['buynow'] = $masp;
    }
} else {
    $sql = "SELECT * FROM sanpham WHERE masp = '".$masp."'";
}

$conn = connect();
mysqli_set_charset($conn, 'utf8');

$result = mysqli_query($conn, $sql);
?>
<script type="text/javascript">
window.onload = function() {
    tinh_tien()
}
</script>
<form action="giaodich.php" method="POST" role="form">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <div class="container-fluid form" style="margin-top: -23px; padding: 20px">
        <div class="row">
            <div class="col-lg-3"></div>
            <div class="col-lg-6">
                <div id="countdown" style="font-size: 24px; color: red; margin-bottom: 20px;">
                    <i class="fas fa-clock"></i> <span id="timer"></span> <!-- Thêm biểu tượng đồng hồ -->
                </div>
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>ẢNH</th>
                            <th>TÊN SẢN PHẨM</th>
                            <th>ĐƠN GIÁ</th>
                            <th>SỐ LƯỢNG</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        // Khởi tạo mảng nếu chưa tồn tại
                        if (!isset($_SESSION['product_names'])) {
                            $_SESSION['product_names'] = [];
                        }

                        // Trong vòng lặp lưu tên sản phẩm vào session
                        while ($row = mysqli_fetch_assoc($result)) {
                            $_SESSION['product_names'][] = $row['tensp']; 
                        ?>
                        <tr>
                            <td><img src="<?php echo $row['anhchinh'] ?>" style="width: 70px"></td>
                            <td><?php echo $row['tensp'] ?></td>
                            <td class="cost" data-val="<?php echo $row['gia'] ?>">
                                <?php echo $row['gia']; $_SESSION['cost'][] = $row['gia']; ?>
                            </td>
                            <td width="30px"><input type="number" name="sl[]" value='1' min="0" onchange="tinh_tien()">
                            </td>
                        </tr>
                        <?php } ?>

                        <tr style="color: green; font-size: 18px;">
                            <th colspan="2">Tổng tiền</th>
                            <th id="tong_tien"></th>
                            <th>VND</th>
                        </tr>
                    </tbody>
                </table>
                <legend>Thông tin giao hàng</legend>
                <p class="errorMes">Giao hàng tận nhà chỉ áp dụng ở TP HCM</p>
                <div class="form-group">
                    <label for="">Tên: </label>
                    <input type="text" class="form-control" id="s_ten" name="ten" value="<?php 
                        if($_SESSION['rights'] == 'user'){
                            echo $_SESSION['user']['ten'];
                        }
                    ?>">
                </div>
                <div class="form-group">
                    <label for="">Quận: </label>
                    <select class="form-control" name="quan" id="s_quan">
                        <option value="q1">Quận 1</option>
                        <option value="q2">Quận 2</option>
                        <option value="q3">Quận 3</option>
                        <option value="q4">Quận 4</option>
                        <option value="q5">Quận 5</option>
                        <option value="q6">Quận 6</option>
                        <option value="q7">Quận 7</option>
                        <option value="q8">Quận 8</option>
                        <option value="q9">Quận 9</option>
                        <option value="q10">Quận 10</option>
                        <option value="q11">Quận 11</option>
                        <option value="q12">Quận 12</option>
                        <option value="qtd">Quận Thủ Đức</option>
                        <option value="qbt">Quận Bình Thạnh</option>
                        <option value="qgv">Quận Gò Vấp</option>
                        <option value="qtb">Quận Tân Bình</option>
                        <option value="qbt">Quận Bình Tân</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="">Địa chỉ: </label>
                    <input type="text" class="form-control" name="dc" id="s_dc" value="<?php 
                        if($_SESSION['rights'] == 'user'){
                            echo $_SESSION['user']['diachi'];
                        }
                    ?>">
                </div>
                <div class="form-group">
                    <label for="">Số điện thoại: </label>
                    <input type="text" class="form-control" name="sodt" id="s_sdt" value="<?php 
        if($_SESSION['rights'] == 'user'){
            echo $_SESSION['user']['sodt'];
        }
    ?>">
                    <small id="error_sdt" class="form-text text-danger" style="display:none;">Số điện thoại không hợp
                        lệ. Vui lòng nhập lại!</small>
                </div>




                <!-- Thêm trường Email -->
                <div class="form-group">
                    <label for="">Email: </label>
                    <input type="email" class="form-control" name="email" id="s_email" value="<?php 
                        if($_SESSION['rights'] == 'user'){
                            echo $_SESSION['user']['email'];
                        }
                    ?>">
                </div>

                <!-- Thêm trường Phương thức thanh toán -->
                <div class="form-group">
                    <label for="">Phương thức thanh toán: </label>
                    <select class="form-control" name="payment_method" id="s_payment_method">
                        <option value="COD">COD (Thanh toán khi nhận hàng)</option>
                    </select>
                </div>

                <button id="submitButton" class="btn btn-primary" type="button">Đặt Hàng</button><br><br>
            </div>


        </div>
    </div>
</form>

<script>
var timeLeft = 300; // 5 phút
function updateCountdown() {
    var minutes = Math.floor(timeLeft / 60);
    var seconds = timeLeft % 60;

    seconds = seconds < 10 ? '0' + seconds : seconds;

    document.getElementById('timer').innerHTML = minutes + ":" + seconds; // Cập nhật vào span

    if (timeLeft > 0) {
        timeLeft--;
    } else {
        clearInterval(countdownInterval);
        document.getElementById('timer').innerHTML = "Thời gian đã hết!";

        // Chuyển hướng đến trang khác
        window.location.href = "index.php"; // Thay đổi "trang_moi.php" thành trang bạn muốn chuyển đến
    }
}

var countdownInterval = setInterval(updateCountdown, 1000);
</script>

?>
<script type="text/javascript">
function tinh_tien() {
    var query = 'tinh_tien';
    var sl = [];
    var sum = 0;
    $('input[name="sl[]"]').each(function() {
        sl.push($(this).val());
    });
    var array = [];
    $(".cost").each(function() {
        array.push($(this).data("val"));
    });
    for (var i = 0; i < sl.length; i++) {
        var tmp = array[i].replace(/ /g, '');
        sum += Number(tmp) * sl[i];
    }
    sum = sum.toString().replace(/\B(?=(\d{3})+(?!\d))/g, " ");
    $('#tong_tien').html(sum);
}

window.onload = function() {
    tinh_tien()
}
</script>

<script>
// Hàm kiểm tra số điện thoại
function validatePhoneNumber() {
    var phoneNumber = document.getElementById("s_sdt").value;
    var errorMessage = document.getElementById("error_sdt");

    // Kiểm tra định dạng số điện thoại (bắt đầu với 0 và có 10 chữ số)
    var phoneRegex = /^(0)[0-9]{9}$/;

    if (phoneRegex.test(phoneNumber)) {
        errorMessage.style.display = "none"; // Ẩn thông báo lỗi nếu đúng định dạng
        return true;
    } else {
        errorMessage.style.display = "block"; // Hiển thị thông báo lỗi nếu sai định dạng
        return false;
    }
}

// Sự kiện khi nhấn nút Đặt Hàng
document.getElementById("submitButton").addEventListener("click", function(event) {
    if (validatePhoneNumber()) {
        document.querySelector("form").submit(); // Submit form nếu số điện thoại hợp lệ
    } else {
        event.preventDefault(); // Ngừng submit nếu số điện thoại không hợp lệ
        alert("Số điện thoại không hợp lệ. Vui lòng nhập lại!");
    }
});
</script>

<?php require_once 'layout/second_footer.php'; ?>