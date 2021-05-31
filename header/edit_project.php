<?php
$db_hostname="127.0.0.1:3306";
$db_username="root";
$db_password="";
$database="taskboard";

if($_SERVER["REQUEST_METHOD"] == "POST") {
	session_start();
	$id = $_POST["EditProjectId"];
	$nume = $_POST["EditProjectName"];
	$description=$_POST["EditProjDescription"];
    $connection = mysqli_connect($db_hostname, $db_username, $db_password);

	if(!$connection) {
		echo "Database Connection Error...".mysqli_connect_error();
	} else {
			$sql = "UPDATE Taskboard.Projects SET nume='$nume', description= '$description' WHERE id=$id";
			$retval = mysqli_query( $connection, $sql );
			if(! $retval ) {
				echo"Error access in table Projects".mysqli_error($connection);
			}else{
                mysqli_close($connection);
                header('location: http://localhost/taskboard/header/settings.php');
            }
    }
    
}
?>

<!-- Edit Project Modal -->
<div class="modal fade" id="EditProject" tabindex="-1" role="dialog" aria-labelledby="EditProjectLabel" aria-hidden="true" data-backdrop="static">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="EditProjectLabel" style="font-size: 20px;">Edit Project Dialog</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
	  <form method="post" class="TaskForm" action="edit_project.php" novalidate>
			<div class="form-group">
				<div class="input-group">
				<div class="input-group-prepend">
						<span class="input-group-text" id="editprojPrepend" style="display: inline-block; width: 3em;"><i class="fa fa-list"></i></span>
					</div>
				<input type="text" id="edit-project-name" class="form-control" name="EditProjectName" placeholder="Project name"
					aria-describedby="editprojPrepend" min="3" required>
				<div class="valid-feedback">
						Looks good!
					</div>
				<div class="invalid-feedback">
						Project name must be at least 3 characters in length!
					</div>
				</div>
			</div>
			<div class="form-group">
				<div class="input-group">
				<div class="input-group-prepend">
						<span class="input-group-text" id="editprojPrepend" style="display: inline-block; width: 3em;"><i class="fa fa-list"></i></span>
					</div>
				<input type="text" id="edit-project-description" class="form-control" name="EditProjDescription" placeholder="Description"
					aria-describedby="editprojPrepend" min="3" required>
				<div class="valid-feedback">
						Looks good!
					</div>
				<div class="invalid-feedback">
						Description must be at least 3 characters in length!
					</div>
				</div>
			</div>
			<input style="visibility: hidden;" type="number" name="EditProjectId" id="edit-project-id">
			<div class="form-group">
				<button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
				<button type="submit" class="btn btn-success">Edit Project</button>
			</div>
		</form>
      </div>
    </div>
  </div>
  </div>