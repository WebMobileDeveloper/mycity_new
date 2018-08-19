<?php
include("header.php");
include_once 'includes/db.php';
include_once 'includes/functions.php';
if (isset($_SESSION['user_id']))
{
    header('location: dashboard.php');
}
$groups = getGroups($link);
$vocations = getVocations($link);

if(isset($_POST['btnlandingsignup']))
{
    $landingzip = $_POST['landingzip'];
    $landingcity = $_POST['landingcity']; 
}

if(  $_SERVER['HTTP_HOST'] == "localhost" || $_SERVER['HTTP_HOST'] == "mycity.dev")
{
    $siteurl = 'http://'. $_SERVER['HTTP_HOST'] . "/";
} 
else
{
    $siteurl =  'https://mycity.com/';
}
$param = array('id' => '0'); 
$groups = json_decode(   curlexecute($param, $siteurl . 'api/api.php/groups/'), true); 
	$vocations =    json_decode(   curlexecute($param, $siteurl . 'api/api.php/vocations/'), true); 
	$cities = json_decode(   curlexecute($param, $siteurl . 'api/api.php/cities/'), true);  

	
$citynames ='';
$grouplist ='';
foreach ($groups as $group)
{
	if($group['grp_name'] != '')
		$citynames .= "<option value='" . $group['grp_name'] . "'>" . $group['grp_name'] . "</option>";
 
	$grouplist .= "<option value='" . $group['id'] . "'>" . $group['grp_name'] . "</option>";
}


if( isset($_POST['tbsearchbycity']) || isset($_POST['tbsearchbyvoc'])   )
{
    $city = $_POST['tbsearchbycity'];
    $vocation  = $_POST['tbsearchbyvoc'];
    $param = array( 'city' =>  $city,'vocation' =>  $vocation, 'goto' => '1', 'userid' =>  $_SESSION['user_id'] );
    $businesslistings = json_decode(   curlexecute($param, $siteurl . 'api/api.php/member/business/search/'), true);
	  
}
?> 
    <section id="main-section" class="secblue" >
        <div class="container  ">
            <div class="row">
			<div col-md-12>
				<h1 class='page-heading'>Search Results for Businesses </h1>
			</div>
			<div class='col-md-3'>
				<div class="panel panel-default panel-search"> 
            <div class="panel-body">
			<p class='txt-lg'>Search Business</p> 
                 <div class="form-group">  
 
 <select data-placeholder="City" id="tbsearchbycity" name="tbsearchbycity"  class="form-control  "  > 
                            <option value=''>Select City</option> 
                             <?php
                            echo $citynames; 
                            ?>
                        </select>
  </div>
  <div class="form-group">   
   <select data-placeholder='Vocations ...' class="form-control    " name="tbsearchbyvoc" id="tbsearchbyvoc"  > 
       <?php
	   foreach ($vocations as $vocation) 
	   {
		   echo "<option value='" . $vocation['voc_name'] . "'>" . $vocation['voc_name'] . "</option>";
		}
	  ?>
  </select>  
  </div>
  <button type="submit" id="form_search_business" class="flatbutton">Search</button>
  </div>
  </div>  
  </div>
			<div class="col-md-9"> 
                    <div id='businesslisting'>
                    <?php 
					
					 
					if(  sizeof(  $businesslistings ) == 0 ||  sizeof(  $businesslistings['results']  ) == 0 )
					{
						?>
					 <div class='row'><div class='col-md-8 col-md-offset-2'><p class='alertmsg'>No matching businesses found!</p></div></div>
					<?php 
					}
					else 
					{  
                       foreach ($businesslistings['results'] as $item)
                       {
                           $user_picture = "images/"  .  $item['image']; 
                           echo  '<div class="col-md-12"><div class="panel panel-default">
                           <div class="panel-body">
                           <h2><strong>' . $item['busi_name']  . '</strong></h2>';
						   echo "<p><small> " . $item['busi_location']  . "</small></p><hr/>";
                           echo '<div class="row">
                           <div class="col-md-3 text-center">'; 
                           echo "<img src='"  . $user_picture  . "' alt='"  . $item['username']  .  "' class='img-rounded img-ctr' height='120' width='120'>";
						   echo "<div class='text-center'>";
						   if( $item['rate'] == 25)
						   {
							   echo "<i title='5 star rated' class='fa fa-star orange star-sm'></i><i title='5 star rated' class='fa fa-star orange star-sm'></i><i title='5 star rated' class='fa fa-star orange star-sm'></i><i title='5 star rated' class='fa fa-star orange star-sm'></i><i title='5 star rated' class='fa fa-star orange star-sm'></i>";
						   }
						   else 
							   if( $item['rate'] == 20)
						   {
							   echo "<i title='4 star rated' class='fa fa-star orange star-sm'></i><i title='4 star rated' class='fa fa-star orange star-sm'></i><i title='4 star rated' class='fa fa-star orange star-sm'></i><i title='4 star rated' class='fa fa-star orange star-sm'></i><i title='4 star rated' class='fa fa-star gray star-sm'></i>";
						   }
						   else 
							   if( $item['rate'] == 15)
						   {
							   echo "<i title='3 star rated' class='fa fa-star orange star-sm'></i><i title='3 star rated' class='fa fa-star orange star-sm'></i><i title='3 star rated' class='fa fa-star orange star-sm'></i><i title='3 star rated' class='fa fa-star gray star-sm'></i><i title='3 star rated' class='fa fa-star gray star-sm'></i>";
						   }
						   else 
							   if( $item['rate'] == 10)
						   {
							   echo "<i title='2 star rated' class='fa fa-star orange star-sm'></i><i title='2 star rated' class='fa fa-star orange star-sm'></i><i title='2 star rated' class='fa fa-star gray star-sm'></i><i title='2 star rated' class='fa fa-star gray star-sm'></i><i title='2 star rated' class='fa fa-star gray star-sm'></i>";
						   }else 
							   if( $item['rate'] == 5)
						   {
							   echo "<i title='1 star rated' class='fa fa-star orange star-sm'></i><i title='1 star rated' class='fa fa-star gray star-sm'></i><i title='1 star rated' class='fa fa-star gray star-sm'></i><i title='1 star rated' class='fa fa-star gray star-sm'></i><i title='1 star rated' class='fa fa-star gray star-sm'></i>";
						   }
						   else 
						   { 
                           echo "<i title='Not rated yet' class='fa fa-star gray star-sm'></i><i title='Not rated yet' class='fa fa-star gray star-sm'></i><i title='Not rated yet' class='fa fa-star gray star-sm'></i><i title='Not rated yet' class='fa fa-star gray star-sm'></i><i title='Not rated yet' class='fa fa-star gray star-sm'></i>";
						   }
						   
						   echo "<button data-id='" . $item['id'] .  "' class='btn-primary btn btn-xs btncomposedirectmail'>Request for Direct Message</button>";
						   
						   echo "</div>"; 
                           echo "</div> <div class='col-md-4'>"; 
                           echo "<p><strong>Name:</strong> " . $item['username']  . "</p>";
                           echo "<p><strong>City:</strong> " . $item['city']  . " - " . $item['zip'] . "</p>";
                           echo "<p><strong>Country:</strong> " . $item['country']  . "</p>";   
                           echo "</div><div class='col-md-5'>";   
                           echo "<p><strong>Nature of Business:</strong> " . $item['busi_type']  . "</p>";  
                           echo "<p><strong>Business Hours:</strong> " . $item['busi_hours']  . "</p>";  
                           echo "<p><strong>Website:</strong><a target='_blank' href='" . $item['busi_website'] . "'>" . 
						   $item['busi_website']  . "</a></p>";  
                           echo "</div>";
						   echo '</div></div></div></div>'; 
                       }
					}
                   ?></div>
                </div>
            </div>
        </div>
    </section>
	
	
	
