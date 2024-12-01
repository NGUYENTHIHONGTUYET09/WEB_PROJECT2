<?php

session_start();
$user = isset($_SESSION['user']) ? $_SESSION['user'] : null;


use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// require 'vendor/PHPMailer/src/Exception.php';

require __DIR__ . '/vendor/PHPMailer/src/Exception.php';

echo "PHPMailer Exception.php loaded successfully!";

// require 'vendor/PHPMailer/src/Exception.php';
require 'vendor/PHPMailer/src/PHPMailer.php';
require 'vendor/PHPMailer/src/SMTP.php';

require_once 'backend-index.php';
require_once 'layout/second_header.php';
$ten = $quan = $dc = $sodt = $money = $sl = $masp = 0; // Khởi tạo biến với giá trị 0
if (isset($_POST['ten'])) {
	$ten = $_POST['ten'];
}
if (isset($_POST['quan'])) {
	$quan = $_POST['quan'];
}
if (isset($_POST['dc'])) {
	$dc = $_POST['dc'];
}
if (isset($_POST['sodt'])) {
	$sodt = $_POST['sodt'];
}
if (isset($_POST['sl'])) {
	$sl = $_POST['sl'];
}

if (isset($_POST['masp'])) {
	$masp = $_POST['masp'];
}

if (isset($_POST['email'])) {
	$email = $_POST['email'];
}


if ($ten == "" || $quan == "" || $dc == "" || $sodt == "" || $email == "") {
	echo "Không được để trống bất kỳ ô nào!";
	require_once 'layout/second_footer.php';
	return 0;
}

date_default_timezone_set('Asia/Ho_Chi_Minh');
$now = date("Y-m-d h:i:s");
$conn = connect();
mysqli_set_charset($conn, 'utf8');

for ($i = 0; $i < count($sl); $i++) {
	if ($sl[$i] < 0) {
		echo "<h3 style='color: red; padding: 30px;'>Số lượng tối thiểu phải bằng 0</h3>";
		require_once 'layout/second_footer.php';
		return 0;
	}
	$x = str_replace(' ', '', $_SESSION['cost'][$i]);
	$x = floatval($x); // Chuyển đổi chuỗi sang số thực
	$money += $sl[$i] * $x; // Bây giờ $money sẽ cộng được
}

if ($money == 0) {
	echo "<h3 style='color: red; padding: 30px;'>Không có sản phẩm nào được đặt!</h3>";
	require_once 'layout/second_footer.php';
	return 0;
}
?>



<?php
try {
	if ($user) {
		// $now = date('Y-m-d'); // For DATE column
		// If it's a DATETIME column, use: $now = date('Y-m-d H:i:s');

		$user_id = intval($user['id']); // Ensure user ID is an integer
		$tongtien = intval($money); // Ensure tong_tien is an integer

		$sql = "SELECT id FROM lich_su_mua_hang WHERE user_id = $user_id AND trang_thai = 'Giỏ hàng'";
		$result = mysqli_query($conn, $sql);
		$row = mysqli_fetch_assoc($result);
		$id = '';
		if ($row) {
			$id = intval($row['id']);
		} else {
			throw new Exception("Not found gio hang " . $user_id . mysqli_error($conn));
		}

		for ($i = 0; $i < count($sl); $i++) {
			$sp = $masp[$i];
			$quan = $sl[$i];

			$sql = "UPDATE lich_su_mua_hang_sanpham
					SET soluong = $quan
					WHERE maLSmuahang = $id AND sanpham_id = $sp";

			if ($quan == 0 || !$quan) {
				$sql = "DELETE FROM lich_su_mua_hang_sanpham WHERE maLSmuahang = $id AND sanpham_id = $sp";
				if (!mysqli_query($conn, $sql)) {
					throw new Exception("Could not delete lich_su_mua_hang_sanpham: " . mysqli_error($conn));
				}
			} else if (!mysqli_query($conn, $sql)) {
				throw new Exception("Could not update lich_su_mua_hang: " . mysqli_error($conn));
			}
		}

		// Update query
		$sql = "UPDATE lich_su_mua_hang 
		SET tong_tien = $tongtien,
			trang_thai = 'Đặt hàng',
			ngay_dat = '$now'
		WHERE user_id = $user_id AND trang_thai = 'Giỏ hàng'";

		// Execute the query
		if (!mysqli_query($conn, $sql)) {
			// Throw an exception with the error message from MySQL
			throw new Exception("Could not update lich_su_mua_hang: " . mysqli_error($conn));
		}
	} else throw new Exception(message: "Could not update lich_su_mua_hang: not user ");
} catch (Exception $e) {
	if (isset($conn)) {
		mysqli_rollback($conn);
	}
	echo "<h3 style='color: red; padding: 30px;'>Đã xảy ra lỗi: " . htmlspecialchars($e->getMessage()) . "</h3>";
} finally {
	if (isset($conn)) {
		mysqli_close($conn);
	}
}
$userId = "";
if ($_SESSION['rights'] == "user") {

	$conn = connect();
	mysqli_set_charset($conn, 'utf8');
	if ($_SESSION['rights'] == 'user') {
		// Lấy email từ cơ sở dữ liệu
		$userId = $_SESSION['user']['id'];
		$query = "SELECT email FROM thanhvien WHERE id = $userId";
		$result = mysqli_query($conn, $query);
		$row = mysqli_fetch_assoc($result);
		$email = $row['email'];
	} else {
		// Lấy email từ form
		$email = $_POST['email'];
	}

	// Khởi tạo đối tượng PHPMailer
	$mail = new PHPMailer(true);

	try {
		// Cấu hình server SMTP của Gmail
		$mail->isSMTP();
		$mail->Host = 'smtp.gmail.com'; // Server SMTP của Gmail
		$mail->SMTPAuth = true;
		$mail->Username = 'huuthien180204@gmail.com'; // Địa chỉ email của shop
		$mail->Password = 'ejsnaiikwgbgjacr'; // Mật khẩu ứng dụng hoặc mật khẩu của email
		$mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
		$mail->Port = 587; // Port cho STARTTLS là 587
		$mail->CharSet = 'UTF-8';

		// Thông tin người gửi và người nhận
		$mail->setFrom('huuthien180204@gmail.com', 'Fashion Katy'); // Địa chỉ và tên người gửi
		$mail->addAddress($email, $ten); // Địa chỉ và tên người nhận

		// Tạo nội dung chi tiết đơn hàng
		$orderDetails = "<ul>";
		for ($i = 0; $i < count($sl); $i++) {
			$productName = $_SESSION['product_names'][$i]; // Tên sản phẩm
			$quantity = $sl[$i];
			$price = number_format($_SESSION['cost'][$i], 0, ",", ".") . " VND";
			$totalPrice = number_format($quantity * $_SESSION['cost'][$i], 0, ",", ".") . " VND";

			$orderDetails .= "<li> Sản phẩm: $productName, Số lượng: $quantity, Đơn giá: $price, Thành tiền: $totalPrice </li>";
		}
		$orderDetails .= "</ul>";

		// Thiết lập nội dung email mới
		$mail->isHTML(true);
		$mail->Subject = "Đơn hàng của bạn đã được đặt thành công";
		$mail->Body = "<p>Xin chào $ten,</p>
									 <p>Cảm ơn bạn đã đặt hàng!</p>
									 <p>Chi tiết đơn hàng:</p>
									 $orderDetails
									 <p><strong>Tổng giá trị đơn hàng: " . number_format($money, 0, ",", ".") . " VND</strong></p>
									 <p>Chúng tôi sẽ liên hệ với bạn sớm nhất có thể.</p>
									 <p>Trân trọng,<br>Đội ngũ hỗ trợ khách hàng.</p>";

		// Gửi email
		$mail->send();
		// echo "Email đã được gửi thành công đến $email.";
	} catch (Exception $e) {
		echo "Có lỗi xảy ra khi gửi email. Lỗi: {$mail->ErrorInfo}";
	} finally {
		if (isset($conn)) {
			mysqli_close($conn);
		}
	}
}



