<?php
require_once 'layout/second_header.php';
require_once 'backend-index.php';
$masp = $q = "";
$_SESSION['cost'] = array(); // Khởi tạo là một mảng
// session_start();
$user = isset($_SESSION['user']) ? $_SESSION['user'] : null;
$result = null;


if ($user) {
    $conn = connect();
    mysqli_set_charset($conn, 'utf8');
    $user_id = $user['id'];

    $sql = "SELECT
                lsmh.id AS id,
                lsmh.tong_tien AS tongtien,
                sp.tensp AS tensp,
                sp.masp AS masp,
                lsmh_sp.soluong AS soluong,
                sp.gia AS gia,
                sp.anhchinh AS hinhanh
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
} else {
}
$result = mysqli_query($conn, $sql);
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
<form action="giaodich.php" method="POST" role="form">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <div class="container-fluid form" style="margin-top: -23px; padding: 20px">
        <div class="row">
            <div class="col-lg-3"></div>
            <div class="col-lg-6">
                <div id="countdown" style="font-size: 24px; color: red; margin: 20px 0px;">
                    <i class="fas fa-clock"></i> <span id="timer"></span> <!-- Thêm biểu tượng đồng hồ -->
                </div>

                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>MÃ SẢN PHẨM</th>
                            <th>ẢNH</th>
                            <th>TÊN SẢN PHẨM</th>
                            <th>ĐƠN GIÁ</th>
                            <th>SỐ LƯỢNG</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = mysqli_fetch_assoc($result)) {
                            $_SESSION['product_names'][] = $row['tensp']; // Lưu tên sản phẩm  
                        ?>
                            <tr>
                                <td><input type="text" name="masp[]" value=<?php echo $row['masp'] ?> readonly></td>
                                <td><img src="<?php echo $row['hinhanh'] ?>" style="width: 70px"></td>
                                <td><?php echo $row['tensp'] ?></td>
                                <td class="cost" data-val="<?php echo $row['gia'] ?>">
                                    <?php echo $row['gia'];
                                    $_SESSION['cost'][] = $row['gia']; ?></td>
                                <td width="30px"><input type="number" name="sl[]" value=<?php echo $row['soluong'] ?> min="0" onchange="tinh_tien()">
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
                                                                                            if ($_SESSION['rights'] == 'user') {
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
                                                                                        if ($_SESSION['rights'] == 'user') {
                                                                                            echo $_SESSION['user']['diachi'];
                                                                                        }
                                                                                        ?>">
                </div>
                <div class="form-group">
                    <label for="">Số điện thoại: </label>
                    <input type="text" class="form-control" name="sodt" id="s_sdt" value="<?php
                                                                                            if ($_SESSION['rights'] == 'user') {
                                                                                                echo $_SESSION['user']['sodt'];
                                                                                            }
                                                                                            ?>">
                </div>

                <!-- Thêm trường Email -->
                <div class="form-group">
                    <label for="">Email: </label>
                    <input type="email" class="form-control" name="email" id="s_email" value="<?php
                                                                                                if ($_SESSION['rights'] == 'user') {
                                                                                                    echo $_SESSION['user']['email'];
                                                                                                }
                                                                                                ?>">
                </div>

                <!-- Thêm trường Phương thức thanh toán -->
                <div class="form-group">
                    <label for="">Phương thức thanh toán: </label>
                    <select class="form-control" name="payment_method" id="s_payment_method">
                        <option value="COD">COD (Thanh toán khi nhận hàng)</option>
                        <option value="Internet Banking">Internet Banking</option>
                        <option value="Credit Card">Thẻ tín dụng</option>
                    </select>
                </div>

                <button onclick="check_before_submit()" class="btn btn-primary" type="submit">Đặt Hàng</button><br><br>
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


<?php require_once 'layout/second_footer.php'; ?>