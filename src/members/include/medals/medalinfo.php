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

require_once("../../../_setup.php");
require_once("../../../classes/member.php");
require_once("../../../classes/medal.php");


$member = new Member($mysqli);
$member->select($_SESSION['btUsername']);

$medalObj = new Medal($mysqli);

if ($member->authorizeLogin($_SESSION['btPassword']) && $medalObj->select($_POST['medalID'])) {
	$medalObj->refreshImageSize();
	$medalInfo = $medalObj->get_info_filtered();


	echo "
		<p align='center'>
			<img src='".$medalInfo['imageurl']."' width='".$medalInfo['imagewidth']."' height='".$medalInfo['imageheight']."'>
		</p>
		<p align='center'>
			<b><u>".$medalInfo['name']."</u></b><br>
			".$medalInfo['description']."
		</p>
	
	";
}
