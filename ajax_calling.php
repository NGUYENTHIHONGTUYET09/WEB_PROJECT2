<?php

require_once 'backend-index.php';


$fname = "";
if (isset($_GET['fname'])) {
    $fname = $_GET['fname'];
}
switch ($fname) {
    case 'php_saling':
        php_saling();
        break;
    case 'php_new':
        php_new();
        break;
    case 'php_buy':
        php_buy();
        break;
    case 'php_dmsp':
        php_danhmucsp();
        break;
    case 'php_dangky':
        php_dangky();
        break;
    case 'php_dangnhap':
        php_dangnhap();
        break;
    case 'php_giohang':
        php_giohang();
        break;
    case 'php_like':
        php_like();
        break;
    case 'php_search':
        php_search();
        break;
    case 'load_more':
        load_more();
        break;

    default:
        echo "Yêu cầu không tìm thấy!";
}
function load_more()
{
    session_start();
    $cr = isset($_GET['current']) ? $_GET['current'] : '';
    $st = ($cr + 1) * $_SESSION['limit'];

    if ($st >= $_SESSION['total']) {
        echo "Hết sản phẩm"; // Thông báo hết sản phẩm
        return; // Kết thúc hàm nếu đã hết sản phẩm
    }

    $sql = $_SESSION['sql'] . " LIMIT " . $st . "," . $_SESSION['limit'];
    $conn = mysqli_connect('localhost', 'root', '', 'qlbh') or die('Không thể kết nối!');
    mysqli_set_charset($conn, 'utf8');

    $result = mysqli_query($conn, $sql);

    // Kiểm tra xem có sản phẩm nào không
    if (mysqli_num_rows($result) == 0) {
        echo "<p>Không có sản phẩm nào để hiển thị.</p>";
    } else {
        while ($row = mysqli_fetch_assoc($result)) {
?>
            <div class='product-container' onclick="hien_sanpham('<?php echo htmlspecialchars($row['masp']); ?>')">
                <a data-toggle='modal' href='sanpham.php?masp=<?php echo htmlspecialchars($row['masp']); ?>'
                    data-target='#modal-id'>
                    <div style="text-align: center;" class='product-img'>
                        <img src='<?php echo htmlspecialchars($row['anhchinh']); ?>' alt='Hình sản phẩm'>
                    </div>
                    <div class='product-info'>
                        <h4><b><?php echo htmlspecialchars($row['tensp']); ?></b></h4>
                        <b class='price'>Giá: <?php echo htmlspecialchars($row['gia']); ?> VND</b>
                        <div class='buy'>
                            <a onclick="like_action('<?php echo htmlspecialchars($row['masp']); ?>')" class='btn btn-default btn-md unlike-container <?php
                                                                                                                                                        if ($_SESSION['rights'] == 'user' && in_array($row['masp'], $_SESSION['like'])) {
                                                                                                                                                            echo 'liked';
                                                                                                                                                        }
                                                                                                                                                        ?>'>
                                <i class='glyphicon glyphicon-heart unlike'></i>
                            </a>
                            <a class='btn btn-primary btn-md cart-container <?php
                                                                            if (($_SESSION['rights'] == "default" && in_array($row['masp'], $_SESSION['client_cart'])) ||
                                                                                ($_SESSION['rights'] != "default" && in_array($row['masp'], $_SESSION['user_cart']))
                                                                            ) {
                                                                                echo 'cart-ordered';
                                                                            }
                                                                            ?>' onclick="addtocart_action('<?php echo htmlspecialchars($row['masp']); ?>')">
                                <i title='Thêm vào giỏ hàng' class='glyphicon glyphicon-shopping-cart cart-item'></i>
                            </a>

                            <a class="snip0050" onclick="addtocart_action('<?php echo htmlspecialchars($row['masp']); ?>',true)"
                                <!-- href='order.php?masp=<?php echo htmlspecialchars($row['masp']); ?>' -->
                                >
                                <span>Mua ngay</span>
                                <i class="glyphicon glyphicon-ok"></i>
                            </a>
                        </div>
                    </div>
                </a>
            </div>
        <?php
        }
    }

    mysqli_close($conn); // Đóng kết nối
}




