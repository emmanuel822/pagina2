<?php session_start();
if(!isset($_SESSION['nombre'])){
   header('location: login.php');
}
?>
<?php include('views/home.view.php');?>