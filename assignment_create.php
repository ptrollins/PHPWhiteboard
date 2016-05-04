<?php
/*
UserCake Version: 2.0.2
http://usercake.com
*/

require_once("models/config.php");
if (!securePage($_SERVER['PHP_SELF'])){die();}
$classid = intval($_SESSION['classid']);
//Forms posted
if(!empty($_POST))
{
    $errors = array();
    $assignname = trim($_POST["assignname"]);
    $description = trim($_POST["description"]);
    $duedate = trim($_POST["duedate"]);


    if($assignname == "")
    {
        $errors[] = "enter assignment name";
    }

    if($description == "")
    {
        $errors[] = "enter assignment description";
    }

    if($duedate == "")
    {
        $errors[] = "enter due date";
    }

    //End data validation
    if(count($errors) == 0)
    {
        $createBlog = createCourse($coursename, $instructor, $schedule, $location);
        print_r($createBlog);
        if($createBlog <> 1){
            $errors[] = "OOOPPSS!! your assignment could not be created";
        }

    }
    if(count($errors) == 0) {
        $successes[] = "Assignment successfully created";
    }
}

require_once("models/header.php");
echo "
<body>
<div id='wrapper'>
<div id='top'><div id='logo'></div></div>
<div id='content'>
<h1>Pace University</h1>
<h2>Create Assignment</h2>

<div id='left-nav'>";
include("left-nav.php");
echo "
</div>

<div id='main'>";

echo resultBlock($errors,$successes);
echo "
<div id='regbox'>
<form name='newAssignment' action='".$_SERVER['PHP_SELF']."' method='post'>

<p>
<label>Name:</label>
<input type='text' name='assignname' />
</p><br>
<p>
<label>Description:</label>
<textarea name='description'></textarea>
</p><br>
<p>
<label>Due Date:</label>
<input type='text' name='duedate' />
</p>
<label>&nbsp;<br>
<input type='submit' value='Create'/>
</p>

</form>
</div>

</div>
<div id='bottom'></div>
</div>
</body>
</html>";

