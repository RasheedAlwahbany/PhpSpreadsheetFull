<?php

require_once 'Header.php';
require '../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$err="";
$columns = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z');
function ExportData($table)
{
    global $connection;
    global $columns;
    global $err;
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    $writer = new Xlsx($spreadsheet);
    if ($connection) {
        $query = $connection->prepare("SELECT * FROM " . $table);
        if ($query->execute()) {
            $is_header = true;
            $row_index = 1;
            while ($row = $query->fetchObject()) {
                $sheet->insertNewRowBefore($row_index);
                $i = 0;
                foreach ($row as $key => $value) {
                    if ($i <=25)
                        $col = "";
                    else if($i>25){
                        $col = 'A';
                        $i=0;
                    }else if($i > 51){
                        $col = 'B';
                        $i=0;
                    }else if($i > 77){
                        $col = 'C';
                        $i=0;
                    }else if($i > 103){
                        $col = 'D';
                        $i=0;
                    }else{
                        $col = 'E';
                        $i=0;
                    }    
                    $col = $col.$columns[$i];
                    echo $col;
                    if ($is_header) {
                        $sheet->setCellValue($col . '' . $row_index, $key);
                    } else {
                        $sheet->setCellValue($col . '' . $row_index, $value);
                    }
                    $i = $i + 1;
                }
                $is_header = false;
                $row_index = $row_index + 1;
            }
            if(!empty($sheet->getCoordinates())){
            if (is_file('DataBackup/'.$table . '.xlsx')) {
                $i = 0;
                while (is_file('DataBackup/'.$table .$i. '.xlsx')){
                    $i = $i + 1;
                    print('DataBackup/'.$table .$i. '.xlsx');
                }
                $writer->save('DataBackup/'.$table . $i . '.xlsx');
            } else
                $writer->save('DataBackup/'.$table . '.xlsx');
        }else{
            $err="Error";
            echo "<br/><br/>This is $table is Blank. <br/><br/>";
        }
        }
    }
}

    if (!empty($_GET['Controller'])) {
        if ($_GET['Controller'] != "All") {
            // if(!str_contains($row->Tables_in_maintenances_supervisor_dbms, "view"))
                ExportData($_GET['Controller']);
            
        } else {
            $query = $connection->prepare("SHOW TABLES FROM `maintenances_supervisor_dbms` ");
            if ($query->execute()) {
                while ($row = $query->fetchObject()) {
                    // if(!str_contains($row->Tables_in_maintenances_supervisor_dbms, "view"))
                        ExportData($row->Tables_in_maintenances_supervisor_dbms);
                }
            }
        }
        if(empty($err))
            header('Location: http://localhost:8000/export_data.php?');
    }

?>
<div class="container">
    <div class="navbar navbar-default" role="navigation">
        <div class="container-fluid">
            <ul class="nav nav-tabs">

                <li class="nav-item active">
                    <div class="dropdown">
                        <button class="btn btn-secondary dropdown-toggle" onclick="checkDropDown();" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                            Create custome backup
                        </button>
                        <ul id="dropdownMenuButtonMenu">
                            <?php
                            if ($connection) {
                                $query = $connection->prepare(" SHOW TABLES FROM `maintenances_supervisor_dbms` ");
                                if ($query->execute()) {
                                    $i=1;
                                    while ($row = $query->fetchObject()) { ?>
                                        <li><a class="dropdown-item" href="?Controller=<?php echo $row->Tables_in_maintenances_supervisor_dbms; ?>">
                                                <?php echo " ".$i.". ".$row->Tables_in_maintenances_supervisor_dbms; ?>
                                            </a></li>
                            <?php $i=$i+1; }
                                }
                            } ?>
                        </ul>

                    </div>
                </li>
                <li class="nav-item">
                    <a class="nav-link" aria-current="page" href="?Controller=All">Create database backup</a>
                </li>

        </div>