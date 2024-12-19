<?php

require_once 'backend-index.php';


$fname = "";
if(isset($_GET['fname'])){
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
	case 'php_search':
	php_search();
	break;
	case 'load_more':
	load_more();
	break;
}
function load_more() {
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
                                    ($_SESSION['rights'] != "default" && in_array($row['masp'], $_SESSION['user_cart']))) {
                                    echo 'cart-ordered';
                                }
                            ?>' onclick="addtocart_action('<?php echo htmlspecialchars($row['masp']); ?>')">
                    <i title='Thêm vào giỏ hàng' class='glyphicon glyphicon-shopping-cart cart-item'></i>
                </a>
                <a class="snip0050" href='order.php?masp=<?php echo htmlspecialchars($row['masp']); ?>'>
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




function php_saling() {
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
                                        ($_SESSION['rights'] != "default" && in_array($row['masp'], $_SESSION['user_cart']))) {
                                        echo 'cart-ordered';
                                    }
                                ?>' data-masp='<?php echo htmlspecialchars($row['masp'], ENT_QUOTES); ?>'
                    onclick="addtocart_action('<?php echo htmlspecialchars($row['masp'], ENT_QUOTES); ?>')">
                    <i title='Thêm vào giỏ hàng' class='glyphicon glyphicon-shopping-cart cart-item'></i>
                </a>
                <a class="snip0050" href='order.php?masp=<?php echo htmlspecialchars($row['masp'], ENT_QUOTES) ?>'>
                    <span>Mua ngay</span><i class="glyphicon glyphicon-ok"></i>
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

function php_new() {
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
                                        ($_SESSION['rights'] != "default" && in_array($row['masp'], $_SESSION['user_cart']))) {
                                        echo 'cart-ordered';
                                    }
                                ?>' data-masp='<?php echo htmlspecialchars($row['masp'], ENT_QUOTES); ?>'
                    onclick="addtocart_action('<?php echo htmlspecialchars($row['masp'], ENT_QUOTES); ?>')">
                    <i title='Thêm vào giỏ hàng' class='glyphicon glyphicon-shopping-cart cart-item'></i>
                </a>
                <a class="snip0050" href='order.php?masp=<?php echo htmlspecialchars($row['masp'], ENT_QUOTES); ?>'>
                    <span>Mua ngay</span>
                    <i class="glyphicon glyphicon-ok"></i>
                </a>
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





function php_buy() {
    session_start();
    $conn = connect();
    mysqli_set_charset($conn, 'utf8');

    // Kiểm tra $_SESSION['limit'] trước khi sử dụng
    if (!isset($_SESSION['limit']) || !is_numeric($_SESSION['limit'])) {
        $_SESSION['limit'] = 10; // Giá trị mặc định nếu chưa được thiết lập
    }

    // Danh sách các mã sản phẩm bạn muốn truy vấn
    $masp_list = [21,22,23,24,25,26,27,28,29,30,31,32]; // Thay [1, 2, 3] bằng các mã sản phẩm bạn cần lấy

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
                                        ($_SESSION['rights'] != "default" && in_array($row['masp'], $_SESSION['user_cart']))) {
                                        echo 'cart-ordered';
                                    }
                                ?>' data-masp='<?php echo htmlspecialchars($row['masp'], ENT_QUOTES); ?>'
                    onclick="addtocart_action('<?php echo htmlspecialchars($row['masp'], ENT_QUOTES); ?>')">
                    <i title='Thêm vào giỏ hàng' class='glyphicon glyphicon-shopping-cart cart-item'></i>
                </a>
                <a class="snip0050" href='order.php?masp=<?php echo htmlspecialchars($row['masp']); ?>'>
                    <span>Mua ngay</span>
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
function php_danhmucsp() {
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
                                        ($_SESSION['rights'] != "default" && in_array($row['masp'], $_SESSION['user_cart']))) {
                                        echo 'cart-ordered';
                                    }
                                ?>' data-masp='<?php echo htmlspecialchars($row['masp'], ENT_QUOTES); ?>'
                    onclick="addtocart_action('<?php echo htmlspecialchars($row['masp'], ENT_QUOTES); ?>')">
                    <i title='Thêm vào giỏ hàng' class='glyphicon glyphicon-shopping-cart cart-item'></i>
                </a>
                <a class="snip0050" href='order.php?masp=<?php echo htmlspecialchars($row['masp'], ENT_QUOTES); ?>'>
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


