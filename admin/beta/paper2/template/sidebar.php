	<div class="sidebar-wrapper">
             
    
            <ul class="nav">

            <li class="active">
                    <a href="dashboard.html">
                        <i class="ti-panel"></i>
                        <p><img src="<?php echo $siteurl. $user_picutre;?>" alt="" class="img-circle" width="40"> <?php echo $username ?></p>
                    </a>
                </li>
  
                <li  >
                <a class='loadprofile' data-toggle="tab" href="#menu1"><i class="fa fa-user"></i>Client / User</a> 
                <a href="<?php echo $help_data_buttons[0]['helpvideo']; ?>" target="_blank" ><i id='hint-profile1' class='glyphicon glyphicon-arrow-right' ></i><span style="color:red;">Help</span></a>
                
                     
                </li>
                 
                <li data-toggle="collapse"  data-target="#peopleknow" class="subnavctrl collapsed active close_drop">
					<a href='#' ><i class="fa fa-desktop"></i>People you know <span class="arrow"></span></a>
				</li> 
				<ul class="sub-menu collapse" id='peopleknow'>
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
        <?php if($_user_role != 'admin' && $user_pkg == "Gold" ) { ?>
				 <li class="close_drop"><a data-toggle="tab" class='getmypartners' href="#menu13"> <i class="fa fa-users"></i>Your Partners</a></li>
				 <li class="close_drop"><a data-toggle="tab" class='getratedpartners' href="#menu16"> <i class="fa fa-users"></i>Highest Rated Partners</a></li>
                
                 <li data-toggle="collapse"  data-target="#directmail" class="subnavctrl collapsed active close_drop">
					<a href='#' ><i class="fa fa-envelope"></i>Direct Mail Service<span class="arrow"></span></a>
				</li> 
				<ul class="sub-menu collapse" id='directmail'>
                   
                <li class="close_drop"><a data-toggle="tab" class='btnviewalldmr' href="#menu49"> <i class="fa fa-users"></i> Search Members</a></li>
                    <li class="close_drop"><a data-toggle="tab" class='btnviewdmrequests' href="#menu50"> <i class="fa fa-envelope"></i> Requests Approved</a></li>
                    <li class="close_drop"><a data-toggle="tab" class='btnviewdmrequestssent' href="#menu51"> <i class="fa fa-envelope"></i> Request Sent</a></li> 
                    <li class="close_drop"><a data-toggle="tab" class='btnviewdmrequestsrcv' href="#menu52"> <i class="fa fa-envelope"></i> Request from other member</a></li>
             


                   </ul> 
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
			<li data-toggle="collapse"  data-target="#reminders" class="subnavctrl collapsed active close_drop">
					<a href="#" ><i class="fa fa-edit"></i>Reminders  <span class="arrow"></span></a> 
			</li> 
			    <ul class="sub-menu collapse"  id='reminders'>
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
                <li>
                    <a href="notifications.html">
                        <i class="ti-bell"></i>
                        <p>Notifications</p>
                    </a>
                </li>
				 
            </ul>
 </div> 