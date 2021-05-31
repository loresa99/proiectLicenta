<?php
$db_hostname="127.0.0.1:3306";
$db_username="root";
$db_password="";
$database="taskboard";

$add_task_err = "";
if($_SERVER["REQUEST_METHOD"] == "POST") {
	$_POST = json_decode(file_get_contents('php://input'), true);

	$task_name = $_POST['TaskName'];
	$skill = $_POST['Skill'];
	$skill_level = $_POST['SkillLevel'];
	$duration = $_POST['Duration'];
	$assigned_to = $_POST['AssignedTo'];
	$status = $_POST['Status'];
	$project_name = $_POST['Project'];
	//logger("Addtask - $task_name $skill $skill_level $duration $assigned_to $status $project_name");
	echo "Addtask - $task_name $skill $skill_level $duration $assigned_to $status $project_name $team_name";

	$skill_id = 0;
	$level_id = 0;
	$status_id = 0;
	$user_id = 0;
	$user_skill_id = 0;
	$user_skill_level_id = 0;
	$project_id = 0;

	$connection = mysqli_connect($db_hostname, $db_username, $db_password);
	if(!$connection) {
		//logger("AddTask - Database Connection Error...".mysqli_connect_error());
		echo "AddTask - Database Connection Error...".mysqli_connect_error();
	} else {
		$sql="SELECT * FROM $database.Skills WHERE skill='$skill'";
		$retval = mysqli_query( $connection, $sql );
		if(! $retval ) {
			//logger( "AddTask - Error access in table Skills: ".mysqli_error($connection));
			echo "AddTask - Error access in table Skills: ".mysqli_error($connection);
		}
		if (mysqli_num_rows($retval) == 1) {
			while($row = mysqli_fetch_assoc($retval)) {
				$skill_id = $row["id"];
			}
		}

		$sql="SELECT * FROM $database.SkillLevel WHERE skill_level='$skill_level'";
		$retval = mysqli_query( $connection, $sql );
		if(! $retval ) {
			//logger("AddTask - Error access in table SkillLevel: ".mysqli_error($connection));
			echo "AddTask - Error access in table SkillLevel: ".mysqli_error($connection);
		}
		if (mysqli_num_rows($retval) == 1) {
			while($row = mysqli_fetch_assoc($retval)) {
				$level_id = $row["id"];
			}
		}

		$pieces = explode(" ", $assigned_to);
		$first_name = $pieces[0];
		$last_name = $pieces[1];
		$sql = "SELECT * FROM $database.TeamMembers WHERE first_name='$first_name' AND last_name='$last_name'";
		$retval = mysqli_query( $connection, $sql );
		if(! $retval ) {
			//logger("AddTask - Error access in table TeamMembers: ".mysqli_error($connection));
			echo "AddTask - Error access in table TeamMembers: ".mysqli_error($connection);
		}
		if (mysqli_num_rows($retval) == 1) {
			while($row = mysqli_fetch_assoc($retval)) {
				$user_id = $row["id"];
			}
		}

		//if ($skill_id == $user_skill_id && $user_skill_level_id >= $level_id) {
			$sql="SELECT * FROM $database.TaskStatus WHERE task_status='$status'";
			$retval = mysqli_query( $connection, $sql );
			if(! $retval ) {
				//logger("AddTask - Error access in table TaskStatus: ".mysqli_error($connection));
				echo "AddTask - Error access in table TaskStatus: ".mysqli_error($connection);
			}
			if (mysqli_num_rows($retval) == 1) {
				while($row = mysqli_fetch_assoc($retval)) {
					$status_id = $row["id"];
				}
			}

			$sql="SELECT * FROM $database.Projects WHERE nume='$project_name'";
			$retval = mysqli_query( $connection, $sql );
			if(! $retval ) {
				//logger("AddTask - Error access in table Projects: ".mysqli_error($connection));
				echo "AddTask - Error access in table Projects: ".mysqli_error($connection);
			}
			
			if (mysqli_num_rows($retval) == 1) {
				while($row = mysqli_fetch_assoc($retval)) {
					$project_id = $row["id"];
				}
			}

			$sql="SELECT * FROM $database.Teams WHERE team_name='$team_name'";
			$retval = mysqli_query( $connection, $sql );
			if(! $retval ) {
				//logger("AddTask - Error access in table Teams: ".mysqli_error($connection));
				echo "AddTask - Error access in table Teams: ".mysqli_error($connection);
			}
			
			if (mysqli_num_rows($retval) == 1) {
				while($row = mysqli_fetch_assoc($retval)) {
					$team_id = $row["id"];
				}
			}
			
			$timestamp = date("Y-m-d H:i:s");
			$sql = "INSERT INTO Taskboard.Tasks(task_name,skill_required,level_required,duration,elapsed,stopped,task_status,assigned_member,project,timestamp) ".
					"VALUES('$task_name',$skill_id,$level_id,$duration,0,0,$status_id,$user_id, $project_id,'$timestamp')";
			$retval = mysqli_query( $connection, $sql );
			if(! $retval ) {
				//logger("AddTask - Error access in table Tasks: ".mysqli_error($connection));
				echo "AddTask - Error access in table Tasks: ".mysqli_error($connection);
			} else {
				header("Location: http://localhost/taskboard/taskuri/taskuri.php");
				echo "ok";
				exit();
			}
		//}
		
        mysqli_close($connection);
	}
}
?>
<!-- Add Task Modal -->
<div class="modal fade" id="AddTask" tabindex="-1" role="dialog" aria-labelledby="AddTaskLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="AddTaskLabel" style="font-size: 20px;">Add Task Dialog</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
	  <form method="post" class="TaskForm needs-validation" action="" novalidate name="add">
			<div class="form-group">
				<div class="input-group">
					<span class="input-group-addon">
						<span style="display: inline-block; width: 10em; text-align: left;"> <i class="fa fa-list"></i> Task name</span>
					</span>
					<input type="text" class="form-control" name="TaskName" id="add-task-name" placeholder="Task name" required>
					<div class="valid-feedback">
        				Looks good!
      				</div>
					<div class="invalid-feedback">
        				The field is required ! 
      				</div>
				</div>
			</div>
			<div class="form-group">
				<div class="input-group">
					<span class="input-group-addon">
						<span style="display: inline-block; width: 10em; text-align: left;"> <i class="fa fa-cogs"></i> Skill</span>
					</span>
					<select class="form-control ignore-validation" id="add_task_skill" name="Skill">
					<?php
							$connection = mysqli_connect($db_hostname, $db_username, $db_password);
							if(!$connection) {
								echo "Database Connection Error...".mysqli_connect_error();
							} else {
								$sql="SELECT * FROM $database.Skills";
								$retval = mysqli_query( $connection, $sql );
								while($row = mysqli_fetch_assoc($retval)) {
									$skill_name=$row["skill"];
									echo "<option>$skill_name</option>";
								}
								mysqli_close($connection);
							}
						?>
					</select>
				</div>
			</div>
			<div class="form-group">
				<div class="input-group">
					<span class="input-group-addon">
						<span style="display: inline-block; width: 10em; text-align: left;"> <i class="fa fa-arrow-up"></i> Skill Level</span>
					</span>
					<select class="form-control ignore-validation" id="add_task_skill_level" name="SkillLevel">
						<option>Level 1</option>
						<option>Level 2</option>
						<option>Level 3</option>
						<option>Level 4</option>
						<option>Level 5</option>
						<option>Level 6</option>
						<option>Level 7</option>
						<option>Level 8</option>
						<option>Level 9</option>
						<option>Level 10</option>
					</select>
				</div>
			</div>
			<div class="form-group">
				<div class="input-group">
					<span class="input-group-addon">
						<span style="display: inline-block; width: 10em; text-align: left;"> <i class="fa fa-clock-o"></i> Duration</span>
					</span>
					<input type="number" class="form-control" name="Duration" id="add-task-duration" placeholder="Duration" min="0" max="1000" required>
					<div class="valid-feedback">
        				Looks good!
      				</div>
					<div class="invalid-feedback">
        				The field is required ! 
      				</div>
				</div>
			</div>
			<div class="form-group">
				<div class="input-group">
					<span class="input-group-addon">
						<span style="display: inline-block; width: 10em; text-align: left;"> <i class="fa fa-user"></i> Assigned To</span>
					</span>
					<select class="form-control ignore-validation" name="AssignedTo" id="add_task_user">
						<?php
							$connection = mysqli_connect($db_hostname, $db_username, $db_password);
							if(!$connection) {
								echo "Database Connection Error...".mysqli_connect_error();
							} else {
								$sql="SELECT * FROM $database.TeamMembers";
								$retval = mysqli_query( $connection, $sql );
								while($row = mysqli_fetch_assoc($retval)) {
									$first_name=$row["first_name"];
									$last_name=$row["last_name"];
									
									echo "<option>$first_name $last_name</option>";
								}
								mysqli_close($connection);
							}
						?>
					</select>
				</div>
			</div>
			<div class="form-group">
				<div class="input-group">
					<span class="input-group-addon">
						<span style="display: inline-block; width: 10em; text-align: left;"> <i class="fa fa-check"></i> Status</span>
					</span>
					<select class="form-control ignore-validation" name="Status" id="add_task_status">
						<option>Todo</option>
						<option>In progress</option>
						<option>Done</option>
					</select>
				</div>
			</div>

			<div class="form-group">
				<div class="input-group">
					<span class="input-group-addon">
						<span style="display: inline-block; width: 10em; text-align: left;"> <i class="fa fa-check"></i> Projects </span>
					</span>
					<select class="form-control ignore-validation" name="Project" id="add_task_project">
						<?php
							$connection = mysqli_connect($db_hostname, $db_username, $db_password);
							if(!$connection) {
								echo"Database Connection Error...".mysqli_connect_error();
							} else {
								$sql="SELECT * FROM $database.Projects";
								$retval = mysqli_query( $connection, $sql );
								while($row = mysqli_fetch_assoc($retval)) {
									$id=$row["id"];
									$nume=$row["nume"];
									
									echo "<option>$nume</option>";
								}
							}
						?>
					</select>
				</div>
			</div>

			<div id="add_task_error" style="width: 100%; margin-top: .25rem; margin-bottom: .25rem; font-size: 80%; color: #dc3545;"></div>
			<div class="form-group">
				<button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
				<button type="submit" class="btn btn-success">Add Task</button>
			</div>
		</form>
      </div>
    </div>
  </div>
  </div>