// $sql = "INSERT INTO giaodich VALUES ('',0,'".$userId."','".$ten."','".$quan."','".$dc."','".$sodt."','".$money."','".$now."')";
// if(!mysqli_query($conn, $sql)){
// 	echo "Đã xảy ra một lỗi nhỏ trong quá trình đặt hàng, vui lòng đặt hàng lại!";

// }
// $sql = "SELECT magd FROM giaodich WHERE magd = LAST_INSERT_ID()";
// $result = mysqli_query($conn, $sql);
// $last_magd = "";
// if(!$result){
// 	echo "Lỗi không xác định, nhưng không sao, đơn hàng của bạn đã được đặt thành công!";
// }
// while($row = mysqli_fetch_assoc($result)){
// 	$last_magd = $row['magd'];
// }
// $sql_multi = array();
// $buynow = "";
// if(isset($_SESSION['buynow'])){
// 	$buynow = $_SESSION['buynow'];
// 	$sql = "INSERT INTO chitietgd VALUES ('".$last_magd."','".$buynow."','".$sl[0]."')";
// 	require_once 'layout/second_footer.php';
// 	return 0;
// }

// if($_SESSION['rights'] == "user"){
// 	$new_masp = $_SESSION['user_cart'];
// } else {
// 	$new_masp = $_SESSION['client_cart'];
// }

// array_shift($new_masp);
// for($i = 0; $i < count($new_masp); $i++){
// 	$sql_multi[] = "INSERT INTO chitietgd VALUES ('".$last_magd."','".$new_masp[$i]."','".$sl[$i]."')";
// }
// for($i = 0; $i < count($sql_multi); $i++){
// 	$result = mysqli_query($conn, $sql_multi[$i]);
// }
?>
<div class="row">
	<div class="col-sm-12">
		<div style="text-align: center; margin: 20px;">
			<h3 style="color: green;">Đơn hàng của quý khách đã được đặt <b>THÀNH CÔNG</b>, cảm ơn quý khách!</h3>
			<span>Đơn hàng đã được gửi đến email của  quý khách, vui lòng kiểm tra!</span>
			<i>Quý khách sẽ sớm nhận được cuộc gọi xác nhận của chúng tôi, giá trị đơn hàng này là
				<b><?php echo number_format($money, 0, ",", " ") ?> VND</b> và sẽ được thanh toán sau khi nhận hàng!</i>
			<a href="index.php">Quay lại trang chủ</a>
			<img src="images/tks4buying.png">
		</div>
	</div>
</div>

<?php require_once 'layout/second_footer.php'; ?>