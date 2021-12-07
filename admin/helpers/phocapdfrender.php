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

use Joomla\CMS\Factory;
use Joomla\String\StringHelper;

defined('JPATH_BASE') or die();
use Joomla\CMS\Document\Document;
use Joomla\CMS\Object\CMSObject;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Filesystem\File;
use Joomla\CMS\Plugin\CMSPlugin;
jimport('joomla.filesystem.file');

class PhocaPDFRender extends Document
{

	public static function renderPDF( $document = '', $staticData = array()) {

		// LOADING OF HELPER FILES (extended TCPDF library), LISTENING TO Phoca PDF Plugins
		$option = Factory::getApplication()->input->getCmd('option');
		$t 		= \Joomla\String\StringHelper::ucfirst(str_replace('com_', '', $option));

		// Used abstract class of Phoca PDF (e.g. in VM)
		if ($option == 'com_phocapdf') {
			$type = Factory::getApplication()->input->getCmd('type');
			switch($type) {

				case 'invoice':
				case 'receipt':
				case 'deliverynote':
					$option = 'com_virtuemart';
				break;

			}

		}

		// Static Data - if the function is called as static with defined parameters
		// and the pdf is not rendered as document
		if (isset($staticData['option']) && $staticData['option'] != '') {
			$option 		= $staticData['option'];
			$pdfFileName	= $staticData['filename'];
			$t				= '';
		}

		self::defineConstants();

		switch ($option) {
			case 'com_content':

				$content 	= new CMSObject();
				// Get info from Phoca PDF Content Plugin

				PluginHelper::importPlugin('phocapdf', 'content');
				$results 	= Factory::getApplication()->triggerEvent('onBeforeCreatePDFContent', array (&$content));

				if (File::exists(JPATH_ADMINISTRATOR.'/components/com_phocapdf/helpers/phocapdfcontenttcpdf.php')) {
					require_once(JPATH_ADMINISTRATOR.'/components/com_phocapdf/helpers/phocapdfcontenttcpdf.php');
					$pdf = new PhocaPDFContentTCPDF($content->page_orientation, 'mm', $content->page_format, true, 'UTF-8', $content->use_cache);
				} else {

					throw new Exception('Document cannot be created - Loading of Phoca PDF library (Content) failed', 500);
					return false;

				}
			break;



			case 'com_phocamenu':

				// Get info from Phoca PDF Restaurant Menu Plugin
				$content 	= new CMSObject();

				PluginHelper::importPlugin('phocapdf');

				$results 	= Factory::getApplication()->triggerEvent('onBeforeCreatePDFRestaurantMenu', array (&$content));

				if (File::exists(JPATH_SITE.'/plugins/phocapdf/restaurantmenu/restaurantmenu.php')) {
					require_once(JPATH_SITE.'/plugins/phocapdf/restaurantmenu/restaurantmenu.php');
					$pdf = new PhocaPDFRestaurantMenuTCPDF($content->page_orientation, 'mm', $content->page_format, true, 'UTF-8', $content->use_cache);
				} else {

					throw new Exception('Document cannot be created - Loading of Phoca PDF Plugin (Restaurant Menu) failed', 500);
					return false;
				}
			break;

			case 'com_phocacart':

				// Get info from Phoca PDF Phoca Cart Plugin
				$content 	= new CMSObject();

				PluginHelper::importPlugin('phocapdf');

				$results 	= Factory::getApplication()->triggerEvent('onBeforeCreatePDFPhocaCart', array (&$content, $staticData));

				if (File::exists(JPATH_SITE.'/plugins/phocapdf/phocacart/phocacart.php')) {
					require_once(JPATH_SITE.'/plugins/phocapdf/phocacart/phocacart.php');
					$pdf = new PhocaPDFPhocaCartTCPDF($content->page_orientation, 'mm', $content->page_format, true, 'UTF-8', $content->use_cache);
				} else {

					throw new Exception('Document cannot be created - Loading of Phoca PDF Plugin (Phoca Cart) failed', 500);
					return false;
				}

			break;

			case 'com_virtuemart':

				// Get info from Phoca PDF VirtueMart Plugin
				$content 	= new CMSObject();

				PluginHelper::importPlugin('phocapdf');

				$results 	= Factory::getApplication()->triggerEvent('onBeforeCreatePDFVirtueMart', array (&$content, $staticData));

				if (File::exists(JPATH_SITE.'/plugins/phocapdf/virtuemart/virtuemart.php')) {
					require_once(JPATH_SITE.'/plugins/phocapdf/virtuemart/virtuemart.php');
					$pdf = new PhocaPDFVirtueMartTCPDF($content->page_orientation, 'mm', $content->page_format, true, 'UTF-8', $content->use_cache);
					$pdf->setStaticData($staticData);

				} else {

					throw new Exception('Document cannot be created - Loading of Phoca PDF Plugin (VirtueMart) failed', 500);
					return false;
				}
			break;
			default:

                $content 	= new CMSObject();
			   if (JPluginhelper::isEnabled('phocapdf',strtolower($t))){
				  PluginHelper::importPlugin( 'phocapdf' );
					$results = Factory::getApplication()->triggerEvent('onBeforeCreatePDF'.$t, array(&$content));
					if (File::exists(JPATH_ADMINISTRATOR.'/components/com_phocapdf/helpers/phocapdfcontenttcpdf.php')) {
						require_once(JPATH_ADMINISTRATOR.'/components/com_phocapdf/helpers/phocapdfcontenttcpdf.php');
						$pdf = new PhocaPDFContentTCPDF($content->page_orientation, 'mm', $content->page_format, true, 'UTF-8', $content->use_cache);
						$results = Factory::getApplication()->triggerEvent('onBeforeDisplayPDF'.$t, array (&$pdf, &$content, &$document));
					} else {
						throw new Exception('Document cannot be created - Loading of Phoca PDF library (Content) failed', 500);
						return false;
					}
			   } else {
				  throw new Exception('PDF ERROR', 'Phoca PDF - '.$t.' plugin is disabled or not installed', 500);
				  return false;
			   }
			break;

			/*
			default:
				$dispatcher   = JEventDispatcher::getInstance();
				PluginHelper::importPlugin( 'phocapdf' );
				$results = $dispatcher->trigger('onBeforeDisplayPDF'.$t, array (&$pdf, &$content, &$document));

			break;*/
		}



		$pdf->SetMargins($content->margin_left, $content->margin_top, $content->margin_right);

		$pdf->SetAutoPageBreak(TRUE, $content->margin_bottom);
		$pdf->setCellHeightRatio($content->site_cell_height);
		$pdf->setFont($content->font_type);

		//$siteFontColor = $pdf->convertHTMLColorToDec($content->site_font_color);
		$spotColors = $pdf->getAllSpotColors();
		$siteFontColor = TCPDF_COLORS::convertHTMLColorToDec($content->site_font_color, $spotColors);
		$pdf->SetTextColor($siteFontColor['R'], $siteFontColor['G'], $siteFontColor['B']);

		//$pdf->setPageFormat($content->page_format, $content->page_orientation);
		$pdf->SetHeaderMargin($content->header_margin);
		$pdf->SetFooterMargin($content->footer_margin);
		$pdf->setImageScale($content->image_scale);


		// PDF Metadata
		$pdf->SetCreator(PDF_CREATOR);


		// Content
		switch ($option) {
			case 'com_content':

				$results = Factory::getApplication()->triggerEvent('onBeforeDisplayPDFContent', array (&$pdf, &$content, &$document));

			break;

			case 'com_phocamenu':

				$results = Factory::getApplication()->triggerEvent('onBeforeDisplayPDFRestaurantMenu', array (&$pdf, &$content, &$document));

			break;

			case 'com_phocacart':


				$results = Factory::getApplication()->triggerEvent('onBeforeDisplayPDFPhocaCart', array (&$pdf, &$content, &$document, $staticData));

			break;

			case 'com_virtuemart':

				$results = Factory::getApplication()->triggerEvent('onBeforeDisplayPDFVirtueMart', array (&$pdf, &$content, &$document, $staticData));

			break;
		}

		// If there is a file, store it to server
		// Case for VM sending invoice - the file is stored on server and sent
		// Not case for Phoca Cart sending invoide as the file is not stored on server, buffer from file is added directly to attachment:
		// instead of using mail function: administrator\components\com_phocacart\libraries\phocacart\email\email.php
		// AddAttachment --> addStringAttachment (file on server vs buffer output) - Joomla! Mail function
		// does not know to work with addStringAttachment, so we needed to overwrite some methods in Joomla! Mail function
		// AddAttachment and addStringAttachment are methods from PHP Mailer
		if (isset($staticData['file']) && $staticData['file'] != '') {

			$pdf->Output($staticData['file'], 'F');
			return true;

		}


		// Called from administrator area (administrator calls frontend view, but it still administrator area)
		$adminView	= Factory::getApplication()->input->get('admin', 0, 'int');
		if ($adminView == 1) {
			$content->pdf_destination = 'S';
		}

		if (isset($staticData['pdf_destination']) && $staticData['pdf_destination'] != '') {
			$content->pdf_destination = $staticData['pdf_destination'];
		}


		if ($content->pdf_destination == 'D' || $content->pdf_destination == 'I') {
			$pdf->Output($content->pdf_name, $content->pdf_destination);
			return true;
		}

		// If e.g. "S" then the output is sent back and not rendered
		// This is what we need when sending PDF as addStringAttachment (we need PDF content only)
		$data = $pdf->Output($content->pdf_name, $content->pdf_destination);


		return $data;

	}


