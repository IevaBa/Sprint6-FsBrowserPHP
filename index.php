<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Files Browser PHP</title>
    <link rel="stylesheet" href="style.css">

</head>

<body>
    <h1>Files Browser</h1>
    <?php echo "<table><tr><th>Type</th><th>Name</th></tr> ";
    $path='./' . $_GET["path"]; 
    $dir= scandir($path);
    $full_path= $_SERVER['REQUEST_URI'].$value;
    $go_back = "?path=". ltrim(dirname($_GET["path"]),"./")."/";
    echo $go_back;
    echo '<br>';
    echo $full_path;
    echo '<h3>Directory contents: '.str_replace('?path=/','',$_SERVER['REQUEST_URI']).'</h3>';
        
    foreach ($dir as $value){
    if ($value != '..' && $value !='.'){
        // checks type of the file
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
    echo "</table>";
    // go back
    echo "<button><a href= '$go_back' >Back</a></button>";
?>
</body>

</html>