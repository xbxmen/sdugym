<?php
header("Content-Type: text/html; charset=UTF-8");
set_include_path(get_include_path() . PATH_SEPARATOR . './');
include 'PHPExcel/IOFactory.php';
date_default_timezone_set('Asia/shanghai');
$inputFileType = 'Excel2007';
$sheetname = 'Data Sheet #1';
$inputFileName = "qwe.xlsx";
$input = array();
if(true){
        /**  Create a new Reader of the type defined in $inputFileType  **/
        $objReader = PHPExcel_IOFactory::createReader($inputFileType);
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

        var_dump($input);
        var_dump(count($input));
}else{
    $response['statue'] = -1;
    $con->for_close();
    echo json_encode($response);
    exit ;
}

function change($word){
    $res = "";
    switch ($word){
        case "A":
            $res = "date";
            break;
        case 'B':
            $res = "week";
            break;
        case 'C':
            $res = "one";
            break;
        case "D":
            $res = "two";
            break;
        case 'E':
            $res = "three";
            break;
        case 'F':
            $res = "four";
            break;
        case "G":
            $res = "five";
            break;
        case 'H':
            $res = "six";
            break;
        case 'I':
            $res = "seven";
            break;
        case "J":
            $res = "eight";
            break;
        case 'K':
            $res = "nine";
            break;
        case 'L':
            $res = "ten";
            break;
        case 'M':
            $res = "eleven";
            break;
        case "N":
            $res = "stadium";
            break;
        case 'O':
            $res = "gym";
            break;
        case 'P':
            $res = "campus";
            break;
    }
    return $res;
}