	/*
	 * Specific method to render PDF divided to two methods because in some cases
	 * we need $pdf instance (TCPDF) when creating HTML output, for example:
	 * $params = $pdf->serializeTCPDFtagParameters(...);
	 * we don't manipulate or update $pdf instance, we only need it to use some specific non static methods
	 * so we add the $pdf instance to place where html will be produced and return it unchanged back
	 * $pdf - defined in function which produces PDF -> sent here to initialize TCPDF ($pdf) -> send back to function which produces PDF
	 * -> this function will call output where the $pdf instance will be used -> and then $pdf will be added to renderInitializedPDF funtion
	 * EXAMPLE
	 * administrator/components/com_phocacart/libraries/phocacart/order/status.php initializePDF()
	 * administrator/components/com_phocapdf/helpers/phocapdfrender.php
	 * administrator/components/com_phocacart/libraries/phocacart/order/status.php $layoutG->render()
	 * components/com_phocacart/layouts/gift_voucher.php
	 * administrator/components/com_phocacart/libraries/phocacart/order/status.php renderInitializedPDF()
	 * administrator/components/com_phocapdf/helpers/phocapdfrender.php
	 */
	public static function initializePDF(&$pdf, &$content, &$document, $staticData) {

        self::defineConstants();

        PluginHelper::importPlugin('phocapdf');
        $results = Factory::getApplication()->triggerEvent('onBeforeCreatePDFPhocaCart', array(&$content, $staticData));

        if (File::exists(JPATH_SITE . '/plugins/phocapdf/phocacart/phocacart.php')) {
            require_once(JPATH_SITE . '/plugins/phocapdf/phocacart/phocacart.php');
            $pdf = new PhocaPDFPhocaCartTCPDF($content->page_orientation, 'mm', $content->page_format, true, 'UTF-8', $content->use_cache);
        } else {
            throw new Exception('Document cannot be created - Loading of Phoca PDF Plugin (Phoca Cart) failed', 500);
            return false;
        }

        $pdf->SetMargins($content->margin_left, $content->margin_top, $content->margin_right);

        $pdf->SetAutoPageBreak(true, $content->margin_bottom);
        $pdf->setCellHeightRatio($content->site_cell_height);
        $pdf->setFont($content->font_type);
        //$siteFontColor    = $pdf->convertHTMLColorToDec($content->site_font_color);
        $spotColors    = $pdf->getAllSpotColors();
        $siteFontColor = TCPDF_COLORS::convertHTMLColorToDec($content->site_font_color, $spotColors);
        $pdf->SetTextColor($siteFontColor['R'], $siteFontColor['G'], $siteFontColor['B']);
        //$pdf->setPageFormat($content->page_format, $content->page_orientation);
        $pdf->SetHeaderMargin($content->header_margin);
        $pdf->SetFooterMargin($content->footer_margin);
        $pdf->setImageScale($content->image_scale);

       // $pdf->setRasterizeVectorImages(true);

        // PDF Metadata
        $pdf->SetCreator(PDF_CREATOR);

        $tagvs = array(
			'div' => array(0 => array('h' => 0, 'n' => 0), 1 => array('h' => 0, 'n' => 0)),
			'h1' => array(0 => array('h' => 0, 'n' => 0), 1 => array('h' => 0, 'n' => 0)),
			'h2' => array(0 => array('h' => 0, 'n' => 0), 1 => array('h' => 0, 'n' => 0))
		);
		$pdf->setHtmlVSpace($tagvs);

        return true;
    }

