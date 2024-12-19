<?php
$conn;
function connect()
{
    $conn = mysqli_connect('localhost', 'root', '280704', 'qlbh') or die('Không thể kết nối!');
    return $conn;
}
function disconnect($conn)
{
    mysqli_close($conn);
}
function validate_input_sql($conn, $str)
{
    return mysqli_real_escape_string($conn, $str);
}
$q = "";
if (isset($_POST['q'])) {
    $q = $_POST['q'];
}
$fname = "";
if (isset($_GET['fname'])) {
    $fname = $_GET['fname'];
}
if ($fname == "load_more") {
    load_more();
}

function load_more()
{
    session_start();
    $cr = '';
    if (isset($_GET['current'])) {
        $cr = $_GET['current'];
    }
    $st = ($cr + 1) * $_SESSION['limit'];
    if ($st >= $_SESSION['total'] - $_SESSION['limit']) {
        echo "Đã hết mục để hiển thị";
    }
    $sql = "SELECT * FROM sanpham s, danhmucsp d WHERE s.madm = d.madm ORDER BY ngay_nhap DESC LIMIT " . $st . "," . $_SESSION['limit'] . "";
    $conn = mysqli_connect('localhost', 'root', '', 'qlbh') or die('Không thể kết nối!');
    mysqli_set_charset($conn, 'utf8');
    $result = mysqli_query($conn, $sql);
    $i = $st;
    while ($row = mysqli_fetch_assoc($result)) {
?>
<tr>
    <td><?php echo ++$i ?></td>
    <td><?php echo $row['tensp'] ?></td>
    <td><?php echo $row['gia'] ?></td>
    <td><?php echo $row['chatlieu'] ?></td>
    <td><?php echo $row['mau'] ?></td>
    <td><?php echo $row['danhcho'] ?></td>
    <td><?php echo $row['khuyenmai'] ?></td>
    <td><?php echo $row['tendm'] ?></td>
    <td><img src="../<?php echo $row['anhchinh'] ?>"></td>
    <td><?php echo $row['ngay_nhap'] ?></td>
    <td> <span><a class='btn btn-warning' href='sua_sanpham.php?masp=$masp'>Sửa</a></span>
    <td><span class="btn btn-danger" onclick="xoa_sp('<?php echo $row['masp'] ?>')">Xóa</span></td>
</tr>

<?php
    }
}


switch ($q) {
    case 'xoa_sp':
        xoa_sp();
        break;
    case 'them_dm':
        them_dm();
        break;
    case 'xoa_dm':
        xoa_dm();
        break;
    case 'giaodich_chuagh':
        giaodich_chuagh();
        break;
    case 'giaodich_dagh':
        giaodich_dagh();
        break;
    case 'giaodich_tatcagh':
        giaodich_tatcagh();
        break;
    case 'them_admin':
        them_admin();
        break;
    case 'xoa_taikhoan':
        xoa_taikhoan();
        break;
    case 'sua_sp':
        sua_sp();
        break;
}



function xoa_sp()
{
    if (isset($_POST['q']) && $_POST['q'] === 'xoa_sp' && isset($_POST['masp_xoa'])) {
        $masp = $_POST['masp_xoa'];

        // Kết nối cơ sở dữ liệu
        $conn = connect();

        // Truy vấn xóa sản phẩm
        $sql = "DELETE FROM sanpham WHERE masp = '" . mysqli_real_escape_string($conn, $masp) . "'";

        if (mysqli_query($conn, $sql)) {
            echo "Xóa thành công!";
        } else {
            echo "Đã xảy ra lỗi!";
        }

        // Đóng kết nối
        disconnect($conn);
    }
}
function them_dm()
{
    $tendm = $_POST['tendm'];
    $xuatsu = $_POST['xuatsu'];
    $conn = connect();
    $sql = "INSERT INTO danhmucsp VALUES (' ','" . $tendm . "','" . $xuatsu . "')";
    if (mysqli_query($conn, $sql)) {
        echo "<script>
alert('Thêm danh mục thành công!')
</script>";
    } else {
        echo "<script>
alert('Đã xảy ra lỗi!')
</script>";
    }
}
function xoa_dm()
{
    $madm = $_POST['madm_xoa'];
    $conn = connect();
    $sql = "DELETE FROM danhmucsp WHERE madm = '" . $madm . "'";
    if (mysqli_query($conn, $sql)) {
        echo "<script>
alert('Xóa thành công!')
</script>";
    } else {
        echo "<script>
alert('Lỗi! Bạn phải xóa hết những sản phẩm thuộc danh mục này trước!')
</script>";
    }
}

