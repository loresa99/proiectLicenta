<?php
	$db_hostname="127.0.0.1:3306";
	$db_username="root";
	$db_password="";
	$database="taskboard";

	function logger($logMsg="logger", $filename="logger", $logData=""){     
		$log  = date("j.n.Y h:i:s")." || $logMsg : ".print_r($logData,1).PHP_EOL .                  
		"-------------------------".PHP_EOL;
		file_put_contents('./'.$filename.date("j.n.Y").'.log', $log, FILE_APPEND);                      
	}

	function initializeDatabase() {
		$db_hostname="127.0.0.1:3306";
		$db_username="root";
		$db_password="";
		$database="taskboard";


		$connection = mysqli_connect($db_hostname, $db_username, $db_password);
		if(!$connection) {
			echo"Database Connection Error...".mysqli_connect_error();
		} else {
			$sql = 'CREATE Database IF NOT EXISTS ' . $database;
			$retval = mysqli_query( $connection, $sql );
			if(! $retval ) {
				echo"Could not create database...".mysqli_connect_error();
			}

			$sql = "CREATE Table IF NOT EXISTS $database.Skills (".
				"id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,".
				"skill VARCHAR(30) NOT NULL)";
			$retval = mysqli_query( $connection, $sql );
			if(! $retval ) {
				echo"Could not create table Skills".mysqli_error($connection);
			} else {
				$sql = "SELECT * FROM $database.Skills";
				$retval = mysqli_query( $connection, $sql );
				if(! $retval ) {
					echo "".mysqli_connect_error();
				}
			}

			$sql = "CREATE Table IF NOT EXISTS $database.WorkingHours (".
				"id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,".
				"hour VARCHAR(10) NOT NULL)";
			$retval = mysqli_query( $connection, $sql );
			if(! $retval ) {
				echo"Could not create table WorkingHours".mysqli_error($connection);
			} else {
				$sql = "SELECT * FROM $database.WorkingHours";
				$retval = mysqli_query( $connection, $sql );
				if(! $retval ) {
					echo "".mysqli_connect_error();
				}
				$count = mysqli_num_rows($retval);
				if($count == 0) {
					$sql = "INSERT INTO $database.WorkingHours (hour) VALUES ('4h/day')";
					mysqli_query( $connection, $sql );
					$sql = "INSERT INTO $database.WorkingHours (hour) VALUES ('6h/day')";
					mysqli_query( $connection, $sql );
					$sql = "INSERT INTO $database.WorkingHours (hour) VALUES ('8h/day')";
					mysqli_query( $connection, $sql );
				}
			}

			$sql = "CREATE Table IF NOT EXISTS $database.TaskStatus (".
				"id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,".
				"task_status VARCHAR(15) NOT NULL)";
			$retval = mysqli_query( $connection, $sql );
			if(! $retval ) {
				echo"Could not create table TaskStatus".mysqli_error($connection);
			} else {
				$sql = "SELECT * FROM $database.TaskStatus";
				$retval = mysqli_query( $connection, $sql );
				if(! $retval ) {
					echo "".mysqli_connect_error();
				}
				$count = mysqli_num_rows($retval);
				if($count == 0) {
					$sql = "INSERT INTO $database.TaskStatus (task_status) VALUES ('Todo')";
					mysqli_query( $connection, $sql );
					$sql = "INSERT INTO $database.TaskStatus (task_status) VALUES ('In progress')";
					mysqli_query( $connection, $sql );
					$sql = "INSERT INTO $database.TaskStatus (task_status) VALUES ('Done')";
					mysqli_query( $connection, $sql );
				}
			}

			$sql = "CREATE Table IF NOT EXISTS $database.SkillLevel (".
				"id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,".
				"skill_level VARCHAR(10) NOT NULL)";
			$retval = mysqli_query( $connection, $sql );
			if(! $retval ) {
				echo"Could not create table SkillLevel".mysqli_error($connection);
			} else {
				$sql = "SELECT * FROM $database.SkillLevel";
				$retval = mysqli_query( $connection, $sql );
				if(! $retval ) {
					echo "".mysqli_connect_error();
				}
				$count = mysqli_num_rows($retval);
				if($count == 0) {
					for ($x = 1; $x <= 10; $x++) {
						$sql = "INSERT INTO $database.SkillLevel (skill_level) VALUES ('Level $x')";
						mysqli_query( $connection, $sql );
					}
				}
			}
			$sql = "CREATE Table IF NOT EXISTS $database.UserSkills (".
				"id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,".
				"userid INT NOT NULL,".
				"skill_id INT NOT NULL,".
				"skill_level INT NOT NULL,".
				"CONSTRAINT fk_user_id FOREIGN KEY (userid) REFERENCES TeamMembers(id),".
				"CONSTRAINT fk_skill_id FOREIGN KEY (skill_id) REFERENCES Skills(id),".
				"CONSTRAINT fk_skill_level FOREIGN KEY (skill_level) REFERENCES SkillLevel(id))";
			$retval = mysqli_query( $connection, $sql );
			if(! $retval ) {
				echo"Could not create table UserSkills".mysqli_error($connection);
			}

			$sql = "CREATE Table IF NOT EXISTS $database.Projects (".
				"id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,".
				"nume VARCHAR(30) NOT NULL,".
				"description VARCHAR(50) NOT NULL)";
			$retval = mysqli_query( $connection, $sql );
			if(! $retval ) {
				echo"Could not create table Projects".mysqli_error($connection);
			}else{
				$sql1="SELECT * FROM $database.Projects";
				$retval= mysqli_query($connection, $sql1);
				if(! $retval){
					echo "Could not select from table Projects".mysqli_error($connection);
				}
				else{
					$count = mysqli_num_rows($retval);
					if($count == 0){
						$sql="INSERT INTO $database.Projects(nume, description) VALUES('Pro', 'abg')";
						mysqli_query($connection,$sql);
					}
				}
			}

			$sql = "CREATE Table IF NOT EXISTS $database.Teams (".
				"id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,".
				"team_name VARCHAR(30) NOT NULL,".
				"description VARCHAR(50) NOT NULL)";
			$retval = mysqli_query( $connection, $sql );
			if(! $retval ) {
				echo"Could not create table Teams".mysqli_error($connection);
			} else {
				$sql1="SELECT * FROM $database.Teams";
				$retval= mysqli_query($connection, $sql1);
				if(! $retval){
					echo "Could not select from table Teams".mysqli_error($connection);
				}
				else{
					$count = mysqli_num_rows($retval);
					if($count == 0){
						$sql="INSERT INTO $database.Teams(team_name, description) VALUES('All', 'abc')";
						mysqli_query($connection,$sql);
					}
				}
				
			}

			$sql = "CREATE Table IF NOT EXISTS $database.TeamMembers (".
				"id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,".
				"first_name VARCHAR(20) NOT NULL,".
				"last_name VARCHAR(20) NOT NULL,".
				"email VARCHAR(30) NOT NULL,".
				"email_confirmed BOOLEAN NOT NULL,".
				"password VARCHAR(20) NOT NULL,".
				"work_hours INT NOT NULL,".
				"role VARCHAR(10) NOT NULL,".
				"team INT NOT NULL,".
				"CONSTRAINT fk_team FOREIGN KEY (team) REFERENCES Teams(id),".
				"CONSTRAINT fk_work_hours FOREIGN KEY (work_hours) REFERENCES WorkingHours(id))";
			$retval = mysqli_query( $connection, $sql );
			if(! $retval ) {
				echo"Could not create table TeamMembers".mysqli_error($connection);
			}

			$sql = "CREATE Table IF NOT EXISTS $database.Tasks (".
				"id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,".
				"task_name VARCHAR(30) NOT NULL,".
				"skill_required INT NOT NULL,".
				"level_required INT NOT NULL,".
				"duration INT NOT NULL,".
				"elapsed INT NOT NULL,".
				"stopped INT NOT NULL,".
				"task_status INT NOT NULL,".
				"assigned_member INT NOT NULL,".
				"project INT NOT NULL,".
				"timestamp VARCHAR(25) NOT NULL,".
				"CONSTRAINT fk_skill_required FOREIGN KEY (skill_required) REFERENCES Skills(id),".
				"CONSTRAINT fk_level_required FOREIGN KEY (level_required) REFERENCES SkillLevel(id),".
				"CONSTRAINT fk_task_status FOREIGN KEY (task_status) REFERENCES TaskStatus(id),".
				"CONSTRAINT fk_assigned_member FOREIGN KEY (assigned_member) REFERENCES TeamMembers(id),".
				"CONSTRAINT fk_project FOREIGN KEY (project) REFERENCES Projects(id))";
			$retval = mysqli_query( $connection, $sql );
			if(! $retval ) {
				echo"Could not create table Tasks".mysqli_error($connection);
			}

			$sql = "CREATE Table IF NOT EXISTS $database.Settings (".
				"id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,".
				"sort_by VARCHAR(10) NOT NULL".
				")";
			$retval = mysqli_query( $connection, $sql );
			if(! $retval ) {
				echo"Could not create table Tasks".mysqli_error($connection);
			} else {
				$sql = "SELECT * FROM $database.Settings";
				$retval= mysqli_query($connection, $sql);
				if(! $retval){
					echo "Could not select from table Settings".mysqli_error($connection);
				} else {
					$count = mysqli_num_rows($retval);
					if($count == 0){
						$sql="INSERT INTO $database.Settings(sort_by) VALUES('ASC')";
						mysqli_query($connection,$sql);
					}
				}
			}
		}

		mysqli_close($connection);
	}
	
?>