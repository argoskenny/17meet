$(document).ready(function(){
	$("#inputAccount").focusout(function(){
		if( !checkAccount() ) {
			return false;
		}
	});

	$("#inputPassword1").focus(function(){
		$("#inputPassword2").val("");
		$("#inputPassword2").parent().removeClass("has-error");
		$("#inputPassword2").parent().removeClass("has-success");
		$("#inputPassword2").next().hide();
	});

	$("#inputPassword1").focusout(function(){
		if( !checkPassword1() ) {
			return false;
		}
	});

	$("#inputPassword2").focusout(function(){
		if( !checkPassword2() ) {
			return false;
		}
	});

	$("#inputEmail").focusout(function(){
		if( !checkEmail() ) {
			return false;
		}
		else {
			var mailVal = $(this).val();
			var queryData = {mailVal:mailVal};
			$.ajax({
				type: "POST",
				url: 'meet_ajax/checkEmail',
				data: queryData,
				success: function(data) {
					if( data == 'success' ){
						passItem('inputEmail','');
					}
					else {
						unpassItem('inputEmail','此電子郵件已被使用');
						checkStr += 'Email ';
					}
				}
			});
		}
	});
	
	// 送出註冊資料
	$("#registerSubmit").click(function(){
		register();
	});
	
	$('.form-control').keyup(function(event){
		if(event.keyCode == 13){
			register();
		}
	});
	
	// 註冊成功動畫
	$('#successMsg').fadeIn('slow');
});

// 註冊
function register(){
	var checkStr = '';
		
	var accountVal = $('#inputAccount').val();
	if( accountVal.length < 3 || !checkVal(accountVal) ) {
		unpassItem('inputAccount','帳號請大於3個英文或數字');
		checkStr += 'Account ';
	}
	else {
		passItem('inputAccount','');
		var queryData = {accountVal:accountVal};
		$.ajax({
			type: "POST",
			async: false,
			url: 'meet_ajax/checkAccount',
			data: queryData,
			success: function(data) {
				if( data == 'success' ){
					passItem('inputAccount','');
				}
				else {
					unpassItem('inputAccount','此帳號已被使用');
					checkStr += 'Account ';
				}
			}
		});
	}
	
	if( !checkPassword1() ) {
		checkStr += 'Password1 ';
	}
	if( !checkPassword2() ) {
		checkStr += 'Password2 ';
	}
	if( !checkEmail('inputEmail','請輸入電子信箱') ) {
		checkStr += 'Email ';
	}
	else {
		var mailVal = $('#inputEmail').val();
		var queryData = {mailVal:mailVal};
		$.ajax({
			type: "POST",
			async: false,
			url: 'meet_ajax/checkEmail',
			data: queryData,
			success: function(data) {
				if( data == 'success' ){
					passItem('inputEmail','');
				}
				else {
					unpassItem('inputEmail','此電子郵件已被使用');
					checkStr += 'Email ';
				}
			}
		});
	}
	if( checkStr != '' ) {
		var tempStr = checkStr.split(" ");
		$('#input'+tempStr[0]).focus();
		return false;
	}
	else {
		document.registerFrom.submit();
		$('.registerArea').fadeOut('slow');
	}
}

// 驗證詳細
function checking(item,alertMsg) {
	if( $('#'+item).val() == '' ) {
		unpassItem(item,alertMsg);
		return false;
	} 
	else {
		passItem(item,alertMsg);
		return true;
	}
}

// 驗證帳號
function checkAccount(){
	var accountVal = $('#inputAccount').val();
	if( accountVal.length < 3 || !checkVal(accountVal) ) {
		unpassItem('inputAccount','帳號請大於3個英文或數字');
		return false;
	}
	else {
		passItem('inputAccount','');
	}
	var queryData = {accountVal:accountVal};
	$.ajax({
		type: "POST",
		url: 'meet_ajax/checkAccount',
		data: queryData,
		success: function(data) {
			if( data == 'success' ){
				passItem('inputAccount','');
				return true;
			}
			else {
				unpassItem('inputAccount','此帳號已被使用');
				return false;
			}
		}
	});
}

// 驗證密碼
function checkPassword1(){
	if( $('#inputPassword1').val().length < 4 || !checkVal($('#inputPassword1').val())) {
		unpassItem('inputPassword1','密碼請大於4個英文或數字');
		return false;
	}
	else {
		passItem('inputPassword1','');
		return true;
	}
}
function checkPassword2(){
	if( $('#inputPassword1').val() == $('#inputPassword2').val() && $('#inputPassword1').val().length >= 4 ) {
		passItem('inputPassword2','');
		return true;
	}
	else {
		unpassItem('inputPassword2','密碼不符');
		return false;
	}
}

// 驗證email
function checkEmail(){
	if( !validEmail($('#inputEmail').val()) ) {
		unpassItem('inputEmail','請輸入正確格式的電子郵件');
		return false;
	}
	else {
		passItem('inputEmail','');
		return true;
	}
}

// 通過
function passItem(DOM,alertText) {
	$('#'+DOM).parent().removeClass('has-error');
	$('#'+DOM).parent().addClass('has-success');
	$('#'+DOM).next().hide();
}

// 不通過
function unpassItem(DOM,alertText) {
	$('#'+DOM).parent().removeClass('has-success');
	$('#'+DOM).parent().addClass('has-error');
	$('#'+DOM).next().html(alertText);
	$('#'+DOM).next().show();
}

// email格式
function validEmail(emailtoCheck) {
	var regExp = /^[^@^\s]+@[^\.@^\s]+(\.[^\.@^\s]+)+$/;
	if ( emailtoCheck.match(regExp) )
		return true;
	else
		return false;
}

// 驗證英數字
function checkVal( str ) {
    var regExp = /^[\d|a-zA-Z]+$/;
    if ( regExp.test(str) ) {
        return true;
	}
    else{
        return false;
	}
}
