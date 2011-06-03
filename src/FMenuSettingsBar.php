<?php
// ===========================================================================================
//
// Function MenuSettingsBar
//
// Create a menubar for the settings section.
// Can be included in a pagecontroller that has defined the following:
//
// $html
// $pc
// $gPage (frontcontrolelr)

// -------------------------------------------------------------------------------------------
//
// Create the sidebar menu and highlightes the active item
//
$pc->LoadLanguage(__FILE__);

/* $target = strstr($gPage, "-", TRUE); /* From PHP 5.3 */
$target = substr($gPage, 0, strpos($gPage, "-")); 

$settingsMenu = $pc->GetSidebarMenu(unserialize(MENU_SETTINGSBAR), $target);

$html .= <<<EOD
<div class='sidebox'>
<fieldset>
<div id='settingsbar'>
<h4>{$pc->lang['SETTINGS']}</h4>
{$settingsMenu}
</div>
</fieldset>
</div>
EOD;

?>