// 新註冊教學
function tutorial(){
	
}

// 通知
if( $(window).width() < 768 ) {
	$('#remindBtn').click(function(){
		window.location = BASE+'chatboard/'+$('#mid').val();
	});
}
else {
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
// 上傳頭像
$('#avaterPic').hover(
	function() {
		$('#avaterPic_cover').animate({
			opacity: 'show'
		},250);
	},
	function() {
		$('#avaterPic_cover').animate({
			opacity: 'hide'
		},250);
	}
);

$('#pic_submit').click(function(){
	var ext = new Array();
	var filearr = new Array();
	ext = $('#pic_upload').val().split('.');
	filearr = ext[0].split('\\');
	if( !checkChinese(filearr[2]) ){
		alert('檔名不得為中文');
		return;
	}
	var filetype = ext[1].toLowerCase();
	if($.inArray(filetype, ['png','jpg','jpeg']) == -1) {
		alert('只允許上傳 JPG 或 PNG 影像檔');
		return;
	}
	if ( !checkpics() ) {
		alert('圖片請勿超過 5MB');
		return;
	}
	
	$('#loading').show();
	$('#uploading').show();
	$.ajaxFileUpload({
		url: 'meet_ajax/upload_pic', 
		secureuri: false,
		fileElementId: 'pic_upload',
		dataType: 'json',
		success: function(data) {
			if( data['status'] == 'success' ) {
				$('#loading').hide();
				$('#uploading').hide();
				$('#upload_avater').modal('hide');
				window.location = BASE+'member/'+$('#mid').val()+'?edit=on';
			}
			else {
				alert('發生錯誤，請稍後再試。');
			}
		}
	});
});
function checkpics(){
	var size = 0;

	if( navigator.userAgent.indexOf("MSIE") > -1) {
		var obj = new ActiveXObject("Scripting.FileSystemObject");
		size = obj.getFile(document.getElementById("pic_upload").value).size;
	}
	else if ( navigator.userAgent.indexOf("Firefox") > -1 
			 || navigator.userAgent.indexOf("Chrome") > -1 
			 || navigator.userAgent.indexOf(".NET") > -1 
			 ||  navigator.userAgent.indexOf("Safari") > -1 ) {
		size = document.getElementById("pic_upload").files.item(0).size;
	}
	else {
		return false;
	}
	
	if( size > 5000000 ){
		alert("上傳檔案不得超過 5MB ");
		return false;
	}
		return true;
}
function checkChinese( str ) {
	// 驗證是否有中文字
	var regExp = /^[\u4E00-\u9FA5]+$/;
	if ( regExp.test(str) ) {
		return false;
	}
    else {
		return true;
	}
}

// 裁切頭像
$('#imageEdit').Jcrop({
	onSelect : pasteCropValue,
	minSize : [200,230],
	maxSize : [400,560],
	setSelect : [0, 0, 400, 560],
	aspectRatio: 20 / 23
});
function pasteCropValue(c){
	var cw = Math.round(c.w);
	var ch = Math.round(c.h);
	$('#cropx').val(c.x);
	$('#cropy').val(c.y);
	$('#cropw').val(cw);
	$('#croph').val(ch);
}
$('#position_save').click(function(){
	var mid = $('#mid').val();
	
	var adjust_crop_x = $('#cropx').val();
	var adjust_crop_y = $('#cropy').val();
	var adjust_crop_w = $('#cropw').val();
	var adjust_crop_h = $('#croph').val();
	
	$('#loading').show();
	var querySting = {	mid:mid,
						adjust_crop_x:adjust_crop_x,
						adjust_crop_y:adjust_crop_y,
						adjust_crop_w:adjust_crop_w,
						adjust_crop_h:adjust_crop_h
					};
	$.ajax({
		type: 'POST',
		url: 'meet_ajax/crop_pic',
		data: querySting,
		success: function(data) {
			if( data == 'success' ) {
				$('#loading').hide();
				$('#edit_avater').modal('hide');
				$('#save_success').modal('show');
			}
			else {
				alert('發生錯誤，請稍後再試');
			}
		}
	});
});

// 未填寫為灰色
greyfilter('memberNameDisplay');
greyfilter('memberMobileDisplay');
greyfilter('memberGenderDisplay');
greyfilter('memberBirthdayDisplay');
greyfilter('memberLocationDisplay');
greyfilter('memberDescriptionDisplay');
function greyfilter(DOM) {
	if ( $('#'+DOM).html() == '尚無資料' ) {
		$('#'+DOM).css('color','grey');
	}
}

// 編輯
$('#memberDetailEdit').click(function() {
	if( $('#memberNameInput').val() == '尚無資料' ) {
		$('#memberNameInput').val('');
	}
	if( $('#memberMobileInput').val() == '尚無資料' ) {
		$('#memberMobileInput').val('');
	}
	
	$('#memberNameInput').show();
	$('#memberMobileInput').show();
	$('#memberGenderInputArea').show();
	$('#memberBirthdayInputArea').show();
	$('#memberLocationInputArea').show();
	$('#editAction').show();
	$('#memberDescriptionInput').show();
	
	$('#memberNameDisplay').hide();
	$('#memberMobileDisplay').hide();
	$('#memberGenderDisplay').hide();
	$('#memberBirthdayDisplay').hide();
	$('#memberLocationDisplay').hide();
	$('#memberDescriptionDisplay').hide();
	
	$('#memberNameInput').focus();
});

// 取消
$('#detailCancel').click(function() {
	$('#memberNameDisplay').show();
	$('#memberMobileDisplay').show();
	$('#memberGenderDisplay').show();
	$('#memberBirthdayDisplay').show();
	$('#memberLocationDisplay').show();
	$('#memberDescriptionDisplay').show();
	
	$('#memberNameInput').hide();
	$('#memberMobileInput').hide();
	$('#memberGenderInputArea').hide();
	$('#memberBirthdayInputArea').hide();
	$('#memberLocationInputArea').hide();
	$('#memberDescriptionInput').hide();
	$('#editAction').hide();
});

// 地區選單
$('#regionInput').change(function() {
	if( $(this).val() == '' ) {
		$('#sectionInput').html('<option value="" selected="selected">請選擇縣市</option>');
		return false;
	}
	
	var querySting = {regionid:$(this).val()};
	$.ajax({
		type: 'POST',
		url: 'meet_ajax/get_section',
		data: querySting,
		success: function(data) {
			$('#sectionInput').html(data);
		}
	});
});

// 數字驗證提示
$('#memberMobileInput').focusout(function() {
	if(!numcheck(this)) {
		$('#editMobileAlert').show();
	}
	else {
		$('#editMobileAlert').hide();
	}
});
$('#memberBirthdayYearInput').focusout(function() {
	if(!numcheck(this)) {
		$('#editBirthdayAlert').show();
	}
	else {
		$('#editBirthdayAlert').hide();
	}
});
$('#memberBirthdayMonthInput').focusout(function() {
	if(!numcheck(this)) {
		$('#editBirthdayAlert').show();
	}
	else {
		$('#editBirthdayAlert').hide();
	}
});
$('#memberBirthdayDayInput').focusout(function() {
	if(!numcheck(this)) {
		$('#editBirthdayAlert').show();
	}
	else {
		$('#editBirthdayAlert').hide();
	}
});

// 送出
$('#detailSubmit').click(function() {
	var mid = $('#mid').val();
	
	var memberNameInput = $('#memberNameInput').val();
	var memberMobileInput = $('#memberMobileInput').val();
	var memberGenderInput = $('input[name=memberGenderInput]:checked').val();
	var memberBirthdayYearInput = $('#memberBirthdayYearInput').val();
	var memberBirthdayMonthInput = $('#memberBirthdayMonthInput').val();
	var memberBirthdayDayInput = $('#memberBirthdayDayInput').val();
	var regionInput = $('#regionInput').val();
	var sectionInput = $('#sectionInput').val();
	var memberDescriptionInput = $('#memberDescriptionInput').val();
	if( memberGenderInput == undefined ) {
		memberGenderInput = 0;
	}
	var querySting = {	memberNameInput:memberNameInput,
						memberMobileInput:memberMobileInput,
						memberGenderInput:memberGenderInput,
						memberBirthdayYearInput:memberBirthdayYearInput,
						memberBirthdayMonthInput:memberBirthdayMonthInput,
						memberBirthdayDayInput:memberBirthdayDayInput,
						regionInput:regionInput,
						sectionInput:sectionInput,
						memberDescriptionInput:memberDescriptionInput,
						mid:mid};
	$.ajax({
		type: 'POST',
		url: 'meet_ajax/save_profile',
		data: querySting,
		dataType: 'json',
		success: function(data) {
			if( data['status'] == 'success' ) {
				$('#save_success').modal('show');
			}
			else {
				alert(data['msg']);
				location.reload();
			}
		}
	});
});

$('.saveok_btn').click(function() {
	location.reload();
});
$('#save_success').click(function() {
	location.reload();
});

// 目前狀態選單
$('.memberStatus .dropdown-menu li a').click(function(){
	$('#statusNow').html($(this).text()+' <span class="caret"></span>');
	var foodwhere_region = $(this).attr('a_type');
	$('#statusID').val($(this).attr('a_type'));
	$('.memberStatus .btn-group').removeClass('open');
});

// 立刻揪團
$('#statusSubmit').click(function(){
	var statusID = $('#statusID').val();
	var mid = $('#mid').val();
	var querySting = {	statusID:statusID,
						mid:mid
					};
	$.ajax({
		type: 'POST',
		url: 'meet_ajax/update_statue',
		data: querySting,
		dataType: 'json',
		success: function(data) {
			if( data['status'] == 'success' ) {
				window.location = BASE+'friendlist/'+mid+'/1';
			}
			else {
				alert(data['msg']);
				location.reload();
			}
		}
	});
});

// 數字驗證
function numcheck(DOM) {
	var re = /^[0-9]+$/;
	if (!re.test(DOM.value)){
		return false;
	}
	else{
		return true;
	}
}