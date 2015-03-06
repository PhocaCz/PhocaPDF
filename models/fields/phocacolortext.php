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
defined('JPATH_BASE') or die;
jimport('joomla.form.formfield');

class JFormFieldPhocaColorText extends JFormField
{
	protected $type 		= 'PhocaColorText';
	protected $phocaParams 	= null;

	protected function getInput() {
	
		$document		= JFactory::getDocument();
		$app			= JFactory::getApplication();
		$option 		= $app->input->get('option');
		$globalValue 	= $this->_getPhocaParams( $this->element['name'] );
		
		JHTML::stylesheet( 'administrator/components/com_phocapdf/assets/jcp/picker.css' );
		$document->addScript(JURI::base(true).'/components/com_phocapdf/assets/jcp/picker.js');
		
		// Initialize some field attributes.
		$size		= $this->element['size'] ? ' size="'.(int) $this->element['size'].'"' : '';
		$class		= $this->element['class'] ? ' class="'.(string) $this->element['class'].'"' : '';
		$maxLength	= $this->element['maxlength'] ? ' maxlength="'.(int) $this->element['maxlength'].'"' : '';
		$readonly	= ((string) $this->element['readonly'] == 'true') ? ' readonly="readonly"' : '';
		$disabled	= ((string) $this->element['disabled'] == 'true') ? ' disabled="disabled"' : '';
		// Initialize JavaScript field attributes.
		$onchange	= $this->element['onchange'] ? ' onchange="'.(string) $this->element['onchange'].'"' : '';
		
		$value 		= htmlspecialchars($this->value, ENT_COMPAT, 'UTF-8');
		
		// TODO 1.6
		// MENU - Set Default value to "" because of saving "" value into the menu link ( use global = "")
		if ($option == "com_menus") {
			$DefaultValue	= (string)$this->element['default'];
			if ($value == $DefaultValue) {
				$value = '';
			}
		}

		
		// Color Picker
		$nameCP = str_replace('[', '_', $this->name);
		$nameCP = str_replace(']', '', $nameCP);
		//$html .= '<span style="position:relative;float:left;margin-left:10px;margin-top:3px;" onclick="openPicker(\''.$nameCP.'\')"  class="picker_buttons">' . JText::_('COM_PHOCAPDF_PICK_COLOR') . '</span>';
		// MENU - Display the global value
		/*if ($option == "com_menus") {
			$html .= '<span style="margin-left:10px;">[</span><span style="background:#fff"> ' . $globalValue . ' </span><span>]</span>';
		}*/
		
		$html[] = '<div class="input-append">';
		$html[] = '<input type="text" name="'.$this->name.'" id="'.$this->id.'" value="'.$value.'"'
			   .$class.$size.$disabled.$readonly.$onchange.$maxLength.'/>';
		$html[] = '<a class="btn" title="'.JText::_('COM_PHOCAPDF_PICK_COLOR').'"'
				.' onclick="openPicker(\''.$nameCP.'\')">'
				. JText::_('COM_PHOCAPDF_PICK_COLOR').'</a>';
		$html[] = '</div>'. "\n";
			
		return implode("\n", $html);

	}
	
	protected function getLabel() {
		echo '<div class="clearfix"></div>';
		return parent::getLabel();
		echo '<div class="clearfix"></div>';
	}
	
	protected function _setPhocaParams(){

		
		$component 			= 'com_phocapdf';
		$paramsC			= JComponentHelper::getParams($component) ;
		$this->phocaParams	= $paramsC;
	}

	protected function _getPhocaParams( $name ){
	
		// Don't call sql query by every param item (it will be loaded only one time)
		if (!$this->phocaParams) {
			$params = $this->_setPhocaParams();
		}
		$globalValue 	= $this->phocaParams->get( $name, '' );	
		return $globalValue;
	}
}
?>