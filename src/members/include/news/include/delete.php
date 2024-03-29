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

require_once("../../../../_setup.php");
require_once("../../../../classes/member.php");
require_once("../../../../classes/rank.php");
require_once("../../../../classes/news.php");

// Start Page

$consoleObj = new ConsoleOption($mysqli);

$cID = $consoleObj->findConsoleIDByName("Manage News");
$consoleObj->select($cID);
$consoleInfo = $consoleObj->get_info_filtered();
$consoleTitle = $consoleInfo['pagetitle'];



$member = new Member($mysqli);
$member->select($_SESSION['btUsername']);


$newsObj = new News($mysqli);

// Check Login
$LOGIN_FAIL = true;
if ($member->authorizeLogin($_SESSION['btPassword']) && $member->hasAccess($consoleObj) && $newsObj->select($_POST['nID'])) {
	if ($_POST['confirm'] == 1) {
		$newsObj->delete();
		require_once("newslist.php");
	} else {
		echo "
		
			<p align='center'>
				Are you sure you want to delete this news post?
			</p>
			
		";
	}
} else {
	echo $_POST['nID'];
}
