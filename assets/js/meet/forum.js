$(document).ready(function(){
	// 未讀訊息變色
	$('.unread').animate({backgroundColor: '#fff'},2000,"linear");
	
	// 輸入訊息
	$('#chatContent').focus(function(){
		 if( $(this).height() < 80 ) {
			$(this).animate({'height': '+=80'},200,"linear");
			$('#chatSubmit').animate({
				height:		'show',
				opacity:	'show'
			},200,"linear");
			$('#chatCancel').animate({
				height:		'show',
				opacity:	'show'
			},200,"linear");
		 }
	});
	// 取消
	$('#chatCancel').click(function(){
		if( $('#chatContent').height() > 80 ) {
			$('#chatContent').animate({'height': '-=80'},200,"linear");
			$('#chatSubmit').animate({
				opacity:	'hide'
			},200,"linear");
			$('#chatCancel').animate({
				opacity:	'hide'
			},200,"linear");
			$('#msgAlert').animate({
				opacity:	'hide'
			},200,"linear");
			$('#chatContent').removeClass('has-error');
		 }
	});
	// 送出
	$('#chatSubmit').click(function(){
		if( $('#chatContent').val() == '' ) {
			$('#msgAlert').html('<b>請輸入留言內容</b>');
			$('#msgAlert').css('display','inline-block').animate({
				opacity:	'show'
			},200,"linear");
			$('#chatContent').addClass('has-error');
			return;
		}
		else {
			$('#msgAlert').animate({
				opacity:	'hide'
			},200,"linear");
			$('#chatContent').removeClass('has-error');
		}
		
		var chatContent = $('#chatContent').val();
		var b_id = $('#b_id').val();
		var t_id = $('#t_id').val();
		
		var queryData = {chatContent:chatContent,b_id:b_id,t_id:t_id};
		$.ajax({
			type: "POST",
			url: 'meet_ajax/post_msg',
			data: queryData,
			dataType: 'json',
			success: function(data) {
				if( data['status'] == 'success' ){
					if( $('#nolist').val() == '1' ) {
						$('#msgBoard').html(data['msg']);
						$('#msgID_'+data['newMsgID']).animate({height:'show',opacity: 'show'},500,"linear");
						
						// 輸入訊息欄回到最初狀態
						$('#chatContent').val('');
						$('#chatContent').animate({'height': '-=80'},500,"linear");
						$('#chatSubmit').animate({
							opacity:	'hide'
						},500,"linear");
						$('#chatCancel').animate({
							opacity:	'hide'
						},500,"linear");
						
						// 已有留言
						$('#nolist').val('0');
					}
					else {
						$('.msglist').first().before(data['msg']);
						$('#msgID_'+data['newMsgID']).animate({opacity: 'show'},500,"linear");
						
						// 輸入訊息欄回到最初狀態
						$('#chatContent').val('');
						$('#chatContent').animate({'height': '-=80'},500,"linear");
						$('#chatSubmit').animate({
							opacity:	'hide'
						},500,"linear");
						$('#chatCancel').animate({
							opacity:	'hide'
						},500,"linear");
					}
				}
				else {
				}
			}
		});
	});
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
function addpage_forum(page){
	var b_id = $('#b_id').val();
	var t_id = $('#t_id').val();
	var querySting = {page:page,b_id:b_id,t_id:t_id};
	$('#page_'+page).children().hide();
	$('#page_'+page).html('<img src="assets/img/backgrounds/loading.gif" width="50" height="50">');
	$.ajax({
		type: 'POST',
		url: 'meet_ajax/addpage_forum',
		data: querySting,
		dataType: 'json',
		success: function(data) {
			if( data['status'] == 'success' ) {
				$('#page_'+page).remove();
				$('.msglist').last().after(data['chatList_HTML']);
			}
			else {
				alert('發生錯誤，請稍後再試');
			}
		}
	});
}
