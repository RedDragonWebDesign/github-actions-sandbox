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

if (!isset($member) || substr($_SERVER['PHP_SELF'], -11) != "console.php") {
	exit();
} else {
	$memberInfo = $member->get_info();
	$consoleObj->select($_GET['cID']);
	if (!$member->hasAccess($consoleObj)) {
		exit();
	}
}


$rankInfo = $memberRank->get_info_filtered();
if ($memberInfo['promotepower'] != 0) {
	$rankInfo['promotepower'] = $memberInfo['promotepower'];
} elseif ($memberInfo['promotepower'] == -1) {
	$rankInfo['promotepower'] = 0;
}

$cID = $_GET['cID'];

$dispError = "";
$countErrors = 0;
if ($memberInfo['rank_id'] == 1) {
	$maxOrderNum = $mysqli->query("SELECT MAX(ordernum) FROM ".$dbprefix."ranks WHERE rank_id != '1'");
	$arrMaxOrderNum = $maxOrderNum->fetch_array(MYSQLI_NUM);

	if ($maxOrderNum->num_rows > 0) {
		$result = $mysqli->query("SELECT rank_id FROM ".$dbprefix."ranks WHERE ordernum = '".$arrMaxOrderNum[0]."'");
		$row = $result->fetch_assoc();
		$rankInfo['promotepower'] = $row['rank_id'];
	}
}

$rankObj = new Rank($mysqli);

$rankObj->select($rankInfo['promotepower']);
$maxRankInfo = $rankObj->get_info_filtered();

if ($rankInfo['rank_id'] == 1) {
	$maxRankInfo['ordernum'] += 1;
} else {
	$maxRankInfo['ordernum'] -= 1;
}

$arrRanks = array();
$result = $mysqli->query("SELECT * FROM ".$dbprefix."ranks WHERE ordernum <= '".$maxRankInfo['ordernum']."' AND rank_id != '1' ORDER BY ordernum DESC");
while ($row = $result->fetch_assoc()) {
	$arrRanks[] = $row['rank_id'];
}


if ( ! empty($_POST['submit']) ) {
	// Check Member

	if (!$member->select($_POST['member']) || $_POST['member'] == $memberInfo['member_id']) {
		$countErrors++;
		$dispError = "&nbsp;&nbsp;&nbsp;<b>&middot;</b> You selected an invalid member.<br>";
	} elseif (!in_array($member->get_info("rank_id"), $arrRanks)) {
		$countErrors++;
		$dispError = "&nbsp;&nbsp;&nbsp;<b>&middot;</b> You may not disable the selected member.<br>";
	}


	if ($countErrors == 0) {
		if ($member->update(array("disabled", "disableddate"), array(1, time()))) {
			$logMessage = "Disabled ".$member->getMemberLink().".";
			$logMessage .= $_POST['reason'] ? "<br><br><b>Reason:</b><br>".filterText($_POST['reason']) : "";

			echo "
			
				<div style='display: none' id='successBox'>
					<p align='center'>
						Successfully disabled ".$member->getMemberLink()."!
					</p>
				</div>
				
				<script type='text/javascript'>
					popupDialog('Disable Member', '', 'successBox');
				</script>
			
			
			";

			$member->select($memberInfo['member_id']);
			$member->logAction($logMessage);
		} else {
			$countErrors++;
			$dispError .= "&nbsp;&nbsp;&nbsp;<b>&middot;</b> Unable to save information to the database.  Please contact the website administrator.<br>";
		}
	}


	if ($countErrors > 0) {
	}
}

$sqlRanks = "('".implode("','", $arrRanks)."')";
$result = $mysqli->query("SELECT * FROM ".$dbprefix."members INNER JOIN ".$dbprefix."ranks ON ".$dbprefix."members.rank_id = ".$dbprefix."ranks.rank_id WHERE ".$dbprefix."members.rank_id IN ".$sqlRanks." AND ".$dbprefix."members.disabled = '0' AND ".$dbprefix."members.member_id != '".$memberInfo['member_id']."'  ORDER BY ".$dbprefix."ranks.ordernum DESC, ".$dbprefix."members.username");
while ($row = $result->fetch_assoc()) {
	$rankObj->select($row['rank_id']);
	$memberoptions .= "<option value='".$row['member_id']."'>".$rankObj->get_info_filtered("name")." ".filterText($row['username'])."</option>";
}

echo "

<form action='".$MAIN_ROOT."members/console.php?cID=".$cID."' method='post'>
<div class='formDiv'>
";

if ($dispError != "") {
	echo "
	<div class='errorDiv'>
	<strong>Unable to disable member because the following errors occurred:</strong><br><br>
	$dispError
	</div>
	";
}

echo "
	Use the form below to disable a member. <br><br>
	<b><u>NOTE:</u></b> Disabling a member will not fully delete them from the website.  A disabled member will not be allowed to log in and will not be listed anywhere on the website.<br><br>
			<table class='formTable'>
				<tr>
					<td class='formLabel'>Member:</td>
					<td class='main'><select name='member' id='memberselect' class='textBox'><option value=''>Select</option>".$memberoptions."</select></td>
				</tr>
				<tr>
					<td class='formLabel' valign='top'>Reason:</td>
					<td class='main' valign='top'><textarea name='reason' cols='40' rows='3' class='textBox'>".$_POST['reason']."</textarea></td>
				</tr>
				<tr>
					<td class='main' align='center' colspan='2'><br>		
						<input type='submit' name='submit' value='Disable Member' class='submitButton'>
					</td>
				</tr>
			</table>
		</div>
	</form>


";
