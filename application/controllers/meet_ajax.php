<?php
class Meet_ajax extends CI_Controller {
	
	// 建構子
	public function __construct() {
		parent::__construct();
		$this->load->model('meet_model');
		
		// 引入SESSION
		$this->load->library('nativesession');
		
		// 引入連結
		$this->load->helper('url');
		
		// 引入COOKIE
		$this->load->helper('cookie');
		
		// 引入資料庫套件
		$this->load->database();
	}
	
	// 登入
	function login(){
		$whereArr = array(	'account' => $_POST['account'],
							'password' => $_POST['password']
							);
		$query = $this->db->get_where('m_member',$whereArr);
		$dataArr = $query->result_array();
		if( count($dataArr) > 0 ) {
			$memberid = $dataArr[0]['id'];
			$this->nativesession->set('LOGIN_ID',$dataArr[0]['id']);
			echo json_encode(array("status"=>"success","memberid"=>$memberid));
		}
		else {
			echo json_encode(array("status"=>"fail"));
		}
	}
	
	// 儲存基本資料
	function save_profile(){
		$mid = $this->nativesession->get('LOGIN_ID');
		if( $mid != $_POST['mid'] ) {
			$returnData['status'] = 'fail';
			$returnData['msg'] = '發生錯誤，請稍後再試。';
			echo json_encode($returnData);
			return;
		}
		$whereArr = array('id' => $mid);
		$query = $this->db->get_where('m_member',$whereArr);
		$dataArr = $query->result_array();
		
		// 資料驗證
		$nameNum = mb_strlen($_POST['memberNameInput']);
		$memberNameInput = ( $nameNum > 10 ) ? '' : mysql_real_escape_string($_POST['memberNameInput']);
		$mobileNum = strlen($_POST['memberMobileInput']);
		$_POST['memberMobileInput'] = (int)$_POST['memberMobileInput'];
		$memberMobileInput = ( $mobileNum != 10 ) ? $dataArr[0]['mobile'] : $_POST['memberMobileInput'];
		$memberGenderInput = $_POST['memberGenderInput'];
		$BirthdayYearNum = strlen($_POST['memberBirthdayYearInput']);
		$BirthdayMonthNum = strlen($_POST['memberBirthdayMonthInput']);
		$BirthdayDayNum = strlen($_POST['memberBirthdayDayInput']);
		$memberBirthdayYearInput = ( $BirthdayYearNum != 4 ) ? $dataArr[0]['birth_year'] : $_POST['memberBirthdayYearInput'];
		$memberBirthdayMonthInput = ( $BirthdayMonthNum > 3 ) ? $dataArr[0]['birth_month'] : $_POST['memberBirthdayMonthInput'];
		$memberBirthdayDayInput = ( $BirthdayDayNum > 3 ) ?  $dataArr[0]['birth_day'] : $_POST['memberBirthdayDayInput'];
		$regionInput = (int)$_POST['regionInput'];
		$sectionInput = (int)$_POST['sectionInput'];
		$memberDescriptionInput = $_POST['memberDescriptionInput'];
		
		$updateArr = array(	'name' => $memberNameInput,
							'mobile' => $memberMobileInput,
							'gender' => $memberGenderInput,
							'birth_year' => $memberBirthdayYearInput,
							'birth_month' => $memberBirthdayMonthInput,
							'birth_day' => $memberBirthdayDayInput,
							'loc_reigon' => $regionInput,
							'loc_section' => $sectionInput,
							'description' => $memberDescriptionInput);
		$this->db->where('id', $mid);
		if( $this->db->update('m_member',$updateArr) == true ) {
			$returnData['status'] = 'success';
			echo json_encode($returnData);
		}
		else {
			$returnData['status'] = 'fail';
			$returnData['msg'] = '發生錯誤，請稍後再試。';
			echo json_encode($returnData);
		}
	}
	
	// 存入最新狀態
	function update_statue(){
		$mid = $this->nativesession->get('LOGIN_ID');
		if( $mid != $_POST['mid'] ) {
			$returnData['status'] = 'fail';
			$returnData['msg'] = '發生錯誤，請稍後再試。';
			echo json_encode($returnData);
			return;
		}
		$id = $_POST['mid'];
		$statusID = $_POST['statusID'];
		$statusGroupID = rand(1,10);
		$statusInsertArr = array(
			'm_id' => $id,
			'status_tag' => $statusID,
			'update_time' => time(),
			'group_id' => $statusGroupID	
		);
		$this->db->update('m_status', $statusInsertArr, array('id'=>$id));
		$this->db->insert('m_status_record', $statusInsertArr); 
		$returnData['status'] = 'success';
		echo json_encode($returnData);
	}
	
