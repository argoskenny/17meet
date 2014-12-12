<?php
class Meet extends CI_Controller {
	
	// 建構子
	public function __construct(){
		parent::__construct();
		$this->load->model('meet_model');
		
		// 引入連結
		$this->load->helper('url');
		
		// 引入SESSION
		$this->load->library('nativesession');
		
		// 引入COOKIE
		$this->load->helper('cookie');
		
		// 引入資料庫套件
		$this->load->database();
	}
	
	// 首頁 登入
	public function index(){
		
		// COOKIE條件選單 必須擺在程式最前面
		//$data = $this->cookie_option();
		$LOGIN_ID = $this->nativesession->get('LOGIN_ID');
		if( $LOGIN_ID ) {
			header('location:'.base_url().'member/'.$LOGIN_ID);
		}
		
		$data['title'] = '17MEET';
		$this->load->view('meet/index',$data);
	}
	
	// 註冊
	public function register(){
		
		// COOKIE條件選單 必須擺在程式最前面
		//$data = $this->cookie_option();
		
		$data['title'] = '會員註冊';
		$this->load->view('meet/register',$data);
	}
	
	// 新註冊會員儲存
	public function newreg(){
		$inputAccount = $_POST['inputAccount'];
		$inputPassword1 = $_POST['inputPassword1'];
		$inputPassword2 = $_POST['inputPassword2'];
		$inputEmail = $_POST['inputEmail'];
		
		// 驗證格式
		if( ctype_alnum($inputAccount) == true 
		&& ctype_alnum($inputPassword1) == true 
		&& ctype_alnum($inputPassword2) == true ) {
			if( strlen($inputAccount) < 3 ) {
				header('location:'.base_url().'/register');
			}
			if( strlen($inputPassword1) < 4 ) {
				header('location:'.base_url().'/register');
			}
			if( $inputPassword1 != $inputPassword2 ) {
				header('location:'.base_url().'/register');
			}
			if( !filter_var($inputEmail, FILTER_VALIDATE_EMAIL) ) {
				header('location:'.base_url().'/register');
			}
		}
		else {
			header('location:'.base_url().'/register');
		}
		
		$checkWhere = array('account'=>$inputAccount);
		$queryCheck = $this->db->get_where('m_member',$checkWhere);
		$checkArr = $queryCheck->result_array();
		if( count($checkArr) == 0 ) {
			$dataInsert = array(
				'account' => $inputAccount,
				'password' => $inputPassword1,
				'email' => $inputEmail,
				'reg_time' => time()
			);
			$this->db->insert('m_member',$dataInsert); 
			$id = $this->db->insert_id();
			
			// 存入狀態
			$statusGroupID = rand(1,10);
			$statusInsertArr = array(
				'm_id' => $id,
				'status_tag' => 0,
				'update_time' => time(),
				'group_id' => $statusGroupID	
			);
			$this->db->insert('m_status', $statusInsertArr);
		}
		$data['title'] = '會員註冊';
		$this->load->view('meet/newreg',$data);
	}
	
