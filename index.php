<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Files System Browser PHP</title>
    <style>
    <?php include 'style.css';
    ?>
    </style>
</head>

<body>
    <?php
      session_start();

      if (isset($_GET['action']) and $_GET['action'] == 'logout') {
         session_destroy();
         session_start();
      }

      $login_msg = '';
      if (isset($_POST['login']) && !empty($_POST['username']) && !empty($_POST['password'])) {
         if ($_POST['username'] == 'Ieva' && $_POST['password'] == '1234') {
            $_SESSION['logged_in'] = true;
            $_SESSION['username'] = $_POST['username'];
         } else {
            $login_msg = 'Wrong username or password !!!';
         }
      }
   ?>
    <div class="login-wrapper"
        <?php isset($_SESSION['logged_in']) && $_SESSION['logged_in'] == true
            ? print("style = 'display: none'")
            : print("style = 'display: flex; flex-direction: column; justify-content: center; align-items: center;'") ?>>
        <form class="login-form" action="" method="POST">
            <h4 class='msg login'><?php echo $login_msg; ?></h4>
            <h2>Login</h2>
            <div class="input-group">
                <input type="text" name="username" placeholder="Ieva" required>
                <label for="username">User Name</label>
            </div>
            <div class="input-group">
                <input type="password" name="password" placeholder="1234" required>
                <label for="password">Password</label>
            </div>
            <input class="submit-btn" type="submit" name="login" value="Login" formaction="./">
        </form>
    </div>
    <?php 
    if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] == true) {
    $log_out = './?action=logout';
    echo '<header><h1>Files System Browser</h1></header>';
    $path='./' . $_GET["path"]; 
    $dir= scandir($path);
    $go_back = "?path=". ltrim(dirname($_GET["path"]),"./")."/";
    
    
    // create new dir logic
    if (isset($_POST['new_dir']) && (!file_exists($_POST['new_dir']))){     
    mkdir($path. '/'.$_POST['new_dir']);
    header("refresh: 1");
    } 
     else if (isset($_POST['new_dir']) && file_exists($path. '/'.$_POST['new_dir'])) {
    echo '<div style="text-align: left; margin-left: 10%" class="msg error"> Directory named "' . $_POST['new_dir'] . '" already exists !!!</div>';}

    // upload files logic
   if(isset($_FILES['file-name'])){
    $errors = "";
    $file_name = $_FILES['file-name']['name'];
    $file_size = $_FILES['file-name']['size'];
    $file_tmp = $_FILES['file-name']['tmp_name'];
    $file_type = $_FILES['file-name']['type'];
     // check extension 
    $file_ext = strtolower(end(explode('.',$_FILES['file-name']['name']))); 
    $extensions = ["jpg","png","pdf"];
    if(in_array($file_ext, $extensions) === false){
        $errors = '<div class= "msg error" >File format not allowed !!! Please choose JPG, PNG or PDF file. </div>';
        }
    if($file_size > 3000000) {
        $errors = '<div class= "msg error">File size is too big !!! (MAX 3 MB)</div>';
        }
    // unique name for the file
    time();
    $ext = pathinfo(path:$file_name, flags:PATHINFO_EXTENSION);
    $file_name= time() .'.'.$ext;
    if(empty($errors) == true) {
        move_uploaded_file($file_tmp, './' . $_GET['path'] . $file_name); 
        echo '<div class= "msg success"> File successfully uploaded !!! </div>'; 
        header("refresh: 1"); 
    } else {
        print_r($errors);
        } 
    }
    // download file logic
    if(isset($_POST['download'])){
     print('Path to download: ' . $path . $_POST['download']);
    $fileToDownloadEscaped = str_replace(" ", "_", htmlentities($path, 0, 'utf-8'));
    ob_clean();
    ob_start();
    header('Content-Description: File Transfer');
    header('Content-Type: application/pdf'); 
    header('Content-Disposition: attachment; filename= '.basename($fileToDownloadEscaped));
    header('Content-Transfer-Encoding: binary');
    header('Expires: 0');
    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
    header('Pragma: public');
    header('Content-Length: ' . filesize($fileToDownloadEscaped)); 
    ob_end_flush();
    readfile($fileToDownloadEscaped);
    exit;
        }
    // delete logic
    if (isset($_POST['delete'])){
        if(is_file($_POST['delete'])) {
            unlink($_POST['delete']);
        } 
        header("refresh: 1");  
    }  
    // wrapper
    echo '<div style="display: flex; flex-direction: column;"><div class="new-dir_and_upload">';
    // create new dir
    echo '<form class="new-dir" action="" method="POST">
            <input class=" new-dir-input" style="margin: 1rem; padding: 0.5rem 1.5rem; border-radius: 0.3rem; border: 1px solid rgb(101, 100, 96); outline: none;" type="text" placeholder="Directory name" name="new_dir" value="">
            <button type="submit">Create New</button>
          </form>';
    // upload files
    echo '<form class="upload" action="" method="post" enctype="multipart/form-data">
            <input class="upload-input" style="margin: 1rem; padding: 0.5rem 1.5rem;border-radius: 0.3rem; border: 1px solid rgb(101, 100, 96); outline: none;" type=file name="file-name">
            <button type="submit">Upload</button>
        </form>';
    echo '</div>';
    // table
    echo '<div style="display: flex; justify-content: center"><table style="background: rgb(235, 232, 232); color: rgba(0, 0, 0, 0.7); box-shadow: 0 1rem 1.5rem rgba(0, 0, 0, 0.5);"><tr><th>Type</th><th>Name</th><th style="text-align: center">Download</th><th style="text-align: center">Delete</th></tr> ';
    foreach ($dir as $value){
    if ($value != '..' && $value !='.' && $value != '.git' && $value != '.DS_Store' && $value != 'index.php' && $value != 'style.css' ){
        // checks type
        echo '<tr>'.'<td><div class="file-or-dir">';
        if (is_dir($path.$value))
        echo 'Directory'; 
        else  echo 'File </div></td>';
        // display files
        echo '<td><div class="file-display" style= "display: flex; min-width: 40vw;">'.(is_dir($path.$value)? '<a style= "color: rgba(0, 0, 0, 0.7);" href="'.(isset($_GET['path'])
        ? $_SERVER['REQUEST_URI'].$value.'/'
        : $_SERVER['REQUEST_URI'].'?path='.$value.'/').'">'.$value.'</a>'
        : $value); '</td>';
        // download file
        echo '</div><td>';
            if(is_file($path.$value)){
                echo('<form style="display: flex; justify-content: center" action=" ?path=' . $path.$value . ' " method="POST">');
                echo('<button type="submit" name="download" value='.$path.$value.'>Download</button>');
                echo('</form>');
            }
        echo '</td>'; 
        // delete file 
        echo '<td>'.
        (is_dir($path.$value) 
        ?''
        : '<form style= "display: flex; justify-content: center" action="" method="post">
           <button type ="submit" name="delete" value ='.$path.$value.'>Delete</button>
           </form>');
        echo '</td>';   
    }
}
    echo "</table></div>"; 
    // go back and logout
    echo '<div style= "display: flex; justify-content: space-between; margin-top: 3rem" >';
    echo '<button style="width: 20vw; justify-content: center; margin-left: 10%"><a style="text-decoration: none; color: rgb(235, 232, 232);" href= '.$go_back.'>Back</a></button>';
    echo '<button style="width: 20vw; justify-content: center; margin-right: 10%"><a style="text-decoration: none; color: rgb(235, 232, 232);" href= '.$log_out.'>Logout</a></button>';
    echo '</div>';
    echo '</div>'; }
?>
</body>

</html>