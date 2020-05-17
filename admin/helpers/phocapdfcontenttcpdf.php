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
jimport( 'joomla.html.parameter' );
defined('_JEXEC') or die();
// Phoca PDF TCPDF
require_once(JPATH_ADMINISTRATOR.'/components/com_phocapdf/helpers/phocapdf.php');
require_once(JPATH_ADMINISTRATOR.'/components/com_phocapdf/assets/tcpdf/tcpdf.php');

class PhocaPDFContentTCPDF extends TCPDF
{
	protected $pluginP = null;

	private function getPluginParameters() {
		if (empty($this->pluginP)) {


			$plugin 	= JPluginHelper::getPlugin('phocapdf', 'content');
			$this->pluginP 	= new JRegistry();
			$this->pluginP->loadString($plugin->params);
		}
		return $this->pluginP;
	}

	public function Header() {

		$pluginP	= $this->getPluginParameters();
		$ormargins 	= $this->getOriginalMargins();
		$headerfont = $this->getHeaderFont();
		$headerdata = $this->getHeaderData();

		// Params
		$params						= array();
		$params['header_display_line']	= $pluginP->get('header_display_line', 1);
		$params['header_display']		= $pluginP->get('header_display', 1);
		$spotColors = $this->getAllSpotColors();
		$params['header_font_color']	= TCPDF_COLORS::convertHTMLColorToDec($pluginP->get('header_font_color', '#000000'), $spotColors);
		$params['header_line_color']	= TCPDF_COLORS::convertHTMLColorToDec($pluginP->get('header_line_color', '#000000'), $spotColors);
		$params['header_bg_color']		= $pluginP->get('header_bg_color', '');
		$params['header_data']			= $pluginP->get('header_data', '');
		$params['header_data_align']	= $pluginP->get('header_data_align', 'L');
		$params['header_cell_height']	= $pluginP->get('header_cell_height', 1.2);

		//Extra values
		if ((int)$params['header_cell_height'] > 3) {
			$params['header_cell_height'] = 3;
		}

		$currentCHRH = $this->getCellHeightRatio();
		$this->setCellHeightRatio($params['header_cell_height']);

		$isHTML = false;
		if ($params['header_data'] != '') {
			$isHTML = true;
		}

		if ($params['header_display'] == 1) {
			if (($headerdata['logo']) AND ($headerdata['logo'] != K_BLANK_IMAGE)) {
				$this->Image(K_PATH_IMAGES.$headerdata['logo'], $this->GetX(), $this->getHeaderMargin(), $headerdata['logo_width']);
				$imgy = $this->getImageRBY();
			} else {
				$imgy = $this->GetY();
			}
			$cell_height = round(($this->getCellHeightRatio() * $headerfont[2]) / $this->getScaleFactor(), 2);
			// set starting margin for text data cell
			if ($this->getRTL()) {
				$header_x = $ormargins['right'] + ($headerdata['logo_width'] * 1.1);
			} else {
				$header_x = $ormargins['left'] + ($headerdata['logo_width'] * 1.1);
			}
			$this->SetTextColor($params['header_font_color']['R'], $params['header_font_color']['G'], $params['header_font_color']['B']);
			// header title
			$this->SetFont($headerfont[0], 'B', $headerfont[2] + 1);
			$this->SetX($header_x);
			$this->Cell(0, $cell_height, $headerdata['title'], 0, 1, '', 0, '', 0);
			// header string
			$this->SetFont($headerfont[0], $headerfont[1], $headerfont[2]);
			$this->SetX($header_x);

			$fill = 0;
			if ($params['header_bg_color'] != '') {
				$spotColors = $this->getAllSpotColors();
				$fillColor = TCPDF_COLORS::convertHTMLColorToDec($params['header_bg_color'], $spotColors);
				$this->SetFillColorArray(array($fillColor['R'],$fillColor['G'],$fillColor['B']));
				$fill = 1;

			}
			$this->MultiCell(0, $cell_height, $headerdata['string'], 0, $params['header_data_align'], $fill, 1, '', '', true, 0, $isHTML);
			// print an ending header line
			$border = 0;
			if ((int)$params['header_display_line'] == 1) {
				$this->SetLineStyle(array('width' => 0.85 / $this->getScaleFactor(), 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array($params['header_line_color']['R'], $params['header_line_color']['G'], $params['header_line_color']['B'])));
				$this->SetY((2.835 / $this->getScaleFactor()) + max($imgy, $this->GetY()));
				$border = 'T';
			}
			if ($this->getRTL()) {
				$this->SetX($ormargins['right']);
			} else {
				$this->SetX($ormargins['left']);
			}
			$this->Cell(0, 0, '', $border, 0, 'C');
		}
		// Set it back
		$this->setCellHeightRatio($currentCHRH);

	}

