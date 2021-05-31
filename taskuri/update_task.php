<?php
    include "../db_connection.php";
    if($_SERVER["REQUEST_METHOD"] == "GET") {
        $id = $_GET["id"];
        $elapsed = $_GET["elapsed"];
        $connection = mysqli_connect($db_hostname, $db_username, $db_password);
        if(!$connection) {
            echo "Database Connection Error: ".mysqli_connect_error();
        } else {
            $sql = "UPDATE Taskboard.Tasks SET elapsed=$elapsed WHERE id=$id";
            $retval = mysqli_query( $connection, $sql );
            if(! $retval ) {
                echo "Error access in table Tasks: ".mysqli_error($connection);
            } else {
                echo "Task updated";
            }
            mysqli_close($connection);
        }
    }
?>