	// 更新頭像
	function upload_pic(){
		$mid = $this->nativesession->get('LOGIN_ID');
		$memberDir = 'assets/pics/head/'.$mid;
		if( !file_exists( $memberDir ) ) {
			mkdir($memberDir,0777,TRUE);
		}
		
		// 刪除舊照片
		$whereArr['id'] = $mid;
		$query = $this->db->get_where('m_member',$whereArr);
		$dataArr = $query->result_array();
		if( $dataArr[0]['picture'] != '' ) {
			unlink('assets/pics/head/'.$mid.'/'.$dataArr[0]['picture']);
		}
		if( $dataArr[0]['e_picture'] != '' ) {
			unlink('assets/pics/head/'.$mid.'/'.$dataArr[0]['e_picture']);
		}
		
		// 照片處理
		$resize_Img = "";
		$resize_ori_Img = "";
		if( !empty($_FILES['pic_upload']) )
		{
			if ($_FILES['pic_upload']['error'] > 0)
			{
				$upload_Error = $_FILES['pic_upload']['error'];
			}
			else
			{
				$tmp_file = 'assets/tmp/'.$_FILES['pic_upload']['name'];
				$img_type = explode(".",$_FILES["pic_upload"]["name"]);
				$resize_Img = 'preview_'.time().'.'.$img_type[1];
				$rezsize_path = 'assets/pics/head/'.$mid.'/'.$resize_Img;
				move_uploaded_file($_FILES['pic_upload']['tmp_name'],$tmp_file);
				
				$config['image_library'] = 'gd2';
				$config['source_image']	= $tmp_file;
				$config['new_image'] = $rezsize_path;
				$config['maintain_ratio'] = TRUE;
				$config['width'] = 400;
				$config['height'] = 560;
				
				$this->load->library('image_lib'); // 圖像處理類別
				$this->image_lib->initialize($config); 
				$this->image_lib->resize();
				
				$resize_ori_Img = "";
			}
			$picUpdate = array('picture'=>$resize_Img,'e_picture'=>'');
			$this->db->update('m_member',$picUpdate, array('id'=>$mid));
			$returnData = array('status'=>'success',
								'img'=>$rezsize_path
								);
			echo json_encode($returnData); 
		}
		else{
			$returnData = array('status'=>'fail');
			echo json_encode($returnData); 
		}
	}
	
	// 編輯頭像
	function crop_pic(){
		// 存取會員資料
		$mid = $this->nativesession->get('LOGIN_ID');
		$whereArr['id'] = $mid;
		$query = $this->db->get_where('m_member',$whereArr);
		$dataArr = $query->result_array();
		
		// 原頭像
		$src = 'assets/pics/head/'.$mid.'/'.$dataArr[0]['picture'];
		$img_type = explode(".",$dataArr[0]['picture']);
		switch($img_type[1]) {
			case 'jpg':
			case 'jpeg':
			case 'JPG':
			case 'JPEG':
				$img_r = imagecreatefromjpeg($src);
			break;
			case 'png':
			case 'PNG':
				$img_r = imagecreatefrompng($src);
			break;
		}
		// 已編輯裁切後的圖片設定
		$targ_w = 400;
		$targ_h = 460;
		$jpeg_quality = 90;
		$dst_r = ImageCreateTrueColor( $targ_w, $targ_h );
		$crop_filename = 'edited_'.time().'.jpg';
		$crop_path = 'assets/pics/head/'.$mid.'/'.$crop_filename;
		
		imagecopyresampled($dst_r,$img_r,0,0,$_POST['adjust_crop_x'],$_POST['adjust_crop_y'],$targ_w,$targ_h,$_POST['adjust_crop_w'],$_POST['adjust_crop_h']);
		imagejpeg($dst_r, $crop_path, $jpeg_quality);
		
		$picUpdate = array('e_picture'=>$crop_filename);
		$this->db->update('m_member',$picUpdate, array('id'=>$mid));
		echo 'success';
	}
	
