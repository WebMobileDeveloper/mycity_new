<?php
include("header.php");
include_once 'includes/db.php';
include_once 'includes/functions.php';
//print_r($_POST);
$city_search = str_replace('+','',$_POST['city_search']);
$zip_search = str_replace('+','',$_POST['zip_search']);
$interests_search = str_replace('+','',$_POST['interests_search']);
$sqlQuery = "SELECT ud.*,u.username FROM `user_details` as ud left join mc_user as u on u.id=ud.user_id WHERE upd_public_private='public' AND user_status='1' ";
if($city_search!=''){
	$sqlQuery .= " AND ud.city LIKE '%".$city_search."%' ";
}
if($zip_search!=''){
	$sqlQuery .= " AND ud.zip LIKE '%".$zip_search."%' ";
}
if($interests_search!='' && $interests_search!='null'){
	$sqlQuery .= " AND FIND_IN_SET('$interests_search',ud.vocations) ";
}
$sqlQuery .= "  order by ud.id asc ";
//echo $sqlQuery;
$posts = $link->query($sqlQuery);
 if(isset($_POST)){
	if($_user_role != 'admin'){
		//echo "INSERT INTO `home_search_log` (`city`, `zip`, `vocation`, `created_at`) VALUES ('".$city_search."','".$zip_search."','".$interests_search."','".date("Y-m-d H:i:s")."')";
		$link->query("INSERT INTO `home_search_log` (`city`, `zip`, `vocation`, `created_at`) VALUES ('".$city_search."','".$zip_search."','".$interests_search."','".date("Y-m-d H:i:s")."')");
	}
 }
?>
<div id="fb-root"></div>
	<div id="contact" class="about">
		<div class="container">
			<div class="row">
				<div class="col-md-12 col-sm-12 col-xs-12">
					<h4 style="margin-bottom: 65px; margin-top: 150px; font-weight: bold;">Users </h4>
				</div> 
			</div>
		</div>
	</div>
	<div class="container">
        <div class="row">
<div class="col-xs-12 col-sm-12 col-md-12"> 
<?php 
	if($posts->num_rows > 0)
	{
		while($row = $posts->fetch_array())
		{
			$about_your_self = $row['about_your_self']; 
			$username = $row['username'];
			
			echo "<div class='postbox'>
					<div class='posttitle'>
					<h3>" . $username . "</h3>
				</div>
				<div class='postcontent'>".$about_your_self."</div> 
					<a href='/' ><button type='button' class='btn pull-right' >Join Mycity.com - Meet Members</button></a>
				</div> " ;
		}  
		
	}else{
		
			echo "<div class='postbox'>
					<div class='posttitle'>
					<h3>No Matching Record Found!</h3>
					</div>
				  </div> " ;
	} 

?>  
</div></div> </div>
			
<?php include("footer.php") ?>
