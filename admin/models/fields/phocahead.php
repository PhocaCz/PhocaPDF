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
jimport('joomla.html.html');
jimport('joomla.form.formfield');

class JFormFieldPhocaHead extends JFormField
{
	protected $type = 'PhocaHead';
	
	protected function getInput() {
		return '';
	}
	
	protected function getLabel() {
		
		echo '<div class="clearfix"></div>';
		
		$phocaImage	= ( (string)$this->element['phocaimage'] ? $this->element['phocaimage'] : '' );
		
		$image 		= '';
		$style		= 'background: #CCE6FF; color: #0069CC;padding:5px;margin:5px 0;';
		
		if ($phocaImage != ''){
			$image 	= JHTML::_('image', 'media/com_phocapdf/images/adminstrator/'. $phocaImage, '' );
		}
		
		if ($this->element['default']) {
		
			if ($image != '') {
				return '<div style="'.$style.'">'
				.'<table border="0"><tr>'
				.'<td valign="middle" align="center">'. $image.'</td>'
				.'<td valign="middle" align="center">'
				.'<strong>'. JText::_($this->element['default']) . '</strong></td>'
				.'</tr></table>'
				.'</div>';
			} else {
				return '<div style="'.$style.'">'
				.'<strong>'. JText::_($this->element['default']) . '</strong>'
				.'</div>';
			}
		} else {
			return parent::getLabel();
		}
		echo '<div class="clearfix"></div>';
	}
	
	/*
	protected function getLabel() {
		$phocaImage	= ( (string)$this->element['phocaimage'] ? $this->element['phocaimage'] : '' );
		
		$image 		= '';
		$style		= 'background: #CCE6FF; color: #0069CC; padding: 5px; vertical-align: middle';
		
		if ($phocaImage != ''){
			$image 	= JHTML::_('image', 'administrator/components/com_phocagallery/assets/images/'. $phocaImage, '' );
			$style	= 'background: #CCE6FF; color: #0069CC; height:65px; padding: 5px; vertical-align: middle';
		}
		
		if ($this->element['label']) {
		
			return $image;
			/*return ''
			.'<li><div style="'.$style.'"><label>'. $image.'</label>'
			.'<strong>'. JText::_($this->element['label']) . '</strong>'
			.'</div></li>';*/
			
			
			
		//echo '<div class="clearfix"></div>';
		/*return ''
			.'<li><div style="'.$style.'"><label>'. $image.'</label>'
			.'<strong>'. JText::_($this->element['label']) . '</strong>'
			.'</div></li>';
			
		return $image;
		echo '<div class="clearfix"></div>';
	
			
		}
	}*/
}
?>