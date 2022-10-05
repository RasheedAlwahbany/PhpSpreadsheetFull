<?php

require_once 'Header.php';
require '../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

function ExportData($table)
{
    global $connection;
    global $columns;
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
                    if (!empty(getCellName($i)))
                        $i = 0;
                    $col = getCellName($i) . $columns[$i];
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
                    }
                    $writer->save('DataBackup/' . $table . $i . '.xlsx');
                    print('<br/> The ( DataBackup/' . $table . $i . '.xlsx ) file created succesfully.<br/>');
                } else
                    $writer->save('DataBackup/' . $table . '.xlsx');
                print('<br/> The ( DataBackup/' . $table . '.xlsx ) file created succesfully.<br/>');
            } else {

                echo "<br/><br/> Oopsy error.<br/> This is ( $table ) table is blank. <br/><br/>";
                return false;
            }
        }
    }
    return true;
}
?>

<div class="container">
    <div class="navbar navbar-default" role="navigation">
        <div class="container-fluid">
        <?php echo $helper->getPageHeading()."<br/>"; ?>
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
                                                <?php echo " Table ( " . $i . " ) => " . $row->Tables_in_maintenances_supervisor_dbms; ?>
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
        <div class="container">
            <h1>
                Operation logs:
            </h1>

            <?php
            if (!empty($_GET['Controller'])) {
                $error = false;
                if ($_GET['Controller'] != "All") {
                    // if(!str_contains($row->Tables_in_maintenances_supervisor_dbms, "view"))
                    $error = ExportData($_GET['Controller']);
                } else {
                    $query = $connection->prepare("SHOW TABLES FROM `maintenances_supervisor_dbms` ");
                    if ($query->execute()) {
                        while ($row = $query->fetchObject()) {
                            // if(!str_contains($row->Tables_in_maintenances_supervisor_dbms, "view"))
                            $error = ExportData($row->Tables_in_maintenances_supervisor_dbms);
                        }
                    }
                }
                if (!$error)
                    echo "<script>document.location='http://localhost:8000/export_data.php?'</script>";
            }

            ?>

        </div>
    </div>
</div>