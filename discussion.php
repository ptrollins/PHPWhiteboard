<?php
require_once("models/config.php");
if (!securePage($_SERVER['PHP_SELF'])){die();}
require_once("models/header.php");

// call to fetchallblogs function from functions.php
$allblogs = fetchClassBlogs();

echo "
<body>
    <div id='wrapper'>
        <div id='content'>
            <h2>Blogs, Blogs and More Blogs!!!</h2>

            <div id='left-nav'>";
include("left-nav.php"); //navigation menu bar.

echo "
            </div> <!--- leftnav div ends -->

            <div id='main'>
                <div id='content'>";
if (isset($allblogs)){
foreach($allblogs as $bloginfo) {
    $summary = truncate_chars($bloginfo['content'], 150, '...');
    //$summary  = $bloginfo['blogcontent'];
    $publisheddate = date("M d, Y", $bloginfo['datetime']);

    echo "
                <div class='post'>
                    <h2><a href='viewblog.php?blogid=" . $bloginfo['blogid'] . "'>" . $bloginfo['title'] . "</a></h2>
                    <br>
                    <p>" . $summary . "</p>
                    <div class='post-info'>
                        <ul>
                            <li>BY : " . $bloginfo['username'] . "</li>
                            <li class='date'>$publisheddate</li>
                            <li class='read-more'><a href='viewblog.php?blogid=" . $bloginfo['blogid'] . "'>Read more</a></li>
                        </ul>
                    </div>
                </div>
            ";
}}
echo "<a href='createblog.php'><button>Create Discussion Post</button></a>";
echo "
                </div>
            </div>
        </div>
        <div id='bottom'></div>
    </div>
</body>
</html>
";
