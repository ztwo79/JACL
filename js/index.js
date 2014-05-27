
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
function error_show (argument) {
	alert("123123");
	$("#errorModal").modal();
}