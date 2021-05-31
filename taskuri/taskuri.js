(function() {
    'use strict';
    window.addEventListener('load', function() {
      var progressItems = document.getElementsByClassName('progress-bar');
      this.console.log("progressItems: " + progressItems.length);
      // Fetch all the forms we want to apply custom Bootstrap validation styles to
      var forms = document.getElementsByClassName('TaskForm needs-validation');
      // Loop over them and prevent submission
      Array.prototype.filter.call(forms, function(form) {
        form.addEventListener('submit', function(event) {
          console.log("Submit form: " + form.name);
          document.getElementById(form.name + "_task_error").innerHTML = "";
            event.preventDefault();
            event.stopPropagation();
            const Http = new XMLHttpRequest();
            const url='http://localhost/taskboard/header/users.php';
            Http.open("GET", url);
            Http.send();
            var skillLevelEnough = true;
            var ready = false;
            Http.onreadystatechange = (e) => {
              if(Http.readyState === 4 && Http.status === 200) {
                console.log(Http.responseText);
                var users = JSON.parse(Http.responseText); //luam din bd
                console.log(users);
                //iteram pe users din bd
                for (var user of users) {
                  var uiUser = document.getElementById(form.name + "_task_user");
                  //ma opresc la user selectat pe interf
                  if (uiUser.value === (user.first_name + " " + user.last_name)) {
                    var skill = document.getElementById(form.name + "_task_skill").value;
                    var skill_level = document.getElementById(form.name + "_task_skill_level").value;

                    var found = false;
                    var found_skill = "";
                    var found_level = "";
                    for(var user_skill of user.skill){
                      if(user_skill.skill.toLowerCase().includes(skill.toLowerCase())) {
                        found_skill = user_skill.skill;
                        found_level = user_skill.level;
                        found = true;
                        break;
                      }
                    }
                    if(!found) {
                      document.getElementById(form.name + "_task_error").innerHTML = "The user selected does not have the selected task skill(user skills:" + found_skill + ") <br> ";
                      skillLevelEnough = false;
                      ready = true;
                    } else {
                      console.log(skill_level);
                      console.log(found_level);
                      if(skill_level.localeCompare(found_level) > 0) {
                        document.getElementById(form.name + "_task_error").innerHTML = "Task skill level greather than user skill level(" + found_level + " )<br>";
                        skillLevelEnough= false;
                        ready = true;
                      }
                    }
                    if (form.checkValidity() === false || skillLevelEnough === false) {
                      console.log("form not valid");
                      event.preventDefault();
                      event.stopPropagation();
                      var inputs = document.getElementsByClassName('form-control');
                      for( var i = 0 ; i < inputs.length; i++ ) {
                        var input = inputs[i];
                        if (!input.classList.value.includes('ignore-validation')) {
                          input.classList.remove('is-invalid');
                          input.classList.remove('is-valid');
                          if (input.checkValidity() === false) {
                            input.classList.add('is-invalid');
                          } else {
                              input.classList.add('is-valid');
                          }
                        }
                      }
                    } else {
                      const Http = new XMLHttpRequest();
                      const url='http://localhost/taskboard/taskuri/' + form.name + '_task.php';
                      console.log(url);
                      Http.open("POST", url);
                      Http.setRequestHeader('Content-Type', 'application/json');
                      var taskId = 0;
                      if (form.name === 'edit') {
                        taskId = document.getElementById("edit-task-id").value;
                      }
                      console.log(document.getElementById(form.name + "-task-name").value);
                      Http.send(JSON.stringify({
                        TaskName: document.getElementById(form.name + "-task-name").value,
                        Skill: document.getElementById(form.name + "_task_skill").value,
                        SkillLevel: document.getElementById(form.name + "_task_skill_level").value,
                        Duration: document.getElementById(form.name + "-task-duration").value,
                        AssignedTo: document.getElementById(form.name + "_task_user").value,
                        Status: document.getElementById(form.name + "_task_status").value,
                        Project: document.getElementById(form.name + "_task_project").value,
                        EditTaskId: taskId
                      }));
                      Http.onreadystatechange = (e) => {
                        if(Http.readyState === 4 && Http.status === 200) {
                          console.log(Http.responseText);
                          if (form.name === 'add') {
                            $('#AddTask').modal('toggle');
                          } else {
                            $('#EditTask').modal('toggle');
                          }
                          console.log("Task " + form.name + "ed successfully");
                          window.top.location.replace('http://localhost/taskboard/');  //refresh
                        }
                        if (Http.status !== 200) {
                          console.log(Http.responseText);
                        }
                      }
                    }
                  }
                }
              }
            }
        }, false);
      });
    }, false);
  })();
  $('#AddTask').on('shown.bs.modal', function (e) {
    document.getElementById("add-task-name").value = '';
    document.getElementById("add-task-duration").value = '';
  });
  $('#EditTask').on('shown.bs.modal', function (e) {
    //get data-id attribute of the clicked element
    var taskId = $(e.relatedTarget).data('task-id');
    console.log(taskId);
    var taskName = $(e.relatedTarget).data('task-name');
    var skill = $(e.relatedTarget).data('skill');
    var level = $(e.relatedTarget).data('level');
    var duration = $(e.relatedTarget).data('duration');
    var firstName = $(e.relatedTarget).data('first-name');
    var lastName = $(e.relatedTarget).data('last-name');
    var status = $(e.relatedTarget).data('status');
    var project = $(e.relatedTarget).data('project');

    document.getElementById("edit-task-id").value = parseInt(taskId);
    document.getElementById("edit-task-name").value = taskName;
    document.getElementById("edit_task_skill").value = skill;
    document.getElementById("edit_task_skill_level").value = level;
    document.getElementById("edit-task-duration").value = duration;
    document.getElementById("edit_task_user").value = firstName + ' ' + lastName;
    document.getElementById("edit_task_status").value = status;
    document.getElementById("edit_task_project").value = project;
  });
  $('#DeleteTask').on('shown.bs.modal', function (e) {
    //get data-id attribute of the clicked element
    var taskId = $(e.relatedTarget).data('task-id');
    var taskName = $(e.relatedTarget).data('task-name');
    document.getElementById('task-name').innerHTML = "Are you sure you want to delete task <i>" + taskName + "</i>?";
    document.getElementById("TaskIdInput").value = parseInt(taskId);
  });

