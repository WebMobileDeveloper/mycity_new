<?php 
include_once 'includes/db.php';
include_once 'includes/functions.php';

//rescanning image 

 $queries = array();


function updateZipCodes($userid, $link )
{

	$results = $link->query("SELECT * FROM referral_suggestions Order BY partnerid  ");
	$i=0;

	global $queries ;

	if($results->num_rows > 0)
	{
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
  			if($zip1 !="" &&  $zip2 != "")
  			{
  				$queries[$i] = "UPDATE referral_suggestions SET sourcezip='$zip1', targetzip='$zip2' WHERE id ='" . $row['id'] . "';"  ;$i++; 
  			}
			

			//echo "Processed record # " . $i . " Distance calculated" . $distanceinmiles . "<br/>";
			
		}  
	}
	$link->close(); 

} 


function processQuery()
{
	global $queries;
	  
	for($i=0; $i< sizeof($queries); $i++)
	{
		 
		echo $queries[$i]. "<br/>";

	}

}

updateZipCodes('19', $link );

processQuery();




?>