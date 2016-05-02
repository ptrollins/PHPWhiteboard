<?php


require_once("models/config.php");
if (!securePage($_SERVER['PHP_SELF'])){die();}

//print_r($_POST);
//Forms posted

if(!empty($_POST))
{
  $errors = array();
  $title = trim($_POST["title"]);
  $blog = trim($_POST["blog"]);

  if($title == "")
  {
    $errors[] = "enter a title";
  }

  if($blog == "")
  {
    $errors[] = "enter blog contents";
  }


  //End data validation
  if(count($errors) == 0)
  {

    $createBlog = createBlog($title, $blog);
    print_r($createBlog);
    if($createBlog <> 1){
      $errors[] = "OOOPPSS!! your blog could not be created";
    }
  }
  if(count($errors) == 0) {
    $successes[] = "blog is now available";
  }
}

require_once("models/header.php");
echo "
<body>
<div id='wrapper'>

<div id='content'>

<h2>Create New Blog</h2>

<div id='left-nav'>";
include("left-nav.php");
echo "
</div>

<div id='main'>";

echo "<pre>";
print_r($errors);
print_r($successes);
echo "</pre>";

echo "
<div id='regbox'>
<form name='newUser' action='".$_SERVER['PHP_SELF']."' method='post'>

<p>
<label>Title:</label>
<input type='text' name='title' />
</p>
<p>
<label>Blog Content:</label>
<textarea name='blog' rows='25' cols='40'></textarea>
</p>


<label>&nbsp;<br>
<input type='submit' value='createBlog'/>
</p>

</form>
</div>

</div>
<div id='bottom'></div>
</div>
</body>
</html>";
?>
