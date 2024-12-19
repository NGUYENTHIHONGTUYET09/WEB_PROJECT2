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


function displayCategorySelect()
{
    // Kết nối đến cơ sở dữ liệu
    $connect = connect();
    $str = "SELECT * FROM danhmucsp"; // Truy vấn lấy danh mục sản phẩm
    $result = $connect->query($str);

    // Hiển thị combobox
    echo "<select name='madanhmuc' class='form-control'>";
    while ($row = $result->fetch_row()) {
        // Lưu mã danh mục làm giá trị, tên danh mục hiển thị trên combobox
        echo "<option value='$row[0]'>" . $row[1] . "</option>";
    }
    echo "</select>";
}


//Danh sach thanh vien
function member_list()
{
    $conn = connect();
    mysqli_set_charset($conn, 'utf8');
    $sql = "SELECT * FROM thanhvien ORDER BY ngaytao DESC";
    $result = mysqli_query($conn, $sql); ?>

<thead>
    <tr>
        <th>ID</th>
        <th>Tên</th>
        <th>Tên tài khoản</th>
        <th>Mật khẩu</th>
        <th>Địa chỉ</th>
        <th>Số dt</th>
        <th>Email</th>
        <th>Ngày tham gia</th>
        <th>Quyền</th>
        <th></th>
    </tr>
</thead>
<tbody>

    <?php while ($row = mysqli_fetch_assoc($result)) { ?>


    <tr>
        <td><?php echo $row['id'] ?></td>
        <td><?php echo $row['ten'] ?></td>
        <td><?php echo $row['tentaikhoan'] ?></td>
        <td>*****</td>
        <td><?php echo $row['diachi'] ?></td>
        <td><?php echo $row['sodt'] ?></td>
        <td><?php echo $row['email'] ?></td>
        <td><?php echo $row['ngaytao'] ?></td>
        <td><?php if ($row['quyen']) echo "Admin";
                    else echo "User";  ?></td>
        <td><span class="btn btn-danger" onclick="xoa_taikhoan('<?php echo $row["id"] ?>')">Xóa</span></td>
    </tr>

    <?php }    ?>
</tbody>
<?php
    disconnect($conn);
}
//Danh sach giao dich
function exchange_list()
{
    $conn = connect();
    mysqli_set_charset($conn, 'utf8');
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
<tbody id="body-gd-list">

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
//Danh sach danh muc san pham
function type_list()
{
    $conn = connect();
    mysqli_set_charset($conn, 'utf8');
    $sql = "SELECT * FROM danhmucsp";
    $result = mysqli_query($conn, $sql); ?>

<thead>
    <tr>
        <th>STT</th>
        <th>Tên danh mục</th>
        <th>Xuất sứ</th>
        <th></th>
    </tr>
</thead>
<tbody>

    <?php while ($row = mysqli_fetch_assoc($result)) { ?>


    <tr>
        <td><?php echo $row['madm'] ?></td>
        <td><?php echo $row['tendm'] ?></td>
        <td><?php echo $row['xuatsu'] ?></td>
        <td>
            <span class="btn btn-danger" onclick="xoa_dm('<?php echo $row['madm'] ?>')">Xóa</span>
        </td>
    </tr>

    <?php }    ?>
</tbody>

<?php
}
//Danh sach san pham
function product_list()
{
    $conn = connect();
    mysqli_set_charset($conn, 'utf8');
    $sql = "SELECT * FROM sanpham s, danhmucsp d WHERE s.madm = d.madm ORDER BY ngay_nhap DESC";
    $i = 1;
    $result = mysqli_query($conn, $sql); ?>

<thead>
    <tr>
        <th>STT</th>
        <th>Tên</th>
        <th>Giá</th>
        <th>Chất liệu</th>
        <th>Màu</th>
        <th>Dành cho</th>
        <th>Khuyến Mãi</th>
        <th>Loại</th>
        <th>Ảnh</th>
        <th>Ngày nhập</th>
        <th>Hành động</th>
    </tr>
</thead>

<tbody id="body-sp-list">
    <?php while ($row = mysqli_fetch_assoc($result)) { ?>
    <tr>
        <td><?php echo $i++ ?></td>
        <td><?php echo htmlspecialchars($row['tensp']) ?></td>
        <td><?php echo number_format($row['gia'], 0, ',', '.') ?> VND</td>
        <td><?php echo htmlspecialchars($row['chatlieu']) ?></td>
        <td><?php echo htmlspecialchars($row['mau']) ?></td>
        <td><?php echo htmlspecialchars($row['danhcho']) ?></td>
        <td><?php echo htmlspecialchars($row['khuyenmai']) ?></td>
        <td><?php echo htmlspecialchars($row['tendm']) ?></td>
        <td><img src="../<?php echo htmlspecialchars($row['anhchinh']) ?>" style="width: 100px; height: auto;"></td>
        <td><?php echo htmlspecialchars($row['ngay_nhap']) ?></td>
        <td>
            <span><a class='btn btn-warning' href='sua_sanpham.php?masp=<?php echo $row['masp']; ?>'>Sửa</a></span>

            <span class="btn btn-danger" onclick="xoa_sp('<?php echo $row['masp'] ?>')">Xóa</span>
        </td>

    </tr>
    <?php } ?>
</tbody>

<?php
}



//in ra cac loai sp
function list_type_pr_for_add()
{
    $conn = connect();
    mysqli_set_charset($conn, 'utf8');
    $sql = "SELECT * FROM danhmucsp";
?> <select class="form-control" id="madm"> <?php
                                                $result = mysqli_query($conn, $sql); ?>
    <?php while ($row = mysqli_fetch_assoc($result)) { ?>
    <option value="<?php echo $row['madm'] ?>"><?php echo $row['tendm'] ?></option>
    <?php }    ?>
</select>
<?php
}




?>