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
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('formbehavior.chosen', 'select');


//$route = 'index.php?option=com_phocapdf&view=phocapdfplugins';
$route = 'index.php?option=com_phocapdf&amp;layout=edit&amp;extension_id='.(int)$this->item->extension_id;


?>
<script type="text/javascript">
Joomla.submitbutton = function(task){
	if (task == '<?php echo $this->t['task'] ?>.cancel' || document.formvalidator.isValid(document.getElementById('adminForm'))) {
		<?php //echo $this->form->getField('header_data')->save(); ?>
		<?php //echo $this->form->getField('footer_data')->save(); ?>
		<?php
		foreach($this->form->getFieldset('phocaheader') as $k => $v) {
			if (strtolower($v->type) == 'phocapdfeditor' || strtolower($v->type) == 'editor') {
				echo $v->save();
			}
		}
		foreach($this->form->getFieldset('phocafooter') as $k => $v) {
			if (strtolower($v->type) == 'phocapdfeditor' || strtolower($v->type) == 'editor') {
				echo $v->save();
			}
		}
		?>
		Joomla.submitform(task, document.getElementById('adminForm'));
	}
	else {
		alert('<?php echo JText::_('JGLOBAL_VALIDATION_FORM_FAILED', true);?>');
	}
}
</script>
<div id="phocapdf">
<form action="<?php echo JRoute::_($route); ?>" method="post" name="adminForm" id="adminForm" class="form-validate form-horizontal">


<table border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td valign="top">
		<ul class="phoca-plugins-menu">
		<?php
		foreach ($this->items as $key => $value) {
			echo '<li '.$value->current.'>'.$value->link.'</li>';

		}

		?>
		</ul>
		</td>
		<td valign="top">
		<div class="phoca-plugins"><?php


		if(isset($this->item->element)) {
			if (JFile::exists(JPATH_COMPONENT_ADMINISTRATOR.'/views/phocapdfplugins/tmpl/default_'.$this->item->element.'.php')) {

				echo $this->loadTemplate($this->item->element);
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
<input type="hidden" name="jform[folder]" id="jform_folder" value="<?php echo $this->item->folder; ?>" />
<input type="hidden" name="jform[element]" id="jform_element" value="<?php echo $this->item->element; ?>" />
<input type="hidden" name="jform[extension_id]" id="jform_extension_id" value="<?php echo $this->item->extension_id; ?>" />
<input type="hidden" name="jform[name]" id="jform_extension_name" value="<?php echo $this->item->name; ?>" />
<input type="hidden" name="cid[]" value="<?php echo $this->item->extension_id; ?>" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="controller" value="phocaplugin" />
<?php echo JHTML::_( 'form.token' ); ?>
</form>
</div>