function php_saling()
{
    session_start();
    $conn = connect();
    mysqli_set_charset($conn, 'utf8');

    // Đảm bảo giới hạn được thiết lập và là một số hợp lệ
    if (!isset($_SESSION['limit']) || !is_numeric($_SESSION['limit'])) {
        $_SESSION['limit'] = 10;  // Đặt một giới hạn mặc định nếu chưa được thiết lập
    }

    // Sử dụng LIMIT từ phiên
    $limit = intval($_SESSION['limit']);
    $sql = "SELECT * FROM sanpham sp INNER JOIN danhmucsp dm ON sp.madm = dm.madm ORDER BY sp.khuyenmai DESC LIMIT $limit";
    $result = mysqli_query($conn, $sql);

    // Kiểm tra xem truy vấn có thành công không
    if (!$result) {
        die("Query failed: " . mysqli_error($conn));  // Xuất thông báo lỗi MySQL để kiểm tra
    }

    while ($row = mysqli_fetch_assoc($result)) {
        // Kiểm tra masp trước khi truyền vào onclick
        $masp = isset($row['masp']) ? json_encode($row['masp']) : 'null';
        ?>
        <div class='product-container' onclick="hien_sanpham(<?php echo $masp; ?>)">
            <a data-toggle='modal' href='sanpham.php?masp=<?php echo htmlspecialchars($row['masp'], ENT_QUOTES) ?>'
                data-target='#modal-id'>
                <div style="text-align: center;" class='product-img'>
                    <img src='<?php echo htmlspecialchars($row['anhchinh'], ENT_QUOTES) ?>'
                        alt="<?php echo htmlspecialchars($row['tensp'], ENT_QUOTES) ?>">
                </div>
                <div class='product-info'>
                    <h4><b><?php echo htmlspecialchars($row['tensp'], ENT_QUOTES) ?></b></h4>
                    <b class='price'>Giá: <?php echo number_format($row['gia'], 0, ',', '.') ?> VND</b>
                    <div class='buy'>
                        <a onclick="like_action(<?php echo $masp; ?>)" class='btn btn-default btn-md unlike-container <?php
                                                                                                                        if (isset($_SESSION['rights']) && $_SESSION['rights'] == 'user' && isset($_SESSION['like']) && in_array($row['masp'], $_SESSION['like'])) {
                                                                                                                            echo 'liked';
                                                                                                                        }
                                                                                                                        ?>'>
                            <i class='glyphicon glyphicon-heart unlike'></i>
                        </a>
                        <a class='btn btn-primary btn-md cart-container <?php
                                                                        if (($_SESSION['rights'] == "default" && in_array($row['masp'], $_SESSION['client_cart'])) ||
                                                                            ($_SESSION['rights'] != "default" && in_array($row['masp'], $_SESSION['user_cart']))
                                                                        ) {
                                                                            echo 'cart-ordered';
                                                                        }
                                                                        ?>' data-masp='<?php echo htmlspecialchars($row['masp'], ENT_QUOTES); ?>'
                            onclick="addtocart_action('<?php echo htmlspecialchars($row['masp'], ENT_QUOTES); ?>')">
                            <i title='Thêm vào giỏ hàng' class='glyphicon glyphicon-shopping-cart cart-item'></i>
                        </a>
                        <a class="snip0050" onclick="addtocart_action('<?php echo htmlspecialchars($row['masp']); ?>',true)"
                            <!-- href='order.php?masp=<?php echo htmlspecialchars($row['masp']); ?>' -->
                            > <span>Mua ngay</span><i class="glyphicon glyphicon-ok"></i>
                        </a>
                    </div>
                </div>
            </a>
        </div>
    <?php
    }
    disconnect($conn);

    // Câu truy vấn SQL để sử dụng khi tải thêm
    $_SESSION['sql'] = "SELECT * FROM sanpham sp INNER JOIN danhmucsp dm ON sp.madm = dm.madm ORDER BY sp.khuyenmai DESC";
    ?>
    <div class="container-fluid text-center">
        <button onclick="load_more(0)" id="loadmorebtn" class="snip1582">Load more</button>
    </div>
    <?php
}