function sort(by) {
  console.log("Sorting by " + by);
  const Http = new XMLHttpRequest();
  const url='http://localhost/taskboard/taskuri/save_sort.php?sort=' + by;
  Http.open("GET", url);
  Http.send();
  Http.onreadystatechange = (e) => {
    if(Http.readyState === 4 && Http.status === 200) {
      console.log(Http.responseText);
      window.top.location.replace('http://localhost/taskboard/');
    }
    if (Http.status !== 200) {
      console.log(Http.responseText);
    }
  }
}

$(document).ready(function () {
  let sortMethod = document.getElementById('sort-method').innerHTML;
  console.log("Sort method: " + sortMethod);
  if (sortMethod === "ASC") {
    document.getElementById("sort-desc").classList.remove("active");
    document.getElementById("sort-time").classList.remove("active");
    document.getElementById("sort-asc").classList.add("active");
  } else if (sortMethod === "DESC") {
    document.getElementById("sort-asc").classList.remove("active");
    document.getElementById("sort-time").classList.remove("active");
    document.getElementById("sort-desc").classList.add("active");
  } else if (sortMethod === "TIME") {
    document.getElementById("sort-asc").classList.remove("active");
    document.getElementById("sort-desc").classList.remove("active");
    document.getElementById("sort-time").classList.add("active");
  }
  console.log("ASC: " + document.getElementById("sort-asc").classList);
  console.log("DESC: " + document.getElementById("sort-desc").classList);
  console.log("TIME: " + document.getElementById("sort-time").classList);

  // Load running tasks from database
  console.log("Logged user: " + document.getElementById("user-role").innerHTML);
  continueTasks();
});

