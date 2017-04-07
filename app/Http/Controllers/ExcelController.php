<?php

namespace App\Http\Controllers;


//include '/PHPExcel/IOFactory.php';
use IOFactory;

class ExcelController{

    private $basePath = "schedules/";
    private $inputFileType = 'Excel2007';
    private $sheetname = 'Data Sheet #1';

    public function getContent($filename){
        $input = array();
        $inputFileName = $this->basePath.$filename;

        /**  Create a new Reader of the type defined in $inputFileType  **/
        $objReader = IOFactory::createReader($this->inputFileType);
        /**  Define how many rows we want to read for each "chunk"  **/
        $chunkSize = 20;
        /**  Create a new Instance of our Read Filter  **/

        /**  Loop to read our worksheet in "chunk size" blocks  **/
        $objPHPExcel = $objReader->load($inputFileName);
        //	Do some processing here
        $objWorksheet = $objPHPExcel->getActiveSheet();
        foreach($objWorksheet->getRowIterator() as $row){
            $cellIterator = $row->getCellIterator();
            $cellIterator->setIterateOnlyExistingCells(false);

            $row = array();
            foreach($cellIterator as $cell){
                if($cell->getValue() != "date"){
                    if($cell->getColumn() == "A"){
                        $row[change($cell->getColumn())] = date("Y-m-d",PHPExcel_Shared_Date::ExcelToPHP($cell->getValue()));
                    }else{
                        $row[change($cell->getColumn())] = $cell->getValue();
                    }
                }else{
                    break;
                }
            }
            if(!empty($row)){
                array_push($input,$row);
            }
        }
        return $input;
        /*var_dump($input);
        var_dump(count($input));*/
    }
}