<?php

$db_hostname="127.0.0.1:3306";
$db_username="root";
$db_password="";
$database="taskboard";
$role = "";

if($_SERVER["REQUEST_METHOD"] == "POST")
 {
	 session_start();
	$_POST = json_decode(file_get_contents('php://input'), true);

	$id = intval($_POST['EditTaskId']);
	$task_name = $_POST['TaskName'];
	$skill=$_POST['Skill'];
	$skill_level=$_POST['SkillLevel'];
	$duration = $_POST['Duration'];
	$assigned_to = $_POST['AssignedTo'];
	$status = $_POST['Status'];
	$project_name = $_POST['Project'];

	$connection = mysqli_connect($db_hostname, $db_username, $db_password);
	if(!$connection) {
		echo "edit Database Connection Error...".mysqli_connect_error();
	}
	if (isset($_SESSION['user_id'])) {
		$userId = $_SESSION['user_id'];
		$sql = "SELECT * FROM $database.TeamMembers WHERE id = '$userId'";
		$retval = mysqli_query( $connection, $sql );
		if(! $retval ) {
			echo "Error accessing table TeamMembers0: ".mysqli_error($connection);
		}
		$rezults= mysqli_num_rows($retval);

		while($row = mysqli_fetch_assoc($retval)) {
			$role= $row["role"];
		}
	}

	$skill_id = 0;
	$level_id = 0;
	$status_id = 0;
	$project_id = 0;

	if(!$connection) {
		echo "edit Database Connection Error...".mysqli_connect_error();
	} else {
		$sql="SELECT * FROM $database.TaskStatus WHERE task_status='$status'";
		$retval = mysqli_query( $connection, $sql );
		if(! $retval ) {
			echo "edit Error access in table TaskStatus".mysqli_error($connection);
			return;
		}
		if (mysqli_num_rows($retval) == 1) {
			while($row = mysqli_fetch_assoc($retval)) {
				$status_id = $row["id"];
			}
		}

		$sql="SELECT * FROM $database.Projects WHERE nume='$project_name'";
		$retval = mysqli_query( $connection, $sql );
		if(! $retval ) {
			echo "edit Error access in table Projects".mysqli_error($connection);
			return;
		}
		if (mysqli_num_rows($retval) == 1) {
			while($row = mysqli_fetch_assoc($retval)) {
				$project_id = $row["id"];
			}
		}

		if ($role == 'Admin') {
			$sql="SELECT * FROM $database.Skills WHERE skill='$skill'";
			$retval = mysqli_query( $connection, $sql );
			if(! $retval ) {
				echo "edit Error access in table Skills".mysqli_error($connection);
				return;
			}

			if (mysqli_num_rows($retval) == 1) {
				while($row = mysqli_fetch_assoc($retval)) {
					$skill_id = $row["id"];
				}
			}

			$sql="SELECT * FROM $database.SkillLevel WHERE skill_level='$skill_level'";
			$retval = mysqli_query( $connection, $sql );
			if(! $retval ) {
				echo "edit Error access in table SkillLevel".mysqli_error($connection);
				return;
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
				echo "edit Error1 access in table TeamMembers".mysqli_error($connection);
				return;
			}
			if (mysqli_num_rows($retval) == 1) {
				while($row = mysqli_fetch_assoc($retval)) {
					$user_id = $row["id"];
				}
			}
			$sql = "UPDATE Taskboard.Tasks SET task_name='$task_name',skill_required=$skill_id,level_required=$level_id,duration=$duration,".
					"task_status=$status_id,assigned_member=$user_id,project=$project_id WHERE id=$id";
			$retval = mysqli_query( $connection, $sql );
			if(! $retval ) {
				echo "edit Error2 access in table TeamMembers".mysqli_error($connection);
				return;
			}
		} else {
			$sql = "UPDATE Taskboard.Tasks SET duration=$duration,task_status=$status_id WHERE id=$id";
			$retval = mysqli_query( $connection, $sql );
			if(! $retval ) {
				echo "edit Error3 access in table TeamMembers".mysqli_error($connection);
				return;
			}
		}
        mysqli_close($connection);
	}
}
?>

