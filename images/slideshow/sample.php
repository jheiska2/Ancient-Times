<?php
//include slideshow.php to access the Send_Slideshow_Data function
include "slideshow.php";
//show the control bar
//$slideshow [ 'control' ][ 'bar_visible' ] = "on";
 //add 3 slides
$slideshow[ 'slide' ][ 0 ] = array ( 'url' => "images/slideshow/forum_romanum.jpg",  'duration' => 3 );
$slideshow[ 'slide' ][ 1 ] = array ( 'url' => "images/slideshow/forumRom.jpg", 'duration' => 3 );
$slideshow[ 'slide' ][ 2 ] = array ( 'url' => "images/slideshow/rome.jpg", 'duration' => 3 );
$slideshow[ 'slide' ][ 3 ] = array ( 'url' => "images/slideshow/rome2.jpg",  'duration' => 3); 
$slideshow[ 'slide' ][ 4 ] = array ( 'url' => "images/slideshow/rome3.jpg",  'duration' => 3); 
$slideshow[ 'slide' ][ 5 ] = array ( 'url' => "images/slideshow/forumRomanum.jpg",  'duration' => 3); 
//send the slideshow data
Send_Slideshow_Data ( $slideshow );
?>