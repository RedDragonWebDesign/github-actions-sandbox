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

require_once($prevFolder."classes/btupload.php");
require_once($prevFolder."classes/consolecategory.php");
require_once($prevFolder."classes/rankcategory.php");

$cID = $_GET['cID'];
$rankCatObj = new RankCategory($mysqli);
$consoleCatObj = new ConsoleCategory($mysqli);



if ($_GET['cnID'] == "") {
	echo "
	<div id='loadingSpiral' class='loadingSpiral'>
		<p align='center'>
			<img src='".$MAIN_ROOT."themes/".$THEME."/images/loading-spiral.gif'><br>Loading
		</p>
	</div>
	<div id='contentDiv'>
	";
	require_once("consoleoptions/main.php");

	echo "	
	</div>
	<div id='deleteMessage' style='display: none'></div>
	<script type='text/javascript'>
	function moveConsole(strDir, intConsoleID) {
		$(document).ready(function() {
			$('#loadingSpiral').show();
			$('#contentDiv').hide();
			$.post('".$MAIN_ROOT."members/include/admin/consoleoptions/move.php', { cDir: strDir, cID: intConsoleID }, function(data) {
					$('#contentDiv').html(data);
					$('#loadingSpiral').hide();
					$('#contentDiv').fadeIn(400);
				});
	
		});
	}
	
	
	function addSeparator(intCatID) {
		$(document).ready(function() {
				$('#loadingSpiral').show();
				$('#contentDiv').hide();
				$.post('".$MAIN_ROOT."members/include/admin/consoleoptions/addseparator.php', { cID: intCatID }, function(data) {
						$('#contentDiv').html(data);
						$('#loadingSpiral').hide();
						$('#contentDiv').fadeIn(400);
					});
		
			});
	
	}
	

	
	function deleteConsole(intConsoleID) {
		$(document).ready(function() {				
		
			$.post('".$MAIN_ROOT."members/include/admin/consoleoptions/delete.php', { cID: intConsoleID }, function(data) {
				$('#deleteMessage').html(data);
				
				$('#deleteMessage').dialog({
			
					title: 'Manage Console Options - Delete',
					width: 400,
					modal: true,
					zIndex: 9999,
					resizable: false,
					show: 'scale',
					buttons: {
						'Yes': function() {
							
							$('#loadingSpiral').show();
							$('#contentDiv').hide();
							$(this).dialog('close');
							$.post('".$MAIN_ROOT."members/include/admin/consoleoptions/delete.php', { cID: intConsoleID, confirm: 1 }, function(data1) {
								$('#contentDiv').html(data1);
								$('#loadingSpiral').hide();
								$('#contentDiv').fadeIn(400);	
							});
						
						},
						'Cancel': function() {
						
							$(this).dialog('close');
						
						}
					}
				});
			
			});
			
			
			
			
			
			
		});			
	}
	
	</script>
	";
} elseif ($_GET['cnID'] != "" and $_GET['action'] == "edit") {
	require_once("consoleoptions/edit.php");
}
