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
defined('JPATH_BASE') or die();

class JElementPhocaEditor extends JElement
{
	var	$_name 			= 'PhocaEditor';
	var $_phocaParams 	= null;

	function fetchElement($name, $value, &$node, $control_name) {
		
		$document	= &JFactory::getDocument();
		$option 	= JRequest::getCmd('option');
		
		$globalValue = &$this->_getPhocaParameter( 'display_editor' );
		if ($globalValue == '') {
			$globalValue = 1;
		}

		$rows = $node->attributes('rows');
		$cols = $node->attributes('cols');
		$size = ( $node->attributes('size') ? 'size="'.$node->attributes('size').'"' : '' );
		$class = ( $node->attributes('class') ? 'class="'.$node->attributes('class').'"' : 'class="text_area"' );
        /*
         * Required to avoid a cycle of encoding &
         * html_entity_decode was used in place of htmlspecialchars_decode because
         * htmlspecialchars_decode is not compatible with PHP 4
         */
        $value = htmlspecialchars(html_entity_decode($value, ENT_QUOTES), ENT_QUOTES);
		
		if ($globalValue == 1) {
			$editor = &JFactory::getEditor();
			$html = $editor->display( $control_name.'['.$name.']',  $value, '550', '300', '60', '20', array('pagebreak', 'readmore'/*, 'image'*/));
			
			// Because of the problem in [editor].php
			//$html	= str_replace('id="'.$control_name.'['.$name.']'.'"', 'id="'.$control_name.$name.'"',$html);
			
		} else {
			$html	= '<textarea name="'.$control_name.'['.$name.']" cols="'.$cols.'" rows="'.$rows.'" '.$class.' id="'.$control_name.$name.'" >'.$value.'</textarea>';
		}
	return $html;
	}
	
	function _setPhocaParams(){
	
		$component 			= 'com_phocapdf';
		$paramsC			= JComponentHelper::getParams($component) ;
		$this->phocaParams	= $paramsC;
	}

	function _getPhocaParameter( $name ){
	
		// Don't call sql query by every param item (it will be loaded only one time)
		if (!$this->_phocaParams) {
			$params = &$this->_setPhocaParams();
		}
		$globalValue 	= &$this->_phocaParams->get( $name, '' );	
		return $globalValue;
	}
}
?>