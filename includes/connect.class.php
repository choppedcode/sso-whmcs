<?php
require(dirname(__FILE__).'/connectmaster.class.php');
/**
 * WHMCS SSO connect class
 * @package SSO_WHMCS
 *
 */
class sso_connect extends sso_connect_master {
	function url() {
		$url=sso_get_addon('sso_url') ? sso_get_addon('sso_url') : 'http://sso.clientcentral.info/';
		if (substr($url,-1) != '/') $url.='/';
		return $url;
	}

	function is_user_logged_in() {
		if (isset($_SESSION['adminid'])) return true;
		elseif (isset($_SESSION['uid'])) return true;
		else return false;

	}

	function log($msg) {
		if (is_array($msg)) $msg=print_r($msg,true);
		logActivity($msg);
	}


	function get_key() {
		return sso_get_addon('sso_key');
	}

	function hash() {
		return md5(sso_get_addon('sso_key'));
	}

	function login_url($redirect='',$params) {
		global $CONFIG;
		if (isset($params['email'])) {
			$timestamp=time();
			$autoauthkey=sso_get_addon('autoauthkey');
			$hash = sha1($params['email'].$timestamp.$autoauthkey);
			return $CONFIG['SystemURL'].'/dologin.php?email='.$params['email'].'&timestamp='.$timestamp.'&hash='.$hash.'&goto='.urlencode($redirect);
		} else {
			return $CONFIG['SystemURL'].'/login.php';
		}
	}

	function logout_url($redirect='') {
		global $CONFIG;
		return $CONFIG['SystemURL'].'/logout.php';
	}

	function invalidate() {
		return isset($_REQUEST['incorrect']) ? 1 : 0;
	}

	function setInvalidate() {
		//$_SESSION['cdssoinvalidate']=1;
	}

	function unsetInvalidate() {
		//$_SESSION['cdssoinvalidate']=0;
	}

}
