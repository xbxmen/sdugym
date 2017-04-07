<?php
/**
 * PHPExcel
 *
 * Copyright (c) 2006 - 2009 PHPExcel
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 * 
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 * 
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA
 *
 * @category   PHPExcel
 * @package    PHPExcel_Shared
 * @copyright  Copyright (c) 2006 - 2009 PHPExcel (http://www.codeplex.com/PHPExcel)
 * @license    http://www.gnu.org/licenses/old-licenses/lgpl-2.1.txt	LGPL
 * @version    1.6.6, 2009-03-02
 */


/**
 * PHPExcel_Shared_Font
 *
 * @category   PHPExcel
 * @package    PHPExcel_Shared
 * @copyright  Copyright (c) 2006 - 2009 PHPExcel (http://www.codeplex.com/PHPExcel)
 */
class PHPExcel_Shared_Font
{
	/**
	 * Calculate an (approximate) OpenXML column width, based on font size and text contained
	 *
	 * @param 	int		$fontSize			Font size (in pixels or points)
	 * @param 	bool	$fontSizeInPixels	Is the font size specified in pixels (true) or in points (false) ?
	 * @param 	string	$columnText			Text to calculate width
	 * @param 	int		$rotation			Rotation angle
	 * @return 	int		Column width
	 */
	public static function calculateColumnWidth($fontSize = 9, $fontSizeInPixels = false, $columnText = '', $rotation = 0) {
		if (!$fontSizeInPixels) {
			// Translate points size to pixel size
			$fontSize = PHPExcel_Shared_Font::fontSizeToPixels($fontSize);
		}
		
		// If it is rich text, use rich text...
		if ($columnText instanceof PHPExcel_RichText) {
			$columnText = $columnText->getPlainText();
		}
		
		// Only measure the part before the first newline character
		if (strpos($columnText, "\r") !== false) {
			$columnText = substr($columnText, 0, strpos($columnText, "\r"));
		}
		if (strpos($columnText, "\n") !== false) {
			$columnText = substr($columnText, 0, strpos($columnText, "\n"));
		}
		
		// Calculate column width
		$columnWidth = ((strlen($columnText) * $fontSize + 5) / $fontSize * 256 ) / 256;

		// Calculate approximate rotated column width
		if ($rotation !== 0) {
			if ($rotation == -165) {
				// stacked text
				$columnWidth = 4; // approximation
			} else {
				// rotated text
				$columnWidth = $columnWidth * cos(deg2rad($rotation))
								+ $fontSize * abs(sin(deg2rad($rotation))) / 5; // approximation
			}
		}

		// Return
		return round($columnWidth, 6);
	}
	
	/**
	 * Calculate an (approximate) pixel size, based on a font points size
	 *
	 * @param 	int		$fontSizeInPoints	Font size (in points)
	 * @return 	int		Font size (in pixels)
	 */
	public static function fontSizeToPixels($fontSizeInPoints = 12) {
		return ((16 / 12) * $fontSizeInPoints);
	}
	
	/**
	 * Calculate an (approximate) pixel size, based on inch size
	 *
	 * @param 	int		$sizeInInch	Font size (in inch)
	 * @return 	int		Size (in pixels)
	 */
	public static function inchSizeToPixels($sizeInInch = 1) {
		return ($sizeInInch * 96);
	}
	
	/**
	 * Calculate an (approximate) pixel size, based on centimeter size
	 *
	 * @param 	int		$sizeInCm	Font size (in centimeters)
	 * @return 	int		Size (in pixels)
	 */
	public static function centimeterSizeToPixels($sizeInCm = 1) {
		return ($sizeInCm * 37.795275591);
	}
}
