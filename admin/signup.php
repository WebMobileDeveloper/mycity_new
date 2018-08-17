<?php
	ob_start();
		include("header.php");
	include_once 'includes/db.php';
	include_once 'includes/functions.php';

	 
	if (isset($_SESSION['user_id']) || (  isset( $_COOKIE['_rmtoken'] ) && $_COOKIE['_rmtoken'] != 0 ) )
	{
		header('location: dashboard.php');exit();
	} 
	
	
	
	$param = array('id' => '0'); 
	$getGroups = getGroups($link); 
	$vocations =   getVocations($link);
	
	
	if( isset($_POST['submit']) )
	{
		$username =  $_POST['username'];
		$password =  $_POST['password'];
		$vocation =  $_POST['vocation'];
		$city =  $_POST['city'];
		if( $username  != '' && $password  != '' && $vocation != '' &&  $city !='' )
		{
			 
			$userrs = $link->query("select * from mc_user where user_email='$username'");
			 
			if($userrs->num_rows  > 0 )
			{
				$msg='A user with the same email already exists! If you have an account, please login instead!';
			}
			else 
			{
				$msg='Registration done successfully. Please <a href="login.php">login</a> now!';
				$rspm = $link->query("insert into mc_user (user_email, user_pass, verified) values ('$username', '" . md5($password) . "', '0' )"); 
				$userid = $link->insert_id; 
				$rspm = $link->query("insert into user_details (user_id, city, vocations) values ('$userid', '$vocation','$city' )"); 
			  
			  $ds = DIRECTORY_SEPARATOR;  
			$path =  $_SERVER['DOCUMENT_ROOT'] . $ds    ; 
			$mailbody  ="";  
			if(  file_exists( $path . "templates/black_template_01.txt" ) )
			{
				$template_part = file_get_contents( $path . "templates/black_template_01.txt" ) ;  
			}
			
			if(  file_exists( $path . "templates/signup_complete.txt" ) )
			{
				$en = base64_encode ( $userid * 5);
				$len2 = strlen( $userid );
				$verify_token = md5($userid)  ;
				$verify_url= 'verify.php?i=' .   $verify_token .  "&b=" . $en . "&dh=" . md5($en);
				 
				$mail_body = file_get_contents( $path . "templates/signup_complete.txt" ) ;  
				$mail_body= str_replace("{verify_url}", $verify_url , $mail_body ) ; 
				$mail_body = str_replace("{email}", $username , $mail_body ) ;    
			}
			
			$mailbody  = str_replace("{mail_body}",  $mail_body  , $template_part ) ;
			sendmail( $username  ,  "referrals@mycity.com",    "Verify account MyCity.com registration" , $mailbody, $mailbody);
			 	
			}
		}
		  
	 } 
?>
 
<section class="header">
    <div class="container">
        <div class="row">
            <div class="col-xs-9 col-md-4">
                
				<?php
					if(!isset($_SESSION['user_id'])) {
						echo '<a href="index.php"><img src="/images/logo.png" alt="MyCity" id="logo"></a>';
					} else {
						echo '<a href="dashboard.php"><img src="/images/logo.png" alt="MyCity" id="logo"></a>';
					}
				?> 
				<a id="play-video" href='#watch-mycity-video' 
				class='noborder watchvideo' data-toggle="modal" data-target="#videomodal" 
				data-video='zUzISiLmqMw' class='play-video-home'>
				<img src='images/bob-profile.png' class='profile'  />
				</a>
			</div>
			<div class="col-xs-3 col-md-3 text-right pull-right">
                <?php
                if(!isset($_SESSION['user_id'])){
					if( basename($_SERVER['PHP_SELF']) == "login.php") 
						echo "<ul><li><a class='btn btn-reg' href='/'>Register  </a></li></ul>";
					else 
						echo "<ul><li><a class='btn btn-reg' href='login.php'>Sign in </a></li></ul>";
                }else{
                    echo "<ul>
						<li><a href='dashboard.php'><i style='font-size: 36px;' class='fa fa-home' title='Home'></i></a></li>
						<li><a href='message.php'><i style='font-size: 36px;' class='fa fa-envelope' title='Messages'></i></a></li>
						<li><a href='logout.php'><i style='font-size: 36px;' class='fa fa-sign-out' title='Logout'></i></a></li>
					</ul>";
                }
                ?>
            </div>
			<div class="col-xs-12 col-md-5 ">
				 <div class="global-searchd">
				 <form action='member-search.php' method='post'>
				 <p id='ts-head'>Search MyCity Members</p>
					<div class='top-search' >
					   <div class='top-search-inner'> 
      <input type="text" id="gskey" name="gskey"   placeholder="Name or vocation">
	   <input type="text" id='gscityorzip' name='gscityorzip' placeholder="City or Zip Code">
	   </div>
      <div class=''>
	   <button type='submit' id='gsearch' ><i class='fa fa-search'></i></button> 
	  </div>
	 <div class='clearer'></div>
	 </div> 
  </form>
			    </div>
			</div>
            
        </div>
    </div>
