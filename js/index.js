
// extrat data
$(document).on('click', '#extrat_data', function(event) {
	event.preventDefault();
	// alert("extrat_data");

	$('#myModal').modal();
});

$(document).on('click', '#extract_sumit_btn', function(event) {
	event.preventDefault();
	alert("123");
	$('#myModal').modal("hide");
	
	$.ajax({ url: 'extract_table_ajax.php?' ,
		cache: false,
		dataType: 'html',// <== 設定傳送格式
		type:'GET',// <== 設定傳值方式
		data: { new_table_name: $("#extract_input").val() },// <== 傳GET的變數，此例是gsn
		error: function(xhr) { alert('Ajax extract table 發生錯誤'+ xhr); },
		success: function(response) {
			alert(response);
		}
	});
});
function error_show (error_message , error_detail) {
	// alert("123123");
	// 放入錯誤訊息
	$("#error_header").html(error_message);
	$("#error_detail").html(error_detail);
	// 顯現 error box
	$("#errorModal").modal();
}

$(document).ready(function(){ 
	// 目錄檔load 之後 
	$("#directory_iframe").on('load', function(event) {
		// 點選iframe 的內容 取消上面有被點開的tab
		// 左邊的瀏覽列
		$('#directory_iframe').contents().on('click', function(event) {
			$(".nav .open").removeClass("open");
		});

	});	

	// 右邊的視窗
	$("#file_content").on('load', function(event) {
		// 點選iframe 的內容 取消上面有被點開的tab
		// 左邊的瀏覽列
		$('#file_content').contents().on('click', function(event) {
			$(".nav .open").removeClass("open");
		});
	});	
	
		

});



