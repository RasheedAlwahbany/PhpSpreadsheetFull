<?php

require_once 'Header.php';
require '../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$columns = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K','L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z');
if($connection){
    $query = $connection->prepare("SELECT * FROM `cities`");
    if($query->execute()){
        $is_header = true;
        $row_index = 1;
        while( $row = $query->fetchObject()){
        //     // $row_index = $sheet->getHighestRow()+1;
            $sheet->insertNewRowBefore($row_index);
            $i=0;
            foreach( $row as $key => $value ){
                if($is_header){
                    $sheet->setCellValue($columns[$i].''.$row_index, $key);
                }else{
                    $sheet->setCellValue($columns[$i].''.$row_index, $value);
                }
                $i=$i+1;
            }
            $is_header=false;
            $row_index = $row_index + 1;

        }
    }
}
$writer = new Xlsx($spreadsheet);
$writer->save('Cities.xlsx');

