<?php
/**
 * Created by PhpStorm.
 * User: zhenkangzhao15mbp
 * Date: 5/2/16
 * Time: 10:16 AM
 */
require_once("models/config.php");
if (!securePage($_SERVER['PHP_SELF'])){die();}

$courseid = intval($_SESSION['classid']);
$course_info = array("name", "url");
$documents = fetchDocumentByCourseId($courseid);

//$arrlength = count($courseinfo_array);
//$courseinfo_array = fetchStudentCouseId($couseid);
//print_r($courseinfo_array);

require_once("models/header.php");
echo "
<body>
<div id='wrapper'>
<div id='top'><div id='logo'></div></div>
<div id='content'>
<h1>UserCake</h1>
<h2>Admin Permissions</h2>
<div id='left-nav'>";

include("left-nav.php");

echo "
</div>
<div id='main'>";

foreach ($documents as $d) {
    echo "
	<tr>
    <td><p>Documents</p><a href=".$d['url'].">".$d['name']."</a></td>
	</tr>";
}

?>