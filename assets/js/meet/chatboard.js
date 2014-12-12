$(document).ready(function(){
	
});
if( $(window).width() < 768 ) {
	$('#remindBtn').click(function(){
		window.location = BASE+'chatboard/'+$('#m_id').val();
	});
}
else {
	// 通知
	$('#remindBtn').popover({
		html : true, 
		content: function() {
			return $('#popover_content_wrapper').html();
		}
	});
	$('#remindBtn').on('shown.bs.popover', function () {
		$('.main').click(function() {
			$('#remindBtn').popover('hide');
		});
	});
}

// 下一頁
function addpage_boardlist(page){
	var querySting = {page:page};
	$('#page_'+page).children().hide();
	$('#page_'+page).html('<img src="assets/img/backgrounds/loading.gif" width="50" height="50">');
	$.ajax({
		type: 'POST',
		url: 'meet_ajax/addpage_boardlist',
		data: querySting,
		dataType: 'json',
		success: function(data) {
			if( data['status'] == 'success' ) {
				$('#page_'+page).remove();
				$('.boardItem').last().after(data['boardList_HTML']);
			}
			else {
				alert('發生錯誤，請稍後再試');
			}
		}
	});
}