function php_dangky(){
	require_once 'signUp.php';
}
function php_dangnhap(){
	require_once 'signIn.php';
}
function php_giohang()
{
    ?>
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header text-center bg-primary text-white">
                    <h3>Giỏ hàng của bạn</h3>
                </div>
                <div class="card-body">
                    <?php
                        session_start();

                        // Lấy giỏ hàng từ session
                        $cart = [];
                        if (isset($_SESSION['user'])) {
                            $cart = $_SESSION['user_cart'] ?? [];
                        } elseif (isset($_SESSION['client_cart'])) {
                            $cart = $_SESSION['client_cart'] ?? [];
                        }

                        // Loại bỏ phần tử đầu tiên và loại bỏ trùng lặp
                        $cart = array_unique($cart);

                        // Kiểm tra giỏ hàng rỗng
                        if (empty($cart)) {
                            echo "<div class='alert alert-warning text-center'>Giỏ hàng của bạn đang trống!</div>";
                            echo "<p class='text-center'>Hãy thêm sản phẩm vào giỏ để tiếp tục mua sắm!</p>";
                            return;
                        }

                        $x = implode(',', array_map('intval', $cart)); // Chuyển phần tử thành số nguyên
                        $conn = connect();
                        mysqli_set_charset($conn, 'utf8');

                        // Truy vấn sản phẩm trong giỏ hàng
                        $sql = "SELECT * FROM sanpham WHERE masp IN ($x)";
                        $result = mysqli_query($conn, $sql);

                        if (!$result) {
                            echo "<div class='alert alert-danger'>Lỗi truy vấn: " . mysqli_error($conn) . "</div>";
                            return;
                        }

                        ?>
                    <form id="cart-form" method="POST" action="order.php?q=multi">
                        <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                        <div class="d-flex align-items-center border-bottom py-3">
                            <!-- Checkbox để chọn sản phẩm -->
                            <input type="checkbox" name="selected_products[]" value="<?php echo $row['masp']; ?>"
                                class="me-3">

                            <img src="<?php echo $row['anhchinh']; ?>" class="rounded" alt="Sản phẩm"
                                style="width: 80px; height: 80px; object-fit: cover; margin-right: 15px;">
                            <div class="flex-grow-1">
                                <h5 class="mb-1"><?php echo htmlspecialchars($row['tensp'], ENT_QUOTES, 'UTF-8'); ?>
                                </h5>
                                <p class="mb-0 text-muted">Giá:
                                    <strong><?php echo number_format($row['gia'], 0, ',', ' '); ?> VND</strong>
                                </p>
                            </div>
                            <button type="button" class="btn btn-danger btn-sm delete"
                                data-masp="<?php echo $row['masp']; ?>">
                                Xóa
                            </button>
                        </div>
                        <?php } ?>
                        <div class="mt-4 text-center">
                            <button type="submit" class="btn btn-success btn-lg px-5">Đặt Hàng</button>
                        </div>
                    </form>

                    <?php
                        ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
}


// Xử lý yêu cầu từ AJAX
if (isset($_GET['fname']) && $_GET['fname'] == 'php_giohangyeuthich') {
    php_giohangyeuthich();
}

