<?php 
ob_start();
include_once("template/head.php");
if (!isset($_SESSION['user_id']))
{
	header('location: index.php'); 
}
include_once 'includes/db.php';
include_once 'includes/functions.php';

if(  $_SERVER['HTTP_HOST'] == "localhost" || $_SERVER['HTTP_HOST'] == "mycity.test")
{
	$_SERVER['HTTPS'] = false;
    $siteurl = 'http://'. $_SERVER['SERVER_NAME'] . "/";
} 
else
{
    $siteurl =  'https://mycity.com/';
}

$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];
$user_email = $_SESSION['user_email'];
$user_phone = $_SESSION['user_phone'];
$_user_role = $_SESSION['user_role'];
$user_group = $_SESSION['user_group'];
$user_group = $_SESSION['user_group'];
$isemployee = $_SESSION['isemployee']; 
$user_picutre = ((file_exists($_SESSION['user_pic']))?$_SESSION['user_pic']:"images/no-photo.png");
$userGrp = '';
$user_pkg = '';
$hideClass = $_user_role == 'admin' ? "" : "hide";


/*Profile Update*/
if(isset($_POST['upload_btn']))
{
	$ds = DIRECTORY_SEPARATOR; 
    if(!empty($_FILES['prof_img']['name']))
	{
        $ext=end(explode(".",$_FILES['prof_img']['name']));
		$upload_path="prof-img-".date("Ymd-His").".".$ext;
		if(move_uploaded_file($_FILES['prof_img']['tmp_name'] , "images/". $upload_path))
		{ 
			$link->query("UPDATE `mc_user` SET image='".$upload_path."' WHERE id=".$user_id);
			$user_picutre="images/".$upload_path;
			$_SESSION['user_pic']="images/".$upload_path;
			
			//location 2
			$secondtarget = $_SERVER['DOCUMENT_ROOT'] . $ds  . "assets" .  $ds. "uploads" .  $ds . "profiles" .  $ds . $upload_path ; 
			copy( "images/". $upload_path  , $secondtarget);
			  
			 
			//location 3
			$thirdtarget = $_SERVER['DOCUMENT_ROOT'] . $ds .  "images" . $ds . $upload_path ; 
			copy(  "images/". $upload_path , $thirdtarget);  
			
			 
		}
	}
} 


/*Profile Update*/
$userGrpQ = $link->query("SELECT * FROM user_details WHERE user_id='$user_id' ");
if ($userGrpQ->num_rows > 0)
{
	$userGrpFet = $userGrpQ->fetch_assoc();
	$userGrp = $userGrpFet['groups'];
}

$userPkgQ = $link->query("SELECT * FROM mc_user WHERE id='$user_id' ");
if ($userPkgQ->num_rows > 0)
{
	$userPkgFet = $userPkgQ->fetch_assoc();
	$user_pkg = $userPkgFet['user_pkg'];
	$publicprofile = $userPkgFet['publicprofile'];
	$profileisvisible = $userPkgFet['profileisvisible'];
}

if($_user_role == 'admin')
{
	$text = 'REGISTERED PEOPLE';
}
else
{
	$text = 'PEOPLE YOU KNOW DETAILS';
} 

if(! function_exists ( 'curl_version' ))
{
	exit ( "Enable cURL in PHP" );
}

$ques_data = getQues($link); 
$getGroups = getGroups($link); 
$allPackages = getPackages();
$aboutUs = getPageDetails('about');
$tagline = getPageDetails("tagline"); 
$mygroups = getMyGroups($link, $user_id);
$triggers = getMyTriggers($link, $user_id);
$vocations =   getVocations($link);
$cities =  getCities($link);
$lifestyles = getLifestyles($link);
$mailtemplates = getMailTemplates($link );
$help_data = getHelps($link);
$help_data_buttons = getHelpsButtons($link);
$video_testimonials =   getTestimonials( );
$alltags = getTags();
$my_profile =  getUserProfile($user_id);
$mynotes = getNotes($user_id,  1)  ;
$my_knowlist =  getMemberKnows($user_id);
$directmailsuggest = getAutoSuggestedMembers($user_id, 0  );
$recentupdates = getRecentUpdatedKnows();
$vocaoptions ='';

foreach ($vocations as $vocation) 
{
	$vocaoptions .= "<option value='" . $vocation['name'] . "'>" . $vocation['name'] . "</option>"; 
}

$citynames ='';
$grouplist ='';
 
foreach ($getGroups as $item)
{
	$sel = $userGrp == $item['id'] ? "selected='selected' " : "";
	if ($_user_role == 'admin')
	{
		$dis = "";
	}
	if($item['name'] != '')
		$citynames .= "<option value='" . $item['name'] . "'>" . $item['name'] . "</option>";
	
	$grouplist .= "<option value='" . $item['id'] . "' " . $sel . $dis . ">" . $item['name'] . "</option>";
} 
  
  
$rspm = $link->query("SELECT * FROM  mc_program_client where client_id='$user_id'");
$programstatus=0;
if($rspm->num_rows  > 0 )
{
	$prow =$rspm->fetch_array() ;
	
	$programstatus = $prow['status'];
	$participantid = $prow['id'];
}
  
 
?>
<body>
<?php
 include_once('template/header.php'); 
?> 
<div class="main-panel"> 
    <div class='container-fluid top-promo-light'>
      <div class='row'>
        <div class='col-md-12 text-center'>
        <h5 ><?php echo $tagline[0]["page_content"] ?></h5>
        </div>  
    </div>
</div> 
<div class="content">
	<div class="container-fluid"> 
		 <div class='row visible-xs'>
			 <div class='col-md-12'>
			 <p class=""> <button type="button" class="btn btn-primary btn-xs " data-toggle="offcanvas">Main Menu</button>
				  </p>
			 </div>
		 </div> 
		<div class="row row-offcanvas row-offcanvas-left">
			<div class="col-sm-4 sidepane col-md-3 sidebar-offcanvas" id="sidebar" role="navigation">
			 
			<div class="panel panel-default" style='height: 250px'>
				 <div class="panel-body">
				 <div id="tbc" class="carousel slide infoalertzone" data-ride="carousel"> 
  <ol class="carousel-indicators">
    <li data-target="#tbc" data-slide-to="0" class="active"></li>
    <li data-target="#tbc" data-slide-to="1"></li> 
	<?php if($programstatus==0 && $_user_role  != 'admin' ) { ?><li data-target="#tbc" data-slide-to="2"></li> <?php } ?>
  </ol>
  
  <div class="carousel-inner " role="listbox"> 
	<?php if($programstatus == 0 && $_user_role  != 'admin') { ?>
	<div class="item carousel-entice active">
      <div class=" pad10 margb3 text-center">
		  <h4>Three Touch Program</h4>
		   <p>Convert connection into a relationship over 30 day period
		   with our unique 3 Touch Program.
</p>
<p><button data-toggle="tab" data-ppid='<?php echo $participantid; ?>' class="btn btn-orange join3tprogram" ><i class="fa fa-link"></i>Join Program Now</button></p>
						</div>
    </div>
	<?php } ?>
    <div class="item <?php if($programstatus == 1 || $_user_role  == 'admin') { echo "active"; } ?>">
      <div class=" pad10 margb3 text-center">
		  <h4>We've selected a few experiences you might like. Get promoted for free.</h4>
		   <p><i class='fa fa-chevron-circle-right'></i> Rating someone by who rated them. <i class='fa fa-chevron-circle-right'></i> Giving a referral to someone.</p>
						</div>
    </div>
    <div class="item">
      <div class=" pad10 margb3 text-center"> 
		 <h4>Introduce highly rated people to people you know in their area.</h4>
		 <br/>
		 <p><a data-toggle="tab" href="#menu2" class="btn btn-primary" ><i class="fa fa-user-plus"></i>Click here to add your know!</a></p>
		<br/>
		<br/>	
	</div>
    </div> 
  </div>
</div> 			 
 </div>
 </div> 
 <div class="panel panel-default"> 
	<div class="panel-heading">
		<h4>Accountability Stats</h4>
	</div>
	<div class="panel-body">
		<input type="text" class="searchname oval-input" id="searchname" placeholder="Partner Name ...">
			<a href='#menu35' data-toggle="tab" class="btn btn-primary btnsearchpartner"><i class='fa fa-search'></i></a> 
		</div>
 </div> 
 <?php if($_user_role == 'admin'  ) { ?>	
 <div class="panel panel-default">
		<div class="panel-heading">
			<h4>Client Tracking Program</h4>
		</div>
		<div class="panel-body panel-menu">  
				  <ul id='reminders'>
				    <li class="close_drop"> 
						 <a  data-toggle="tab" class='cfg_fetchemails'    href='#menu68' ><i class="fa fa-bell-o"></i>  Setup Email</a>
					</li> 
					<li class="close_drop"> 
						 <a  data-toggle="tab" class='cfg_assignemail'    href='#menu69' ><i class="fa fa-bell-o"></i>  Client Tracking</a>
					</li> 
					<li class="close_drop"> 
						 <a  data-toggle="tab" class='cfg_getallvoicemail'    href='#menu71' ><i class="fa fa-bell-o"></i> Voice Mails Logs</a>
					</li>  
					 
				  </ul> 
			</div> 
	</div>
	  <?php } ?> 
			 
			 
		 <div class="panel panel-default">
                <div class="panel-heading">
					<h4>People you know</h4>
                </div>
				<div class="panel-body panel-menu">
					<ul id='peopleknow'>
						
						<?php if($_user_role == 'admin'  ) { ?>	
						<li><a data-toggle="tab" href="#menu2" class='showknowentryform loadknowsormembers'><i class="fa fa-user-plus"></i>Add/Update Member</a></li> 
						<li><a data-toggle="tab" class="btnviewhighrankknows" href="#menu60" ><i class="fa fa-user-plus"></i>View Top Rated Know</a></li> 
						<li><a data-toggle="tab" href="#menu56" ><i class="fa fa-user-plus"></i>Add Business Card</a></li>  
						 
						 
						 
						 <?php }else 
						 {
							?>
							<li><a data-toggle="tab" href="#menu2" class='showknowentryform loadknowsormembers' ><i class="fa fa-user-plus"></i>Add/Update People</a></li> 
							<?php
						 }?>	
						<li class="close_drop"><a data-toggle="tab" data-pagesize='10' data-pageno='1' class='showreferrals' href="#menu17" id='hint-addreferral'> <i class="fa fa-users"></i>Introduction/Referral</a>
							<a href="<?php echo $help_data_buttons[1]['helpvideo']; ?>" target="_blank" ><i class='fa fa-arrow-right' ></i><span style="color:red;"> Help</span></a>
						</li>
						<li><a data-toggle="tab"  href="#" class='ref_wizard'><i class="fa fa-support"></i> Referral Wizard</a>
						<a href="<?php echo $help_data_buttons[9]['helpvideo']; ?>" target="_blank" ><i class='fa fa-arrow-right' ></i><span style="color:red;"> Help</span></a>
						</li>  
						 <li><a data-toggle="tab" href="#menu79" class='viewallknows'><i class="fa fa-user-plus"></i> Search Knows</a></li>  
						
						<?php if($_user_role == 'admin'  ) { ?>
						<li><a data-toggle="tab"  title='Reverse Tracking of Partners' href="#menu46" class='showreversetrackpane' ><i class="fa fa-user"></i> Reverse Tracking</a></li>
						<li><a data-toggle="tab" title='Show Unfinished Signups' data-page='1' href="#menu48" class='viewunfinishedsignup' ><i class="fa fa-user"></i> Incomplete Signups</a></li>
						 <li><a data-toggle="tab" title='Manage Employee' class='alink_loadmembers' href="#menu83" ><i class="fa fa-bar-chart"></i> Manage Employees</a></li>
				<?php }  ?>
		 </ul> 
	 </div>  
	</div> 
 
	<div class="panel panel-default">
		<div class="panel-heading">
			<h4>3 Touch Program</h4>
		</div>
		<div class="panel-body panel-menu">  
				
				  <ul id='reminders'>
					<?php if($_user_role == 'admin'  ) { ?>	
						<li class="close_drop"> 
						<a  data-toggle="tab" href='#menu74' class='loadprogramquestions' ><i class="fa fa-cog"></i> Program Questions</a>
						</li> 
					  
						<li class="close_drop"> 
						 <a  data-toggle="tab" href='#menu76' class='3tperformances' ><i class="fa fa-bell-o"></i> Progress Tracking</a>
						</li> 
						
						
					<?php } else  if($programstatus == 1 ) 
					{  
					?>
						 
						<li class="close_drop"> 
							 <a  data-toggle="tab" href='#menu75' class='chooseprogparticipants' ><i class="fa fa-bell-o"></i> Manage Relationship</a>
						</li>
<li class="close_drop"><a data-toggle="tab" href='#menu78' class='3tactivitylogs' ><i class="fa fa-bell-o"></i> Track Activities</a></li>						
				 <?php	 }  ?>
				  </ul> 
			</div> 
	</div> 
	 <?php if($isemployee == 1 ): ?> 
	 <?php if($_user_role == 'admin' ): ?>
		<div class="panel panel-default">
		<div class="panel-heading">
			<h4>Manage Staff Activity</h4>
		</div>
		<div class="panel-body panel-menu">  
				
				  <ul id='reminders'>
					  <li class="close_drop"> 
						<a  data-toggle="tab" href='#menu85' class='loadstaffactivities' ><i class="fa fa-cog"></i> View Logs</a>
					</li>   
				  </ul> 
			</div> 
	</div>
	<?php else: ?>
	<div class="panel panel-default">
		<div class="panel-heading">
			<h4>My Activity Log</h4>
		</div>
		<div class="panel-body panel-menu">  
				
				  <ul id='reminders'>
					  <li class="close_drop"> 
						<a  data-toggle="tab" href='#menu82' class='loadmyclients' ><i class="fa fa-cog"></i> Add Log</a>
						</li> 
					  <li class="close_drop"> 
						<a  data-toggle="tab" href='#menu83' class='loadmylogs' ><i class="fa fa-cog"></i> View Logs</a>
						</li> 
				  </ul> 
			</div> 
	</div>
	<?php 
	endif; 
	endif; 
	?>
	<div class="panel panel-default">
		<div class="panel-heading">
			<h4>Reminders</h4>
		</div>
		<div class="panel-body panel-menu">  
				  <ul id='reminders'>
				    <li class="close_drop"> 
						 <a  data-toggle="tab" class='fetchreminder'  href='#menu34' ><i class="fa fa-bell-o"></i>  Check Reminders</a>
					</li> 
					<li class="close_drop"> 
						 <a  data-toggle="tab" class='configureReminder' href="#menu32"><i class="fa fa-clock-o"></i> Set Reminder</a>  
					</li> 
					<li class="close_drop"> 
						 <a  class='showremindersummary' data-toggle="tab" href="#menu33"><i class="fa fa-pencil"></i> Edit Reminders</a>  
					</li> 
				  </ul> 
			</div> 
	</div> 
	<?php if ($_user_role == 'admin') { ?> 
		<div class="panel panel-default">
                <div class="panel-heading">
                    <h4>System Configuration</h4>
                </div>
            <div class="panel-body panel-menu">
			<ul>   
				<li><a data-toggle="tab" href="#menu6"><i class="fa fa-graduation-cap"></i> Vocations</a></li>
				<li><a data-toggle="tab" href="#menu26"><i class="fa fa-graduation-cap"></i> Lifestyle</a></li>
                <li><a data-toggle="tab" href="#menu28"><i class="fa fa-envelope"></i> Configure Mail Templates</a></li> 
				<li><a data-toggle="tab" href="#menu29"><i class="fa fa-cog"></i> Manage Help Buttons</a></li>
				<li><a data-toggle="tab" href="#menu38"><i class="fa fa-cog"></i> Manage Testimonials</a></li>
				<li><a data-toggle="tab" class='reloadsettings' href="#menu42" ><i class="fa fa-cog"></i> Add/Edit Common Vocations</a></li>
                <li><a data-toggle="tab" class='reloadsettings' href="#menu47" ><i class="fa fa-cog"></i> Add/Edit Tags</a></li> 
            
				<li class="close_drop"> 
				<a  data-toggle="tab" href='#menu74' class='loadprogramquestions' ><i class="fa fa-cog"></i> 3 Touch Program Questions</a>
				</li> 
					
					
			</ul>
		 </div> 
	 </div> 	
	 <div class="panel panel-default">
		<div class="panel-heading">
			<h4>Page Changes</h4>
		</div>
		<div class="panel-body panel-menu">
			<ul  >
				<li ><a data-toggle="tab"  href="#pagepackages" ><i class="fa fa-cube"></i> Packages</a></li>
				<li><a data-toggle="tab"  href="#pageaboutus"  ><i class="fa fa-support"></i> About Us</a></li> 
				<li><a data-toggle="tab" id='manageblog'  href="#blogmanage" ><i class="fa fa-pencil-square"></i> Blog</a></li>
				<li><a data-toggle="tab"  href="#pagetagline" ><i class="fa fa-tags"></i> Tagline</a></li>
			</ul>
		 </div> 
	 </div>
	 <div class="panel panel-default">
		<div class="panel-heading">
			<h4>Users Management</h4>
		</div>
		<div class="panel-body panel-menu">	
			   <ul  >
					<li><a data-toggle="tab" class='newSignup' href="#menu14"><i class="fa fa-users"></i>New Clients Group Request</a></li>
					<li><a data-toggle="tab" class='get_FAQ' href="#menu11"><i class="fa fa-support"></i>Help / FAQ</a></li>
					<li><a data-toggle="tab" href="#menu5"><i class="fa fa-users"></i>Groups</a></li>
					<li><a data-toggle="tab" class='btnloadcitylisting' href="#menu66"><i class="fa fa-building"></i> New City Listing Requests</a></li>
					<li><a data-toggle="tab" class='knowstatpane' href="#menu18"><i class="fa fa-users"></i>Knows Stats</a></li>
					<li><a data-toggle="tab" class='fetchpoints' href="#menu22"><i class="fa fa-users"></i>Manage Loyalty Points</a></li>
					<li><a data-toggle="tab" title='Generate a report of who entered new knows recently' class='newKnowEntries' href="#menu23"><i class="fa fa-bar-chart"></i>New Know Report</a></li>
                    <li><a data-toggle="tab" title='Generate a report of who signuped recently'  href="#menu45"><i class="fa fa-users"></i> New Signups</a></li>
                    <li  ><a data-toggle="tab" href="#menu27"> <i class="fa fa-users"></i>Track Referrals By Group</a></li>
                    <li  ><a data-toggle="tab" href="#menu40"> <i class="fa fa-users"></i>Track Referrals By Vocation</a></li>
                    <li><a data-toggle="tab" title='Singup from LinkedIn Invite' class='linkedinsignup' href="#menu43" ><i class="fa fa-linkedin"></i> LinkedIn Contacts Signups</a></li>
                    <li><a data-toggle="tab" title='Export to Spreadsheet' class='' href="#menuExportSpread" ><i class="fa fa-bar-chart"></i> Export to Spreadsheet</a></li>
				</ul>  		 
		 </div> 
	 </div>
	      
	 
			 <?php } ?> 
	  
