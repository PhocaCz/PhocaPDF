<?php
/*
 * @package		Joomla.Framework
 * @copyright	Copyright (C) Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 * @component Phoca Component
 * @copyright Copyright (C) Jan Pavelka www.phoca.cz
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License version 2 or later;
 */
defined( '_JEXEC' ) or die( 'Restricted access' );
if(!defined('DS')) define('DS', DIRECTORY_SEPARATOR);
jimport( 'joomla.filesystem.folder' );

class com_phocapdfInstallerScript
{
	function install($parent) {
		
		//$db			= JFactory::getDBO();
		$msgSQL 	= '';
		$errFile	= array();
		$sccFile	= array();
		$msgError	= '';
		$msgSuccess	= '';
		
		// Install View
		$installView 		= self::installView($sccFile, $errFile);
		$renameBak 			= self::renameBak($sccFile, $errFile, 'freemono');
		$renameBak 			= self::renameBak($sccFile, $errFile, 'helvetica');
		$installContentView	= self::installContentView($sccFile, $errFile);
		//$installContent		= self::installContent($sccFile, $errFile);

		// Error
		if ($msgSQL !='') {
			$msgError .= '<br />' . $msgSQL;
		}
	
		if (!empty($sccFile)) {
			$msgSuccess .= '<br />' . implode("<br />", $sccFile);
		}
	
		if (!empty($errFile)) {
			$msgError .= '<br />' . implode("<br />", $errFile);
		}
		
	
		// END MESSAGE	
		if ($msgError != '') {
			$msg = '<span style="font-weight: bold;color:#ff0000;">'.JText::_('COM_PHOCAPDF_ERROR_INSTALL').'</span>: ' . $msgSuccess . $msgError;
			JFactory::getApplication()->enqueueMessage($msg, 'error');
		} else {
			$msg = '<span style="font-weight: bold;color:#00cc00;">'.JText::_('COM_PHOCAPDF_SUCCESS_INSTALL').'</span>: ' . $msgSuccess;
			$msg .= JText::_('COM_PHOCAPDF_INSTALLATION_NOT_COMPLETE');
			JFactory::getApplication()->enqueueMessage($msg, 'message');
		}
		
		//$link = 'index.php?option=com_phocapdf';
		//$this->setRedirect($link, $msg);
		

		
		$parent->getParent()->setRedirectURL('index.php?option=com_phocapdf');
	}
	function uninstall($parent) {}

	function update($parent) {
		
		
		//$db			= &JFactory::getDBO();
		//$dbPref 	= $db->getPrefix();
		$msgSQL 	= '';
		$errFile	= array();
		$sccFile	= array();
		$msgError	= '';
		$msgSuccess	= '';
		
		// Install View
		$installView 	= self::installView($sccFile, $errFile);
		$renameBak 		= self::renameBak($sccFile, $errFile, 'freemono');
		$renameBak 		= self::renameBak($sccFile, $errFile, 'helvetica');
		$installContentView	= self::installContentView($sccFile, $errFile);
		//$installContent	= self::installContent($sccFile, $errFile);

		// Error
		if ($msgSQL !='') {
			$msgError .= '<br />' . $msgSQL;
		}
	
		if (!empty($sccFile)) {
			$msgSuccess .= '<br />' . implode("<br />", $sccFile);
		}
	
		if (!empty($errFile)) {
			$msgError .= '<br />' . implode("<br />", $errFile);
		}
		
	
			
		// End Message
		if ($msgError != '') {
			$msg = '<span style="font-weight: bold;color:#ff0000;">'.JText::_('COM_PHOCAPDF_ERROR_UPGRADE').'</span>: ' . $msgSuccess . $msgError;
			JFactory::getApplication()->enqueueMessage($msg, 'error');
		} else {
			$msg = '<span style="font-weight: bold;color:#00cc00;">'.JText::_('COM_PHOCAPDF_SUCCESS_UPGRADE').'</span>: ' . $msgSuccess;
			$msg .= JText::_('COM_PHOCAPDF_INSTALLATION_NOT_COMPLETE');
			JFactory::getApplication()->enqueueMessage($msg, 'message');
		}
		
		//$link = 'index.php?option=com_phocapdf';
		//$this->setRedirect($link, $msg);

		//$msg 	=  JText::_('COM_PHOCAPDF_UPDATE_TEXT');
		//$msg   .= ' (' . JText::_('COM_PHOCAPDF_VERSION'). ': ' . $parent->get('manifest')->version . ')';
		//$app	= JFactory::getApplication();
		JFactory::getApplication()->redirect(JRoute::_('index.php?option=com_phocapdf'));
	}

	function preflight($type, $parent) {}

