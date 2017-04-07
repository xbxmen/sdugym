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

/** PHPExcel_Cell_DataType */
require_once 'PHPExcel/Cell/DataType.php';


/**
 * PHPExcel_Cell_DefaultValueBinder
 *
 * @category   PHPExcel
 * @package    PHPExcel_Cell
 * @copyright  Copyright (c) 2006 - 2009 PHPExcel (http://www.codeplex.com/PHPExcel)
 */
class PHPExcel_Cell_DefaultValueBinder implements PHPExcel_Cell_IValueBinder
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
		// Set value explicit
		$cell->setValueExplicit( $value, PHPExcel_Cell_DataType::dataTypeForValue($value) );
		
		// Done!
		return true;
	}
	
	/**
	 * DataType for value
	 *
	 * @param	mixed 	$pValue
	 * @return 	int
	 */
	public static function dataTypeForValue($pValue = null) {
		// Match the value against a few data types
		if (is_null($pValue)) {
			return PHPExcel_Cell_DataType::TYPE_NULL;
		} elseif ($pValue === '') {
			return PHPExcel_Cell_DataType::TYPE_STRING;
		} elseif ($pValue instanceof PHPExcel_RichText) {
			return PHPExcel_Cell_DataType::TYPE_STRING;
		} elseif ($pValue{0} === '=') {
			return PHPExcel_Cell_DataType::TYPE_FORMULA;
		} elseif (is_bool($pValue)) {
			return PHPExcel_Cell_DataType::TYPE_BOOL;
		} elseif (preg_match('/^\-?[0-9]*\.?[0-9]*$/', $pValue)) {
			return PHPExcel_Cell_DataType::TYPE_NUMERIC;
		} elseif (array_key_exists($pValue, PHPExcel_Cell_DataType::getErrorCodes())) {
			return PHPExcel_Cell_DataType::TYPE_ERROR;
		} else {
			return PHPExcel_Cell_DataType::TYPE_STRING;
		}
	}
}