<div class="panel panel-default ">
                <div class="panel-heading">
                    <h4>Tools</h4>
                </div>
            <div class="panel-body panel-menu">
						<ul>    
				 <?php if ($_user_role != 'admin') { ?> 
				<li><a data-toggle="tab" href="#menu65"><i class="fa fa-building"></i> Request to List Your City</a></li>
				<?php } ?>
             
        	<li><a data-toggle="tab" href="#menu3"><i class="fa fa-search"></i>Search Nearest Members</a></li>
            <?php if ($_user_role == 'admin') { ?>  
			<li><a data-toggle="tab" href="#menu4" class='loadquestions'><i class="fa fa-question"></i> Questions</a></li>
			
			<li><a data-toggle="tab" href="#menu8"  class='loadsearchlog'><i class="fa fa-graduation-cap"></i> Search Logs</a></li>
			<li><a data-toggle="tab" href="#menu30" class='loadhomesearchlog'><i class="fa fa-graduation-cap"></i> Home Search Logs</a></li>
			<li><a data-toggle="tab" class='businesslog' href="#menu54"><i class="fa fa-clipboard"></i> Business Search Logs</a></li>
			<li><a data-toggle="tab" class='trendingsrclog' href="#menu62"><i class="fa fa-clipboard"></i> Top Bar Search Logs</a></li>
			<li><a data-toggle="tab"  class='loadinbox' href="#menu20"> <i class="fa fa-envelope"></i>Inbox</a></li>
			<?php } ?>
			<li><a data-toggle="tab" href="#menu12"> <i class="fa fa-question-circle"></i>My Triggers</a>
			<a href="<?php echo $help_data_buttons[3]['helpvideo']; ?>" target="_blank" ><i class='fa fa-arrow-right' ></i><span style="color:red;">Help</span></a>
            </li>   
		    <li><a data-toggle="tab" href="#menu25" class='loadimportedknows' ><i class="fa fa-users"></i> Manage Imported Knows</a>
						<a href="<?php echo $help_data_buttons[10]['helpvideo']; ?>" target="_blank" ><i id='hint-profile1' class='fa fa-arrow-right' ></i><span style="color:red;">Help</span></a>
						</li>  
			<li><a data-toggle="tab" href="#menu24"  ><i class="fa fa-upload"></i> Mass Upload Your Knows</a></li> 
			<li><a data-toggle="tab" title='Import Linked Contacts' class='newKnowEntries' href="#menu39"><i class="fa fa-linkedin"></i>Import LinkedIn Contacts</a></li>
			<li><a data-toggle="tab" title='Generate a report] of imported LinkedIn Contacts' class='linkedinimportlist' href="#menu40" ><i class="fa fa-linkedin"></i>View Imported LinkedIn Contacts</a></li>
			<li><a data-toggle="tab" title='Privacy Settings' class='mn_privacysetting' href="#menu77" ><i class="fa fa-linkedin"></i> Privacy Settings</a></li>
			<?php if ($user_id == '19') { ?>
			<li><a data-toggle="tab" title='Import Linked Contacts'   href="#menu80"><i class="fa fa-linkedin"></i> Import Linked Knows For Mobile</a></li>
			<li><a data-toggle="tab" title='Import Linked Contacts' class='viewimportedliest'  href="#menu81"><i class="fa fa-linkedin"></i> View Import List</a></li>
			<?php } ?>		
			
			
			<?php if ($_user_role == 'admin') { ?>
			<li><a data-toggle="tab" title='Update distance between zip codes' class='managedistances' href="#menu55" ><i class="fa fa-linkedin"></i> Update Distance</a></li>
			<li><a data-toggle="tab" title='Manage Fuzzy Search KeyWords' class='managefuzzysearch' href="#menu61" ><i class="fa fa-cog"></i> Fuzzy Search Keyword</a></li>				 
			<li><a data-toggle="tab" title='Manage City Zip Codes' class='managezipcode' href="#menu64" ><i class="fa fa-pencil"></i> Manage Zip Codes</a></li>
			<?php } ?>		   
		</ul> 
		</div> 
	</div> 
	</div> 
	<div class="col-sm-8 col-md-9">
		<div class="tab-content">
			<div class="row">
				<div class='col-xs-12 col-md-12'> 
				<div class="tab-content">
			<!--Menu11--> 
			<div id="menu11" class="tab-pane fade maintab">
				<div class="top-head">
					<div class="col-xs-12 col-sm-8">
                        <h4>Help Instructions</h4>
                    </div>
					<div class="clearfix"></div>
                </div>
				<div class="col-xs-12 col-sm-8 col-sm-offset-2 no-padd">
				<div class='row'>
					<div class="col-sm-12 col-xs-12 padd-8">
						<label>Help Title:</label>
							<input  placeholder="Enter a title/heading for help content" class="form-control help_title" data-id="cue-1" type="text">
					</div> 
					<!-- code added on 19-4-2017 -->
					<div class="col-sm-12 col-xs-12 padd-8">
						<label>?</label>
							<input  placeholder="Enter a video Link to hyperlink it" class="form-control help_ques" data-id="cue-1" type="text">
					</div> 	
					<!-- code ended -->
					<div class="col-sm-12 col-xs-12 padd-8">
						<label>Help Content:</label>
						<textarea class="form-control help_content" placeholder="Complete help explanation ..."></textarea>
					</div>
							
					<div class="col-sm-12 col-xs-12 padd-8 text-center">
                        <div class="col-xs-6 padd-3 text-left">
                       	<button class="btn btn-primary btnblock save_helpinstruction">Submit</button>
                       	<input type='hidden' id='faqid' value='0'/>
                    </div>
                    <div class="col-xs-6 padd-3 text-right">
                    </div>
                </div>
            	<div class="clearfix"></div>
			</div>
		</div>
		<div class="col-xs-12 col-sm-12 padd-5">
		<hr/>
		</div>
		<div class="col-xs-12 col-sm-12 srdDtls"  >
		<div class='row'>
		<div class='col-md-10'>
			<h4>EXISTING QUESTIONS AND EXPLANATION</h4>
		</div>
		<div class='col-md-2'>
			<button class='btn btn-primary get_FAQ' >Refresh</button>
		</div>
		</div> 
		<div class="table-responsive">
		<div id="helptable"></div>
		</div>
		</div> 
		</div> 
	<!--Menu11--> 
	<!--Menu31-->
	<div id="menu31" class="tab-pane fade maintab">
		<div class="top-head">
			<div class="col-xs-12 col-sm-8">
				<h4>Performance Report</h4>
			</div>
			<div class="clearfix"></div>
		</div>
		<div class="col-xs-12 col-sm-10 col-sm-offset-1 no-padd">
			<div id='performdashboard'></div>
				<div class="clearfix"></div>
			</div>
      </div>	
	 <!--end of Menu31--> 
	 <!--Menu31-->
	<div id="menu32" class="tab-pane fade maintab">
		<div class="top-head">
			<div class="col-xs-12 col-sm-12">
				<h4>Configure Reminder</h4>
			</div>
			<div class="clearfix"></div>
		</div>
		<div class="col-xs-12 col-sm-12">
			<div id='reminderform'>	
		<div class='row'>
		<div class="col-sm-12"> 
			<div class="form-group">
				<label for="title">Reminder Type:</label> 
			  </div>
			 </div>  
		<div class="col-sm-2"> 
			<div class="radio">
				<label style="font-size: 1em">
					<input type="radio" name="type" value="TASK">
					<span class="cr"><i class="cr-icon fa fa-tasks"></i></span>
					Task
				</label>
			</div> 
         </div>
		<div class="col-sm-2"> 
			<div class="radio">
				<label style="font-size: 1em">
					<input type="radio" checked name="type" value="NOTE">
					<span class="cr"><i class="cr-icon fa fa-pencil-square-o"></i></span>
					Note
				</label>
			</div> 
        </div>
		<div class="col-sm-2"> 
			<div class="radio">
				<label style="font-size: 1em">
					<input type="radio" name="type" value="EMAIL">
					<span class="cr"><i class="cr-icon fa fa-envelope"></i></span>
					Email
				</label>
			</div> 
        </div>
		<div class="col-sm-2"> 
			<div class="radio">
				<label style="font-size: 1em">
					<input type="radio" name="type" value="CALL">
					<span class="cr"><i class="cr-icon fa fa-phone"></i></span>
					Call
				</label>
			</div> 
        </div>
		<div class="col-sm-2"> 
			<div class="radio">
				<label style="font-size: 1em">
					<input type="radio" name="type" value="MEETING">
					<span class="cr"><i class="cr-icon fa fa-users"></i></span>
					Meeting
				</label>
			</div> 
         </div> 
		 </div>
		 <div class="form-group">
				<label for="title">Reminder Title:</label>
				<input type="text" class="form-control" id="title" placeholder="Reminder Title">
			  </div>
			  <div class="form-group">
				<label for="text">Reminder Text:</label>
				<textarea class="form-control" rows='10' id="text" placeholder="Reminder Body"></textarea>
			  </div>
			<hr/>  
		    <div class='row'>
			 <?php if($_user_role == 'admin'  ) { ?>	
				<div class='col-md-3'>
					<div class="form-group">
					<label for="title">Assigned To:</label>
					<input type="text" class="form-control" id="assignno" placeholder="Assign reminder to ...">
					<input type="hidden" class="form-control" id="hidassignno"  >
					</div> 
				</div>
			 <?php } ?>
				<div class='col-md-4'>
					<div class="form-group">
					<label for="remindermailday">Email Reminder on the day of:</label>
					<input type="text" class="form-control" id="remindermailday" placeholder="Reminder Title"> 
					</div> 
				</div>
			<div class='col-md-4'>
				<div class="form-group">
					<label for="remindermailday">Email Reminder on the day of:</label>
					<select  class="form-control form-control-sm" id="hour" style='width: 90px;display:inline-block'>
					<?php 
					for($hr=1; $hr <=12; $hr++)
					{
						if($hr < 10)
							echo "<option>0" . $hr . "</option>";
						else
							echo "<option>" . $hr . "</option>";
					}
					?> 
					</select> : 
					<select  class="form-control form-control-sm" id="min" style='width: 90px;display:inline-block'>
					<?php 
					for($hr=1; $hr <=60; $hr++)
					{
						if($hr < 10)
							echo "<option>0" . $hr . "</option>";
						else
							echo "<option>" . $hr . "</option>";
					}						
					?> 
					</select> 
					<select  class="form-control form-control-xs" id="hrformat" style='width: 80px;display:inline-block'>
						<option>AM</option>  
						<option>PM</option>  
					</select> 
					</div> 
				</div>
			 </div>  
			  <button type="button" id='btnsavereminder'  class="btn btn-primary ">Submit</button>
			  <button type="button" id='btnclearreminder' class="btn btn-danger  ">Cancel</button> 
			</div>
			<div class="clearfix"></div>
		</div>
	</div>	
	 <!--end of Menu31--> 
	 <!--Menu33-->
	<div id="menu33" class="tab-pane fade maintab">
		 
			<div class="col-xs-12 col-sm-12 no-padd">
			
			<div class="panel panel-default  panel-success"> 
			 <div class="panel-heading">
				<h4>Manage My Reminders</h4>
			 </div>
				<div class="panel-body"> 
		
			
				<div id='remindersummary'></div>
				
				</div>
				</div>
			</div>
		</div>	
	 <!--end of Menu33--> 
	 <!--Menu34-->
	<div id="menu34" class="tab-pane fade maintab">
		<div class="top-head">
			<div class="col-xs-12 col-sm-8">
				<h4>Important Reminders</h4>
			</div>
			<div class="clearfix"></div>
		</div>
			<div class="col-xs-12 col-sm-10 col-sm-offset-1 no-padd">
				<div class='row'>
					<div class="col-sm-6"><h3 class='text-center'>All reminders you have created </h3>
						<div id='reminder-gridleft'></div>
					</div>
					<div class="col-sm-6"><h3 class='text-center'>All reminders assigned to you</h3>
						<div id='reminder-gridright'></div>
					</div>
				</div>
		 
				<div class="clearfix"></div>
			</div>
		</div>	
  <!--end of Menu34-->
  <!--Menu35-->
	<div id="menu35" class="tab-pane fade maintab">
		<div class="top-head">
			<div class="col-xs-12 col-sm-8">
				<h4>Partner Search Result</h4>
			</div>
			<div class="clearfix"></div>
		</div>
			<div class="col-xs-12 col-sm-10 col-sm-offset-1 no-padd">
				 <div id='partnersearchresult'></div>
				  
				<div class="clearfix"></div>
			</div>
		</div>	
	<!--end of Menu35--> 
    <!--Menu36-->
	<div id="menu36" class="tab-pane fade maintab">
		<div class="top-head">
			<div class="col-xs-12 col-sm-8">
				<h4>My Contact People</h4>
			</div>
			<div class="clearfix"></div>
		</div>
			<div class="col-xs-12 col-sm-10 col-sm-offset-1 no-padd">
				 <div id='myknowsgrid'></div>
				  
				<div class="clearfix"></div>
			</div>
		</div>	 
 <!--end of Menu36-->
<!--menu37-->
	<div id="menu37" class="tab-pane fade maintab">
		<div class="top-head">
			<div class="col-xs-12 col-sm-8">
				<h4>Know Entry Summary</h4>
			</div>
			<div class="clearfix"></div>
		</div>
			<div class="col-xs-12 col-sm-10 col-sm-offset-1 no-padd">
				 <div id='knowentrylog'></div> 
				<div class="clearfix"></div>
			</div>
		</div>	 
 <!--end of menu37--> 
 <div id="menu38" class="tab-pane fade maintab"> 
                            <div class="top-head">
                                <div class="col-xs-12 col-sm-8">
                                    <h4>Manage Testimonials</h4>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                            <div class="col-xs-12 col-sm-12 col-sm-offset-0  ">  
                                     <div class="form-group">
										<label for="exampleInputEmail1">Testimonial Video Link:</label>
										<input type='text' class="form-control" name='testimonial_video' id='testimonial_video' required />
									  </div>
                                   <div class="form-group">
										<label for="exampleInputEmail1">Help Video:</label>
										<textarea type='text' class="form-control"  name='testimonial_summary' id='testimonial_summary'></textarea>
									  </div>
                                    <div class="form-group">
										<button type='button' data-id='0' class='btn btn-primary' id='btnsavetestimonial' >Save</button>

										<button type='button'   class='btn btn-danger' id='btncanceltestimonial' >Cancel</button>
 
							    </div>  
                             <div class="clearfix"></div>
					   </div> 
						<div class="col-xs-12 people-know">
						<div class="col-xs-12">
							<h4>Testimonial Videos</h4>
						</div>
							<div class="col-xs-12" style="overflow-x: auto;">
							<table class="table table-responsive">
								<thead>
                                    <tr> 
                                       <th>Sorting Handle</th> 
                                        <th>Testimonial Video URL</th> 
                                        <th>Summary</th> 
                                        <th>Action</th>
                                    </tr>
                                    </thead>
                                    <tbody id='divtestimonials'>
									<?php 
										$rowindex=1;
										foreach ($video_testimonials as $item )
										{
											echo "<tr class='ui-state-default' data-id='" . $item['id'] . "'><td><i class='fa fa-arrows'></i></td><td id='tbody-" . $item['id'] . "'><span class='videolink" . $item['id'] . "'>" . $item['videolink'] ."</span>" ;
											echo "</td><td class='videosummary" . $item['id'] . "'>" .  $item['summary']  .  "</td><td>
											<button class='btn-primary btn btn-xs edittestimonial' data-id='" . $item['id'] . "'><i class='fa fa-pencil'></i></button> 
											<button class='btn-danger btn btn-xs deletestimonial' data-id='" . $item['id'] . "'><i class='fa fa-trash'></i></button>

											";
											$rowindex++;
										}

										if($rowindex == 1)
										{
											echo '<tr><td colspan="4">No testimonial exists!</td></tr>'; 
										}
										else 
										{
											echo '<tr><td colspan="4"><button class="btn btn-primary btn-xs btnsavesortingorder">Save Sorting Order</button></td></tr>'; 
										}
									?>
									</tbody>
                                </table>
                            </div>
                        </div>
	 <!--menu38 end-->	
   </div>	 
   <!-- menu39 -->
   <div id="menu39" class="tab-pane maintab">
        <div class="top-head">
			<div class="col-xs-12 col-sm-8">
				<h4>Linkedin Contact Import </h4>
			</div>
			<div class="clearfix"></div>
		</div>
		<div class="col-xs-12 col-md-12  "> 
			<form action="includes/uploader-2.php" class="dropzone" id="linkimport"></form>
			<div class='form-group pad10 text-center'>
			<button class='btn btn-primary btn-lg linkedinimport'>Start Import</button> 

	 <a data-toggle="tab" href="#menu40"  class='btn btn-danger btn-lg linkedinimportlist'>
	 View LinkedIn Import List</a> 
							  </div>
                        </div>
                    </div>
			  <!--menu39 -->
			  <!-- menu80 -->
<div id="menu80" class="tab-pane maintab">
        <div class="top-head">
			<div class="col-xs-12 col-sm-8">
				<h4>Linkedin Contact Import </h4>
			</div>
			<div class="clearfix"></div>
		</div>
		<div class="col-xs-12 col-md-12  "> 
			<form action="includes/uploader-2.php" class="dropzone" id="linkimport"></form>
			<div class='form-group pad10 text-center'>
			<button class='btn btn-primary btn-lg linkedinimport_temp'>Start Import</button> 

	 <a data-toggle="tab" href="#menu81"  class='btn btn-danger btn-lg viewimportedliest'>
	 View LinkedIn Import List</a> 
							  </div>
                        </div>
                    </div>
			  <!--menu80 -->
			  
			 <!-- menu81 -->
   <div id="menu81" class="tab-pane maintab">
       <div class="top-head">
			<div class="col-xs-12 col-sm-8">
					<h4>Linkedin Contacts Imported</h4>
                            </div>
                         <div class="clearfix"></div>
                        </div>
                        <div class="col-xs-12 col-md-12  ">
							<form class='form-inline form-gray'>
						 <div class="form-group">
							<label>Imported LinkedIn Contacts</label> 
							<input type="text" placeholder="Contact Name ..." class="form-control search-control" id="tbconnectname">
						</div> 
				<button type='button' class="btn btn-primary btnblock searchimportedliest">Search</button> </form>
                   <div id='linkedinlist2'></div> 
			</div>
                    </div>
			  <!--menu81 -->
		<!-- menu86 -->
	<div id="menu86" class="tab-pane maintab  ">
		<div class="top-head">
			<div class="col-xs-12 col-sm-8">
				<h4>Duplicate Referrals</h4>
			</div> 
			</div>
			 
			<div class="col-md-12 marg40"> 
				<div id='referrals_duplicates'></div>
			</div> 
     </div>
 <!--menu86 -->	
 
	<?php if($isemployee ==1) : ?>
<!-- menu82 -->
	<div id="menu82" class="tab-pane maintab">
		<div class="top-head">
			<div class="col-xs-12 col-sm-8">
				<h4>Activity Log Form</h4>
			</div>
			 
			</div>
			 
			
			<div class="col-md-10 marg40"> 
			
			<div id='memselecgrid'></div>
			
			<form class=" form-gray-wide"> 
				 
				 <div class="row">
						<div class="col-xs-12 col-md-12">
							 <div class="form-group"> 
								<label>Activity Details:</label> 
								<textarea id="activity_desc" rows="10" class="form-control"></textarea> 
							</div> 
						</div>  
				</div>
				<button type="button" class="btn btn-primary btnblock btn_save_activity" data-id="" 
				data-name="">Save</button>       
<a type="button" href="dashboard.php" class="btn btn-danger ">Cancel</a>  				
			</form> 
		</div> 
     </div>
 <!--menu82 -->	 
 <!-- menu83 -->
	<div id="menu83" class="tab-pane maintab">
		<div class="top-head">
			<div class="col-xs-12 col-sm-8">
				<h4>My Activity Log Entries</h4>
			</div> 
			</div> 
			<div class="col-md-10">  
			<div id='activity_log'></div> 
		</div> 
     </div>
 <!--menu83 -->	
 
 <!-- menu85 -->
	<div id="menu85" class="tab-pane maintab  ">
		<div class="top-head">
			<div class="col-xs-12 col-sm-8">
				<h4>Staff Activity Management</h4>
			</div> 
			</div>
			<div class="col-xs-12 col-md-12 ">
				<form class='form-inline form-gray'>
					<div class="form-group">
						<label>Search Staff</label> 
						<input type="text" placeholder="Contact Name ..." class="form-control search-control" id="tbstaffname">
					</div> 
					<button type='button' class="btn btn-primary btnblock btn_searchstaff">Search</button> </form>
                    
			</div> 
			<div class="col-md-12 marg40"> 
				<div id='staffactivitylog'></div>
			</div> 
     </div>
 <!--menu85 -->	
 
 
