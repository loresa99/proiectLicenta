<?php
$db_hostname="127.0.0.1:3306";
$db_username="root";
$db_password="";
$database="taskboard";

$add_team_err = "";
if($_SERVER["REQUEST_METHOD"] == "POST") {
    $team_name = $_POST['TeamName'];
    $description =$_POST['Description'];

	$connection = mysqli_connect($db_hostname, $db_username, $db_password);
	if(!$connection) {
		echo"Database Connection Error...".mysqli_connect_error();
	} else {
        $sql="SELECT * FROM $database.Teams WHERE team_name='$team_name'";
        $retval = mysqli_query( $connection, $sql );
        if(! $retval ) {
			echo "Error access in table Teams".mysqli_error($connection);
		}
        $count=mysqli_num_rows($retval);
        if($count == 0){
		    $sql="Insert INTO $database.Teams(team_name,description) VALUES ('$team_name','$description')";
            $retval = mysqli_query( $connection, $sql );
            if(! $retval ) {
                echo "Error inserting in table Teams".mysqli_error($connection);
            }else{
                header('location: http://localhost/taskboard/header/settings.php');
            }
        }
		
		
        mysqli_close($connection);
	}
}
?>
<!-- Add team Modal -->
<div class="modal fade" id="AddTeam" tabindex="-1" role="dialog" aria-labelledby="AddTeamLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="AddTeamLabel" style="font-size: 20px;">Add Team Dialog</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
	  <form method="post" class="Settings needs-validation" action="add_team.php" novalidate>
          <div class="form-group">
            <div class="input-group">
              <div class="input-group-prepend">
                      <span class="input-group-text" id="teamPrepend" style="display: inline-block; width: 3em;"><i class="fa fa-user"></i></span>
                  </div>
              <input type="text" class="form-control" name="TeamName" placeholder="Team name"
                aria-describedby="teamPrepend" min="3" required>
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
                      <span class="input-group-text" id="teamdescPrepend" style="display: inline-block; width: 3em;"><i class="fa fa-list"></i></span>
                  </div>
              <input type="text" class="form-control" name="Description" placeholder="Description"
                aria-describedby="teamdescPrepend" min="3" required>
              <div class="valid-feedback">
                    Looks good!
                  </div>
              <div class="invalid-feedback">
                    Description must be at least 3 characters in length!
                  </div>
            </div>
          </div>
            
			<div id="add_team_error" style="width: 100%; margin-top: .25rem; margin-bottom: .25rem; font-size: 80%; color: #dc3545;"></div>
			<div class="form-group">
				<button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
				<button type="submit" class="btn btn-success">Add Team</button>
            </div>
            
		</form>
      </div>
    </div>
  </div>
  </div>