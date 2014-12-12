$(document).ready(function(){
	// 隨機切換背景圖片
	var maxNum = 7;  
	var minNum = 1;  
	var n = Math.floor(Math.random() * (maxNum - minNum + 1)) + minNum;
	var srcBG = 'assets/img/backgrounds/login_bg0'+n+'.jpg';
	var $img = $( '<img src="' + srcBG + '">' );
	$img.bind( 'load', function(){
		$('body').css( 'background-image', 'url(' + srcBG + ')' );
	} );
	if( $img[0].width ){ $img.trigger( 'load' ); }
	
	//$('body').css('background-image', 'url(assets/img/backgrounds/login_bg0'+n+'.jpg)');
	
	// LOGO隨螢幕大小縮放
	if( $(window).width() < 380 ) {
		var inputWidth = $("#account").width();
		inputWidth = inputWidth + 20;
		$("#meetLogo").width(inputWidth);
		$("#meetLogo").height(inputWidth*17/60);
	}
	else {
		$("#meetLogo").width(300);
		$("#meetLogo").height(85);
	}
	$(window).resize(function(){              
		if( $(window).width() < 380 ) {
			var inputWidth = $("#account").width();
			inputWidth = inputWidth + 20;
			$("#meetLogo").width(inputWidth);
			$("#meetLogo").height(inputWidth*17/60);
		}
		else {
			$("#meetLogo").width(300);
			$("#meetLogo").height(85);
		}
	});
	
	// 登入
	$("#loginForm").keyup(function(event){
		if(event.keyCode == 13){
			accountLogin();
		}
	});
	$('#send').click(function() {
		accountLogin();
	});
});

// 登入
function accountLogin(){
	if( $('#account').val() == '' ) {
			$('#alertMsg').show();
			return false;
		}
		if( $('#password').val() == '' ) {
			$('#alertMsg').show();
			return false;
		}
		
		var account = $('#account').val();
		var password = $('#password').val();
		
		$.ajax({
			type: 'POST',
			url: 'meet_ajax/login',
			dataType: 'JSON',
			data: {account:account,password:password},
			success:function(data) {
				if( data['status'] == 'success' ) {
					window.location = BASE+'member/'+data['memberid'];
				}
				else {
					$('#alertMsg').show();
					return false;
				}
			},
			error:function() {
				alert('發生錯誤！');
			}
		});
}

// 捲動到最上面
function gotop() {
	$("html,body").animate({scrollTop:0},900);
}