    public static function renderInitializedPDF(&$pdf, &$content, &$document, $staticData) {







	    $results = Factory::getApplication()->triggerEvent('onBeforeDisplayPDFPhocaCart', array (&$pdf, &$content, &$document, $staticData));

		// If there is a file, store it to server
		// Case for VM sending invoice - the file is stored on server and sent
		// Not case for Phoca Cart sending invoide as the file is not stored on server, buffer from file is added directly to attachment:
		// instead of using mail function: administrator\components\com_phocacart\libraries\phocacart\email\email.php
		// AddAttachment --> addStringAttachment (file on server vs buffer output) - Joomla! Mail function
		// does not know to work with addStringAttachment, so we needed to overwrite some methods in Joomla! Mail function
		// AddAttachment and addStringAttachment are methods from PHP Mailer
		if (isset($staticData['file']) && $staticData['file'] != '') {
			$pdf->Output($staticData['file'], 'F');
			return true;
		}

		// Called from administrator area (administrator calls frontend view, but it still administrator area)
		$adminView	= Factory::getApplication()->input->get('admin', 0, 'int');
		if ($adminView == 1) {
			$content->pdf_destination = 'S';
		}

		if (isset($staticData['pdf_destination']) && $staticData['pdf_destination'] != '') {
			$content->pdf_destination = $staticData['pdf_destination'];
		}

		if ($content->pdf_destination == 'D' || $content->pdf_destination == 'I') {
			$pdf->Output($content->pdf_name, $content->pdf_destination);
			return true;
		}

		// If e.g. "S" then the output is sent back and not rendered
		// This is what we need when sending PDF as addStringAttachment (we need PDF content only)
		$data = $pdf->Output($content->pdf_name, $content->pdf_destination);


		return $data;

	}