	// 會員首頁
	public function member($id){
		// SESSION CHECK
		$id = (int)$id;
		$this->session_check($id);
		
		require(APPPATH.'meetconfig/area.inc.php');
		require(APPPATH.'meetconfig/status.inc.php');
		
		// 存取會員資料
		$whereArr['id'] = $id;
		$query = $this->db->get_where('m_member',$whereArr);
		$dataArr = $query->result_array();
		
		// 基本資料
		if( $dataArr[0]['name'] == '' ) {
			$memberName = '尚無資料';
		}
		else {
			$memberName = $dataArr[0]['name'];
		}
		
		if( $dataArr[0]['mobile'] == 0 ) {
			$memberMobile = '尚無資料';
		}
		else {
			$memberMobile = '0'.$dataArr[0]['mobile'];
		}
		
		switch( $dataArr[0]['gender'] ) {
			case '0':
			default:
				$memberGender = '尚無資料';
				$data['gender1Check'] = '';
				$data['gender2Check'] = '';
			break;
			case '1':
				$memberGender = '男性';
				$data['gender1Check'] = 'checked';
				$data['gender2Check'] = '';
			break;
			case '2':
				$memberGender = '女性';
				$data['gender1Check'] = '';
				$data['gender2Check'] = 'checked';
			break;
		}
		
		if( $dataArr[0]['birth_year'] == '0' || 
			$dataArr[0]['birth_month'] == '0' || 
			$dataArr[0]['birth_day'] == '0' ) {
			$memberBirthday = '尚無資料';
		}
		else {
			$memberBirthday = $dataArr[0]['birth_year'].' / ';
			$memberBirthday .= $dataArr[0]['birth_month'].' / ';
			$memberBirthday .= $dataArr[0]['birth_day'];
		}
		$yearNow = date("Y",time());
		$yearNow = (int)$yearNow;
		// 年
		$memberBirthdayYearStr = '';
		for($yearCount = 1890; $yearCount <= $yearNow; $yearCount++ ) {
			if( $dataArr[0]['birth_year'] != 0 && $dataArr[0]['birth_year'] == $yearCount ) {
				$memberBirthdayYearStr .= '<option value="'.$yearCount.'" selected>'.$yearCount.'</option>';
			}
			else {
				$memberBirthdayYearStr .= '<option value="'.$yearCount.'">'.$yearCount.'</option>';
			}
		}
		// 月
		$memberBirthdayMonthStr = '';
		for($monthCount = 1; $monthCount < 13; $monthCount++ ) {
			if( $dataArr[0]['birth_month'] != 0 && $dataArr[0]['birth_month'] == $monthCount ) {
				$memberBirthdayMonthStr .= '<option value="'.$monthCount.'" selected>'.$monthCount.'</option>';
			}
			else {
				$memberBirthdayMonthStr .= '<option value="'.$monthCount.'">'.$monthCount.'</option>';
			}
		}
		// 日
		$memberBirthdayDayStr = '';
		for($dayCount = 1; $dayCount < 32; $dayCount++ ) {
			if( $dataArr[0]['birth_day'] != 0 && $dataArr[0]['birth_day'] == $dayCount ) {
				$memberBirthdayDayStr .= '<option value="'.$dayCount.'" selected>'.$dayCount.'</option>';
			}
			else {
				$memberBirthdayDayStr .= '<option value="'.$dayCount.'">'.$dayCount.'</option>';
			}
		}
		
		if( $dataArr[0]['loc_reigon'] == '0' || $dataArr[0]['loc_section'] == '0' ) {
			$memberLocation = '尚無資料';
		}
		else {
			$memberLocation = $Regionid[$dataArr[0]['loc_reigon']].' '.$Sectionid[$dataArr[0]['loc_section']];
		}
		$memberEmail = '<span class="cantEdit">'.$dataArr[0]['email'].'</span>';
		
		if( $dataArr[0]['description'] == '' ) {
			$memberDescription = '尚無資料';
			$memberDescriptionTextarea = '';
		}
		else {
			$memberDescription = nl2br($dataArr[0]['description']);
			$memberDescriptionTextarea = $dataArr[0]['description'];
		}
		
		// 基本資料 編輯選單
		$data['regionHTML'] = '<option value="">請選擇</option>';
		foreach( $Regionid as $key => $val ) {
			if( $dataArr[0]['loc_reigon'] == $key ) {
				$data['regionHTML'] .= "<option value='".$key."' selected='selected'>".$val."</option>";
			}
			else {
				$data['regionHTML'] .= "<option value='".$key."'>".$val."</option>";
			}
		}
		$data['sectionHTML'] = '';
		if( $dataArr[0]['loc_section'] != '0' ) {
			foreach( $Area_rel[$dataArr[0]['loc_reigon']] as $key => $val ) {
				if( $dataArr[0]['loc_section'] == $val ) {
					$data['sectionHTML'] .= "<option value='".$val."' selected='selected'>".$Sectionid[$val]."</option>";
				}
				else {
					$data['sectionHTML'] .= "<option value='".$val."'>".$Sectionid[$val]."</option>";
				}
			}
		}
		
		// 新手教學
		$data['tutorial'] = '';
		$daySinceRegtime = @ceil( (time() - $dataArr[0]['reg_time']) / 86400 );
		if( $dataArr[0]['name'] == '' 
			&& $dataArr[0]['mobile'] == 0 
			&& $dataArr[0]['gender'] == 0 
			&& $daySinceRegtime <= 3 ) {
			$data['tutorial'] = 'tutorial();';
		}
		
		// 頭像處理
		$data['autoEditShow'] = '';
		if( $dataArr[0]['picture'] == '' ) {
			$data['headPic'] = 'assets/img/head/defaultHead.jpg';
			$data['updateLock'] = 'disabled="disabled"';
			
		}
		else {
			$data['headPicSrc'] = 'assets/pics/head/'.$id.'/'.$dataArr[0]['picture'];
			$data['updateLock'] = '';
			if( $dataArr[0]['e_picture'] == '' ) {
				$data['headPic'] = 'assets/pics/head/'.$id.'/'.$dataArr[0]['picture'];
				if( !empty($_GET['edit']) && $_GET['edit'] == 'on' ) {
					$data['autoEditShow'] = '$("#edit_avater").modal("show");';
				}
			}
			else {
				$data['headPic'] = 'assets/pics/head/'.$id.'/'.$dataArr[0]['e_picture'];
			}
		}
		
		// 好友列表
		$friendListHTML = '';
		$friendIDArr = array();
		$whereArrFriendList = array();
		
		$whereArrFriendList['m_id'] = $id;
		$whereArrFriendList['status'] = '1';
		$whereArrFriendList['confirm_time != '] = '0';
		
		$queryFriendList = $this->db->get_where('m_friend_list',$whereArrFriendList);
		$dataArrFriendList = $queryFriendList->result_array();
		
		if( count($dataArrFriendList) > 0 ) {
			foreach( $dataArrFriendList as $key => $friendsID) {
				$friendIDArr[] = $friendsID['t_id'];
			}

			$this->db->select('id, account, picture');
			$this->db->from('m_member');
			$this->db->where_in('id', $friendIDArr);
			$queryFriendsData = $this->db->get();
			$dataFriendsDetail = $queryFriendsData->result_array();
			
			foreach( $dataFriendsDetail as $key => $friendsData) {
				$friendListHTML .= '';
			}
		}
		else {
			$friendListHTML = '<div class="nolist">尚未新增好友</div>';
		}
		
		// 目前狀態
		$this->db->select('status_tag');
		$this->db->from('m_status');
		$this->db->where('m_id',$id);
		$this->db->order_by('update_time', 'DESC');
		$this->db->limit(1);
		$queryStatus = $this->db->get();
		$dataStatus = $queryStatus->result_array();
		$statusID = ( empty($dataStatus[0]['status_tag']) ) ? 0 : $dataStatus[0]['status_tag'];
		$statusCurrent = $Statusdata[$statusID];
		$statusOption = '';
		foreach($Statusdata as $sID => $sText ) {
			$statusOption .= '<li><a href="javascript:void(0);" a_type="'.$sID.'">'.$sText.'</a></li>';
		}
		
		$data['title'] = '會員中心';
		
		$data['mid'] = $id;
		$data['account'] = $dataArr[0]['account'];
		$data['memberName'] = $memberName;
		$data['memberMobile'] = $memberMobile;
		$data['memberGender'] = $memberGender;
		$data['memberBirthday'] = $memberBirthday;
		$data['memberLocation'] = $memberLocation;
		$data['memberEmail'] = $memberEmail;
		
		$data['memberBirthdayYearStr'] = $memberBirthdayYearStr;
		$data['memberBirthdayMonthStr'] = $memberBirthdayMonthStr;
		$data['memberBirthdayDayStr'] = $memberBirthdayDayStr;
		
		$data['memberDescription'] = $memberDescription;
		$data['memberDescriptionTextarea'] = $memberDescriptionTextarea;
		
		$data['config']['regionid'] = $Regionid;
		
		$data['statusID'] = $statusID;
		$data['statusCurrent'] = $statusCurrent;
		$data['statusOption'] = $statusOption;
		
		$data['friendListHTML'] = $friendListHTML;
		
		// 通知中心
		$unreadData = $this->remindCenter($id);
		$data['unread_num'] = $unreadData['unread_num'];
		$data['remind_HTML'] = $unreadData['remind_HTML'];
		
		$this->load->view('meet/member',$data);
	}
	
