<?php
/*
 * @package		Joomla.Framework
 * @copyright	Copyright (C) 2005 - 2010 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 *
 * @component Phoca Component
 * @copyright Copyright (C) Jan Pavelka www.phoca.cz
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License version 2 or later;
 */
if(!defined('DS')) define('DS', DIRECTORY_SEPARATOR);
include_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_phocapdf'.DS.'helpers'.DS.'phocapdfbrowser.php');
defined('_JEXEC') or die();
class PhocaPDFHelper
{

	/*
	 * components/com_content/helpers/icon.php
	 */
	public static function getPhocaPDFContentIcon($item, $params, $attribs = array()) {
		
		$lang		= JFactory::getLanguage();
		$lang->load('plg_phocapdf_content', JPATH_ADMINISTRATOR, $lang->getDefault(), false, false);
		
		// Plugin Parameters
		jimport( 'joomla.html.parameter' );
	 	//$pluginP 	= new JParameter( $plugin->params );
		$plugin 	= JPluginHelper::getPlugin('phocapdf', 'content');
		$pluginP 	= new JRegistry();
		$pluginP->loadString($plugin->params);
		
		
		$include_articles 	= $pluginP->get('include_articles', '');
		$include_categories = $pluginP->get('include_categories', '');
		
		$include_articles 	= explode(',', $include_articles );
		$include_categories = explode(',', $include_categories );
		
		$include_articles 	= array_filter($include_articles);
		$include_categories = array_filter($include_categories);
		
		if (!empty($include_articles)) {
			if(isset($item->id) && !in_array($item->id, $include_articles)) {
				return "";
			}
		}
		
		if (!empty($include_categories)) {
			if(isset($item->catid) && !in_array($item->catid, $include_categories)) {
				return "";
			}
		}
		
		$exclude_articles 	= $pluginP->get('exclude_articles', '');
		$exclude_categories = $pluginP->get('exclude_categories', '');
		
		$exclude_articles 	= explode(',', $exclude_articles );
		$exclude_categories = explode(',', $exclude_categories );
		if(isset($item->catid) && in_array($item->catid, $exclude_categories)) {
			return "";
		}
		if(isset($item->id) && in_array($item->id, $exclude_articles)) {
			return "";
		}
		
		$pdfDest	= $pluginP->get('pdf_destination', 'S');
		
		$status = 'status=no,toolbar=no,scrollbars=yes,titlebar=no,menubar=no,'
				 .'resizable=yes,width=640,height=480,directories=no,location=no';
		
		if ($pdfDest == 'I' || $pdfDest == 'D') {
			$attribs['onclick'] = '';
		} else {
			$browser = PhocaPDFHelperBrowser::browserDetection('browser');
			if ($browser == 'msie7' || $browser == 'msie8') {
				$attribs['onclick'] = '';
				$attribs['target'] 	= '_blank';
			} else {
				$attribs['onclick'] = "window.open(this.href,'win2','".$status."'); return false;";
			}
		}
		
		$url  = ContentHelperRoute::getArticleRoute($item->slug, $item->catid);
		$url .= '&tmpl=component&format=pdf';//&page='.@ $request->limitstart;

		// checks template image directory for image, if non found default are loaded
		if ($params->get('show_icons')) {
			$text = JHTML::_('image','components/com_phocapdf/assets/images/pdf_button.png', JText::_('PLG_PHOCAPDF_CONTENT_PDF'));
		} else {
			if ($params->get('show_print_icon')) {
				//$sep = JText::_('JGLOBAL_ICON_SEP');
				$sep = '';
			} else if ($params->get('show_email_icon')) {
				$sep = JText::_('JGLOBAL_ICON_SEP');
			} else {
				$sep = '';
			}
			$text = '&#160;'. JText::_('PLG_PHOCAPDF_CONTENT_PDF') .'&#160;'. $sep;
			
			$text =  JText::_('PLG_PHOCAPDF_CONTENT_PDF') . $sep;
		}

		$attribs['title']	= JText::_('PLG_PHOCAPDF_CONTENT_PDF');
		
		$attribs['rel']		= 'nofollow';

		
		$output = '<li class="print-icon">'
		 . JHTML::_('link',JRoute::_($url), '<span class="icon-file"></span>&#160;' .$text. '&#160;', $attribs)
		.'</li>';

		return $output;
	}


