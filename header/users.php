<?php
    include "../db_connection.php";

    class User {}
    class Skill {}

    $connection = mysqli_connect($db_hostname, $db_username, $db_password);
    if(!$connection) {
        echo " user - Database Connection Error: ".mysqli_connect_error();
    } else {
        
        $sql="SELECT * FROM $database.TeamMembers";
        $retval = mysqli_query( $connection, $sql );
        if(! $retval){
            echo "user - Error in access table TeamMembers: ".mysqli_error($connection);
        }
        $users = [];

        while($row = mysqli_fetch_assoc($retval)) {
            $first_name = $row["first_name"];
            $last_name = $row["last_name"];
            $work_hours = $row["work_hours"];
            $id = $row["id"];

            $sql = "SELECT * FROM $database.UserSkills WHERE userid=$id";
            $retval1 = mysqli_query( $connection, $sql );
            if(! $retval1){
                echo "user - Error in access table UserSkills: ".mysqli_error($connection);
            }
            $skill_id = 0;
            $skill_level_id = 0;
            $skill = array();

            while($row1 = mysqli_fetch_assoc($retval1)){
                $skill_id = $row1["skill_id"];
                $skill_level_id = $row1['skill_level'];
                $skill_name = "";
                $skill_level_name = "";
                $sql="SELECT * FROM $database.Skills WHERE id=$skill_id";
                $retval2 = mysqli_query( $connection, $sql );
                if(! $retval2){
                    echo "user - Error in access table Skills: ".mysqli_error($connection);
                } else {
                    while($row2 = mysqli_fetch_assoc($retval2)){
                        $skill_name = $row2["skill"];
                    }
                }
                $sql="SELECT * FROM $database.SkillLevel WHERE id=$skill_level_id";
                $retval2 = mysqli_query( $connection, $sql );
                if(! $retval2){
                    echo "user - Error in access table SkillLevel: ".mysqli_error($connection);
                } else {
                    while($row2 = mysqli_fetch_assoc($retval2)){
                        $skill_level_name = $row2["skill_level"];
                    }
                }
                $skl = new Skill;
                $skl->skill = $skill_name;
                $skl->level = $skill_level_name;
                array_push($skill, $skl);
            }

            $sql = "SELECT * FROM $database.WorkingHours WHERE id=$work_hours";
            $retval1 = mysqli_query( $connection, $sql );
            if(! $retval1){
                echo "user - Error in access table WorkingHours: ".mysqli_error($connection);
            }
            $hours = "";
            while($row1 = mysqli_fetch_assoc($retval1)) {
                $hours = $row1["hour"];
            }

            $user = new User;
            $user->first_name = $first_name;
            $user->last_name = $last_name;
            $user->skill = $skill;
            array_push($users, $user);
        }
        mysqli_close($connection);

        $usersJSON = json_encode($users);
        echo $usersJSON;
    }
?>