	// 聊天室
	public function forum($b_id,$t_id){
		$m_id = $this->nativesession->get('LOGIN_ID');
		if( empty($m_id) ) {
			header('location:'.base_url());
		}
		
		$queryInvite = $this->db->get_where('m_chat_borad',array('id'=>$b_id,'m_id'=>$m_id,'t_id'=>$t_id));
		$memberInvite = $queryInvite->result_array();
		if( count($memberInvite) == 0 ) {
			$queryRespond = $this->db->get_where('m_chat_borad',array('id'=>$b_id,'m_id'=>$t_id,'t_id'=>$m_id));
			$memberRespond = $queryRespond->result_array();
			if( count($memberRespond) == 0 ) {
				header('location:'.base_url());
			}
			// 登入會員為被邀請者
			else {}
		}
		// 登入會員為邀請者
		else {}
		
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
		$listWhereArr = array('b_id'=>$b_id);
		$this->db->where($listWhereArr);
		$this->db->order_by('add_time','DESC');
		$this->db->limit(20);
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
			if( $chat_count >= 20 ) {
				$CHAT_HTML .= '<div class="forumBottom" id="page_2">
									<button class="btn btn-primary" onclick="addpage_forum(2)">下一頁</button>
								</div>';
			}
			$nolist = '0';
		}
		else {
			$CHAT_HTML = '<div class="nomsg">尚無任何留言訊息</div>';
			$nolist = '1';
		}
		
