 
<footer class="footer">
<div class="container-fluid">
    <nav class="pull-left">
        <ul>
        <?php
        if(!isset($_SESSION['user_id'])) {
            echo '<li><a href="index.php">Home </a></li>';
        } else {
            echo '<li><a href="dashboard.php">Home </a></li>';
        }
    ?>
    <li class='bar'>|</li>
    <li><a href="<?php echo $base; ?>about">About</a></li>
    <li class='bar'>|</li>
    <li><a href="<?php echo $base; ?>blog">Blog</a></li>
    <li class='bar'>|</li>
    <li><a href="http://edgeupnetworks.com/">Find Partners </a></li>
    <li class='bar'>|</li>
    <li><a href="<?php echo $base; ?>packages">Services & Pricing </a></li>
    <li class='bar'>|</li>
    <li><a href="<?php echo $base; ?>testimonials"> Testimonials </a></li>
    <li class='bar'>|</li>
	<li><a href="<?php echo $base; ?>terms-and-conditions">Terms & Conditions </a></li>
	<li>|</li>
	<li><a href="<?php echo $base; ?>privacy">Privacy Policy </a></li>
	<li>|</li> 
	<li><a href="<?php echo $base; ?>faqs">FAQs</a></li>
	<li>|</li> 
    <li><a href="contact.php">Contact us</a></li>
        </ul>
    </nav>
    <div class="copyright pull-right">
        &copy; <script>document.write(new Date().getFullYear())</script>, All rights are reserved <a href="http://mycity.com">MyCity</a>
    </div>
</div>
</footer>

<div class="modal fade videomodal" tabindex="-1" role="dialog" aria-labelledby="videomodal"
         id="videomodal">
        <div class="modal-dialog " id='watch-mycity-video'>
            <div class="modal-content">
               <div class="modal-header">
					<button type="button" id="close-video" class="close" data-dismiss="modal" aria-hidden="true">×</button>
				</div>
                <div class="modal-body  "  > 
				<div class="embed-responsive embed-responsive-16by9">
					 <div id="player"></div>   
                </div>
				 </div>
            </div>
        </div>
</div> 
<div id="stop" class="scrollTop">
	<span><a href=""><i class='fa fa-arrow-up'></i></a></span>
</div>
<?php
	echo ( isset($error) ?  $error : ''); 
?>
<div class="notifications" style="pointer-events: auto;"></div>
<script src="<?php echo   $base.$asset;?>js/jquery-1.8.3.min.js" ></script> 
<script src="<?php echo   $base.$asset;?>js/jquery-3.2.1.min.js" ></script> 
<script src="<?php echo   $base.$asset;?>js/bootstrap.min.js" type="text/javascript"></script>  	
<script src="<?php echo $base.$asset ;?>js/jquery-ui.min.js"></script>     
<script src="<?php echo $base.$asset ;?>dt/datatables.min.js"></script>  
<script src="<?php echo $base.$asset ;?>js/chosen.jquery.min.js"></script> 
<script src="<?php echo $base.$asset ;?>js/tooltipster.bundle.min.js"></script>
<script src="<?php echo $base.$asset ;?>js/jquery.easy-autocomplete.min.js"></script>  
<script src="<?php echo $base.$asset ;?>js/dashboard.js?v=1.<?php echo mt_rand(1, 100); ?>"></script> 
<script src="<?php echo $base.$asset ;?>js/register.js"></script>  
<script src="<?php echo $base.$asset ;?>ckeditor/ckeditor.js"></script>	
<script src="<?php echo $base.$asset ;?>js/dropzone.js"></script>
 

<?php 
if( isset($this->session->role) && $this->session->role =='admin'){
?><script src="<?php echo $base.$asset ;?>js/admin.min.js?v=1.<?php echo mt_rand(1, 100); ?>"></script> 
<?php } ?> 
 <script> 
$(document).ready( function ()
{
	$('#tbl-reminders').DataTable( {
        "paging":   false,
        "ordering": false,
        "info":     false
    });
	$(".chosen-select").chosen({no_results_text: "Oops, nothing found!"}); 
	 
	$('#tbl_clients').DataTable(
	{
		"paging":   false,
		"ordering": false,
        "info":     false
    }); 
 	 
});

$(document).ready(function()
{
	$('.tooltip').tooltipster(); 
    $( "#divtestimonials" ).sortable();
    $( "#divtestimonials" ).disableSelection();  
	
	
});


CKEDITOR.replace( 'emailbody' );  
 


 	
</script> 

<?php 

echo "<script>";

if(isset($cur_url) && $cur_url =='tmv')
{
	?>
	CKEDITOR.replace( 'testimonial_summary' ); 
	<?php
}

if(isset($cur_url) && $cur_url =='tagline')
{
	?>
	CKEDITOR.replace( 'tagline' ); 
	<?php
}

if(isset($cur_url) && $cur_url =='aboutpage')
{
	?>
	CKEDITOR.replace( 'aboutcontent' ); 
	<?php
}


if(isset($cur_url) && $cur_url =='inviteknow')
{
	?>
	$('#composeinvitemail').modal('show'); 
	if(!CKEDITOR.instances.knowinviteemail)
	{
		CKEDITOR.replace( 'knowinviteemail' );   
	}
	else 
	{
		CKEDITOR.instances.knowinviteemail.destroy();
		CKEDITOR.replace( 'knowinviteemail' );
	} 
	CKEDITOR.instances['knowinviteemail'].setData( data.mailbody );
	<?php
}



echo "</script>";
?>



<?php if( isset($wizard) )
{
if($wizard	== true )
{
	?>
	<script>
		ref_wizard() ;
	</script>  
	<?php
}
}
?>

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

<script>

var tag = document.createElement('script'); 
	tag.src = "https://www.youtube.com/iframe_api";
	var firstScriptTag = document.getElementsByTagName('script')[0];
	firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);
	var player;
    function onYouTubeIframeAPIReady() 
	{
		videoID = $('#play-video').attr('data-video');
		layer = new YT.Player('player', {
          height: '540',
          width: '840',
          videoId:  videoID,
		   playerVars: { rel: 0},
          events: {
            'onReady': onPlayerReady,
            'onStateChange': onPlayerStateChange
          }
        });
      }
    function onPlayerReady(event) { }
    var done = false;
    function onPlayerStateChange(event) 
    {
        if (event.data == YT.PlayerState.PLAYING && !done) {
          setTimeout(stopVideo, 6000);
          done = true;
        }
    }
	
    $("#close-video").click(function(){ player.stopVideo(); }) 
    $("#play-video").click(function(){ player.playVideo(); 	 });
	</script>
</body>
</html>