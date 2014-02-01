<?php
if (!defined("WHMCS")) die("This file cannot be accessed directly");

function sso_config() {
	$configarray = array(
    "name" => "Single-sign-on",
    "description" => "Single-sign-on accross multiple domains. Currently supports Wordpress & WHMCS.<br /><strong>Notice</strong>: single-sign-on is disabled when you are logged in as an admin in WHMCS or using the 'Login as Client' facility.",
	"version" => "1.0.3",
    "author" => "Zingiri",
    "language" => "english",
    "fields" => array(
	"sso_key" => array("FriendlyName" => "API key", "Type" => "text", "Size" => "45", "Description" => "The API key is an arbitrary string of characters you can generate yourself. It has to be unique accross all participating domains."),
	"sso_url" => array("FriendlyName" => "SSO URL", "Type" => "text", "Size" => "80", "Default" => 'http://sso.clientcentral.info', "Description" => 'The URL to the SSO server. Download the SSO server code <a href="https://github.com/choppedcode/sso-server" target="_blank">here</a>.'),
	"autoauthkey" => array("FriendlyName" => "AutoAuth key", "Type" => "text", "Size" => "45", "Description" => '<a href="http://docs.whmcs.com/AutoAuth" target="_blank">AutoAuth</a> stands for Automatic Authentication and is a method to be able to automatically log a user in from your own trusted third party code. The addon uses this method to have a user auto login if it has been logged in on one of the other applications managed with single-sign-on.'),
	));
	return $configarray;
}

