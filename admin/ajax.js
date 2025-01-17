function them_sp() {
    var formData = new FormData($("#add-product-form")[0]);

    // Kiểm tra tên sản phẩm và giá
    var tensp = $('#tensp').val();
    var gia = $('#gia').val();

    if (tensp == "" || gia == "") {
        alert("Tên sản phẩm và giá không được để trống!");
        return;
    }

    $.ajax({
        url: "for-ajax.php",  // Đảm bảo rằng đường dẫn đúng
        type: "POST",
        data: formData,
        dataType: "text",
        contentType: false,  // Không tự động định dạng lại dữ liệu
        processData: false,  // Không xử lý dữ liệu tự động
        success: function(result) {
            $("#sp_error").html(result);
            if (result.indexOf("Thêm sản phẩm thành công") !== -1) {
                // Reload lại trang khi thêm sản phẩm thành công
                window.location.reload();
            }
        },
        error: function(jqXHR, textStatus, errorThrown) {
            console.error("AJAX error:", textStatus, errorThrown);
        }
    });
}



function xoa_sp(masp_xoa){
	var q = 'xoa_sp';
	$.ajax({
		url : "for-ajax.php",
		type : "post",
		dataType:"text",
		data : {
			q , masp_xoa
		},
		success : function (result){
			$('#sp_error').html(result);
			window.location.reload();
		}
	});
}
function them_dm(){
	var q = 'them_dm';
	var tendm = $('#tendm').val();
	var xuatsu = $('#xuatsu').val();
	if(tendm == "" || xuatsu == ""){
		alert('Không được để trống!');
		return 0;
	}
	$.ajax({
		url : "for-ajax.php",
		type : "post",
		dataType:"text",
		data : {
			q, tendm, xuatsu
		},
		success : function (result){
			$('#sp_error').html(result);
			window.location.reload();
		}
	});
}
function xoa_dm(madm_xoa){
	var q = 'xoa_dm';
	$.ajax({
		url : "for-ajax.php",
		type : "post",
		dataType:"text",
		data : {
			q , madm_xoa
		},
		success : function (result){
			$('#sp_error').html(result);
			window.location.reload();
		}
	});
}

//sap xep cac giao dich
function list_chuagh(){
	var q = 'giaodich_chuagh';
	$('#loadmorebtngd').attr('onclick','load_more_gd(0,`gd_chuagd_body`,`chuagd`)');
	$('#loai_gd').text("chưa giao hàng");
	$.ajax({
		url : "for-ajax.php",
		type : "post",
		dataType:"text",
		data : {
			q
		},
		success : function (result){
			$('#tbl-giaodich-list').html(result);
		}
	});
}
function list_dagh(){
	var q = 'giaodich_dagh';
	$('#loadmorebtngd').attr('onclick','load_more_gd(0,`gd_dagd_body`,`dagd`)');
	$('#loai_gd').text("đã giao hàng");
	$.ajax({
		url : "for-ajax.php",
		type : "post",
		dataType:"text",
		data : {
			q
		},
		success : function (result){
			$('#tbl-giaodich-list').html(result);
		}
	});
}
function list_tatcagh(){
	var q = 'giaodich_tatcagh';
	$('#loadmorebtngd').attr('onclick','load_more_gd(0,`gd_tatcagd_body`,`tatcagd`)');
	$('#loai_gd').text("tất cả");
	$.ajax({
		url : "for-ajax.php",
		type : "post",
		dataType:"text",
		data : {
			q
		},
		success : function (result){
			$('#tbl-giaodich-list').html(result);
		}
	});
}
//giao dich da xong
function xong(magd_xong){
	var q = 'giaodich_xong';
	var x = $(this).closest('tr').children("td:nth-child(4)").text();
	$.ajax({
		url : "for-ajax.php",
		type : "post",
		dataType:"text",
		data : {
			q, magd_xong
		},
	});
}
function them_admin(){
	var q = 'them_admin';
	var ten = $('#admin-name').val();
	var tentk = $('#admin-username').val();
	var mk = $('#admin-password').val();
	$.ajax({
		url : "for-ajax.php",
		type : "post",
		dataType:"text",
		data : {
			q, ten, tentk, mk
		},
		success : function (result){
			$('#tbl-thanhvien-list').html(result);
			location.reload();
		}
	});
}
function xoa_taikhoan(id_tk_xoa){
	var q = 'xoa_taikhoan';
	$.ajax({
		url : "for-ajax.php",
		type : "post",
		dataType:"text",
		data : {
			q, id_tk_xoa
		},
		success : function (result){
			$('#tbl-thanhvien-list').html(result);
			location.reload();
		}
	});
}
function display_edit_sanpham(masp_sua_sp){
	$('#sua_sp-area').show(300);
	$('#edit_sp_btn').attr("onclick","sua_sp('"+masp_sua_sp+"')");
/*	$('#tbl-sanpham-list').toggle(300);
	$('#loadmorebtn').toggle();*/
}
function sua_sp(masp_sua){
	var q = 'sua_sp';
	var tensp_ed = $('#tensp-edit').val();
	var gia_ed = $('#gia-edit').val();
	var baohanh_ed = $('#baohanh-edit').val();
	var khuyenmai_ed = $('#khuyenmai-edit').val();
	var tinhtrang_ed = $('#tinhtrang-edit').val();
	if(tensp_ed == "" && gia_ed == "" && baohanh_ed == "" && khuyenmai_ed == "" && tinhtrang_ed == ""){
		alert("Bạn phải sửa ít nhất một trường!");
		return 0;
	}
	$.ajax({
		url : "for-ajax.php",
		type : "post",
		dataType:"text",
		data : {
			q, masp_sua, tensp_ed, gia_ed, baohanh_ed, khuyenmai_ed, tinhtrang_ed
		},
		success : function (result){
			$('#big-error').html(result);
			/*location.reload();*/
		}
	});
}