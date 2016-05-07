<?php

require_once("models/config.php");
if (!securePage($_SERVER['PHP_SELF'])){die();}

$courseid = intval($_SESSION['classid']);
$documents = fetchDocumentByCourseId($courseid);

require_once("models/header.php");
echo "
<body>
<div id='wrapper'>
<div id='top'><div id='logo'></div></div>
<div id='content'>
<h1>Whiteboard</h1>
<h2>Documents</h2>
<div id='left-nav'>";

include("left-nav.php");

echo "
</div>
<div id='main'>";

if(isset($documents)) {
    foreach ($documents as $d) {
        echo "
        <tr>
        <td><p>Documents</p><a href=" . $d['url'] . ">" . $d['name'] . "</a></td>
        </tr>";
    }
}