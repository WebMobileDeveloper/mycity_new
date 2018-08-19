<?php
	ob_start();
	include("header.php");
	include_once 'includes/db.php';
	include_once 'includes/functions.php';

	echo $_COOKIE['_rmtoken'];
	if (isset($_SESSION['user_id']) || (  isset( $_COOKIE['_rmtoken'] ) && $_COOKIE['_rmtoken'] != 0 ) )
	{
		header('location: dashboard.php');exit();
	}

	 
	
	
	$groups = getGroups($link);
	$vocations = getVocations($link);


	if(isset($_POST['btnlandingsignup']))
	{
		$landingzip = $_POST['landingzip'];
		$landingcity = $_POST['landingcity']; 
	}

	//?token=7836e0721b2c6977135b916ef286bcb49ec&l=3

	if(  $_SERVER['HTTP_HOST'] == "localhost" || $_SERVER['HTTP_HOST'] == "mycity.dev")
	{
		$siteurl = 'http://'. $_SERVER['HTTP_HOST'] . "/";
	} 
	else
	{
		$siteurl =  'https://mycity.com/';
	}

	$profilepic =  "images/no-photo.png" ;
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


	if(isset($_GET['l']) && isset( $_GET['token'] ))
	{
		$tokenlength = $_GET['l'];
		$token = $_GET['token'];

		$encid =substr($token, 0, $tokenlength);
		$enctoken =substr($token, $tokenlength, strlen($token) - $tokenlength);
	   
		if(strcmp( md5($encid),  $enctoken  ) == 0)
		{
			$param = array('id' =>  $encid ); 
			$memberprofile = json_decode(   curlexecute($param, $siteurl . 'api/api.php/member/getprofile/'), true);
		  
			$member =  $memberprofile[0] ;
			$useremail = $member['user_email'];
			if(  $member['image'] !='' )
			{
				$profilepic = "images/" . $member['image'];
				$profilepic = ((file_exists( $profilepic ))? $profilepic : "images/no-photo.png"); 
			}
		}  
	}
?>
    <section id="main-section" class="welcome-sec  login">
        <div class="container">
            <div class="row">
            <div class="col-xs-6 col-sm-12 col-md-4 pull-left  hidden-xs"  > 
            <div class="panel panel-default panel-search">
            <div class="panel-heading text-left"><h2 class='htxt-md'>Search Rated Businesses / Individuals</h2></div>
            <div class="panel-body">
                 <div class="form-group">  
 
 <select data-placeholder="City" id="tbsearchbycity" name="tbsearchbycity"  class="form-control  "  > 
                            <option value=''>Select City</option> 
                            <?php
                            echo $citynames; 
                            ?>
                        </select>
  </div>
  <div class="form-group">   
   <select data-placeholder='Vocations ...' class="form-control   " name="tbsearchbyvoc" id="tbsearchbyvoc"  > 
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
 <div id='tempblock'></div>
            </div>
            <div class="col-xs-6 col-sm-12 col-md-4 pull-right text-center hidden-xs"  >
                <p class='login-intro'><i class='fa fa-support'></i><br/>
                   <span>When you refer others you’re actually helping Yourself. 
                   <br/>Learn who appreciates you for your efforts.</span></p>
            </div> 
            <div class="col-xs-12 col-sm-12 col-md-4 text-center"  >
                    <div class='formarea'>
                        <h1 class="login-title"><img width='50' height='50' class='img   img-circle' src='<?php echo $profilepic; ?>' /> MyCity Login</h1>   
                      <hr/> 
                <div class="form-group"> 
                    <input id="form_login_username" name="username" class="form-control" placeholder="Your email" value='<?php echo $useremail; ?>'>
                </div>
                <div class="form-group">
                    <input id="form_login_password" type="password" class="form-control" name="password" placeholder="Password">
                </div>
				
				<div class="form-group text-left"> 
				  <div class="checkbox">
					<label>
					  <input id="form_login_remember_me" name='remember_me' type="checkbox"> Remember me
					</label>
				  </div> 
			  </div>
  
  
                <button type="submit" id="form_sign_in_button" class="flatbutton">Sign In</button>
                <!--<p class="forgot_password"><a href="javascript:void(0)">Forgot your password?</a></p>
                <p class="strikey">or</p>
                <button id="log_in_facebook" class="facebook_button flatbutton">Sign in</button>-->
				 <p class="forgot_password"><span data-toggle="modal" data-target="#forgetPW" style="cursor:pointer;">Forgot your password?</span></p>
                  
                    </div>
                </div>
            </div>
        </div>
    </section> 
    <script>
        function readURL(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function (e) {
                    $('#blah')
                        .attr('src', e.target.result)
                        .width(130)
                        .height(130);
                };
                reader.readAsDataURL(input.files[0]);
                $(".hideafter").hide();
                $("#blah").show();
            }
        }
    </script>

<?php include("footer.php") ?>