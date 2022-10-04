<?php

require_once 'Header.php';
require '../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\IReader;

// $reader = IOFactory::load('DataBackup/'.'cities.xls',IReader::LOAD_WITH_CHARTS);
$reader = new Xlsx();
$reader->setReadDataOnly(true);
$spreadsheet = $reader->load("DataBackup/"."cities.xlsx");
print_r($spreadsheet);



