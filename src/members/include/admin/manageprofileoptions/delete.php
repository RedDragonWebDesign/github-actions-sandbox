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
require_once("../../../../classes/consoleoption.php");
require_once("../../../../classes/profileoption.php");
require_once("../../../../classes/profilecategory.php");


$consoleObj = new ConsoleOption($mysqli);
$member = new Member($mysqli);
$member->select($_SESSION['btUsername']);
$profileOptionObj = new ProfileOption($mysqli);
$profileCatObj = new ProfileCategory($mysqli);

$cID = $consoleObj->findConsoleIDByName("Manage Profile Options");
$consoleObj->select($cID);
$_GET['cID'] = $cID;



if ($member->authorizeLogin($_SESSION['btPassword'])) {
	$memberInfo = $member->get_info_filtered();

	if ($member->hasAccess($consoleObj) && $profileOptionObj->select($_POST['oID'])) {
		define("MEMBERRANK_ID", $memberInfo['rank_id']);


		if ($_POST['confirm'] == 1) {
			$profileOptionObj->delete();
			require_once("main.php");
		} else {
			$profileOptionName = $profileOptionObj->get_info_filtered("name");
			echo "<p align='center'>Are you sure you want to delete the profile option <b>".$profileOptionName."</b>?</p>";
		}
	} elseif (!$profileOptionObj->select($_POST['oID'])) {
		echo "<p align='center'>Unable find the selected profile option.  Please try again or contact the website administrator.</p>";
	}
}
