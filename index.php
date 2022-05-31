<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Files Browser PHP</title>
    <style>
    <?php include 'style.css';
    ?>
    </style>
</head>

<body>
    <h1>Files Browser</h1>
    <?php 
    // upload files logic
   if(isset($_FILES['file-name'])){
    $errors = "";
    $file_name = $_FILES['file-name']['name'];
    $file_size = $_FILES['file-name']['size'];
    $file_tmp = $_FILES['file-name']['tmp_name'];
    $file_type = $_FILES['file-name']['type'];
     // check extension 
    $file_ext = strtolower(end(explode('.',$_FILES['file-name']['name']))); 
    $extensions = array("jpeg","png","pdf");
    if(in_array($file_ext, $extensions) === false){
        $errors = '<div class= "msg error" >File format not allowed !!! Please choose JPEG, PNG or PDF file. </div>';
        }
    if($file_size > 3000000) {
        $errors = '<div class= "msg error">File size is too big !!! (MAX 3 MB)</div>';
        }
    if (file_exists($file_name)){
       $errors = '<div class= "msg error">File with the same name already exists !!! </div>'; 
    }
    if(empty($errors) == true) {
        move_uploaded_file($file_tmp, './' . $_GET['path'] . $file_name); 
        echo '<div class= "msg success"> File successfully uploaded !!! </div>'; 
    } else {
        print_r($errors);
        }
    }
    // wrapper
    echo '<div class="new-dir_and_upload">';
    // create new dir
    echo '<form class="new-dir" action="" method="POST">
            <input class=" new-dir-input" type="text" placeholder="Create new directory" name="new_dir" value="">
            <button type="submit">Create New</button>
          </form>';
    // upload files
    echo '<form class="upload" action="" method="post" enctype="multipart/form-data">
            <input class="upload-input" type=file name="file-name">
            <button type="submit">Upload</button>
        </form>';
    echo '</div>';
    // table
    echo "<table><tr><th>Type</th><th>Name</th></tr> ";
    $path='./' . $_GET["path"]; 
    $dir= scandir($path);
    $full_path= $_SERVER['REQUEST_URI'].$value;
    $go_back = "?path=". ltrim(dirname($_GET["path"]),"./")."/";
    echo $go_back;
    echo '<br>';
    echo $full_path;
    echo '<h3>Directory contents: '.str_replace('?path=/','',$_SERVER['REQUEST_URI']).'</h3>';
        
    foreach ($dir as $value){
    if ($value != '..' && $value !='.' && $value != '.git' && $value != '.DS_Store'){
        // checks type
        echo '<tr>'.'<td>';
        if (is_dir($path.$value))
        echo 'Directory'; 
        else  echo 'File'.'</td>';
        // display files
        echo '<td>'.(is_dir($path.$value)? '<a href="'.(isset($_GET['path'])
        ? $_SERVER['REQUEST_URI'].$value.'/'
        : $_SERVER['REQUEST_URI'].'?path='.$value.'/').'">'.$value.'</a>'
        : $value); '</td>';  
    }
        }
    // create new dir logic
    foreach ($dir as $value) {
    if (isset($_POST['new_dir']) && (!file_exists($_POST['new_dir']))){     
     mkdir($path. '/'.$_POST['new_dir']);
     header("refresh: 1");
    } 
}  
    echo "</table>"; 
    // go back
    echo "<button><a href= '$go_back' >Back</a></button>";
?>
</body>

</html>