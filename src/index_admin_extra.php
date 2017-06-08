<?php
session_start();

if (!isset($_SESSION["u"]) || $_SESSION['level']!=9) {
	header("Location: login.php");
	exit ;
}






?>