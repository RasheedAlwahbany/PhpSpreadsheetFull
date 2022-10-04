<?php
    $connection = new pdo("mysql:host=localhost;dbname=maintenances_supervisor_dbms;port=3306;charset=utf8", "root", "");
    $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $connection->setAttribute(PDO::ATTR_EMULATE_PREPARES, true);