function php_new()
{
    session_start();
    $conn = connect();
    mysqli_set_charset($conn, 'utf8');

    // Kiểm tra $_SESSION['limit'] trước khi sử dụng
    if (!isset($_SESSION['limit']) || !is_numeric($_SESSION['limit'])) {
        $_SESSION['limit'] = 10; // Giá trị mặc định nếu chưa được thiết lập
    }

    // Khởi tạo các biến session nếu chưa tồn tại
    if (!isset($_SESSION['like'])) {
        $_SESSION['like'] = [];
    }
    if (!isset($_SESSION['client_cart'])) {
        $_SESSION['client_cart'] = [];
    }
    if (!isset($_SESSION['user_cart'])) {
        $_SESSION['user_cart'] = [];
    }
    if (!isset($_SESSION['rights'])) {
        $_SESSION['rights'] = 'default'; // Hoặc giá trị mặc định bạn muốn
    }

    $sql = "SELECT * FROM sanpham sp, danhmucsp dm WHERE sp.madm = dm.madm ORDER BY sp.ngay_nhap DESC LIMIT " . intval($_SESSION['limit']);
    $result = mysqli_query($conn, $sql);

    // Kiểm tra xem truy vấn có thành công không
    if (!$result) {
        echo "Lỗi truy vấn: " . mysqli_error($conn);
        disconnect($conn);
        return; // Dừng thực thi nếu có lỗi
    }

    // Kiểm tra xem có sản phẩm nào không
    if (mysqli_num_rows($result) === 0) {
        echo "Không có sản phẩm nào.";
        disconnect($conn);
        return; // Dừng thực thi nếu không có sản phẩm
    }

    while ($row = mysqli_fetch_assoc($result)) {
    ?>
        <div class='product-container' onclick="hien_sanpham('<?php echo htmlspecialchars($row['masp'], ENT_QUOTES); ?>')">
            <a data-toggle='modal' href='sanpham.php?masp=<?php echo htmlspecialchars($row['masp'], ENT_QUOTES); ?>'
                data-target='#modal-id'>
                <div style="text-align: center;" class='product-img'>
                    <img src='<?php echo htmlspecialchars($row['anhchinh'], ENT_QUOTES); ?>' alt='Hình sản phẩm'>
                </div>
                <div class='product-info'>
                    <h4><b><?php echo htmlspecialchars($row['tensp'], ENT_QUOTES); ?></b></h4>
                    <b class='price'>Giá: <?php echo htmlspecialchars($row['gia'], ENT_QUOTES); ?> VND</b>
                    <div class='buy'>
                        <a onclick="like_action('<?php echo htmlspecialchars($row['masp'], ENT_QUOTES); ?>')" class='btn btn-default btn-md unlike-container <?php
                                                                                                                                                                if ($_SESSION['rights'] == 'user' && in_array($row['masp'], $_SESSION['like'])) {
                                                                                                                                                                    echo 'liked';
                                                                                                                                                                }
                                                                                                                                                                ?>'>
                            <i class='glyphicon glyphicon-heart unlike'></i>
                        </a>
                        <a class='btn btn-primary btn-md cart-container <?php
                                                                        if (($_SESSION['rights'] == "default" && in_array($row['masp'], $_SESSION['client_cart'])) ||
                                                                            ($_SESSION['rights'] != "default" && in_array($row['masp'], $_SESSION['user_cart']))
                                                                        ) {
                                                                            echo 'cart-ordered';
                                                                        }
                                                                        ?>' data-masp='<?php echo htmlspecialchars($row['masp'], ENT_QUOTES); ?>'
                            onclick="addtocart_action('<?php echo htmlspecialchars($row['masp'], ENT_QUOTES); ?>')">
                            <i title='Thêm vào giỏ hàng' class='glyphicon glyphicon-shopping-cart cart-item'></i>
                        </a>

                        <a class="snip0050" onclick="addtocart_action('<?php echo htmlspecialchars($row['masp']); ?>',true)"
                            <!-- href='order.php?masp=<?php echo htmlspecialchars($row['masp']); ?>' -->
                            > <span>Mua ngay</span>
                            <i class="glyphicon glyphicon-ok"></i>
                            </>
                    </div>
                </div>
            </a>
        </div>
    <?php
    }




    disconnect($conn);

    // Cập nhật SQL vào session
    $_SESSION['sql'] = "SELECT * FROM sanpham sp, danhmucsp dm WHERE sp.madm = dm.madm ORDER BY sp.ngay_nhap DESC";
    ?>
    <div class="container-fluid text-center">
        <button onclick="load_more(0)" id="loadmorebtn" class="snip1582">Load more</button>
    </div>
    <?php
}





