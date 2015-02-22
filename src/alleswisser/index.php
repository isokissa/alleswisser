<?php
namespace Isokissa\Alleswisser;

require_once( "Model.php" );
require_once( "QuestionView.php" );
?>

<!DOCTYPE html>
<html>
<header>
    <meta charset="utf-8"/>
    <link href="stylesheet.css" rel="stylesheet"/>


</header>
<body>

<?php


$model = new Model(); 
$view = new QuestionView();
$controller = new QuestionController($model, $view); 

if (isset($_GET['action'])){
    echo $controller->{$_GET['action']}($_POST);
}
else {
    echo $view->outputQuestion( 1 );
}

?>

</body>
</html>