</section>

    <section id="main-section" class="welcome-sec  login">
        <div class="container">
            <div class="row">
			<?php
			 
			if($msg  !='' )
			{
				echo "<div class='col-md-12'><p class='alertwidebl  text-center'>". $msg . "</div>";
			}
			 
			?>
			<div class="col-xs-6 col-sm-12 col-md-4  text-center hidden-xs"  >
                <p class='login-intro'><i class='fa fa-support'></i><br/>
                   <span>When you refer others you’re actually helping Yourself. 
                   <br/>Learn who appreciates you for your efforts.</span></p>
            </div> 
			 <div class="col-xs-6 col-sm-12 col-md-4 pull-left  hidden-xs"  > 
            <div class="panel panel-default panel-search">
            <div class="panel-heading text-left"><h2 class='htxt-md'>Search Rated Businesses / Individuals</h2></div>
            <div class="panel-body">
                 <div class="form-group">  
 
 <select data-placeholder="City" id="tbsearchbycity" name="tbsearchbycity"  class="form-control  "  > 
                            <option value=''>Select City</option> 
                             <?php
                           
	 foreach ($getGroups as $item) 
	   {
		   echo "<option value='" . $item['name'] . "'>" . $item['name'] . "</option>";
		}
		
                            ?>
                        </select>
  </div>
  <div class="form-group">   
   <select data-placeholder='Vocations ...' class="form-control   " name="tbsearchbyvoc" id="tbsearchbyvoc"  > 
       <?php
	   foreach ($vocations as $vocation) 
	   {
		   echo "<option value='" . $vocation['name'] . "'>" . $vocation['name'] . "</option>";
		}
	  ?>
  </select>
 
                </div>
                <button type="submit" id="form_search_business" class="flatbutton">Search</button>  
            </div>
         </div>
 <div id='tempblock'></div>
            </div>
			
			
            
            <div class="col-xs-12 col-sm-12 col-md-4 text-center"  >
			<form method='post' action='signup.php'>
                    <div class='formarea'>
                        <h1 class="login-title"> MyCity Signup</h1>   
                      <hr/> 
                <div class="form-group"> 
			 
                    <input id="form_login_username" name="username" class="form-control" placeholder="Your email" value='<?php echo $useremail; ?>'>
                </div>
                <div class="form-group">
                    <input id="form_login_password" type="password" class="form-control" name="password" placeholder="Password">
                </div>
				<div class="form-group">
				<select data-placeholder='Vocations ...' class="form-control   " name="vocation" id="vocation"  > 
       <?php
	   foreach ($vocations as $vocation) 
	   {
		   echo "<option value='" . $vocation['name'] . "'>" . $vocation['name'] . "</option>";
		}
	  ?>
  </select> </div>
  
  <div class="form-group">  
 
 <select data-placeholder="City" id="city" name="city"  class="form-control  "  > 
                            <option value=''>Select City</option> 
                            <?php
                           
	 foreach ($getGroups as $item) 
	   {
		   echo "<option value='" . $item['name'] . "'>" . $item['name'] . "</option>";
		}
		
                            ?>
                        </select>
  </div>
  
			  <button type="submit" name="submit" id='btnsignup' class="flatbutton">Sign In</button>
			  <p class="forgot_password">Are you a member? Click <a  href='login.php'>here</a> to login.</p>
			  </div>
			  
			  </form>
                </div>
            </div>
        </div>
    </section> 
   

<?php include("footer.php") ?>