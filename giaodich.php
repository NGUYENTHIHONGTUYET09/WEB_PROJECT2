<?php 

session_start();

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;


require 'vendor/PHPMailer/src/PHPMailer.php';
require 'vendor/PHPMailer/src/SMTP.php';

require_once 'backend-index.php';
require_once 'layout/second_header.php';

$ten = $quan = $dc = $sodt = $money = $sl = 0; // Khởi tạo biến với giá trị 0
if(isset($_POST['ten'])){
	$ten = $_POST['ten'];
}
if(isset($_POST['quan'])){
	$quan = $_POST['quan'];
}
if(isset($_POST['dc'])){
	$dc = $_POST['dc'];
}
if(isset($_POST['sodt'])){
	$sodt = $_POST['sodt'];
}
if(isset($_POST['sl'])){
	$sl = $_POST['sl'];
}

if(isset($_POST['email'])){
	$email = $_POST['email'];
}


if($ten == "" || $quan == "" || $dc == "" || $sodt == "" || $email == ""){
	echo "Không được để trống bất kỳ ô nào!";
	require_once 'layout/second_footer.php';
	return 0;
}

date_default_timezone_set('Asia/Ho_Chi_Minh');
$now = date("Y-m-d h:i:s");
$conn = connect();
mysqli_set_charset($conn, 'utf8');

for ($i = 0; $i < count($sl); $i++) {
	if (!isset($sl[$i]) || $sl[$i] < 0) {
			echo "<h3 style='color: red; padding: 30px;'>Số lượng tối thiểu phải là 0 và chỉ mục phải tồn tại.</h3>";
			require_once 'layout/second_footer.php';
			return 0;
	}
	$x = str_replace(' ', '', $_SESSION['cost'][$i]);
	$x = floatval($x);
	$money += $sl[$i] * $x;
}


if($money == 0){
	echo "<h3 style='color: red; padding: 30px;'>Không có sản phẩm nào được đặt!</h3>";
	require_once 'layout/second_footer.php';
	return 0;
}
?>

<div class="row">
    <div class="col-sm-12">
        <div style="text-align: center;">
            <h3 style="color: green;">Đơn hàng của quý khách đã được đặt <b>THÀNH CÔNG</b>, cảm ơn quý khách!</h3>
            <i>Quý khách sẽ sớm nhận được cuộc gọi xác nhận của chúng tôi, giá trị đơn hàng này là
                <b><?php echo number_format($money, 0, ","," ") ?> VND</b> và sẽ được thanh toán sau khi nhận hàng!</i>
            <a href="index.php">Quay lại trang chủ</a>
            <img src="images/clothes/tks4buying.png" width="100">
        </div>
    </div>
</div>

<?php
$userId = "";
if($_SESSION['rights'] == "user"){
	if ($_SESSION['rights'] == 'user') {
		// Lấy email từ cơ sở dữ liệu
		$userId = $_SESSION['user']['id'];
		$query = "SELECT email FROM thanhvien WHERE id = '$userId'";
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
				$productName = $_SESSION['product_names'][$i]; // Lấy tên sản phẩm từ session
				$quantity = $sl[$i];
				$price = number_format($_SESSION['cost'][$i], 0, ",", ".") . " VND";
				$totalPrice = number_format($quantity * $_SESSION['cost'][$i], 0, ",", ".") . " VND";
				$orderDetails .= "<li> Sản phẩm: $productName, Số lượng: $quantity, Đơn giá: $price, Thành tiền: $totalPrice </li>";
		}
		$orderDetails .= "<ul>";

		$orderDetails = "<ul>";
		for ($i = 0; $i < count($sl); $i++) {
			$productName = $_SESSION['product_names'][$i];
			$quantity = $sl[$i];
			$price = number_format($_SESSION['cost'][$i], 0, ",", ".");
			$totalPrice = number_format($quantity * $_SESSION['cost'][$i], 0, ",", ".");
			$orderDetails .= "<li>Sản phẩm: $productName, Số lượng: $quantity, Đơn giá: $price, Thành tiền: $totalPrice</li>";
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
		echo "Email đã được gửi thành công đến $email.";
	} catch (Exception $e) {
		echo "Có lỗi xảy ra khi gửi email. Lỗi: {$mail->ErrorInfo}";
	}
	
}

