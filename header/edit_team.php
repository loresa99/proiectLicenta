<?php
$db_hostname="127.0.0.1:3306";
$db_username="root";
$db_password="";
$database="taskboard";

if($_SERVER["REQUEST_METHOD"] == "POST") {
	session_start();
	$id = $_POST["EditTeamId"];
	$team_name = $_POST["EditTeamName"];
	$description=$_POST["EditDescription"];
    $connection = mysqli_connect($db_hostname, $db_username, $db_password);

	if(!$connection) {
		echo "Database Connection Error...".mysqli_connect_error();
	} else {
			$sql = "UPDATE Taskboard.Teams SET team_name='$team_name', description= '$description' WHERE id=$id";
			$retval = mysqli_query( $connection, $sql );
			if(! $retval ) {
				echo"Error access in table Teams".mysqli_error($connection);
			}else{
                mysqli_close($connection);
                header('location: http://localhost/taskboard/header/settings.php');
            }
		
    }
    
}
?>

<!-- Edit Team Modal -->
<div class="modal fade" id="EditTeam" tabindex="-1" role="dialog" aria-labelledby="EditTeamLabel" aria-hidden="true" data-backdrop="static">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="EditTeamLabel" style="font-size: 20px;">Edit Team Dialog</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
	  <form method="post" class="Settings needs-validation" action="edit_team.php" novalidate>
			<div class="form-group">
				<div class="input-group">
				<div class="input-group-prepend">
						<span class="input-group-text" id="editteamPrepend" style="display: inline-block; width: 3em;"><i class="fa fa-user"></i></span>
					</div>
				<input type="text" id="edit-team-name" class="form-control" name="EditTeamName" placeholder="Team name"
					aria-describedby="editteamPrepend" min="3" required>
				<div class="valid-feedback">
						Looks good!
					</div>
				<div class="invalid-feedback">
						Team name must be at least 3 characters in length!
					</div>
				</div>
			</div>
			<div class="form-group">
				<div class="input-group">
				<div class="input-group-prepend">
						<span class="input-group-text" id="editdescPrepend" style="display: inline-block; width: 3em;"><i class="fa fa-user"></i></span>
					</div>
				<input type="text"class="form-control" name="EditDescription" placeholder="Description"
					aria-describedby="editdescPrepend" min="3" required>
				<div class="valid-feedback">
						Looks good!
					</div>
				<div class="invalid-feedback">
						Description must be at least 3 characters in length!
					</div>
				</div>
			</div>
			<input style="visibility: hidden;" type="number" name="EditTeamId" id="edit-team-id">
			<div class="form-group">
				<button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
				<button type="submit" class="btn btn-success">Edit Team</button>
			</div>
		</form>
      </div>
    </div>
  </div>
  </div>