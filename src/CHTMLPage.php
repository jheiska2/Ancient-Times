<?php
// Class CHTMLPage
//
// Create and print a HTML page.
class CHTMLPage {

    // Internal variables
    protected $iPc;

    // Constructor
    public function __construct() { 
    
        $this->iPc = new CPageController();
        $this->iPc->LoadLanguage(__FILE__);
    }

    // Destructor
    public function __destruct() { ; }

    // Print out a resulting page according to arguments
    public function PrintPage($aTitle="", $aHTMLLeft="", $aHTMLMain="", $aHTMLRight="", $aHTMLHead="", $aJavaScript="", $enablejQuery=FALSE) {

        $titlePage    = $aTitle;
        $titleSite    = WS_TITLE;
        $language        = WS_LANGUAGE;
        $charset        = WS_CHARSET;
        $stylesheet    = WS_STYLESHEET;
        $favicon         = WS_FAVICON;
        $footer            = WS_FOOTER;
        
        $apps        = $this->PrepareApplicationMenu();
        $login    = $this->prepareLoginLogoutMenu();
        $nav         = $this->prepareNavigationBar();
        $body        = $this->preparePageBody($aHTMLLeft, $aHTMLMain, $aHTMLRight);
        $w3c        = $this->prepareValidatorTools();
        $timer    = $this->prepareTimer();

        $jQuery         = ($enablejQuery) ? "<script type='text/javascript' src='" . JS_JQUERY . "'></script> <!-- jQuery --> " : '';
        $javascript = (empty($aJavaScript)) ? '' : "<script type='text/javascript'>{$aJavaScript}</script>";
        $slide =        include "images/slideshow/slideshow.php";
        $slide2 =       Insert_Slideshow ( "images/slideshow/slideshow.swf", "images/slideshow/sample.php", 870, 180 );

        $html = <<<EOD
<!DOCTYPE html>
<html lang="{$language}">
    <head>
        <meta charset="{$charset}" />
        <title>{$titlePage}</title>
        <link rel="shortcut icon" href="{$favicon}" />
        <link rel="stylesheet" href="{$stylesheet}" />
        {$jQuery}
        {$aHTMLHead}
        {$javascript}
        <!-- HTML5 support for IE -->
        <script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>        
    </head>
    <body>  
        <div id='wrap'>
            <div id='top'>{$login}{$apps}</div>
            <div id='head'>
                <div id='title'><p>{$titleSite}{$slide}{$slide2}</p></div>
                <div id='nav'>{$nav}</div>
            </div>
            {$body}
            <div id='footer'><p>{$footer}</p></div>
            <div id='bottom'><p>{$timer}{$w3c}</p></div>
        </div> 
    </body>
</html>

EOD;
        // Print the header and page
        header("Content-Type: text/html; charset={$charset}");
        
 
        echo $html;
    }

    // Prepare the apps-menu
    public function PrepareApplicationMenu() {
    
        $menu = unserialize(MENU_APPLICATION);

        $apps = "<div id='apps'><p>";
        foreach($menu as $key => $value) {
            $apps .= "<a class='noUnderline' href='{$value}'>{$key}</a> ";
        }
        $apps .= "</p></div>";
    
        return $apps;    
    }


    // Prepare the login-menu, changes look if user is logged in or not
    public function PrepareLoginLogoutMenu() {
        
        global $gModule;
    
        $m = "m={$gModule}&amp;";
        $pc = $this->iPc;
        $html = "";

        // If user is logged in, show details about user and some links.
        // If user is not logged in, show link to login-page
        if(isset($_SESSION['accountUser'])) {
        
            $admHtml = "";
            if(isset($_SESSION['groupMemberUser']) && $_SESSION['groupMemberUser'] == 'adm') {
                $admHtml = "<a href='?{$m}p=admin'>{$pc->lang['ADMIN']}</a> ";
            }
        
            $html = <<<EOD
<div id='loginbar'>
    <p>
    {$_SESSION['accountUser']}   
    <!--
    <a href='?{$m}p=account-details'>{$pc->lang['SETTINGS']}</a>  
    {$admHtml} 
    -->
    <a href='?{$m}p=logoutp'>{$pc->lang['LOGOUT']}</a>
    </p>
</div>

EOD;
        }
        else {
        
            $html = <<<EOD
<div id='loginbar'>
    <p>
    <a href='?{$m}p=login'>{$pc->lang['LOGIN']}</a>
    </p>
</div>

EOD;
        }
        
        return $html;    
    }