<!-- Edit Task Modal -->
<div class="modal fade" id="EditTask" tabindex="-1" role="dialog" aria-labelledby="EditTaskLabel" aria-hidden="true" data-backdrop="static">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="EditTaskLabel" style="font-size: 20px;">Edit Task Dialog</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
	  <form method="post" class="TaskForm needs-validation" action="edit_task.php" novalidate name="edit">
			<div class="form-group">
				<div class="input-group">
					<span class="input-group-addon">
						<span style="display: inline-block; width: 10em; text-align: left;"> <i class="fa fa-list"></i> Task name</span>
					</span>
					<?php
							$connection = mysqli_connect($db_hostname, $db_username, $db_password);
							if(!$connection) {
								echo "edit Database Connection Error1...".mysqli_connect_error();
							} else {
							
							if (isset($_SESSION['user_id'])) {
								$userId = $_SESSION['user_id'];
								$sql = "SELECT * FROM $database.TeamMembers WHERE id = '$userId'";
								$retval = mysqli_query( $connection, $sql );
								while($row = mysqli_fetch_assoc($retval)) {
									$role=$row["role"];
									if($role == 'Operator'){
										echo "<input type=\"text\" id=\"edit-task-name\" class=\"form-control\" name=\"EditTaskName\" placeholder=\"Task name\" required disabled>";
									}else{
										echo "<input type=\"text\" id=\"edit-task-name\" class=\"form-control\" name=\"EditTaskName\" placeholder=\"Task name\" required>";
									}
								}
								mysqli_close($connection);
							}
							}
						?>
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
					<select id="edit_task_skill" class="form-control ignore-validation" name="EditSkill">
					<?php
							$connection = mysqli_connect($db_hostname, $db_username, $db_password);
							if(!$connection) {
								echo "edit Database Connection Error1...".mysqli_connect_error();
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
					<?php
							$connection = mysqli_connect($db_hostname, $db_username, $db_password);
							if(!$connection) {
								echo "edit Database Connection Error1...".mysqli_connect_error();
							} else {
							
							if (isset($_SESSION['user_id'])) {
								$userId = $_SESSION['user_id'];
								$sql = "SELECT * FROM $database.TeamMembers WHERE id = '$userId'";
								$retval = mysqli_query( $connection, $sql );
								while($row = mysqli_fetch_assoc($retval)) {
									$role=$row["role"];
									
									if($role == 'Operator'){
										echo "<select id=\"edit_task_skill_level\" class=\"form-control ignore-validation\" name=\"EditSkillLevel\" disabled>";
									}else{
										echo"<select id=\"edit_task_skill_level\" class=\"form-control ignore-validation\" name=\"EditSkillLevel\">";
									}
								}
								mysqli_close($connection);
							}
							}
						?>
					
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
					<input type="number" id="edit-task-duration" class="form-control" name="EditDuration" placeholder="Duration" min="0" max="1000" required>
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

					<select id="edit_task_user" class="form-control ignore-validation" name="EditAssignedTo" onclick="changed();">
						<?php
							$connection = mysqli_connect($db_hostname, $db_username, $db_password);
							if(!$connection) {
								echo "edit Database Connection Error1...".mysqli_connect_error();
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
					<select id="edit_task_status" class="form-control ignore-validation" name="EditStatus">
						<option>Todo</option>
						<option>In progress</option>
						<option>Done</option>
					</select>
				</div>
			</div>

			<div class="form-group">
				<div class="input-group">
					<span class="input-group-addon">
						<span style="display: inline-block; width: 10em; text-align: left;"> <i class="fa fa-check"></i> Project </span>
					</span>
					<select id="edit_task_project" class="form-control ignore-validation" name="EditProject">
					<?php
							$connection = mysqli_connect($db_hostname, $db_username, $db_password);
							if(!$connection) {
								echo "edit Database Connection Error1...".mysqli_connect_error();
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

			<input style="visibility: hidden;" type="number" name="EditTaskId" id="edit-task-id">
			<div id="edit_task_error" style="width: 100%; margin-top: .25rem; margin-bottom: .25rem; font-size: 80%; color: #dc3545;"></div>
			<div class="form-group">
				<button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
				<button type="submit" class="btn btn-success">Edit Task</button>
			</div>
		</form>
      </div>
    </div>
  </div>
  </div>