	// 開啟聊天室
	function open_forum(){
		$m_id = $this->nativesession->get('LOGIN_ID');
		$t_id = $_POST['id'];
		 
		$newBoard = false;
		$queryInvite = $this->db->get_where('m_chat_borad',array('m_id'=>$m_id,'t_id'=>$t_id));
		$memberInvite = $queryInvite->result_array();
		if( count($memberInvite) == 0 ) {
			$queryRespond = $this->db->get_where('m_chat_borad',array('m_id'=>$t_id,'t_id'=>$m_id));
			$memberRespond = $queryRespond->result_array();
			if( count($memberRespond) == 0 ) {
				$newBoard = true;
			}
			// 登入會員為被邀請者
			else {
				$returnData = array('status'=>'success',
									'forum_id'=>$memberRespond[0]['id']
									);
				echo json_encode($returnData);
				return;
			}
		}
		// 登入會員為邀請者
		else {
			$returnData = array('status'=>'success',
								'forum_id'=>$memberInvite[0]['id']
								);
			echo json_encode($returnData);
			return;
		}
		
		// 開新討論板 登入會員為邀請者
		if( $newBoard == true ) {
			$newBoardInsertArr = array(
				'm_id' => $m_id,
				't_id' => $t_id,
				'status' => '0',
				'start_time' => time()
			);
			$this->db->insert('m_chat_borad', $newBoardInsertArr);
			$newFourmID = $this->db->insert_id();
			$returnData = array('status'=>'success',
								'forum_id'=>$newFourmID
								);
			echo json_encode($returnData);
			return;
		}
		
	}
	
	// 留言儲存
	function post_msg(){
		$m_id = $this->nativesession->get('LOGIN_ID');
		$t_id = (int)$_POST['t_id'];
		$b_id = (int)$_POST['b_id'];
		$chatContent = $_POST['chatContent'];
		
		// 更新聊天室時間
		$updateArr = array('update_time'=>time());
		$this->db->where('id',$b_id);
		$this->db->update('m_chat_borad',$updateArr);
		
		// 會員資料
		$querySelf = $this->db->get_where('m_member',array('id'=>$m_id));
		$memberSelf = $querySelf->result_array();
		if( $memberSelf[0]['picture'] == '' ) {
			$picSelf = 'assets/img/head/defaultHead.jpg';
		}
		else {
			if( $memberSelf[0]['e_picture'] == '' ) {
				$picSelf = 'assets/pics/head/'.$m_id.'/'.$memberSelf[0]['picture'];
			}
			else {
				$picSelf = 'assets/pics/head/'.$m_id.'/'.$memberSelf[0]['e_picture'];
			}
		}
		$selfName = ($memberSelf[0]['name'] == '') ? $memberSelf[0]['account'] : $memberSelf[0]['name'];
		
		// 訊息儲存
		$newMsgInsertArr = array(
			'b_id' => $b_id,
			'm_id' => $m_id,
			't_id' => $t_id,
			'content' => $chatContent,
			'add_time' => time()
		);
		$this->db->insert('m_chat_msg', $newMsgInsertArr);
		$newMsgID = $this->db->insert_id();
		$msgTime = '<span class="msgtime">'.date('Y-m-d H:i:s',time()).'</span>';
		$show_msg = '<div class="msglist post_left" id="msgID_'.$newMsgID.'">
						<div class="selfimg">
							<img src="'.$picSelf.'" class="img-responsive img-circle">
							<b>'.$selfName.'</b>
						</div>
						<div class="selfmsg">
							'.nl2br($chatContent).'<br/>
							'.$msgTime.'
						</div>
					</div>'; 
		
		$returnData = array('status'=>'success',
							'newMsgID'=>$newMsgID,
							'msg' => $show_msg
							);
		echo json_encode($returnData);
		return;
	}
	
