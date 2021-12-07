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
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;

$r = $this->r;
$tabs = array (
'site' 		=> Text::_('COM_PHOCAPDF_SITE'),
'header' 	=> Text::_('COM_PHOCAPDF_HEADER'),
'footer'		=> Text::_('COM_PHOCAPDF_FOOTER'),
'pdf'		=> Text::_('COM_PHOCAPDF_PDF')
);
echo $r->navigation($tabs);


/*
<ul id="myTab" class="nav nav-tabs">
  <li class="active"><a href="#site" data-toggle="tab" ><?php echo HTMLHelper::_( 'image', 'media/com_phocapdf/images/administrator/icon-16-site.png','') . '&nbsp;'.Text::_('COM_PHOCAPDF_SITE'); ?></a></li>
  <li class=""><a href="#header" data-toggle="tab"><?php echo HTMLHelper::_( 'image', 'media/com_phocapdf/images/administrator/icon-16-header.png','') . '&nbsp;'.Text::_('COM_PHOCAPDF_HEADER'); ?></a></li>
  <li class=""><a href="#footer" data-toggle="tab"><?php echo HTMLHelper::_( 'image', 'media/com_phocapdf/images/administrator/icon-16-footer.png','') . '&nbsp;'.Text::_('COM_PHOCAPDF_FOOTER'); ?></a></li>
  <li class=""><a href="#pdf" data-toggle="tab"><?php echo HTMLHelper::_( 'image', 'media/com_phocapdf/images/administrator/icon-16-pdf.png','') . '&nbsp;'.Text::_('COM_PHOCAPDF_PDF'); ?></a></li>

</ul>
<div id="myTabContent" class="tab-content"><?php
*/
echo $r->startTabs();

echo $r->startTab('site', $tabs['site'], 'active');
if($output = PhocaPDFHelperParams::renderSite( $this->form->getFieldset('phocasite'))) {
	echo $output;
} else {
	echo '<div style="text-align: center; padding: 5px;">'.Text::_('COM_PHOCAPDF_THERE_ARE_NO_PARAMETERS_FOR_THIS_ITEM').'</div>';
}
echo $r->endTab();

echo $r->startTab('header', $tabs['header']);
if($output = PhocaPDFHelperParams::renderMisc($this->form->getFieldset('phocaheader'))) {
	echo $output;
} else {
	echo '<div style="text-align: center; padding: 5px; ">'.Text::_('COM_PHOCAPDF_THERE_ARE_NO_PARAMETERS_FOR_THIS_ITEM').'</div>';
}
echo $r->endTab();

echo $r->startTab('footer', $tabs['footer']);
if($output = PhocaPDFHelperParams::renderMisc($this->form->getFieldset('phocafooter'))) {
	echo $output;
} else {
	echo '<div style="text-align: center; padding: 5px; ">'.Text::_('COM_PHOCAPDF_THERE_ARE_NO_PARAMETERS_FOR_THIS_ITEM').'</div>';
}
echo $r->endTab();

echo $r->startTab('pdf', $tabs['pdf']);
if($output = PhocaPDFHelperParams::renderMisc($this->form->getFieldset('phocapdf'))) {
	echo $output;
} else {
	echo '<div style="text-align: center; padding: 5px; ">'.Text::_('COM_PHOCAPDF_THERE_ARE_NO_PARAMETERS_FOR_THIS_ITEM').'</div>';
}
echo $r->endTab();


echo $r->endTabs();







echo '<div class="alert alert-warning ph-plugin-warning">';
if (isset($this->item->enabled)) {
	if ($this->item->enabled == 1) {
		echo HTMLHelper::_('image', 'media/com_phocapdf/images/administrator/icon-16-true.png', '' )
		.' ' . Text::_('COM_PHOCAPDF_PLUGIN_IS_ENABLED_IN_MANAGER');
	} else {
		echo HTMLHelper::_('image', 'media/com_phocapdf/images/administrator/icon-16-false.png', '' )
		.' '. Text::_('COM_PHOCAPDF_PLUGIN_IS_DISABLED_IN_MANAGER');
	}

}

echo '<br />' .HTMLHelper::_('image', 'media/com_phocapdf/images/administrator/icon-16-warning.png', '' )
.' '. Text::_('COM_PHOCAPDF_SETTINGS_WARNING');


echo '</div>';

echo '<div id="phocapdf-apply"><a href="#" class="btn btn-success" onclick="javascript: submitbutton(\'phocapdfplugin.apply\')"><i class="icon-apply icon-white"></i>&nbsp;&nbsp;'.Text::_('COM_PHOCAPDF_SAVE').'</a></div>';



echo '<div style="clear:both"></div>';
echo '</div>';
