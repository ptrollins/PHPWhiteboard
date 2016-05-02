<?php

require_once("models/config.php");
if (!securePage($_SERVER['PHP_SELF'])){die();}
require_once("models/header.php");

//$thisblogid = $_GET['blogid'];
//$thisblog = fetchThisBlog($thisblogid);

$listofblogs = fetchClassBlogs();

echo "
<body>
<div id='wrapper'>
    <div id='top'>
            <div id='logo'></div>
        </div>
    <div id='content'>

    <h2>Blogs, Blogs and More Blogs!!!</h2>

<div id='left-nav'>";
include("left-nav.php");

echo "</div>
      <div id='main'>
        <a href='createblog.php'><button>Create Discussion Post</button></a>
        <br><br><br>
        </div><table>";
foreach ($listofblogs as $displayblog)
{
    echo '<tr>';
    echo '<td>'. $displayblog['blogid'] .'</td>';
    echo '<td>'. $displayblog['title'] .'</td>';
    echo '<td><a href="viewblog.php?blogid='.$displayblog['blogid'].'">View</a></td>';
    //echo '<td align="center"><a href="publish.php?blogid='.$displayblog['blogid'].'">'.$displayblog['title'].'</a></td>';
    echo '<td><a href="deleteblog.php?blogid='.$displayblog['blogid'].'">Delete</a></td>';
    echo '</tr>';
}

//echo "<h1 style='color: red;'>"; print $thisblog['title']; echo "</h1>";
//echo "<br><br><br><br>";
//echo "<h3 style='color:blue;'>"; print $thisblog['blogcontent']; echo "</h3>";

echo"</table><div id='bottom'></div>
    </div>
    </div>
</body>
</html>";

?>