<?php endif ; ?>  
			  
	<!-- menu40 -->
	<div id="menu40" class="tab-pane maintab">
		<div class="top-head">
			<div class="col-xs-12 col-sm-8">
					<h4>Linkedin Contacts Imported</h4>
                            </div>
                         <div class="clearfix"></div>
                        </div>
                        <div class="col-xs-12 col-md-12  ">
							<form class='form-inline form-gray'>
						 <div class="form-group">
							<label>LinkedIn Contacts</label> 
							<input type="text" placeholder="Contact Name ..." class="form-control search-control" id="tblinkedincontact">
						</div> 
				<button type='button' class="btn btn-primary btnblock filterlinkedincontact">Search</button> </form>
                   <div id='linkedinlist'></div> 
			</div>
      	</div> 
	<!--menu40 -->
	<!-- menu41 -->
	<div id="menu41" class="tab-pane maintab">
        <div class="top-head">
			<div class="col-xs-12 col-sm-8">
			   <h4>Recently Added Contacts</h4>
	        </div>
		<div class="clearfix"></div>
		</div>
		<div class="col-xs-12 col-md-12  ">
			<form class='form-inline form-gray'>
				<div class="form-group">
					<label>Search Contacts:</label> 
			<input type="text" placeholder="Contact Name ..." class="form-control search-control" id="tblinkedincontact">
			</div> 
			<button type='button' class="btn btn-primary btnblock filterlinkedincontact">Search</button> </form>
                <div id='contactsaddedlastweek'></div> 
			 </div>
        </div>
	 <!-- menu41 -->
	 <!-- menu42 -->
	  <div id="menu42" class="tab-pane maintab">
        <div class="top-head">
			<div class="col-xs-12 col-sm-8">
			   <h4>Common Vocations For Imported Contacts</h4>
	        </div>
		<div class="clearfix"></div>
		</div>
		 <div class="col-xs-12 col-sm-10 padd-5">
          <div class="col-sm-12 col-xs-12"><label class="custom-label">Select Member Vocation:</label></div>
		   <div class="col-sm-12 col-xs-12"> 
			   <div id="comvoc"></div> 
              <select data-placeholder='Choose vocations ...' class="form-control chosen-select " name="member_voc" id="member_voc"  > 
                  <?php
                       foreach ($vocations as $vocation) {
                          echo "<option value='" . $vocation['name'] . "'>" . $vocation['name'] . "</option>";
                       }
                   ?>
             </select>
             
            </div>  
			
			<div class="col-sm-12 col-xs-12"><label class="custom-label">Common Vocations for knows:</label></div>
		   <div class="col-sm-12 col-xs-12"> 
			   <div id="comvoc"></div> 
              <select data-placeholder='Choose vocations ...' multiple class="form-control chosen-select  common_vocations" name="common_vocations[]" id="common_vocations"  > 
                  <?php
                       foreach ($vocations as $vocation) {
                          echo "<option value='" . $vocation['name'] . "'>" . $vocation['name'] . "</option>";
                       }
                   ?>
             </select>
             <small class="pull-right">(Enter comma seperated values)</small>
            </div>  
			
			 
			
		 <div class="col-sm-12 col-xs-12  ">
			 <button type='button' class='btn btn-primary savesettingscv'>Save Settings</button>
		</div>
		
		</div>
		<div class="col-sm-12 col-xs-12  "> 
			 <div id='gridexistcvocs'></div>
		</div>
		
      </div>  
	 <div class="modal fade commonvocationmodal" tabindex="-1" role="dialog" aria-labelledby="commonvocationmodal"
         id="commonvocationmodal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span></button>
                    <h2 class="modal-title" >Add Vocation</h2> 
                </div>
                <div class="modal-body modal-body-no-pad" style='max-height: 520px; overflow-y:scroll; text-align:left'> 
					<div id='settingsvalue'>
					</div>
                </div>
				 <div class="modal-footer" >
					  <button type='button' class='btn btn-primary addcommonvocation'>Add Vocation</button>
				 </div>
            </div>
        </div>
 </div> 
  <!-- menu42 -->
		 <!-- menu43 -->
	<div id="menu43" class="tab-pane maintab">
        <div class="top-head">
			<div class="col-xs-12 col-sm-8">
			   <h4>Recently Registered LinkedIn Contacts</h4>
	        </div>
		<div class="clearfix"></div>
		</div>
		<div class="col-xs-12 col-md-12  ">
			  
        <div id="linkedinsignuplist"></div>     
             </div>
        </div>
	 <!-- menu43 --> 
	  <!-- menuExportSpread -->
	<div id="menuExportSpread" class="tab-pane maintab">
        <div class="top-head">
			<div class="col-xs-12 col-sm-8">
			   <h4>Export to Spreadsheet</h4>
	        </div>
		<div class="clearfix"></div>
		</div>
		<div class="col-xs-12 col-md-12">
			<div> 
			<a class="btn btn-primary" href="/export_to_spreadsheet.php" role="button">Dump All User Data</a> 
			</div>     
		</div>
        </div>
	<!-- menuExportSpread --> 
    <!-- menu44 -->
	<div id="menu44" class="tab-pane maintab">
         
		  <div class="col-xs-12 col-md-8">
		   <div id="interestedmembers"></div> 
		  </div> 
		   
		  <div class="col-xs-12 col-md-4 hidden-xs">
	     <div class="globalsearch">
			<form class='  form-gray-wide '  >
			<div class='row'>
			<div class="col-xs-12 col-sm-12">
			   <h4>Search Members </h4>
            </div>
			  
            <div class="col-xs-12 col-sm-12">
               <label>Name:</label> 
                 <input type="text"  placeholder="Specify Name" id='dmname' class="form-control dmname">
            </div>
			<div class="col-xs-12 col-sm-12">
				<label>City:</label> 
                  <select data-placeholder="Specify Cities"  id="dmcity" class='chosen-select dmcity' multiple > 
                     <?php 
                        foreach ($cities as $city)
                        {
							echo "<option value='" . $city['name'] . "'>" . $city['name'] . "</option>";
                        }
                     ?>
                  </select> 
		    </div>
			<div class="col-xs-12 col-sm-12">
				<div class="form-group">
					<label>Vocation:</label> 
					<select data-placeholder='Choose vocations ...' multiple class="form-control chosen-select dmvocations" name="dmvocations[]" id="dmvocations"  > 
						  <?php
							   foreach ($vocations as $vocation) {
								  echo "<option value='" . $vocation['name'] . "'>" . $vocation['name'] . "</option>";
							   }
						   ?>
					 </select>
                </div> 
            </div>  
			<div class="col-xs-12 col-sm-12">
				<button type='button' class="btn btn-primary btnblock btntopgap btnsearchdmmembers">Search</button>   
			</div>  
            
			</div> 
           </form>
		    </div>
		  </div>
		  
		   <div id="mypartnerslist"></div> 
		  </div>
      
	 <!-- menu44 -->
 
	<!-- menu46 -->
	<div id="menu46" class="tab-pane maintab">
            <div class="top-head">
                <div class="col-xs-12 col-sm-8">
                    <h4>Reverse Tracking Partner</h4>
                </div>
                <div class="clearfix"></div>
            </div>
            <div class="col-xs-12 col-md-12  ">
				 <form class='  form-gray-wide'>

            <div class='row'>
                <div class="col-xs-12 col-md-3">
						 <div class="form-group">
							<label>Phone &amp; Name:</label> 
							<input type="text" placeholder="Contact Name or Phone ..." class="form-control search-control" id="tbknowsearchkey"> 
                        </div> 
                </div> 
                <div class="col-xs-12 col-md-3 ">
						 <div class="form-group">
							<label>Location:</label>   
                            <select data-placeholder="Specify Cities"    id="reversetracklocation" class='form-control search-control revtracklocation ' > 
                            <option value=''>--Select Location---</option> 
                            <?php
                            
                                echo $citynames;
                            ?>
                        </select>
                    </div> 
                </div> 
              
                <div class="col-xs-12 col-md-6  ">
                     <div class="form-group">
							<label>Select Vocations:</label> 
                            <select data-placeholder="Select vocations" id='reverselookupvoc' name='reverselookupvoc' multiple class="form-control reverselookupvoc"> 	 
										<option value=''>Vocation</option> 
										<?php 
										foreach($vocations as $vocitem)
										{
											 echo "<option value='" .$vocitem['name']  . "'>" . $vocitem['name'] . "</option>";
										}
										?>
							 </select> 
						</div> 
                 </div>  
            </div> 
             <div class='row'>
					<div class="col-xs-12 col-md-6">
						 <div class="form-group"> 
							<label>Know Tags:</label> 
                            <select data-placeholder='Specify Tags ...'  multiple  name="reversetracktags"  class="form-control chosen-select reversetracktags" id="reversetracktags"> 
                            <?php
                                foreach ($alltags as $tag)
                                {
                                    echo "<option value='" . $tag['tagname'] . "'>" . $tag['tagname'] . "</option>";
                                }
                            ?>
                            </select> 
                        </div> 
                    </div>  
					
					<div class="col-xs-12 col-md-6">
						<label>Know Lifestyles:</label> 
						<select data-placeholder="Select Lifestyles" id="reversetracklifestyle" class='chosen-select reversetracklifestyle' multiple >
							<?php
								foreach ($lifestyles as $lifestyle)
								{
									echo "<option value='" . $lifestyle['name'] . "'>" . $lifestyle['name'] . "</option>";
								}
							?>
						</select>
						<small class="pull-right">(Multiple lifestyle can be selected)</small>
					</div>  
                </div>
				<div class='row'> 
					<div class="col-xs-12 col-md-6">
						<label>Zip:</label> 
						<input type="text" placeholder="Zip ..." class="form-control search-control" id="tbzip">  
					</div> 
				</div>
				<div class='row'>
				<div class="col-xs-12 col-md-12">
                    <button type='button' class="btn btn-primary btnblock reversetrackpartner">Search</button>  