//Danh sach giao dich sap xep
function giaodich_chuagh()
{
    session_start();
    $conn = connect();
    mysqli_set_charset($conn, 'utf8');
    echo $_SESSION['limit'];
    $sql = "SELECT * FROM giaodich WHERE tinhtrang = 0";
    $i = 1;
    $result = mysqli_query($conn, $sql); ?>

<thead>
    <tr>
        <th>STT</th>
        <th>Tình trạng</th>
        <th>Tên</th>
        <th>Quận</th>
        <th>Địa chỉ</th>
        <th>Số DT</th>
        <th>Tổng tiền</th>
        <th>Ngày</th>

    </tr>
</thead>
<tbody id="gd_chuagd_body">

    <?php while ($row = mysqli_fetch_assoc($result)) { ?>


    <tr>
        <td><?php echo $i++ ?></td>
        <td><?php if ($row['tinhtrang']) echo "<h4 class='label label-success'>Đã hoàn tất</h4>";
                    else echo "<h4 class='label label-danger'>Chưa hoàn tất</h4>";  ?>
        </td>
        <td><?php echo $row['user_name'] ?></td>
        <td><?php echo $row['user_dst'] ?></td>
        <td><?php echo $row['user_addr'] ?></td>
        <td><?php echo $row['user_phone'] ?></td>
        <td><?php echo $row['tongtien'] ?></td>
        <td><?php echo $row['date'] ?></td>

    </tr>

    <?php }    ?>
</tbody>

<?php
    disconnect($conn);
}
function giaodich_dagh()
{
    session_start();
    $conn = connect();
    mysqli_set_charset($conn, 'utf8');
    $sql = "SELECT * FROM giaodich WHERE tinhtrang = 1";
    $i = 1;
    $result = mysqli_query($conn, $sql); ?>

<thead>
    <tr>
        <th>STT</th>
        <th>Tình trạng</th>
        <th>Tên</th>
        <th>Quận</th>
        <th>Địa chỉ</th>
        <th>Số DT</th>
        <th>Tổng tiền</th>
        <th>Ngày</th>

    </tr>
</thead>
<tbody id="gd_dagd_body">

    <?php while ($row = mysqli_fetch_assoc($result)) { ?>


    <tr>
        <td><?php echo $i++ ?></td>
        <td><?php if ($row['tinhtrang']) echo "<h4 class='label label-success'>Đã hoàn tất</h4>";
                    else echo "<h4 class='label label-danger'>Chưa hoàn tất</h4>";  ?>
        </td>
        <td><?php echo $row['user_name'] ?></td>
        <td><?php echo $row['user_dst'] ?></td>
        <td><?php echo $row['user_addr'] ?></td>
        <td><?php echo $row['user_phone'] ?></td>
        <td><?php echo $row['tongtien'] ?></td>
        <td><?php echo $row['date'] ?></td>
        <td></td>
    </tr>

    <?php }    ?>
</tbody>

<?php
    disconnect($conn);
}
function giaodich_tatcagh()
{
    session_start();
    $conn = connect();
    mysqli_set_charset($conn, 'utf8');
    $sql = "SELECT * FROM giaodich";
    $i = 1;
    $result = mysqli_query($conn, $sql); ?>

<thead>
    <tr>
        <th>STT</th>
        <th>Tình trạng</th>
        <th>Tên</th>
        <th>Quận</th>
        <th>Địa chỉ</th>
        <th>Số DT</th>
        <th>Tổng tiền</th>
        <th>Ngày</th>
    </tr>
</thead>
<tbody id="gd_tatcagd_body">

    <?php while ($row = mysqli_fetch_assoc($result)) { ?>


    <tr>
        <td><?php echo $i++ ?></td>
        <td><?php if ($row['tinhtrang']) echo "<h4 class='label label-success'>Đã hoàn tất</h4>";
                    else echo "<h4 class='label label-danger'>Chưa hoàn tất</h4>";  ?>
        </td>
        <td><?php echo $row['user_name'] ?></td>
        <td><?php echo $row['user_dst'] ?></td>
        <td><?php echo $row['user_addr'] ?></td>
        <td><?php echo $row['user_phone'] ?></td>
        <td><?php echo $row['tongtien'] ?></td>
        <td><?php echo $row['date'] ?></td>

    </tr>

    <?php }    ?>
</tbody>

<?php
    disconnect($conn);
}