function php_buy()
{
    session_start();
    $conn = connect();
    mysqli_set_charset($conn, 'utf8');

    // Kiểm tra $_SESSION['limit'] trước khi sử dụng
    if (!isset($_SESSION['limit']) || !is_numeric($_SESSION['limit'])) {
        $_SESSION['limit'] = 10; // Giá trị mặc định nếu chưa được thiết lập
    }

    // Danh sách các mã sản phẩm bạn muốn truy vấn
    $masp_list = [21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31, 32]; // Thay [1, 2, 3] bằng các mã sản phẩm bạn cần lấy

    // Chuyển đổi danh sách `masp` thành chuỗi để sử dụng trong SQL
    $masp_str = implode(",", array_map('intval', $masp_list));

    // Câu truy vấn để lấy sản phẩm theo `masp` cụ thể và giới hạn theo lượt mua
    $sql = "SELECT * FROM sanpham sp, danhmucsp dm 
            WHERE sp.madm = dm.madm AND sp.masp IN ($masp_str) 
            ORDER BY sp.luotmua DESC 
            LIMIT " . intval($_SESSION['limit']);
    $result = mysqli_query($conn, $sql);

    // Kiểm tra xem truy vấn có thành công không
    if (!$result) {
        echo "Lỗi truy vấn: " . mysqli_error($conn);
        disconnect($conn);
        return; // Dừng thực thi nếu có lỗi
    }

    while ($row = mysqli_fetch_assoc($result)) {
    ?>
        <div class='product-container' onclick="hien_sanpham('<?php echo htmlspecialchars($row['masp']); ?>')">
            <a data-toggle='modal' href='sanpham.php?masp=<?php echo htmlspecialchars($row['masp']); ?>'
                data-target='#modal-id'>
                <div style="text-align: center;" class='product-img'>
                    <img src='<?php echo htmlspecialchars($row['anhchinh']); ?>' alt='Hình sản phẩm'>
                </div>
                <div class='product-info'>
                    <h4><b><?php echo htmlspecialchars($row['tensp']); ?></b></h4>
                    <b class='price'>Giá: <?php echo htmlspecialchars($row['gia']); ?> VND</b>
                    <div class='buy'>
                        <a onclick="like_action('<?php echo htmlspecialchars($row['masp']); ?>')" class='btn btn-default btn-md unlike-container <?php
                                                                                                                                                    if ($_SESSION['rights'] == 'user' && in_array($row['masp'], $_SESSION['like'])) {
                                                                                                                                                        echo 'liked';
                                                                                                                                                    }
                                                                                                                                                    ?>'>
                            <i class='glyphicon glyphicon-heart unlike'></i>
                        </a>
                        <a class='btn btn-primary btn-md cart-container <?php
                                                                        if (($_SESSION['rights'] == "default" && in_array($row['masp'], $_SESSION['client_cart'])) ||
                                                                            ($_SESSION['rights'] != "default" && in_array($row['masp'], $_SESSION['user_cart']))
                                                                        ) {
                                                                            echo 'cart-ordered';
                                                                        }
                                                                        ?>' data-masp='<?php echo htmlspecialchars($row['masp'], ENT_QUOTES); ?>'
                            onclick="addtocart_action('<?php echo htmlspecialchars($row['masp'], ENT_QUOTES); ?>')">
                            <i title='Thêm vào giỏ hàng' class='glyphicon glyphicon-shopping-cart cart-item'></i>
                        </a>
                        <a class="snip0050" onclick="addtocart_action('<?php echo htmlspecialchars($row['masp']); ?>',true)"
                            <!-- href='order.php?masp=<?php echo htmlspecialchars($row['masp']); ?>' -->
                            ><span>Mua ngay</span>
                            <i class="glyphicon glyphicon-ok"></i>
                        </a>
                    </div>
                </div>
            </a>
        </div>
    <?php
    }
    disconnect($conn);
    $_SESSION['sql'] = "SELECT * FROM sanpham sp, danhmucsp dm WHERE sp.madm = dm.madm AND sp.masp IN ($masp_str) ORDER BY sp.luotmua DESC";
    ?>
    <div class="container-fluid text-center">
        <button onclick="load_more(0)" id="loadmorebtn" class="snip1582">Load more</button>
    </div>
    <?php
}