<div class="modal fade" id="senddirectmailrequest" tabindex="-1" 
    role="dialog" aria-labelledby="senddirectmailrequest" >
    <div class="modal-dialog  ">
        <div class="modal-content">
            <div class="modal-header modal-headerblue">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h2 class="modal-title">Request Direct Email</h2> 
            </div>
            <div class="modal-body modal-nopad text-left" style='height: 340px; overflow-y: scroll;'> 
<div class='row'>
            <div class='col-md-12'> 
                <h4 class='text-center'>This member is not in your direct email list. <br/>
                Please make a request first to allow direct messaging.</h4>
                <hr/>
                <div id='memberprofilepreview' ></div>
            </div>
            </div>     </div>
            <div class="modal-footer ">
                <button class="btn btn-success" id="btnrequestdirectmail">Send Request</button>
				<hidden id='btnsenddirectemail'></hidden>
            </div> 
          </div>
        </div>
</div> 
 
 <div class="modal fade" id="senddirectmailcomposer" tabindex="-1" 
    role="dialog" aria-labelledby="senddirectmailcomposer" >
    <div class="modal-dialog modal-lg ">
        <div class="modal-content">
            <div class="modal-header modal-headerblue">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h2 class="modal-title">Send Direct Email</h2> 
            </div>
            <div class="modal-body modal-nopad text-left"> 
            <div class='row'>
            <div class='col-md-7'> 
                <h3>Member Profile</h3>
                <div id='memberprofilepreview2' style='height: 340px; overflow-y: scroll;'></div>
            </div>
            <div class='col-md-5'> 
            <h3>Compose Email</h3> 
            <label>Subject:</label>
            <input  class="form-control directmailsubject" placeholder="Subject">
                        <br/>
                        <label>Email Body:</label>
                        <textarea style='height: 150px;' class="form-control directmailbody" placeholder="Email body..."></textarea>
                        </div>
                        </div>     </div>
            <div class="modal-footer ">
                <button class="btn btn-success" id="btnsenddirectemail">Send Mail</button>
            </div> 
          </div>
        </div>
</div>


<?php include("footer.php") ?>