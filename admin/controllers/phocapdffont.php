<?php
/* @package Joomla
 * @copyright Copyright (C) Open Source Matters. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * @extension Phoca Extension
 * @copyright Copyright (C) Jan Pavelka www.phoca.cz
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */
defined('_JEXEC') or die();
use Joomla\CMS\MVC\Controller\FormController;
use Joomla\Utilities\ArrayHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Session\Session;
use Joomla\CMS\Factory;
use Joomla\CMS\Client\ClientHelper;
jimport('joomla.client.helper');
jimport('joomla.application.component.controlleradmin');

class PhocaPDFCpControllerPhocaPDFFont extends FormController
{
	protected	$option 		= 'com_phocapdf';

	public function &getModel($name = 'PhocaPDFFonts', $prefix = 'PhocaPDFCpModel', $config = array())
	{
		$model = parent::getModel($name, $prefix, array('ignore_request' => true));
		return $model;
	}


	function delete() {

		$cid 	= Factory::getApplication()->input->get( 'cid', array(), 'array' );// POST (Icon), GET (Small Icon)
		ArrayHelper::toInteger($cid);

		if (count($cid ) < 1) {
			throw new Exception(Text::_('COM_PHOCAPDF_SELECT_ITEM_DELETE'), 500);
			return false;
		}

		$model 		= $this->getModel();
		$errorMsg	= $model->delete($cid);
 		if($errorMsg != '') {
			//echo "<script> alert('".$model->getError(true)."'); window.history.go(-1); </script>\n";
			$msg = Text::_( 'COM_PHOCAPDF_FONT_ERROR_DELETE' ) . '<br />' . $errorMsg;
		} else {
			$msg = Text::_( 'COM_PHOCAPDF_FONT_SUCCESS_DELETE' );
		}

		$this->setRedirect( 'index.php?option=com_phocapdf&view=phocapdffonts', $msg );
	}


	function install() {
		// Check for request forgeries
		Session::checkToken() or die( 'Invalid Token' );

		$post 	= Factory::getApplication()->input->get('post');
		$ftp 	= ClientHelper::setCredentialsFromRequest('ftp');

		$model = $this->getModel();

		if ($model->install()) {
			$cache = Factory::getCache('mod_menu');
			$cache->clean();
			$msg = Text::_('COM_PHOCAPDF_NEW_FONT_INSTALLED');
		} else {
			$msg = Text::_( 'COM_PHOCAPDF_FONT_ERROR_INSTALL' );
		}

		$this->setRedirect( 'index.php?option=com_phocapdf&view=phocapdffonts', $msg );
	}

}

?>