	// 找朋友列表 下一頁
	function addpage(){
		$pageid = $_POST['page'];
		$id = $this->nativesession->get('LOGIN_ID');
		
		require(APPPATH.'meetconfig/area.inc.php');
		require(APPPATH.'meetconfig/status.inc.php');
		
		// 狀態編號
		$querySID = $this->db->get_where('m_status',array('m_id'=>$id));
		$SIDarr = $querySID->result_array();
		$statusID = $SIDarr[0]['status_tag'];
		
		// 存取最近狀態的會員
		$prePage = 12;
		$pagelimit = ( $pageid == 1 ) ? 0 : ( $pageid-1 )*$prePage; //頁數
		if( $statusID == 0 ) {
			$listWhereArr = array('m_id !='=> $id);
		}
		else {
			$listWhereArr = array('status_tag' => $statusID,
									'm_id !='=> $id);
		}
		$this->db->where($listWhereArr);
		$this->db->order_by('update_time','DESC');
		$this->db->limit($prePage,$pagelimit);
		$queryStatus = $this->db->get('m_status');
		$statusListArr = $queryStatus->result_array();
		
		$list_HTML = '';
		$list_count = 0;
		$listMid = array();
		if( count($statusListArr) > 0 ) {
			foreach( $statusListArr as $statusTmpData ) {
				$this->db->where('id',$statusTmpData['m_id']);
				$queryMember = $this->db->get('m_member');
				$dataMemberArr = $queryMember->result_array();
				foreach( $dataMemberArr as $memberData ) {
					if( $memberData['picture'] != '' ) {
						if( $memberData['e_picture'] != '' ) {
							$pics = 'assets/pics/head/'.$memberData['id'].'/'.$memberData['e_picture'];
						}
						else {
							$pics = 'assets/pics/head/'.$memberData['id'].'/'.$memberData['picture'];
						}
					}
					else {
						$pics = 'assets/img/head/defaultHead.jpg';
					}
					
					$listName = ( $memberData['name'] == '' ) ? '' : '<div class="friendListItem">'.$memberData['name'].'</div>';
					
					if( mb_strlen($memberData['description'] , 'UTF-8') > 20 ) {
						$memberData['description'] = mb_substr($memberData['description'],0,20,"UTF8")."...";
					}
					$listDes = ( $memberData['description'] == '' ) ? '' : '<div class="friendListItem desStyle">'.$memberData['description'].'</div>';
					
					if ( $memberData['loc_reigon'] == '0' && $memberData['loc_section'] == '0' ) {
						$listLoc = '';
					}
					else {
						$listLoc = '<div class="friendListItem">'.$Regionid[$memberData['loc_reigon']].' '.$Sectionid[$memberData['loc_section']].'</div>';
					}
					
					$gender = ($memberData['gender'] == '2') ? '她' : '他';
					
					$list_HTML .= '<div class="col-xs-12 col-sm-6 col-md-4 friendListCover">
										<div class="friendListArea" onmouseover="showBtn(\''.$memberData['id'].'\')" onmouseout="hideBtn(\''.$memberData['id'].'\')">
											<div class="friendListPic">
												<img src="'.$pics.'">
											</div>
											<div class="friendListDetail">
												<div class="friendListItem">'.$memberData['account'].'</div>
												'.$listName.'
												'.$listLoc.'
												'.$listDes.'
												<div class="friendListItem fli-btn" id="conBtn_'.$memberData['id'].'">
													<a href="javascript:;" onclick="conversation('.$memberData['id'].')">
														<button class="btn btn-primary">留言給'.$gender.'</button>
													</a>
												</div>
											</div>
										</div>
									</div>';
				}
				$list_count++;
			}
			$nextpageID = $pageid+1;
			if( $list_count >= 12 ) {
				$list_HTML .= '<div class="col-xs-12 friendListAreaBottom" id="page_'.$nextpageID.'"><button class="btn btn-primary" onclick="addpage('.$nextpageID.')">下一頁</button></div>';
			}
			else {
				$list_HTML .= '<div class="col-xs-12 friendListAreaEnd">已達列表底端</div>';
			}
		}
		else {
			$list_HTML = '<div class="col-xs-12 friendListAreaEnd">已達列表底端</div>';
		}
		
		$returnData = array();
		$returnData['status'] = 'success';
		$returnData['list_HTML'] = $list_HTML;
		echo json_encode($returnData);
	}
	
