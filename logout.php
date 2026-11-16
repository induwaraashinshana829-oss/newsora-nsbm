<?php require __DIR__.'/includes/functions.php'; if($_SERVER['REQUEST_METHOD']==='POST')
{check_csrf();$_SESSION=[];session_destroy();
go('login.php');} go('settings.php');