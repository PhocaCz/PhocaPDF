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
JHTML::_('behavior.tooltip');
jimport('joomla.filesystem.file');
?>
<div id="phocapdf">
<form action="<?php echo JRoute::_('index.php?option=com_phocapdf&view=phocapdfplugins'); ?>" method="post" name="adminForm" id="adminForm">

<table border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td valign="top">
		<ul class="phoca-plugins-menu">
		<?php
		foreach ($this->tmpl['plugins'] as $key => $value) {
			echo '<li '.$value->current.'>'.$value->link.'</li>';

		}
		?>
		</ul>
		</td>
		<td valign="top">
		<div class="phoca-plugins"><?php
		if(isset($this->tmpl['plugin']->element)) {
			if (JFile::exists(JPATH_COMPONENT_ADMINISTRATOR.'/views/phocapdfplugins/tmpl/default_'.$this->tmpl['plugin']->element.'.php')) {

				echo $this->loadTemplate($this->tmpl['plugin']->element);
			} else {
				echo JText::_('COM_PHOCAPDF_PLUGIN_NOT_EXIST');
			}
				
		} else {
			echo JText::_('COM_PHOCAPDF_NO_PHOCAPDF_PLUGIN_INSTALLED');
		}
		

		?><div class="phoca-plugins-ie">&nbsp;</div>
		</div>

		</td>
	</tr>
</table>

<div class="clearfix"></div>

<input type="hidden" name="cid[]" value="<?php echo $this->tmpl['plugin']->id; ?>" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="controller" value="phocaplugin" />
</form>
</div>