function php_giohangyeuthich() {
    session_start();

    if (isset($_SESSION['user'])) {
        $conn = connect();
        mysqli_set_charset($conn, 'utf8');

        if (!isset($_SESSION['like']) || empty($_SESSION['like'])) {
            echo "<div style='text-align: center; margin-top: 50px;'>
                    <h4 style='color: #444;'>BẠN CHƯA THÍCH SẢN PHẨM NÀO!</h4>
                    <i style='color: #888;'>Quay lại trang chủ và thả tym :)</i>
                  </div>";
            return;
        }

        $tmpArr = $_SESSION['like'];
        array_shift($tmpArr);
        $tmpArr = array_unique($tmpArr);

        if (empty($tmpArr)) {
            echo "<div style='text-align: center; margin-top: 50px;'>
                    <h4 style='color: #444;'>BẠN CHƯA THÍCH SẢN PHẨM NÀO!</h4>
                    <i style='color: #888;'>Quay lại trang chủ và thả tym :)</i>
                  </div>";
            return;
        }

        $masp_list = implode(",", array_map('intval', $tmpArr));
        $sql = "SELECT sp.masp, sp.tensp, sp.anhchinh, sp.gia 
                FROM sanpham AS sp
                WHERE sp.masp IN ($masp_list)";
        $result = mysqli_query($conn, $sql);

        if (mysqli_num_rows($result) > 0) {
            ?>
<div style="max-width: 900px; margin: 50px auto; font-family: Arial, sans-serif;">
    <div class="card-header text-center bg-primary text-white"
        style="padding: 20px; border-radius: 10px 10px 0 0; font-size: 24px; font-weight: bold;">
        Giỏ hàng yêu thích
    </div>

    <div
        style="background: #f8f9fa; padding: 30px; border-radius: 0 0 10px 10px; box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);">
        <?php while ($row = mysqli_fetch_assoc($result)) { ?>
        <div class="d-flex align-items-center mb-4 p-3 rounded"
            style="background: #ffffff; box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);">
            <a href="sanpham.php?masp=<?php echo $row['masp']; ?>" class="d-flex align-items-center w-100"
                style="text-decoration: none; color: inherit;">
                <img src="<?php echo htmlspecialchars($row['anhchinh']); ?>" class="rounded" alt="Sản phẩm"
                    style="width: 100px; height: 100px; object-fit: cover; margin-right: 20px; border: 2px solid #ddd;">

                <div class="flex-grow-1">
                    <h5 class="mb-1" style="font-size: 20px; font-weight: bold; color: #333;">
                        <?php echo htmlspecialchars($row['tensp'], ENT_QUOTES, 'UTF-8'); ?>
                    </h5>
                    <p class="mb-0" style="color: #666;">
                        Giá: <strong style="color: #e74c3c; font-size: 18px;">
                            <?php echo number_format($row['gia'], 0, ',', '.'); ?> VND
                        </strong>
                    </p>
                </div>
            </a>

            <!-- Nút chuyển sản phẩm vào giỏ hàng -->
            <button type="button" class="btn btn-success btn-sm add-to-cart" data-masp="<?php echo $row['masp']; ?>"
                style="font-size: 14px; padding: 5px 10px;">
                Chuyển vào giỏ hàng
            </button>
            <!-- Nút xóa sản phẩm khỏi yêu thích -->
            <button type="button" class="btn btn-danger btn-sm delete-from-like" data-masp="<?php echo $row['masp']; ?>"
                style="font-size: 14px; padding: 5px 10px; margin-left: 10px;">
                Xóa
            </button>

        </div>
        <?php } ?>
    </div>
</div>
<?php
        } else {
            echo "<div style='text-align: center; margin-top: 50px;'>
                    <h4 style='color: #444;'>BẠN CHƯA THÍCH SẢN PHẨM NÀO!</h4>
                    <i style='color: #888;'>Quay lại trang chủ và thả tym :)</i>
                  </div>";
        }
    } else {
        echo "<div style='text-align: center; margin-top: 50px;'>
                <i style='color: #888;'>Xin lỗi, bạn phải <a onclick='ajax_dangnhap()' style='color: #007bff;'>đăng nhập</a> để xem những sản phẩm yêu thích của mình!
                Nếu chưa có tài khoản, hãy <a onclick='ajax_dangky()' style='color: #007bff;'>đăng ký ngay</a>.</i>
              </div>";
    }
}



