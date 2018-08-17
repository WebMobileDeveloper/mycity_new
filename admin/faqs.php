<?php
$page_title='FAQs - MyCity';
include("header.php");
include_once 'includes/db.php';
include_once 'includes/functions.php';
if (isset($_SESSION['user_id']))
{
	header('location: dashboard.php');
}
  
if(isset($_POST['btnlandingsignup']))
{
    $landingzip = $_POST['landingzip'];
    $landingcity = $_POST['landingcity']; 
}
if(  $_SERVER['HTTP_HOST'] == "localhost" || $_SERVER['HTTP_HOST'] == "mycity.test")
{
    $siteurl = 'http://'. $_SERVER['SERVER_NAME'] . "/";
} 
else
{
    $siteurl =  'https://mycity.com/';
} 						
?>

<section id="dashboard">
 <div class="container ">
  <div class="row">
  <div style='margin-top: 50px' class='col-md-8 col-md-offset-2 col-xs-10 col-xs-offset-1 marg4'>
    <h1 class="caps"><strong>FAQS</strong> </h1>  

 <div id="helpaccordion">
 
<div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true"><div class="panel panel-default"><div class="panel-heading" role="tab" id="head0"><h2 class="panel-title"><a role="button" data-toggle="collapse" data-parent="#accordion" href="#col0" aria-expanded="true" aria-controls="collapseOne">MyCity Calling System</a></h2></div><div id="col0" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="head0"><div class="panel-body"><img width="100%" src="https://mycity.com/assets/img/edgeup_network_success_system.jpg" alt="MyCity Calling System"></div></div></div><div class="panel panel-default"><div class="panel-heading" role="tab" id="head1"><h2 class="panel-title"><a role="button" data-toggle="collapse" data-parent="#accordion" href="#col1" aria-expanded="true" aria-controls="collapseOne">MyCity Business Growth</a></h2></div><div id="col1" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="head1"><div class="panel-body"><img width="100%" src="https://mycity.com/assets/img/mycity_business_growth.jpg" alt="Edgeup Network Success System"></div></div></div><div class="panel panel-default"><div class="panel-heading" role="tab" id="head2"><h2 class="panel-title"><a role="button" data-toggle="collapse" data-parent="#accordion" href="#col2" aria-expanded="true" aria-controls="collapseOne">Voice Mail Drops and Permission Texting System</a></h2></div><div id="col2" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="head2"><div class="panel-body"><img width="100%" src="https://mycity.com/assets/img/voice-mail-drops-and-permission-texting-system.jpg" alt="Voice Mail Drops &amp; Permission Texting System"></div></div></div><div class="panel panel-default"><div class="panel-heading" role="tab" id="head3"><h2 class="panel-title"><a role="button" data-toggle="collapse" data-parent="#accordion" href="#col3" aria-expanded="true" aria-controls="collapseOne">Interview Training Video</a></h2></div><div id="col3" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="head3"><div class="panel-body"><div class="embed-responsive embed-responsive-16by9 tmvideo"><iframe class="embed-responsive-item" frameborder="0" width="100" height="315" src="https://www.youtube.com/embed/KYmyrMQ0ucw"></iframe> </div> </div></div></div><div class="panel panel-default"><div class="panel-heading" role="tab" id="head4"><h2 class="panel-title"><a role="button" data-toggle="collapse" data-parent="#accordion" href="#col4" aria-expanded="true" aria-controls="collapseOne">People You Know- Your Most Valuable Asset.</a></h2></div><div id="col4" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="head4"><div class="panel-body">Since 70-90% of your new business comes from people you know, this section is important. 10 years ago the average person knew 150-180 people. Today people know 500-1000 people due to social media and mobile communication devlces. By giving introductions/referrals to the people you know is a great way to strengthening your relationships. Add people you know. Rate them and ask who they wish to meet.  Work with your referral partners to give and in doing so receive referrals. </div></div></div><div class="panel panel-default"><div class="panel-heading" role="tab" id="head5"><h2 class="panel-title"><a role="button" data-toggle="collapse" data-parent="#accordion" href="#col5" aria-expanded="true" aria-controls="collapseOne">Understanding Why Mycity.com</a></h2></div><div id="col5" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="head5"><div class="panel-body">The Mycity.com System is a proactive way of networking with networking partners. 10 years ago the average person knew 150-180 people. Social media an mobile communications is rapidly expanding the people we know. Today, people know 500-1000 plus people. Few business people have developed databases or rating system help you to evaluate for business and networking purposes a system to create more clients and referral partners.  </div></div></div><div class="panel panel-default"><div class="panel-heading" role="tab" id="head6"><h2 class="panel-title"><a role="button" data-toggle="collapse" data-parent="#accordion" href="#col6" aria-expanded="true" aria-controls="collapseOne">Client / User</a></h2></div><div id="col6" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="head6"><div class="panel-body">This is your personal profile. When you joined as a member you entered basic information. 1) You name, locations, email address, phone number, 2) your vocation, 3) Targeted clients and 4) Targeted referral partners. 
To change information simply go to action on the right side and click on the pencil to change/update information.</div></div></div><div class="panel panel-default"><div class="panel-heading" role="tab" id="head7"><h2 class="panel-title"><a role="button" data-toggle="collapse" data-parent="#accordion" href="#col7" aria-expanded="true" aria-controls="collapseOne">Group Referral Partners</a></h2></div><div id="col7" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="head7"><div class="panel-body">Be proactive and send a referral/introduction to one of your referral partners. Search for a partner and see who their targeted clients and targeted referral partners. The more referrals you give, the more referrals you get! </div></div></div><div class="panel panel-default"><div class="panel-heading" role="tab" id="head8"><h2 class="panel-title"><a role="button" data-toggle="collapse" data-parent="#accordion" href="#col8" aria-expanded="true" aria-controls="collapseOne">Search Page</a></h2></div><div id="col8" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="head8"><div class="panel-body">You can search people you would like to meet by vocations. See how your partners have rated these individuals. And, you can see who your targeted clients and targeted referral partners would like to meet.
Suggestion is to meet these targeted individuals and start building a relationship. Start by helping them. Build relationships before trying to sell.</div></div></div><div class="panel panel-default"><div class="panel-heading" role="tab" id="head9"><h2 class="panel-title"><a role="button" data-toggle="collapse" data-parent="#accordion" href="#col9" aria-expanded="true" aria-controls="collapseOne">Contact Us</a></h2></div><div id="col9" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="head9"><div class="panel-body">Bob Friedenthal CEO Edge Up Network, 310-736-5787, bob@edgeupnetwork.com or bob@mycity.com, Located in Los Angeles, California
</div></div></div><div class="panel panel-default"><div class="panel-heading" role="tab" id="head10"><h2 class="panel-title"><a role="button" data-toggle="collapse" data-parent="#accordion" href="#col10" aria-expanded="true" aria-controls="collapseOne">Looking to add mycity to your group or want to start your own group?</a></h2></div><div id="col10" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="head10"><div class="panel-body">Make 2017 a banner year with our program. Talk to us about adding the Mycity.com system to your existing networking group. Or if you are interested in starting your own, talk to us.</div></div></div><div class="panel panel-default"><div class="panel-heading" role="tab" id="head11"><h2 class="panel-title"><a role="button" data-toggle="collapse" data-parent="#accordion" href="#col11" aria-expanded="true" aria-controls="collapseOne">Video- Setting Up Your Account</a></h2></div><div id="col11" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="head11"><div class="panel-body">Copy and paste the url below into your address bar:
https://youtu.be/ZnTeA98RwYE</div></div></div><div class="panel panel-default"><div class="panel-heading" role="tab" id="head12"><h2 class="panel-title"><a role="button" data-toggle="collapse" data-parent="#accordion" href="#col12" aria-expanded="true" aria-controls="collapseOne">Sales Tips</a></h2></div><div id="col12" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="head12"><div class="panel-body">Staying in front of the people you know is an important aspect of keeping your business healthy. The Mycity.com rating questions are a perfect reason to call people. Make sure to ask at the end of the questions, who they wish to meet. Then if you can send them an introduction/referral, they will certainly appreciate it. It also entitles you to ask for a referral as well.</div></div></div><div class="panel panel-default"><div class="panel-heading" role="tab" id="head13"><h2 class="panel-title"><a role="button" data-toggle="collapse" data-parent="#accordion" href="#col13" aria-expanded="true" aria-controls="collapseOne">Sales Tip- Triggers</a></h2></div><div id="col13" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="head13"><div class="panel-body">Triggers are embedded in the Mycity.com system. Triggers are the way that you can ask people certain questions to introduce your referral partners. Example: Realtor triggers- 1) Do you know anybody that is interested in buying or selling a home, 2) Do you know anyone that is getting married, 3) Having a child, 4) getting divorced, 5) Downsizing, 6) going bankrupt?</div></div></div><div class="panel panel-default"><div class="panel-heading" role="tab" id="head14"><h2 class="panel-title"><a role="button" data-toggle="collapse" data-parent="#accordion" href="#col14" aria-expanded="true" aria-controls="collapseOne">Suggestion Referral Tool</a></h2></div><div id="col14" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="head14"><div class="panel-body">The purpose of networking groups is to give and receive referrals. Mycity has developed software for creating introductions/referrals between networking partners. Here is an example: Partner A knows a financial planner with a high rating. The financial planner would like to meet a CPA. Partner B knows a CPA with a high rating. The system makes a suggested introduction/referral. Both partners are informed and if they feel the introduction is worthwhile they will follow through with the introduction. The partners will be acknowledged for having given introductions/referrals. This helps their brand and their popularity.</div></div></div><div class="panel panel-default"><div class="panel-heading" role="tab" id="head15"><h2 class="panel-title"><a role="button" data-toggle="collapse" data-parent="#accordion" href="#col15" aria-expanded="true" aria-controls="collapseOne">Introduction/Referral Help</a></h2></div><div id="col15" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="head15"><div class="panel-body">Mycity.com. Working with your partners to create introductions/Referral for people that you know.  People belong to networking groups today to receive referrals. Since 70-90% of most new business for professionals comes from referrals, this is powerful. This is the 21st century form of network. Realize face to face networking is incredibly important. Mycity is just making you more efficient and effective with your time.</div></div></div><div class="panel panel-default"><div class="panel-heading" role="tab" id="head16"><h2 class="panel-title"><a role="button" data-toggle="collapse" data-parent="#accordion" href="#col16" aria-expanded="true" aria-controls="collapseOne">OUR RATING SYSTEM helps your highly rated people you know receive referrals!</a></h2></div><div id="col16" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="head16"><div class="panel-body">Members rate other people they know with the following questions: !) Do they want to grow their business? 2) Are they willing to give referrals? 3) Do they do any networking? 4) What is their expertise in their field? 5) Would you recommend them?</div></div></div><div class="panel panel-default"><div class="panel-heading" role="tab" id="head17"><h2 class="panel-title"><a role="button" data-toggle="collapse" data-parent="#accordion" href="#col17" aria-expanded="true" aria-controls="collapseOne">Uploading your LinkedIn connections</a></h2></div><div id="col17" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="head17"><div class="panel-body">Once you log on into mycity.com, next to your profile are instructions how to download your connections. Should you have questions, please call Bob Friedenthal 310-736-5787 or email bob@mycity.com</div></div></div><div class="panel panel-default"><div class="panel-heading" role="tab" id="head18"><h2 class="panel-title"><a role="button" data-toggle="collapse" data-parent="#accordion" href="#col18" aria-expanded="true" aria-controls="collapseOne">The Search Box </a></h2></div><div id="col18" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="head18"><div class="panel-body">The Search box shows highly rated people. It also gives you an opportunity to connect with the highly rated person.</div></div></div></div>

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

	<!--Start of Tawk.to Script-->
<script type="text/javascript">
var Tawk_API=Tawk_API||{}, Tawk_LoadStart=new Date();
(function(){
var s1=document.createElement("script"),s0=document.getElementsByTagName("script")[0];
s1.async=true;
s1.src='https://embed.tawk.to/5a7df79c4b401e45400cd301/default';
s1.charset='UTF-8';
s1.setAttribute('crossorigin','*');
s0.parentNode.insertBefore(s1,s0);
})();
</script>
<!--End of Tawk.to Script-->


<?php include("footer.php") ?>