	function postflight($type, $parent) {}
	
	
	function installView(&$sccFile, &$errFile) {
		
		$success 	= '<span style="font-weight: bold;color:#00cc00;">'.JText::_('COM_PHOCAPDF_SUCCESS').'</span> - ';
		$error 		= '<span style="font-weight: bold;color:#ff0000;">'.JText::_('COM_PHOCAPDF_ERROR').'</span> - ';
		jimport( 'joomla.client.helper' );
		jimport( 'joomla.filesystem.file' );
		jimport( 'joomla.filesystem.folder' );
		$ftp 	=& JClientHelper::setCredentialsFromRequest('ftp');
		
		$src 		= JPATH_ROOT. '/administrator/components/com_phocapdf/files/pdf/pdf.php';
		$dest 		= JPATH_ROOT. '/libraries/joomla/document/pdf/pdf.php';
		$folderPath = JPATH_ROOT. '/libraries/joomla/document/pdf';
		
		if(!JFolder::create($folderPath, 0755)) {
			$errFile[]	= $error . JText::_( 'COM_PHOCAPDF_FOLDER_CREATING' ). ': ' . str_replace( JPATH_ROOT . '/', '', $folderPath);
		} else {
			$sccFile[]	= $success . JText::_( 'COM_PHOCAPDF_FOLDER_CREATING' ). ': ' . str_replace( JPATH_ROOT . '/', '', $folderPath);
		}
		
		$data = "<html>\n<body bgcolor=\"#FFFFFF\">\n</body>\n</html>";
		if(!JFile::write($folderPath.DS."index.html", $data)) {
			$errFile[]	= $error . JText::_( 'COM_PHOCAPDF_FILE_CREATING' ). ': ' . str_replace( JPATH_ROOT . '/', '',$folderPath).DS."index.html";
		} else {
			$sccFile[]	= $success . JText::_( 'COM_PHOCAPDF_FILE_CREATING' ). ': ' . str_replace( JPATH_ROOT . '/', '',$folderPath).DS."index.html";
		}
		
		
		if (file_exists($src)) {
			if (!JFile::copy($src, $dest)) {
				$errFile[]	= $error . JText::_( 'COM_PHOCAPDF_FILE_COPYING' ). ': '
					. '<br />&nbsp;&nbsp; - ' . JText::_( 'COM_PHOCAPDF_SOURCE_FILE' ). ': ' . str_replace( JPATH_ROOT . '/', '', $src)
					. '<br />&nbsp;&nbsp; - ' . JText::_( 'COM_PHOCAPDF_DESTINATION_FILE' ). ': ' . str_replace( JPATH_ROOT . '/', '', $dest);
			} else {
				$sccFile[]	= $success . JText::_( 'COM_PHOCAPDF_FILE_COPYING' ). ': '
					. '<br />&nbsp;&nbsp; - ' . JText::_( 'COM_PHOCAPDF_SOURCE_FILE' ). ': ' . str_replace( JPATH_ROOT . '/', '', $src)
					. '<br />&nbsp;&nbsp; - ' . JText::_( 'COM_PHOCAPDF_DESTINATION_FILE' ). ': ' . str_replace( JPATH_ROOT . '/', '', $dest);
			}
		} else {
			$errFile[] = $error . JText::_( 'COM_PHOCAPDF_ERROR_FILE_NOT_EXIST' ). ': ' . str_replace( JPATH_ROOT . '/', '', $src);
		}	
		
		if (!file_exists($dest)) {
			$errFile[] = $error . JText::_( 'COM_PHOCAPDF_ERROR_FILE_NOT_EXIST' ). ': ' . str_replace( JPATH_ROOT . '/', '', $dest);
		}
		
		return true;// will be not worked, we are working with errorMsg
	}
	