//Danh muc san pham
function php_danhmucsp()
{
    session_start();
    $conn = connect();
    mysqli_set_charset($conn, 'utf8');
    $detail = "";

    if (isset($_GET['detail'])) {
        $detail = strtolower($_GET['detail']);
    }

    $sql = "";

    switch ($detail) {
        case 'all':
            $sql = "SELECT * FROM sanpham sp, danhmucsp dm WHERE sp.madm = dm.madm ORDER BY sp.gia ASC";
            break;

        case 'ao_khoac':
            $sql = "SELECT * FROM sanpham sp, danhmucsp dm 
                    WHERE sp.madm = dm.madm 
                    AND sp.masp IN (
                        SELECT masp FROM sanpham 
                        WHERE madm IN (
                            SELECT madm FROM danhmucsp WHERE tendm = 'Áo Khoác'
                        )
                    ) 
                    ORDER BY sp.gia ASC";
            break;

        case 'ao_thun':
            $sql = "SELECT * FROM sanpham sp, danhmucsp dm 
                    WHERE sp.madm = dm.madm 
                    AND sp.masp IN (
                        SELECT masp FROM sanpham 
                        WHERE madm IN (
                            SELECT madm FROM danhmucsp WHERE tendm = 'Áo Thun'
                        )
                    ) 
                    ORDER BY sp.gia ASC";
            break;

        case 'ao_so_mi':
            $sql = "SELECT * FROM sanpham sp, danhmucsp dm 
                    WHERE sp.madm = dm.madm 
                    AND sp.masp IN (
                        SELECT masp FROM sanpham 
                        WHERE madm IN (
                            SELECT madm FROM danhmucsp WHERE tendm = 'Áo Sơ Mi'
                        )
                    ) 
                    ORDER BY sp.gia ASC";
            break;

        case 'ao_hoodie':
            $sql = "SELECT * FROM sanpham sp, danhmucsp dm 
                    WHERE sp.madm = dm.madm 
                    AND sp.madm IN (
                        SELECT madm FROM danhmucsp WHERE tendm = 'Áo Hoodie'
                    ) 
                    ORDER BY sp.gia ASC";
            break;

        case 'quan':
            $sql = "SELECT * FROM sanpham sp, danhmucsp dm 
                    WHERE sp.madm = dm.madm 
                    AND sp.masp IN (
                        SELECT masp FROM sanpham 
                        WHERE madm IN (
                            SELECT madm FROM danhmucsp WHERE tendm = 'Quần'
                        )
                    ) 
                    ORDER BY sp.gia ASC";
            break;

        case 'dam':
            $sql = "SELECT * FROM sanpham sp, danhmucsp dm 
                    WHERE sp.madm = dm.madm 
                    AND sp.masp IN (
                        SELECT masp FROM sanpham 
                        WHERE madm IN (
                            SELECT madm FROM danhmucsp WHERE tendm = 'Đầm'
                        )
                    ) 
                    ORDER BY sp.gia ASC";
            break;

        case 'phu_kien':
            $sql = "SELECT * FROM sanpham sp, danhmucsp dm 
                    WHERE sp.madm = dm.madm 
                    AND sp.masp IN (
                        SELECT masp FROM sanpham 
                        WHERE madm IN (
                            SELECT madm FROM danhmucsp WHERE tendm = 'Phụ Kiện'
                        )
                    ) 
                    ORDER BY sp.gia ASC";
            break;

        default:
            echo "<p>Danh mục không hợp lệ.</p>";
            disconnect($conn);
            return;
    }


    // Lưu lại truy vấn SQL ban đầu
    $sqlx = $sql;
    // Giới hạn số lượng kết quả hiển thị
    $sql .= " LIMIT " . $_SESSION['limit'];

    // Thực hiện truy vấn SQL
    $result = mysqli_query($conn, $sql);

    // Kiểm tra xem truy vấn có thành công không
    if (!$result) {
        error_log("Lỗi truy vấn: " . mysqli_error($conn)); // Ghi log lỗi vào file log
        echo "Lỗi truy vấn: " . mysqli_error($conn);
        disconnect($conn);
        return; // Dừng thực thi nếu có lỗi
    }

    // Kiểm tra xem có sản phẩm nào không
    if (mysqli_num_rows($result) == 0) {
        echo "<p>Không có sản phẩm nào trong danh mục này.</p>";
    } else {
        echo "<h3>Danh mục sản phẩm / " . ucwords($detail) . "</h3>";

        // Lặp qua kết quả và hiển thị từng sản phẩm
        while ($row = mysqli_fetch_assoc($result)) {
    ?>
            <div class='product-container' onclick="hien_sanpham('<?php echo htmlspecialchars($row['masp'], ENT_QUOTES); ?>')">
                <a data-toggle='modal' href='sanpham.php?masp=<?php echo htmlspecialchars($row['masp'], ENT_QUOTES); ?>'
                    data-target='#modal-id'>
                    <div style="text-align: center;" class='product-img'>
                        <img src='<?php echo htmlspecialchars($row['anhchinh'], ENT_QUOTES); ?>' alt='Hình sản phẩm'>
                    </div>
                    <div class='product-info'>
                        <h4><b><?php echo htmlspecialchars($row['tensp'], ENT_QUOTES); ?></b></h4>
                        <b class='price'>Giá: <?php echo number_format(htmlspecialchars($row['gia'], ENT_QUOTES), 0, ',', '.'); ?>
                            VND</b>
                        <div class='buy'>
                            <a onclick="like_action('<?php echo htmlspecialchars($row['masp'], ENT_QUOTES); ?>')" class='btn btn-default btn-md unlike-container <?php
                                                                                                                                                                    if ($_SESSION['rights'] == 'user' && in_array($row['masp'], $_SESSION['like'])) {
                                                                                                                                                                        echo 'liked';
                                                                                                                                                                    }
                                                                                                                                                                    ?>'>
                                <i class='glyphicon glyphicon-heart unlike'></i>
                            </a>
                            <a class='btn btn-primary btn-md cart-container <?php
                                                                            if (($_SESSION['rights'] == "default" && in_array($row['masp'], $_SESSION['client_cart'])) ||
                                                                                ($_SESSION['rights'] != "default" && in_array($row['masp'], $_SESSION['user_cart']))
                                                                            ) {
                                                                                echo 'cart-ordered';
                                                                            }
                                                                            ?>' data-masp='<?php echo htmlspecialchars($row['masp'], ENT_QUOTES); ?>'
                                onclick="addtocart_action('<?php echo htmlspecialchars($row['masp'], ENT_QUOTES); ?>')">
                                <i title='Thêm vào giỏ hàng' class='glyphicon glyphicon-shopping-cart cart-item'></i>
                            </a>
                            <a class="snip0050" onclick="addtocart_action('<?php echo htmlspecialchars($row['masp']); ?>',true)"
                                <!-- href='order.php?masp=<?php echo htmlspecialchars($row['masp']); ?>' -->
                                > <span>Mua ngay</span>
                                <i class="glyphicon glyphicon-ok"></i>
                            </a>
                        </div>
                    </div>
                </a>
            </div>
    <?php
        }
    }

    // Đóng kết nối
    disconnect($conn);
    // Lưu lại truy vấn SQL ban đầu vào session
    $_SESSION['sql'] = $sqlx;
    ?>
    <div class="container-fluid text-center">
        <button onclick="load_more(0)" id="loadmorebtn" class="snip1582">Load more</button>
    </div>
<?php
}
function php_dangky()
{
    require_once 'signUp.php';
}
function php_dangnhap()
{
    require_once 'signIn.php';
}
function php_giohang()
{
?>
    <div class="container-fluid form" style="padding: 20px">
        <div class="row">
            <div class="col-sm-3">
            </div>
            <div class="col-sm-6">
                <legend>
                    <h2>Giỏ hàng của bạn</h2>
                </legend>

                <?php
                session_start();
                $user = isset($_SESSION['user']) ? $_SESSION['user'] : null;

                // Kiểm tra xem có session người dùng hay không
                if ($user) {
                    $conn = connect();
                    mysqli_set_charset($conn, 'utf8');
                    $user_id = $user['id'];

                    $sql = "SELECT
                                    lsmh.id AS id,
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


                    if ($result && mysqli_num_rows($result) > 0) {
                        while ($row = mysqli_fetch_assoc($result)) { ?>
                            <a data-toggle='modal' href="sanpham.php?masp=<?php echo htmlspecialchars($row['masp']); ?>"
                                data-target='#modal-id'>
                                <div class='prd-in-cart' onclick="hien_sanpham('<?php echo $row['masp'] ?>')">
                                    <!-- <div class='prd-in-cart'> -->
                                    <img src="<?php echo $row['hinhanh'] ?>">
                                    <div class='prd-dt'>
                                        <h3><b><?php echo $row['tensp'] ?></b></h3>
                                        <span class='prd-price'><?php echo number_format($row['gia'], 0, ',', ' ') ?> VND</span>
                                    </div>
                                </div>
                            </a>
                    <?php }
                    } else {
                        echo "<h4>Giỏ hàng trống</h4>";
                        echo "<i>Ây da, bạn phải bỏ hàng vào giỏ đã chứ :) Help me!!</i>";
                        return 0;
                    }
                    ?>

                    <a href="order.php" class="btn btn-success btn-block"
                        style="color: white; font-size: 27px; margin-bottom: 10px;">Đặt Hàng</a>
            </div>
        </div>
    </div>
<?php
                }
            }


            function php_like()
            {
?>

<div class="container-fluid form" style="margin-top: -23px; padding: 20px">
    <div class="row">
        <div class="col-sm-3">
        </div>
        <div class="col-sm-6">
            <legend>
                <h2>SẢN PHẨM YÊU THÍCH</h2>
            </legend>

            <?php
                session_start();
                if (isset($_SESSION['user'])) {
                    $conn = connect();
                    mysqli_set_charset($conn, 'utf8');
                    $tmpArr = $_SESSION['like'];

                    array_shift($tmpArr);
                    $tmpArr = array_unique($tmpArr);
                    $x = '(' . implode(',', $tmpArr) . ')';
                    $sql = "SELECT * FROM sanpham  WHERE masp IN " . $x . "";
                    $result = mysqli_query($conn, $sql);
                    if ($x == '()') {
                        echo "<h4>BẠN CHƯA THÍCH SẢN PHẨM NÀO!</h4>";
                        echo "<i>Quay lại trang chủ và thả tym :)</i>";
                        return 0;
                    }
                    while ($row = mysqli_fetch_assoc($result)) { ?>
                    <a data-toggle='modal' href="sanpham.php?masp=<?php echo $row['masp'] ?>" data-target='#modal-id'>
                        <div class='prd-in-cart' onclick="hien_sanpham('<?php echo $row['masp'] ?>')">
                            <img src="<?php echo $row['anhchinh'] ?>">
                            <div class='prd-dt'>
                                <h3><b><?php echo $row['tensp'] ?></b></h3>
                                <span class='prd-price'><?php echo $row['gia'] ?></span>
                                <a href="order.php?masp=<?php echo $row['masp'] ?>" class="btn btn-success">Mua ngay</a>
                            </div>
                        </div>
                    </a>
                <?php
                    }
                ?>

                <a href="order.php?q=buylikepr" class="btn btn-success btn-block"
                    style="color: white;font-size: 27px; margin-bottom: 10px;">Mua tất cả</a>
        </div>
    </div>
</div>
<?php

                } else {
?>
    <i>Xin lỗi, bạn phải <a onclick="ajax_dangnhap()">đăng nhập</a> để xem những sản phẩm yêu thích của mình! Nếu chưa có
        tài khoản, hãy <a onclick="ajax_dangky()">đăng ký ngay</a></i>
<?php
                }
?>
</div>
</div>
</div>
<?php
            }


            function php_search()
            {
                $s = $_GET['s'];
                $conn = connect();
                mysqli_set_charset($conn, 'utf8');


                $sql = "SELECT * FROM sanpham sp
            JOIN danhmucsp dm ON sp.madm = dm.madm
            WHERE sp.tensp LIKE '%" . mysqli_real_escape_string($conn, $s) . "%'";

                $result = mysqli_query($conn, $sql);

                if (!$result) {
                    echo "Lỗi truy vấn: " . mysqli_error($conn);
                    disconnect($conn);
                    return;
                }

                echo "<h4>Kết quả tìm kiếm cho: " . htmlspecialchars($s) . "</h4>";
                if (mysqli_num_rows($result) == 0) {
                    echo "<i>Không có mặt hàng này</i>";
                }

                if (mysqli_num_rows($result) > 0) {
                    while ($row = mysqli_fetch_assoc($result)) {
                        $masp = htmlspecialchars($row['masp'] ?? '');
                        $anhchinh = htmlspecialchars($row['anhchinh'] ?? '');
                        $tensp = htmlspecialchars($row['tensp'] ?? '');
                        $gia = htmlspecialchars($row['gia'] ?? 0);
?>
        <div class='product-container' onclick="hien_sanpham('<?php echo $masp; ?>')">
            <a data-toggle='modal' href='sanpham.php?masp=<?php echo $masp; ?>' data-target='#modal-id'>
                <div style="text-align: center;" class='product-img'>
                    <img src='<?php echo $anhchinh; ?>' alt="<?php echo $tensp; ?>">
                </div>
                <div class='product-info'>
                    <h4><b><?php echo $tensp; ?></b></h4>
                    <b class='price'>Giá: <?php echo number_format($gia, 0, ',', '.'); ?> VND</b>
                    <div class='buy'>
                        <a onclick="like_action('<?php echo $masp; ?>')" class='btn btn-default btn-md unlike-container <?php
                                                                                                                        if (isset($_SESSION['rights']) && $_SESSION['rights'] == 'user' && isset($_SESSION['like']) && in_array($masp, $_SESSION['like'])) {
                                                                                                                            echo 'liked';
                                                                                                                        }
                                                                                                                        ?>'>
                            <i class='glyphicon glyphicon-heart unlike'></i>
                        </a>
                        <a class='btn btn-primary btn-md cart-container <?php
                                                                        if (($_SESSION['rights'] == "default" && in_array($row['masp'], $_SESSION['client_cart'])) ||
                                                                            ($_SESSION['rights'] != "default" && in_array($row['masp'], $_SESSION['user_cart']))
                                                                        ) {
                                                                            echo 'cart-ordered';
                                                                        }
                                                                        ?>' data-masp='<?php echo htmlspecialchars($row['masp'], ENT_QUOTES); ?>'
                            onclick="addtocart_action('<?php echo htmlspecialchars($row['masp'], ENT_QUOTES); ?>')">
                            <i title='Thêm vào giỏ hàng' class='glyphicon glyphicon-shopping-cart cart-item'></i>
                        </a>

                        <a class="snip0050" onclick="addtocart_action('<?php echo htmlspecialchars($row['masp']); ?>',true)"
                            <!-- href='order.php?masp=<?php echo htmlspecialchars($row['masp']); ?>' -->
                            ><span>Mua ngay</span><i class="glyphicon glyphicon-ok"></i>
                        </a>
                    </div>
                </div>
            </a>
        </div>
<?php
                    }
                }
                disconnect($conn);
            }
?>

<script src="cart.js"></script> <!-- Đường dẫn tới file cart.js -->