		// 更新對方訊息的閱讀狀態
		$this->db->update('m_chat_msg', array('status' => '1'), array('b_id' => $b_id,'m_id' => $t_id));
		
		// 存取個人資料
		$queryArr = $this->db->get_where('m_member',array( 'id' => $m_id ));
		$dataArr = $queryArr->result_array();
		
		$data['m_id'] = $m_id;
		$data['b_id'] = $b_id;
		$data['t_id'] = $t_id;
		$data['account'] = $dataArr[0]['account'];
		$data['partnerName'] = $partnerName;
		$data['CHAT_HTML'] = $CHAT_HTML;
		$data['nolist'] = $nolist;
		$data['title'] = '留言板';
		
		// 通知中心
		$unreadData = $this->remindCenter($m_id);
		$data['unread_num'] = $unreadData['unread_num'];
		$data['remind_HTML'] = $unreadData['remind_HTML'];
		
		$this->load->view('meet/forum',$data);
	}
	
	// 尋找好友列表
	public function friendlist($id,$pageid){
		
		// COOKIE條件選單 必須擺在程式最前面
		//$data = $this->cookie_option();
		$id = (int)$id;
		$this->session_check($id);
		
		require(APPPATH.'meetconfig/area.inc.php');
		require(APPPATH.'meetconfig/status.inc.php');
		
		// 驗證狀態編號
		$querySID = $this->db->get_where('m_status',array('m_id'=>$id));
		$SIDarr = $querySID->result_array();
		$statusID = $SIDarr[0]['status_tag'];
		
		$checkStatusID = false;
		foreach( $Statusdata as $sID => $sText) {
			if( $sID == $statusID ) {
				$checkStatusID = true;
			}
		}
		if( !$checkStatusID ) {
			header('location:'.base_url().'/member/'.$id);
		}
		
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
		
		$data['list_HTML'] = '';
		if( count($statusListArr) > 0 ) {
			foreach( $statusListArr as $statusTmpData ) {
				// 存取該會員資料
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
					
					if( mb_strlen($memberData['description'] , 'UTF-8') > 18 ) {
						$memberData['description'] = mb_substr($memberData['description'],0,18,"UTF8")."...";
					}
					$listDes = ( $memberData['description'] == '' ) ? '' : '<div class="friendListItem desStyle">'.$memberData['description'].'</div>';
					
					$listTime = '<div class="friendListItem updateTime">'.date("Y-m-d H:i",$statusTmpData['update_time']).'</div>';
					
					if ( $memberData['loc_reigon'] == '0' && $memberData['loc_section'] == '0' ) {
						$listLoc = '';
					}
					else {
						$listLoc = '<div class="friendListItem">'.$Regionid[$memberData['loc_reigon']].' '.$Sectionid[$memberData['loc_section']].'</div>';
					}
					$gender = ($memberData['gender'] == '2') ? '她' : '他';
					$data['list_HTML'] .= '<div class="col-xs-12 col-sm-6 col-md-4 friendListCover">
												<div class="friendListArea" onmouseover="showBtn(\''.$memberData['id'].'\')" onmouseout="hideBtn(\''.$memberData['id'].'\')">
													<div class="friendListPic">
														<img src="'.$pics.'">
													</div>
													<div class="friendListDetail">
														<div class="friendListItem">'.$memberData['account'].'</div>
														'.$listName.'
														'.$listLoc.'
														'.$listDes.'
														'.$listTime.'
														<div class="friendListItem fli-btn" id="conBtn_'.$memberData['id'].'">
															<a href="javascript:;" onclick="conversation('.$memberData['id'].')">
																<button class="btn btn-primary">留言給'.$gender.'</button>
															</a>
														</div>
													</div>
												</div>
											</div>';
				}
				
			}
			$nextpageID = $pageid+1;
			$data['list_HTML'] .= '<div class="col-xs-12 friendListAreaBottom" id="page_'.$nextpageID.'"><button class="btn btn-primary" onclick="addpage('.$nextpageID.')">下一頁</button></div>';
			
		}
		else {
			$data['list_HTML'] = '<div class="col-xs-12 nolist">暫無資料，請換個條件再試一次。</div>
								<a href="member/'.$id.'"><button class="btn btn-lg btn-primary btn-block back_btn" type="button" name="back" id="back">返回</button></a>';
		}
		
		// 存取個人資料
		$queryArr = $this->db->get_where('m_member',array( 'id' => $id ));
		$dataArr = $queryArr->result_array();
		
		$data['mid'] = $id;
		$data['account'] = $dataArr[0]['account'];
		$data['title'] = '尋找新朋友';
		
		// 通知中心
		$unreadData = $this->remindCenter($id);
		$data['unread_num'] = $unreadData['unread_num'];
		$data['remind_HTML'] = $unreadData['remind_HTML'];
		
		$this->load->view('meet/friendlist',$data);
	}
	
	// 通知中心
	public function chatboard($m_id){
		$m_id = (int)$m_id;
		$this->session_check($m_id);
		
		$this->db->where('update_time !=', '0');
		$this->db->where("(m_id = '$m_id' OR t_id = '$m_id')", null, false);
		$this->db->order_by('update_time','DESC');
		$this->db->limit(8,0);
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
				$boardList_count++;
			}
			
			if( $boardList_count >= 8 ){
				$boardList_HTML .= '<div class="boardBottom" id="page_2">
										<button class="btn btn-primary" onclick="addpage_boardlist(2)">下一頁</button>
									</div>';
			}
			else {
				$boardList_HTML .= '<div class="listbottom">已達列表底端</div>';
			}
		}
		else {
			$boardList_HTML = '<div class="nomsg">尚無任何留言訊息</div>';
		}
		
		$data['boardList_HTML'] = $boardList_HTML;
		
		// 存取個人資料
		$queryArr = $this->db->get_where('m_member',array( 'id' => $m_id ));
		$dataArr = $queryArr->result_array();
		
		$data['m_id'] = $m_id;
		$data['account'] = $dataArr[0]['account'];
		$data['title'] = '留言板列表';
		
		require(APPPATH.'meetconfig/area.inc.php');
		require(APPPATH.'meetconfig/status.inc.php');
		
		// 通知中心
		$unreadData = $this->remindCenter($m_id);
		$data['unread_num'] = $unreadData['unread_num'];
		$data['remind_HTML'] = $unreadData['remind_HTML'];
		
		$this->load->view('meet/chatboard',$data);
	} 
	
	// 通知選單小提示
	function remindCenter($m_id){
		$data['remind_HTML'] = '';
		$unreadArr = array();
		$unreadNewArr = array();
		$firstFive = 1;
		
		// 未讀留言數量
		$msgWhereArr = array('t_id'=>$m_id,'status'=>'0');
		$this->db->where($msgWhereArr);
		$this->db->order_by('id','ASC');
		$this->db->limit(200);
		$queryMsg = $this->db->get('m_chat_msg');
		$msgArr = $queryMsg->result_array();
		
		if( count($msgArr) > 0 ) {
			foreach($msgArr as $msgData) {
				$unreadArr[$msgData['b_id']] = $msgData;
			}
		}
		$data['unread_num'] = count($unreadArr);
		$data['unread_num'] = ( $data['unread_num'] == 0 ) ? '' : '<span class="badge">'.$data['unread_num'].'</span>';
		
		// 最新五則
		$this->db->where('update_time !=', '0');
		$this->db->where("(m_id = '$m_id' OR t_id = '$m_id')", null, false);
		$this->db->order_by('update_time','DESC');
		$this->db->limit(5);
		$queryBoard = $this->db->get('m_chat_borad');
		$msgBoard = $queryBoard->result_array();
		
		if( count($msgBoard) > 0 ) {
			foreach($msgBoard as $boardData) {
				// 取出該訊息詳細
				$msgWhereNewest = array('b_id'=>$boardData['id']);
				$this->db->where($msgWhereNewest);
				$this->db->order_by('add_time','DESC');
				$this->db->limit(1);
				$queryMsgNewest = $this->db->get('m_chat_msg');
				$msgNewest = $queryMsgNewest->result_array();
				
				// 最新留言為自己
				if( $msgNewest[0]['m_id'] == $m_id ) {
					$partnerID = $msgNewest[0]['t_id'];
					$labelMsg = '<span class="label label-warning labelRemind">已回覆</span>';
				}
				// 最新留言為對方
				else {
					$partnerID = $msgNewest[0]['m_id'];
					
					if( $msgNewest[0]['status'] == '0' ) {
						$labelMsg = '<span class="label label-primary labelRemind">新留言</span>';
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
				
				$msgContent = ( mb_strlen($msgNewest[0]['content'] , 'UTF-8') > 8 ) ? mb_substr($msgNewest[0]['content'],0,8,"UTF8")."..." : $msgNewest[0]['content'];
				$msgTime = date('Y-m-d H:i:s',$msgNewest[0]['add_time']);
				
				$data['remind_HTML'] .= '<li>
											<a href="forum/'.$boardData['id'].'/'.$partnerID.'">
												<img src="'.$picMember.'">
												<p>
													<b>'.$memberName.'</b><br/>
													'.$msgContent.'<br/>
													<span class="remindTime">'.$msgTime.'</span>
												</p>
												'.$labelMsg.'
											</a>
										</li>';
			}
		}
		
		$data['remind_HTML'] = ( $data['remind_HTML'] == '' ) ? '<div class="remind-no-list">尚無任何通知</div>' : $data['remind_HTML'];
		return $data;
	}
	
	// 圖片驗證
	public function CaptchaImg(){
		$im = imagecreate(60, 30);
		
		// 橘底白字
		$bg = imagecolorallocate($im, 249, 112, 92);
		$textcolor = imagecolorallocate($im, 255, 255, 255);
		
		// 初始化驗證碼
		$text = "";

		// 創建一個隨機函數包所需要的範圍
		$textAll = array_merge_recursive(range('A','Z'),range('a','z'),range('0','9'));

		$length = 4;
		for($i = 1; $i <= $length;)
		{
			 // 隨機取出一位數。
			 $ai = rand(0,61);
			 $val = $textAll[$ai];
			 if(($val != 'O') && ($val != 'o') && ($val != '0'))
			 {
				 $text.=$textAll[$ai];
				 $i++;
			 }
		}

		// 記入SESSION 驗證使用
		$this->nativesession->set('check_number',$text);
		
		// 寫入圖片
		imagestring($im, 6, 14, 8, $text, $textcolor);

		// 輸出圖片
		header('Content-type: image/png');
		imagepng($im);
		imagedestroy($im);
	}
	
	// 登出
	public function logout(){
		$this->nativesession->delete('LOGIN_ID');
		header('location:'.base_url());
	}
	
	// 登入驗證
	function session_check($id = ""){
		$LOGIN_ID = $this->nativesession->get('LOGIN_ID');
		if( empty($LOGIN_ID) ) {
			header('location:'.base_url());
		}
		if( $id != "" && $LOGIN_ID != $id ) {
			$this->nativesession->delete('LOGIN_ID');
			header('location:'.base_url());
		}
		return true;
	}
	
	// 檢查網址自帶變數是否為數字
	function check_segment($var){
		if(is_numeric($var) == false)
		{
			header('Location:'.base_url());
			exit;
		}
		else
		{
			return true;
		}
	}

}