	// 留言板列表 下一頁
	function addpage_boardlist(){
		$pageid = $_POST['page'];
		$m_id = $this->nativesession->get('LOGIN_ID');
		
		$prePage = 8;
		$pagelimit = ( $pageid == 1 ) ? 0 : ( $pageid-1 )*$prePage; //頁數
		$this->db->where('update_time !=', '0');
		$this->db->where("(m_id = '$m_id' OR t_id = '$m_id')", null, false);
		$this->db->order_by('update_time','DESC');
		$this->db->limit($prePage,$pagelimit);
		$queryBoard = $this->db->get('m_chat_borad');
		$msgBoard = $queryBoard->result_array();
		
		$boardList_HTML = '';
		$boardList_count = 0;
		if( count($msgBoard) > 0 ) {
			foreach($msgBoard as $boardData) {
				// 取出該訊息詳細
				$msgWhereNewest = array('b_id'=>$boardData['id']);
				$this->db->where($msgWhereNewest);
				$this->db->order_by('add_time','DESC');
				$this->db->limit(1);
				$queryMsgNewest = $this->db->get('m_chat_msg');
				$msgNewest = $queryMsgNewest->result_array();
				
				if( count($msgNewest) > 0 ) {
					// 最新留言為自己
					if( $msgNewest[0]['m_id'] == $m_id ) {
						$partnerID = $msgNewest[0]['t_id'];
						$labelMsg = '<span class="label label-warning labelList">已回覆</span>';
					}
					// 最新留言為對方
					else {
						$partnerID = $msgNewest[0]['m_id'];
						
						if( $msgNewest[0]['status'] == '0' ) {
							$labelMsg = '<span class="label label-primary labelList">新留言</span>';
						}
						else {
							$labelMsg = '';
						}
					}
					
					// 取出會員資料
					$queryMember = $this->db->get_where('m_member',array('id'=>$partnerID));
					$msgMember = $queryMember->result_array();
					
					$memberName = ($msgMember[0]['name'] == '') ? $msgMember[0]['account'] : $msgMember[0]['name'];
					if( $msgMember[0]['picture'] == '' ) {
						$picMember = 'assets/img/head/defaultHead.jpg';
					}
					else {
						if( $msgMember[0]['e_picture'] == '' ) {
							$picMember = 'assets/pics/head/'.$partnerID.'/'.$msgMember[0]['picture'];
						}
						else {
							$picMember = 'assets/pics/head/'.$partnerID.'/'.$msgMember[0]['e_picture'];
						}
					}
					
					$msgContent = ( mb_strlen($msgNewest[0]['content'] , 'UTF-8') > 70 ) ? mb_substr($msgNewest[0]['content'],0,70,"UTF8")."..." : $msgNewest[0]['content'];
					$msgTime = date('Y-m-d H:i:s',$msgNewest[0]['add_time']);
					
					$boardList_HTML .= '<div class="boardItem">
											<a href="forum/'.$boardData['id'].'/'.$partnerID.'">
												<div class="col-xs-4 col-md-2">
													<img src="'.$picMember.'" class="img-responsive img-circle">
												</div>
												<div class="col-xs-8 col-md-10">
													<ul>
														<li><b>'.$memberName.'</b>'.$labelMsg.'</li>
														<li>'.$msgContent.'</li>
														<li class="msgTime">'.$msgTime.'</li>
													</ul>
												</div>
											</a>
										</div>';
				}
				$boardList_count++;
			}
			$nextpageID = $pageid+1;
			if($boardList_count >= 8) {
				$boardList_HTML .= '<div class="boardBottom" id="page_'.$nextpageID.'">
										<button class="btn btn-primary" onclick="addpage_boardlist('.$nextpageID.')">下一頁</button>
									</div>';
			}
			else{
				$boardList_HTML .= '<div class="listbottom">已達列表底端</div>';
			}
		}
		else {
			$boardList_HTML = '<div class="listbottom">已達列表底端</div>';
		}
		
		$returnData = array();
		$returnData['status'] = 'success';
		$returnData['boardList_HTML'] = $boardList_HTML;
		echo json_encode($returnData);
	}
	