</div>						
</div>					
				</form>
                   <div id='reversetrackinglist'></div>  
                   <div id="rtmember"></div>
			</div>
      	</div> 
	<!--menu46 --> 
    <!--Menu12--> 
	<div id="menu12" class="tab-pane fade maintab"> 
		<div class="top-head">
			<div class="col-xs-12 col-sm-8">
				<h4>TRIGGERS</h4>
			</div>
			<div class="clearfix"></div>
		</div>
		<div class="col-xs-12 col-sm-12 no-padd  ">
			<div>
			 <div class="col-sm-12 col-xs-12 ">
			    <h4><b>Add New Trigger</b></h4>
			 </div>
			 <div class="col-sm-9 col-xs-12 padd-8">
				<input type="text" placeholder="Add New Trigger" class="form-control triggerName">
             </div>
             <div class="col-sm-2 col-xs-12 padd-8 text-center">
             	<button style="margin-top: 0 !important" class="btn btn-primary btnblock addNewTrigger">ADD NEW</button>
             </div>
             <div class="clearfix"></div>
             </div>
             </div>
            <div class="col-xs-12 people-know">
					<div class="col-xs-12">
						<h4>TRIGGERS</h4>
						</div>
						<div class="col-xs-12" style="overflow-x: auto;">
							<table class="table table-responsive">
								<thead>
                                    <tr>
                                        <th>Question #</th>
                                        <th>Trigger Question</th> 
                                        <th>Action</th>
                                    </tr>
                                    </thead>
                                    <tbody >
									<?php
										$rowindex=1;
										foreach ($triggers as $trigger )
										{
											echo "<tr><td>" . $rowindex. "</td><td id='tbody-" . $trigger['id'] . "'><span id='trigbody-" . $trigger['id'] . "'>" . $trigger['trigger_question'] ."</span>" ;
											echo "<div class='edittrig' id='edittrig-" . $trigger['id'] . "''>
											<input type='text' id='trigtext-" . $trigger['id'] . "' value='" . $trigger['trigger_question'] . "' />
											<input type='button' data-id='" . $trigger['id'] . "'  class='btn btn-primary btn-xs updatetrig' value='Update'/></div>";
											echo "</td><td>
											<button class='btn-primary btn btn-xs edittrigger' data-id='" . $trigger['id'] . "'><i class='fa fa-pencil'></i></button>
											<button class='btn-danger btn btn-xs removetrigger' data-id='" . $trigger['id'] . "'><i class='fa fa-times-circle'></i></button></td></tr>";
											$rowindex++;
										}
									?>
									</tbody>
                                </table>
                            </div>
                        </div> 
                </div>
				<!--Menu12-->
                <!--Menu13-->
                <?php  if($user_pkg == "Gold") { ?>	
				<div id="menu13" class="tab-pane fade maintab"> 
                            <div class="top-head">
                                <div class="col-xs-12 col-sm-8">
                                    <h4>My Group Partners</h4>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                            <div class="col-xs-12 col-sm-12 col-sm-offset-0 no-padd"> 
                                    <div class="col-sm-12 col-xs-12 padd-8">
                                        <h4><b>Your Group</b></h4>
                                    </div>
                                    <div class="col-sm-5 col-xs-12 padd-8">
                                        <select class="form-control fetGroupMembers">
                                            <option value="null">--- Select Group ---</option>
                                            <?php
                                            foreach ($mygroups as $group ) {
                                                echo "<option value='" . $group['id'] . "'>" . $group['grp_name'] . "</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    <div class="col-sm-5 col-xs-12 padd-8">
                                        <select id='groupMembers' class="form-control groupMembers">
										</select>
                                    </div>
                                    <div class="col-sm-2 col-xs-12 padd-8 text-center">
                                        <button style="margin-top: 0 !important" class="btn btn-primary btnblock showselectedProfile">VIEW PROFILE</button>
                                   </div>
                                    <div class="clearfix"></div> 
							</div> 
							<div class="col-xs-12 col-sm-12  ">	
                               <div id='displayProfile'></div>
                            </div>  	
                </div> 
				<?php  } ?>	
				<!--Menu13-->
				<!--menu16-->
				<?php  if($user_pkg == "Gold") { ?>	
				<div id="menu16" class="tab-pane fade maintab"> 
                            <div class="top-head">
                                <div class="col-xs-12 col-sm-8">
                                    <h4>My Highest Rated Group Partners</h4>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                            <div class="col-xs-12 col-sm-12 col-sm-offset-0 no-padd"> 
                                    <div class="col-sm-12 col-xs-12 padd-8">
                                        <h4><b>Select Group</b></h4>
                                    </div>
                                    <div class="col-sm-5 col-xs-12 padd-8">
                                        <select class="form-control ratedmemberGroup">
                                            <option value="null">--- Select Group ---</option>
                                            <?php
												foreach ($mygroups as $group )
												{
													echo "<option value='" . $group['id'] . "'>" . $group['grp_name'] . "</option>";
												}
                                            ?>
                                        </select>
                                    </div>
                                    <div class="col-sm-5 col-xs-12 padd-8">
									 <select id='memberVocation' name='memberVocation' class="form-control memberVocation"> 	 
										<option value=''>Select Vocation</option> 
										<?php 
											foreach($vocations as $vocitem)
											{
												 echo "<option value='" .$vocitem['name']  . "'>" . $vocitem['name'] . "</option>";
											}
										?>
									</select> 
                                    </div>
                                    <div class="col-sm-2 col-xs-12 padd-8 text-center">
                                        <button style="margin-top: 0 !important" class="btn btn-primary btnblock showratedpartners">SHOW PARTNERS</button>
                                   </div>
                                    <div class="clearfix"></div> 
							</div> 
					<div class="col-xs-12 col-sm-12  ">	
						<div id='getratedpartners'></div>
                    </div>  	
                </div>
				<?php  } ?>	
				<!--menu16-->
				<!--menu17-->
				 
				<div id="menu17" class="tab-pane fade maintab"> 
                            <div class="top-head">
                                <div class="col-xs-12 col-sm-8">
                                    <h4>Suggested Connections</h4>
                                </div>
                                <div class="clearfix"></div>
                            </div>
						<div class="col-xs-12 col-sm-6  ">	
						<div class='infobox'>
						<?php
							$templog = $link->query("SELECT  * from activity_log where uid='$user_id' and lkey='scgridpage' order by id desc ");
							$scgridpage = $templog->fetch_array();
						?>
						<p>Your recently worked on page <span id='lpage'><?php echo$scgridpage['lvalue'];  ?></span>
						<a   href='#' class='btn btn-primary btn-xs page-link showreferrals' data-pagesize='10' data-pageno='<?php echo $scgridpage['lvalue']; ?>'  href=''>
							Click to jump there
						</a>
						</p>
						<?php  
						?>  	
						</div>
						</div>	
						<div class="col-xs-12 col-sm-6  ">	
						<div class='infobox'>
							<p>You are currently working on Page <span id='cpage'>1</span> <button data-pageno='0' class='btn btn-primary btn-xs savesuggestcpage'>Save Current Working Page</button></p>
						</div>
						  
						</div> 
						<div class="col-xs-12 col-sm-12">	
						<div id='suggestedconnects'></div> 
						</div>  
                </div>
				 	
				<!--menu17-->
				<!--menu18-->
			<div id="menu18" class="tab-pane fade maintab">
				<div class="top-head">
					<div class="col-xs-12 col-sm-8">
						<h4>KNOW Stats</h4>
					</div>
				<div class="clearfix"></div>
			</div>
			<div class="col-xs-12 col-sm-12  ">
				<?php
					$sortfield = 'abcdefghijklmnopqrstuvwxyz';
				?>
				<h2>Select starting letter of a partner name:</h2>
				<nav aria-label="Page navigation"><ul class="pagination">
				<?php
					for($i=0; $i<26; $i++ )
					{
						echo "<li><span   class='fetchknowstats' data-key='". $sortfield[$i] . "'  >". $sortfield[$i] . "</span></li>";
					}
				?>
				</ul></nav> 
				<div id='reloadknowstatistics'></div>
			</div>
		</div> 
		<!--menu18-->
		<!--menu19-->
		<div id="menu19" class="tab-pane fade maintab">
					<div class="top-head">
						<div class="col-xs-12 col-sm-8">
							<h4>List of Know Suggestios for <span id='partnername'></span></h4>
                        </div>
                        <div class="col-xs-12 col-sm-4">
                        	<a data-toggle='tab' class='btn btn-primary btn-xs' href='#menu18'>Back to partner list</a>
                        </div>
                   	<div class="clearfix"></div>
                    </div>
					<div class="col-xs-12 col-sm-12  ">
						<?php
						  	$sortfield = 'abcdefghijklmnopqrstuvwxyz';
						  	?> 
					<div id='knowsuggesthistory'></div>
					</div>
				</div> 
<!--menu19--> 
<!--menu20-->
<div id="menu20" class="tab-pane fade maintab">
	<div class="top-head">
		<div class="col-xs-12 col-sm-8">
			<h4>Inbox</h4>
		</div>
		<div class="col-xs-12 col-sm-4">
		</div>
		<div class="clearfix"></div>
		</div>
		<div class="col-xs-12 col-sm-12">	
			<?php
			$sortfield = 'abcdefghijklmnopqrstuvwxyz';
			?> 
			<div id='inboxgrid'></div>
		</div> 
		</div> 
		<!--menu20-->
		<!--menu21-->
		<div id="menu21" class="tab-pane fade maintab"> 
			<div class="top-head">
				<div class="col-xs-12 col-sm-6">
					<h4>Referral Suggestion Mailbox <a href="<?php echo $help_data_buttons[2]['helpvideo']; ?>" target="_blank" ><i class='fa fa-arrow-right' ></i><span style="color:red;"> Help</span></a></h4>
				</div>			
			 
					<div class="clearfix"></div> 
                  </div>  
				  <div class="col-xs-12 people-know">
					<form class='form-inline form-gray'>
						 <div class="form-group">
							<label>Referrals given</label> 
							<input type="text" placeholder="Receipent Name ..." class="form-control search-control" id="searchreceipent">
						</div> 
						<button class="btn btn-primary btnblock searchmailbox">Search</button> </form>
					</div>
			<div class="col-xs-12 col-sm-12  " >


			<div class='marg4'>

  <!-- Nav tabs -->
  <ul class="nav nav-tabs" role="tablist">
    <li role="presentation" class="active"><a href="#home"  data-mf="0" class="btn-mailfilter loadmyinbox "  aria-controls="home" role="tab" data-toggle="tab">Referral Mails</a></li>
    <li role="presentation"><a href="#profile"  data-mf="1" class="btn-mailfilter  loadtriggerinbox" aria-controls="profile" role="tab" data-toggle="tab">Trigger Mails</a></li>
    <li role="presentation"><a href="#messages"  data-mf="1" class="btn-mailfilter loadlinkedininvites" aria-controls="messages" role="tab" data-toggle="tab">LinkedIn Contacts</a></li>
    <li role="presentation"><a href="#settings"  data-mf="3" class="btn-mailfilter loaddirectconnectionmail" aria-controls="settings" role="tab" data-toggle="tab">Direct Connection Mails</a></li>
  </ul>

  <!-- Tab panes -->
  <div class="tab-content">
    <div role="tabpanel" class="tab-pane active" id="home">
	<div id='myoutboxgrid0'></div>  
	</div>
    <div role="tabpanel" class="tab-pane" id="profile">
	<div id='myoutboxgrid1'></div>  
	</div>
    <div role="tabpanel" class="tab-pane" id="messages">
	<div id='myoutboxgrid2'></div>  
	</div>
    <div role="tabpanel" class="tab-pane" id="settings">
	<div id='myoutboxgrid3'></div>  
	</div>
  </div>

</div>
 </div> 	 
    </div> 
	<!--menu21-->
	<!--menu21b-->
		<div id="menu21b" class="tab-pane fade maintab"> 
			<div class="top-head">
				<div class="col-xs-12 col-sm-2">
					<h4>Inbox</h4>
				</div>	

<div class="col-xs-12 col-sm-10">
					<form class='form-inline form-gray'>
						 <div class="form-group"> 
							<input type="text" placeholder="Receipent Name ..." class="form-control search-control" id="searchreceipent">
						</div> 
						<button class="btn btn-primary btnblock searchmailbox"><i class='fa fa-search'></i> Search</button> </form>
					</div>

					 
                  </div>  
	 
			<div class="col-xs-12 col-sm-12  " >
 
			<div class='marg4'>

  <!-- Nav tabs -->
  <ul class="nav nav-tabs" role="tablist">
    <li role="presentation" class="active"><a href="#homein"  data-mint="0" class="loadinbox"  aria-controls="homein" role="tab" data-toggle="tab"> Messages</a></li>
    <li role="presentation" ><a href="#conreqin"  data-mint="10" class="loadinbox"  aria-controls="conreqin" role="tab" data-toggle="tab"> Connection Requests</a></li>
     </ul>

  <!-- Tab panes -->
  <div class="tab-content">
    <div role="tabpanel" class="tab-pane active" id="homein">
		<div id='myinboxdc0'></div>  
	</div> 
	
	<div role="tabpanel" class="tab-pane  " id="conreqin">
		<div id='myinboxdc10'></div>  
	</div> 
	
  </div>

</div>
 </div> 	 
    </div> 
	<!--menu21b-->
	
	<!--menu22-->
	<div id="menu22" class="tab-pane fade maintab">
	<div class="top-head">
	<div class="col-xs-12 col-sm-8">
	<h4>Manage Loyalty Score</h4>
	</div>
	<div class="col-xs-12 col-sm-4">
	</div>
	<div class="clearfix"></div>
	</div>
	<div class="col-xs-12 col-sm-12  " >
	<div id='showloyaltypoints'></div> 
	</div> 		
	</div> 
	<!--menu22-->
	<!-- menu23 -->
	<div id="menu23" class="tab-pane maintab">
		<div class="top-head">
			<div class="col-xs-12 col-sm-8">
				<h4><i class='fa fa-users'></i> View Connections Report</h4>
             </div>
                            <!--<div class="col-xs-12 col-sm-4 text-center">
                                <button class="btnblock">SEARCH PAGE</button>
                            </div>-->
                            <div class="clearfix"></div>
                        </div>
                        <div class="col-xs-12 col-md-12  ">
						<p>You can view connection report based on the partner vocations and group. </p>
						<div class='row'>
							<div class="col-sm-1">
								<p class="text-right pad10">Vocation</p>
							</div>
							<div class="col-sm-3 pad10">
								<select id='rrvocation'   name='rrvocation' 
									data-placeholder='Choose Group ...' class='group-select' > 
									<option value='all'></option>
									<?php 
										foreach ($vocations as $vocation )
										{
											echo "<option value='" . $vocation['name'] . "'>" . $vocation['name'] . "</option>";
										}
									?>
								</select>
							</div>
							<div class="col-sm-1">
								<p class="text-right pad10">Group:</p>
							</div>
							<div class="col-sm-3 pad10">
								<select id='rrselectgroup'   name='rrselectgroup' 
								data-placeholder='Choose Group ...' class='group-select' > 
								<option value='all'></option>
								<?php 
									foreach ($getGroups as $group )
									{
										echo "<option value='" . $group['id'] . "'>" . $group['name'] . "</option>";
									}
								?>
							</select>
						</div><div class="col-sm-1">
							<button type="submit" class="btn btn-search-o searchpartners" >Search</button>  
						</div>
						</div>
					<div class="table-responsive" id='newknowentrylist'>
					</div>
                </div>
            </div>
			<!--menu23 -->
			<!-- menu45 -->
				<div id="menu45" class="tab-pane maintab">
					<div class="top-head">
						<div class="col-xs-12 col-sm-8">
							<h4><i class='fa fa-users'></i> New Signups</h4>
                        </div>
						<!--<div class="col-xs-12 col-sm-4 text-center">
							<button class="btnblock">SEARCH PAGE</button>
                        </div>-->
                            <div class="clearfix"></div>
                        </div>
                        <div class="col-xs-12 col-md-12  ">
						<p>You can view new signup during a specific time period. Simply specify the date range.</p>
						<div class='row'> 
							<div class="col-sm-3 pad10">
								 <input type='text' id='startDate'  class='form-control txtdatepicker' placeholder='Starting Date'/>
							</div>  
							<div class="col-sm-3 pad10">
								 <input type='text' id='endDate' class='form-control txtdatepicker' placeholder='Ending Date'/>
							</div><div class="col-sm-2">   
                                  <button style="margin-top: 15px;" type="submit" class="btn btn-primary  btn-search-o shownewsignups" >Show New Signups</button>
  
							</div>
						</div>
					<div class="table-responsive" id='newsignups'>
					</div>
                </div>
            </div>
  <!--menu45 -->  
  <!-- menu47 -->
  <div id="menu47" class="tab-pane maintab">
	<div class="top-head">
		<div class="col-xs-12 col-sm-8">
			<h4><i class='fa fa-users'></i> Manage Tags</h4>
		</div> 
		<div class="clearfix"></div>
	</div>
	<div class="col-xs-12 col-md-10 col-md-offset-1 ">
                                    
                        <div class="search-loc" style="background: none;border: 1px solid #dbdbdb">
                                    <div class="col-sm-12 col-xs-12 padd-8">
                                        <h4><b>Existing Tags:</b></h4>
                                    </div>
                                    <div class="col-sm-5 col-xs-12 padd-8">
                                        <select class="form-control fetchTag">
                                            <option value="null">-select-</option>
                                            <?php
                                            foreach ($alltags as $tags) {
                                                echo "<option value='" . $tags['id'] . "'>" . $tags['tagname'] . "</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    <div class="col-sm-4 col-xs-12 padd-8">
                                        <input type="text" data-val='' class="form-control editTag">
                                    </div>
                                    <div class="col-sm-2 col-xs-12   text-center">
                                        <button style="margin-top: 0 !important" class="btnblock updTag">UPDATE</button> 
                                    </div>
                      <div class="clearfix"></div>
                    </div>  
	</div>
	<div class="col-sm-12 col-xs-12 padd-8 text-center"><br></div>
					<div class="col-xs-12 col-sm-10 col-sm-offset-1 no-padd  ">
						<div class="search-loc" style="background: none;border: 1px solid #dbdbdb">
                                    <div class="col-sm-12 col-xs-12 padd-8">
                                        <h4><b>Add New Tag</b></h4>
                                    </div>
                                    <div class="col-sm-5 col-xs-12 padd-8">
                                        <input type="text" placeholder="Add New Tag"  class="form-control tagname">
                                    </div>
                                    <div class="col-sm-2 col-xs-12 padd-8 text-center">
                                        <button style="margin-top: 0 !important" class="btnblock addTag">ADD NEW
                                        </button>
                                    </div>
                                    <div class="clearfix"></div>
                  </div>
                </div> 
            </div>
            <!--menu47 -->  
            <?php if($_user_role == 'admin'  ) { ?>
            <!-- menu48 -->
<div id="menu48" class="tab-pane maintab">
                        <div class="top-head">
                            <div class="col-xs-12 col-sm-8">
                                <h4><i class='fa fa-users'></i> Incomplete Signups</h4>
                            </div> 
                            <div class="clearfix"></div>
                        </div>
                           
                        <div class="col-xs-12 col-md-12  ">
             
                
        <div id="unfinishedsignup"></div>     
 <div class="modal fade intromailtemplate" tabindex="-1" role="dialog" aria-labelledby="emailunifinishsignup" id="emailunifinishsignup"> 
                  <div class="modal-dialog "  > 
                  <div class="modal-content"> 
                 <div class="modal-header"> 
                 <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span></button> 
                 <h2 class="modal-title" >Unfinished Signup Email Template</h2>  
                 </div> 
                 <div class="modal-body text-left "  >
                  <div id="mailbody"><?php
                     
                     $ds = DIRECTORY_SEPARATOR;
                     $apppath = '';
                     $path =  $_SERVER['DOCUMENT_ROOT'] . $ds  ;  
                    
                         if(  file_exists( $path . "templates/unfinishsignuptemplate01.txt" ) )
                         {
                             $mailbody = file_get_contents( $path . "templates/unfinishsignuptemplate01.txt"  ) ;  
                             echo $mailbody; 
                         }
                      
                     ?></div> 
                 </div> 
                 <div class="modal-footer clearfix" > 
                 <button   class="btn btn-primary sendemailforunifinishsignup" >Send Mail</button> 
                 <button data-dismiss="modal"  class="btn btn-danger" >Cancel</button> 
                 </div> 
                 </div> 
                 </div> 
            </div>  

    </div>
	 </div>
  <!--menu48 --> 
			
  <!-- menu62-->
	<div id="menu62" class="tab-pane maintab">
		<div class="col-xs-12 col-md-12">
			<h4>Top Trending Search</h4>
		</div>
		  
		<div class="col-xs-12 col-md-12">
			<div id='trendingsearchgrid' class='marg4'></div>
		</div>
	</div>
 <!--menu62 --> 	
		<!-- menu64-->
	<div id="menu64" class="tab-pane maintab">
		<div class="col-xs-12 col-md-12">
			<h4>US City Zip Codes</h4>
		</div>
		<div class="col-xs-12 col-md-12">
			<ul class='pagination'>
			<?php 
			$alphastring = 'abcdefghijklmnopqrstuvwxyz';
			for($i=0; $i < 26; $i++)
			{
				$letter = substr( $alphastring, $i ,1);
				echo "<li><a href='#menu64' class='managezipcode' data-citystart='$letter'>" . $letter  . "</a></li>";
			} 
			?>
			
			</ul>
		
			<div id='uscityzicodes' class='marg4'></div>
		</div>
	</div>
<!-- menu64 -->	

<!-- menu66-->
<div id="menu66" class="tab-pane maintab fade">
	
	<div class="col-xs-12 col-sm-8">
		<h4>Manage City Listing</h4>
	</div><div class="col-xs-12 col-sm-8">
		<div id='newcitylistings' ></div>
	</div>
</div>  

<!-- menu66-->

<!-- menu67-->
<div id="menu67" class="tab-pane maintab fade">
	
	<div class="col-xs-12 col-sm-8">
		<h4>Outgoing Profile Claim Emails</h4>
	</div>
	<div class="col-xs-12 col-sm-10">
		 
	<ul class="nav nav-tabs" role="tablist">
		<li role="presentation" class="active"><a href="#oboxprofileclaims_tab1"  role="tab" data-toggle="tab"> Search by members</a></li>
		<li role="presentation" ><a href="#oboxprofileclaims_tab2"  role="tab" data-toggle="tab"> Search by non-members</a></li>
	</ul> 
	<div class="tab-content">
		<div role="tabpanel" class="tab-pane active" id="oboxprofileclaims_tab1">
			<div id='oboxprofileclaims' ></div>
		</div> 
		
		<div role="tabpanel" class="tab-pane  " id="oboxprofileclaims_tab2">
			<div id='oboxprofileclaimspublic' ></div>
		</div>  
	</div>
    
	</div>
</div>  

<!-- menu67-->
<!-- menu68-->
<div id="menu68" class="tab-pane maintab fade"> 
	<div class="col-xs-12 col-sm-8">
		<h4>Email Configuration</h4>
	</div>
	<div class="col-xs-12 col-sm-10">
	  <div class="form-group">
			<label for="emheading">Email Heading</label>
			<input type="text" class="form-control" id="emheading" placeholder="Email Heading">
		  </div>
	</div>
	
	<div class="col-xs-12 col-sm-10">
	  <div class="form-group">
			<label for="emheading">Email Body</label>
			<textarea type="text" class="form-control" id="embody" placeholder="Email Content"></textarea>
		  </div>
	</div>
	
	
	<div class="col-xs-12 col-sm-10"> 
		<button type="submit" class="btn btn-primary btnepsave">Submit</button>
	</div>	 
<div class="col-xs-12 col-sm-10 pad40"> 
	<div id='emailgrid'></div>
</div>
	 
</div>
<!-- menu68--> 

<!-- menu69-->
<div id="menu69" class="tab-pane maintab fade"> 
	<div class="col-xs-12 col-sm-12">
		<h4>Select Client</h4>
	</div>
	
	<div class="col-xs-12 col-sm-6">
	<div class='globalsearch'>
	<label for="em_client">Search Client:</label> 
	<div class='row'>
	<div class="col-xs-12 col-sm-8">
	
		<div class="form-group"> 
			<input type="text" class="form-control " id="em_client" placeholder="Client Name"> 
		</div>
		
	</div>
	<div class="col-xs-12 col-sm-2">
	
		<div class="form-group">  
			<input type="button" class="btn btn-primary " id="btn_srhclient" value='Search'  >
		</div>
		
	</div>	
	 </div> 
	</div> 
	</div>  
	
<div class="col-xs-12 col-sm-12 marg40">
	 
			  <ul class="nav nav-tabs" role="tablist">
				<li role="presentation" class="active"><a href="#allactmembers" aria-controls="homein" role="tab" data-toggle="tab"> Active Members</a></li>
				<li role="presentation" ><a href="#allinactmembers" aria-controls="conreqin" role="tab" data-toggle="tab"> Members</a></li>
				<li role="presentation" ><a href="#exclients" aria-controls="exclients" role="tab" data-toggle="tab"> Ex-clients</a></li>
				 
			</ul> 
			  <div class="tab-content">
				<div role="tabpanel" class="tab-pane active" id="allactmembers">
					<div id='actmembersgrid'></div>  
				</div> 
				
				<div role="tabpanel" class="tab-pane  " id="allinactmembers">
					<div id='inactmembersgrid'></div>  
				</div> 
				<div role="tabpanel" class="tab-pane  " id="exclients">
					<div id='exclientsgrid'></div>  
				</div> 
	 </div>

</div>	
<div class="col-xs-12  ">
<div id='emseqloading'></div> 
</div>
 <div class="row marg40 clearfix" id='tl_box'>
    
<div class='col-md-12'>
<div class="tl-box" >
<div class="pad10 text-center">
		<h4 class='white'>Email Sequence Assigned <span id="sp_nameselected"> to Jeff Eisenberg</span></h4>
<hr/>	
</div> 
<ul id="events-tl"></ul>

<div class="pad10 "> 
<hr/>	<a href="javascript:void(0);" class="btn btn-primary btnassignemail" id="add">Add New Email</a> 
</div>
</div>
		 
</div> 
</div> 
</div>  
<!-- menu69-->

 
<!-- menu71-->
<div id="menu71" class="tab-pane maintab fade"> 
	<div class="col-xs-12 col-sm-12">
		<h4> Voice Mails Logs</h4>
	</div> 
	  
	  <div class="col-xs-12 col-sm-6">
	<div class='globalsearch'>
	<label for="em_client">Search Client:</label> 
	<div class='row'>
	<div class="col-xs-12 col-sm-8">
	
		<div class="form-group"> 
			<input type="text" class="form-control " id="vm_client" placeholder="Client Name"> 
		</div>
		
	</div>
	<div class="col-xs-12 col-sm-2">
	
		<div class="form-group">  
			<input type="button" class="btn btn-primary " id="btn_srhvmclient" value='Search'  >
		</div>
		
	</div>	
	 </div> 
	</div> 
	</div>
	
<div class="col-xs-12 col-sm-12 marg40">

		<ul class="nav nav-tabs navactionlog" role="tablist">
				<li role="presentation" class="active"><a href="#voicemail_tab" aria-controls="homein" role="tab" data-toggle="tab"> Clients with Voice Mail</a></li>
				<li role="presentation" ><a href="#novoicemail_tab" aria-controls="conreqin" role="tab" data-toggle="tab"> Clients without Voicemail</a></li>
				 </ul> 
			  <div class="tab-content">
				<div role="tabpanel" class="tab-pane active" id="voicemail_tab">
					<div id='voicemail_logs'></div> 
				</div> 
				
				<div role="tabpanel" class="tab-pane  " id="novoicemail_tab">
					<div id='novoicemail_logs'></div> 
				</div> 
				
			</div> 
</div>	
 
 
<!-- menu70-->
<div id="menu70" class="tab-pane maintab fade">
	<div class="top-head" id='voicemailform'>
		<div class="col-xs-12 col-sm-8">
		<h4>Voice Mail Entry</h4>
		</div>
		<div class="col-md-10"> 
			<form class=' form-gray-wide' > 
				<div class='row'>
					<div class="col-xs-12 col-sm-6">
							 <div class="form-group">
								<label>Voicemail Assign Date:</label> 
								<input type="text" placeholder="Assign Date" id='vm_assigndate'class="form-control"> 
							</div> 
					</div>  
					
					<div class="col-xs-12 col-sm-2">
					<div class="form-group">
						<label for="vm_schedulehr">Hour</label>
						<select class="form-control" id="vm_schedulehr">
							<option>00</option>
							<option>01</option>
							<option>02</option>
							<option>03</option>
							<option>04</option>
							<option>05</option>
							<option>06</option>
							<option>07</option>
							<option>08</option>
							<option>09</option>
							<option>10</option>
							<option>11</option>
							<option>12</option>
						</select>
						
					  </div> 
				</div> 
				
				<div class="col-xs-12 col-sm-2">
					<div class="form-group">
						<label for="vm_schedulemin">Minute</label>
						<select class="form-control" id="vm_schedulemin">
							<option>00</option>
							<option>05</option>
							<option>10</option>
							<option>15</option>
							<option>20</option>
							<option>25</option>
							<option>30</option>
							<option>35</option>
							<option>40</option>
							<option>45</option>
							<option>50</option>
							<option>55</option>
						</select>
						
					  </div> 
				</div> 
				
				<div class="col-xs-12 col-sm-2">
					<div class="form-group">
						<label for="vm_scheduleper">Period</label> 
						<select class="form-control" id="vm_scheduleper">
							<option>AM</option>
							<option>PM</option> 
						</select> 
					  </div> 
				</div> 
					 
				</div> 
				 <div class='row'>
						<div class="col-xs-12 col-md-12">
							 <div class="form-group"> 
								<label>Voice Mail Description:</label> 
								<textarea id="vm_description" rows='10' class="form-control" ></textarea> 
							</div> 
						</div>  
				</div>
				<button type='button' class="btn btn-primary btnblock cfg_save_voicemail">Save</button>       
<a type='button' href='dashboard.php' class="btn btn-danger ">Cancel</a>  				
			</form>
			
		</div>
		
 <div class='col-md-12'>
 
<div id='vmevent-loading'></div>  
	<div class="tl-box" >
	<div class="pad10 text-center">
		<h4 class='white'>Voicemail Tracking Timeline <span id="vm_nameselected"></span></h4>
	<hr/>	
	</div> 
	<ul id="vmevent-tl"></ul> 
	
<div class="pad10 "> 
<hr/> <a href="#voicemailform"  style='color: #fff'>To add new voicemail scroll to top</a> 
</div>


	</div> 
</div> 

	</div>
    
</div>  
<!-- menu70--> 


</div>   
<!-- menu71-->

<!-- menu73-->
<div id="menu73" class="tab-pane maintab"> 
   <div class="col-xs-12 col-md-12"> 
	 
		<div class="panel-body"><p><strong> Members in 3-Touch Program</strong></p> 
		</div>
	 
	</div>
	<div class="col-xs-12 col-md-12"> 
	<div id='progmems'></div>
	</div> 
	<div class="col-xs-12 col-md-12"> 
	<div id='memquestions'>
	
	</div>
	</div> 
	
</div>
<!-- menu73 -->
 
<!-- menu74-->
<div id="menu74" class="tab-pane maintab"> 
   <div class="col-xs-12 col-md-12"> 
  <div class='panel panel-default  panel-success'> 
		 <div class='panel-heading'><h2>Manage Program Questions</h2></div><div class='panel-body'> 
			
			 <div class='form-group'>   
			 <label>Program Name:</label> 
			 <div class='form-group'> 
			 <select id='programname' class='form-control' >
				<option value='1'>3 Touch Program</option>
			 </select>
			 </div>
			 <label>New Question:</label> 
			 <div class='form-group'> 
			 <textarea type='text' id='tbquest' rows='10' class='form-control' placeholder='Question for client' ></textarea> 
			 </div><div class='form-group'><input type='button' id='btnsavequest' class='btn btn-primary' Value='Save Question' /> 
			 <input type='button' id='btncancel' class='btn btn-danger' Value='Cancel' /> </div> 
			</div> 
			<hr/>
			
		<h3>Existing Questions</h3>
		
		  <div id='progquestions'></div>
			 
	 </div></div> 
			
	</div> 
	<div class="col-xs-12 col-md-12"> 
	
	</div> 
	
</div>
<!-- menu74 -->

<!-- menu76-->
<div id="menu76" class="tab-pane maintab"> 
   <div class="col-xs-12 col-md-12"> 
	 <div class="panel panel-default  panel-success">
		<div class="panel-body"><p><strong> 3 Touch Program Tracking</strong></p>
		
		 <div id='progpartrptgrid'></div>
	</div>
	</div> 
 <div id='p_rel_track'></div>
	<div class="tl-box" >
		 <div class="pad10 text-center">
				<h4 class='white'>3 Touch Program Progress <span id="3tp_relprog"></span></h4>
		<hr/>	
		</div> 
			<div id='programtrack'> 
				<ul id="progrel-tl"></ul> 
			</div> 
 <div class="pad10 "> 
 </div>
 </div>  
</div> 
</div>
<!-- menu76 -->

<!-- menu83 -->
	<div id="menu83" class="tab-pane maintab  ">
		<div class="top-head">
			<div class="col-xs-12 col-sm-8">
				<h4>Employee Management</h4>
			</div> 
			</div>
			<div class="col-xs-12 col-md-12  ">
				<form class='form-inline form-gray'>
					<div class="form-group">
						<label>Search Members</label> 
						<input type="text" placeholder="Contact Name ..." class="form-control search-control" id="tbstaffname">
					</div> 
					<button type='button' class="btn btn-primary btnblock btn_searchstaff">Search</button> </form>
                    
			</div>
			
			<div class="col-md-10 margt3"> 
			
			<!-- Nav tabs -->
			<ul class="nav nav-tabs margt3" role="tablist">
				<li role="presentation" class="active"><a href="#m85_nonstaffs" aria-controls="homein" role="tab" data-toggle="tab"> Users</a></li>
				<li role="presentation" ><a href="#m85_staffs" aria-controls="conreqin" role="tab" data-toggle="tab"> Staffs</a></li>
			</ul>

			  <!-- Tab panes -->
			  <div class="tab-content">
				<div role="tabpanel" class="tab-pane active" id="m85_nonstaffs">
					<div id='allnonstafss'></div>
				</div>  
				<div role="tabpanel" class="tab-pane  " id="m85_staffs">
					<div id='allstafss'></div>
				</div>  
			  </div>
 
			 
		  
		</div>
		
     </div>
 <!--menu83 -->	
 
<?php  } ?>

 
<!-- menu75-->
<div id="menu75" class="tab-pane maintab"> 
   <div class="col-xs-12 col-md-12"> 
	 <div class="panel panel-default  panel-success"> 
	 <div class="panel-heading">
		<h4>3 Touch Program Relationship Management</h4>
	 </div>
		<div class="panel-body"> 
		<div class="col-xs-12 col-md-12"> 
		  <div id='pprelations'></div>
		</div>
		<div class="col-xs-12 col-md-12"> 
	<div class="globalsearch">
	<label for="em_client">Search Relationship:</label> 
	<div class="row">
	 <div class="col-xs-12 col-sm-5">
		<div class="form-group"> 
			<input type="text" class="form-control " id="tb_3trelation" placeholder="Relationship Name"> 
		</div> 
	</div>
	<div class="col-xs-12 col-sm-5">
		<div class="form-group"> 
			<select data-placeholder="Select Tags" id="3tsearchtag" class='chosen-select' multiple >
				<?php
				foreach ($alltags as $tag)
				{
					echo "<option value='" . $tag['tagname'] . "'>" . $tag['tagname'] . "</option>";
				}
				?>
			</select>
			<small class="pull-right">(Multiple tags can be selected)</small>
		</div> 
	</div>
	<div class="col-xs-12 col-sm-2"> 
		<div class="form-group">  
			<input type="button" class="btn btn-primary " id="btn_src3trelation" value="Search">
		</div> 
	</div>	
	 </div> 
	</div>
	</div>
	<div class="col-xs-12 col-sm-12"> 
		<div class='margt3' id='progparticipantgrid'></div> 
	</div> 
	</div>
</div>  
	<div id='programprogress'></div>  
		<div class="tl-box" >
			 <div class="pad10 text-center">
				<h4 class='white'>3 Touch Program Progress <span id="3tp_progress"></span></h4>
			<hr/>	
			</div> 
			<div id='programtrack'> 
				<ul id="program-tl"></ul> 
			</div> 
			<div class="pad10 ">
			 <hr>
			 <a href="javascript:void(0);" class="btn btn-primary btnadd3tq" id="add" >Add New Question</a> 
			 <a href="javascript:void(0);" class="btn btn-danger btndel3tq" id="add" >Remove Relation</a> 
			</div>
		</div>
	</div> 
</div>
<!-- menu75 -->

<!-- menu77-->
<div id="menu77" class="tab-pane maintab"> 
   <div class="col-xs-12 col-md-12">
		<div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
			  <div class="panel panel-default">
				<div class="panel-heading" role="tab" id="headingOne">
				  <h4 class="panel-title">
					<a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
					  Who can see your connections
					</a>
				  </h4>
				</div>
				<div id="collapseOne" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingOne">
				    <div class="panel-body">
					<p>Choose who can see your list of connections</p>
					
					<div class="col-xs-6"> 
						<div class='form-group'  > 
						<select class='form-control' id='whocanview' >
							<option value='0'>Only You</option>
							<option value='10'>Your connections</option> 
						</select>
						</div>
					</div>
					<div class="col-xs-6"> 
						<button class='btn btn-primary updateprivacy'> Save Task</button> 
					</div>
					
					</div>
				</div>
			  </div>
		</div>
		<div id='p_rel_track'></div>
	</div>
</div>
<!-- menu77 --> 
<!-- menu78-->
 <div id="menu78" class="tab-pane maintab"> 
   <div class="col-xs-12 col-md-12">
		 
			  <div class="panel panel-default">
				<div class="panel-heading">
					<h4> 3 Touch Program Activities Log</h4>
				</div>
				 
				    <div class="panel-body" style='height: 640px; overflow-y:scroll'>
					 <div id='3t_activities_log'></div>
					</div> 
			  </div> 
	</div>
</div>
<!-- menu78 -->
<!-- menu79-->
 <div id="menu79" class="tab-pane maintab"> 
   <div class="col-xs-12 col-md-12">
		<div class="panel panel-default">
			<div class="panel-heading">
				<h4>Invite Knows to MyCity</h4>
			</div>
			<div class="panel-body"> 
				<div class="row"> 
					<div class="col-xs-12 col-md-3">
						<input type="text"  placeholder="Specify Name" id='src_name' class="form-control src_name">
					</div>  
					 
					<div class="col-xs-12 col-md-5">
						<select data-placeholder="Select Vocations" id="src_vocation" class='chosen-select src_vocation'    >
							<?php
								foreach ($vocations as $vocation)
								{
									echo "<option value='" . $vocation['name'] . "'>" . $vocation['name'] . "</option>";
                                }
							?>
						</select><small class="pull-right">(Multiple vocations can be selected)</small>
					</div> 
					<div class="col-xs-12 col-md-4" style='padding-top: 10px;'>
					<button class="btn btn-primary btnblock src_all_knows">Search</button></div>
				</div>
				<div class="row marg4">  
				<div class="col-xs-12  ">
				<div id='allknowsgrid'></div>
				</div></div>
			</div> 
		</div> 
	</div>
</div>
<!-- menu79 --> 
<!-- menu65-->
<div id="menu65" class="tab-pane maintab"> 
   <div class="col-xs-12 col-md-6"> 
	 <div class="panel panel-default  panel-success">
		<div class="panel-body"><p><strong>Request to list New City</strong></p><hr>
	 
		 <div class="form-group">
			<input type="text" placeholder="City Name" class="form-control " id="tbnewcityname">
		 </div>
		 <button type='button' class="btn btn-primary btnblock btnrequestcity">Save</button> 
	 
	</div>
	</div> 	 
</div>
<div class="col-xs-12 col-md-6">
</div>
</div>
<!-- menu65 -->


<!-- menu49 -->
<div id="menu49" class="tab-pane maintab">
                        <div class="top-head">
                            <div class="col-xs-12 col-sm-8">
                                <h4><i class='fa fa-users'></i> Members You May Be Interested To Contact</h4>
                            </div> 
                            <div class="clearfix"></div>
                        </div>
                           
                        <div class="col-xs-12 col-md-12  ">
             
                        
        </div>
        </div>
    <!--menu49 --> 
<!-- menu53 -->
    <div id="menu53" class="tab-pane maintab">
                        <div class="top-head">
                            <div class="col-xs-12 col-sm-8">
                                <h4><i class='fa fa-users'></i> Businesses Search</h4>
                            </div> 
                            <div class="clearfix"></div>
                        </div>
                           
                        <div class="col-xs-6 col-md-6 chosen-select-lg ">
                <div class="panel panel-default panel-search">
            <div class="panel-heading text-left"><h2 class='htxt-md'>Search Rated Businesses / Individuals</h2></div>
            <div class="panel-body">
                 <div class="form-group">  
 
 <select data-placeholder="City" id="tbsearchbycity" name="tbsearchbycity"  class="form-control   "  > 
                            <option value=''>Select City</option> 
                            <?php
                            
                               echo $citynames;  
                            ?>
                        </select>
  </div>
  <div class="form-group">   
   <select data-placeholder='Vocations ...' class="form-control " name="tbsearchbyvoc" id="tbsearchbyvoc"  > 
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
  
             </div>
						 
            </div>
            <!--menu53 --> 

			
			<!-- menu54 -->
		<div id="menu54" class="tab-pane maintab">
			<div class="top-head">
                            <div class="col-xs-12 col-sm-8">
                                <h4><i class='fa fa-users'></i> Business Search Log</h4>
                            </div> 
                            <div class="clearfix"></div>
                        </div>
                           
                        <div class="col-xs-12 col-md-12  ">
             
			<div id="businesssearchlog"></div>     
		</div>
						 
            </div>
            <!--menu54 --> 
			
		 <!-- menu55 -->
		<div id="menu55" class="tab-pane maintab">
			<div class="top-head">
				<div class="col-xs-12 col-sm-8">
                                <h4><i class='fa fa-users'></i> Zip Code Distances</h4>
                            </div> 
                            <div class="clearfix"></div>
                        </div>
                           
                        <div class="col-xs-12 col-md-12  ">
             
			<div id="distancegrid"></div>     
		</div>
						 
            </div>
            <!--menu55 --> 
			<!-- menu56 -->
		<div id="menu56" class="tab-pane maintab">
			<div class="top-head">
				<div class="col-xs-12 col-sm-8">
					<h4><i class='fa fa-users'></i> Upload Business Card</h4>
				</div>
				<div class="clearfix"></div>
				</div>      
                        <div class="col-xs-12 col-md-12  ">
						 <form action="includes/uploader-3.php"
							  class="dropzone"
							  id="busincard">
						 </form>
						 <div class='form-group pad10 text-center'>
							 <button class='btn btn-primary btn-lg generatevcard'>Generate VCard</button> 
						 </div> 
			 <div class='vcarddet'></div> 
		</div>
						 
            </div>
            <!--menu56 --> 
			
			
		<!-- menu57 -->
		<div id="menu57" class="tab-pane maintab">
			<div class="top-head">
				<div class="col-xs-12 col-md-12">
					<h4><i class='fa fa-envelope'></i> Compose Email for member</h4>
				</div>
			</div> 
			 
			<div class="col-xs-12 col-md-12  ">
				<label class="custom-label">To:</label>
				<input type="text" class="form-control compose_name" id='compose_name' name="compose_name" required="">
				<input type="hidden" class="form-control compose_cmail" id='compose_cmail' name="compose_cmail" required="">
			</div>
			<div class="col-xs-12 col-md-12  ">
				<label class="custom-label">CC: (Adding CC is optional)</label>
				<input type="text" class="form-control compose_cc" id="compose_cc" name="compose_cc" required="">
			</div>
			
			<div class="col-xs-12 col-md-12  ">
				<label class="custom-label">Subject:</label>
				<input type="text" class="form-control compose_subject" id="compose_subject" name="compose_subject" required="">
			</div> 
			<div class="col-xs-12 col-md-12  "> 
				<label class="custom-label">Email Body:</label>
				<textarea name='emailbody' class="form-control emailbody"  id='emailbody' rows='5'></textarea>
			</div> 
			<div class="col-xs-12 col-md-12 pad10"> 
			
				<button type="button" class="btn btn-primary btn-lg" id="btnsendemailtomember" >Sent</button>
				
				<a  data-toggle="tab" href="#menu2" class="btn btn-link btn-lg"   >Cancel</a>
				
			</div> 
			
        </div>
		<!--menu57 --> 
			 
 <!-- menu58 -->
	<div id="menu58" class="tab-pane maintab">
		  
				<div class="col-xs-12 col-md-8">
				 <div class='marg4'>

			  <!-- Nav tabs -->
			  <ul class="nav nav-tabs" role="tablist">
				<li role="presentation" class="active"><a href="#membergrid" aria-controls="homein" role="tab" data-toggle="tab"> Members</a></li>
				<li role="presentation" ><a href="#knowgrid" aria-controls="conreqin" role="tab" data-toggle="tab"> Their Knows</a></li>
				 </ul>

			  <!-- Tab panes -->
			  <div class="tab-content">
				<div role="tabpanel" class="tab-pane active" id="membergrid">
					<div class="memberlist"></div>
				</div> 
				
				<div role="tabpanel" class="tab-pane  " id="knowgrid">
					<div class="knowlist"></div>
				</div> 
				
			  </div>

			</div>
				</div>
				
				<div class="col-xs-12 col-md-4 hidden-xs">
	     <div class="globalsearch">
			<form class='  form-gray-wide '  >
			<div class='row'>
			<div class="col-xs-12 col-sm-12">
			   <h4>Search Members </h4>
            </div> 
            <div class="col-xs-12 col-sm-12">
               <label>Name:</label> 
                 <input type="text"  placeholder="Specify Name" id='dmname2' class="form-control dmname">
            </div>
			<div class="col-xs-12 col-sm-12">
				<label>City:</label> 
                  <select data-placeholder="Specify Cities"  id="dmcity2" class='chosen-select dmcity' multiple > 
                     <?php 
                        foreach ($cities as $city)
                        {
							echo "<option value='" . $city['name'] . "'>" . $city['name'] . "</option>";
                        }
                     ?>
                  </select> 
		    </div>
			<div class="col-xs-12 col-sm-12">
				<div class="form-group">
					<label>Vocation:</label> 
					<select data-placeholder='Choose vocations ...' multiple class="form-control chosen-select dmvocations2" name="dmvocations2[]" id="dmvocations2"  > 
						  <?php
							   foreach ($vocations as $vocation) {
								  echo "<option value='" . $vocation['name'] . "'>" . $vocation['name'] . "</option>";
							   }
						   ?>
					 </select>
                </div> 
            </div>  
			<div class="col-xs-12 col-sm-12">
				<button type='button' class="btn btn-primary btnblock btntopgap btnsearchdmmembers2">Search</button>   
			</div>  
            
			</div> 
           </form>
		    </div>
		  </div>
		  
				      
	  </div>
 <!--menu58 --> 
 <!-- menu59-->
	<div id="menu59" class="tab-pane maintab">
		<div class="col-xs-12 col-md-12">  
			<!-- Nav tabs -->
				<ul class="nav nav-tabs" role="tablist">
					<li role="presentation" class="active"><a href="#conreqsent"  data-mf="0" class="btn-mailfilter getconnectionrequest "  aria-controls="home" role="tab" data-toggle="tab">Request Sent</a></li>
					<li role="presentation"><a href="#conreqreceived"  data-mf="1" class="btn-mailfilter  getconnectioninrequest" aria-controls="profile" role="tab" data-toggle="tab">Request Received</a></li>
					</ul> 
				  <!-- Tab panes -->
				  <div class="tab-content">
					<div role="tabpanel" class="tab-pane active tabcontent" id="conreqsent">
					<h3>Connection Request Sent</h3>
					<div id="conreqlist1"></div>
					</div>
					<div role="tabpanel" class="tab-pane tabcontent" id="conreqreceived">
					<h3>Connection Request Received</h3>
					<div id="conreqlist0"></div>  
					</div> 
		    </div>  
		</div>		      
	  </div>
 <!--menu59 --> 
 
   <!-- menu60-->
	<div id="menu60" class="tab-pane maintab">
		   <div class="col-xs-12 col-sm-8">
                                <h4>Top Rated Knows</h4>
                            </div>
				<div class="col-xs-12 col-md-12">
				
				<div class="globalsearch"> 
					<div class="row">
					 <div class="col-xs-12 col-md-2 pad10">
							<strong>Search by email:</strong></div>
						 <div class="col-xs-12 col-md-4">
							<input type="text" placeholder="Search by email" class="form-control" id="memberemail">
						</div>
						<div class="col-xs-12 col-md-4">
							<button   class="btn btn-primary searchmemberbyemail"><i class='fa fa-search'></i> Search</button> 
						</div> 
					 
					 </div>
					 </div>
				
				<div id="topratedknows" class='marg4'></div>
				</div>
				      
	</div>
  <!-- menu60 --> 
  <!-- menu61-->
	<div id="menu61" class="tab-pane maintab">
		<div class="col-xs-12 col-md-12">
			<h4>Manage Fuzzy Search Keywords</h4>
		</div>
		
		<div class="col-xs-12 col-md-4">
			<input type="text" placeholder="Search text" class="form-control" id="fuzkeyword">
        </div>                       
        <div class="col-xs-12 col-md-4">
			<input type="text" placeholder="Text to map" class="form-control" id="fuzmaptext">
        </div>
		<div class="col-xs-12 col-md-4">
			<button data-i='0'  class="btn btn-primary addfuzzysearchkeyword">ADD NEW</button>
			<button class="btn btn-danger cancelfuzzykeyupdate">CANCEL</button>
		</div>   
 
		<div class="col-xs-12 col-md-12">
			<div id='fuzzykeyworlist' class='marg4'></div>
		</div>
</div>
 <!--menu61 --> 
  <div id="menu63" class="tab-pane">
	<div id='contentblock'></div>
 </div>
			<!-- menu24 -->
			<div id="menu24" class="tab-pane maintab">
                <div class="top-head">
                            <div class="col-xs-12 col-sm-8">
                                <h4>Mass Upload New Knows</h4>
                            </div>
                            <!--<div class="col-xs-12 col-sm-4 text-center">
                                <button class="btnblock">SEARCH PAGE</button>
                            </div>-->
                            <div class="clearfix"></div>
                        </div>
                        <div class="col-xs-12 col-md-12  "> 
							 <form action="includes/uploader.php"
							  class="dropzone"
							  id="my-knows"></form>
							  <div class='form-group pad10 text-center'>
							 <button class='btn btn-primary btn-lg importknows'>Start Import</button> 
							  </div>
                        </div>
                    </div>
			  <!--menu24 -->
			  <!-- menu25 -->
				<div id="menu25" class="tab-pane maintab">
                    <div class="top-head">
                    	<div class="col-xs-12 col-sm-8">
                            <h4>Manage Newly Imported Knows</h4>
                        </div>
                        <!--<div class="col-xs-12 col-sm-4 text-center">
                        	<button class="btnblock">SEARCH PAGE</button>
                         </div>
                        -->
                        <div class="clearfix"></div>
                        </div>
                        <div class="col-xs-12 col-md-12  "> 
						  <div class="manageimportedlist"></div> 
                        </div>
                    </div>
				<!--menu25 --> 
				<!-- menu14 -->
				<div id="menu14" class="tab-pane maintab">
                        <div class="top-head">
                            <div class="col-xs-12 col-sm-8">
                                <h4>Manage Client / User Group Status</h4>
                            </div> 
                            <div class="clearfix"></div>
                        </div>
                        <div class="col-xs-12 col-md-12  ">
							<div class="table-responsive" id='newuserlist'>
							</div>
                        </div>
                    </div>
				<!--menu14 --> 
				<!-- menu15 -->
				<div id="menu15" class="tab-pane maintab">
                        <div class="top-head">
                            <div class="col-xs-12 col-sm-12">
                                <h4>Blogs</h4>
                            </div>
                           
                            <div class="clearfix"></div>
                        </div>
                        <div class="col-xs-12 col-md-12  ">
							<div id='bloglist'></div>
                        </div>
                    </div>
				<!-- menu15 --> 
				<!-- viewpost -->
				<div id="viewpost" class="tab-pane maintab">
                        <div class="top-head">
                            <div class="col-xs-12 col-sm-12">
                                <h4 id='blogheading'></h4>
                            </div> 
                            <div class="clearfix"></div>
                        </div>
                        <div class="col-xs-12 col-md-12  ">
							<div id='blogcontent'></div> 
                        </div>
						
						 <div class="col-xs-12 col-md-12  " id='comment'> <hr/>
							<h3>Leave a Reply</h3>
							<p>Your email address will not be published. Required fields are marked *</p>
							<form id='commentform'> 
								<div class="form-group">
								<label for="name">Name: *</label>
								<input type="text" required class="form-control" name="name" id="name" placeholder="Your Name">
								</div>
							  
								<div class="form-group">
									<label for="email">Email: *</label>
									<input type="email" required class="form-control" name="email" id="email" placeholder="Your Email">
								</div>

								<div class="form-group">
									<label for="comment">Comment: *</label>
									<textarea required class="form-control" name="commentbody" id="commentbody" placeholder='Comment' ></textarea>
								</div>
							 
								<div class='row'> 
									<div class="col-xs-12 col-md-2" >
									<input type="hidden"  id="postid" name="postid"/>
									<button type="button" id='postcomment' class="flatbutton">Post Comment</button>
								</div> 
							</div>
						</form>
                      </div>
                </div>
				<!-- viewpost --> 
				<!--Menu10-->
				<div id="menu10" class="tab-pane fade maintab">
					 <div class='row'>
						<div class="col-xs-12 col-sm-12">
							<h4>Help Instruction</h4>
						</div> 
					 
					<div class="col-xs-12 col-md-10"> 
					 <div id="helpaccordion"></div>
					</div>

					</div>
				</div>
				
				<!--Menu10-->  
				<!--Menu7-->
				<div id="menu7" class="tab-pane fade maintab">
                             <div class="top-head">
                                <div class="col-xs-12 col-sm-8">
                                    <h4>Feedback</h4>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                            <div class="col-xs-12 col-sm-8 col-sm-offset-2 no-padd">
                                <div class="search-loc" style="background: none;border: 1px solid #dbdbdb">
                                    <div class="feedbackData">
									<div class="col-xs-12 no-padd">
                <div class="col-sm-6 col-xs-12 padd-8">
					<label>Full Name:</label>
                    <input placeholder="Enter full name..." class="form-control feedback_name" data-id="cue-1" type="text">
                </div>
                <div class="col-sm-6 col-xs-12 padd-8">
				<label>E-Mail:</label>
                    <input placeholder="Enter email..." class="form-control feedback_email" data-id="cue-1" type="text">
                </div>
				 <div class="col-sm-12 col-xs-12 padd-8">
				 <label>Comment:</label>
                    <textarea class="form-control feedback_comment" placeholder="Enter Comment..."></textarea>
                </div>
				</div></div>
				<div class="col-sm-12 col-xs-12 padd-8 text-center">
                                        <div class="col-xs-6 padd-3 text-left">
                                            <button class="btn btn-primary  btnblock send_feedback">Submit</button>
                                        </div>
                                        <div class="col-xs-6 padd-3 text-right"> 
                                        </div>
                                    </div>
                                    <div class="clearfix"></div>
                                </div>
                         </div>
					</div>
				    <!--Menu7-->
                    <div id="menu1" class="tab-pane active maintab">
					  <div class='grid-row no-padd'>
                        <div class="top-head">
                            <div class="col-xs-12 col-sm-8">
                                <h4>Client / User Details <a href="<?php echo $help_data_buttons[0]['helpvideo']; ?>" target="_blank" ><i id='hint-profile1' class='fa fa-arrow-right' ></i><span style="color:red;">Help</span></a></h4>
                            </div> 
                            <div class="clearfix"></div>
                    </div>
			<div class="col-md-6">
			    <div class="panel panel-default panelhome">
                <div class="panel-heading">
                    <h4>Profile</h4>
                </div>
            <div class="panel-body">
                    <a href="#" data-toggle='modal' data-target='#changeAccSett' class="changeAccSett btn-act" 
                    data-id="<?php echo $user_id ?>" title='Click to edit profile'>
                        <i class="fa fa-pencil"></i> </a>
						<div class="row">
						<div class="col-md-4">
							 <img src="<?php echo $siteurl . $user_picutre;?>" alt="" class="img-rounded"   height="120" width="120" />
							 <br/><a href="#" data-toggle='modal' data-target='#changepicture' class="btn-primary btn btn-xs changepic_btn marg1" data-id="<?php echo $user_id ?>">
							 <i class="fa fa-pencil"></i> Update Picture
							 </a>
						</div>
						<div class="col-md-6">
						<?php 
						$myrefcount = 
						$link->query('select count(*) as totalreferrals from referralsuggestions where knowenteredby=\'' . $user_id . '\''  );
						if($myrefcount->num_rows > 0 )
						{
							$mytotalref = $myrefcount->fetch_array()['totalreferrals'];
						} 
						?>
						<p id='profilep'>
                        <?php
                         echo "<strong>" . $my_profile[0]['username'] . "<br/>" . $my_profile[0]['user_email'] . 
                         "<br/>Phone: <a href='tel:" . $my_profile[0]['user_phone'] ."'>" . $my_profile[0]['user_phone']. "</a>" .
                         "<br/>Package Name:" .$my_profile[0]['user_pkg'] . "</strong>";
                         ?></p> 			
						</div>
                        <div class="col-md-12">
                        <?php 
                            if( $profileisvisible == '1' )
                            {
								?> 
                                <p><br/><strong>Your Publicly Visible Profile:</strong></p>
								<a href="<?php echo  $publicprofile; ?>"><?php echo  $publicprofile; ?></a>
                                 <!--input class='form-control' type='text' disabled 
                            value='<?php echo $publicprofile ?>'/-->   
							<br/>
							<button  class='btn btn-primary btnmakeprofilepublic'>Update Profile Link</button>							
                            <?php
                            }
                            else 
                            {
								$username_parts  =  explode(' ', $username);
								$combined_name  = implode('',  $username_parts );
								$combined_name = strtolower($combined_name);
								
								
							?>
                            <p><br/><strong>Your Public Profile:</strong></p>
							<a href="https://mycity.com/profile/?n=<?php echo $combined_name;?>&c=<?php echo  $user_id; ?>">https://mycity.com/profile/?n=<?php echo $combined_name;?>&c=<?php echo  $user_id; ?></a>
							<br>
							<button  class='btn btn-primary btnmakeprofilepublic'>Make My Profile Public</button>
							<?php
							}
                            ?>
</div> 	
					</div>
			 </div>
         </div>
         <?php if(  $my_profile[0]['user_type'] == 1 ) :?>
         <div class="panel panel-default panelhome-sm">
            <div class="panel-heading">
                <h4>Business Information</h4>
            </div>
            <div class="panel-body" id="groupnames">
            <p id='profilep'>
                        <?php
                         echo "<strong>" . $my_profile[0]['busi_name']  ."</strong>" .
                         "<br/><strong>Business Type: </strong>" . $my_profile[0]['busi_type']. 
                         "<br/><strong>Location: </strong>" .$my_profile[0]['busi_location'] . 
                         "<br/><strong>Business Hours: </strong>" .$my_profile[0]['busi_hours'] ;
                         ?></p> 
             </div>
         </div> 
        <?php endif; ?>
         <div class="panel panel-default panelhome-sm">
            <div class="panel-heading">
                <h4>My Cities</h4>
            </div>
             
			<div class="panel-body" id="groupnames" style='height: 340px; overflow-y:scroll'>
              
			 <?php 
			 $mygroups = explode(',', $my_profile[0]['group_names']);
			 foreach($mygroups as $gitem)
			 {
				echo "<span class='grpitem'>" . $gitem . "</span>"; 
			 } 
               //echo str_replace( ",", ", ",  $my_profile[0]['group_names']) ;
             ?> 
             </div>
         </div>
         

	 </div> 	 
 <div class="col-md-6">
 <?php  
 if ($_user_role != 'admin') :
?>

<div class="panel panel-default panelhome">
		 
			 <div class="panel-body  ">
             <h3 class='text-center txt-head-md'>Get Referrals From Your LinkedIn Connections</h3>
             
            <p class='text-center'> <i class='fa fa-linkedin fa-5x blue-o'></i>
             </p>
             <p>
             <strong>Step 1:</strong> Go to <a target='_blank' class='blue' href='https://www.linkedin.com/psettings/member-data'>https://www.linkedin.com/psettings/member-data</a> <br/>
             <strong>Step 2:</strong> Click <strong class='blue'>Request Archive</strong><br/>
             <strong>Step 3:</strong> Email the .zip file or Connections.csv to support@edgeupnetwork.com. 

</p>  
	    </div>
    </div>


 	<div class="panel panel-default panelhome-sm">
		<div class="panel-heading">
		 <h4>My Preferences</h4>
		</div>
			  <div class="panel-body pscroll"  style='height: 240px; overflow-y:scroll'>
             <div id='memberdetails'>
             <?php 
             
             echo "<p><strong>Target Clients:</strong> <br/>" ; 
			 $targetclients = explode(',', $my_profile[0]['target_clients']);
			 foreach($targetclients as $tcitem)
			 {
				echo "<span class='grpitem'>" . $tcitem . "</span>"; 
			 } 
			 echo "</p>"; 
			 echo "<p><strong>Target Referral Partners:</strong><br/>";
			 
			 $targetreferralpartners = explode(',', $my_profile[0]['target_referral_partners']);
			 foreach($targetreferralpartners as $trpitem)
			 {
				echo "<span class='grpitem'>" . $trpitem . "</span>"; 
			 } 
             echo "</p>"; 
			 echo  "<p><strong>Vocation:</strong><br/>";
			 $myvocations = explode(',', $my_profile[0]['vocations']);
			 foreach($myvocations as $vitem)
			 {
				echo "<span class='grpitem'>" . $vitem . "</span>"; 
			 } 
             echo "</p>"; 

            ?></div> 
	    </div>
    </div>
	 <?php  
	 if ( $isemployee == '1') 
	 {
		
		$mytasks = $link->query("select  * from mc_employee_task  where user_id='$user_id' order by assignedon asc ");
		if($mytasks->num_rows > 0)
		{
	?> 
	<div class="panel panel-default panelhome-sm">
		<div class="panel-heading">
		 <h4>My Tasks</h4>
		</div>
			  <div class="panel-body pscroll"  style='height: 250px; overflow-y:scroll'>
             <div id='memberdetails'>
             <?php
			 
				echo "<ul>"; 
				while($row = $mytasks->fetch_array() )
				{
					if($row['task_desc'] !='')
						echo "<li >  " . $row['task_desc'] . "</li>";  
				} 
				echo "</ul>"; 
			  
			 ?>
			</div> 
	    </div>
    </div>
	<?php 
		
		} 
	}
	
	?> 
<?php  
else:
?>

<div class="panel panel-default  ">
	<div class="panel-heading">
		<div class='pull-left'>
		<h4>Front Page Statement</h4>
		</div>
		<div class='pull-right'>
			<button class='btn btn-primary btnshownoteedit' title='Click to add new statement' ><i class='fa fa-plus'></i></button>
		</div> 
		<div class='clearfix'></div>
		
		</div>
	    <div class="panel-body text-left pscroll panelnote">
			<?php
				  
				if( sizeof($mynotes['results']) > 0 ): 
					$row = $mynotes['results'][0];
					$notetext = $row['note'];
						echo "<div class='inforow text-left'>";
						echo  "<div id='fp_note'>".$row['note'] . "</div><br/><small> Note entered on: ";
						echo  $row['enteredon'] ."</small></p>";
						echo "</div> ";  
					else:
					$notetext='';
					echo "<p class='infoalert pad10 text-center'>You haven't created any note!</p>";
				endif;
					 
			?>
	    </div>
    </div>
	<div class="modal modalbl fade" tabindex="-1" role="dialog" aria-labelledby="mystatement" id="mystatement">
    <div class="modal-dialog ">
        <div class="modal-content">
            <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title">Instant Note</h4>
            </div>
				<div class="modal-body text-left"  >
					 <div class="form-group">
						 <label for="exampleInputEmail1">Compose Note:</label>
						 <textarea class="form-control" id='instantnote' rows="4"><?php echo $notetext;?></textarea> 
					 </div>
                </div>
				 <div class="modal-footer" >
				 <button type="button" id='btnsavenote' class="btn btn-primary">Save</button> 
				 </div>
            </div>
    </div>
</div> 

<div class="panel panel-default panelhome">
	 <div class="panel-heading">
		 <h4>Contacts added during last 2 weeks</h4>
	</div>
   <div class="panel-body " >
	<?php 
	   	$total_invites = getwhoinvitedwhom();
		if (sizeof($total_invites) > 0)
		{
			echo "<table class='table2'>";
			echo "<tr><th>Member/Partner</th><th>Total Knows Added</th></tr>";
			for ($i=0; $i < 5 && $i < sizeof($total_invites) ; $i++) 
			{
				echo "<tr><td><a data-key='' data-pg='1' data-mid='" . $total_invites[$i]['id']  . "' href='#menu41' class='btnshownewcontacts' title='Click to view the contacts' data-toggle='tab'>" . $total_invites[$i]['member'] . 
					"</a></td><td class='text-center'><a data-key='' data-pg='1' data-mid='" . $total_invites[$i]['id']  . "' href='#menu41' class='btnshownewcontacts' title='Click to view the contacts' data-toggle='tab'><span class='badge badge-blue'>" . $total_invites[$i]['invite_count'] . "</span></a></td></tr>";
			}
			echo "</table>"; 
		}
	?>
   </div>
 </div>	 


 <div class="panel panel-default panelhome">
	 <div class="panel-heading">
		 <h4>Recent Lifestyle Updated Knows</h4>
	</div>
   <div class="panel-body "  style='height: 240px; overflow-y:scroll'>
	<?php 
	      
		if ( sizeof($recentupdates['results'] )  > 0)
		{
			echo "<table class='table table-colored'>";
			echo "<tr><th>Member/Partner</th><th>Lifestyle Updated</th><th></th></tr>";
			for ($i=0;   $i <  sizeof($recentupdates['results'] ) ; $i++) 
			{
				echo "<tr><td>"  . $recentupdates['results'][$i]['client_name']  . 
					"</td><td > ". $recentupdates['results'][$i]['client_lifestyle']  . 
					"</td><td><button class='btn btn-xs btn-primary btnviewknowprofile' data-i='"  . $recentupdates['results'][$i]['know_id']  .  "'>View</button>" . 
					"</td></tr>";
			}
			echo "</table>"; 
		}
		else
		{
			echo "<p class='alertinfofix'>No recent know update found!</p>";
		}
	?>
   </div>
 </div>	
 
<?php 		 
endif;
?>
</div>
</div>

</div>
		
<div id="menu2" class="tab-pane fade maintab">
			<div class="top-head">
					<div class="col-xs-12 col-sm-8">
						<h4>ENTER PEOPLE YOU KNOW DETAILS 
								<a href="<?php echo $help_data_buttons[7]['helpvideo']; ?>" target="_blank" ><i class='fa fa-arrow-right' ></i><span style="color:red;"> Help</span></a>
								</h4>
                            </div>
                            <div class="clearfix"></div>
                        </div>
						
						<div class='row'>
                        <div class="col-xs-12 col-sm-6 padd-5"> 
                             <label class="custom-label">Name:</label> 
							 <input type="text" class="form-control client_name" name="e_name" required="">
							 
                        </div>
                        <div class="col-xs-12 col-sm-6 padd-5">
                            <label class="custom-label">Vocation(s):</label> 
                            <select data-placeholder='Choose vocations ...' multiple class="form-control client_pro" name="e_profession[]" id="e_prof"  > 
                                    <?php
                                    
                                    foreach ($vocations as $vocation) {
                                        echo "<option value='" . $vocation['name'] . "'>" . $vocation['name'] . "</option>";
                                    }
                                    ?>
                                </select>
                                <small class="pull-right">(Enter comma seperated)</small>
                            
                        </div>
                        </div>
						<div class='row'> 
                        <div class="col-xs-12 col-sm-6 padd-5">
							<label class="custom-label">Phone:</label> 
                            <input type="text" class="form-control client_ph" name="e_phone" required="">
                           
                        </div> 
                        <div class="col-xs-12 col-sm-6 form-group">
                            <label class="custom-label">Email:</label> 
                            <input type="text" class="form-control client_email newcontactemail" name="e_email" required="">
                            
                        </div> 
						</div>
						<div class='row'> 
							<div class="col-xs-12 col-sm-6 padd-5">
							 <label class="custom-label">Lifestyle: </label> 
                             <select data-placeholder='Specify lifestyles ...'  multiple  name="e_lifestyle" class="form-control chosen-select  client_lifestyle" id="">
                                <?php
                                foreach ($lifestyles as $lifestyle) { 
                                    echo "<option value='" . $lifestyle['name'] . "'>" . $lifestyle['name'] . "</option>";
                                }
                                    ?>
							 </select>								
                  
							 <label class="custom-label">Lifestyle: <strong >( In case your city is not listed, request to list it <a data-toggle='tab' data-dismiss='modal' aria-label='Close' href='#menu65'>here</a>.)</strong>
                    </label> 
							<select data-placeholder='Specify Cities' multiple  name="e_location"  class='form-control chosen-select client_location' > 
								 
								<?php   
								 echo $citynames;
							   ?>
						   </select> 
							 <small class="pull-right">(Enter comma separated)</small>
                      
						<label class="custom-label">Zip:<br></label> 
                        <input type="text" name="e_zip" class="form-control client_zip" id=""> 
								
						<label class="custom-label">Note(s):</label>
						<input type="text" class="form-control client_note" name="e_note" required="">
						<small class="pull-right">(Enter comma separated)</small>
						

						<div class=" <?php echo $hideClass; ?> ">
							<label class="custom-label">Groups:<br></label>
						</div>
						<div class=" <?php echo $hideClass; ?> ">
							<select class="form-control user_grp">
								<?php
									foreach ($getGroups as $item)
									{
										$sel = $userGrp == $item['id'] ? "selected='selected' " : "";
                                        if ($_user_role == 'admin')
										{
                                            $dis = "";
                                        }
										else
										{
                                            //$dis = $userGrp == $item['id'] ? "" : "disabled='disabled' ";
                                        }
                                        echo "<option value='" . $item['id'] . "' " . $sel . $dis . ">" . $item['name'] . "</option>";
                                    }
                                    ?>
							</select>
                        </div>
						 
					  </div>
							 
				 
					<?php
                        //if ($_user_role == 'admin' OR $user_pkg != 'free') {
                       
                        $i = 1;
						$textquestion =''; 
						$ratequestions ='';
						$vocaquestions ='';
						
                        foreach ($ques_data as $item) {
                            $name = "rating0" . $i;
                            $q_id = $item['id'];
                            $question = $item['question'];
                            $q_type = $item['question_type'];
							
							if($q_type == "rating"):
							
								$ratequestions .= " <div class='col-xs-12 col-sm-12 '>
									<label class='custom-label'>$question</label> 
									<div class='col-sm-6 col-xs-12 form-group'>
										<span class='starRating main user_ques_main' data-ques='$q_id'>";
								$ratequestions .= "<input id='rating01$i' type='radio' class='user_ques' name='$name' value='5' checked><label for='rating01$i'><span></span></label><label for='rating01$i'>5</label>
											<input id='rating02$i' type='radio' class='user_ques' name='$name' value='4'><label for='rating02$i'><span></span></label>
											<label for='rating02$i'>4</label>
											<input id='rating03$i' type='radio'  class='user_ques' name='$name' value='3'><label for='rating03$i'><span></span></label>
											<label for='rating03$i'>3</label>
											<input id='rating04$i' type='radio'  class='user_ques' name='$name' value='2'><label for='rating04$i'><span></span></label>
											<label for='rating04$i'>2</label>
											<input id='rating05$i' type='radio' class='user_ques' name='$name' value='1'><label for='rating05$i'><span></span></label>
											<label for='rating05$i'>1</label>"; 
							    $ratequestions .= "</span> </div> </div> ";
							
							else: 
								
							$vocaquestions .= "<div class='row'>
								 <div class=col-sm-12 col-xs-12>
									<label class='custom-label'>$question</label>  ";
								 
							$vocaquestions .= 
								"<select id='answer$q_id' data-ques='$q_id'  name='$name' 
								data-placeholder='Choose vocations ...' class='user_ques_text_add user_target_voc' multiple  > ";
								  
						    $vocaquestions .= "</select> </div></div>";
							endif;
                            $i++;
                        }
                       
                        //}
						
					echo '<div class="col-xs-12 col-sm-6 ">'; 	
					echo $ratequestions;
					echo "</div></div>"; 
                    echo $vocaquestions;    
						?> 
				  
				 <div class='row'>
					<div class="col-xs-12 col-sm-12 padd-5">
                        <label class="custom-label">Tags:</label> 
                        <select data-placeholder='Specify Tags ...'  multiple  name="knowtags"  class="form-control chosen-select  client_tags" id=""> 
                            <?php
                                foreach ($alltags as $tag)
                                {
                                    echo "<option value='" . $tag['tagname'] . "'>" . $tag['tagname'] . "</option>";
                                }
                            ?>
                            </select> 
                        
                    </div>  
				<div class="col-sm-12 pad10">
					<input type="button" value="Submit" class="btn btn-primary btnblock pull-right addnewknow">   
				</div>
</div>
						 
				<?php // people you know details ?>
				
				 
				<div class='globalsearch'>
				 
				<div class="row">
					 <div class="col-xs-12 col-md-12">
					<?php
					if ($_user_role == 'admin')
					{
						echo  '<h3>Search Registered Member</h3>';
					}
					else
					{
						echo  '<h3>Search your existing contacts</h3>';
					}
					?> <hr/>
					</div>
				</div>
				<div class="row">
					<div class="col-xs-12 col-md-3">
						<input type="text"  placeholder="Specify Name" class="form-control srchRefName">
					</div>
					<div class="col-xs-12 col-md-3">
						<input type="text"  placeholder="Entry Date" class="form-control srchentryDate">
					</div>
					<div class="col-xs-12 col-md-3">
						<input type="text"  placeholder="Email" class="form-control srchemail">
					</div>
					
					<div class="col-xs-12 col-md-3">
						<input type="text"  placeholder="Phone number" class="form-control srchPhone">
					</div> 
				</div> 
				<div class="row">
				
				<div class="col-xs-12 col-md-3 padt10">
					<select data-placeholder="Specify Cities"  id="filtercity" class='chosen-select' multiple > 
						<?php
							echo $citynames;
						?>
				</select>  
				</div> 
					<div class="col-xs-12 col-md-3 padt10">
						<input type="text"  placeholder="Specify Zip Code" class="form-control srchZipCode">
					</div>
					
					<div class="col-xs-12 col-md-6 padt10">
						<select data-placeholder="Select Tags" id="filterTags" class='chosen-select srchTags' multiple >
							<?php
								foreach ($alltags as $tag)
								{
									echo "<option value='" . $tag['tagname'] . "'>" . $tag['tagname'] . "</option>";
                                }
							?>
						</select>
						<small class="pull-right">(Multiple tags can be selected)</small>
					</div> 

					
					</div> 
					<div class="row  "> 
					<div class="col-xs-12 col-md-5"> 
						<select data-placeholder="Select Lifestyles" id="filterLifestyle" class='chosen-select user_ques_text_add' multiple >
							 <?php
								foreach ($lifestyles as $lifestyle)
								{
									echo "<option value='" . $lifestyle['name'] . "'>" . $lifestyle['name'] . "</option>";
                                }
							?>
						</select>
						<small class="pull-right">(Multiple lifestyle can be selected)</small>
					</div>
					
					<div class="col-xs-12 col-md-5">
                    <select data-placeholder="Select Vocations" id="locateVoc" class='chosen-select user_ques_text_add' multiple  >
						 
							<?php
								foreach ($vocations as $vocation)
								{
									echo "<option value='" . $vocation['name'] . "'>" . $vocation['name'] . "</option>";
                                }
							?>
						</select><small class="pull-right">(Multiple vocations can be selected)</small>
					</div>
					<div class="col-xs-12 col-md-2 " style='padding-top: 10px;'><button class="btn btn-primary btnblock srchRef">Search</button></div>
				</div>	 

			</div>
				
				<div class="col-xs-12 people-know" id='myknows'>
					<div class="col-xs-12">
					<hr/>
						<h5><?php echo $text ?></h5>
						</div>
						<div class="col-xs-12"  >
							<table class="table table-responsive">
								<thead>
                                    <tr>
                                        <th>Reference Name</th>
                                        <th>Vocation</th>
                                        <th>Phone</th>
                                        <th>Email</th>
                                        <th>Location</th>
                                        <th>Group</th>
                                        <?php if ($_user_role == 'admin') { ?>
                                            <th>Package</th>
                                            <th>References</th>
                                            <th>Joined On</th>
                                        <?php } else { ?>
                                            <th>Ratings</th>
                                        <?php } ?>
                                        <th>Action  
			   <a href="<?php echo $help_data_buttons[8]['helpvideo']; ?>" target="_blank" ><i class='fa fa-arrow-right' ></i><span style="color:red;"> Help</span></a>
				</th>
                                    </tr>
                                    </thead>
                                    <tbody class="clientsUsers"></tbody>
         </table> 
         <div class="modal fade mine-modal" id="modaltriggermailselect" tabindex="-1" role="dialog" aria-labelledby="triggermailselect">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                <h4 class="modal-title" id="suggestedref">Select A Trigger Mail</h4>
                            </div>
                            <div class="modal-body text-left" id='triggermailselect' style='height: 450px; overflow-y: scroll'>
                         <?php 
                            $rowindex=1;
                            echo '<div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">'; 
                            $counter=1 ; 
                            if( sizeof($mailtemplates)  > 0)
                            {
                                echo '<h4 class="text-center">Below are the available trigger mails. Select the one email</h4>';
                                foreach ($mailtemplates as $item )
                                {
                                   if( strcasecmp($item['mailtype'] , 'Introduction Mail' ) != 0 )
                                   {
                                          echo '<div class="panel panel-default">
                                        <div class="panel-heading" role="tab" id="heading' . $counter .'">
                                        <h4 class="panel-title">
                                            <a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapse' . $counter .'" aria-expanded="true" aria-controls="collapse' . $counter .'">
                                                '. $item['template'] .'
                                            </a>
                                        </h4>
                                        </div>
                                        <div id="collapse' . $counter .'" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="heading' . $counter .'">
                                        <div class="panel-body">
                                            '. html_entity_decode(  $item['mailbody'] ) .' 
                                            <button data-tid="' . $item['id'] . '" type="button" data-dismiss="modal"  class="btn btn-success btnsendtrigger">Send Mail</button>
                                        </div>
                                        </div>
                                    </div>'; 
                                   } 
                                    $counter++;
                                }
                            }
                            else 
                                echo '<h4 class="text-center">No email template has been configured yet. Please contact admin!</h4>';  
                            
                                echo "</div>"; 
                         ?>
                            </div> 
                            <div class="modal-footer">
                                <button type="button" class="btn btn-danger pull-right"    data-dismiss="modal" >Close</button>
                            </div> 
                            </div>
                        </div>
                        </div>   
                            </div>
                        </div>
                    </div> 
                     <div id="menu3" class="tab-pane maintab fade">
                        <div class="top-head">
                            <div class="col-xs-12 col-sm-12">
                                <h4>SEARCHING</h4>
                            </div> 
                        </div>
             
                        <div class="col-xs-12 col-sm-12">
                            <div class="globalsearch">
                                <div class="col-sm-6 col-xs-12 ">
                                    <label for="vocSrch">Vocations:</label>
                                    <!-- <input type="text" id="vocSrch" class="form-control"> -->
                                    <select id="vocSrch" class="dropdown chosen-select" multiple>
                                        <option value="">-vocation-</option>
                                        <?php
										foreach ($vocations as $vocation)
										{
											echo "<option value='" . $vocation['name'] . "'>" . $vocation['name'] . "</option>";
										}
										?>
                                    </select>
									<small class="pull-right">(Multiple vocations can be selected)</small>
                                </div>
                                <div class="col-sm-4 col-xs-12 ">
                                    <label for="locSrch">Zip Code:</label>
                                    <input type="text" id="locSrch" class="form-control">
								</div>
									<div class="col-sm-2 col-xs-12 padd-8 text-center"><br>
										<input type="button"  value="SEARCH" class="btn btn-primary btn-block srchPeople">
									</div>
                                <div class="clearfix"></div>
                            </div>
                        </div>
                       <div class="col-xs-12 col-sm-12 padd-5">
                            <hr/>
                        </div>
                        <div class="col-xs-12 col-sm-12 srdDtls" style="display: none">
                            <h4>Search result of nearby members</h4>
                            <div class="table-responsive"> 
                                <table class="table ">
                                    <thead>
										<tr>
											<th>Name</th>
											<th>Email</th>
											<th>Phone</th>
											<th>Zip</th>
											<th>Distance</th>
											<th>Knows</th>
											<th>Action</th>
										</tr>
                                    </thead>
                                    <tbody id="srchrslts"></tbody>
                                </table>
                            </div>
                        </div>
                    </div> 
					<div id="menu27" class="tab-pane maintab fade">
                            <div class="top-head">
                                <div class="col-xs-12 col-sm-8">
                                    <h4>REFERRAL TRACKING BY GROUP</h4>
                                </div> 
										
                                <div class="clearfix"></div>
                            </div>
                            <div class="col-xs-12 col-sm-12 col-sm-offset-0 no-padd">
								<div class="col-sm-2">
									<p class="text-right pad10">User Group:</p>
								</div>
								<div class="col-sm-4 pad10">
								 
											
								<select id='selectgroup'   name='selectgroup' 
								data-placeholder='Choose Group ...' class='group-select' > 
								<option value=''></option>
								<?php 
									foreach ($getGroups as $group )
									{
										echo "<option value='" . $group['id'] . "'>" . $group['name'] . "</option>";
									}
								?>												
								</select> 
									 </div>
									<div class="col-sm-3 pad5">
										<button class='btn btn-search-o' id='fetchgroupmembers'>Show Members</button>
									 </div>  
                            </div> 
							 <div class="col-xs-12 col-sm-12 col-sm-offset-0 no-padd">
								<div class='pad10' id='reftrackboard'></div>
							 </div>
                    </div>

					<div id="menu40" class="tab-pane maintab fade">
                            <div class="top-head">
                                <div class="col-xs-12 col-sm-8">
                                    <h4>REFERRAL TRACKING BY VOCATION</h4>
                                </div> 
										
                                <div class="clearfix"></div>
                            </div>
                            <div class="col-xs-12 col-sm-12 col-sm-offset-0 no-padd">
								<div class="col-sm-2">
									<p class="text-right pad10">User Vocation:</p>
								</div>
								<div class="col-sm-4 pad10">
								<select data-placeholder='Choose vocations ...' class='form-control' style='height:29px;' name="e_prof1" id="e_prof1"  >
								<option value=''>Select Vocation</option>
									<?php
									foreach ($vocations as $vocation) {
										echo "<option value='" . $vocation['name'] . "'>" . $vocation['name'] . "</option>";
									}
									?>
								</select> 
									 </div>
									<div class="col-sm-3 pad5">
										<button class='btn btn-search-o' id='fetchgroupmembersvoc'>Show Members</button>
									 </div>  
                            </div> 
							 <div class="col-xs-12 col-sm-12 col-sm-offset-0 no-padd">
								<div class='pad10' id='reftrackboardvoc'></div>
							 </div>
                    </div>
                    <?php if($_user_role != 'admin') { ?>
						<div id="menu9" class="tab-pane maintab fade">
							<div class="top-head">
								<div class="col-xs-12 col-sm-8">
									<h4>SUGGESTED PEOPLE</h4>
								</div>
								<div class="clearfix"></div>
							</div>
							<div class="clearfix"></div>
							
							<div class="col-xs-12 col-sm-10 col-sm-offset-1">
								<div class="search-loc">
									<!-- <div class="col-sm-5 col-xs-12 padd-8">
										<label for="targetSrch">Search by Vocation:</label>
										<select id="targetSrch" class="dropdown">
											<option value="">-vocation-</option>
											<?php
											foreach ($vocations as $vocation) {
												echo "<option value='" . $vocation['name'] . "'>" . $vocation['name'] . "</option>";
											}
											?>
										</select>
									</div> -->
									<div class="col-sm-5 col-xs-12 padd-8">
										<label for="nameSrch">Search by name:</label>
										<input type="text" id="nameSrch" class="form-control" placeholder="i.e. John Doe">
									</div>
									<div class="col-sm-2 col-xs-12 padd-8 text-center">
										<br>
										<input type="button" style="width: 100%;" value="SEARCH" class="btnblock srchTarget">
									</div>
									<div class="clearfix"></div>
								</div>
							</div>
							<div class="col-xs-12 col-sm-12 padd-5">
								<hr>
							</div>
							<div class="col-xs-12 col-sm-12 targetDtls" style="display: none">
								<h4>SEARCH RESULTS FOUND</h4>
								<div class="table-responsive">
									<div id="srchTargetRslts"></div> 
								</div>
							</div>
						</div>
					<?php
					  }
					?>
                <?php
				if ($_user_role == 'admin') 
				{
				?>
					<div id="menu28" class="tab-pane fade maintab">
						<div class="top-head">
							<div class="col-xs-12 col-sm-8">
								<h4>Manage Email Templates</h4>
							</div>
							<div class="clearfix"></div>
						</div>
						<div class="col-xs-12 col-sm-12 col-sm-offset-0  ">
							<div class="form-group">
									<label for="template">Template Name:</label>
									<input type='text' class="form-control"  name='template' id='template' />
							</div>
							<div class="form-group">
								<label for="templatetype">Template Type:</label>
								<select class="form-control" name='templatetype' id='templatetype' >
									<option value='0'>Trigger Email Template</option>
									<option value='1'>Referral Introduction Email</option>
										<option value='2'>LinkedIn Invitation Email</option>
										<option value='3'>Testimonial Videos</option>
										<option value='4'>Invite people to Other Group Meetings</option>
										<option value='5'>Unfinished Signup Notifier</option>
                                    </select>
							</div> 
							<div class="form-group">
								<label for="subject">Email Subject:</label>
								<input type='text' class="form-control"  name='subject' id='subject' />
							</div>
							<div class="form-group"> 
								<label for="emailtemplate">Email Body:</label>
								<textarea name='emailtemplate' class="form-control  "  id='emailtemplate' rows='5'></textarea>
							</div> 
							<div class="form-group">
								<button type='button' class='btn btn-primary' id='btnsavetemplate' >Save</button>
							</div>
							<div class="clearfix"></div>
						<div class="panel panel-success">
						  <div class="panel-heading">Available Template Variables</div>
						  <div class="panel-body">
						 <div class='row'>
						  <div class="col-md-6">
							 <strong>Receipent's Name</strong> {receipent}
						  </div>
						   <div class="col-md-6"> 
							   <strong>Website URL</strong> {link_url}
						  </div> 
						  </div>
						  <div class='row'>
							   <div class="col-md-6">
									<strong>Introducee's Name</strong> {introducee}
							   </div> 
							   <div class="col-md-6">
								  <strong>Introducee's Email</strong> {introducee_email}
							   </div> 
						  </div>
						  <div class='row'>
							<div class="col-md-6">
								<strong>Introducee's Phone</strong> {introducee_phone}
							</div> 
							<div class="col-md-6">
								<strong>Introducee's Profession</strong> {introducee_profession} 
							</div> 
						  </div>
						<div class='row'>  
						   <div class="col-md-12">
				<p style='padding:10px;border-radius:6px;background-color:#4c99d9;color:#fff;'><strong>How to use?</strong>:<br/>
				Copy and paste the text enclosed in braces in email body or subject 
				wherever you want the actual receipent name or introducee information to appear in outgoing 
				referral/introduction mail.
				<br/>
				NB: These template variables are available only for Referral Mail Type.
						  </p></div>
						 </div>
						  </div>
						</div>
				</div>
			<div class="col-xs-12 col-sm-12  ">	
				<div id='allmailtemplates'></div>
			</div> 
		<?php  
        if(sizeof($mailtemplates) > 0 )
        { 
		?>
		<div class="col-xs-12 people-know">
			<div class="col-xs-12">
				<h4>Existing Mail Templates</h4>
			</div>
			<div class="col-xs-12" style="overflow-x: auto;">
				<table class="table table-responsive">
					<thead>
						<tr>  
							<th>Mail Type</th> 
							<th>Template Name</th> 
							<th>Subject</th> 
							<th width='90px'>Action</th>
						</tr>
					</thead>
					<tbody id='triggermails'>
					<?php 
						$rowindex=1;
						foreach ($mailtemplates as $item )
						{
							echo "<tr><td>" .  $item['mailtype']  .  "</td><td id='tbody-" . $item['id'] . "'><span id='trigbody-" . $item['id'] . "'>" . $item['template'] ."</span>" ;
							echo "</td><td>" .  $item['subject']  .  "</td><td>
							<button class='btn-primary btn btn-xs editmailtemplate' data-id='" . $item['id'] . "'><i class='fa fa-pencil'></i></button>
							<button class='btn-danger btn btn-xs removemailtemplate' data-id='" . $item['id'] . "'><i class='fa fa-times-circle'></i></button></td></tr>";
							$rowindex++;
						}
					?>
					</tbody>
				 </table>
			 </div>
		</div>
		<?php 
			}
		?>	
		<!--Menu28-->
   </div> 
   <div id="menu29" class="tab-pane maintab fade">
		<div class="top-head">
			<div class="col-xs-12 col-sm-8">
				<h4>Manage Help Buttons</h4>
			</div>
			<div class="clearfix"></div>
		</div>
		<div class="col-xs-12 col-sm-12 col-sm-offset-0  "> 
			<div class="form-group">
				<label for="exampleInputEmail1">Help Title:</label>
				<input type='text' class="form-control" name='helptitle' id='helptitle' required />
			</div>
			<div class="form-group">
				<label for="exampleInputEmail1">Help Video:</label>
				<input type='text' class="form-control"  name='helpvideo' id='helpvideo' />
				</div>
				<div class="form-group">
					<button type='button' class='btn btn-primary' id='btnsavehelp' >Save</button>
				</div>
			<div class="clearfix"></div>
		</div>
		<!--
		<div class="col-xs-12 col-sm-12  ">	
			<div id='allmailtemplates'></div>
		</div> -->
						
		<div class="col-xs-12 people-know">
			<div class="col-xs-12">
				<h4>Help Button List</h4>
			</div>
			<div class="col-xs-12" style="overflow-x: auto;">
			<table class="table table-responsive">
								<thead>
                                    <tr> 
                                        <th>Help Title</th> 
                                        <th>Help Video</th> 
                                        <th>Action</th>
                                    </tr>
                                    </thead>
                                    <tbody id='divhelpvideos'>
									<?php 
										$rowindex=1;
										foreach ($help_data_buttons as $item )
										{
											echo "<tr><td id='tbody-" . $item['id'] . "'><span id='trigbody-" . $item['id'] . "'>" . $item['helptitle'] ."</span>" ;
											echo "</td><td>" .  $item['helpvideo']  .  "</td><td>
											<button class='btn-primary btn btn-xs edithelpvideo' data-id='" . $item['id'] . "'><i class='fa fa-pencil'></i></button>";
											$rowindex++;
										}
									?>
									<!--
											<button class='btn-danger btn btn-xs removemailtemplate' data-id='" . $item['id'] . "'><i class='fa fa-times-circle'></i></button></td></tr>-->
									</tbody>
                                </table>
                            </div>
                        </div>
					<!--Menu29 end-->	
	  </div>		
      <!--Menu8-->
	  <div id="menu8" class="tab-pane maintab fade">
                             <div class="top-head">
                                <div class="col-xs-12 col-sm-8">
                                    <h4>Search Logs</h4>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                            <div class="col-xs-12 col-sm-8 col-sm-offset-2 no-padd">
                                <div class="search-loc" style="background: none;border: 1px solid #dbdbdb">
                                    <div class="col-xs-12 no-padd">
									<div class="table-responsive">
                                <table class="table ">
                                    <thead>
                                    <tr>
                                        <th>User Name</th>
                                        <th>Vocation</th>
                                        <th>Location</th>
                                        <th>Date & Time</th>
                                    </tr>
                                    </thead>
                                    <tbody class="SearcLogs"></tbody>
                                </table>
                            </div>
									</div>
									 <div class="col-xs-12 no-padd">
									<div class="pagilog">
									</div></div>
								
                                    <div class="clearfix"></div>
                                </div>
                            </div>
                        </div>
			   <!--Menu8--> 
			   <!--Menu30-->
				<div id="menu30" class="tab-pane maintab fade">
                             <div class="top-head">
                                <div class="col-xs-12 col-sm-8">
                                    <h4>Home Search Logs</h4>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                            <div class="col-xs-12 col-sm-8 col-sm-offset-2 no-padd">
                                <div class="search-loc" style="background: none;border: 1px solid #dbdbdb">
                                    <div class="col-xs-12 no-padd">
									<div class="table-responsive">
                                <table class="table ">
                                    <thead>
                                    <tr>
                                        <th>City</th>
                                        <th>Zip</th>
                                        <th>Vocation</th>
                                        <th>Date & Time</th>
                                    </tr>
                                    </thead>
                                    <tbody class="HomeSearchLogs"></tbody>
                                </table>
                            </div>
									</div>
									 <div class="col-xs-12 no-padd">
									<div class="pagilog">
									</div></div>
								
                                    <div class="clearfix"></div>
                                </div>
                            </div>
                    </div>	 
					<div id="menu4" class="tab-pane maintab fade">
							<div class="top-head">
                                <div class="col-xs-12 col-sm-8">
                                    <h4>QUESTIONS</h4>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                            <div class="col-xs-12 col-sm-8 col-sm-offset-2 no-padd">
                                <div class="search-loc" style="background: none;border: 1px solid #dbdbdb">
                                    <div class="questionsData"></div>
                                    <div class="col-sm-12 col-xs-12 padd-8 text-center">
                                        <div class="col-xs-6 padd-3 text-left">
                                            <button class="btnblock addNewQues">ADD MORE FIELDS</button>
                                        </div>
                                        <div class="col-xs-6 padd-3 text-right">
                                            <input type="button" value="SAVE CHANGES" class="btnblock saveQues">
                                        </div>
                                    </div>
                                    <div class="clearfix"></div>
                                </div>
                            </div>
                        </div>
                        <div id="menu5" class="tab-pane maintab fade">
                            <div class="top-head">
                                <div class="col-xs-12 col-sm-8">
                                    <h4>GROUPS</h4>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                            <div class="col-xs-12 col-sm-12 col-sm-offset-0 no-padd  ">
                                <div class="search-loc" style="background: none;border: 1px solid #dbdbdb;padding-bottom: 30px;">
                                    <div class="col-sm-12 col-xs-12 padd-8">
                                        <h4><b>Groups You Have</b></h4>
                                    </div>
                                    <div class="col-sm-4 col-xs-12 padd-8">
                                  
                                        <select class="form-control userClientGrps">
                                            <option value="null">-select group-</option>
                                            <?php
                                            foreach ($getGroups as $item) {
                                                echo "<option value='" . $item['id'] . "'>" . $item['name'] . "</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    <div class="col-sm-4 col-xs-12 padd-8">
                                        <input type="text" class="form-control newGrpVal">
                                    </div>
                                    <div class="col-sm-4 col-xs-12 padd-8 text-center">
                                        <button style="margin-top: 0 !important" class="btn btn-primary updGroup">UPDATE GROUP
                                        </button>
                                        <button style="margin-top: 0 !important" class="btn btn-primary delGroup">DELETE GROUP
                                        </button>
                                    </div>
                                    <div class="clearfix"></div>
                                </div>
                            </div>
                            <div class="col-sm-12 col-xs-12 padd-8 text-center">
                                <br>
                            </div>
                            <div class="col-xs-12 col-sm-10 col-sm-offset-1 no-padd  ">
                                <div class="search-loc" style="background: none;border: 1px solid #dbdbdb; padding-bottom: 30px;">
                                    <div class="col-sm-12 col-xs-12 padd-8">
                                        <h4><b>Add New Groups</b></h4>
                                    </div>
                                    <div class="col-sm-5 col-xs-12 padd-8">
                                        <input type="text" placeholder="Add New Group" class="form-control groupName">
                                    </div>
                                    <div class="col-sm-2 col-xs-12 padd-8 text-center">
                                        <button style="margin-top: 0 !important" class="btn btn-primary addNewGroup">ADD GROUP
                                        </button>
                                    </div>
                                    <div class="clearfix"></div>
                                </div>
                            </div>
                        </div>
                        <div id="menu6" class="tab-pane maintab fade">
							<div class="col-xs-12 col-md-12">
								<div class="top-head"> 
                                    <h4>VOCATIONS</h4>
                                </div> 
                            </div>
							
                            <div class="col-xs-12 col-md-12">
                                <div class="search-loc" style="background: none;border: 1px solid #dbdbdb; padding: 10px;">
                                    
									<h4><strong>Vocations You Have</strong></h4>
                                    <div class='row'>
										<div class=" col-xs-12 col-md-4">
                                        <select class="form-control fetVocations">
                                            <option value="null">-select-</option>
                                            <?php
                                            foreach ($vocations as $vocation) {
                                                echo "<option value='" . $vocation['id'] . "'>" . $vocation['name'] . "</option>";
                                            }
                                            ?>
                                        </select>
										</div>
										<div class="col-xs-12 col-md-5">
											<input type="text" class="form-control editVocation">
										</div>	
										<div class="col-xs-12 col-md-1">
											<button style="margin-top: 0 !important" class="btn btn-primary btn-sm btnblock updVoc">UPDATE</button> 
										</div>
										<div class="col-xs-12 col-md-1"> 
											<button style="margin-top: 0 !important" class="btn btn-primary  btn-sm btnblock delVoc">DELETE</button>
										</div>
									</div> 
                                </div>
                            </div>
							 
                            <div class="col-xs-12 col-sm-10 col-sm-offset-1 no-padd margt3 ">
                                <div class="search-loc" style="background: none;border: 1px solid #dbdbdb; padding: 10px;">
                                    <div class="col-sm-12 col-xs-12 padd-8">
                                        <h4><b>Add New Vocation</b></h4>
                                    </div>
                                    <div class="col-sm-5 col-xs-12 padd-8">
                                        <input type="text" placeholder="Add New Vocation"
                                               class="form-control vocationName">
                                    </div>
                                    <div class="col-sm-2 col-xs-12 padd-8 text-center">
                                        <button style="margin-top: 0 !important" class="btn btn-primary btnblock addNewVoc">ADD NEW
                                        </button>
                                    </div>
                                    <div class="clearfix"></div>
                                </div>
                            </div>
                        </div> 
						
						<div id="menu26" class="tab-pane maintab fade">
                            <div class="top-head">
                                <div class="col-xs-12 col-sm-8">
                                    <h4>LIFESTYLES</h4>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                            <div class="col-xs-12 col-sm-12 col-sm-offset-0 no-padd">
                                <div class="search-loc" style="background: none;border: 1px solid #dbdbdb">
                                    <div class="col-sm-12 col-xs-12 padd-8">
                                        <h4><b>Existing Lifestyles:</b></h4>
                                    </div>
                                    <div class="col-sm-5 col-xs-12 padd-8">
                                        <select class="form-control fetchLifestyles">
                                            <option value="null">-select-</option>
                                            <?php
                                            foreach ($lifestyles as $lifestyle) {
                                                echo "<option value='" . $lifestyle['id'] . "'>" . $lifestyle['name'] . "</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    <div class="col-sm-5 col-xs-12 padd-8">
                                        <input type="text" data-lifestyle='' class="form-control editLifestyle">
                                    </div>
                                    <div class="col-sm-2 col-xs-12 padd-8 text-center">
                                        <button style="margin-top: 0 !important" class="btn btn-primary btnblock updLifestyle">UPDATE</button> 
                                    </div>
                                    <div class="clearfix"></div>
                                </div>
                            </div>
                            <div class="col-sm-12 col-xs-12 padd-8 text-center"><br></div>
                            <div class="col-xs-12 col-sm-10 col-sm-offset-1 no-padd  ">
                                <div class="search-loc" style="background: none;border: 1px solid #dbdbdb">
                                    <div class="col-sm-12 col-xs-12 padd-8">
                                        <h4><b>Add New Lifestyle</b></h4>
                                    </div>
                                    <div class="col-sm-5 col-xs-12 padd-8">
                                        <input type="text" placeholder="Add New Lifestyle"
                                               class="form-control lifestylename">
                                    </div>
                                    <div class="col-sm-2 col-xs-12 padd-8 text-center">
                                        <button style="margin-top: 0 !important" class="btn btn-primary btnblock addNewLifestyle">ADD NEW
                                        </button>
                                    </div>
                                    <div class="clearfix"></div>
                                </div>
                            </div>
                        </div>  
                        <div id="pagepackages" class="tab-pane maintab fade">
                            <div  class="pagesData">
                                <div  id="pages_edit" >
                                    <div class="top-head">
                                        <div class="col-xs-12 col-sm-8">
                                            <h4>Services & Pricing <button class="btn btn-primary btnblock" data-toggle="modal" data-target="#edit_package">Add Packages</button></h4>
                                        </div>
                                        <div class="clearfix"></div>
                                    </div>
                                    <div class="col-xs-12 col-sm-10 col-sm-offset-1 no-padd">
                                        <div class="row">
                                             
                                                <?php
                                                foreach ($allPackages as $allPackage) {
                                                    $id_pack = $allPackage['row']['id'];
                                                    $title_pack = $allPackage['row']['package_title'];
                                                    $price_pack = $allPackage['row']['package_price'];
                                                    $conn_limit = $allPackage['row']['conn_limit'];
                                                    $share_limit = $allPackage['row']['share_limit'];
                                                    $ref_limit = $allPackage['row']['ref_limit'];
                                                    $conn_desc = $allPackage['row']['conn_desc'];
                                                    $share_desc = $allPackage['row']['share_desc'];
                                                    $ref_desc = $allPackage['row']['ref_desc'];
                                                    $package_limit = $allPackage['row']['package_limit'];
                                                    $pkg_status = $allPackage['row']['pkg_status'];
                                                    $services = $allPackage['services'];

                                                    $share_limit = $share_limit == 0 ? 'Unlimited' : $share_limit;
                                                    $conn_limit = $conn_limit == 0 ? 'No' : $conn_limit;
                                                    $partnersSharing = $ref_limit == 0 ? 'Unlimited' : $ref_limit;

                                                    $min = $package_limit == 0 ? '' : "<h3>".$package_limit. " months minimum</h3>";
                                                    $status = $pkg_status == 'activate' ? 'deactivate' : 'activate';

                                                    ?>
													<div class=" col-sm-6 col-xs-12 packageDetails">
                                                    <div class="box text-center">
                                                        <h4 class="bg"><?php echo $title_pack; ?>
                                                            <i class="fa fa-power-off del_pkg" data-id="<?php echo $id_pack; ?>" data-toggle="tooltip"
                                                               title="<?php echo $status ?>"></i>
                                                            <i data-toggle="modal" data-target="#edit_package" class="fa fa-pencil edit_package"
                                                               data-id="<?php echo $id_pack; ?>"></i>
                                                        </h4>
                                                        <h4><span>$<?php echo $price_pack; ?></span></h4>
                                                        <?php echo $min ?>
                                                        <ul>
                                                            <li><?php echo $conn_limit . " " . $conn_desc; ?></li>
                                                            <li><?php echo $share_limit . " " . $share_desc; ?></li>
                                                            <li><?php echo $partnersSharing . " " . $ref_desc; ?></li>
                                                            <?php
                                                            foreach ($services as $service) {
                                                                echo "<li>".$service['services']."</li>";
                                                            }
                                                            ?>
                                                        </ul>
                                                    </div>
													 </div>
                                                <?php } ?> 
                                        </div>
                                    </div>
                                </div>
						</div>
					</div>
						<div id="pageaboutus" class="tab-pane maintab fade">
                                 <div  id="pages_edit" >
                                    <div class="top-head">
                                        <div class="col-xs-12 col-sm-8">
                                            <h4>About Us Changes </h4>
                                        </div>
                                        <div class="clearfix"></div>
                                    </div>
                                    <div class="col-xs-12 col-sm-8 col-sm-offset-2 no-padd">
                                        <div class="content-inner">
                                            <input type="text" class="form-control" placeholder="Content Title" name="about_title">
                                            <textarea name="about_content" id="" cols="30" rows="10" class="form-control" placeholder="Content"></textarea>
                                            <input type="button" class="btnblock saveAbout" value="Save now">
                                        </div>
                                    </div>

                                    <div class="clearfix"></div>
                                    <div class="col-sm-8 col-sm-offset-2  no-padd pageData">
                                        <?php
                                        foreach ($aboutUs as $aboutU) {
                                            $pg_id = $aboutU['id'];
                                            ?>
                                            <div class='pageDataInner'>
                                                <i class='fa fa-pencil edPgCntnt' data-toggle='modal' data-target='#editContent' data-id='<?php echo $pg_id ?>'
                                                   style='cursor: pointer;'></i>
                                                <i class='fa fa-trash delPgCntnt' data-id='<?php echo $pg_id ?>' style='cursor: pointer;'></i>
                                                <div class="clearfix"></div>
                                                <h3><?php echo $aboutU['page_title'] ?></h3>
                                                <p><?php echo nl2br($aboutU['page_content']) ?></p>
                                            </div>
                                            <div class='clearfix'></div>
                                            <hr/>
                                        <?php } ?>
                                    </div>
							</div>
                                </div>

                   <div  id="pageblog" class="tab-pane maintab fade">
						<div  id="pages_edit" >                                   
								   <div class="top-head">
                                        <div class="col-xs-12 col-sm-8">
                                            <h4>Blog Changes </h4>
                                        </div>
                                        <div class="clearfix"></div>
                                    </div>
                                    <div class="col-xs-12 col-sm-10 col-sm-offset-1 no-padd">
                                        <div class="content-inner">
                                            <button class="btnblock pull-right" data-toggle="modal" data-target="#edit-1"> Customize Blog Title</button>
                                            &nbsp;&nbsp;<button data-toggle="modal" data-target="#addblog" class="btnblock pull-right">Add Blog</button>
                                            <div class="clearfix"></div>
                                            <div class="panel-group blogsData" id="accordion" role="tablist" aria-multiselectable="true">
                                                <?php getBlogs(); ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
				 </div>
				 <div  id='pagetagline' class="tab-pane maintab fade">
						 <div  id="pages_edit" >			
							  <div class="top-head">
                                        <div class="col-xs-12 col-sm-8">
                                            <h4>Tagline</h4>
                                        </div>
                                        <div class="clearfix"></div>
                                    </div>
									<div class="col-xs-12 col-sm-8 col-sm-offset-2 no-padd">
                                        <div class="content-inner">
										<input type="text" class="form-control" value="<?php echo $tagline[0]['page_content']; ?>" placeholder="Tagline" name="tagline">
										<input type="button" class="btn btn-primary btnblock saveTagline" value="Save now">
										</div>
									</div>
                                </div>
                     </div>  
				<div  id="blogmanage" class="tab-pane maintab fade">
						<div  id="pages_edit" >
							<div class="top-head">
								<div class="col-xs-12 col-sm-8">
									<h4>Manage Blog Posts </h4>
								</div>
								<div class="col-xs-12 col-sm-4">
									<a class='btn btn-primary' data-toggle="tab" href='#addpost'>Add New Post</a>
								</div>
								<div class="clearfix"></div>
							</div>
							<div class="col-xs-12 col-sm-10 col-sm-offset-1 no-padd">
							
									<div  id='allpost'>
									</div>
                            </div>
						</div>
				</div>
				<div  id="addpost" class="tab-pane maintab fade">
						<div  id="pages_edit" >                                   
								   <div class="top-head">
                                        <div class="col-xs-12 col-sm-8">
                                            <h4>Add New Post</h4>
                                        </div>
                                        <div class="clearfix"></div>
                                    </div>
                                    <div class="col-xs-12 col-sm-10 col-sm-offset-1 no-padd">
                                          
                                     <div class="form-group">
										<label for="exampleInputEmail1">Post Title:</label>
										<input type='text' class="form-control"  name='posttitle' id='posttitle' />
									  </div>
									<div class="form-group">
										<label for="exampleInputPassword1">Post Content:</label>
										<textarea name='postbody' class="form-control  "  id='postbody' rows='5'></textarea>
								    </div>
							<input id='savepost' class='btn btn-primary' type='button' value='Save' name='savepost'/>
						</div>
					</div>
				</div>
				<div  id="editpost" class="tab-pane maintab fade">
						<div  id="pages_edit" >                                   
								   <div class="top-head">
                                        <div class="col-xs-12 col-sm-8">
                                            <h4>Edit Post</h4>
                                        </div>
                                        <div class="clearfix"></div>
                                    </div>
                                    <div class="col-xs-12 col-sm-10 col-sm-offset-1 no-padd"> 
									  <div class="form-group">
										<label for="exampleInputEmail1">Post Title:</label>
										<input type='text' class="form-control"  name='posttitle' id='editposttitle'/>
									  </div>
									  <div class="form-group">
										<label for="exampleInputPassword1">Post Content:</label>
										<textarea name='postbody' class="form-control  "  id='editpostbody' rows='5'></textarea>
									  </div>
									  <input type='hidden' id='postid' />
									  <input class='btn btn-primary' id='updatepost' type='button' value='Save' name='updatepost'/>
					     </div>
			     </div>
	   </div>
	  <?php }  ?>
   </div>
   </div> <!-- end of col12 -->
   </div><!--end of row -->
   </div>
  </div>
 </div>
  </div> 
</div> 
<?php 
 include_once('template/footer.php');
?>


<?php 
 include_once('template/footerjs.php');
?>
  </body>
</html>