	public function Footer() {

			$footerfont = $this->getFooterFont();

			$pluginP	= $this->getPluginParameters();
			// Params
			$params								= array();
			$params['footer_display_line']		= $pluginP->get('footer_display_line', 1);
			$spotColors = $this->getAllSpotColors();
			$params['footer_font_color']		= TCPDF_COLORS::convertHTMLColorToDec($pluginP->get('footer_font_color', '#000000'), $spotColors);
			$params['footer_line_color']		= TCPDF_COLORS::convertHTMLColorToDec($pluginP->get('footer_line_color', '#000000'), $spotColors);
			$params['footer_bg_color']			= $pluginP->get('footer_bg_color', '');
			$params['footer_display']			= $pluginP->get('footer_display', 1);
			$params['footer_data']				= $pluginP->get('footer_data', '');
			$params['footer_display_pagination']= $pluginP->get('footer_display_pagination', 1);
			$params['footer_data_align']		= $pluginP->get('footer_data_align', 'R');
			$params['footer_margin']			= $pluginP->get('footer_margin', 15);
			$params['footer_cell_height']		= $pluginP->get('footer_cell_height', 1.2);

			//Extra values
			if ((int)$params['footer_cell_height'] > 3) {
				$params['footer_cell_height'] = 3;
			}

			if ((int)$params['footer_margin'] > 50) {
				$params['footer_margin'] = 50;
			}

			$currentCHRF = $this->getCellHeightRatio();
			$this->setCellHeightRatio($params['footer_cell_height']);

			$isHTML = false;
			if ($params['footer_data'] != '') {
				//$params['footer_data'] = str_replace(utf8_encode("<p>�</p>"), '<p></p>', $params['footer_data']);
				$params['footer_data'] = str_replace(array(utf8_encode(chr(11)), utf8_encode(chr(160))), ' ', $params['footer_data']);
				$isHTML = true;
			}

			if ($params['footer_display'] == 1) {
				$cur_y = $this->GetY();
				$ormargins = $this->getOriginalMargins();
				$this->SetTextColor($params['footer_font_color']['R'], $params['footer_font_color']['G'], $params['footer_font_color']['B']);
				//set style for cell border
				$border = 0;
				if ((int)$params['footer_display_line'] == 1) {
					$line_width = 0.85 / $this->getScaleFactor();
					$this->SetLineStyle(array('width' => $line_width, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array($params['footer_line_color']['R'], $params['footer_line_color']['G'], $params['footer_line_color']['B'])));
					$border = 'T';
				}

				//print document barcode
				$barcode = $this->getBarcode();
				if (!empty($barcode)) {
					$this->Ln($line_width);
					$barcode_width = round(($this->getPageWidth() - $ormargins['left'] - $ormargins['right'])/3);
					$this->write1DBarcode($barcode, 'C128B', $this->GetX(), $cur_y + $line_width, $barcode_width, (($this->getFooterMargin() / 3) - $line_width), 0.3, '', '');
				}

				$wPage = isset($this->l['w_page']) ? $this->l['w_page'] : '';
				if (empty($this->pagegroups)) {
					$pagenumtxt = $wPage.' '.$this->getAliasNumPage().' / '.$this->getAliasNbPages();
				} else {
					$pagenumtxt = $wPage.' '.$this->getPageNumGroupAlias().' / '.$this->getPageGroupAlias();
				}
				$this->SetY($cur_y);

				/*
				 * Specific Plugin code for Footer
				 * Header is set here in system plugin (Phoca PDF Content Plugin) because we need title and header data
				 * Footer is set in helper of system plugin (Phoca PDF Component) because we need TCPDF data (pagination)
				 */
				// Pagination
				if ($params['footer_display_pagination'] == 0) {
					$pagenumtxt = '';
				}
				// Footer Data
				if ($params['footer_data'] != '') {
					$footertxt = str_replace('{phocapdfpagination}', $pagenumtxt, $params['footer_data']);

				} else {
					$footertxt = $pagenumtxt;
				}

				$cell_height = round(($this->getCellHeightRatio() * $footerfont[2]) / $this->getScaleFactor(), 2);

				$this->SetFont($footerfont[0], $footerfont[1], $footerfont[2]);
				if ($this->getRTL()) {
					$this->SetX($ormargins['right']);
				} else {
					$this->SetX($ormargins['left']);
				}

				$fill = 0;
				if ($params['footer_bg_color'] != '') {
					$spotColors = $this->getAllSpotColors();
					$fillColor = TCPDF_COLORS::convertHTMLColorToDec($params['footer_bg_color'], $spotColors);
					$this->SetFillColorArray(array($fillColor['R'],$fillColor['G'],$fillColor['B']));
					$fill = 1;

				}

				$this->MultiCell(0, $cell_height, $footertxt, $border, $params['footer_data_align'], $fill, 1, '', '', true, 0, $isHTML);

			}

			$posY	= $this->getPageHeight() -10;
			$this->writeHTMLCell(0, 0, 0, $posY, PhocaPDFCell::setCell(1), 0, 0, 0,true, 'R');

			// Set it back
			$this->setCellHeightRatio($currentCHRF);

		}

}
?>
