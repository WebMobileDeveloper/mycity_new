<?php
ob_start();
include_once("header.php");
if (!isset($_SESSION['user_id']))
{
	header('location: index.php'); 
}
include_once 'includes/db.php';
include_once 'includes/functions.php'; 
?>
 
    


<a href="<?php echo $googleImportUrl; ?>"> Import google contacts </a>

 

<?php include('footer.php'); ob_end_flush();?>