var runningTasks = [];
var simulated = true;
var schedule = 0;

function start(taskid) {
  if (simulated) {
    // 1 minute elapses in 0.5 seconds
    schedule = 2000;
  } else {
    // Real time
    schedule = 60 * 1000; // 60 seconds
  }
  for (var task of runningTasks) {
    if (task.id === taskid) {
      // If task found, just resume
      if (!task.running) {
        task.running = true;
        task.update(task.elapsed, taskid);
        $("#progress-" + taskid).addClass("progress-bar-striped");
        $("#progress-" + taskid).addClass("progress-bar-animated");
        return;
      }
    }
  }
  // Add task to task pool
  let elapsed = parseInt(document.getElementById("elapsed-" + taskid).innerHTML);
  addTask(taskid, elapsed);
  const Http = new XMLHttpRequest();
  const url='http://localhost/taskboard/taskuri/start_task.php?id=' + taskid;
  Http.open("GET", url);
  Http.send();
  Http.onreadystatechange = (e) => {
    if(Http.readyState === 4 && Http.status === 200) {
      console.log(Http.responseText);
    }
  }
}

function stop(id) {
  for (var task of runningTasks) {
    if (task.id === id) {
      // Stop the task
      console.log('task ' + id + " stopped");
      task.running = false;
      console.log(task.elapsed);
      const Http = new XMLHttpRequest();
      const url='http://localhost/taskboard/taskuri/stop_task.php?id=' + id;
      Http.open("GET", url);
      Http.send();
      Http.onreadystatechange = (e) => {
        if(Http.readyState === 4 && Http.status === 200) {
          console.log(Http.responseText);
        }
      }
      break;
    }
  }
  $("#progress-" + id).removeClass("progress-bar-striped");
  $("#progress-" + id).removeClass("progress-bar-animated");
  document.getElementById("start-" + id).disabled = false;
  document.getElementById("stop-" + id).disabled = true;
}

// Continue task progress after refresh
function continueTasks() {
  if (simulated) {
    // 1 minute elapses in 0.5 seconds
    schedule = 2000;
  } else {
    // Real time
    schedule = 60 * 1000; // 60 seconds
  }
  console.log("Continuing tasks...");
  var tasks = document.getElementsByClassName('elapsed');
  console.log("Tasks found: " + tasks.length);
  for (let task of tasks) {
    if (parseInt(task.innerHTML) > 0) {
      // Continue task
      if (document.getElementById("stopped-" + task.id.split("-")[1]).innerHTML === "0") {
        if (document.getElementById("user-role").innerHTML === "Operator") {
          console.log("Continue task " + task.id.split("-")[1]);
          addTask(parseInt(task.id.split("-")[1]), parseInt(task.innerHTML));
        }
      } else {
        console.log("Task '" + task.id.split("-")[1] + "' is stopped");
        var id = task.id.split("-")[1];
        var duration = parseInt(document.getElementById('duration-' + id).innerHTML);
        var durationMinutes = duration * 60;
        if (document.getElementById("user-role").innerHTML === "Operator") {
          document.getElementById("start-" + id).disabled = false;
          document.getElementById("stop-" + id).disabled = true;
        }
        let elapsed = parseInt(document.getElementById("elapsed-" + id).innerHTML);
        task.elapsed = elapsed;
        task.duration = durationMinutes;
        var displayTime = Math.round(task.elapsed / durationMinutes * 100);
        if (displayTime <= 50) {
          $("#progress-" + id).addClass("bg-danger");
        } else if (displayTime <= 80) {
          $("#progress-" + id).removeClass("bg-danger");
          $("#progress-" + id).addClass("bg-warning");
        } else if (displayTime <= 90) {
          $("#progress-" + id).removeClass("bg-warning");
          $("#progress-" + id).addClass("bg-info");
        }
        $("#progress-" + id).css("width", displayTime + "%").text(displayTime + " %");
      }
    }
  }
}

