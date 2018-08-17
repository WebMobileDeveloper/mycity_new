<?php 
include_once 'includes/db.php';
include_once 'includes/functions.php';

//rescanning image 

 $queries = array();


function fetchZipCodes($userid, $link )
{

	$results = $link->query("SELECT * FROM referralsuggestions where knowenteredby='$userid' and isdeleted='0' AND ( sourcezip IS NULL OR sourcezip = '' OR  targetzip IS NULL OR targetzip ='') ");
	$i=0;

	global $queries ;
	
	if($results->num_rows > 0)
	{

		echo "<table class='table table-bordered table-striped'>";
		echo "<tr><td>ID</td><td>Know To Refer</td><td>Know Referred To</td><td>Source Zip</td><td>Target Zip</td><td>Action</td></tr>";
		
		while($row = $results->fetch_array())
		{
			$introid = $row['knowtorefer'];
			$introduceto = $row['knowreferedto'];

			$zip1row = $link->query("SELECT * FROM user_people where id='$introid'");
			if($zip1row->num_rows > 0)
			{
				$zip1 = $zip1row->fetch_array()['client_zip'];
			}
			$zip2row = $link->query("SELECT * FROM user_people where id='$introduceto'");
		    if($zip2row->num_rows > 0)
			{
				$zip2 = $zip2row->fetch_array()['client_zip'];
			}

			echo "<tr>";
			echo "<td>" . $row['id'] . "</td>";
			echo "<td>" . $row['knowtorefer'] . "</td>";
			echo "<td>" . $row['knowreferedto'] . "</td>";
			echo "<td id='sz'>"  .   $zip1  .  "</td>";
			echo "<td id='sz'>"  .   $zip2 .  "</td>";
			echo "<td id='sz'><button data-id='" . $row['id'] . "' data-zip1='" . $zip1 . "' data-zip2='" . $zip2  . "'   class='btn btn-primary btn-sm update'>Update Zip Code</button></td>";
			echo "</tr>";
		}

		echo "<tr>";
		echo "<td colspan='4'></td><td ><button  class='btn btn-primary btn-sm updateallzip'>Update All</button></td><td></td>";
		echo "</tr>";
		echo "</table>";
	}
	$link->close(); 

} 
 
 
  

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="author" content="KeyDesign"/>
    <meta name="description" content="AppSperia - App Landing Page"/>
    <meta name="keywords" content="AppSperia , Landing page, Template, App, Mobile, Android, iOS"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <title></title>
    <link rel="stylesheet" href="css/default.css"/>
    <link rel="stylesheet" href="css/style.css"/>
    <link rel="stylesheet" href="css/style_2.css"/>
    <link rel="stylesheet" href="css/font-awesome.min.css">
    <link rel="stylesheet" href="css/owl.theme.css">
    <link rel="stylesheet" href="css/owl.carousel.css">
    <link rel="stylesheet" href="css/bootstrap.min.css"/>
    <link rel="stylesheet" href="css/custom.css"/>
	<link rel="stylesheet" href="css/dropdown.css"/>
	<link rel="stylesheet" href="css/light.css"/>
	<link rel="stylesheet" href="css/jquery-ui.min.css">
	<link rel="stylesheet" href="css/chosen.css">

    <script src="js/jquery.js" type="text/javascript"></script>
    <script src="js/bootstrap.min.js" type="text/javascript"></script>
    
	<script src="js/custom.js" type="text/javascript"></script>
 
</head>
<body class="no-padd" style="padding: 0 !important; ">

<div class='container'>
<div class='row'>
<div class='col-md-12'>
<?php 
fetchZipCodes('19', $link );
?>
</div>
</div>
</div>
<script>
 
 $(document).on('click', '.update', function(){

   var id = $(this).data('id');
   var zip1 = $(this).data('zip1');
   var zip2 = $(this).data('zip2');

	if(zip1 !="" && zip2 != "")
	{
		$.ajax({
			type: 'post',
			url: 'includes/ajax.php',
			data: { updatezips:1, zip1: zip1, zip2: zip2, id: id } ,
			success: function (data)
			{  
				console.log(data);
				alertFunc('success', 'Zip updated!') ; 
		    }
	   	}); 
	}

})


 $(document).on('click', '.updateallzip', function(){
 	
 	 $('.update').each(function() {
   			
 	 	var id = $(this).data('id');
 	 	var zip1 = $(this).data('zip1');
 	 	var zip2 = $(this).data('zip2');
 

 	    if (  $.trim(zip1)  !="" && $.trim( zip2 ) !="")

		{ 
			 $.ajax({
				type: 'post',
				url: 'includes/ajax.php',
				data: { updatezips:1, zip1: zip1, zip2: zip2, id: id } ,
				success: function (data)
				{  
					console.log(data);
					alertFunc('success', 'Zip updated!') ; 
			    }
		   	});
		 
		}

	});


 })

 


 


</script> 
</body>
</html>