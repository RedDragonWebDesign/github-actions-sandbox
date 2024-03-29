<?php
/*
 * BlueThrust Clan Scripts
 * Copyright 2014
 *
 * Author: Bluethrust Web Development
 * E-mail: support@bluethrust.com
 * Website: http://www.bluethrust.com
 *
 * License: http://www.bluethrust.com/license.php
 *
 */
$prevFolder = "../../";
require_once("../../_setup.php");
require_once("../../classes/member.php");
require_once("../../classes/rank.php");
require_once("../../classes/btplugin.php");
require_once("../../classes/consolecategory.php");

// Plugin Info

$PLUGIN_TABLE_NAME = $dbprefix."facebook";
$PLUGIN_NAME = "Facebook Login";

$arrAPIKeys = array(
	'appID' => "",
	'appSecret' => ""
);


// Start Page

$consoleObj = new ConsoleOption($mysqli);

$cID = $consoleObj->findConsoleIDByName("Plugin Manager");
$consoleObj->select($cID);
$consoleInfo = $consoleObj->get_info_filtered();


$member = new Member($mysqli);
$member->select($_SESSION['btUsername']);

$pluginObj = new btPlugin($mysqli);

// Check Login
$LOGIN_FAIL = true;

if ($member->authorizeLogin($_SESSION['btPassword']) && $member->hasAccess($consoleObj)) {
	$countErrors = 0;
	$dispError = array();

	// Check if already installed

	if (in_array($_POST['pluginDir'], $pluginObj->getPlugins("filepath"))) {
		$countErrors++;
		$dispError[] = "The selected plugin is already installed!";
	}

	// Check if plugin table name interferes with other tables

	$result = $mysqli->query("SHOW TABLES");

	while ($row = $result->fetch_array()) {
		if ($row[0] == $PLUGIN_TABLE_NAME) {
			$countErrors++;
			$dispError[] = "There is database table that conflicts with this plugin.";
		}
	}

	if ($countErrors == 0) {
		$sql = "
		
		
		CREATE TABLE IF NOT EXISTS `".$dbprefix."facebook` (
		  `fbconnect_id` int(11) NOT NULL AUTO_INCREMENT,
		  `facebook_id` varchar(255) NOT NULL,
		  `member_id` int(11) NOT NULL,
		  `access_token` int(255) NOT NULL,
		  `name` varchar(255) NOT NULL,
		  `lastupdate` int(11) NOT NULL,
		  PRIMARY KEY (`fbconnect_id`)
		) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;
		
		
		";

		if ($mysqli->query($sql)) {
			$jsonAPIKey = json_encode($arrAPIKeys);
			$pluginObj->addNew(array("name", "filepath", "dateinstalled", "apikey"), array($PLUGIN_NAME, $_POST['pluginDir'], time(), $jsonAPIKey));

			// Check if need to add new console category

			$result = $mysqli->query("SELECT consolecategory_id FROM ".$dbprefix."consolecategory WHERE name = 'Social Media Connect'");
			if ($result->num_rows == 0) {
				$consoleCatObj = new ConsoleCategory($mysqli);
				$newOrderNum = $consoleCatObj->getHighestOrderNum()+1;
				$consoleCatObj->addNew(array("name", "ordernum"), array("Social Media Connect", $newOrderNum));
				$consoleCatID = $consoleCatObj->get_info("consolecategory_id");
			} else {
				$row = $result->fetch_assoc();
				$consoleCatID = $row['consolecategory_id'];
			}

			$consoleObj->setCategoryKeyValue($consoleCatID);
			$newSortNum = $consoleObj->getHighestSortNum()+1;
			$consoleObj->addNew(array("consolecategory_id", "pagetitle", "filename", "sortnum"), array($consoleCatID, $PLUGIN_NAME, "../plugins/facebook/facebookconnect.php", $newSortNum));
		} else {
			$countErrors++;
			$dispError[] = "Unable to create plugin database table.";
		}
	}

	$arrReturn = array();
	if ($countErrors == 0) {
		$arrReturn['result'] = "success";
	} else {
		$arrReturn['result'] = "fail";
		$arrReturn['errors'] = $dispError;
	}


	echo json_encode($arrReturn);
}
