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
        while( $row = $query->fetchObject()){
            $row_index = $sheet->getHighestRow()+1;
            $sheet->insertNewRowBefore($row_index);
            
            foreach( $row as $key => $value ){
                if($is_header)
                $sheet->setCellValue($columns[$row_index].''.$row_index, $value);
            }
        }
    }
}
$writer = new Xlsx($spreadsheet);
$writer->save('Cities.xlsx');
