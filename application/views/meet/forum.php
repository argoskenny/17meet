<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
	
	<meta name="author" content="17MEET" />
	<meta name="dcterms.rightsHolder" content="jazamila.com/17MEET" />
	<meta name="description" content="17MEET 一個讓你認識新朋友的地方。" />
	<meta name="robots" content="all" />
	<meta name="googlebot" content="all" />
	
	<meta property="og:title" content="17MEET"/>
	<meta property="og:type" content="website"/>
	<meta property="og:image" content="<?php echo base_url();?>assets/img/logo/oglogo.png"/>
	<meta property="og:url" content="<?php echo base_url();?>"/>
	<meta property="og:description" content="想認識新朋友嗎？17MEET讓你用最輕鬆又簡單的方式快速遇見與你有相同興趣的朋友。"/>
	
	<title><?php echo $title;?></title>
	<base href="<?php echo base_url();?>"/><!--[if IE]></base><![endif]-->
	
	<link rel="shortcut icon" href="<?php echo base_url();?>assets/img/logo/meet.ico" >
	<link href="assets/css/common/bootstrap.min.css" rel="stylesheet" type="text/css" />
	<link href="assets/css/meet/public.css" rel="stylesheet" type="text/css" />
	<link href="assets/css/meet/forum.css" rel="stylesheet" type="text/css" />
</head>

<body ontouchstart="">
<input type="hidden" id="m_id" name="m_id" value="<?php echo $m_id;?>">
<input type="hidden" id="b_id" name="b_id" value="<?php echo $b_id;?>">
<input type="hidden" id="t_id" name="t_id" value="<?php echo $t_id;?>">
<input type="hidden" id="nolist" name="nolist" value="<?php echo $nolist;?>">
	<div class="navbar navbar-inverse navbar-fixed-top" role="navigation">
		<div class="container">
		<div class="navbar-header">
			<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
			<span class="sr-only">Toggle navigation</span>
			<span class="icon-bar"></span>
			<span class="icon-bar"></span>
			<span class="icon-bar"></span>
			</button>
			<a class="navbar-brand" href="">17MEET <span class="label label-warning">BETA</span></a>
		</div>
		<div class="navbar-collapse collapse">
			<ul class="nav navbar-nav navbar-right">
				<!--<li><a href="javascript:;">邀請</a></li>-->
				<li><a href="javascript:;" id="remindBtn" data-toggle="popover" title="" data-placement="bottom" data-title="留言通知">通知<?php echo $unread_num;?></a></li>
				<li><a href="member/<?php echo $m_id;?>"><?php echo $account;?></a></li>
				<li><a href="logout">登出</a></li>
			</ul>
		</div><!--/.navbar-collapse -->
		</div>
	</div>
	<div class="main">
		<div class="container">
			<div class="col-xs-12 col-sm-9">
				<div>
					<h2>您與<?php echo $partnerName?>的留言板</h2>
				</div>
				<div class="form-group msgArea">
					<textarea id="chatContent" class="form-control" placeholder="立即留言！"></textarea>
					<button class="btn btn-primary btn-lg" id="chatSubmit">確定送出</button>
					<button class="btn btn-default btn-lg" id="chatCancel">取消</button>
					<span id="msgAlert"></span>
				</div>
				<div class="msgTitle">歷史訊息</div>
				<div id="msgBoard">
					<?php echo $CHAT_HTML;?>
				</div>
				</div>
			<div class="col-xs-12 col-sm-3">
			</div>
		</div>
	</div>

<!-- 通知小視窗 -->
<div class="remindArea" id="popover_content_wrapper">
	<?php echo $remind_HTML;?>
	<div class="remindBottom"><a href="chatboard/<?php echo $m_id;?>">更多留言</a></div>
</div>
<script type="text/javascript" src="assets/js/common/jquery-1.10.2.min.js"></script>
<script type="text/javascript" src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
<script type="text/javascript" src="assets/js/common/bootstrap.min.js"></script>
<script type="text/javascript">var BASE = '<?php echo base_url();?>';</script>
<script type="text/javascript" src="assets/js/meet/forum.js"></script> 
</body> 
</html>