	function installContentView(&$sccFile, &$errFile) {
		
		$success 	= '<span style="font-weight: bold;color:#00cc00;">'.JText::_('COM_PHOCAPDF_SUCCESS').'</span> - ';
		$error 		= '<span style="font-weight: bold;color:#ff0000;">'.JText::_('COM_PHOCAPDF_ERROR').'</span> - ';
		jimport( 'joomla.client.helper' );
		jimport( 'joomla.filesystem.file' );
		jimport( 'joomla.filesystem.folder' );
		$ftp 	= JClientHelper::setCredentialsFromRequest('ftp');
		
		$src 	= JPATH_ROOT. '/administrator/components/com_phocapdf/files/com_content/views/article/view.pdf.php';
		$dest 	= JPATH_ROOT. '/components/com_content/views/article/view.pdf.php';
		
		
		if (file_exists($src)) {
			if (!JFile::copy($src, $dest)) {
				$errFile[]	= $error . JText::_( 'COM_PHOCAPDF_FILE_COPYING' ). ': '
					. '<br />&nbsp;&nbsp; - ' . JText::_( 'COM_PHOCAPDF_SOURCE_FILE' ). ': ' . str_replace( JPATH_ROOT . '/', '', $src)
					. '<br />&nbsp;&nbsp; - ' . JText::_( 'COM_PHOCAPDF_DESTINATION_FILE' ). ': ' . str_replace( JPATH_ROOT . '/', '', $dest);
			} else {
				$sccFile[]	= $success . JText::_( 'COM_PHOCAPDF_FILE_COPYING' ). ': '
					. '<br />&nbsp;&nbsp; - ' . JText::_( 'COM_PHOCAPDF_SOURCE_FILE' ). ': ' . str_replace( JPATH_ROOT . '/', '', $src)
					. '<br />&nbsp;&nbsp; - ' . JText::_( 'COM_PHOCAPDF_DESTINATION_FILE' ). ': ' . str_replace( JPATH_ROOT . '/', '', $dest);
			}
		} else {
			$errFile[] = $error . JText::_( 'COM_PHOCAPDF_ERROR_FILE_NOT_EXIST' ). ': ' . str_replace( JPATH_ROOT . '/', '', $src);
		}	
		
		if (!file_exists($dest)) {
			$errFile[] = $error . JText::_( 'COM_PHOCAPDF_ERROR_FILE_NOT_EXIST' ). ': ' . str_replace( JPATH_ROOT . '/', '', $dest);
		}
		
		return true;// will be not worked, we are working with errorMsg
	}
	
	function renameBak(&$sccFile, &$errFile, $fontName = 'freemono') {
	
		$success 	= '<span style="font-weight: bold;color:#00cc00;">'.JText::_('COM_PHOCAPDF_SUCCESS').'</span> - ';
		$error 		= '<span style="font-weight: bold;color:#ff0000;">'.JText::_('COM_PHOCAPDF_ERROR').'</span> - ';
		
		jimport( 'joomla.client.helper' );
		jimport( 'joomla.filesystem.file' );
		jimport( 'joomla.filesystem.folder' );
		$ftp 	= JClientHelper::setCredentialsFromRequest('ftp');
		
		$rsrc 		= JPATH_ROOT . '/administrator/components/com_phocapdf/fonts/' . $fontName . '.bak';
		$rdest 		= JPATH_ROOT . '/administrator/components/com_phocapdf/fonts/' . $fontName . '.xml';
		
		if (file_exists($rsrc) && !file_exists($rdest)) {
			if(!JFile::move($rsrc, $rdest)) {
				$errFile[]	= $error . JText::_( 'COM_PHOCAPDF_FILE_RENAMING' ). ': '
					. '<br />&nbsp;&nbsp; - ' . JText::_( 'COM_PHOCAPDF_SOURCE_FILE' ). ': ' . str_replace( JPATH_ROOT . '/', '', $rsrc)
					. '<br />&nbsp;&nbsp; - ' . JText::_( 'COM_PHOCAPDF_DESTINATION_FILE' ). ': ' . str_replace( JPATH_ROOT . '/', '', $rdest);
			} else {
				$sccFile[]	= $success . JText::_( 'COM_PHOCAPDF_FILE_RENAMING' ). ': '
					. '<br />&nbsp;&nbsp; - ' . JText::_( 'COM_PHOCAPDF_SOURCE_FILE' ). ': ' . str_replace( JPATH_ROOT . '/', '', $rsrc)
					. '<br />&nbsp;&nbsp; - ' . JText::_( 'COM_PHOCAPDF_DESTINATION_FILE' ). ': ' . str_replace( JPATH_ROOT . '/', '', $rdest);
			}
		} else {
			//$errFile[] = $error . JText::_( 'COM_PHOCAPDF_ERROR_FILE_NOT_EXIST' ). ': ' . str_replace( JPATH_ROOT . '/', '', $rsrc);
			//$errFile[] = $error . JText::_( 'COM_PHOCAPDF_ERROR_FILE_EXIST' ). ': ' . $rdest;
		}

		if (!file_exists($rdest)) {
			$errFile[] = $error . JText::_( 'COM_PHOCAPDF_ERROR_FILE_NOT_EXIST' ). ': ' . str_replace( JPATH_ROOT . '/', '', $rdest);
		}
		
		return true;// will be not worked, we are working with errorMsg
	}
	