	public static function renderFTPaccess() {
	
		$ftpOutput = '<fieldset title="'.JText::_('COM_PHOCAPDF_FTP_LOGIN_LABEL'). '">'
		.'<legend>'. JText::_('COM_PHOCAPDF_FTP_LOGIN_LABEL').'</legend>'
		.JText::_('COM_PHOCAPDF_FTP_LOGIN_DESC')
		.'<table class="adminform nospace">'
		.'<tr>'
		.'<td width="120"><label for="username">'. JText::_('JGLOBAL_USERNAME').':</label></td>'
		.'<td><input type="text" id="username" name="username" class="input_box" size="70" value="" /></td>'
		.'</tr>'
		.'<tr>'
		.'<td width="120"><label for="password">'. JText::_('JGLOBAL_PASSWORD').':</label></td>'
		.'<td><input type="password" id="password" name="password" class="input_box" size="70" value="" /></td>'
		.'</tr></table></fieldset>';
		return $ftpOutput;
	}
	
	
	
	public static function getPhocaInfo($pdf = 1) {
		$params = JComponentHelper::getParams('com_phocapdf') ;
		
		$pdf 	= $params->get( 'pdf_id', 1);
		if ($pdf == 1) {
			return '<'.'a'.' '.'s'.'t'.'y'.'l'.'e'.'='.'"'.'c'.'o'.'l'.'o'.'r'.':'.' '.'r'.'g'.'b'.'('.'1'.'7'.'5'.','.'1'.'7'.'5'.','.'1'.'7'.'5'.')'.'"'.' '.'h'.'r'.'e'.'f'.'='.'"'.'h'.'t'.'t'.'p'.':'.'/'.'/'.'w'.'w'.'w'.'.'.'p'.'h'.'o'.'c'.'a'.'.'.'c'.'z'.'/'.'p'.'h'.'o'.'c'.'a'.'p'.'d'.'f'.'"'.'>'.'P'.'h'.'o'.'c'.'a'.' '.'P'.'D'.'F'.'<'.'/'.'a'.'>';
		} else {
			return '';
		}
	}



	public static function getPhocaVersion($component) {
		$folder = JPATH_ADMINISTRATOR .DS. 'components'.DS.$component;
		if (JFolder::exists($folder)) {
			$xmlFilesInDir = JFolder::files($folder, '.xml$');
		} else {
			$folder = JPATH_SITE .DS. 'components'.DS.$component;
			if (JFolder::exists($folder)) {
				$xmlFilesInDir = JFolder::files($folder, '.xml$');
			} else {
				$xmlFilesInDir = null;
			}
		}

		$xml_items = '';
		if (count($xmlFilesInDir))
		{
			foreach ($xmlFilesInDir as $xmlfile)
			{
				if ($data = JApplicationHelper::parseXMLInstallFile($folder.DS.$xmlfile)) {
					foreach($data as $key => $value) {
						$xml_items[$key] = $value;
					}
				}
			}
		}
		
		if (isset($xml_items['version']) && $xml_items['version'] != '' ) {
			return $xml_items['version'];
		} else {
			return '';
		}
	}
	
}

class PhocaPDFCell
{
	public static function setCell($pdf = 1) {
		$params = JComponentHelper::getParams('com_phocapdf') ;
		$pdf 	= $params->get( 'pdf_id', 1);
		if ($pdf == 1) {
			return '<'.'a'.' '.'s'.'t'.'y'.'l'.'e'.'='.'"'.'c'.'o'.'l'.'o'.'r'.':'.' '.'r'.'g'.'b'.'('.'1'.'7'.'5'.','.'1'.'7'.'5'.','.'1'.'7'.'5'.')'.'"'.' '.'h'.'r'.'e'.'f'.'='.'"'.'h'.'t'.'t'.'p'.':'.'/'.'/'.'w'.'w'.'w'.'.'.'p'.'h'.'o'.'c'.'a'.'.'.'c'.'z'.'/'.'p'.'h'.'o'.'c'.'a'.'p'.'d'.'f'.'"'.'>'.'P'.'h'.'o'.'c'.'a'.' '.'P'.'D'.'F'.'<'.'/'.'a'.'>';
		} else {
			return '';
		}
	}

}

class PhocaPDFControlPanel
{
	
	public static function quickIconButton( $component, $link, $image, $text ) {
		
		$lang	= &JFactory::getLanguage();
		$button = '';
		if ($lang->isRTL()) {
			$button .= '<div class="icon-wrapper">';
		} else {
			$button .= '<div class="icon-wrapper">';
		}
		$button .=	'<div class="icon">'
				   .'<a href="'.$link.'">'
				   .JHTML::_('image', 'administrator/components/'.$component.'/assets/images/'.$image, $text )
				   .'<span>'.$text.'</span></a>'
				   .'</div>';
		$button .= '</div>';

		return $button;
	}
}
?>