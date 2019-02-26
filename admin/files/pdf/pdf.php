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
defined('JPATH_PLATFORM') or die;
jimport('joomla.application.module.helper');
jimport('joomla.document.document');
jimport('joomla.filesystem.file');

class JDocumentPdf extends JDocument
{ 
	// Document
	public $_generator	= 'Phoca PDF';
	public $_charset 	= 'utf-8';
	public $_mime 		= 'application/pdf';
	public $_type 		= 'pdf';
	
	// PDF (Html)
	public $_links 		= array();
	public $_custom 	= array();
	protected $_caching = null;
	
	// PDF
	public $_header			= null;
	public $_name			= 'Phoca';
	public $_article_title 	= '';
	public $_article_text	= '';

	
	public function __construct($options = array()) {
		
		parent::__construct($options);
		$this->_type = 'pdf';
		$this->_mime = 'application/pdf';
		$this->_caching = null;
		//header('Content-type: application/pdf');
		JApplicationWeb::setHeader('Content-type', 'application/pdf', true);//Because of cache
		// Set default mime type and document metadata (meta data syncs with mime type by default)
		//$this->setMetaData('Content-Type', 'application/pdf', true);
		//$this->setMetaData('robots', 'index, follow');
	}
	
	function setHeader($text) {
		$this->_header = $text;
	}
	function getHeader() {
		return $this->_header;
	}

	function setName($name = 'Phoca') {
		$this->_name = $name;
	}

	function getName() {
		return $this->_name;
	}
	
	function setArticleText($text) {
		$this->_article_text = $text;
	}

	function getArticleText() {
		return $this->_article_text;
	}
	
	function setArticleTitle($text) {
		$this->_article_title = $text;
	}

	function getArticleTitle() {
		return $this->_article_title;
	}

	function render( $caching = false, $params = array()) {
		
		//header('Content-type: application/pdf');
		//J Response::allowCache(false);
		JApplicationWeb::setHeader('Content-type', 'application/pdf', true);// Because of cache
		JApplicationWeb::setHeader('Content-disposition', 'inline; filename="'.$this->getName().'.pdf"', true);
		
		//$this->_caching = $caching;
		//Call static function because of using on different places by different extensions
		if (JFile::exists(JPATH_ADMINISTRATOR.'/components/com_phocapdf/helpers/phocapdfrender.php')) {
			require_once(JPATH_ADMINISTRATOR.'/components/com_phocapdf/helpers/phocapdfrender.php');
		} else {
			throw new Exception('Document cannot be created - Loading of Phoca PDF library (Render) failed', 404);
			return false;
		}

		parent::render();
		
		$data = PhocaPdfRender::renderPDF($this);

		return $data;
	}
	
	function addCustomTag() {	
		return true;
	}
	
	public function getHeadData() {
		$data = array();
		$data['title']		= $this->title;
		$data['description']= $this->description;
		$data['link']		= $this->link;
		$data['metaTags']	= $this->_metaTags;
		$data['links']		= $this->_links;
		$data['styleSheets']= $this->_styleSheets;
		$data['style']		= $this->_style;
		$data['scripts']	= $this->_scripts;
		$data['script']		= $this->_script;
		$data['custom']		= $this->_custom;
		return $data;
	}
	
	public function setHeadData($data)
	{
		
		if (empty($data) || !is_array($data)) {
			return;
		}
		
		$this->title		= (isset($data['title']) && !empty($data['title'])) ? $data['title'] : $this->title;
		$this->description	= (isset($data['description']) && !empty($data['description'])) ? $data['description'] : $this->description;
		$this->link			= (isset($data['link']) && !empty($data['link'])) ? $data['link'] : $this->link;
		$this->_metaTags	= (isset($data['metaTags']) && !empty($data['metaTags'])) ? $data['metaTags'] : $this->_metaTags;
		$this->_links		= array();//(isset($data['links']) && !empty($data['links'])) ? $data['links'] : $this->_links;
		$this->_styleSheets	= array();//(isset($data['styleSheets']) && !empty($data['styleSheets'])) ? $data['styleSheets'] : $this->_styleSheets;
		$this->_style		= array();//(isset($data['style']) && !empty($data['style'])) ? $data['style'] : $this->_style;
		$this->_scripts		= array();//(isset($data['scripts']) && !empty($data['scripts'])) ? $data['scripts'] : $this->_scripts;
		$this->_script		= array();//(isset($data['script']) && !empty($data['script'])) ? $data['script'] : $this->_script;
		$this->_custom		= array();//(isset($data['custom']) && !empty($data['custom'])) ? $data['custom'] : $this->_custom;	
	}


	/* 
	public function setBuffer($content, $options = array())
	{
		// The following code is just for backward compatibility.
		if (func_num_args() > 1 && !is_array($options)) {
			$args = func_get_args(); $options = array();
			$options['type'] = $args[1];
			$options['name'] = (isset($args[2])) ? $args[2] : null;
		}

		parent::$_buffer[$options['type']][$options['name']] = $content;
	}*/
	/*
	public function setBuffer($content, $options = array())
	{
		// The following code is just for backward compatibility.
		if (func_num_args() > 1 && !is_array($options))
		{
			$args = func_get_args();
			$options = array();
			$options['type'] = $args[1];
			$options['name'] = (isset($args[2])) ? $args[2] : null;
		}

		parent::$_buffer[$options['type']][$options['name']] = $content;

		return $this;
	}
*/
}