<?php
namespace Isokissa\Alleswisser;

require_once( "vendor/autoload.php");
?>

<!DOCTYPE html>
<html>
<header>
    <meta charset="utf-8"/>
    <link href="stylesheet.css" rel="stylesheet"/>


</header>
<body>

<?php

$dataMap = new NoSQLiteDataMap( __DIR__."/alleswisser.db" );
$model = new Model( $dataMap ); 
$view = new QuestionView();
$controller = new QuestionController($model, $view); 

if (isset($_GET['action'])){
    echo $controller->{$_GET['action']}($_POST);
}
else {
    echo $controller->defaultAction();
}

?>

</body>
</html>
