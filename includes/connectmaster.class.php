<?php
/**
 * WHMCS SSO connect master class
 * @author Zingiri
 * @package SSO_WHMCS
 *
 */
class sso_connect_master {
	/**
	 * Connect to the SSO server
	 * @param string $action Action
	 * @param array $params Parameters
	 */
	function connect($action,$params=array()) {
		$url = $this->url().'api.php'; # URL to SSO API file
		$postfields["key"] = $this->get_key();
		$postfields["cookie"] = isset($_COOKIE['cdsso']) ? $_COOKIE['cdsso'] : '';

		$postfields["action"] = $action; #action performed by the API:Functions

		$postfields=array_merge($postfields,$params);

		$this->log('SSO connect: '.$url.' with post parameters '.print_r($postfields,true));

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_TIMEOUT, 100);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
		$data = curl_exec($ch);
		if (curl_errno($ch)) {
			$this->errno=curl_errno($ch);
			$this->error=curl_error($ch);
			$this->log('HTTP Error:'.$this->errno.'/'.$this->error.' at '.$url);
			return false;
		}

		curl_close($ch);

		if (empty($data)) $this->log('SSO returned no data');
		else $this->log('SSO returned: '.print_r($data,true));

		$results=json_decode($data,1);
		$this->log($results);
		if ($results["status"]!="success") {
			$this->log('Connection not successful, SSO returned unreadable data');
		}

		return $results;
	}

	function frame($redirect,$loginUrl) {
		$content='';
		$target=sso_connect::url().'auth.php?hash='.sso_connect::hash();
		$remote=sso_connect::url().'auth_helper.php?url='.urlencode($target);
		$remoteSwf='http://23dbd2b813a5bbdcf94f-ad7ae67fe93983be12b9863528b62e99.r9.cf1.rackcdn.com/easyxdm.swf';

		$content='<div id="sso-frame-container" style="display:block"></div>';
		$content.='<script type="text/javascript">';
		$content.='jQuery(document).ready(function($) {
		new easyXDM.Socket({
		    remote: "'.$remote.'",
	    	swf: "'.$remoteSwf.'",
	    	container: document.getElementById("sso-frame-container"),
	    	onMessage: function(message, origin){
		    	var server=jQuery.parseJSON(message);
		    	if (cdsso.debug==1) console.log(server);
		    	if (jQuery.cookie("cdsso") != server.cookie) {
		    		jQuery.removeCookie("cdsso");
		    		jQuery.cookie("cdsso", server.cookie, { expires: 2, path: "/" });
		    	}
		    	if (jQuery.cookie("cdssoli") != server.loggedin) {
		    		jQuery.removeCookie("cdssoli");
		    		jQuery.cookie("cdssoli", server.loggedin, { expires: 2, path: "/" });
		    	}
		    	if (cdsso.debug==1) console.log(cdsso);
		    	if ((server.loggedin == 0) && (cdsso.loggedin == 1)) window.location=cdsso.logouturl;
		    	if ((cdsso.loggedin == 0) && (server.loggedin == 1) && (cdsso.invalidate == 0)) window.location=cdsso.loginurl;
	    	}
		});
	});';
		$content.='var cdsso={};';
		$content.='cdsso.loggedin="'.intval(sso_connect::is_user_logged_in()).'";';
		$content.='cdsso.loginurl="'.$loginUrl.'";';
		$content.='cdsso.logouturl="'.sso_connect::logout_url( $redirect ).'";';
		$content.='cdsso.invalidate="'.sso_connect::invalidate().'";';
		$content.='cdsso.debug="'.sso_connect::debug().'";';
		$content.='</script>';
		return $content;

	}

	function debug() {
		return 0;
	}

}