function php_search() {
$s = $_GET['s'];
$conn = connect();
mysqli_set_charset($conn, 'utf8');

// Chuẩn hóa từ khóa tìm kiếm
$s = trim($s);
$s = mysqli_real_escape_string($conn, $s);

// Sử dụng REGEXP để tìm kiếm linh hoạt và chính xác
$sql = "SELECT * FROM sanpham sp
JOIN danhmucsp dm ON sp.madm = dm.madm
WHERE sp.tensp LIKE '% $s %'
OR sp.tensp LIKE '$s %'
OR sp.tensp LIKE '% $s'
OR sp.tensp LIKE '$s'";
$result = mysqli_query($conn, $sql);

if (!$result) {
echo "Lỗi truy vấn: " . mysqli_error($conn);
disconnect($conn);
return;
}

echo "<h4>Kết quả tìm kiếm cho: " . htmlspecialchars($s) . "</h4>";
if (mysqli_num_rows($result) == 0) {
echo "<i>Không tìm thấy sản phẩm phù hợp</i>";
}

// Hiển thị danh sách sản phẩm
while ($row = mysqli_fetch_assoc($result)) {
$masp = htmlspecialchars($row['masp'] ?? '');
$anhchinh = htmlspecialchars($row['anhchinh'] ?? '');
$tensp = htmlspecialchars($row['tensp'] ?? '');
$gia = htmlspecialchars($row['gia'] ?? 0);
?>
<div class='product-container'>
    <div class='product-img' onclick="hien_sanpham('<?php echo $masp; ?>')">
        <a href='sanpham.php?masp=<?php echo $masp; ?>' data-toggle='modal' data-target='#modal-id'>
            <img src='<?php echo $anhchinh; ?>' alt="<?php echo $tensp; ?>">
        </a>
    </div>
    <div class='product-info' style="padding: 20px; border-top: 1px solid #ddd; text-align: center;">
        <h4 style="margin-bottom: 10px;"><b><?php echo $tensp; ?></b></h4>
        <p class='price' style="color: #ff5722; font-size: 18px; margin-bottom: 15px;">
            Giá: <?php echo number_format($gia, 0, ',', '.'); ?> VND
        </p>
        <div class='buy' style="display: flex; justify-content: space-around; align-items: center;">
            <!-- Like Button -->
            <a onclick="like_action('<?php echo $masp; ?>')" class='btn btn-default btn-md unlike-container 
               <?php 
                   echo (isset($_SESSION['rights']) && $_SESSION['rights'] == 'user' && 
                         isset($_SESSION['like']) && in_array($masp, $_SESSION['like'])) ? 'liked' : ''; 
               ?>'
                style="background-color: #f8f9fa; color: #000; border: 1px solid #ddd; padding: 10px; border-radius: 5px;">
                <i class='glyphicon glyphicon-heart unlike'></i>
            </a>
            <!-- Add to Cart Button -->
            <a class='btn btn-primary btn-md cart-container 
               <?php 
                   if (isset($_SESSION['rights']) && $_SESSION['rights'] == "default" && 
                       isset($_SESSION['client_cart']) && in_array($masp, $_SESSION['client_cart'])) {
                       echo 'cart-ordered';
                   } elseif (isset($_SESSION['rights']) && $_SESSION['rights'] != "default" && 
                             isset($_SESSION['user_cart']) && in_array($masp, $_SESSION['user_cart'])) {
                       echo 'cart-ordered';
                   }
               ?>' data-masp='<?php echo $masp; ?>' onclick="addtocart_action('<?php echo $masp; ?>')"
                style="background-color: #007bff; color: white; padding: 10px; border-radius: 5px;">
                <i title='Thêm vào giỏ hàng' class='glyphicon glyphicon-shopping-cart cart-item'></i>
            </a>
            <!-- Buy Now Button -->
            <a class="snip0050" href='order.php?masp=<?php echo $masp; ?>'
                style="background-color: #28a745; color: white; padding: 10px; border-radius: 5px; text-decoration: none;">
                <span>Mua ngay</span>
                <i class="glyphicon glyphicon-ok"></i>
            </a>
        </div>
    </div>

</div>
<?php
    }

    disconnect($conn);
}


?>

<script src="cart.js"></script> <!-- Đường dẫn tới file cart.js -->