<?php

$memberinfo = $member->row();

$programstatus = '';
$participantid = $memberinfo->id;
$_user_role = $memberinfo->user_role;
$user_id = $memberinfo->id;

$sql_query = "select count(*) as reccnt from mc_program_client where client_id='" . $user_id . "'";
$rst = $this->db->query($sql_query);
$pp_count = 0;
if ($rst->num_rows() > 0) {
    $pp_count = $rst->row()->reccnt;
}

?>
<div class="container-fluid">
    <div class='row'>
        <div class='col-md-3 sidepane'>
            <div class="panel panel-default" style='height: 250px'>
                <div class="panel-body">
                    <div id="tbc" class="carousel slide infoalertzone" data-ride="carousel">

                        <div class="carousel-inner " role="listbox">
                            <?php
                            if ($_user_role == 'user' && $pp_count == 0) {
                                ?>
                                <ol class="carousel-indicators">
                                    <li data-target="#tbc" data-slide-to="0" class="active"></li>
                                    <li data-target="#tbc" data-slide-to="1"></li>
                                    <li data-target="#tbc" data-slide-to="2"></li>
                                </ol>
                                <div class="item carousel-entice active">
                                    <div class=" pad10 margb3 text-center">
                                        <h4>Three Touch Program</h4>
                                        <p>Convert connection into a relationship over 30 day period
                                            with our unique 3 Touch Program.
                                        </p>
                                        <?php echo form_open(); ?>
                                        <p>
                                            <button type='submit' name='btn_join_prg' value='join_program' class="btn btn-orange join3tprogram"><i class="fa fa-link"></i>Join Program Now</button>
                                            <input type='hidden' name='ppid' value='<?php echo $participantid; ?>'/>
                                        </p>
                                        <?php echo form_close(); ?>
                                    </div>
                                </div>
                                <div class="item  ">
                                    <div class=" pad10 margb3 text-center">
                                        <h4>We've selected a few experiences you might like. Get promoted for free.</h4>
                                        <p><i class='fa fa-chevron-circle-right'></i> Rating someone by who rated them. <i class='fa fa-chevron-circle-right'></i> Giving a referral to someone.</p>
                                    </div>
                                </div>
                                <div class="item">
                                    <div class=" pad10 margb3 text-center">
                                        <h4>Introduce highly rated people to people you know in their area.</h4>
                                        <br/>
                                        <p><a href="<?php echo $base; ?>my-network/add/" class="btn btn-primary"><i class="fa fa-user-plus"></i>Click here to add your know!</a></p>
                                        <br/>
                                        <br/>
                                    </div>
                                </div>
                                <?php
                            } else {
                                ?>
                                <ol class="carousel-indicators">
                                    <li data-target="#tbc" data-slide-to="0" class="active"></li>
                                    <li data-target="#tbc" data-slide-to="1"></li>
                                </ol>
                                <div class="item active ">
                                    <div class=" pad10 margb3 text-center">
                                        <h4>We've selected a few experiences you might like. Get promoted for free.</h4>
                                        <p><i class='fa fa-chevron-circle-right'></i> Rating someone by who rated them. <i class='fa fa-chevron-circle-right'></i> Giving a referral to someone.</p>
                                    </div>
                                </div>
                                <div class="item">
                                    <div class=" pad10 margb3 text-center">
                                        <h4>Introduce highly rated people to people you know in their area.</h4>
                                        <br/>
                                        <p><a href="<?php echo $base; ?>my-network/add/" class="btn btn-primary" class="btn btn-primary"><i class="fa fa-user-plus"></i>Click here to add your know!</a>
                                        </p>
                                        <br/>
                                        <br/>
                                    </div>
                                </div>
                                <?php
                            }
                            ?>
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


            <?php if ($this->session->role == 'admin') { ?>
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4>Client Tracking Program</h4>
                    </div>
                    <div class="panel-body panel-menu">
                        <ul id='reminders'>
                            <li class="close_drop">
                                <a href='<?php echo $base; ?>dashboard/setup-email'><i class="fa fa-bell-o"></i> Setup Email</a>
                            </li>
                            <li class="close_drop">
                                <a href='<?php echo $base; ?>dashboard/client-tracking'><i class="fa fa-bell-o"></i> Client Tracking</a>
                            </li>
                            <li class="close_drop">
                                <a href='<?php echo $base; ?>dashboard/clients-voice-mails'><i class="fa fa-bell-o"></i> Voice Mails Logs</a>
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

                        <?php if ($_user_role == 'admin') { ?>
                            <li><a href="<?php echo $base; ?>member" class='showknowentryform loadknowsormembers'><i class="fa fa-user-plus"></i>Add/Update Member</a></li>
                            <li><a href='<?php echo $base; ?>dashboard/top-rated-knows'><i class="fa fa-user-plus"></i>View Top Rated Know</a></li>
                            <li><a data-toggle="tab" href="#menu56"><i class="fa fa-user-plus"></i>Add Business Card</a></li>

                            <li><a href="<?php echo BASE_URL; ?>/invite-knows"><i class="fa fa-bar-chart"></i> Generate Join mycity Landing Page</a></li>

                        <?php } else {
                            ?>
                            <li><a href="<?php echo $base; ?>my-network"><i class="fa fa-user-plus"></i>Add/Update People</a></li>
                            <li><a href='<?php echo $base; ?>dashboard/top-rated-knows'><i class="fa fa-user-plus"></i>View Top Rated Know</a></li>
                            <?php
                        } ?>
                        <li class="close_drop"><a href="<?php echo $base; ?>dashboard/referrals" id='hint-addreferral'> <i class="fa fa-users"></i>Introduction/Referral</a>
                            <a href="<?php echo $help_data_buttons[1]['helpvideo']; ?>" target="_blank"><i class='fa fa-arrow-right'></i><span style="color:red;"> Help</span></a>
                        </li>
                        <li><a href="<?php echo $base; ?>my-network/wizard"><i class="fa fa-support"></i> Referral Wizard</a>
                            <a href="<?php echo $help_data_buttons[9]['helpvideo']; ?>" target="_blank"><i class='fa fa-arrow-right'></i><span style="color:red;"> Help</span></a>
                        </li>
                        <li><a href="<?php echo $base; ?>my-network/search" class='viewallknows'><i class="fa fa-user-plus"></i> Search Knows</a></li>
                        <?php if ($_user_role == 'admin') { ?>
                            <li><a title='Reverse Tracking of Partners' href="<?php echo $base; ?>dashboard/reverse-tracking" class='showreversetrackpane'><i class="fa fa-user"></i> Reverse
                                    Tracking</a></li>
                            <li><a title='Reverse Tracking of Partners' href="<?php echo $base; ?>member/incomplete-signup"><i class="fa fa-user"></i> Incomplete Signups</a></li>

                        <?php } ?>
                    </ul>
                </div>
            </div>
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4>3 Touch Program</h4>
                </div>
                <div class="panel-body panel-menu">
                    <ul id='reminders'>
                        <?php if ($_user_role == 'admin') { ?>
                            <li class="close_drop">
                                <a href='<?php echo $base; ?>program/question'><i class="fa fa-cog"></i> Program Questions</a>
                            </li>

                            <li class="close_drop">
                                <a data-toggle="tab" href='#menu76' class='3tperformances'><i class="fa fa-bell-o"></i> Progress Tracking</a>
                            </li>

                        <?php } else  //if($programstatus == 1 )
                        {
                            ?>
                            <li class="close_drop">
                                <a href='<?php echo $base; ?>program/relations'><i class="fa fa-bell-o"></i> Manage Relationship</a>
                            </li>
                            <li class="close_drop"><a href='<?php echo $base; ?>program/activities'><i class="fa fa-bell-o"></i> Track Activities</a></li>
                        <?php } ?>
                    </ul>
                </div>
            </div>


            <?php if ($_user_role == 'user') { ?>
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4>Reminders</h4>
                    </div>
                    <div class="panel-body panel-menu">
                        <ul id='reminders'>
                            <li class="close_drop">
                                <a class='fetchreminder' href='<?php echo $base; ?>reminders'><i class="fa fa-bell-o"></i> Check Reminders</a>
                            </li>
                            <li class="close_drop">
                                <a href='<?php echo $base; ?>reminders/add'><i class="fa fa-clock-o"></i> Set Reminder</a>
                            </li>
                            <li class="close_drop">
                                <a class='showremindersummary' href="<?php echo $base; ?>reminders/manage"><i class="fa fa-pencil"></i> Edit Reminders</a>
                            </li>
                        </ul>
                    </div>
                </div>

            <?php } ?>
            <?php if ($_user_role == 'admin') { ?>
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4>System Configuration</h4>
                    </div>
                    <div class="panel-body panel-menu">
                        <ul>
                            <li><a href='<?php echo $base; ?>manage-vocations'><i class="fa fa-graduation-cap"></i> Vocations</a></li>
                            <li><a href="<?php echo $base; ?>manage-vocations/common-vocation/"><i class="fa fa-cog"></i> Common Vocations</a></li>
                            <li><a href='<?php echo $base; ?>manage-lifestyle'><i class="fa fa-graduation-cap"></i> Lifestyle</a></li>
                            <li><a data-toggle="tab" href="#menu28"><i class="fa fa-envelope"></i> Configure Mail Templates</a></li>
                            <li><a href="<?php echo $base; ?>manage-helpbutton"><i class="fa fa-cog"></i> Manage Help Buttons</a></li>
                            <li><a href="<?php echo $base; ?>testimonials/manage"><i class="fa fa-cog"></i> Manage Testimonials</a></li>
                            <li><a href="<?php echo $base; ?>manage-tags"><i class="fa fa-cog"></i> Add/Edit Tags</a></li>
                        </ul>
                    </div>
                </div>
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4>Page Changes</h4>
                    </div>
                    <div class="panel-body panel-menu">
                        <ul>
                            <li><a href="<?php echo $base; ?>packages/manage"><i class="fa fa-cube"></i> Packages</a></li>
                            <li><a href="<?php echo $base; ?>about/edit"><i class="fa fa-support"></i> About Us</a></li>
                            <li><a data-toggle="tab" id='manageblog' href="#blogmanage"><i class="fa fa-pencil-square"></i> Blog</a></li>
                            <li><a href="<?php echo $base; ?>configuration/tagline"><i class="fa fa-tags"></i> Tagline</a></li>
                        </ul>
                    </div>
                </div>
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4>Users Management</h4>
                    </div>
                    <div class="panel-body panel-menu">
                        <ul>
                            <li><a data-toggle="tab" class='newSignup' href="#menu14"><i class="fa fa-users"></i>New Clients Group Request</a></li>
                            <li><a href='<?php echo $base; ?>faqs/edit'><i class="fa fa-support"></i>Help / FAQ</a></li>
                            <li><a href='<?php echo $base; ?>manage-groups'><i class="fa fa-users"></i>Groups</a></li>
                            <li><a href='<?php echo $base; ?>manage-groups/manage-new-listing'><i class="fa fa-building"></i> New City Listing Requests</a></li>
                            <li><a data-toggle="tab" class='knowstatpane' href="#menu18"><i class="fa fa-users"></i>Knows Stats</a></li>
                            <li><a data-toggle="tab" class='fetchpoints' href="#menu22"><i class="fa fa-users"></i>Manage Loyalty Points</a></li>
                            <li><a data-toggle="tab" title='Generate a report of who entered new knows recently' class='newKnowEntries' href="#menu23"><i class="fa fa-bar-chart"></i>New Know
                                    Report</a></li>
                            <li><a data-toggle="tab" title='Generate a report of who signuped recently' href="#menu45"><i class="fa fa-users"></i> New Signups</a></li>
                            <li><a data-toggle="tab" href="#menu27"> <i class="fa fa-users"></i>Track Referrals By Group</a></li>
                            <li><a data-toggle="tab" href="#menu40"> <i class="fa fa-users"></i>Track Referrals By Vocation</a></li>
                            <li><a data-toggle="tab" title='Singup from LinkedIn Invite' class='linkedinsignup' href="#menu43"><i class="fa fa-linkedin"></i> LinkedIn Contacts Signups</a></li>
                            <li><a data-toggle="tab" title='Export to Spreadsheet' class='' href="#menuExportSpread"><i class="fa fa-bar-chart"></i> Export to Spreadsheet</a></li>
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
                            <li><a href="<?php echo $base; ?>city/request_listing"><i class="fa fa-building"></i> Request to List Your City</a></li>
                        <?php } ?>
                        <li><a href="<?php echo $base; ?>business/nearby"><i class="fa fa-search"></i>Nearby Members</a></li>
                        <?php if ($_user_role == 'admin') { ?>
                            <li><a href="<?php echo $base; ?>dashboard/search-log"><i class="fa fa-graduation-cap"></i> Search Logs</a></li>
                            <li><a data-toggle="tab" href="#menu30" class='loadhomesearchlog'><i class="fa fa-graduation-cap"></i> Home Search Logs</a></li>
                            <li><a data-toggle="tab" class='businesslog' href="#menu54"><i class="fa fa-clipboard"></i> Business Search Logs</a></li>
                            <li><a data-toggle="tab" class='trendingsrclog' href="#menu62"><i class="fa fa-clipboard"></i> Top Bar Search Logs</a></li>
                        <?php } ?>
                        <li><a href="<?php echo $base; ?>triggers"> <i class="fa fa-question-circle"></i>My Triggers</a>
                            <a href="<?php echo $help_data_buttons[3]['helpvideo']; ?>" target="_blank"><i class='fa fa-arrow-right'></i><span style="color:red;">Help</span></a>
                        </li>
                        <li><a href="<?php echo $base; ?>my-network/import_from_linkedin"><i class="fa fa-upload"></i> Import LinkedIn Connection File</a></li>
                        <li><a data-toggle="tab" title='Generate a report] of imported LinkedIn Contacts' class='linkedinimportlist' href="#menu40"><i class="fa fa-linkedin"></i>View Imported LinkedIn
                                Contacts</a></li>
                        <li><a href="<?php echo $base; ?>configuration/privacy" title='Privacy Settings' class='mn_privacysetting'><i class="fa fa-linkedin"></i> Privacy Settings</a></li>
                        <?php if ($_user_role == 'admin') { ?>
                            <li><a data-toggle="tab" title='Update distance between zip codes' class='managedistances' href="#menu55"><i class="fa fa-linkedin"></i> Update Distance</a></li>
                            <li><a data-toggle="tab" title='Manage Fuzzy Search KeyWords' class='managefuzzysearch' href="#menu61"><i class="fa fa-cog"></i> Fuzzy Search Keyword</a></li>
                            <li><a data-toggle="tab" title='Manage City Zip Codes' class='managezipcode' href="#menu64"><i class="fa fa-pencil"></i> Manage Zip Codes</a></li>
                        <?php } ?>
                    </ul>
                </div>
            </div>
        </div>
 