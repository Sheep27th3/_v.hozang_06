<?php
if(session_status() == PHP_SESSION_NONE){
    session_start();
}

$_SESSION['admin_id'] = 1;
?>