<?php 
ob_start();
include_once("template/head.php");
if (!isset($_SESSION['user_id']))
{
	header('location: index.php'); 
}
include_once 'includes/db.php';
include_once 'includes/functions.php';

if(  $_SERVER['HTTP_HOST'] == "localhost" || $_SERVER['HTTP_HOST'] == "mycity.dev")
{
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
$user_picutre = ((file_exists($_SESSION['user_pic']))?$_SESSION['user_pic']:"images/no-photo.png");
$userGrp = '';
$user_pkg = '';
$hideClass = $_user_role == 'admin' ? "" : "hide";


/*Profile Update*/
if(isset($_POST['upload_btn']))
{
    if(!empty($_FILES['prof_img']['name']))
	{
        $ext=end(explode(".",$_FILES['prof_img']['name']));
		$upload_path="prof-img-".date("Ymd-His").".".$ext;
		if(move_uploaded_file($_FILES['prof_img']['tmp_name'],"images/".$upload_path))
		{
			echo "UPDATE `mc_user` SET image='".$upload_path."' WHERE user_id=".$user_id;
			$link->query("UPDATE `mc_user` SET image='".$upload_path."' WHERE id=".$user_id);
			$user_picutre="images/".$upload_path;
			$_SESSION['user_pic']="images/".$upload_path;
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

if ($_user_role == 'admin')
{
	$text = 'REGISTERED PEOPLE';
}
else
{
	$text = 'PEOPLE YOU KNOW DETAILS';
}


$ques_data = getQues($link);
$param = array('id' => '0');
$getGroups = json_decode(   curlexecute($param, $siteurl . 'api/api.php/groups/'), true);  //  getGroups($link);  
$allPackages = getPackages();
$aboutUs = getPageDetails('about');
$tagline = getPageDetails("tagline"); 
$param = array('id' =>  $user_id );
$triggers = json_decode(   curlexecute($param, $siteurl . 'api/api.php/triggers/'), true);  //  getMyTriggers($link, $user_id);
$param = array('id' =>  $user_id );
$mygroups = json_decode(   curlexecute($param, $siteurl . 'api/api.php/groups/'), true);  //  getMyGroups($link, $user_id);
$param = array('id' => '0');
$vocations =    json_decode(   curlexecute($param, $siteurl . 'api/api.php/vocations/'), true);  //    getVocations($link); 
$lifestyles =    json_decode(   curlexecute($param, $siteurl . 'api/api.php/lifestyle/'), true)['results'] ;   
$cities = json_decode(   curlexecute($param, $siteurl . 'api/api.php/cities/'), true); // getAllCities($link);  

$mailtemplates = json_decode(   curlexecute($param, $siteurl . 'api/api.php/emailtemplates/'), true);  // getMailTemplates($link );
$help_data =  json_decode(   curlexecute($param, $siteurl . 'api/api.php/helps/'), true); // getHelps($link);
$help_data_buttons =  json_decode(   curlexecute($param, $siteurl . 'api/api.php/helpbuttons/'), true); //  getHelpsButtons($link);
$video_testimonials =  json_decode(   curlexecute($param, $siteurl . 'api/api.php/testimonials/'), true); // getTestimonials($link);
$alltags = json_decode(   curlexecute($param, $siteurl . 'api/api.php/tags/'), true);


$param = array('userid' =>  $user_id, 'page' => 0 );
$directmailsuggest = json_decode(   curlexecute($param, $siteurl . 'api/api.php/member/autosuggest/'), true);

$param = array('profileid' =>  $user_id);
$my_profile =  json_decode(   curlexecute($param, $siteurl . 'api/api.php/member/getbyid/'), true); // getTestimonials($link);

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
		<div class="row">
			<div class='col-md-3 sidepane'>
			 <div class="panel panel-default">
                <div class="panel-heading">
                    <h4>Accountability Stats</h4>
                </div>
            <div class="panel-body">
			<input type="text" class="searchname" id="searchname" placeholder="Partner Name ...">  
                    <a  href='#menu35' data-toggle="tab" class="btn-white btnsearchpartner"><i class='fa fa-search'></i></a> 
			</div>
			
			</div>
			
		 <div class="panel panel-default">
                <div class="panel-heading">
                    <h4>People you know</h4>
                </div>
            <div class="panel-body">
			<ul  id='peopleknow'>
					<li><a data-toggle="tab" href="#menu2" ><i class="fa fa-user-plus"></i>Add/Update People</a></li>
					<li><a data-toggle="tab" href="#menu25" class='loadimportedknows' ><i class="fa fa-users"></i> Manage Imported Knows</a>
					<a href="<?php echo $help_d0ata_buttxsons[10]['helpvideo']; ?>" target="_blank" ><i id='hint-profile1' class='glyphicon glyphicon-arrow-right' ></i><span style="color:red;">Help</span></a>
					</li>  
					<li><a data-toggle="tab" href="#menu24"  ><i class="fa fa-upload"></i> Mass Upload Your Knows</a></li> 
 				<li class="close_drop"><a data-toggle="tab" data-pagesize='10' data-pageno='1' class='showreferrals' href="#menu17" id='hint-addreferral'> <i class="fa fa-users"></i>Introduction/Referral</a>
						<a href="<?php echo $help_data_buttons[1]['helpvideo']; ?>" target="_blank" ><i class='glyphicon glyphicon-arrow-right' ></i><span style="color:red;"> Help</span></a>
					</li>
					<li><a data-toggle="tab"  href="#" class='ref_wizard'><i class="fa fa-support"></i> Referral Wizard</a>
					<a href="<?php echo $help_data_buttons[9]['helpvideo']; ?>" target="_blank" ><i class='glyphicon glyphicon-arrow-right' ></i><span style="color:red;"> Help</span></a>
					</li>  
 					<li><a data-toggle="tab" title='Import Linked Contacts' class='newKnowEntries' href="#menu39"><i class="fa fa-linkedin"></i>Import LinkedIn Contacts</a></li>
        <li><a data-toggle="tab" title='Generate a report of imported LinkedIn Contacts' class='linkedinimportlist' href="#menu40" ><i class="fa fa-linkedin"></i>View Imported LinkedIn Contacts</a></li>
        <?php if($_user_role == 'admin'  ) { ?>
            <li><a data-toggle="tab" title='Reverse Tracking of Partners' href="#menu46" class='showreversetrackpane' ><i class="fa fa-user"></i> Reverse Tracking</a></li>
            <li><a data-toggle="tab" title='Show Unfinished Signups' data-page='1' href="#menu48" class='viewunfinishedsignup' ><i class="fa fa-user"></i> Incomplete Signups</a></li>
        <?php } ?>
	 </ul> 
			</div>
			
			</div>
		        <?php if($_user_role != 'admin'  ) { ?>	
			 <div class="panel panel-default">
                <div class="panel-heading">
                    <h4>Direct Email Service</h4>
                </div>
            <div class="panel-body">
<ul  id='directmail'>
                   
                <li class="close_drop"><a data-toggle="tab" class='btnviewalldmr' href="#menu49"> <i class="fa fa-users"></i> Search Members</a></li>
                    <li class="close_drop"><a data-toggle="tab" class='btnviewdmrequests' href="#menu50"> <i class="fa fa-envelope"></i> Requests Approved</a></li>
                    <li class="close_drop"><a data-toggle="tab" class='btnviewdmrequestssent' href="#menu51"> <i class="fa fa-envelope"></i> Request Sent</a></li> 
                    <li class="close_drop"><a data-toggle="tab" class='btnviewdmrequestsrcv' href="#menu52"> <i class="fa fa-envelope"></i> Request from other member</a></li>
             
 
                   </ul>
			</div>
			
			</div>
			<?php } ?>
			
			
			
			<div class="panel panel-default">
                <div class="panel-heading">
                    <h4>Reminders</h4>
                </div>
            <div class="panel-body"> 
			
				  <ul    id='reminders'>
				    <li class="close_drop"> 
						 <a  data-toggle="tab" class='fetchreminder'  href='#menu34' ><i class="fa fa-bell-o"></i>  Check Reminders</a>
					</li> 
					<li class="close_drop"> 
						 <a  data-toggle="tab" class='configureReminder' href="#menu32"><i class="fa fa-clock-o"></i> Set Reminder</a>  
					</li> 
					<li class="close_drop"> 
						 <a  class='showremindersummary' data-toggle="tab" href="#menu33"><i class="fa fa-clock-o"></i> Edit Reminders</a>  
					</li> 
				</ul>
				
				
			</div>
			
			</div>
			
<div class="panel panel-default">
                <div class="panel-heading">
                    <h4>Tools</h4>
                </div>
            <div class="panel-body">
						<ul>    
				<li class="close_drop "  ><a class='loadprofile' data-toggle="tab" href="#menu1"><i class="fa fa-user"></i>Client / User</a> 
				<a href="<?php echo $help_data_buttons[0]['helpvideo']; ?>" target="_blank" ><i id='hint-profile1' class='glyphicon glyphicon-arrow-right' ></i><span style="color:red;">Help</span></a>
				 	
				</li> 
               
        <?php if($_user_role != 'admin'  ) { ?>
				 <li class="close_drop"><a data-toggle="tab" class='getmypartners' href="#menu13"> <i class="fa fa-users"></i>Your Partners</a></li>
				 <li class="close_drop"><a data-toggle="tab" class='getratedpartners' href="#menu16"> <i class="fa fa-users"></i>Highest Rated Partners</a></li>
                
 
<?php } ?>
				<li><a data-toggle="tab" href="#menu3"><i class="fa fa-search"></i>Search page</a></li>
                <?php if ($_user_role == 'admin') { ?> 
				<li data-toggle="collapse"  data-target="#sitepages" class="subnavctrl collapsed active close_drop">
					<a href="#"><i class="fa fa-edit"></i>Page Changes <span class="arrow"></span></a>
		</li>
        <ul class="sub-menu collapse"  id='sitepages'>
				<li ><a data-toggle="tab"  href="#pagepackages" ><i class="fa fa-cube"></i> Packages</a></li>
				<li><a data-toggle="tab"  href="#pageaboutus"  ><i class="fa fa-support"></i> About Us</a></li> 
				<li><a data-toggle="tab" id='manageblog'  href="#blogmanage" ><i class="fa fa-pencil-square"></i> Blog</a></li>
				<li><a data-toggle="tab"  href="#pagetagline" ><i class="fa fa-tags"></i> Tagline</a></li>
	    </ul>
        <li data-toggle="collapse"  data-target="#usertools" class="subnavctrl collapsed active">
            <a href="#"><i class="fa fa-cog fa-lg"></i> Users Management <span class="arrow"></span></a>
			</li>
                <ul class="sub-menu collapse" id="usertools">
					<li><a data-toggle="tab" class='newSignup' href="#menu14"><i class="fa fa-users"></i>New Clients Group Request</a></li>
					<li><a data-toggle="tab" class='get_FAQ' href="#menu11"><i class="fa fa-support"></i>Help / FAQ</a></li>
					<li><a data-toggle="tab" href="#menu5"><i class="fa fa-users"></i>Groups</a></li>
					<li><a data-toggle="tab" class='knowstatpane' href="#menu18"><i class="fa fa-users"></i>Knows Stats</a></li>
					<li><a data-toggle="tab" class='fetchpoints' href="#menu22"><i class="fa fa-users"></i>Manage Loyalty Points</a></li>
					<li><a data-toggle="tab" title='Generate a report of who entered new knows recently' class='newKnowEntries' href="#menu23"><i class="fa fa-bar-chart"></i>New Know Report</a></li>
                    <li><a data-toggle="tab" title='Generate a report of who signuped recently'  href="#menu45"><i class="fa fa-users"></i> New Signups</a></li>
                    <li  ><a data-toggle="tab" href="#menu27"> <i class="fa fa-users"></i>Track Referrals By Group</a></li>
                    <li  ><a data-toggle="tab" href="#menu40"> <i class="fa fa-users"></i>Track Referrals By Vocation</a></li>
                    <li><a data-toggle="tab" title='Singup from LinkedIn Invite' class='linkedinsignup' href="#menu43" ><i class="fa fa-linkedin"></i> LinkedIn Contacts Signups</a></li>
                    <li><a data-toggle="tab" title='Export to Spreadsheet' class='' href="#menuExportSpread" ><i class="fa fa-bar-chart"></i> Export to Spreadsheet</a></li>
				</ul>  
			<li><a data-toggle="tab" href="#menu4"><i class="fa fa-question"></i> Questions</a></li>
			<li data-toggle="collapse"  data-target="#sytemconfig" class="subnavctrl collapsed active close_drop">
			    <a href="#"><i class="fa fa-edit"></i>System Configuration <span class="arrow"></span></a>
			</li>
			<ul class="sub-menu collapse"  id='sytemconfig'>
				<li><a data-toggle="tab" href="#menu6"><i class="fa fa-graduation-cap"></i> Vocations</a></li>
				<li><a data-toggle="tab" href="#menu26"><i class="fa fa-graduation-cap"></i> Lifestyle</a></li>
                <li><a data-toggle="tab" href="#menu28"><i class="fa fa-envelope"></i> Configure Mail Templates</a></li> 
				<li><a data-toggle="tab" href="#menu29"><i class="fa fa-cog"></i> Manage Help Buttons</a></li>
				<li><a data-toggle="tab" href="#menu38"><i class="fa fa-cog"></i> Manage Testimonials</a></li>
				<li><a data-toggle="tab" class='reloadsettings' href="#menu42" ><i class="fa fa-cog"></i> Add/Edit Common Vocations</a></li>
                <li><a data-toggle="tab" class='reloadsettings' href="#menu47" ><i class="fa fa-cog"></i> Add/Edit Tags</a></li>
                
            </ul>
			<li><a data-toggle="tab" href="#menu8"><i class="fa fa-graduation-cap"></i> Search Logs</a></li>
			<li><a data-toggle="tab" href="#menu30"><i class="fa fa-graduation-cap"></i> Home Search Logs</a></li>
			<li><a data-toggle="tab"  class='loadinbox' href="#menu20"> <i class="fa fa-envelope"></i>Inbox</a></li>
			<?php } ?>
			    <li><a data-toggle="tab"  class='loadmyinbox' href="#menu21"> <i class="fa fa-envelope"></i>Mailbox</a> 
				<a href="<?php echo $help_data_buttons[2]['helpvideo']; ?>" target="_blank" ><i class='glyphicon glyphicon-arrow-right' ></i><span style="color:red;"> Help</span></a> 
			</li> 
			<li><a data-toggle="tab" href="#menu7"> <i class="fa fa-envelope"></i>Feedback</a></li>
			<li><a data-toggle="tab" class='getpublicfaqs' href="#menu10"> <i class="fa fa-support"></i>Help / FAQ</a></li>
            <li><a data-toggle="tab" href="#menu12"> <i class="fa fa-question-circle"></i>My Triggers</a>
			<a href="<?php echo $help_data_buttons[3]['helpvideo']; ?>" target="_blank" ><i class='glyphicon glyphicon-arrow-right' ></i><span style="color:red;">Help</span></a>
            </li>  
            <?php if($user_id == '19' ) 
               { ?>
		    <li>
                <a class='messagetomember' data-toggle="tab" href="#menu44"><i class="fa fa-user"></i>Messages to Members</a>  
			</li> 
            <?php } ?>
            <li> 
				<a class='viewperformance' data-toggle="tab" href="#menu31"><i class="fa fa-user"></i>Performance Report</a>  
			</li> 
			 

		</ul>
			
			</div>
			
			</div>

      </div>
           <div class="col-md-9">
               <div class="tab-content">
				<div class="row">
						<div class='col-xs-12 col-md-12'>
						
						<div class="tab-content">
			<!--Menu11--> 
			<div id="menu11" class="tab-pane fade">
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
                       	<button class="btnblock save_helpinstruction">Submit</button>
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
	<div id="menu31" class="tab-pane fade">
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
	<div id="menu32" class="tab-pane fade">
		<div class="top-head">
			<div class="col-xs-12 col-sm-8">
				<h4>Configure Reminder</h4>
			</div>
			<div class="clearfix"></div>
		</div>
		<div class="col-xs-12 col-sm-10 col-sm-offset-1 no-padd">
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
				<div class='col-md-3'>
					<div class="form-group">
					<label for="title">Assigned To:</label>
					<input type="text" class="form-control" id="assignno" placeholder="Assign reminder to ...">
					<input type="hidden" class="form-control" id="hidassignno"  >
					</div> 
				</div>
				<div class='col-md-4'>
					<div class="form-group">
					<label for="remindermailday">Email Reminder on the day of:</label>
					<input type="text" class="form-control" id="remindermailday" placeholder="Reminder Title">
					 
					</div> 
				</div>
			<div class='col-md-4'>
					<div class="form-group">
					<label for="remindermailday">Email Reminder on the day of:</label>
					<select  class="form-control form-control-sm" id="hour">
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
					<select  class="form-control form-control-sm" id="min">
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
					<select  class="form-control form-control-xs" id="hrformat">
						<option>AM</option>  
						<option>PM</option>  
					</select>
					 
					</div> 
				</div>
			 </div>  
			  <button type="button" id='btnsavereminder'  class="btn btn-orange">Submit</button>
			 <button type="button" id='btnclearreminder' class="btn btn-orange-outline">Cancel</button>

			</div>
			<div class="clearfix"></div>
		</div>
	</div>	
	 <!--end of Menu31--> 
	 <!--Menu33-->
	<div id="menu33" class="tab-pane fade">
		<div class="top-head">
			<div class="col-xs-12 col-sm-8">
				<h4>Manage My Reminders</h4>
			</div>
			<div class="clearfix"></div>
		</div>
			<div class="col-xs-12 col-sm-10 col-sm-offset-1 no-padd">
				
				<div id='remindersummary'></div>
				<div class="clearfix"></div>
			</div>
		</div>	
	 <!--end of Menu33-->
	 
	 <!--Menu34-->
	<div id="menu34" class="tab-pane fade">
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
	<div id="menu35" class="tab-pane fade">
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
	<div id="menu36" class="tab-pane fade">
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
	<div id="menu37" class="tab-pane fade">
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

 <div id="menu38" class="tab-pane fade"> 
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
											echo "<tr><td id='tbody-" . $item['id'] . "'><span class='videolink" . $item['id'] . "'>" . $item['videolink'] ."</span>" ;
											echo "</td><td class='videosummary" . $item['id'] . "'>" .  $item['summary']  .  "</td><td>
											<button class='btn-primary btn btn-xs edittestimonial' data-id='" . $item['id'] . "'><i class='fa fa-pencil'></i></button> 
											<button class='btn-danger btn btn-xs deletestimonial' data-id='" . $item['id'] . "'><i class='fa fa-trash'></i></button>

											";
											$rowindex++;
										}

										if($rowindex == 1)
										{
											echo '<tr><td colspan="3">No testimonial exists!</td></tr>'; 
										}
									?>
									</tbody>
                                </table>
                            </div>
                        </div>
	 <!--menu38 end-->	
   </div>	 
   <!-- menu39 -->
   <div id="menu39" class="tab-pane">
        <div class="top-head">
                            <div class="col-xs-12 col-sm-8">
                                <h4>Linkedin Contact Import </h4>
                            </div>
                           
                            <div class="clearfix"></div>
                        </div>
                        <div class="col-xs-12 col-md-12  "> 
							 <form action="includes/uploader-2.php"
							  class="dropzone"
							  id="my-knows"></form>
							  <div class='form-group pad10 text-center'>
							 <button class='btn btn-primary btn-lg linkedinimport'>Start Import</button> 

	 <a data-toggle="tab" href="#menu40"  class='btn btn-danger btn-lg linkedinimportlist'>
	 View LinkedIn Import List</a> 
							  </div>
                        </div>
                    </div>
			  <!--menu39 -->
	<!-- menu40 -->
	<div id="menu40" class="tab-pane">
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
				<button type='button' class="btnblock filterlinkedincontact">Search</button> </form>
                   <div id='linkedinlist'></div> 
			</div>
      	</div> 
	<!--menu40 -->
	<!-- menu41 -->
	<div id="menu41" class="tab-pane">
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
			<button type='button' class="btnblock filterlinkedincontact">Search</button> </form>
                <div id='contactsaddedlastweek'></div> 
			 </div>
        </div>
	 <!-- menu41 -->
	 <!-- menu42 -->
	  <div id="menu42" class="tab-pane">
        <div class="top-head">
			<div class="col-xs-12 col-sm-8">
			   <h4>Common Vocations For Imported Contacts</h4>
	        </div>
		<div class="clearfix"></div>
		</div>
		 <div class="col-xs-12 col-sm-10 padd-5">
          <div class="col-sm-12 col-xs-12"><label class="custom-label">Vocation(s):</label></div>
		   <div class="col-sm-12 col-xs-12"> 
			   <div id="comvoc"></div> 
              <select data-placeholder='Choose vocations ...' multiple class="form-control chosen-select  common_vocations" name="common_vocations[]" id="e_prof"  > 
                  <?php
                       foreach ($vocations as $vocation) {
                          echo "<option value='" . $vocation['voc_name'] . "'>" . $vocation['voc_name'] . "</option>";
                       }
                   ?>
             </select>
             <small class="pull-right">(Enter comma seperated)</small>
            </div>  
		 <div class="col-sm-12 col-xs-12  ">
			 <button type='button' class='btn btn-primary savesettingscv'>Save Settings</button>
		</div></div>
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
	<div id="menu43" class="tab-pane">
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
	<div id="menuExportSpread" class="tab-pane">
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
	<div id="menu44" class="tab-pane">
        <div class="top-head">
			<div class="col-xs-12 col-sm-8">
			   <h4>Message To Member  
               </h4>
            </div>
            <div class="col-xs-12 col-sm-4"> 
                   <span  class="btn btn-success editinvitemailtemplate" style='margin-top: 10px' >
                   <i class='fa fa-pencil' ></i> View Email Template
                   </span> 
            </div>
            
		<div class="clearfix"></div>
		</div>
		<div class="col-xs-12 col-md-12  ">
			  
        <div id="mypartnerslist"></div>     
             </div>
        </div>
	 <!-- menu44 -->
 
	<!-- menu46 -->
	<div id="menu46" class="tab-pane">
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
                            
                                foreach ($cities as $city)
                                {
                                    echo "<option value='" . $city['name'] . "'>" . $city['name'] . "</option>";
                                }
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
											 echo "<option value='" .$vocitem['voc_name']  . "'>" . $vocitem['voc_name'] . "</option>";
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
                </div>
                    <button type='button' class="btnblock reversetrackpartner">Search</button>        
				</form>
                   <div id='reversetrackinglist'></div>  
                   <div id="rtmember"></div>
			</div>
      	</div> 
	<!--menu46 --> 
    <!--Menu12--> 
	<div id="menu12" class="tab-pane fade"> 
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
             	<button style="margin-top: 0 !important" class="btnblock addNewTrigger">ADD NEW</button>
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
				<div id="menu13" class="tab-pane fade"> 
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
                                        <button style="margin-top: 0 !important" class="btnblock showselectedProfile">VIEW PROFILE</button>
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
				<div id="menu16" class="tab-pane fade"> 
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
                                            foreach ($mygroups as $group ) {
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
											 echo "<option value='" .$vocitem['voc_name']  . "'>" . $vocitem['voc_name'] . "</option>";
										}
										?>
									</select> 
                                    </div>
                                    <div class="col-sm-2 col-xs-12 padd-8 text-center">
                                        <button style="margin-top: 0 !important" class="btnblock showratedpartners">SHOW PARTNERS</button>
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
				<?php  if($user_pkg == "Gold") { ?>	
				<div id="menu17" class="tab-pane fade"> 
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

						<div class="col-xs-12 col-sm-12  ">	
						<div id='suggestedconnects'></div>
					
						</div>  
                </div>
				<?php  } ?>	
				<!--menu17-->
				<!--menu18-->
			<div id="menu18" class="tab-pane fade"> 
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
				<div id="menu19" class="tab-pane fade">
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
				<div id="menu20" class="tab-pane fade"> 
                            <div class="top-head">
                                <div class="col-xs-12 col-sm-8">
                                    <h4>Inbox</h4>
                                </div>
                                <div class="col-xs-12 col-sm-4">	
                                   
                                 </div>
                                <div class="clearfix"></div>
                            </div>
						<div class="col-xs-12 col-sm-12  ">	
						  <?php
						  	$sortfield = 'abcdefghijklmnopqrstuvwxyz';
						  	?> 
							<div id='inboxgrid'></div>
						</div> 
							
                </div> 
				<!--menu20-->

				<!--menu21--> 
				<div id="menu21" class="tab-pane fade"> 
                            <div class="top-head">
                                <div class="col-xs-12 col-sm-6">
									<h4>Referral Suggestion Mailbox</h4>
								</div>								
								<div class="col-xs-12 col-sm-6">								
									<div class="btn-group" role="group"  >							  
										<button type="button"  data-mf="0" class="btn btn-primary btn-mailfilter loadmyinbox " >Referral Mails</button>
										<button type="button"   data-mf="1" class="btn btn-default btn-mailfilter  loadtriggerinbox">Trigger Mails</button>
										<button type="button"   data-mf="2" class="btn btn-default btn-mailfilter loadlinkedininvites">LinkedIn Contacts</button> 
									</div>								
								</div> 
                                <div class="clearfix"></div> 
                  </div>  
				  <div class="col-xs-12 people-know">
					<form class='form-inline form-gray'>
						 <div class="form-group">
							<label>Referrals given</label> 
							<input type="text" placeholder="Receipent Name ..." class="form-control search-control" id="searchreceipent">
						</div> 
						<button class="btnblock searchmailbox">Search</button> </form>
					</div>

						<div class="col-xs-12 col-sm-12  " > 

 

							<div id='myoutboxgrid'></div>  
						</div> 	 
                </div> 
				<!--menu21-->
				
				<!--menu22--> 
				<div id="menu22" class="tab-pane fade"> 
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
				<div id="menu23" class="tab-pane">
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
											echo "<option value='" . $vocation['voc_name'] . "'>" . $vocation['voc_name'] . "</option>";
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
										echo "<option value='" . $group['id'] . "'>" . $group['grp_name'] . "</option>";
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
				<div id="menu45" class="tab-pane">
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
                                  <button style="margin-top: 15px;" type="submit" class="btn btn-search-o shownewsignups" >Show New Signups</button>
  
							</div>
						</div>
					<div class="table-responsive" id='newsignups'>
					</div>
                </div>
            </div>
            <!--menu45 --> 
             

  <!-- menu47 -->
  <div id="menu47" class="tab-pane">
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
<div id="menu48" class="tab-pane">
                        <div class="top-head">
                            <div class="col-xs-12 col-sm-8">
                                <h4><i class='fa fa-users'></i> Incomplete Signups</h4>
                            </div> 
                            <div class="clearfix"></div>
                        </div>
                           
                        <div class="col-xs-12 col-md-12  ">
              <!--
                        <form class='  form-gray-wide' style='visibility:hidden'>

            <div class='row'>
                <div class="col-xs-12 col-md-3">
						 <div class="form-group">
							<label>From:</label> 
							<input type="text" placeholder="Signup from ..." class="form-control search-control" id="tbfrom">
                           
                        </div> 
                </div> 
                <div class="col-xs-12 col-md-3">
						 <div class="form-group">
							<label>to:</label> 
							<input type="text" placeholder="Signup to ..." class="form-control search-control" id="tbto">
                           
                        </div> 
                </div> 
                <div class="col-xs-12 col-md-3 ">   
                         <button type='button' class="btnblock viewunfinishedsignup">Show All</button>   
                </div>  
            </div> 
           </form> -->
                
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
        <?php  } ?>
        <!-- menu49 -->
<div id="menu49" class="tab-pane">
                        <div class="top-head">
                            <div class="col-xs-12 col-sm-8">
                                <h4><i class='fa fa-users'></i> Members You May Be Interested To Contact</h4>
                            </div> 
                            <div class="clearfix"></div>
                        </div>
                           
                        <div class="col-xs-12 col-md-12  ">
             
                        <form class='  form-gray-wide'  >

            <div class='row'>
            <div class="col-xs-2">
            <label>Name:</label> 
                <input type="text"  placeholder="Specify Name" id='dmname' class="form-control dmname">
            </div>
            
            <div class="col-xs-3"> 
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

                <div class="col-xs-12 col-md-5">
						 <div class="form-group">
							<label>Vocation:</label> 
                            <select data-placeholder='Choose vocations ...' multiple class="form-control chosen-select dmvocations" name="dmvocations[]" id="dmvocations"  > 
                  <?php
                       foreach ($vocations as $vocation) {
                          echo "<option value='" . $vocation['voc_name'] . "'>" . $vocation['voc_name'] . "</option>";
                       }
                   ?>
             </select>
                        </div> 
                </div>  
                <div class="col-xs-12 col-md-1">   
                         <button type='button' class="btnblock btntopgap btnsearchdmmembers">Search</button>   
                </div>  
            </div> 
           </form>
           <div id="interestedmembers"></div>
        </div>
        </div>
    <!--menu49 -->
    <!-- menu50 -->
    <div id="menu50" class="tab-pane">
        <div class="top-head">
                            <div class="col-xs-12 col-sm-8">
                                <h4><i class='fa fa-users'></i> Direct Emailing Requests</h4>
                            </div> 
                            <div class="clearfix"></div>
                        </div>
                           
                        <div class="col-xs-12 col-md-12  ">
             
        <div id="directmailrequests"></div>     
  
             </div>
						 
            </div>
            <!--menu50 --> 


            <!-- menu51 -->
  <div id="menu51" class="tab-pane">
                        <div class="top-head">
                            <div class="col-xs-12 col-sm-8">
                                <h4><i class='fa fa-users'></i> Direct Emailing Requests Sent</h4>
                            </div> 
                            <div class="clearfix"></div>
                        </div>
                           
                        <div class="col-xs-12 col-md-12  ">
             
        <div id="directmailrequestssent"></div>     
  
             </div>
						 
            </div>
            <!--menu51 --> 
 <!-- menu52 -->
    <div id="menu52" class="tab-pane">
                        <div class="top-head">
                            <div class="col-xs-12 col-sm-8">
                                <h4><i class='fa fa-users'></i> Direct Emailing Requests From Other Members</h4>
                            </div> 
                            <div class="clearfix"></div>
                        </div>
                           
                        <div class="col-xs-12 col-md-12  ">
             
        <div id="directmailrequestsrcv"></div>     
  
             </div>
						 
            </div>
            <!--menu52 --> 

			<!-- menu24 -->
			<div id="menu24" class="tab-pane">
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
				<div id="menu25" class="tab-pane">
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
				<div id="menu14" class="tab-pane">
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
				<div id="menu15" class="tab-pane">
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
				<div id="viewpost" class="tab-pane">
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
				<div id="menu10" class="tab-pane fade">
					<div class="top-head">
						<div class="col-xs-12 col-sm-8">
							<h4>Help Instruction</h4>
						</div>
						<div class="clearfix"></div>
					</div>
					<div class="col-xs-12 col-sm-10 col-sm-offset-1 no-padd"> 
					 <div id="helpaccordion"></div>
					</div>
                        </div>
				    <!--Menu10--> 
					
					<!--Menu7-->
				<div id="menu7" class="tab-pane fade">
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
                                            <button class="btnblock send_feedback">Submit</button>
                                        </div>
                                        <div class="col-xs-6 padd-3 text-right"> 
                                        </div>
                                    </div>
                                    <div class="clearfix"></div>
                                </div>
                         </div>
					</div>
				    <!--Menu7-->
                    <div id="menu1" class="tab-pane active">
					  <div class='grid-row no-padd'>
                        <div class="top-head">
                            <div class="col-xs-12 col-sm-8">
                                <h4>Client / User Details</h4>
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
							 <br/><a href="#" data-toggle='modal' data-target='#changepicture' class="btn-primary btn btn-xs changepic_btn" data-id="<?php echo $user_id ?>">
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
                         "<br/>Phone: " . $my_profile[0]['user_phone']. 
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
                            <?php
                            }
                            else 
                            { 
                                
                            ?>
                            <p><br/><strong>Your Public Profile:</strong></p>
							<a href="http://mycity.com/profile/?l=<?php echo  $user_id; ?>">http://mycity.com/profile/?l=<?php echo  $user_id; ?></a>
							<br>
                            <!--input class='form-control' type='text' disabled 
                                value='http://mycity.com/profile/?l=<?php echo  $user_id; ?>'/-->   
 
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
            <div class="panel-body" id="groupnames">
             <?php 
               echo str_replace( ",", ", ",  $my_profile[0]['group_names']) ;
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
<p class='text-center'>
             <i class='fa fa-lock hint'></i> <strong>Our promise to you:</strong> Mycity will never 
             spam your connections or send any invites without your approval. </p>
              
             
	    </div>
    </div>


 	<div class="panel panel-default panelhome-sm">
		<div class="panel-heading">
		 <h4>My Preferences</h4>
		</div>
			 <div class="panel-body pscroll">
             <div id='memberdetails'>
             <?php 
             
             echo "<p><strong>Target Clients:</strong> " . 
             ( $my_profile[0]['target_clients'] =='' ? 'Not Specified':  $my_profile[0]['target_clients'] ) .  "</p>"  . 
             "<p><strong>Target Referral Partners:</strong> " . 
             ( $my_profile[0]['target_referral_partners'] =='' ? 'Not Specified':  $my_profile[0]['target_referral_partners'] ) . "</p>" .
             "<p><strong>Vocation:</strong> " .
             ( $my_profile[0]['vocations'] =='' ? 'Not Specified':  $my_profile[0]['vocations'] ) . "</p>";

            ?></div>
			  
	    </div>
    </div>
<?php
else:
?>
<div class="panel panel-default panelhome">
	 <div class="panel-heading">
		 <h4>Contacts added during last 2 weeks</h4>
	</div>
   <div class="panel-body ">
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
<?php 		 
endif;
?>
</div>
<div class='clearfix'></div>
<div class='col-md-12 col-lg-12'> 
<div class="panel panel-default panelhome">
    <div class="panel-heading">
        <h4>Members You May Be Interested To Contact</h4>
	</div>
    <div class="panel-body ">
            <div id="myCarousel" class="carousel slide"> 
                <div class="carousel-inner">
                    <?php 

                    $totalrows = sizeof(  $directmailsuggest['results'] );

                    if($totalrows <= 4 )
                    {
                        ?>
                        <div class="item active">
                            <div class="row">
                                <?php
                                for ($i=0; $i<4 ; $i++)
                                {
                                    ?>
                                    <div class="col-sm-3">
                                        <div class='member-info'><div class='member-summary'>
                                        <div data-id='<?php echo $directmailsuggest['results'][$i]['id']; ?>'  >
                                    <?php 
                                    echo  "<p><strong>" . $directmailsuggest['results'][$i]['username']  . "</strong><br/>" ;
                                   
                                    echo  $directmailsuggest['results'][$i]['city'] . " - " ;
                                    echo  $directmailsuggest['results'][$i]['zip'] . "<br/> ".$directmailsuggest['results'][$i]['country']  . "</p>" ;
                                    ?>
                                    </div> 
                                </div>
                                    <div class='member-footer'>
                                        <button data-id='<?php echo $directmailsuggest['results'][$i]['id']; ?>' class='btn btn-sm btn-primary btncomposedirectmail'  >Contact Now</button>
                                    </div></div>
                                </div>
                                    <?php 
                                }
                                ?> 
                            </div> 
                        </div>
                        <?php 
                    } 
                    else 
                    if($totalrows <= 8)
                    {
                        ?>
                        <div class="item active">
                            <div class="row">
                                <?php
                                for ($i=0; $i<4 ; $i++)
                                {
                                    ?>
                                    <div class="col-sm-3">
                                    <div class='member-info'><div class='member-summary'>
                                    <div data-id='<?php echo $directmailsuggest['results'][$i]['id']; ?>'  >
                                <?php 
                                echo  "<p><strong>" . $directmailsuggest['results'][$i]['username']  . "</strong><br/>" ;
                               
                                echo  $directmailsuggest['results'][$i]['city'] . " - " ;
                                echo  $directmailsuggest['results'][$i]['zip'] . "<br/> ".$directmailsuggest['results'][$i]['country']  . "</p>" ;
                                ?>
                                </div> 
                            </div>
                                <div class='member-footer'>
                                    <button data-id='<?php echo $directmailsuggest['results'][$i]['id']; ?>' class='btn btn-sm btn-primary btncomposedirectmail'  >Contact Now</button>
                                </div></div>
                            </div>
                                    <?php
                                    
                                }

                                ?> 
                            </div> 
                        </div>
                        <div class="item  ">
                            <div class="row">
                                <?php
                                for ($i= 4; $i < 8 ; $i++)
                                {
                                    ?>
                                     <div class="col-sm-3">
                                     <div class='member-info'><div class='member-summary'>
                                     <div data-id='<?php echo $directmailsuggest['results'][$i]['id']; ?>'  >
                                 <?php 
                                 echo  "<p><strong>" . $directmailsuggest['results'][$i]['username']  . "</strong><br/>" ;
                                
                                 echo  $directmailsuggest['results'][$i]['city'] . " - " ;
                                 echo  $directmailsuggest['results'][$i]['zip'] . "<br/> ".$directmailsuggest['results'][$i]['country']  . "</p>" ;
                                 ?>
                                 </div> 
                             </div>
                                 <div class='member-footer'>
                                     <button data-id='<?php echo $directmailsuggest['results'][$i]['id']; ?>' class='btn btn-sm btn-primary btncomposedirectmail'  >Contact Now</button>
                                 </div></div>
                             </div>
                                    <?php
                                    
                                }

                                ?> 

<div class="col-sm-3">
                                    <div class='member-viewall'> 
                                    <a class='btn btn-lg btn-primary btnviewalldmr'  data-toggle="tab"   href="#menu49"   >View All</a>
                                </div> 
                            </div>


                            </div> 
                        </div>
                        <?php 
                    }  
                    else  
                    {
                        ?>
                        <div class="item active">
                            <div class="row">
                                <?php
                                for ($i=0; $i<4 ; $i++)
                                {
                                    ?>
                                    <div class="col-sm-3">
                                    <div class='member-info'><div class='member-summary'>
                                    <div data-id='<?php echo $directmailsuggest['results'][$i]['id']; ?>'  >
                                <?php 
                                echo  "<p><strong>" . $directmailsuggest['results'][$i]['username']  . "</strong><br/>" ;
                               
                                echo  $directmailsuggest['results'][$i]['city'] . " - " ;
                                echo  $directmailsuggest['results'][$i]['zip'] . "<br/> ".$directmailsuggest['results'][$i]['country']  . "</p>" ;
                                ?>
                                </div> 
                            </div>
                                <div class='member-footer'>
                                    <button data-id='<?php echo $directmailsuggest['results'][$i]['id']; ?>' class='btn btn-sm btn-primary btncomposedirectmail'  >Contact Now</button>
                                </div></div>
                            </div>
                                    <?php
                                    
                                }

                                ?> 
                            </div> 
                        </div>
                        <div class="item  ">
                            <div class="row">
                                <?php
                                for ($i= 4; $i < 8 ; $i++)
                                {
                                    ?>
                                     <div class="col-sm-3">
                                     <div class='member-info'><div class='member-summary'>
                                     <div data-id='<?php echo $directmailsuggest['results'][$i]['id']; ?>'  >
                                 <?php 
                                 echo  "<p><strong>" . $directmailsuggest['results'][$i]['username']  . "</strong><br/>" ;
                                
                                 echo  $directmailsuggest['results'][$i]['city'] . " - " ;
                                 echo  $directmailsuggest['results'][$i]['zip'] . "<br/> ".$directmailsuggest['results'][$i]['country']  . "</p>" ;
                                 ?>
                                 </div> 
                             </div>
                                 <div class='member-footer'>
                                     <button data-id='<?php echo $directmailsuggest['results'][$i]['id']; ?>' class='btn btn-sm btn-primary btncomposedirectmail'  >Contact Now</button>
                                 </div></div>
                             </div>
                                    <?php
                                    
                                }

                                ?> 
                            </div> 
                        </div>

                        <div class="item  ">
                            <div class="row">
                                <?php
                                for ($i= 8; $i < 11 ; $i++)
                                {
                                    ?>
                                     <div class="col-sm-3">
                                     <div class='member-info'><div class='member-summary'>
                                     <div data-id='<?php echo $directmailsuggest['results'][$i]['id']; ?>'  >
                                 <?php 
                                 echo  "<p><strong>" . $directmailsuggest['results'][$i]['username']  . "</strong><br/>" ;
                                
                                 echo  $directmailsuggest['results'][$i]['city'] . " - " ;
                                 echo  $directmailsuggest['results'][$i]['zip'] . "<br/> ".$directmailsuggest['results'][$i]['country']  . "</p>" ;
                                 ?>
                                 </div> 
                             </div>
                                 <div class='member-footer'>
                                     <button  data-id='<?php echo $directmailsuggest['results'][$i]['id']; ?>' class='btn btn-sm btn-primary btncomposedirectmail'  >Contact Now</button>
                                 </div></div>
                             </div>
                                    <?php
                                    
                                }

                                ?> 

<div class="col-sm-3">
                                    <div class='member-viewall'> 
                                    <a class='btn btn-lg btn-primary btnviewalldmr'  data-toggle="tab"   href="#menu49"   >View All</a>
                                </div> 
                            </div>

                            </div> 
                        </div>

                        <?php 
                    } 
                    ?> 
                     
                </div>
                <a class="left carousel-control" href="#myCarousel" data-slide="prev">‹</a> 
                <a class="right carousel-control" href="#myCarousel" data-slide="next">›</a>
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
            </div> 
          </div>
        </div>
</div> 
</div>
 
                  <div class="col-lg-4 col-md-6">
                    <div class="panel panel-primary">
                        <div class="panel-heading">
                            <div class="row">
                                <div class="col-xs-3">
                                    <i class="fa fa-link fa-5x"></i>
                                </div>
                                <div class="col-xs-9 text-right">
                                    <div class="huge"><span data-value='<?php echo $mytotalref ;?>'  class="count"></span></div>
                                    <div>Total Referrals</div>
                                </div>
                            </div>
                        </div>
                        <a data-toggle="tab" href="#" class="ref_wizard" aria-expanded="true">
                            <div class="panel-footer">
                                <span class="pull-left">Send Referral</span>
                                <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                                <div class="clearfix"></div>
                            </div>
                        </a>
                    </div> 
                </div>
 

<div class="col-lg-4 col-md-6">
                    <div class="panel panel-red">
                        <div class="panel-heading">
                            <div class="row">
                                <div class="col-xs-3">
                                    <i class="fa fa-address-card fa-5x"></i>
                                </div>
                                <div class="col-xs-9 text-right">
                                    <div class="huge"><span data-value='<?php echo getMyKnows($link, $user_id); ?>'  class="count"></span></div>
                                    <div>Total Knows</div>
                                </div>
                            </div>
                        </div>
                        <a  data-toggle="tab" href="#menu2"   >
                            <div class="panel-footer">
                                <span class="pull-left">Add New Know</span>
                                <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                                <div class="clearfix"></div>
                            </div>
                        </a>
                    </div>
                </div>

 <?php  
 if ($_user_role == 'admin') :

 	$resconnections = $link->query(" select u.username, u.user_email, u.user_phone , user_id, count(*) as knowcount 
 		from user_people as p  inner join mc_user as u 
 		where u.id<>'1' and u.id = p.user_id group by user_id ");
 	$totalconnections=0 ;
 	if($resconnections->num_rows > 0)
 	{
 		while($row =$resconnections->fetch_array() )
 		$totalconnections +=  $row['knowcount'] ;
 	} 

?>
<div class="col-lg-4 col-md-6">
                    <div class="panel panel-green">
                        <div class="panel-heading">
                            <div class="row">
                                <div class="col-xs-3">
                                    <i class="fa fa-handshake-o fa-5x"></i>
                                </div>
                                <div class="col-xs-9 text-right">
                                    <div class="huge"><span data-value='<?php echo  $resconnections->num_rows ; ?>'  class="count"></span></div>
                                    <div>Total Partners</div>
                                </div>
                            </div>
                        </div>
                        <a  data-toggle="tab" href="#menu37" data-page='1' data-role='1'  class='btnknowreport'  >
                            <div class="panel-footer">
                                <span class="pull-left">These partners have added <?php echo $totalconnections; ?> knows</span>
                                 <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                                <div class="clearfix"></div>
                            </div>
                         </a>
                    </div>
                </div>
<?php
else: 
	$user = $link->query("SELECT * FROM user_details WHERE user_id = '$user_id'");
	$user = $user->fetch_array();
	$groups = explode(",", $user['groups']); 
	$whereGroup = "(FIND_IN_SET('".implode("', `groups`) OR FIND_IN_SET('", $groups)."', `groups`))";  
	$query = "SELECT * FROM user_details WHERE user_id <> '$user_id' and " . $whereGroup  ; 
 	  
 	$partners = $link->query( $query ); 
 	$totalpartners =  $partners->num_rows;
 	$totalconnections=0;
 	if($partners->num_rows > 0)
 	{
 		$subquery = "select user_id from user_details where user_id <> '$user_id' and user_id <> '1' and " . $whereGroup  ; 
 		$knowcount = $link->query(  "select sum(knowcount) as totalknow from  (select   count(*)  as knowcount 
        from user_people  as p inner join mc_user as u on p.user_id=u.id 
        where u.id in (" . $subquery  . ") group by p.user_id) as table1" );
        $totalconnections = $knowcount->fetch_array()['totalknow'];
     }
?>
<div class="col-lg-4 col-md-6">
                    <div class="panel panel-green">
                        <div class="panel-heading">
                            <div class="row">
                                <div class="col-xs-3">
                                    <i class="fa fa-star fa-5x"></i>
                                </div>
                                <div class="col-xs-9 text-right">
                                    <div class="huge"><span data-value='<?php echo getLoyaltyPoint($link, $user_id);  ?>' class="count"></span></div>
                                    <div>Reward Points</div>
                                </div>
                            </div>
                        </div>
                       <div class="panel-footer">
                                <span class="pull-left">Overall reward score so far.</span> 
                                <div class="clearfix"></div>
                            </div>
                    </div>
                </div> 	 
						  
                <div class="col-lg-4 col-md-6">
                    <div class="panel panel-primary">
                        <div class="panel-heading">
                            <div class="row">
                                <div class="col-xs-3">
                                    <i class="fa fa-handshake-o fa-5x"></i>
                                </div>
                                <div class="col-xs-9 text-right">
                                    <div class="huge"><span data-value='<?php echo   $totalpartners ; ?>'  class="count"></span></div>
                                    <div>Total Partners</div>
                                </div>
                            </div>
                        </div>
                        <a  data-toggle="tab" href="#menu37" data-role='0' data-page='1' class='btnknowreport'  >
                            <div class="panel-footer">
                                <span class="pull-left">These partners have added <?php echo $totalconnections; ?> knows</span>
                                 <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                                <div class="clearfix"></div>
                            </div>
                         </a>
                    </div>
   </div> 
<?php
endif;
 ?> 
 </div>        
		  <?php  if ($_user_role == 'admin') {  
							$signupcount = $link->query(" SELECT COUNT(*) as signupcnt FROM  mc_user WHERE date(createdOn) ='" . date('Y-m-d') .  "' ");
							$totalsignup = $signupcount->fetch_array();
							
							$emailsentlog = $link->query("SELECT COUNT(*) as emailcnt FROM referralsuggestions where emailstatus='1' AND date(senton) ='" . date('Y-m-d') .  "'");
							$emailsentlog = $emailsentlog->fetch_array();
							
							$totalrefrow = $link->query("SELECT count(*) as cnt FROM referralsuggestions where isdeleted <> '1'");
							$totalref = $totalrefrow->fetch_array() ;
   ?> 

			   <div class="col-lg-4 col-md-6">
                    <div class="panel panel-primary">
                        <div class="panel-heading">
                            <div class="row">
                                <div class="col-xs-3">
                                    <i class="fa fa-user-plus fa-5x"></i>
                                </div>
                                <div class="col-xs-9 text-right">
                                    <div class="huge"><span data-value='<?php echo $totalsignup['signupcnt'];?>' class="count"></span></div>
                                    <div>New Member(s)</div>
                                </div>
                            </div>
                        </div>
                         
                            <div class="panel-footer">
                                <span class="pull-left">Total New signup Today</span> 
                                <div class="clearfix"></div>
                            </div>
                         
                    </div>
                </div>

                <div class="col-lg-4 col-md-6">
                    <div class="panel panel-red">
                        <div class="panel-heading">
                            <div class="row">
                                <div class="col-xs-3">
                                    <i class="fa fa-envelope fa-5x"></i>
                                </div>
                                <div class="col-xs-9 text-right">
                                    <div class="huge"><span data-value='<?php echo $emailsentlog['emailcnt'];?>' class="count"></span></div>
                                    <div>Total Referral Mail(s)</div>
                                </div>
                            </div>
                        </div>
                         
                            <div class="panel-footer">
                                <span class="pull-left">Total Referral Mails Sent Today</span> 
                                <div class="clearfix"></div>
                            </div>
                        
                    </div>
                </div>

 			<div class="col-lg-4 col-md-6">
                    <div class="panel panel-green">
                        <div class="panel-heading">
                            <div class="row">
                                <div class="col-xs-3">
                                    <i class="fa fa-check-circle fa-5x"></i>
                                </div>
                                <div class="col-xs-9 text-right">
                                    <div class="huge"><span data-value='<?php echo $totalref['cnt']; ?>' class="count"></span></div>
                                    <div>Referrals Matched</div>
                                </div>
                            </div>
                        </div>
                        
                            <div class="panel-footer">
                                <span class="pull-left">Total Referrals Matched</span> 
                                <div class="clearfix"></div>
                            </div>
                        
                    </div>
                </div>

 <?php } ?>  
                    </div>
                    <div id="menu2" class="tab-pane fade">
                        <div class="top-head">
                            <div class="col-xs-12 col-sm-8">
                                <h4>ENTER PEOPLE YOU KNOW DETAILS 
								<a href="<?php echo $help_data_buttons[7]['helpvideo']; ?>" target="_blank" ><i class='glyphicon glyphicon-arrow-right' ></i><span style="color:red;"> Help</span></a>
								</h4>
                            </div>
                            <div class="clearfix"></div>
                        </div>
                        <div class="col-xs-12 col-sm-6 padd-5"> 
                            <div class="col-xs-12"><label class="custom-label">Name:</label></div>
                            <div class="col-xs-12"> 
								<input type="text" class="form-control client_name" name="e_name" required="">
							</div>
                        </div>
                        <div class="col-xs-12 col-sm-6 padd-5">
                            <div class="col-sm-12 col-xs-12"><label class="custom-label">Vocation(s):</label></div>
                            <div class="col-sm-12 col-xs-12">
                           
                                <select data-placeholder='Choose vocations ...' multiple class="form-control client_pro" name="e_profession[]" id="e_prof"  > 
                                    <?php
                                    
                                    foreach ($vocations as $vocation) {
                                        echo "<option value='" . $vocation['voc_name'] . "'>" . $vocation['voc_name'] . "</option>";
                                    }
                                    ?>
                                </select>
                                <small class="pull-right">(Enter comma seperated)</small>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <div class="col-xs-12 col-sm-6 padd-5">
                            <div class="col-sm-12 col-xs-12"><label class="custom-label">Phone:</label></div>
                            <div class="col-sm-12 col-xs-12">
                                <input type="text" class="form-control client_ph" name="e_phone" required="">
                            </div>
                        </div> 
                        <div class="col-xs-12 col-sm-6 padd-5">
                            <div class="col-sm-12 col-xs-12"><label class="custom-label">Email:</label></div>
                            <div class="col-sm-12 col-xs-12">
                                <input type="text" class="form-control client_email newcontactemail" name="e_email" required="">
                            </div>
                        </div> 
                        <div class="col-xs-12 col-sm-6 padd-5">
                            <div class="col-sm-12 col-xs-12">
								<label class="custom-label">Lifestyle:<br></label>
                            </div>
                            <div class="col-sm-12 col-xs-12">
                                <select data-placeholder='Specify lifestyles ...'  multiple  name="e_lifestyle" class="form-control chosen-select  client_lifestyle" id="">
                                <?php
                                foreach ($lifestyles as $lifestyle) { 
                                    echo "<option value='" . $lifestyle['ls_name'] . "'>" . $lifestyle['ls_name'] . "</option>";
                                }
                                    ?>
								</select>								
                            </div> 
							<div class="col-sm-12 col-xs-12">
								<label class="custom-label">Location(s):<br></label>
                            </div>
                            <div class="col-sm-12 col-xs-12">
                                <input type="text" name="e_location" class="form-control client_location" id="">
                                <small class="pull-right">(Enter comma separated)</small>
                            </div>
							<div class="col-sm-12 col-xs-12">
								<label class="custom-label">Zip:<br></label>
                            </div>
                            <div class="col-sm-12 col-xs-12">
                                <input type="text" name="e_zip" class="form-control client_zip" id=""> 
                            </div>
							<div class="col-xs-12 col-sm-12 padd-5">
							<div class="col-sm-12 col-xs-12"><label class="custom-label">Note(s):</label></div>
                            <div class="col-sm-12 col-xs-12">
								<input type="text" class="form-control client_note" name="e_note" required="">
								<small class="pull-right">(Enter comma separated)</small>
                            </div>
						  </div>
						<div class="col-sm-12 col-xs-12 <?php echo $hideClass; ?> ">
							<label class="custom-label">Groups:<br></label>
						</div>
						<div class="col-sm-12 col-xs-12 <?php echo $hideClass; ?> ">
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
                                        echo "<option value='" . $item['id'] . "' " . $sel . $dis . ">" . $item['grp_name'] . "</option>";
                                    }
                                    ?>
							</select>
                        </div>
                    </div>
					<?php
                        //if ($_user_role == 'admin' OR $user_pkg != 'free') {
                        echo '<div class="col-xs-12 col-sm-6  padd-5">';
                        $i = 1;
						$textquestion ='';
                        foreach ($ques_data as $item) {
                            $name = "rating0" . $i;
                            $q_id = $item['id'];
                            $question = $item['question'];
                            $q_type = $item['question_type'];
							if($q_type == "rating"):
							 
                            echo "
                                <div class='col-xs-12 col-sm-12 padd-5 pad-bor'>
                                <div class='col-sm-6 padd-5 col-xs-12'><label class='custom-label'>$question</label></div>
                                <div class='col-sm-6 col-xs-12 padd-5'>
                                <span class='starRating main user_ques_main' data-ques='$q_id'>";
                                  echo "<input id='rating01$i' type='radio' class='user_ques' name='$name' value='5' checked><label for='rating01$i'><span></span></label><label for='rating01$i'>5</label>
                                        <input id='rating02$i' type='radio' class='user_ques' name='$name' value='4'><label for='rating02$i'><span></span></label>
                                        <label for='rating02$i'>4</label>
                                        <input id='rating03$i' type='radio'  class='user_ques' name='$name' value='3'><label for='rating03$i'><span></span></label>
                                        <label for='rating03$i'>3</label>
                                        <input id='rating04$i' type='radio'  class='user_ques' name='$name' value='2'><label for='rating04$i'><span></span></label>
                                        <label for='rating04$i'>2</label>
                                        <input id='rating05$i' type='radio' class='user_ques' name='$name' value='1'><label for='rating05$i'><span></span></label>
                                        <label for='rating05$i'>1</label>"; 
                                echo "</span>
                                </div>
                            </div>
                                </span>";
							else: 
								$textquestion = "<div class='col-xs-12 col-sm-12  padd-5'>
								 <div class=col-sm-12 col-xs-12>
									<label class='custom-label'>$question</label> 
								</div>
								<div class=col-sm-12 col-xs-12> 
                                <span class='starRating main user_ques_main' data-ques='$q_id'>";
								 
								$textquestion .= 
								"<select id='answer$q_id' data-ques='$q_id'  name='$name' 
								data-placeholder='Choose vocations ...' class='chosen-select user_ques_text_add' multiple  >
								<option value=''></option>";
								 
								foreach($vocations as $vocitem)
								{
									$textquestion .= "<option value='" .$vocitem['voc_name']  . "'>" . $vocitem['voc_name'] . "</option>";
								}
								$textquestion .= "</select>
							<label for='rating01$i'><span></span></label> 
                            </span></div></div>";
							endif;
                            $i++;
                        }
                        echo '</div>';
                        //}
                    echo $textquestion;    
						?> <div class="col-xs-12 col-sm-12 padd-5">
                        <div class="col-sm-12 col-xs-12"><label class="custom-label">Tags:</label></div>
                        <div class="col-sm-12 col-xs-12"> 
                            <select data-placeholder='Specify Tags ...'  multiple  name="knowtags"  class="form-control chosen-select  client_tags" id=""> 
                            <?php
                                foreach ($alltags as $tag)
                                {
                                    echo "<option value='" . $tag['tagname'] . "'>" . $tag['tagname'] . "</option>";
                                }
                            ?>
                            </select> 
                        </div>
                    </div>  
				<div class="col-sm-12">
					<div class="col-sm-11 col-xs-12 "> 
					<input type="button" value="Submit" class="btnblock pull-right addnewknow"></div>
				</div>
				<?php // people you know details ?>
				<div class="col-xs-12 people-know">
				<hr/>
				</div>
				<div class="col-xs-12 people-know">
                <div class="col-xs-2">
                <input type="text"  placeholder="Specify Name" class="form-control srchRefName">
            </div>
            <div class="col-xs-2">
                <input type="text"  placeholder="Entry Date" class="form-control srchentryDate">
            </div>
            <div class="col-xs-2">
                <input type="text"  placeholder="Phone number" class="form-control srchPhone">
            </div>

            <div class="col-xs-3"> 
                <select data-placeholder="Specify Cities"  id="filtercity" class='chosen-select user_ques_text_add' multiple > 
                    <?php
                       
                        foreach ($cities as $city)
                        {
                            echo "<option value='" . $city['name'] . "'>" . $city['name'] . "</option>";
                        }
                    ?>
                </select> 
            </div> 
            <div class="col-xs-3">
                <input type="text"  placeholder="Specify Zip Code" class="form-control srchZipCode">
            </div>
				</div>
				<div class="col-xs-12 people-know"> 
                <div class="col-xs-3">
						<select data-placeholder="Select Tags" id="filterTags" class='chosen-select srchTags' multiple >
							<?php
								foreach ($alltags as $tag)
								{
									echo "<option value='" . $tag['tagname'] . "'>" . $tag['tagname'] . "</option>";
                                }
							?>
						</select>
					</div> 

					<div class="col-xs-3"> 
						<select data-placeholder="Select Lifestyle" id="filterLifestyle" class='chosen-select user_ques_text_add' multiple >
							 <?php
								foreach ($lifestyles as $lifestyle)
								{
									echo "<option value='" . $lifestyle['ls_name'] . "'>" . $lifestyle['ls_name'] . "</option>";
                                }
							?>
						</select>
					</div>
					<div class="col-xs-4">
                    <select data-placeholder="Select Vocations" id="locateVoc" class='chosen-select user_ques_text_add' multiple  >
						 
							<?php
								foreach ($vocations as $vocation)
								{
									echo "<option value='" . $vocation['voc_name'] . "'>" . $vocation['voc_name'] . "</option>";
                                }
							?>
						</select>
					</div>
					<div class="col-xs-2"><button class="btnblock srchRef">Search</button></div>
				</div>	 	
				<div class="col-xs-12 people-know" id='myknows'>
					<div class="col-xs-12">
						<h4><?php echo $text ?></h4>
						</div>
						<div class="col-xs-12" style="overflow-x: auto;">
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
			   <a href="<?php echo $help_data_buttons[8]['helpvideo']; ?>" target="_blank" ><i class='glyphicon glyphicon-arrow-right' ></i><span style="color:red;"> Help</span></a>
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

                    <div id="menu3" class="tab-pane  fade">
                        <div class="top-head">
                            <div class="col-xs-12 col-sm-8">
                                <h4>SEARCHING</h4>
                            </div>
                            <div class="clearfix"></div>
                        </div>
                        <div class="clearfix"></div> 
                        <div class="col-xs-12 col-sm-10 col-sm-offset-1">
                            <div class="search-loc">
                                <div class="col-sm-5 col-xs-12 padd-8">
                                    <label for="vocSrch">Search by Vocation:</label>
                                    <!-- <input type="text" id="vocSrch" class="form-control"> -->
                                    <select id="vocSrch" class="dropdown" multiple>
                                        <option value="">-vocation-</option>
                                        <?php
										foreach ($vocations as $vocation)
										{
											echo "<option value='" . $vocation['voc_name'] . "'>" . $vocation['voc_name'] . "</option>";
										}
                                        ?>
                                    </select>
									<small class="pull-right">(Hold Ctrl to Select Multiple)</small>
                                </div>
                                <div class="col-sm-5 col-xs-12 padd-8">
                                    <label for="locSrch">Search by location:</label>
                                    <input type="text" id="locSrch" class="form-control">
								</div>
									<div class="col-sm-2 col-xs-12 padd-8 text-center"><br>
										<input type="button" style="width: 100%;" value="SEARCH" class="btnblock srchPeople">
									</div>
                                <div class="clearfix"></div>
                            </div>
                        </div>
                        <div class="col-xs-12 col-sm-12 padd-5">
                            <hr/>
                        </div>
                        <div class="col-xs-12 col-sm-12 srdDtls" style="display: none">
                            <h4>SEARCH RESULTS FOUND</h4>
                            <div class="table-responsive">
                                <table class="table ">
                                    <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Phone</th>
                                        <th>Knows</th>
                                        <th>LEAVE A MESSAGE</th>
                                    </tr>
                                    </thead>
                                    <tbody id="srchrslts"></tbody>
                                </table>
                            </div>
                        </div>
                    </div> 
					<div id="menu27" class="tab-pane fade">
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
										echo "<option value='" . $group['id'] . "'>" . $group['grp_name'] . "</option>";
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

					<div id="menu40" class="tab-pane fade">
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
										echo "<option value='" . $vocation['voc_name'] . "'>" . $vocation['voc_name'] . "</option>";
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
						<div id="menu9" class="tab-pane fade">
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
												echo "<option value='" . $vocation['voc_name'] . "'>" . $vocation['voc_name'] . "</option>";
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
                    <?php if ($_user_role == 'admin') { ?>
					
               
				<div id="menu28" class="tab-pane fade"> 
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
										<select class="form-control"  name='templatetype' id='templatetype' >
                                            <option value='0'>Trigger Email Template</option>
                                            <option value='1'>Referral Introduction Email</option>
											 <option value='2'>LinkedIn Invitation Email</option>
											<option value='3'>Testimonial Videos</option>
                                            <option value='4'>Invite people to Other Group Meetings</option>
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
				}?>	
				<!--Menu28-->
   </div>

   <div id="menu29" class="tab-pane fade"> 
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
							
							<!--<div class="col-xs-12 col-sm-12  ">	
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
	  <div id="menu8" class="tab-pane fade">
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
				<div id="menu30" class="tab-pane fade">
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
					<div id="menu4" class="tab-pane fade">
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
                        <div id="menu5" class="tab-pane fade">
                            <div class="top-head">
                                <div class="col-xs-12 col-sm-8">
                                    <h4>GROUPS</h4>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                            <div class="col-xs-12 col-sm-12 col-sm-offset-0 no-padd  ">
                                <div class="search-loc" style="background: none;border: 1px solid #dbdbdb">
                                    <div class="col-sm-12 col-xs-12 padd-8">
                                        <h4><b>Groups You Have</b></h4>
                                    </div>
                                    <div class="col-sm-5 col-xs-12 padd-8">
                                  
                                        <select class="form-control userClientGrps">
                                            <option value="null">-select group-</option>
                                            <?php
                                            foreach ($getGroups as $item) {
                                                echo "<option value='" . $item['id'] . "'>" . $item['grp_name'] . "</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    <div class="col-sm-5 col-xs-12 padd-8">
                                        <input type="text" class="form-control newGrpVal">
                                    </div>
                                    <div class="col-sm-2 col-xs-12 padd-8 text-center">
                                        <button style="margin-top: 0 !important" class="btnblock updGroup">UPDATE GROUP
                                        </button>
                                        <button style="margin-top: 0 !important" class="btnblock delGroup">DELETE GROUP
                                        </button>
                                    </div>
                                    <div class="clearfix"></div>
                                </div>
                            </div>
                            <div class="col-sm-12 col-xs-12 padd-8 text-center">
                                <br>
                            </div>
                            <div class="col-xs-12 col-sm-10 col-sm-offset-1 no-padd  ">
                                <div class="search-loc" style="background: none;border: 1px solid #dbdbdb">
                                    <div class="col-sm-12 col-xs-12 padd-8">
                                        <h4><b>Add New Groups</b></h4>
                                    </div>
                                    <div class="col-sm-5 col-xs-12 padd-8">
                                        <input type="text" placeholder="Add New Group" class="form-control groupName">
                                    </div>
                                    <div class="col-sm-2 col-xs-12 padd-8 text-center">
                                        <button style="margin-top: 0 !important" class="btnblock addNewGroup">ADD GROUP
                                        </button>
                                    </div>
                                    <div class="clearfix"></div>
                                </div>
                            </div>
                        </div>
                        <div id="menu6" class="tab-pane fade">
                            <div class="top-head">
                                <div class="col-xs-12 col-sm-8">
                                    <h4>VOCATIONS</h4>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                            <div class="col-xs-12 col-sm-12 col-sm-offset-0 no-padd">
                                <div class="search-loc" style="background: none;border: 1px solid #dbdbdb">
                                    <div class="col-sm-12 col-xs-12 padd-8">
                                        <h4><b>Vocations You Have</b></h4>
                                    </div>
                                    <div class="col-sm-5 col-xs-12 padd-8">
                                        <select class="form-control fetVocations">
                                            <option value="null">-select-</option>
                                            <?php
                                            foreach ($vocations as $vocation) {
                                                echo "<option value='" . $vocation['id'] . "'>" . $vocation['voc_name'] . "</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    <div class="col-sm-5 col-xs-12 padd-8">
                                        <input type="text" class="form-control editVocation">
                                    </div>
                                    <div class="col-sm-2 col-xs-12 padd-8 text-center">
                                        <button style="margin-top: 0 !important" class="btnblock updVoc">UPDATE</button>
                                        <button style="margin-top: 0 !important" class="btnblock delVoc">DELETE</button>
                                    </div>
                                    <div class="clearfix"></div>
                                </div>
                            </div>
                            <div class="col-sm-12 col-xs-12 padd-8 text-center"><br></div>
                            <div class="col-xs-12 col-sm-10 col-sm-offset-1 no-padd  ">
                                <div class="search-loc" style="background: none;border: 1px solid #dbdbdb">
                                    <div class="col-sm-12 col-xs-12 padd-8">
                                        <h4><b>Add New Vocation</b></h4>
                                    </div>
                                    <div class="col-sm-5 col-xs-12 padd-8">
                                        <input type="text" placeholder="Add New Vocation"
                                               class="form-control vocationName">
                                    </div>
                                    <div class="col-sm-2 col-xs-12 padd-8 text-center">
                                        <button style="margin-top: 0 !important" class="btnblock addNewVoc">ADD NEW
                                        </button>
                                    </div>
                                    <div class="clearfix"></div>
                                </div>
                            </div>
                        </div> 
						
						<div id="menu26" class="tab-pane fade">
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
                                                echo "<option value='" . $lifestyle['id'] . "'>" . $lifestyle['ls_name'] . "</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    <div class="col-sm-5 col-xs-12 padd-8">
                                        <input type="text" data-lifestyle='' class="form-control editLifestyle">
                                    </div>
                                    <div class="col-sm-2 col-xs-12 padd-8 text-center">
                                        <button style="margin-top: 0 !important" class="btnblock updLifestyle">UPDATE</button> 
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
                                        <button style="margin-top: 0 !important" class="btnblock addNewLifestyle">ADD NEW
                                        </button>
                                    </div>
                                    <div class="clearfix"></div>
                                </div>
                            </div>
                        </div>  
                        <div id="pagepackages" class="tab-pane fade">
                            <div  class="pagesData">
                                <div  id="pages_edit" >
                                    <div class="top-head">
                                        <div class="col-xs-12 col-sm-8">
                                            <h4>Services & Pricing</h4>
                                        </div>
                                        <div class="clearfix"></div>
                                    </div>
                                    <div class="col-xs-12 col-sm-10 col-sm-offset-1 no-padd">
                                        <div class="content-inner">
                                            <div class=" col-sm-6 col-xs-12 packageDetails">
                                                <button class="btnblock" data-toggle="modal" data-target="#edit_package">Add Packages</button>
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
                                                <?php } ?>
                                            </div>
                                            <div class="clearfix"></div>
                                        </div>
                                    </div>
                                </div>
						</div>
					</div>
						<div id="pageaboutus" class="tab-pane fade">
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

                   <div  id="pageblog" class="tab-pane fade">
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
					 
					<div   id='pagetagline' class="tab-pane fade">
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
										<input type="button" class="btnblock saveTagline" value="Save now">
										</div>
									</div>
                                </div>
                     </div> 
 
				<div  id="blogmanage" class="tab-pane fade">
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
				<div  id="addpost" class="tab-pane fade">
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
				<div  id="editpost" class="tab-pane fade">
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
</body>

<?php 
 include_once('template/footerjs.php');
?>
  
</html>