    // Prepare the header-div of the page
    public function PrepareNavigationBar() {
    
        global $gPage;
        $menu = unserialize(MENU_NAVBAR);
        
        $nav = "<ul>";
        foreach($menu as $key => $value) {
            $selected = (strcmp($gPage, substr($value, 3)) == 0) ? " class='sel'" : "";
            $nav .= "<li{$selected}><a href='{$value}'>{$key}</a></li>";
        }
        $nav .= '</ul>';
    
        return $nav;    
    }


    // Prepare everything within the body-div
    public function PreparePageBody($aBodyLeft, $aBodyMain, $aBodyRight) {

        // General error message from session
        $htmlErrorMessage = $this->GetErrorMessage();
        
        // Stylesheet must support this
        // 1, 2 or 3-column layout? 
        // LMR, show left, main and right column
        // LM,  show left and main column
        // MR,  show main and right column
        // M,   show main column
        //
        $cols  = empty($aBodyLeft)  ? '' : 'L';
        $cols .= empty($aBodyMain)  ? '' : 'M';
        $cols .= empty($aBodyRight) ? '' : 'R';

        // Get content for each column, if defined, else empty
        $bodyLeft  = empty($aBodyLeft)  ? "" : "<div id='left_{$cols}'>{$aBodyLeft}</div>";
        $bodyRight = empty($aBodyRight) ? "" : "<div id='right_{$cols}'>{$aBodyRight}</div>";
        $bodyMain  = empty($aBodyMain)  ? "" : "<div id='main_{$cols}'>{$aBodyMain}<p class='last'>&nbsp;</p></div>";

        $html = <<<EOD
<div id='body'>                                             
    {$htmlErrorMessage}
    <div id='container_{$cols}'>
        <div id='content_{$cols}'>
            {$bodyLeft}
            {$bodyMain}
        </div>                                                 <!-- End Of #content -->
    </div>                                                     <!-- End Of #container -->
    {$bodyRight}
    <div class='clear'>&nbsp;</div>
</div>                                                         <!-- End Of #body -->

EOD;

        return $html;
    }

    // Prepare html for validator tools
    public function PrepareValidatorTools() {

        if(!WS_VALIDATORS) { return ""; }

         $refToThisPage                     = CPageController::CurrentURL();
         $linkToCSSValidator             = "<a href='http://jigsaw.w3.org/css-validator/check/referer'>CSS</a>";
        $linkToMarkupValidator    = "<a href='http://validator.w3.org/check/referer'>XHTML</a>";
        $linkToCheckLinks                 = "<a href='http://validator.w3.org/checklink?uri={$refToThisPage}'>Links</a>";
         $linkToHTML5Validator        = "<a href='http://html5.validator.nu/?doc={$refToThisPage}'>HTML5</a>";
 
        return "<br />{$linkToCSSValidator} {$linkToMarkupValidator} {$linkToCheckLinks} {$linkToHTML5Validator}";
    }


    // Prepare html for the timer
    public function PrepareTimer() {
    
        if(WS_TIMER) {
            global $gTimerStart;
            return 'Page generated in ' . round(microtime(TRUE) - $gTimerStart, 5) . ' seconds.';
        }
    }


    // Create a errormessage if its set in the SESSION
    public function GetErrorMessage() {

        $html = "";

        if(isset($_SESSION['errorMessage'])) {
        
            $html = <<<EOD
<div class='errorMessage'>
{$_SESSION['errorMessage']}
</div>
EOD;

            unset($_SESSION['errorMessage']);
        }

        return $html;   
    }
} // End of Of Class
?>