function them_admin()
{
    $conn = connect();

    // Kiểm tra và nhận dữ liệu từ AJAX
    $ten = isset($_POST['ten']) ? mysqli_real_escape_string($conn, $_POST['ten']) : null;
    $tentk = isset($_POST['tentk']) ? mysqli_real_escape_string($conn, $_POST['tentk']) : null;
    $mk = isset($_POST['mk']) ? mysqli_real_escape_string($conn, $_POST['mk']) : null;
    $diachi = isset($_POST['diachi']) ? mysqli_real_escape_string($conn, $_POST['diachi']) : null;
    $sdt = isset($_POST['sdt']) ? mysqli_real_escape_string($conn, $_POST['sdt']) : null;
    $email = isset($_POST['email']) ? mysqli_real_escape_string($conn, $_POST['email']) : null;

    // Kiểm tra nếu các trường bắt buộc bị bỏ trống
    if (!$ten || !$tentk || !$mk) {
        echo "Tên, tên tài khoản và mật khẩu là bắt buộc!";
        exit;
    }

    // Kiểm tra số điện thoại đúng định dạng (10 chữ số)
    if (!preg_match("/^\d{10}$/", $sdt)) {
        echo "Số điện thoại phải gồm 10 chữ số.";
        exit;
    }

    // Kiểm tra email có hợp lệ không
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "Địa chỉ email không hợp lệ.";
        exit;
    }

    // Kiểm tra số điện thoại đã tồn tại trong cơ sở dữ liệu
    $sql_check_sdt = "SELECT * FROM thanhvien WHERE sodt = '$sdt'";
    $result_sdt = mysqli_query($conn, $sql_check_sdt);
    if (mysqli_num_rows($result_sdt) > 0) {
        echo "Số điện thoại đã tồn tại trong hệ thống.";
        exit;
    }

    // Kiểm tra email đã tồn tại trong cơ sở dữ liệu
    $sql_check_email = "SELECT * FROM thanhvien WHERE email = '$email'";
    $result_email = mysqli_query($conn, $sql_check_email);
    if (mysqli_num_rows($result_email) > 0) {
        echo "Email đã tồn tại trong hệ thống.";
        exit;
    }

    // Gán ngày tạo bằng ngày hiện tại
    $ngaytao = date('Y-m-d H:i:s'); // Định dạng: Năm-Tháng-Ngày Giờ:Phút:Giây

    // Tạo câu lệnh SQL
    $sql = "INSERT INTO thanhvien (ten, tentaikhoan, matkhau, diachi, sodt, email, ngaytao, quyen) 
            VALUES ('$ten', '$tentk', '$mk', '$diachi', '$sdt', '$email', '$ngaytao', '1')";

    // Thực thi và kiểm tra lỗi
    if (!mysqli_query($conn, $sql)) {
        echo "<script>alert('Tên tài khoản đã tồn tại hoặc xảy ra lỗi!')</script>";
    } else {
        echo "<script>alert('Tạo thành công!')</script>";
    }

    disconnect($conn);
}


function xoa_taikhoan()
{
    $id = $_POST['id_tk_xoa'];
    $conn = connect();
    $sql = "DELETE FROM thanhvien WHERE id = '" . $id . "'";
    if (!mysqli_query($conn, $sql)) {
        echo "Đã xảy ra lỗi!";
    } else {
        echo "<script>alert('Xóa xong!')</script>";
    }
    disconnect($conn);
}

function sua_sp()
{
    $masp = isset($_POST['masp_sua']) ? $_POST['masp_sua'] : null;
    $tensp = isset($_POST['tensp_ed']) ? $_POST['tensp_ed'] : null;
    $gia = isset($_POST['gia_ed']) ? $_POST['gia_ed'] : null;
    $khuyenmai = isset($_POST['khuyenmai_ed']) ? $_POST['khuyenmai_ed'] : null;
    $tinhtrang = isset($_POST['tinhtrang_ed']) ? $_POST['tinhtrang_ed'] : null;

    // Chuẩn bị mảng dữ liệu cập nhật
    $set = [];
    if ($tensp !== null && $tensp !== "") {
        $set[] = "tensp = '" . mysqli_real_escape_string(connect(), $tensp) . "'";
    }
    if ($gia !== null && $gia !== "") {
        $set[] = "gia = '" . mysqli_real_escape_string(connect(), $gia) . "'";
    }
    if ($khuyenmai !== null && $khuyenmai !== "") {
        $set[] = "khuyenmai = '" . mysqli_real_escape_string(connect(), $khuyenmai) . "'";
    }
    if ($tinhtrang !== null && $tinhtrang !== "") {
        $set[] = "tinhtrang = '" . mysqli_real_escape_string(connect(), $tinhtrang) . "'";
    }

    // Kết hợp các phần tử của mảng `set` thành chuỗi
    $str = implode(", ", $set);

    // Nếu không có gì để cập nhật
    if (empty($str)) {
        echo "<script>alert('Không có gì để cập nhật.');</script>";
        return;
    }

    $conn = connect();
    $sql = "UPDATE sanpham SET $str WHERE masp = '" . mysqli_real_escape_string($conn, $masp) . "'";

    if (mysqli_query($conn, $sql)) {
        // Hiển thị thông báo và reload lại trang
        echo "<script>
							alert('Cập nhật thành công!');
							window.location.reload(); // Reload trang hiện tại
						</script>";
    } else {
        echo "Đã xảy ra lỗi: " . mysqli_error($conn);
    }

    disconnect($conn);
}