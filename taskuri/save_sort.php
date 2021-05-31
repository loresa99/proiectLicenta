<?php
    include "../db_connection.php";
    if($_SERVER["REQUEST_METHOD"] == "GET") {
        $sort = $_GET["sort"];
        $connection = mysqli_connect($db_hostname, $db_username, $db_password);
        if(!$connection) {
            echo "Database Connection Error: ".mysqli_connect_error();
        } else {
            $sql = "UPDATE Taskboard.Settings SET sort_by='$sort'";
            $retval = mysqli_query( $connection, $sql );
            if(! $retval ) {
                echo "Error access in table Settings: ".mysqli_error($connection);
            } else {
                echo "Sort method saved";
            }
            mysqli_close($connection);
        }
    }
?>
