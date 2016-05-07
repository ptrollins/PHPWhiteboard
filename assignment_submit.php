<?php

require_once("models/config.php");
if (!securePage($_SERVER['PHP_SELF'])){die();}
$classid = intval($_SESSION['classid']);
if(isset($_GET['a'])) {
    $_SESSION['assignid'] = $_GET['a'];
}if(!isset($_SESSION['assignid'])){
    header("Location: account.php"); die();
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
    <form name='submitAssignment' action='assignments.php' method='post' enctype='multipart/form-data'>  
        <p>
            <label>Select file:</label>
            <input type='file' name='assignmentfile' />
        </p><br>
        <p><input type='submit' name='submit' value='Submit'></p>
    </form>
</div>

</div>

<div id='bottom'></div>
</div>
</body>
</html>";