	public static function defineConstants() {
	    if(!defined('K_TCPDF_EXTERNAL_CONFIG'))	{define('K_TCPDF_EXTERNAL_CONFIG', true);}
		if(!defined('K_PATH_MAIN'))	            {define("K_PATH_MAIN", JPATH_ADMINISTRATOR.'/components/com_phocapdf/assets/tcpdf');}
		if(!defined('K_PATH_URL'))				{define("K_PATH_URL", JPATH_BASE);}// URL path
		if(!defined('K_PATH_FONTS'))	        {define("K_PATH_FONTS", JPATH_ADMINISTRATOR.'/components/com_phocapdf/fonts/');}
		if(!defined('K_PATH_CACHE'))			{define("K_PATH_CACHE", K_PATH_MAIN.'/cache/');}// Cache directory path
		$urlPath = JUri::base(true) . '/administrator/components/com_phocapdf/assets/tcpdf/';// Cache URL path
		if(!defined('K_PATH_URL_CACHE'))		{define("K_PATH_URL_CACHE", $urlPath.'cache/');}
		if(!defined('K_PATH_IMAGES'))			{define("K_PATH_IMAGES", K_PATH_MAIN.'/images/');}// Images path
		if(!defined('K_BLANK_IMAGE'))			{define("K_BLANK_IMAGE", K_PATH_IMAGES."/_blank.png");}// Blank image path
		if(!defined('K_CELL_HEIGHT_RATIO'))		{define("K_CELL_HEIGHT_RATIO", 1.25);}// Cell height ratio
		if(!defined('K_TITLE_MAGNIFICATION'))	{define("K_TITLE_MAGNIFICATION", 1.3);}// Magnification scale for titles
		if(!defined('K_SMALL_RATIO'))			{define("K_SMALL_RATIO", 2/3);}// Reduction scale for small font
		if(!defined('HEAD_MAGNIFICATION'))		{define("HEAD_MAGNIFICATION", 1.1);}// Magnication scale for head
		if(!defined('PDF_PAGE_FORMAT'))			{define('PDF_PAGE_FORMAT', 'A4');}// page format
		if(!defined('PDF_PAGE_ORIENTATION'))	{define('PDF_PAGE_ORIENTATION', 'P');}// page orientation (P=portrait, L=landscape)
		if(!defined('PDF_CREATOR'))				{define('PDF_CREATOR', 'Phoca PDF');}// document creator
		if(!defined('PDF_AUTHOR'))				{define('PDF_AUTHOR', 'Phoca PDF');}// document author
		if(!defined('PDF_HEADER_TITLE'))		{define('PDF_HEADER_TITLE', 'Phoca PDF');}// header title
		if(!defined('PDF_HEADER_STRING'))		{define('PDF_HEADER_STRING', "Phoca PDF");}// header description string
		//define('PDF_HEADER_LOGO', 'tcpdf_logo.jpg');// image logo
		//define('PDF_HEADER_LOGO_WIDTH', 30);// header logo image width [mm]
		if(!defined('PDF_UNIT'))	            {define('PDF_UNIT', 'mm');}// document unit of measure [pt=point, mm=millimeter, cm=centimeter, in=inch]
		if(!defined('PDF_header_margin'))		{define('PDF_header_margin', 10);}// header margin
		if(!defined('PDF_footer_margin'))		{define('PDF_footer_margin', 10);}// footer margin
		if(!defined('PDF_MARGIN_TOP'))			{define('PDF_MARGIN_TOP', 33);}// top margin
		if(!defined('PDF_MARGIN_BOTTOM'))		{define('PDF_MARGIN_BOTTOM', 25);}// bottom margin
		if(!defined('PDF_MARGIN_LEFT'))			{define('PDF_MARGIN_LEFT', 15);}// left margin
		if(!defined('PDF_MARGIN_RIGHT'))		{define('PDF_MARGIN_RIGHT', 15);}// right margin
		if(!defined('PDF_FONT_NAME_MAIN'))		{define('PDF_FONT_NAME_MAIN', 'helvetica');}// main font name
		if(!defined('PDF_FONT_SIZE_MAIN'))		{define('PDF_FONT_SIZE_MAIN', 10);}// main font size
		if(!defined('PDF_FONT_NAME_DATA'))		{define('PDF_FONT_NAME_DATA', 'helvetica');}// data font name
		if(!defined('PDF_FONT_SIZE_DATA'))		{define('PDF_FONT_SIZE_DATA', 8);}// data font size
		if(!defined('PDF_IMAGE_SCALE_RATIO'))	{define('PDF_IMAGE_SCALE_RATIO', 4);}// Ratio used to scale the images
		if(!defined('K_TCPDF_CALLS_IN_HTML'))	{define('K_TCPDF_CALLS_IN_HTML', true);}
    }
}