	// 留言板 下一頁
	function addpage_forum(){
		$b_id = $_POST['b_id'];
		$m_id = $this->nativesession->get('LOGIN_ID');
		$t_id = $_POST['t_id'];
		$pageid = $_POST['page'];
		
		// 會員資料
		$querySelf = $this->db->get_where('m_member',array('id'=>$m_id));
		$memberSelf = $querySelf->result_array();
		if( $memberSelf[0]['picture'] == '' ) {
			$picSelf = 'assets/img/head/defaultHead.jpg';
		}
		else {
			if( $memberSelf[0]['e_picture'] == '' ) {
				$picSelf = 'assets/pics/head/'.$m_id.'/'.$memberSelf[0]['picture'];
			}
			else {
				$picSelf = 'assets/pics/head/'.$m_id.'/'.$memberSelf[0]['e_picture'];
			}
		}
		$selfName = ($memberSelf[0]['name'] == '') ? $memberSelf[0]['account'] : $memberSelf[0]['name'];
		
		$queryPartner = $this->db->get_where('m_member',array('id'=>$t_id));
		$memberPartner = $queryPartner->result_array();
		if( $memberPartner[0]['picture'] == '' ) {
			$picPartner = 'assets/img/head/defaultHead.jpg';
		}
		else {
			if( $memberPartner[0]['e_picture'] == '' ) {
				$picPartner = 'assets/pics/head/'.$t_id.'/'.$memberPartner[0]['picture'];
			}
			else {
				$picPartner = 'assets/pics/head/'.$t_id.'/'.$memberPartner[0]['e_picture'];
			}
		}
		$partnerName = ($memberPartner[0]['name'] == '') ? $memberPartner[0]['account'] : $memberPartner[0]['name'];
		
		$CHAT_HTML = '';
		$chat_count = 0;
		$prePage = 20;
		$pagelimit = ( $pageid == 1 ) ? 0 : ( $pageid-1 )*$prePage; //頁數
		$listWhereArr = array('b_id'=>$b_id);
		$this->db->where($listWhereArr);
		$this->db->order_by('add_time','DESC');
		$this->db->limit($prePage,$pagelimit);
		$queryChat = $this->db->get('m_chat_msg');
		$chatHistory = $queryChat->result_array();
		if( count($chatHistory) > 0 ) {
			foreach( $chatHistory as $chatData ) {
				$msgTime = '<span class="msgtime">'.date('Y-m-d H:i:s',$chatData['add_time']).'</span>';
				// 自己
				if( $chatData['m_id'] == $m_id) {
					$CHAT_HTML .= '<div class="msglist" id="msgID_'.$chatData['id'].'">
									<div class="selfimg">
										<img src="'.$picSelf.'" class="img-responsive img-circle">
										<b>'.$selfName.'</b>
									</div>
									<div class="selfmsg">
										'.nl2br($chatData['content']).'<br/>
										'.$msgTime.'
									</div>
									</div>'; 
				}
				// 對象
				if( $chatData['m_id'] == $t_id ) {
					$unread = ( $chatData['status'] == '0' ) ? 'unread' : '';
					$CHAT_HTML .= '<div class="msglist '.$unread.'" id="msgID_'.$chatData['id'].'">
									<div class="partnerimg">
										<img src="'.$picPartner.'" class="img-responsive img-circle">
										<b>'.$partnerName.'</b>
									</div>
									<div class="partnermsg">
										'.nl2br($chatData['content']).'<br/>
										'.$msgTime.'
									</div>
									</div>'; 
				}
				$chat_count++;
			}
			$nextpageID = $pageid+1;
			if( $chat_count >= 20 ) {
				$CHAT_HTML .= '<div class="forumBottom" id="page_'.$nextpageID.'">
									<button class="btn btn-primary" onclick="addpage_forum('.$nextpageID.')">下一頁</button>
								</div>';
			}
			else {
				$CHAT_HTML .= '<div class="forumEnd">已無任何留言</div>';
			}
		}
		else {
			$CHAT_HTML = '<div class="forumEnd">已無任何留言</div>';
		}
		
		$returnData = array();
		$returnData['status'] = 'success';
		$returnData['chatList_HTML'] = $CHAT_HTML;
		echo json_encode($returnData);
	}
	
	// 帳號驗證
	function checkAccount(){
		$whereArr['account'] = $_POST['accountVal'];
		$query = $this->db->get_where('m_member', $whereArr);
		$query_arr = $query->result_array();
		if( count($query_arr) > 0 ) {
			echo 'fail';
		}
		else {
			echo 'success';
		}
	}
	
	// 電子郵件驗證
	function checkEmail(){
		$whereArr['email'] = $_POST['mailVal'];
		$query = $this->db->get_where('m_member', $whereArr);
		$query_arr = $query->result_array();
		if( count($query_arr) > 0 ) {
			echo 'fail';
		}
		else {
			echo 'success';
		}
	}
	
	// 提供地區option
	function get_section(){
		// 載入地區與類型設定檔
		require(APPPATH .'meetconfig/area.inc.php');

		$r_id = $_POST['regionid'];
		$HTML = '';

		foreach( $Area_rel[$r_id] as $key => $val ) {
			$HTML .= '<option value="'.$val.'" >'.$Sectionid[$val].'</option>';
		}

		echo $HTML;
	}
	
	// 驗證碼檢查
	function check_captcha() {
		if($this->nativesession->get('check_number') == $_POST['captcha']) {
			echo 'success';
		}
		else {
			echo 'fail';
		}
	}
}
?>