$sql = "INSERT INTO giaodich (user_id, user_name, user_dst, user_addr, user_phone, tongtien, date) 
        VALUES ('$userId', '$ten', '$quan', '$dc', '$sodt', '$money', '$now')";

if (!mysqli_query($conn, $sql)) {
    error_log("Error inserting into giaodich: " . mysqli_error($conn));
    echo "<script>alert('Đã xảy ra lỗi, vui lòng thử lại sau!');</script>";
    return;
}

$last_magd = mysqli_insert_id($conn);
if (!$last_magd) {
    error_log("Failed to retrieve last inserted ID: " . mysqli_error($conn));
    echo "<script>alert('Lỗi không xác định, nhưng đơn hàng của bạn đã được đặt.');</script>";
    return;
}

// Thêm chi tiết giao dịch vào bảng chitietgiaodich
for ($i = 0; $i < count($sl); $i++) {
    // Kiểm tra sản phẩm có trong session hay không
    if (isset($_SESSION['product_ids'][$i])) {
        $masp = $_SESSION['product_ids'][$i]; // Mã sản phẩm từ session
    } else {
        // Truy vấn cơ sở dữ liệu để lấy mã sản phẩm
        $product_name = $_SESSION['product_names'][$i];
        $sql = "SELECT masp FROM sanpham WHERE tensp = '$product_name' LIMIT 1";
        $result = mysqli_query($conn, $sql);
        if ($row = mysqli_fetch_assoc($result)) {
            $masp = $row['masp']; // Lấy mã sản phẩm
        } else {
            echo "Không tìm thấy mã sản phẩm: $product_name";
            continue;
        }
    }
    
    $quantity = $sl[$i];
    $price = $_SESSION['cost'][$i];
    $total = $quantity * $price;

    // Thêm chi tiết giao dịch vào bảng chitietgiaodich
    $sql_detail = "INSERT INTO chitietgiaodich (magd, masp, soluong) 
                   VALUES ('$last_magd', '$masp', '$quantity')";
    if (!mysqli_query($conn, $sql_detail)) {
        error_log("Error inserting into chitietgiaodich: " . mysqli_error($conn));
        echo "<script>alert('Đã xảy ra lỗi khi thêm chi tiết giao dịch!');</script>";
    }
}

// Xóa sản phẩm đã đặt khỏi giỏ hàng trong cơ sở dữ liệu
if ($_SESSION['rights'] === 'user') {
	$userId = $_SESSION['user']['id']; // Lấy ID người dùng từ session
	$last_magd = $last_magd; // Lấy ID đơn hàng đã được tạo trước đó

	// Chỉ xóa các sản phẩm trong giỏ hàng đã được đặt (dựa trên ID đơn hàng)
	$sql_delete_cart = "DELETE FROM giohang WHERE user_id = '$userId' AND masp IN (SELECT masp FROM chitietgiaodich WHERE magd = '$last_magd')";
	
	if (!mysqli_query($conn, $sql_delete_cart)) {
			error_log("Error deleting ordered products from cart: " . mysqli_error($conn));
			echo "<script>alert('Lỗi khi xóa sản phẩm khỏi giỏ hàng.');</script>";
	} else {
			echo "<script>alert('Sản phẩm đã được xóa khỏi giỏ hàng!');</script>";
	}
}

require_once 'layout/second_footer.php';

unset($_SESSION['user_cart'], $_SESSION['client_cart'], $_SESSION['product_names'], $_SESSION['cost'], $_SESSION['buynow']);

?>