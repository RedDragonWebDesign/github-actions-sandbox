<?php

require_once($prevFolder."themes/include_header.php");
require_once($prevFolder."themes/destiny/destinymenu.php");
$themeMenusObj = new DestinyMenu($mysqli);

$btThemeObj->setThemeName("Destiny");

$btThemeObj->menusObj = $themeMenusObj;
$btThemeObj->addHeadItem("destinyjs", "<script type='text/javascript' src='".MAIN_ROOT."themes/destiny/destiny.js'></script>");
$btThemeObj->addHeadItem("google-font", "<link href='http://fonts.googleapis.com/css?family=PT+Sans:400,700' rel='stylesheet' type='text/css'>");
?>
<!DOCTYPE html>
<html lang="en-us">
	<head>
		<?php $btThemeObj->displayHead(); ?>
	</head>
<body>

	<div class='topBarBG'>
	
		<div class='topBar'>
		
			<div class='destinyLogo'></div>
			<div id='logoSmall'><img src='<?php echo $MAIN_ROOT; ?>themes/destiny/images/logo-small.png'></div>
			
			<?php $themeMenusObj->displayMenu(2); ?>			
			
		</div>
		
	</div>
	<div id='topBarBGImg'></div>
	
	<div class='wrapper'>	
		
	
		<div class='headerDiv'>
			<div class='logoDiv'>
				<a href='<?php echo MAIN_ROOT; ?>'><img src='<?php echo $websiteInfo['logourl']; ?>'></a>
			</div>
		</div>
	
		<div class='bodyDiv'>
		
			<div class='leftMenuDiv'><?php $themeMenusObj->displayMenu(0); ?></div>
			<div class='rightMenuDiv'><?php $themeMenusObj->displayMenu(1); ?></div>
			<div class='centerContentDiv'>
			<?php require_once(BASE_DIRECTORY."include/clocks.php");
