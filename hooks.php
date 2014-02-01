<?php
if (!defined("WHMCS")) die("This file cannot be accessed directly");

require(dirname(__FILE__).'/includes/connect.class.php');

function sso_get_addon($setting) {
	$sql="SELECT `value` FROM `tbladdonmodules` WHERE `module`='sso' and `setting`='".$setting."'";
	$result = full_query($sql);
	if ($data = mysql_fetch_array($result)) {
		if (count($data)>0) return $data["value"];
		else return false;
	}
	return false;
}
function sso_frame_header($vars) {
	$content="<script type='text/javascript' src='modules/addons/sso/js/cookie/jquery.cookie.js'></script>";
	$content.="<script type='text/javascript' src='http://23dbd2b813a5bbdcf94f-ad7ae67fe93983be12b9863528b62e99.r9.cf1.rackcdn.com/easyXDM.min.js?ver=3.5.1'></script>";
	return $content;
}

function sso_frame_footer($vars) {
	if (isset($_SESSION['adminid']) && $_SESSION['adminid']) return;
	$redirect='index.php';
	$email='';
	$cookie=isset($_COOKIE['cdsso']) ? $_COOKIE['cdsso'] : null;
	$key=sso_connect::get_key();
	if ($cookie) {
		$sso=new sso_connect();
		$result=$sso->connect('loggedin');
		if (isset($result['loggedin']) && $result['loggedin'] && isset($result['email'])) $email=$result['email'];
	}
	$loginUrl=sso_connect::login_url($redirect,array('email'=>$email));
	$content='';
	$content.=sso_connect::frame($redirect,$loginUrl);
	return $content;
}

function sso_logout($vars) {
	$sso=new sso_connect();
	$result=$sso->connect('logout');
}

function sso_login($vars) {
	if (!isset($_SESSION['adminid']) && isset($vars['userid']) && ($userId=$vars['userid'])) {
		$sql="SELECT * FROM `tblclients` WHERE `id`='".$userId."'";
		if (($result = full_query($sql)) && ($data = mysql_fetch_array($result))) {
			$sso=new sso_connect();
			$result=$sso->connect('login',array('firstname'=>$data['firstname'],'lastname'=>$data['lastname'],'email' => $data['email']));
		}
	}
}

add_hook('ClientAreaHeaderOutput',1,'sso_frame_header');
add_hook('ClientAreaFooterOutput',1,'sso_frame_footer');
add_hook('ClientLogout',2,'sso_logout');
add_hook('ClientLogin',3,'sso_login');

