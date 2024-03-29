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

defined('_JEXEC') or die('Restricted access'); 
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;


echo '<div id="phocapdf-pane">';
//$pane =& J Pane::getInstance('Tabs', array('startOffset'=> 0));
echo HTMLHelper::_('tabs.start', 'config-tabs-com_phocapdf-plugin', array('useCookie'=>1));
//echo $pane->startPane( 'pane' );

// - - - - - - - - - - - - - - - 
// Site
echo HTMLHelper::_('tabs.panel', HTMLHelper::_( 'image', 'administrator/components/com_phocapdf/assets/images/icon-16-site.png','') . '&nbsp;'.Text::_('COM_PHOCAPDF_SITE'), 'site');
echo '<div style="font-size:1px;height:1px;margin:0px;padding:0px;">&nbsp;</div>';//because of IE bug
if($output = PhocaPDFHelperParams::renderSite($this->params, 'params', 'phocasite')) {
	echo $output;
} else {
	echo '<div style="text-align: center; padding: 5px;">'.Text::_('There are no parameters for this item').'</div>';
}
//echo $pane->endPanel();
// - - - - - - - - - - - - - - -

// - - - - - - - - - - - - - - - 
// Header
echo HTMLHelper::_('tabs.panel', HTMLHelper::_( 'image', 'administrator/components/com_phocapdf/assets/images/icon-16-header.png','') . '&nbsp;'.Text::_('COM_PHOCAPDF_HEADER'), 'header');
echo '<div style="font-size:1px;height:1px;margin:0px;padding:0px;">&nbsp;</div>';//because of IE bug
if($output = PhocaPDFHelperParams::renderMisc($this->params, 'params', 'phocaheader')) {
	echo $output;
} else {
	echo '<div style="text-align: center; padding: 5px; ">'.Text::_('There are no parameters for this item').'</div>';
}
//echo $pane->endPanel();
// - - - - - - - - - - - - - - -

// - - - - - - - - - - - - - - - 
// Footer
echo HTMLHelper::_('tabs.panel', HTMLHelper::_( 'image', 'administrator/components/com_phocapdf/assets/images/icon-16-footer.png','') . '&nbsp;'.Text::_('COM_PHOCAPDF_FOOTER'), 'footer');
echo '<div style="font-size:1px;height:1px;margin:0px;padding:0px;">&nbsp;</div>';//because of IE bug
if($output = PhocaPDFHelperParams::renderMisc($this->params, 'params', 'phocafooter')) {
	echo $output;
} else {
	echo '<div style="text-align: center; padding: 5px; ">'.Text::_('There are no parameters for this item').'</div>';
}
//echo $pane->endPanel();
// - - - - - - - - - - - - - - -

// - - - - - - - - - - - - - - - 
// PDF
echo HTMLHelper::_('tabs.panel', HTMLHelper::_( 'image', 'administrator/components/com_phocapdf/assets/images/icon-16-pdf.png','') . '&nbsp;'.Text::_('COM_PHOCAPDF_PDF'), 'pdf');
echo '<div style="font-size:1px;height:1px;margin:0px;padding:0px;">&nbsp;</div>';//because of IE bug
if($output = PhocaPDFHelperParams::renderMisc($this->params, 'params', 'phocapdf')) {
	echo $output;
} else {
	echo '<div style="text-align: center; padding: 5px;">'.Text::_('There are no parameters for this item').'</div>';
}
//echo $pane->endPanel();
// - - - - - - - - - - - - - - -

//echo $pane->endPane();
echo HTMLHelper::_('tabs.end');
echo '</div>';

echo '<div id="phocapdf-apply"><a href="#" onclick="javascript: submitbutton(\'apply\')">'.Text::_('Save').'</a></div>';
	
echo '<div style="margin-top:20px">';	
if (isset($this->tmpl['plugin']->published)) {
	if ($this->tmpl['plugin']->published == 1) {
		echo HTMLHelper::_('image', 'administrator/components/com_phocapdf/assets/images/icon-16-true.png', '' )
		.' ' . Text::_('Plugin is enabled in Plugin Manager');
	} else {
		echo HTMLHelper::_('image', 'administrator/components/com_phocapdf/assets/images/icon-16-false.png','' )
		.' '. Text::_('Plugin is disabled in Plugin Manager');
	}
	
}

echo '<br />' .HTMLHelper::_('image', 'administrator/components/com_phocapdf/assets/images/icon-16-warning.png','' )
.' '. Text::_('Phoca PDF Settings Warning');

echo '</div>';
echo '<div style="clear:both"></div>';