	function installContent(&$sccFile, &$errFile) {
		
		$success 	= '<span style="font-weight: bold;color:#00cc00;">'.JText::_('COM_PHOCAPDF_SUCCESS').'</span> - ';
		$error 		= '<span style="font-weight: bold;color:#ff0000;">'.JText::_('COM_PHOCAPDF_ERROR').'</span> - ';
		
		jimport( 'joomla.client.helper' );
		jimport( 'joomla.filesystem.file' );
		jimport( 'joomla.filesystem.folder' );
		$ftp 	= JClientHelper::setCredentialsFromRequest('ftp');
		
		$src[0] = JPATH_ROOT . '/administrator/components/com_phocapdf/files/com_content/views/article/tmpl/default.php';
		$src[1] = JPATH_ROOT . '/administrator/components/com_phocapdf/files/com_content/views/category/tmpl/blog_item.php';
		$src[2] = JPATH_ROOT . '/administrator/components/com_phocapdf/files/com_content/views/featured/tmpl/default_item.php';
				 
		$dest[0] = JPATH_ROOT . '/components/com_content/views/article/tmpl/default.php';
		$dest[1] = JPATH_ROOT . '/components/com_content/views/category/tmpl/blog_item.php';
		$dest[2] = JPATH_ROOT . '/components/com_content/views/featured/tmpl/default_item.php';
				 
		// First backup
		$backUpError = 0;
		foreach ($dest as $destValue) {
			if (file_exists($destValue)) {
				
				if(JFile::getExt($destValue) == 'php') {
					$destValueBak = str_replace('.php', '.bak.php', $destValue);
				} else {
					$destValueBak = $destValue . '.bak';
				}
			
				if(!JFile::copy($destValue, $destValueBak)) {
					$backUpError = 1;
					$errFile[]	= $error . JText::_( 'COM_PHOCAPDF_FILE_BACKUPING' ). ': '
					. '<br />&nbsp;&nbsp; - ' . JText::_( 'COM_PHOCAPDF_SOURCE_FILE' ). ': ' . str_replace( JPATH_ROOT . '/', '', $destValue)
					. '<br />&nbsp;&nbsp; - ' . JText::_( 'COM_PHOCAPDF_DESTINATION_FILE' ). ': ' .str_replace( JPATH_ROOT . '/', '', $destValueBak);
				} else {
					$sccFile[]	= $success . JText::_( 'COM_PHOCAPDF_FILE_BACKUPING' ). ': '
						. '<br />&nbsp;&nbsp; - ' . JText::_( 'COM_PHOCAPDF_SOURCE_FILE' ). ': ' . str_replace( JPATH_ROOT . '/', '', $destValue)
						. '<br />&nbsp;&nbsp; - ' . JText::_( 'COM_PHOCAPDF_DESTINATION_FILE' ). ': ' . str_replace( JPATH_ROOT . '/', '', $destValueBak);
				}
			} else {
				$errFile[] = $error . JText::_( 'COM_PHOCAPDF_ERROR_FILE_NOT_EXIST' ). ': ' . str_replace( JPATH_ROOT . '/', '', $destValue);
			}
		}
		if ($backUpError == 1) {
			$errFile[] = $error . JText::_( 'COM_PHOCAPDF_ERROR_CONTENT_FILES_NOT_COPIED' ). ': ' . str_replace( JPATH_ROOT . '/', '', $destValue);
			return false;// no more actions if files are not backuped
		}
			
		$i = 0;
		foreach ($dest as $destValue) {
			if (file_exists($destValue)) {
				if(!JFile::copy($src[$i], $destValue)) {
					$errFile[]	= $error . JText::_( 'COM_PHOCAPDF_FILE_COPYING' ). ': '
					. '<br />&nbsp;&nbsp; - ' . JText::_( 'COM_PHOCAPDF_SOURCE_FILE' ). ': ' . str_replace( JPATH_ROOT . '/', '', $src[0])
					. '<br />&nbsp;&nbsp; - ' . JText::_( 'COM_PHOCAPDF_DESTINATION_FILE' ). ': ' . str_replace( JPATH_ROOT . '/', '', $destValue);
				} else {
					$sccFile[]	= $success . JText::_( 'COM_PHOCAPDF_FILE_COPYING' ). ': '
						. '<br />&nbsp;&nbsp; - ' . JText::_( 'COM_PHOCAPDF_SOURCE_FILE' ). ': ' . str_replace( JPATH_ROOT . '/', '', $src[0])
						. '<br />&nbsp;&nbsp; - ' . JText::_( 'COM_PHOCAPDF_DESTINATION_FILE' ). ': ' . str_replace( JPATH_ROOT . '/', '', $destValue);
				}
			} else {
				if ($msgFile != '') { $msgFile .= '<br />';}
				$errFile[] = $error . JText::_( 'COM_PHOCAPDF_ERROR_FILE_NOT_EXIST' ). ': ' . str_replace( JPATH_ROOT . '/', '', $destValue);
			}
			$i++;
		}
		
		return true;// will be not worked, we are working with errorMsg
	}
}
?>