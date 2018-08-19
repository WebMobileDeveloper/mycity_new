<?php
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

$param = array('userid' =>  $user_id, 'goto' => 1);
$mynotes = json_decode(   curlexecute($param, $siteurl . 'api/api.php/notes/getall/'), true);


$vocaoptions ='';
foreach ($vocations as $vocation) 
{
	$vocaoptions .= "<option value='" . $vocation['voc_name'] . "'>" . $vocation['voc_name'] . "</option>"; 
}
$citynames ='';
$grouplist ='';
foreach ($groups as $group)
{
	if($group['grp_name'] != '')
		$citynames .= "<option value='" . $group['grp_name'] . "'>" . $group['grp_name'] . "</option>";
 
	$grouplist .= "<option value='" . $group['id'] . "'>" . $group['grp_name'] . "</option>";
}						
?> 
	    
    
    <section id="sec_five" style='background: #177361;
    padding: 170px 0 !important;'class="next-sectionsd form-large"> 
          <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <h1 class="description">Who is your targeted referral partners?</h1>
                </div>
                <div class="col-md-4 col-md-offset-4 logo-background targeted_client_main">
                    <div class="multi-select industries targeted_referral_append" data-name="industries" data-required="1" data-max="5" data-numbered="1"
                         data-clearable="1">
                        <div class="form-group">
                            <select name="targeted_referral_partners[1]" data-name="industries" class="form-control select2 target_referrals signup select2-hidden-accessible"
                                     placeholder="Select your referral vocation..." data-class="form-large" tabindex="-1" aria-hidden="true">
                                <option value="null">Select your referral vocation...</option>
                                <?php
                               echo $vocaoptions ;
                                ?>
                            </select>
                            <span class="required">*</span></div>
                        <div class="form-group">
                            <select name="targeted_referral_partners[2]" data-name="industries" class="form-control select2 signup target_referrals select2-hidden-accessible"
                                    placeholder="Select your referral vocation..." data-class="form-large" tabindex="-1" aria-hidden="true">
                                <option value="null">Select your referral vocation...</option>
                                <?php
                               echo $vocaoptions ;
                                ?>
                            </select>

                        </div>
                        <div class="form-group">
                            <select name="targeted_referral_partners[3]" data-name="industries" class="form-control target_referrals select2 signup select2-hidden-accessible"
                                    placeholder="Select your referral vocation..." data-class="form-large" tabindex="-1" aria-hidden="true">
                                <option value="null">Select your referral vocation...</option>
                                <?php
                                echo $vocaoptions ;
                                ?>
                            </select>

                        </div>
                    </div>
                    <div class="form-group">
                        <p><a id="add_more_targeted_referral" class="add-more" data-for="industries"><i class="fa fa-plus-circle"></i> Add another industry</a>
                        </p>
                    </div>
                    <div class="form-group">
                        <button type="button" data-sec="#sec_twelve" id="nextBtn7" class="nextBtn btn btn-block button green submit">Next</button>
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