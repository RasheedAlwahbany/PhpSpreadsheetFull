<?php

require_once 'Header.php';
require '../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$err = "";
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

                    $col = getCellName($i) . $columns[$i];
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
            if (!empty($sheet->getCoordinates())) {
                if (is_file('DataBackup/' . $table . '.xlsx')) {
                    $i = 0;
                    while (is_file('DataBackup/' . $table . $i . '.xlsx')) {
                        $i = $i + 1;
                        print('DataBackup/' . $table . $i . '.xlsx');
                    }
                    $writer->save('DataBackup/' . $table . $i . '.xlsx');
                } else
                    $writer->save('DataBackup/' . $table . '.xlsx');
            } else {
                $err = "Error";
                echo "<br/>Oopsy error.<br/><br/>This is $table table is blank. <br/><br/>";
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
    if (empty($err))
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
                            Export custom database backup
                        </button>
                        <ul id="dropdownMenuButtonMenu">
                            <?php
                            if ($connection) {
                                $query = $connection->prepare(" SHOW TABLES FROM `maintenances_supervisor_dbms` ");
                                if ($query->execute()) {
                                    $i = 1;
                                    while ($row = $query->fetchObject()) { ?>
                                        <li><a class="dropdown-item" href="?Controller=<?php echo $row->Tables_in_maintenances_supervisor_dbms; ?>">
                                                <?php echo " Table ( " . $i . " ): " . $row->Tables_in_maintenances_supervisor_dbms; ?>
                                            </a></li>
                            <?php $i = $i + 1;
                                    }
                                }
                            } ?>
                        </ul>

                    </div>
                </li>
                <li class="nav-item">
                    <a class="nav-link" aria-current="page" href="?Controller=All">Export all database tables backup</a>
                </li>

        </div>