// duration - the task total duration in hours
// durationMinutes - the task total duration in minutes
// task.elapsed - the elapsed time in minutes
function addTask(taskid, elaps) {
  var taskObject = {id: taskid, elapsed: elaps, duration: 0, running: true, update: function(updateTime, id) {
    var duration = parseInt(document.getElementById('duration-' + id).innerHTML);
    var durationMinutes = duration * 60;
    for (var task of runningTasks) {
      if (task.id === id && task.running) {
        document.getElementById("start-" + id).disabled = true;
        document.getElementById("stop-" + id).disabled = false;
        task.elapsed = updateTime;
        task.duration = durationMinutes;
        var displayTime = Math.round(task.elapsed / durationMinutes * 100);
        if (displayTime <= 50) {
          $("#progress-" + task.id).addClass("bg-danger");
        } else if (displayTime <= 80) {
          $("#progress-" + task.id).removeClass("bg-danger");
          $("#progress-" + task.id).addClass("bg-warning");
        } else if (displayTime <= 90) {
          $("#progress-" + task.id).removeClass("bg-warning");
          $("#progress-" + task.id).addClass("bg-info");
        }
        if (displayTime >= 100) {
          $("#progress-" + task.id).removeClass("bg-info");
          $("#progress-" + task.id).addClass("bg-success");
          $("#progress-" + task.id).css("width", 100 + "%").text(100 + " %");
          $("#progress-" + task.id).removeClass("progress-bar-striped");
          $("#progress-" + task.id).removeClass("progress-bar-animated");
          for( var i = 0; i < runningTasks.length; i++) {
            if ( runningTasks[i].id === id) {
              runningTasks.splice(i, 1);
              break;
            }
          }
          // Automatically put task on done
          var taskStatus = document.getElementById('task-status-' + id);
          taskStatus.innerHTML = "Done";
          var durationElem = document.getElementById('duration-' + id);
          durationElem.innerHTML = "0h";
          document.getElementById("start-" + id).disabled = true;
          document.getElementById("stop-" + id).disabled = true;
          $("#start-" + id).removeClass("btn-secondary");
          $("#stop-" + id).removeClass("btn-secondary");
          $("#start-" + id).addClass("btn-light");
          $("#stop-" + id).addClass("btn-light");
          $("#task-status-" + task.id).removeClass("badge-warning");
          $("#task-status-" + task.id).removeClass("badge-danger");
          $("#task-status-" + task.id).addClass("badge-success");
          // And now in the database
          const Http = new XMLHttpRequest();
          const url='http://localhost/taskboard/taskuri/finish_task.php?id=' + id;
          Http.open("GET", url);
          Http.send();
          Http.onreadystatechange = (e) => {
            if(Http.readyState === 4 && Http.status === 200) {
              console.log(Http.responseText);
              console.log('Task ' + id + " done");
            }
          }
        } else {
          // Update task in database
          const Http = new XMLHttpRequest();
          const url='http://localhost/taskboard/taskuri/update_task.php?id=' + id + "&elapsed=" + task.elapsed;
          Http.open("GET", url);
          Http.send();
          Http.onreadystatechange = (e) => {
            if(Http.readyState === 4 && Http.status === 200) {
              console.log(Http.responseText);
              console.log('Task ' + id + " updated " + task.elapsed);
            }
          }
          setTimeout(function() {
            task.update(task.elapsed + 1, id);
            $("#progress-" + id).css("width", displayTime + "%").text(displayTime + " %");
          }, schedule);
        }
        break;
      }
    }
  }};
  $("#progress-" + taskid).addClass("progress-bar-striped");
  $("#progress-" + taskid).addClass("progress-bar-animated");
  runningTasks.push(taskObject);
  taskObject.update(elaps, taskid);
}

//paginare
$(document).ready(function () {
  let t = document.getElementsByClassName('.task_item').length;
  $('.example').rpmPagination({
     limit: 10,
      total: t,
      domElement: '.task_item'
  });
//filtrare
$("#filter").on("keyup", function() {
    var value = $(this).val().toLowerCase();
    $("#table tbody tr").filter(function() {
      $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
    });
  });
});
