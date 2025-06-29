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

include_once(JPATH_ADMINISTRATOR.'/components/com_phocapdf/helpers/phocapdfbrowser.php');
defined('_JEXEC') or die();
use Joomla\CMS\Factory;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\Filesystem\Path;
use Joomla\Registry\Registry;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\Filesystem\Folder;
use Joomla\CMS\Installer\Installer;
class PhocaPDFHelper
{

	/*
	 * components/com_content/helpers/icon.php
	 */
	public static function getPhocaPDFContentIcon($item, $params, $attribs = array()) {

		$lang		= Factory::getLanguage();
		$lang->load('plg_phocapdf_content', JPATH_ADMINISTRATOR, $lang->getDefault(), false, false);

		// Plugin Parameters
		jimport( 'joomla.html.parameter' );

		$plugin 	= PluginHelper::getPlugin('phocapdf', 'content');
		$pluginP 	= new Registry();
		$pluginP->loadString($plugin->params);

		$include_articles 	= $pluginP->get('include_articles', '');
		$include_categories = $pluginP->get('include_categories', '');

		$include_articles 	= explode(',', $include_articles );
		$include_categories = explode(',', $include_categories );

		$include_articles 	= array_filter($include_articles);
		$include_categories = array_filter($include_categories);

		/*if (!empty($include_articles)) {
			if(isset($item->id) && !in_array($item->id, $include_articles)) {
				return "";
			}
		}

		if (!empty($include_categories)) {
			if(isset($item->catid) && !in_array($item->catid, $include_categories)) {
				return "";
			}
		}*/


		$include_article = true;
		$include_category = true;

		if (!empty($include_articles)) {
			if(isset($item->id) && !in_array($item->id, $include_articles)) {
				$include_article = false;
			}
		}

		if (!empty($include_categories)) {
			if(isset($item->catid) && !in_array($item->catid, $include_categories)) {
				$include_category = false;
			}
		}

		// If neither article id nor category id is included return ""
		if(!($include_article || $include_category)){
			return "";
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

		if(!isset($attribs['onclick'])) {
			$attribs['onclick'] = '';
		}
		if(!isset($attribs['target'])) {
			$attribs['target'] = '';
		}

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

		if (!class_exists('Joomla\Component\Content\Site\Helper\RouteHelper')) {
			require_once (JPATH_SITE . '/components/com_content/src/Helper/Routehelper.php');
		}

		$url  = Joomla\Component\Content\Site\Helper\RouteHelper::getArticleRoute($item->slug, $item->catid);
		$url .= '&tmpl=component&format=pdf';//&page='.@ $request->limitstart;

		// checks template image directory for image, if non found default are loaded
		/*if ($params->get('show_icons')) {
			$text = HTMLHelper::_('image','media/com_phocapdf/images/pdf_button.png', Text::_('PLG_PHOCAPDF_CONTENT_PDF'));
		} else {
			if ($params->get('show_print_icon')) {
				//$sep = Text::_('JGLOBAL_ICON_SEP');
				$sep = '';
			} else if ($params->get('show_email_icon')) {
				$sep = Text::_('JGLOBAL_ICON_SEP');
			} else {
				$sep = '';
			}
			//$text = '&#160;'. Text::_('PLG_PHOCAPDF_CONTENT_PDF') .'&#160;'. $sep;

			$text =  Text::_('PLG_PHOCAPDF_CONTENT_PDF') . $sep;
		}*/
		$text =  Text::_('PLG_PHOCAPDF_CONTENT_PDF');
		$attribs['title']	= Text::_('PLG_PHOCAPDF_CONTENT_PDF');

		$attribs['rel']		= 'nofollow';



		//$output = '<li class="print-icon">'
		// . HtmlHelper::_('link',JRoute::_($url), '<span class="icon-file"></span>&#160;' .$text. '&#160;', $attribs)
		//.'</li>';
		$output = '<div class="pdf-print-icon">'
		 . '<a class="btn btn-danger" href="'.Route::_($url).'" onclick="'.$attribs['onclick'].'" target="'.$attribs['target'].'"><span class="glyphicon glyphicon-file icon-file"></span> ' .$text. '</a>'
		.'</div>';

		return $output;
	}


	public static function renderFTPaccess() {

		$ftpOutput = '<fieldset title="'.Text::_('COM_PHOCAPDF_FTP_LOGIN_LABEL'). '">'
		.'<legend>'. Text::_('COM_PHOCAPDF_FTP_LOGIN_LABEL').'</legend>'
		.Text::_('COM_PHOCAPDF_FTP_LOGIN_DESC')
		.'<table class="adminform nospace">'
		.'<tr>'
		.'<td width="120"><label for="username">'. Text::_('JGLOBAL_USERNAME').':</label></td>'
		.'<td><input type="text" id="username" name="username" class="input_box" size="70" value="" /></td>'
		.'</tr>'
		.'<tr>'
		.'<td width="120"><label for="password">'. Text::_('JGLOBAL_PASSWORD').':</label></td>'
		.'<td><input type="password" id="password" name="password" class="input_box" size="70" value="" /></td>'
		.'</tr></table></fieldset>';
		return $ftpOutput;
	}



	public static function getPhocaInfo($pdf = 1) {

		PluginHelper::importPlugin('phocatools');
        $results = Factory::getApplication()->triggerEvent('onPhocatoolsOnDisplayInfo', array('NjI5NTM4NDcxNzcxMTc='));
        if (isset($results[0]) && $results[0] === true) {
            return '';
        }

		$params = ComponentHelper::getParams('com_phocapdf') ;

		$pdf 	= $params->get( 'pdf_id', 1);
		if ($pdf == 1) {
			return '<'.'a'.' '.'s'.'t'.'y'.'l'.'e'.'='.'"'.'c'.'o'.'l'.'o'.'r'.':'.' '.'r'.'g'.'b'.'('.'1'.'7'.'5'.','.'1'.'7'.'5'.','.'1'.'7'.'5'.')'.'"'.' '.'h'.'r'.'e'.'f'.'='.'"'.'h'.'t'.'t'.'p'.':'.'/'.'/'.'w'.'w'.'w'.'.'.'p'.'h'.'o'.'c'.'a'.'.'.'c'.'z'.'/'.'p'.'h'.'o'.'c'.'a'.'p'.'d'.'f'.'"'.'>'.'P'.'h'.'o'.'c'.'a'.' '.'P'.'D'.'F'.'<'.'/'.'a'.'>';
		} else {
			return '';
		}
	}



	public static function getPhocaVersion($component) {
		$folder = JPATH_ADMINISTRATOR.'/components'.'/'.$component;
		if (PhocaPDFHelper::folderExists($folder)) {
			$xmlFilesInDir = Folder::files($folder, '.xml$');
		} else {
			$folder = JPATH_SITE .'/'. 'components'.'/'.$component;
			if (PhocaPDFHelper::folderExists($folder)) {
				$xmlFilesInDir = Folder::files($folder, '.xml$');
			} else {
				$xmlFilesInDir = null;
			}
		}

		$xml_items = array();
		if (count($xmlFilesInDir))
		{
			foreach ($xmlFilesInDir as $xmlfile)
			{
				if ($data = Installer::parseXMLInstallFile($folder.'/'.$xmlfile)) {
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

	public static function fileExists($file) {
    return is_file(Path::clean($file));
}

	public static function folderExists($path) {
		return is_dir(Path::clean($path));
	}

}

class PhocaPDFCell
{
	public static function setCell($pdf = 1) {

		PluginHelper::importPlugin('phocatools');
        $results = Factory::getApplication()->triggerEvent('onPhocatoolsOnDisplayInfo', array('NjI5NTM4NDcxNzcxMTc='));
        if (isset($results[0]) && $results[0] === true) {
            return '';
        }


		$params = ComponentHelper::getParams('com_phocapdf') ;
		$pdf 	= $params->get( 'pdf_id', 1);
		if ($pdf == 1) {
			return '<'.'a'.' '.'s'.'t'.'y'.'l'.'e'.'='.'"'.'c'.'o'.'l'.'o'.'r'.':'.' '.'r'.'g'.'b'.'('.'1'.'7'.'5'.','.'1'.'7'.'5'.','.'1'.'7'.'5'.')'.'"'.' '.'h'.'r'.'e'.'f'.'='.'"'.'h'.'t'.'t'.'p'.':'.'/'.'/'.'w'.'w'.'w'.'.'.'p'.'h'.'o'.'c'.'a'.'.'.'c'.'z'.'/'.'p'.'h'.'o'.'c'.'a'.'p'.'d'.'f'.'"'.'>'.'P'.'h'.'o'.'c'.'a'.' '.'P'.'D'.'F'.'<'.'/'.'a'.'>';
		} else {
			return '';
		}
	}

}
/*
class PhocaPDFControlPanel
{

	public static function quickIconButton( $component, $link, $image, $text ) {

		$lang	= Factory::getLanguage();
		$button = '';
		if ($lang->isRTL()) {
			$button .= '<div class="icon-wrapper">';
		} else {
			$button .= '<div class="icon-wrapper">';
		}
		$button .=	'<div class="icon">'
				   .'<a href="'.$link.'">'
				   .HTMLHelper::_('image', 'administrator/components/'.$component.'/assets/images/'.$image, $text )
				   .'<span>'.$text.'</span></a>'
				   .'</div>';
		$button .= '</div>';

		return $button;
	}
}*/
?>
