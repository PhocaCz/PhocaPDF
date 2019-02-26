<?php
/* @package Joomla
 * @copyright Copyright (C) Open Source Matters. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * @extension Phoca Extension
 * @copyright Copyright (C) Jan Pavelka www.phoca.cz
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();
jimport( 'joomla.application.component.modellist' );
jimport( 'joomla.installer.installer' );
jimport( 'joomla.installer.helper' );
jimport( 'joomla.filesystem.folder' );
jimport( 'joomla.filesystem.file' );
class PhocaPDFCpModelPhocaPDFFonts extends JModelList
{
	protected $option 		= 'com_phocapdf';
	protected $path_dest	= null;
	protected $path_source 	= array();
	protected $total_files 	= null;
	protected $context		= 'com_phocapdf.phocapdffonts';


	public function __construct($config = array())
	{
		parent::__construct($config);

		// Guess the context as Option.ModelName.
		if (empty($this->context)) {
			$this->context = strtolower($this->option.'.'.$this->getName());
		}

		if (!$this->total_files) {
			$xmlFiles			= JFolder::files($this->getPathDest(), '.xml$', 1, true);
			$this->total_files	= count($xmlFiles);
		}

		// Correct the list.start in case some of file will be deleted - then we must correct list.start
		if((int)$this->total_files == (int)$this->getState('list.start') || ((int)$this->total_files != 0 && (int)$this->total_files < (int)$this->getState('list.start'))) {
			$this->setState('list.start', 0);

		}

	}




	public function getItems($removeInfo = 0)
	{

		$app = JFactory::getApplication();
		// Get a storage key.
		$store = $this->getStoreId();

		// Try to load the data from internal storage.
		if (!empty($this->cache[$store])) {
			return $this->cache[$store];
		}

		// Load Items - - - - -
		$items				= array();
		$xmlFiles 			= JFolder::files($this->getPathDest(), '.xml$', 1, true);

		//$this->total_files 	= count($xmlFiles);

		// Get list while deleting info - while deleting we get no info form state
		$cF = count($xmlFiles);
		if ($removeInfo == 1) {
			$iV = (int)$app->input->get('liststart', 0, 'int');
			$nV	= (int)$iV + (int)$app->input->get('listlimit', 10, 'int');
			if ($nV > $cF) {
				$nV = $cF;
			}
		} else {
			$iV = (int)$this->getState('list.start');
			$nV	= (int)$this->getState('list.start') + (int)$this->getState('list.limit');
			if ($nV > $cF) {
				$nV = $cF;
			}
		}

		// If at least one xml file exists
		if ($cF > 0) {
			$j = 0;
			for ($i = $iV, $n = $nV; $i < $n; $i++) {
				$row = &$xmlFiles[$i];
				$items[$j]				= new StdClass();

				// Is it a valid joomla installation manifest file?
				$xml = $this->_isManifest($row);

				if(!is_null($xml->children())) {
					foreach ($xml->children() as $key => $value) {


						$items[$j]->id			=	$i + 1;
						$items[$j]->checked_out	=	false;


						if (is_a($value, 'SimpleXMLElement') && $value->getName() == 'name') {
							//$items[$j]->name		= $value->data();
							$items[$j]->name		= (string)$xml->children();
						}
						if (is_a($value, 'SimpleXMLElement') && $value->getName() == 'tag') {
							//$items[$j]->tag			=	$value->data();
							$items[$j]->tag		= (string)$xml->children()->tag;
						}

						// Get list while deleting function
						if ($removeInfo == 1) {
							if (is_a($value, 'SimpleXMLElement') && $value->getName() == 'files') {
								if(!is_null($value->children())) {

									foreach ($value->children() as $key2 => $value2) {
										//$items[$j]->files[] 		= $value2->data();
										$items[$j]->files[]			= (string)$value2;
										$items[$j]->manifestfile	= $row;
									}
								}
							}
						}
					}
				}
				$j++;
			}
		}
		// End Load Items - - - - -

		// Add the items to the internal cache.
		$this->cache[$store] = $items;

		return $this->cache[$store];
	}

	function getTotal() {
		if (empty($this->total_files)) {
			// Should not happen, because we should got $this->total_files by getData()
			$xmlFiles 			= JFolder::files($this->getPathDest(), '.xml$', 1, true);
			$this->total_files 	= count($xmlFiles);
		}

		return $this->total_files;
	}

	function getPathDest() {
		if (empty($this->path_dest)) {
			$this->path_dest = JPATH_COMPONENT_ADMINISTRATOR.'/fonts';
		}
		return $this->path_dest;
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

	function install() {

		$app = JFactory::getApplication();
		$package = $this->getPackageFromUpload();

		if (!$package) {
			$app->enqueueMessage(JText::_('COM_PHOCAPDF_UNABLE_TO_FIND_INSTALL_PACKAGE'), 'error');
			$this->deleteTempFiles();
			return false;
		}

		if ($package['dir'] && JFolder::exists($package['dir'])) {
			$this->setPath('source', $package['dir']);
		} else {
			$app->enqueueMessage(JText::_('COM_PHOCAPDF_INSTALL_PATH_NOT_EXIST'), 'error');
			$this->deleteTempFiles();
			return false;
		}

		// We need to find the installation manifest file
		if (!$this->_findManifest()) {
			$app->enqueueMessage(JText::_('COM_PHOCAPDF_UNABLE_TO_FIND_REQUIRED_INFORMATION_INSTALL_PACKAGE'), 'error');
			$this->deleteTempFiles();
			return false;
		}

		// Files - copy files in manifest
		foreach ($this->_manifest->children() as $child)
		{


			if (is_a($child, 'SimpleXMLElement') && $child->getName() == 'files') {

				if ($this->parseFiles($child) === false) {
					$app->enqueueMessage(JText::_('COM_PHOCAPDF_UNABLE_TO_FIND_REQUIRED_INFORMATION_INSTALL_PACKAGE'), 'error');
					$this->deleteTempFiles();
					return false;
				}

			}

		}

		// File - copy the xml file
		$copyFile 		= array();
		$path['src']	= $this->getPath( 'manifest' ); // XML file will be copied too
		$path['dest']	= $this->getPathDest() .'/'. basename($this->getPath('manifest'));
		$copyFile[] 	= $path;
		$this->copyFiles($copyFile);
		$this->deleteTempFiles();

		return true;
	}

	protected function getPackageFromUpload() {

		$app = JFactory::getApplication();
		// Get the uploaded file information

		$userfile = $app->input->files->get( 'Filedata', null, 'raw');

		// Make sure that file uploads are enabled in php
		if (!(bool) ini_get('file_uploads')) {
			$app->enqueueMessage(JText::_('COM_PHOCAPDF_WARNINSTALLFILE'), 'error');
			return false;
		}
		// Make sure that zlib is loaded so that the package can be unpacked
		if (!extension_loaded('zlib')) {
			$app->enqueueMessage(JText::_('COM_PHOCAPDF_WARNINSTALLZLIB'), 'error');
			return false;
		}
		// If there is no uploaded file, we have a problem...
		if (!is_array($userfile) ) {
			$app->enqueueMessage(JText::_('COM_PHOCAPDF_NO_FILE_SELECTED'), 'error');
			return false;
		}
		// Check if there was a problem uploading the file.
		if ( $userfile['error'] || $userfile['size'] < 1 ) {
			$app->enqueueMessage(JText::_('COM_PHOCAPDF_WARNINSTALLUPLOADERROR'), 'error');
			return false;
		}

		// Build the appropriate paths
		$config 	= JFactory::getConfig();
		$tmp_dest 	= $config->get('tmp_path').'/'.$userfile['name'];
		$tmp_src	= $userfile['tmp_name'];

		// Move uploaded file
		jimport('joomla.filesystem.file');
		$uploaded = JFile::upload($tmp_src, $tmp_dest, false, true);

		// Unpack the downloaded package file
		$package = self::unpack($tmp_dest);
		$this->_manifest =& $manifest;

		$this->setPath('packagefile', $package['packagefile']);
		$this->setPath('extractdir', $package['extractdir']);

		return $package;
	}

	protected function deleteTempFiles () {
		// Delete Temp files
		$path = $this->getPath('source');
		if (is_dir($path)) {
			$val = JFolder::delete($path);
		} else if (is_file($path)) {
			$val = JFile::delete($path);
		}
		$packageFile = $this->getPath('packagefile');
		if (is_file($packageFile)) {
			$val = JFile::delete($packageFile);
		}
		$extractDir = $this->getPath('extractdir');
		if (is_dir($extractDir)) {
			$val = JFolder::delete($extractDir);
		}
	}

	protected function copyFiles($files) {

		$app = JFactory::getApplication();

		if (is_array($files) && count($files) > 0) {
			foreach ($files as $file) {
				// Get the source and destination paths
				$filesource	= JPath::clean($file['src']);
				$filedest	= JPath::clean($file['dest']);

				if (!file_exists($filesource)) {
					$app->enqueueMessage(JText::sprintf('COM_PHOCAPDF_ERROR_FILE_NOT_EXIST_S', $filesource), 'error');
					return false;
				} else {
					if (!(JFile::copy($filesource, $filedest))) {
						$app->enqueueMessage(JText::sprintf('COM_PHOCAPDF_ERROR_FAILED_TO_COPY_FILE_TO', $filesource, $filedest), 'error');
						return false;
					}
				}
			}
		} else {
			$app->enqueueMessage(JText::sprintf('COM_PHOCAPDF_ERROR_FONT_FILE_NOT_FOUND'), 'error');
			return false;
		}

		return count($files);
	}


	/*
	 * DELETE
	 */
	function delete($cid = array()) {

		$errorMsg 	= '';
		$items 		= $this->getItems(1);


		foreach ($cid as $key => $value) {

			foreach($items as $key2 => $value2) {

				if ($value2->id == $value && $value2->tag == 'freemono') {
					$errorMsg .= $value2->name . ': '.JText::_('COM_PHOCAPDF_ERROR_BASIC_FONT_CANNOT_BE_DELETED') . '<br />';
				} else {
					if ((int)$value2->id == (int)$value) {
						if (isset($value2->files)) {
							foreach($value2->files as $key3 => $value3) {
								if ($value3 != 'index.html') {
									if (JFile::exists($this->getPathDest() . '/' . $value3)) {
										if(JFile::delete($this->getPathDest() . '/' . $value3)) {

										} else {
											$errorMsg .= $value3 . ': '.JText::_('COM_PHOCAPDF_FILE_COULD_NOT_BE_DELETED') . '<br />';
										}
									} else {
										// $errorMsg .= $value3 . ': '.JText::_('This file doesn\'t exist') . '<br />';
									}
								}
							}

							// Delete the manifest file too
							if (isset($value2->manifestfile)) {
								if (JFile::exists($value2->manifestfile)) {
									if(JFile::delete($value2->manifestfile)) {

									} else {
										$errorMsg .= $value3 . ': '.JText::_('COM_PHOCAPDF_XML_INSTALL_FILE_COULD_NOT_BE_DELETED') . '<br />';
									}
								}
							} else {
								$errorMsg .= JText::_('COM_PHOCAPDF_XML_FILE_COULD_NOT_BE_FOUND') . '<br />';
							}
						} else {
							$errorMsg .= JText::_('COM_PHOCAPDF_XML_LIST_OF_FILES_COULD_NOT_BE_FOUND') . '<br />';
						}
					}
				}
			}
		}

		return $errorMsg;
	}


	function getPath($name, $default=null){
		return (!empty($this->path_source[$name])) ? $this->path_source[$name] : $default;
	}

	function setPath($name, $value) {
		$this->path_source[$name] = $value;
	}

	function _findManifest() {

		$app = JFactory::getApplication();

		// Get an array of all the xml files from the installation directory
		$xmlfiles = JFolder::files($this->getPath('source'), '.xml$', 1, true);

		// If at least one xml file exists
		if (count($xmlfiles) > 0) {
			foreach ($xmlfiles as $file)
			{
				// Is it a valid joomla installation manifest file?
				$manifest = $this->_isManifest($file);
				if (!is_null($manifest)) {

					$attr = $manifest->attributes();

					if ((string)$attr['type'] != 'phocapdffonts') {
						$app->enqueueMessage(JText::_('COM_PHOCAPDF_ERROR_NO_FONT_FILE'), 'error');
						return false;
					}

					// Set the manifest object and path
					$this->_manifest =& $manifest;
					$this->setPath('manifest', $file);

					// Set the installation source path to that of the manifest file
					$this->setPath('source', dirname($file));

					return true;
				}
			}

			// None of the xml files found were valid install files
			$app->enqueueMessage(JText::_('COM_PHOCAPDF_ERRORNOTFINDJOOMLAXMLSETUPFILE'), 'error');
			return false;
		} else {
			// No xml files were found in the install folder

			$app->enqueueMessage(JText::_('COM_PHOCAPDF_ERRORNOTFINDJOOMLAXMLSETUPFILE'), 'error');
			return false;
		}
	}


	function parseFiles($element, $cid=0) {

		// Initialize variables
		$copyfiles = array ();

		if (!is_a($element, 'SimpleXMLElement') || !count($element->children())) {
			return 0;// Either the tag does not exist or has no children therefore we return zero files processed.
		}

		$files = $element->children(); // Get the array of file nodes to process

		if (count($files) == 0) {
			return 0; // No files to process
		}

		$source 	 = $this->getPath('source');
		$destination = $this->getPathDest();
		// Process each file in the $files array (children of $tagName).

		/*
		foreach ($files as $file) {
			$path['src']	= $source.'/'.$file->data();
			$path['dest']	= $destination.'/'.$file->data();

			// Add the file to the copyfiles array
			$copyfiles[] = $path;
		}*/

		if (!empty($files->filename)) {
			foreach($files->filename as $fik => $fiv) {
				$path['src']	= $source.'/'.$fiv;
				$path['dest']	= $destination.'/'.$fiv;
				$copyfiles[] = $path;
			}
		}

		return $this->copyFiles($copyfiles);
	}

	public static function unpack($p_filename)
	{
		// Path to the archive
		$archivename = $p_filename;

		// Temporary folder to extract the archive into
		$tmpdir = uniqid('install_');

		// Clean the paths to use for archive extraction
		$extractdir = JPath::clean(dirname($p_filename) . '/' . $tmpdir);
		$archivename = JPath::clean($archivename);

		// Do the unpacking of the archive
		try
		{
            $archive = new \Joomla\Archive\Archive;
            $archive->extract($archivename, $extractdir);
		}
		catch (Exception $e)
		{
			return false;
		}

		/*
		 * Let's set the extraction directory and package file in the result array so we can
		 * cleanup everything properly later on.
		 */
		$retval['extractdir'] = $extractdir;
		$retval['packagefile'] = $archivename;

		/*
		 * Try to find the correct install directory.  In case the package is inside a
		 * subdirectory detect this and set the install directory to the correct path.
		 *
		 * List all the items in the installation directory.  If there is only one, and
		 * it is a folder, then we will set that folder to be the installation folder.
		 */

		 $files		= \JFolder::files ($extractdir, '');
		 $folders	= \JFolder::folders ($extractdir, '');
		 if (!empty($files) && !empty($folders)) {
		 	$dirList = array_merge((array)$files, (array)$folders);
		 } else if (!empty($files)) {
		 	$dirList = $files;
		 } else if (!empty ($folders)) {
		 	$dirList = $folders;
		 }


		if (count($dirList) == 1)
		{
			if (JFolder::exists($extractdir . '/' . $dirList[0]))
			{
				$extractdir = JPath::clean($extractdir . '/' . $dirList[0]);
			}
		}

		/*
		 * We have found the install directory so lets set it and then move on
		 * to detecting the extension type.
		 */
		$retval['dir'] = $extractdir;

		/*
		 * Get the extension type and return the directory/type array on success or
		 * false on fail.
		 */
		$retval['type'] = self::detectType($extractdir);
		if ($retval['type'])
		{
			return $retval;
		}
		else
		{
			return false;
		}
	}

	public static function detectType($p_dir)
	{
		// Search the install dir for an XML file
		$files = JFolder::files($p_dir, '\.xml$', 1, true);

		if (!count($files))
		{
			JLog::add(JText::_('JLIB_INSTALLER_ERROR_NOTFINDXMLSETUPFILE'), JLog::WARNING, '');
			return false;
		}

		if (!empty($files)) {
			foreach ($files as $file)
			{
				$xml = simplexml_load_file($file);

				if (!$xml)
				{
					continue;
				}

				if ($xml->getName() != 'install')
				{
					unset($xml);
					continue;
				}

				$type = (string) $xml->attributes()->type;

				// Free up memory
				unset($xml);
				return $type;
			}
		}

		JLog::add(JText::_('JLIB_INSTALLER_ERROR_NOTFINDJOOMLAXMLSETUPFILE'), JLog::WARNING, '');

		// Free up memory.
		unset($xml);
		return false;
	}

}

?>
