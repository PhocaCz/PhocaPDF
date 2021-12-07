<?php
/*
 * @package Joomla 1.5
 * @copyright Copyright (C) 2005 Open Source Matters. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 *
 * @component Phoca Component
 * @copyright Copyright (C) Jan Pavelka www.phoca.cz
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */
 
 /*
Script Name: Simple 'if' PHP Browser detection
Author: Harald Hope, Website: http://TechPatterns.com/
Script Source URI: http://TechPatterns.com/downloads/php_browser_detection.php
Version 2.0.2
Copyright (C) 29 June 2007
 
Modified 22 April 2008 by Jon Czerwinski
Added IE 7 version detection
 
This program is free software; you can redistribute it and/or modify it under 
the terms of the GNU General Public License as published by the Free Software
Foundation; either version 3 of the License, or (at your option) any later version.
 
This program is distributed in the hope that it will be useful, but WITHOUT 
ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS
FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 
Get the full text of the GPL here: http://www.gnu.org/licenses/gpl.txt
 
Coding conventions:
http://cvs.sourceforge.net/viewcvs.py/phpbb/phpBB2/docs/codingstandards.htm?rev=1.3
*/
 
/*
the order is important, because opera must be tested first, and ie4 tested for before ie general
same for konqueror, then safari, then gecko, since safari navigator user agent id's with 'gecko' in string.
note that $dom_browser is set for all  modern dom browsers, this gives you a default to use, unfortunately we
haven't figured out a way to do this with actual method testing, which would be much better and reliable.
 
Please note: you have to call the function in order to get access to the variables, you call it by this:
 
browser_detection('browser');
 
then put you code that you want to use the variables with after that.
 
*/
defined('_JEXEC') or die();
class PhocaPDFHelperBrowser
{
	public static function browserDetection( $which_test ) {
	 
			// initialize the variables
			$browser = '';
			$dom_browser = '';
	 
			// set to lower case to avoid errors, check to see if http_user_agent is set
			$navigator_user_agent = ( isset( $_SERVER['HTTP_USER_AGENT'] ) ) ? strtolower( $_SERVER['HTTP_USER_AGENT'] ) : '';
	 
			// run through the main browser possibilities, assign them to the main $browser variable
			if (stristr($navigator_user_agent, "opera")) 
			{
					$browser = 'opera';
					$dom_browser = true;
			}
	 
	/*
	Test for IE 7 added
	April 22, 2008
	Jon Czerwinski
	*/
			elseif (stristr($navigator_user_agent, "msie 7")) 
			{
					$browser = 'msie7'; 
					$dom_browser = false;
			}
			
			elseif (stristr($navigator_user_agent, "msie 8")) 
			{
					$browser = 'msie8'; 
					$dom_browser = false;
			}
	 
			elseif (stristr($navigator_user_agent, "msie 4")) 
			{
					$browser = 'msie4'; 
					$dom_browser = false;
			}
	 
			elseif (stristr($navigator_user_agent, "msie")) 
			{
					$browser = 'msie'; 
					$dom_browser = true;
			}
	 
			elseif ((stristr($navigator_user_agent, "konqueror")) || (stristr($navigator_user_agent, "safari"))) 
			{
					$browser = 'safari'; 
					$dom_browser = true;
			}
	 
			elseif (stristr($navigator_user_agent, "gecko")) 
			{
					$browser = 'mozilla';
					$dom_browser = true;
			}
	 
			elseif (stristr($navigator_user_agent, "mozilla/4")) 
			{
					$browser = 'ns4';
					$dom_browser = false;
			}
	 
			else 
			{
					$dom_browser = false;
					$browser = false;
			}
	 
			// return the test result you want
			if ( $which_test == 'browser' )
			{
					return $browser;
			}
			elseif ( $which_test == 'dom' )
			{
					return $dom_browser;
					//  note: $dom_browser is a boolean value, true/false, so you can just test if
					// it's true or not.
			}
	}
	 
	/*
	you would call it like this:
	 
	$user_browser = browser_detection('browser');
	 
	if ( $user_browser == 'opera' )
	{
			do something;
	}
	 
	or like this:
	 
	if ( browser_detection('dom') )
	{
			execute the code for dom browsers
	}
	else
	{
			execute the code for non DOM browsers
	}
	 
	and so on.......
	 
	 
	*/
}
?>
