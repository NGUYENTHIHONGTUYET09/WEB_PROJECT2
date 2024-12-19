-- Tạo bảng categories để lưu trữ thông tin danh mục sản phẩm
CREATE TABLE IF NOT EXISTS danhmucsp (
    madm INT AUTO_INCREMENT PRIMARY KEY,   -- Khóa chính
    tendm VARCHAR(100) NOT NULL,         -- Tên danh mục
    xuatsu VARCHAR(100)                   -- Xuất xứ
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tạo bảng products để lưu trữ thông tin sản phẩm
CREATE TABLE IF NOT EXISTS sanpham (
    masp INT AUTO_INCREMENT PRIMARY KEY,    -- Khóa chính
    tensp VARCHAR(255) NOT NULL,          -- Tên sản phẩm
    gia DECIMAL(10, 2) NOT NULL,          -- Giá sản phẩm
    chatlieu VARCHAR(100),                 -- Chất liệu
    mau VARCHAR(50),                       -- Màu sắc
    danhcho VARCHAR(100),                  -- Dành cho
    khuyenmai VARCHAR(255),                -- Khuyến mãi
    madm INT,                              -- ID danh mục (khóa ngoại)
    anhchinh VARCHAR(255),                 -- Ảnh chính sản phẩm
    luotmua INT DEFAULT 0,                -- Số lượt mua
    luotxem INT DEFAULT 0,                 -- Số lượt xem
    ngaytao TIMESTAMP DEFAULT CURRENT_TIMESTAMP, -- Ngày tạo
    ngay_nhap TIMESTAMP DEFAULT CURRENT_TIMESTAMP, -- Ngày nhập sản phẩm
    FOREIGN KEY (madm) REFERENCES danhmucsp(madm) ON DELETE CASCADE -- Liên kết với bảng danhmuc
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tạo bảng giao dịch để lưu trữ thông tin giao dịch
CREATE TABLE IF NOT EXISTS giaodich (
    magd INT AUTO_INCREMENT PRIMARY KEY,        -- Khóa chính
    user_id INT,                               -- ID của người dùng
    user_name VARCHAR(255) NOT NULL,            -- Tên người dùng
    user_dst VARCHAR(100),                       -- Quận
    user_addr VARCHAR(255),                      -- Địa chỉ
    user_phone VARCHAR(15),                      -- Số điện thoại
    tongtien DECIMAL(10, 2) NOT NULL,           -- Tổng tiền
    date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,    -- Ngày giao dịch
    tinhtrang TINYINT DEFAULT 0                  -- Tình trạng giao dịch (0: chưa giao, 1: đã giao)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tạo bảng chitietgiaodich để lưu trữ thông tin chi tiết giao dịch
CREATE TABLE IF NOT EXISTS chitietgiaodich (
    id INT AUTO_INCREMENT PRIMARY KEY,           -- Khóa chính
    magd INT NOT NULL,                           -- ID giao dịch (khóa ngoại liên kết với bảng giaodich)
    masp INT NOT NULL,                           -- Mã sản phẩm
    soluong INT NOT NULL,                        -- Số lượng sản phẩm
    FOREIGN KEY (magd) REFERENCES giaodich(magd) ON DELETE CASCADE,
    FOREIGN KEY (masp) REFERENCES sanpham(masp) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tạo bảng thanhvien để lưu trữ thông tin thành viên
CREATE TABLE IF NOT EXISTS thanhvien (
    id INT AUTO_INCREMENT PRIMARY KEY,            -- Khóa chính
    ten VARCHAR(255) NOT NULL,                    -- Tên thành viên
    tentaikhoan VARCHAR(100) NOT NULL UNIQUE,     -- Tên tài khoản (độc nhất)
    matkhau VARCHAR(255) NOT NULL,                 -- Mật khẩu
    diachi TEXT,                                   -- Địa chỉ (tùy chọn)
    sodt VARCHAR(15),                              -- Số điện thoại (tùy chọn)
    email VARCHAR(100),                            -- Email (tùy chọn)
    ngaytao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,   -- Ngày tạo tài khoản
    quyen INT DEFAULT 0                           -- Quyền hạn (0: người dùng bình thường, 1: admin)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tạo bảng giohang để lưu trữ thông tin giỏ hàng
CREATE TABLE IF NOT EXISTS giohang (
    id INT AUTO_INCREMENT PRIMARY KEY,            -- Khóa chính
    user_id INT NOT NULL,                          -- ID của người dùng
    masp INT NOT NULL,                             -- Mã sản phẩm
    soluong INT NOT NULL DEFAULT 1,                -- Số lượng sản phẩm
    FOREIGN KEY (user_id) REFERENCES thanhvien(id) ON DELETE CASCADE,  -- Ràng buộc khóa ngoại
    FOREIGN KEY (masp) REFERENCES sanpham(masp) ON DELETE CASCADE          -- Ràng buộc khóa ngoại
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tạo bảng sanphamyeuthich để lưu trữ thông tin sản phẩm yêu thích
CREATE TABLE IF NOT EXISTS sanphamyeuthich (
    id INT AUTO_INCREMENT PRIMARY KEY,            -- Khóa chính
    user_id INT NOT NULL,                          -- ID của người dùng
    masp INT NOT NULL,                             -- Mã sản phẩm
    FOREIGN KEY (user_id) REFERENCES thanhvien(id) ON DELETE CASCADE,  -- Ràng buộc khóa ngoại
    FOREIGN KEY (masp) REFERENCES sanpham(masp) ON DELETE CASCADE          -- Ràng buộc khóa ngoại
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO danhmucsp (tendm, xuatsu) VALUES
('Áo Khoác', 'Việt Nam'),
('Áo Thun', 'Việt Nam'),
('Áo Sơ Mi', 'Việt Nam'),
('Áo Hoodie', 'Việt Nam'),
('Quần', 'Việt Nam'),
('Đầm', 'Việt Nam'),
('Phụ Kiện', 'Việt Nam')
ON DUPLICATE KEY UPDATE madm = madm; -- Không thêm nếu đã có

INSERT INTO sanpham (tensp, gia, chatlieu, mau, danhcho, khuyenmai, madm, anhchinh)
VALUES
    -- Áo Khoác
    ('Áo Khoác Bomber', 750000, 'Nylon', 'Đen', 'Nam', 'Giảm 10%', 1, 'images/ao_khoac/ao_khoac_bomber.jpg'),
    ('Áo Khoác Dạ Nữ', 900000, 'Dạ', 'Đen', 'Nữ', 'Giảm 20%', 1, 'images/ao_khoac/ao_khoac_da_nu.jpg'),
    ('Áo Khoác Denim', 600000, 'Denim', 'Xanh dương', 'Nam', 'Giảm 10%', 1, 'images/ao_khoac/ao_khoac_denim.jpg'),
    ('Áo Khoác Gió', 800000, 'Polyester', 'Xanh dương', 'Nam', 'Giảm 5%', 1, 'images/ao_khoac/ao_khoac_gio.jpg'),
    ('Áo Khoác Mùa Đông', 650000, 'Polyester', 'Xanh dương', 'Nam', 'Giảm 10%', 1, 'images/ao_khoac/ao_khoac_mua_dong.jpg'),
    ('Áo Khoác Phao', 700000, 'Polyester', 'Xám', 'Nam', 'Giảm 5%', 1, 'images/ao_khoac/ao_khoac_phao.jpeg'),

    -- Áo Thun
    ('Áo Polo Thể Thao', 350000, 'Cotton', 'Vàng', 'Nữ', 'Giảm 5%', 2, 'images/ao_thun/ao_polo_the_thao.jpg'),
    ('Áo Thun Cổ Tròn', 250000, 'Cotton', 'Vàng', 'Unisex', 'Giảm 5%', 2, 'images/ao_thun/ao_thun_co_tron.jpg'),
    ('Áo Thun Fit', 500000, 'Cotton', 'Xám', 'Nam', 'Giảm 5%', 2, 'images/ao_thun/ao_thun_fit.jpeg'),
    ('Áo Thun Nữ Cổ V', 300000, 'Cotton', 'Đen', 'Nữ', 'Giảm 5%', 2, 'images/ao_thun/ao_thun_nu_co_v.jpg'),
    ('Áo Thun Dài Tay', 400000, 'Cotton', 'Xanh rêu', 'Nam', 'Giảm 5%', 2, 'images/ao_thun/ao_thun_tay_dai.jpg'),
    ('Áo Thun Unisex', 350000, 'Cotton', 'Vàng', 'Nữ', 'Giảm 10%', 2, 'images/ao_thun/ao_tshirt_nu.jpg'),


    -- Áo Sơ Mi
    ('Áo Sơ Mi Caro', 400000, 'Cotton', 'Nhiều màu', 'Nam', 'Giảm 10%', 3, 'images/ao_so_mi/ao_so_mi_caro.jpg'),
    ('Áo Sơ Mi Cọc Tay', 500000, 'Cotton', 'Đen', 'Nam', 'Giảm 10%', 3, 'images/ao_so_mi/ao_so_mi_coc_tay.jpeg'),
    ('Áo Sơ Mi Dài Tay', 550000, 'Cotton', 'Trắng', 'Nam', 'Giảm 10%', 3, 'images/ao_so_mi/ao_so_mi_dai_tay.jpeg'),
    ('Áo Sơ Mi Kẻ Sọc', 400000, 'Cotton', 'Xanh dương', 'Nam', 'Giảm 10%', 3, 'images/ao_so_mi/ao_so_mi_ke_soc.jpg'),
    ('Áo Sơ Mi Nữ', 350000, 'Cotton', 'Trắng', 'Nữ', 'Giảm 10%', 3, 'images/ao_so_mi/ao_so_mi_nu.jpg'),
    ('Áo Sơ Mi Trắng', 450000, 'Cotton', 'Trắng', 'Nữ', 'Giảm 20%', 3, 'images/ao_so_mi/ao_so_mi_trang.jpg'),

    -- Áo Hoodie
    ('Áo Hoodie Da Lộn', 450000, 'Da Lộn', 'Nâu', 'Nam', 'Giảm 5%', 4, 'images/ao_hoodie/ao_hoodie_da_lon.jpeg'),
    ('Áo Hoodie Jack Lane Mix', 600000, 'Nỉ', 'Trắng đen', 'Nam', 'Giảm 5%', 4, 'images/ao_hoodie/ao_hoodie_jack_lane_mix.jpg'),
    ('Áo Hoodie Just Odin', 500000, 'Nỉ', 'Trắng', 'Nam', 'Giảm 5%', 4, 'images/ao_hoodie/ao_hoodie_just_odin.jpg'),
    ('Áo Hoodie Lông Cừu', 500000, 'Lông cừu', 'Nâu', 'Nữ', 'Giảm 5%', 4, 'images/ao_hoodie/ao_hoodie_long_cuu.jpeg'),
    ('Áo Hoodie Nỉ', 700000, 'Nỉ', 'Trắng', 'Nam', 'Giảm 15%', 4, 'images/ao_khoac/ao_hoodie_ni.jpg'),
    ('Áo Hoodie OD Bestle', 650000, 'Nỉ', 'Đen', 'Nam', 'Giảm 5%', 4, 'images/ao_hoodie/ao_hoodie_od_bestle.jpg'),

    -- Quần
    ('Quần Baggy Nữ', 250000, 'Polyester', 'Xanh dương', 'Nữ', 'Giảm 5%', 5, 'images/quan/quan_baggy_nu.jpg'),
    ('Quần Jeans Rách', 500000, 'Jean', 'Xanh dương', 'Nữ', 'Giảm 15%', 5, 'images/quan/quan_jeans_rach.jpg'),
    ('Quần Jeans Skinny', 550000, 'Jean', 'Xanh nhạt', 'Nam', 'Giảm 15%', 5, 'images/quan/quan_jeans_skinny.jpg'),
    ('Quần Jogger Thể Thao', 400000, 'Cotton', 'Trắng, Đen', 'Nam', 'Giảm 15%', 5, 'images/quan/quan_jogger_the_thao.jpg'),
    ('Quần Kaki Nữ', 500000, 'Kaki', 'Trắng', 'Nữ', 'Giảm 15%', 5, 'images/quan/quan_kaki_nu.jpg'),
    ('Quần Legging Nữ', 250000, 'Polyester', 'Xám', 'Nữ', 'Giảm 5%', 5, 'images/quan/quan_legging_nu.jpg'),
    ('Quần Short Thể Thao', 400000, 'Cotton', 'Xanh Dương', 'Nam', 'Giảm 15%', 5, 'images/quan/quan_short_the_thao.jpg'),
    ('Quần Tây Nam', 700000, 'Kaki', 'Đen', 'Nam', 'Giảm 15%', 5, 'images/quan/quan_tay_nam.jpg'),

    -- Đầm
    ('Đầm Dự Tiệc', 900000, 'Lụa', 'Be', 'Nữ', 'Giảm 10%', 6, 'images/dam/dam_da_hoi.jpg'),
    ('Đầm Maxi Hoa', 800000, 'Lụa', 'Be', 'Nữ', 'Giảm 20%', 6, 'images/dam/dam_maxi_hoa.jpg'),
    ('Đầm Maxi Trễ Vai', 600000, 'Cotton', 'Nhiều màu', 'Nữ', 'Giảm 15%', 6, 'images/dam/dam_maxi_tre_vai.jpg'),
    ('Đầm Suông Trẻ Trung', 600000, 'Cotton', 'Xanh lá nhạt', 'Nữ', 'Giảm 15%', 6, 'images/dam/dam_suong_tre_trung.jpg'),
    ('Đầm Xòe Công Sở', 900000, 'Lụa', 'Hồng', 'Nữ', 'Giảm 20%', 6, 'images/dam/dam_xoe_cong_so.webp'),
    ('Đầm Xòe Vintage', 800000, 'Lụa', 'Nhiều màu', 'Nữ', 'Giảm 20%', 6, 'images/dam/dam_xoe_vintage.jpg'),

    -- Phụ Kiện
    ('Dây Chuyền Pandora', 800000, 'Bạc', 'Trắng', 'Nữ', 'Giảm 10%', 7, 'images/phu_kien/day_chuyen_pandora.webp'),
    ('Kính Mát Thời Trang', 300000,  'Nhựa', 'Đen','Unisex', 'Giảm 15%', 7,'images/phu_kien/kinh_mat_thoi_trang.jpg'),
('Lắc Tay Bạc', 700000, 'Bạc', 'Trắng', 'Nữ', 'Giảm 10%',7,'images/phu_kien/lac_tay_bac.jpg'),
('Nón Lưỡi Trai', 200000,'Cotton', 'Đen', 'Unisex','Giảm 10%',7,'images/phu_kien/non_luoi_trai.jpg'),
('Thắt Lưng Da', 250000, 'Da', 'Đen', 'Unisex', 'Giảm 5%', 7,'images/phu_kien/that_lung_da.jpg'),
('Vòng Tay Thời Trang', 150000, 'Kim loại','Vàng', 'Nữ','Giảm 10%',7,'images/phu_kien/vong_tay_thoi_trang.png');

INSERT INTO thanhvien (ten, tentaikhoan, matkhau, diachi, sodt, email, quyen) VALUES
('Nguyễn Văn A', 'nguyenvana', 'password123', '123 Đường ABC, Hà Nội', '0901234567', 'a@example.com', 0),
('Trần Thị B', 'tranthib', 'password456', '456 Đường DEF, Hồ Chí Minh', '0912345678', 'b@example.com', 0),
('Lê Văn C', 'levanc', 'password789', '789 Đường GHI, Đà Nẵng', '0923456789', 'c@example.com', 1),
('Phạm Minh D', 'phaminhd', 'password101', '321 Đường JKL, Hà Nội', '0934567890', 'd@example.com', 0),
('Nguyễn Thị E', 'nguyenthie', 'password202', '654 Đường MNO, Hồ Chí Minh', '0945678901', 'e@example.com', 1),
('Trần Văn F', 'tranvanf', 'password303', '987 Đường PQR, Đà Nẵng', '0956789012', 'f@example.com', 0)
ON DUPLICATE KEY UPDATE id=id; -- Không thêm nếu đã có



INSERT INTO giaodich (user_id, user_name, user_dst, user_addr, user_phone, tongtien, tinhtrang) VALUES
(1, 'Nguyễn Văn A', 'Hà Nội', '123 Đường ABC', '0901234567', 25000000.00, 1),  
(2, 'Trần Thị B', 'Hồ Chí Minh', '456 Đường DEF', '0912345678', 1500000.00, 1),  
(3, 'Lê Văn C', 'Đà Nẵng', '789 Đường GHI', '0923456789', 10000000.00, 1),      
(1, 'Nguyễn Văn A', 'Hà Nội', '123 Đường ABC', '0901234567', 3000000.00, 0),   
(2, 'Trần Thị B', 'Hồ Chí Minh', '456 Đường DEF', '0912345678', 4000000.00, 0),  
(3, 'Lê Văn C', 'Đà Nẵng', '789 Đường GHI', '0923456789', 3500000.00, 1)

INSERT INTO giaodich (user_id, user_name, user_dst, user_addr, user_phone, tongtien, date, tinhtrang) VALUES
(1, 'Nguyễn Văn A', 'Hà Nội', '123 Đường ABC, Hà Nội', '0901234567', 1500000.00, '2024-11-30 10:00:00', 1),
(2, 'Trần Thị B', 'Hồ Chí Minh', '456 Đường DEF, Hồ Chí Minh', '0912345678', 1200000.00, '2024-10-30 11:00:00', 1),
(3, 'Lê Văn C', 'Đà Nẵng', '789 Đường GHI, Đà Nẵng', '0923456789', 2000000.00, '2024-04-30 12:00:00', 1),
(4, 'Phạm Minh D', 'Hà Nội', '321 Đường JKL, Hà Nội', '0934567890', 2500000.00, '2024-08-30 13:00:00', 1),
(5, 'Nguyễn Thị E', 'Hồ Chí Minh', '654 Đường MNO, Hồ Chí Minh', '0945678901', 1800000.00, '2024-10-30 14:00:00', 1),
(6, 'Trần Văn F', 'Đà Nẵng', '987 Đường PQR, Đà Nẵng', '0956789012', 2200000.00, '2024-01-30 15:00:00', 1),
(1, 'Nguyễn Văn A', 'Hà Nội', '123 Đường ABC, Hà Nội', '0901234567', 3000000.00, '2024-10-29 16:00:00', 1),
(2, 'Trần Thị B', 'Hồ Chí Minh', '456 Đường DEF, Hồ Chí Minh', '0912345678', 500000.00, '2024-11-28 17:00:00', 1),
(3, 'Lê Văn C', 'Đà Nẵng', '789 Đường GHI, Đà Nẵng', '0923456789', 3500000.00, '2024-10-27 18:00:00', 1),
(4, 'Phạm Minh D', 'Hà Nội', '321 Đường JKL, Hà Nội', '0934567890', 4000000.00, '2024-09-26 19:00:00', 1),
(5, 'Nguyễn Thị E', 'Hồ Chí Minh', '654 Đường MNO, Hồ Chí Minh', '0945678901', 2800000.00, '2024-11-25 20:00:00', 1),
(6, 'Trần Văn F', 'Đà Nẵng', '987 Đường PQR, Đà Nẵng', '0956789012', 3300000.00, '2024-01-24 21:00:00', 1)       
ON DUPLICATE KEY UPDATE magd=magd;


INSERT INTO chitietgiaodich (magd, masp, soluong) VALUES
(1, 1, 2), 
(1, 2, 1), 
(1, 3, 3), 
(1, 4, 2), 
(2, 5, 4), 
(2, 6, 1), 
(3, 7, 2), 
(3, 8, 3), 
(4, 9, 2), 
(4, 10, 5), 
(5, 1, 2), 
(6, 2, 3), 
(6, 3, 1), 
(7, 14, 4), 
(7, 15, 2), 
(8, 16, 3), 
(8, 17, 1), 
(9, 18, 2), 
(10, 19, 4), 
(10, 2, 3), 
(11, 1, 1),
(12, 22, 2), 
(12, 3, 1), 
(12, 24, 4), 
(12, 5, 2)
ON DUPLICATE KEY UPDATE id=id;



INSERT INTO giohang (user_id, masp, soluong) VALUES
(1, 1, 2),  
(1, 3, 1),  
(2, 4, 1),  
(3, 5, 3),  
(2, 1, 1),  
(3, 6, 1)  
ON DUPLICATE KEY UPDATE id=id; -- Không thêm nếu đã có



INSERT INTO sanphamyeuthich (user_id, masp) VALUES
(1, 1),  
(1, 2), 
(2, 3), 
(3, 1),  
(3, 4),  
(2, 5)  
ON DUPLICATE KEY UPDATE id=id; -- Không thêm nếu đã có

ALTER TABLE thanhvien MODIFY diachi VARCHAR(255) NULL;
ALTER TABLE thanhvien MODIFY sodt VARCHAR(15) NULL;
ALTER TABLE thanhvien MODIFY email VARCHAR(100) NULL;


-- LỊCH SỬ MUA HÀNG
-- Tạo bảng lịch sử mua hàng
CREATE TABLE `lich_su_mua_hang` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `ngay_dat` DATE,  -- Ngày đặt hàng
    `ngay_du_kien_nhan` DATE,  -- Ngày dự kiến nhận hàng
    `tong_tien` int,
    `user_id` int, 
    `trang_thai` ENUM('Đã nhận', 'Chờ nhận', 'Yêu cầu trả', 'Giỏ hàng') NOT NULL  ,-- Trạng thái đơn hàng
FOREIGN KEY (user_id) REFERENCES thanhvien(id) ON DELETE CASCADE 
);

CREATE TABLE `lich_su_mua_hang_sanpham` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `sanpham_id` int,  -- Ngày đặt hàng
    `soluong` int ,
    `maLSmuahang` int,
FOREIGN KEY (maLSmuahang) REFERENCES lich_su_mua_hang(id) ON DELETE CASCADE, 
FOREIGN KEY (sanpham_id) REFERENCES sanpham(masp) ON DELETE CASCADE 
);



-- Tạo bảng giao dịch (giả sử bảng giao dịch có mã giao dịch và trạng thái)
CREATE TABLE `giao_dich` (
    `ma_giao_dich` INT AUTO_INCREMENT PRIMARY KEY,
    `ma_sp` VARCHAR(50) NOT NULL,  -- Mã sản phẩm
    `trang_thai` ENUM('0', '1') NOT NULL  -- Trạng thái giao dịch (0: chưa giao, 1: đã giao)
);


-- Trigger kiểm tra yêu cầu trả hàng
DELIMITER $$


CREATE TRIGGER trg_check_return_request
BEFORE UPDATE ON lich_su_mua_hang
FOR EACH ROW
BEGIN
    -- Kiểm tra điều kiện trả hàng
    IF NEW.trang_thai = 'Yêu cầu trả hàng' THEN
        -- Kiểm tra ngày hiện tại so với ngày dự kiến nhận hàng
        IF DATEDIFF(CURDATE(), NEW.ngay_du_kien_nhan) > 3 THEN
            SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'Không thể yêu cầu hoàn trả vì đã quá 3 ngày kể từ ngày dự kiến nhận hàng';
        END IF;
    END IF;
END$$


DELIMITER ;




-- Trigger thay đổi trạng thái giao dịch sau khi đặt hàng
DELIMITER $$

CREATE TRIGGER cap_nhat_trang_thai_giao_dich
AFTER INSERT ON `lich_su_mua_hang`
FOR EACH ROW
BEGIN
    DECLARE ngay_hien_tai DATE;
    SET ngay_hien_tai = CURDATE();

    -- Nếu ngày hiện tại đã qua 3 ngày từ ngày dự kiến nhận, cập nhật trạng thái giao dịch
    IF DATEDIFF(NEW.ngay_du_kien_nhan, ngay_hien_tai) <= 3 THEN
        UPDATE giaodich SET tinhtrang = 1 WHERE magd= NEW.ma_sp;
    END IF;
END$$

DELIMITER ;
-----------------------------------------
SELECT
    YEAR(date) AS year,
    MONTH(date) AS month,
    SUM(tongtien) AS total_amount
FROM
    giaodich
WHERE
    tinhtrang = 1
GROUP BY
    YEAR(date),
    MONTH(date)
ORDER BY
    year DESC, month DESC;





INSERT INTO chitietgiaodich (magd, masp, soluong) VALUES
(1, 1, 1), 
(1, 2, 1),  
(2, 3, 1),  
(3, 4, 1), 
(3, 5, 1),  
(4, 1, 1),  
(5, 2, 1)   
ON DUPLICATE KEY UPDATE id=id; -- Không thêm nếu đã có

CREATE TABLE nguoidung (
    id INT PRIMARY KEY AUTO_INCREMENT,
    email NVARCHAR(255) NOT NULL,
    password NVARCHAR(255) NOT NULL,
    birth_date DATE NOT NULL
);


DELIMITER //

CREATE TRIGGER trg_validate_birth_date
BEFORE INSERT ON nguoidung
FOR EACH ROW
BEGIN
    -- Kiểm tra ngày sinh lớn hơn ngày hiện tại
    IF NEW.birth_date > CURRENT_DATE THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Ngày sinh không hợp lệ: Ngày sinh phải nhỏ hơn hoặc bằng ngày hiện tại.';
    END IF;

    -- Kiểm tra người dùng chưa đủ 14 tuổi
    IF TIMESTAMPDIFF(YEAR, NEW.birth_date, CURRENT_DATE) < 14 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Ngày sinh không hợp lệ: Người dùng phải đủ 14 tuổi.';
    END IF;
END;
//

DELIMITER ;


-- Trường hợp hợp lệ:
INSERT INTO nguoidung (email, password, birth_date)
VALUES ('testuser@gmail.com', 'password123', '2010-12-13');  -- Người này đã 14 tuổi


-- Trường hợp không hợp lệ (ngày sinh > ngày hiện tại):
INSERT INTO nguoidung (email, password, birth_date)
VALUES ('futureuser@gmail.com', 'password123', '2025-01-01'); -- Ngày sinh trong tương lai

-- Trường hợp không hợp lệ (chưa đủ 14 tuổi):
INSERT INTO nguoidung (email, password, birth_date)
VALUES ('younguser@gmail.com', 'password123', '2012-12-14'); -- Chưa đủ 14 tuổi

     
