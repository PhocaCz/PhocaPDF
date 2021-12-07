<?php
/* @package Joomla
 * @copyright Copyright (C) Open Source Matters. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * @extension Phoca Extension
 * @copyright Copyright (C) Jan Pavelka www.phoca.cz
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */
defined('_JEXEC') or die();
use Joomla\CMS\Form\FormField;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Filesystem\Folder;


class JFormFieldPhocaPDFFontType extends FormField
{
	protected $type 		= 'PhocaPDFFontType';
	protected $path			= null;

	protected function getInput() {
		
		$font = $this->_getXmlFiles();
		$options = array();
		if (!empty($font)) {
			foreach($font as $option) {
				if (isset($option->tag)) {
					$val	= $option->tag;
					$text	= $option->tag;
					$options[] = HTMLHelper::_('select.option', $val, Text::_($text));
				}
			}
		}
		if (empty($options)) {
			return Text::_('COM_PHOCAPDF_NO_PHOCAPDF_FONT_FOUND');
		} else {
			return HTMLHelper::_('select.genericlist',  $options, $this->name, 'class="form-select"', 'value', 'text', $this->value, $this->id);
		}
		
	}
	
	protected function _getXmlFiles() {
		$xmlFiles 		= Folder::files($this->_getPath(), '.xml$', 1, true);
		
		$font			= array();
		
		// If at least one xml file exists
		if (count($xmlFiles) > 0) {
			$i = 0;
			foreach ($xmlFiles as $key => $value) {
				
				$xml = $this->_isManifest($value);
			
				if(!is_null($xml->children())) {
					
					foreach ($xml->children() as $key => $value) {
						
						
						if (is_a($value, 'SimpleXMLElement') && $value->getName() == 'tag') {
							
							$font[$i]		= new StdClass();
							$font[$i]->tag 	= (string)$value;
							$i++;
						}
						
					}
				}
			}
		}
		
		return $font;
	}
	
	
	function _isManifest($file) {
		$xml	= simplexml_load_file($file);
		if (!$xml) {
			unset ($xml);
			return null;
		}
		
		if (!is_object($xml) || ($xml->getName() != 'install' )) {
			
			unset ($xml);
			return null;
		}
		
		
		return $xml;
	}
	
	protected function _getPath() {
		if (empty($this->_path)) {
			$this->_path = JPATH_ADMINISTRATOR.'/components/com_phocapdf/fonts';
		}
		return $this->_path;
	}
}
?>
