<?php



require_once("models/config.php");
if (!securePage($_SERVER['PHP_SELF'])){die();}
require_once("models/header.php");

$myblogs = fetchMyBlogs();
//print_r($myblogs);


echo "
<body>
<div id='wrapper'>

<div id='content'>

<h2>Blogs, Blogs and More Blogs!!!</h2>

<div id='left-nav'>";
include("left-nav.php");

echo "
</div>
<div id='main'>

   <table border='2'>
    <tr>
        <th>BlogID</th>
        <th>Title</th>
        <th>View</th>
        <th>Publish</th>
        <th>Delete</th>
    </tr>";
foreach($myblogs as $displayblog)
{
    echo '<tr>';
    echo '<td>'. $displayblog['blogid'] .'</td>';
    echo '<td>'. $displayblog['title'] .'</td>';
    echo '<td><a href="viewblog.php?blogid='.$displayblog['blogid'].'">View</a></td>';
    echo '<td align="center"><a href="publish.php?blogid='.$displayblog['blogid'].'">'.$displayblog['active'].'</a></td>';
    echo '<td><a href="deleteblog.php?blogid='.$displayblog['blogid'].'">Delete</a></td>';
    echo '</tr>';
}

echo "</table>
</div>
<div id='bottom'></div>
</div>
</body>
</html>";