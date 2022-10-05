<?php

require_once 'Header.php';
require '../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Reader\Xlsx;

$reader = new Xlsx();
$reader->setReadDataOnly(true);

?>

<div class="container">
    <div class="navbar navbar-default" role="navigation">
        <div class="container-fluid">
            <ul class="nav nav-tabs">

                <li class="nav-item active">
                    <div class="dropdown">
                        <button class="btn btn-secondary dropdown-toggle" onclick="checkDropDown();" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                            Import custom database backup
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
                    <a class="nav-link" aria-current="page" href="?Controller=All">Import all database tables backup</a>
                </li>

        </div>

        <div class="container">
        <h1>
          Operation logs:  
        </h1>
<?php
if (!empty($_GET['Controller'])) {
    function updateItem($table, $table_columns)
    {
        global $connection;
        $query = $connection->prepare("UPDATE " . $table . " SET " . $table_columns . " WHERE " . explode(',',$table_columns)[0]);
        if ($query->execute()) {
            return true;
        }

        return false;
    }


    function saveItem($table, $item)
    {
        global $connection;
        $query = $connection->prepare("INSERT INTO " . $table . " VALUES (" . $item . ")");
        if ($query->execute()) {
            
            return true;
        }

        return false;
    }

    function checkItem($table, $column,$value)
    {
        global $connection;
        $query = $connection->prepare("SELECT * FROM " . $table . " WHERE  " . $column."=".$value);
        if ($query->execute()) {
            return true;
        }

        return false;
    }

    function getBackups($table, $sheet)
    {
        $row_index = 0;
        $table_cols = "";

        foreach ($sheet->toArray() as $key => $val) {
            
            $i = 0;
            $item = "";
            while ($i < count($val)) {
                if ($i == 0) {
                    if ($row_index == 0)
                        $table_cols .= $val[$i];
                    else {
                        if($val[$i]==0 || empty($val[$i]))
                            $item .= 'NULL';
                        else
                            $item .= "'".$val[$i]."'";
                    }
                } else {
                    if ($row_index == 0)
                        $table_cols .= "," . $val[$i];
                    else{
                        if(empty($val[$i]))
                            $item .= ",NULL";
                        else
                            $item .= ",'" . $val[$i]."'";
                    }
                }
                $i = $i + 1;
            }
            if ($row_index != 0){
                if(!is_array($table_cols))
                    $table_cols=explode(',',$table_cols);
                $item_c=$item;
                $item=explode(',',$item);
                if (checkItem($table,$table_cols[0],$item[0])) {
                    $i = 0;
                    $update_values = "";
                    while ($i < count($table_cols)) {
                        if ($i == 0)
                            $update_values .= $table_cols[$i] . "=" . $item[$i];
                        else
                            $update_values .= "," . $table_cols[$i] . "=" . $item[$i];
                        $i=$i+1;
                    }
                    if (!empty($update_values)) {
                        if (updateItem($table, $update_values))
                            echo "<br/> Update ( " . $item_c . " ) succesfully.<br/>";
                        else
                            echo "<br/> Update ( " . $item_c . " ) error.<br/>";
                    } else
                        echo "<br/> Update ( " . $item_c . " ) error.<br/>";
                } else {
                    if (saveItem($table, $item_c))
                        echo "<br/> Adding ( " . $item_c . " ) succesfully.<br/>";
                    else
                        echo "<br/> Adding ( " . $item_c . " ) error.<br/>";
                }
            }
           
            $row_index = $row_index + 1;

        }
    }
    if ($_GET['Controller'] != 'All') {
        if (file_exists("DataBackup/" . $_GET['Controller'] . ".xlsx")) {
            $spreadsheet = $reader->load("DataBackup/" . $_GET['Controller'] . ".xlsx");
            $sheet = $spreadsheet->getActiveSheet();
            if (!empty($sheet->getCoordinates())){
                getBackups($_GET['Controller'], $sheet);
                echo "<br/> backup success.<br/> For the ( " . $_GET['Controller'] . " ) file<br/><br/>";
            }else
                echo "<br/> Oopsy error.<br/> The ( " . $_GET['Controller'] . " ) file is empty<br/><br/>";
        } else {
            echo "<br/> Oopsy error.<br/> The ( " . $_GET['Controller'] . " ) file which you want to backup it is not exist.<br/><br/>";
        }
    } else {
        $query = $connection->prepare(" SHOW TABLES FROM `maintenances_supervisor_dbms` ");
        if ($query->execute()) {
            $i = 1;
            while ($row = $query->fetchObject()) {
                if (file_exists("DataBackup/" . $row->Tables_in_maintenances_supervisor_dbms . ".xlsx")) {
                    $spreadsheet = $reader->load("DataBackup/" . $row->Tables_in_maintenances_supervisor_dbms . ".xlsx");
                    $sheet = $spreadsheet->getActiveSheet();
                    if (!empty($sheet->getCoordinates())){
                        getBackups($row->Tables_in_maintenances_supervisor_dbms, $sheet);
                        echo "<br/> backup success.<br/> For the ( " . $row->Tables_in_maintenances_supervisor_dbms . " ) file<br/><br/>";
                    }else
                        echo "<br/> Oopsy error.<br/> The ( " . $row->Tables_in_maintenances_supervisor_dbms . " ) file is empty<br/><br/>";
                } else {
                    echo "<br/> Oopsy error.<br/> The ( " . $row->Tables_in_maintenances_supervisor_dbms . " ) file which you want to backup it is not exist.<br/><br/>";
                }
            }
        }
    }
}
?>

        </div>
    </div>
</div>