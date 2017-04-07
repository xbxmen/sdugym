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
 * @package    PHPExcel_Cell
 * @copyright  Copyright (c) 2006 - 2009 PHPExcel (http://www.codeplex.com/PHPExcel)
 * @license    http://www.gnu.org/licenses/old-licenses/lgpl-2.1.txt	LGPL
 * @version    1.6.6, 2009-03-02
 */


/** PHPExcel_Cell */
require_once 'PHPExcel/Cell.php';

/** PHPExcel_Cell_IValueBinder */
require_once 'PHPExcel/Cell/IValueBinder.php';

/** PHPExcel_Cell_DefaultValueBinder */
require_once 'PHPExcel/Cell/DefaultValueBinder.php';

/** PHPExcel_Style_NumberFormat */
require_once 'PHPExcel/Style/NumberFormat.php';

/** PHPExcel_Shared_Date */
require_once 'PHPExcel/Shared/Date.php';


/**
 * PHPExcel_Cell_AdvancedValueBinder
 *
 * @category   PHPExcel
 * @package    PHPExcel_Cell
 * @copyright  Copyright (c) 2006 - 2009 PHPExcel (http://www.codeplex.com/PHPExcel)
 */
class PHPExcel_Cell_AdvancedValueBinder extends PHPExcel_Cell_DefaultValueBinder implements PHPExcel_Cell_IValueBinder
{
	/**
	 * Bind value to a cell
	 *
	 * @param PHPExcel_Cell $cell	Cell to bind value to
	 * @param mixed $value			Value to bind in cell
	 * @return boolean
	 */
	public function bindValue(PHPExcel_Cell $cell, $value = null)
	{
		// Find out data type
		$dataType = parent::dataTypeForValue($value);
		
		// Style logic - strings
		if ($dataType === PHPExcel_Cell_DataType::TYPE_STRING && !$value instanceof PHPExcel_RichText) {
			// Check for percentage
			if (preg_match('/^\-?[0-9]*\.?[0-9]*\s?\%$/', $value)) {
				// Convert value to number
				$cell->setValueExplicit( (float)str_replace('%', '', $value) / 100, PHPExcel_Cell_DataType::TYPE_NUMERIC);
				
				// Set style
				$cell->getParent()->getStyle( $cell->getCoordinate() )->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE );
				
				return true;
			}
			
			// Check for date
			if (strtotime($value) !== false) {
				// Convert value to Excel date
				$cell->setValueExplicit( PHPExcel_Shared_Date::PHPToExcel(strtotime($value)), PHPExcel_Cell_DataType::TYPE_NUMERIC);
				
				// Set style
				$cell->getParent()->getStyle( $cell->getCoordinate() )->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_DATE_YYYYMMDD2 );
				
				return true;
			}
		}
		
		// Style logic - Numbers
		if ($dataType === PHPExcel_Cell_DataType::TYPE_NUMERIC) {
			// Leading zeroes?
			if (preg_match('/^\-?[0]+[0-9]*\.?[0-9]*$/', $value)) {
				// Convert value to string
				$cell->setValueExplicit( $value, PHPExcel_Cell_DataType::TYPE_STRING);
				
				// Set style
				$cell->getParent()->getStyle( $cell->getCoordinate() )->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_TEXT );
				
				return true;
			}
		}
		
		// Not bound yet? Use parent...
		return parent::bindValue($cell, $value);
	}
}
