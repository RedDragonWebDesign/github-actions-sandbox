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

if (!isset($member)) {
	exit();
} else {
	$memberInfo = $member->get_info_filtered();
	$consoleObj->select($_GET['cID']);
	if (!$member->hasAccess($consoleObj)) {
		exit();
	}
}


echo "
	<script type='text/javascript'>
	
		$(document).ready(function() {
			$('#breadCrumb').html(\"<a href='".$MAIN_ROOT."'>Home</a> > <a href='".$MAIN_ROOT."members'>My Account</a> > Manage Games Played\");
		});
	
	</script>
";


$cOptObj = new ConsoleOption($mysqli);
$intAddGamesPlayedCID = $cOptObj->findConsoleIDByName("Add Games Played");

if ($cID == "") {
	$cID = $cOptObj->findConsoleIDByName("Manage Games Played");
}

$intHighestOrder = $gameObj->getHighestOrderNum();
$counter = 0;
$x = 1;
$result = $mysqli->query("SELECT * FROM ".$dbprefix."gamesplayed ORDER BY ordernum DESC");
while ($row = $result->fetch_assoc()) {
	if ($counter == 1) {
		$addCSS = " alternateBGColor";
		$counter = 0;
	} else {
		$addCSS = "";
		$counter = 1;
	}

	if ($x == 1) {
		$dispUpArrow = "<img src='".$MAIN_ROOT."images/transparent.png' width='24' height'24'>";
	} else {
		$dispUpArrow = "<a href='javascript:void(0)' onclick=\"moveGame('up', '".$row['gamesplayed_id']."')\"><img src='".$MAIN_ROOT."themes/".$THEME."/images/buttons/uparrow.png' width='24' height='24' title='Move Up'></a>";
	}

	if ($x == $intHighestOrder) {
		$dispDownArrow = "<img src='".$MAIN_ROOT."images/transparent.png' width='24' height'24'>";
	} else {
		$dispDownArrow = "<a href='javascript:void(0)' onclick=\"moveGame('down', '".$row['gamesplayed_id']."')\"><img src='".$MAIN_ROOT."themes/".$THEME."/images/buttons/downarrow.png' width='24' height='24' title='Move Down'></a>";
	}


	$dispGames .= "
	<tr>
		<td class='dottedLine".$addCSS."' width=\"76%\">&nbsp;&nbsp;<span class='main'><b><a href='".$MAIN_ROOT."members/console.php?cID=".$cID."&gID=".$row['gamesplayed_id']."&action=edit'>".$row['name']."</a></b></td>
		<td align='center' class='dottedLine".$addCSS."' width=\"6%\">".$dispUpArrow."</td>
		<td align='center' class='dottedLine".$addCSS."' width=\"6%\">".$dispDownArrow."</td>
		<td align='center' class='dottedLine".$addCSS."' width=\"6%\"><a href='".$MAIN_ROOT."members/console.php?cID=".$cID."&gID=".$row['gamesplayed_id']."&action=edit'><img src='".$MAIN_ROOT."themes/".$THEME."/images/buttons/edit.png' width='24' height='24' title='Edit Game'></a></td>
		<td align='center' class='dottedLine".$addCSS."' width=\"6%\"><a href='javascript:void(0)' onclick=\"deleteGame('".$row['gamesplayed_id']."')\"><img src='".$MAIN_ROOT."themes/".$THEME."/images/buttons/delete.png' width='24' height='24' title='Delete Game'></a></td>
	</tr>
	";

	$x++;
}


if ($x == 1) {
	$dispGames = "<tr><td colspan='5'><br><p align='center' class='main'><i>No games added yet!</i></p></td></tr>";
}

echo "


<table class='formTable' style='border-spacing: 1px; margin-left: auto; margin-right: auto'>
	<tr>
		<td class='main' colspan='2' align='right'>
			&raquo; <a href='".$MAIN_ROOT."members/console.php?cID=".$intAddGamesPlayedCID."'>Add Games Played</a> &laquo;<br><br>
		</td>
	</tr>
	<tr>
		<td class='formTitle' width=\"76%\">Game Name:</td>
		<td class='formTitle' width=\"24%\">Actions:</td>
	</tr>
</table>
<table class='formTable' style='border-spacing: 0px; margin-top: 0px; margin-left: auto; margin-right: auto'>
<tr><td colspan='5' class='dottedLine'></td></tr>
".$dispGames."
</table>


";
