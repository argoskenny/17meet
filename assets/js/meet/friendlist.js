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

function showBtn(id) {
	$('#conBtn_'+id).show();
}
function hideBtn(id) {
	$('#conBtn_'+id).hide();
}

// 一起聊聊
function conversation(id){
	var querySting = {id:id};
	$.ajax({
		type: 'POST',
		url: 'meet_ajax/open_forum',
		data: querySting,
		dataType: 'json',
		success: function(data) {
			if( data['status'] == 'success' ) {
				window.location = BASE+'forum/'+data['forum_id']+'/'+id;
			}
			else {
				alert('發生錯誤，請稍後再試');
			}
		}
	});
}

// 下一頁
function addpage(page){
	var querySting = {page:page};
	$('#page_'+page).children().hide();
	$('#page_'+page).html('<img src="assets/img/backgrounds/loading.gif" width="50" height="50">');
	$.ajax({
		type: 'POST',
		url: 'meet_ajax/addpage',
		data: querySting,
		dataType: 'json',
		success: function(data) {
			if( data['status'] == 'success' ) {
				$('#page_'+page).remove();
				$('.friendListCover').last().after(data['list_HTML']);
			}
			else {
				alert('發生錯誤，請稍後再試');
			}
		}
	});
}