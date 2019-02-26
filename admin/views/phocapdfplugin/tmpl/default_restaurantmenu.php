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


?>

<ul id="myTab" class="nav nav-tabs">
  <li class="active"><a href="#site" data-toggle="tab" ><?php echo JHTML::_( 'image', 'media/com_phocapdf/images/administrator/icon-16-site.png','') . '&nbsp;'.JText::_('COM_PHOCAPDF_SITE'); ?></a></li>
  <li class=""><a href="#header" data-toggle="tab"><?php echo JHTML::_( 'image', 'media/com_phocapdf/images/administrator/icon-16-header.png','') . '&nbsp;'.JText::_('COM_PHOCAPDF_HEADER'); ?></a></li>
  <li class=""><a href="#footer" data-toggle="tab"><?php echo JHTML::_( 'image', 'media/com_phocapdf/images/administrator/icon-16-footer.png','') . '&nbsp;'.JText::_('COM_PHOCAPDF_FOOTER'); ?></a></li>
  <li class=""><a href="#pdf" data-toggle="tab"><?php echo JHTML::_( 'image', 'media/com_phocapdf/images/administrator/icon-16-pdf.png','') . '&nbsp;'.JText::_('COM_PHOCAPDF_PDF'); ?></a></li>
  
</ul>
<div id="myTabContent" class="tab-content"><?php

echo '<div class="tab-pane fade active in" id="site">'."\n";
if($output = PhocaPDFHelperParams::renderSite( $this->form->getFieldset('phocasite'))) {
	echo $output;
} else {
	echo '<div style="text-align: center; padding: 5px;">'.JText::_('COM_PHOCAPDF_THERE_ARE_NO_PARAMETERS_FOR_THIS_ITEM').'</div>';
}
echo '</div>';

echo '<div class="tab-pane fade" id="header">'."\n";
if($output = PhocaPDFHelperParams::renderMisc($this->form->getFieldset('phocaheader'))) {
	echo $output;
} else {
	echo '<div style="text-align: center; padding: 5px; ">'.JText::_('COM_PHOCAPDF_THERE_ARE_NO_PARAMETERS_FOR_THIS_ITEM').'</div>';
}
echo '</div>';

echo '<div class="tab-pane fade" id="footer">'."\n";
if($output = PhocaPDFHelperParams::renderMisc($this->form->getFieldset('phocafooter'))) {
	echo $output;
} else {
	echo '<div style="text-align: center; padding: 5px; ">'.JText::_('COM_PHOCAPDF_THERE_ARE_NO_PARAMETERS_FOR_THIS_ITEM').'</div>';
}
echo '</div>';

echo '<div class="tab-pane fade" id="pdf">'."\n";
if($output = PhocaPDFHelperParams::renderMisc($this->form->getFieldset('phocapdf'))) {
	echo $output;
} else {
	echo '<div style="text-align: center; padding: 5px; ">'.JText::_('COM_PHOCAPDF_THERE_ARE_NO_PARAMETERS_FOR_THIS_ITEM').'</div>';
}
echo '</div>';



echo '<div id="phocapdf-apply"><a href="#" class="btn btn-large btn-success right" onclick="javascript: submitbutton(\'phocapdfplugin.apply\')"><i class="icon-apply icon-white"></i>&nbsp;&nbsp;'.JText::_('COM_PHOCAPDF_SAVE').'</a></div>';
	
echo '<div style="margin-top:20px">';	
if (isset($this->item->enabled)) {
	if ($this->item->enabled == 1) {
		echo JHTML::_('image', 'media/com_phocapdf/images/administrator/icon-16-true.png', '' )
		.' ' . JText::_('COM_PHOCAPDF_PLUGIN_IS_ENABLED_IN_MANAGER');
	} else {
		echo JHTML::_('image', 'media/com_phocapdf/images/administrator/icon-16-false.png', '' )
		.' '. JText::_('COM_PHOCAPDF_PLUGIN_IS_DISABLED_IN_MANAGER');
	}
	
}

echo '<br />' .JHTML::_('image', 'media/com_phocapdf/images/administrator/icon-16-warning.png', '' )
.' '. JText::_('COM_PHOCAPDF_SETTINGS_WARNING');

echo '</div>';
echo '<div style="clear:both"></div>';
echo '</div>';




