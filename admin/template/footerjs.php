<?php

if(  $_SERVER['HTTP_HOST'] == "localhost" || $_SERVER['HTTP_HOST'] == "mycity.test")
{
	$_SERVER['HTTPS'] = false;
    $siteurl = 'http://'. $_SERVER['SERVER_NAME'] . "/";
} 
else
{
    $siteurl =  'https://mycity.com/';
}

?>

	<link rel="stylesheet" href="<?php echo $siteurl;?>css/tooltipster.bundle.min.css"> 
	<link rel="stylesheet" type="text/css" href="<?php echo $siteurl;?>assets/dt/datatables.min.css"/> 
	<script src="<?php echo $siteurl;?>admin/js/jquery.js" type="text/javascript"></script>
    <script src="<?php echo $siteurl;?>admin/js/bootstrap.min.js" type="text/javascript"></script>
	<script type="text/javascript" src="<?php echo $siteurl;?>admin/assets/dt/datatables.min.js"></script> 
	<script src="<?php echo $siteurl;?>admin/assets/js/paper-dashboard.js"></script> 
	<script src="<?php echo $siteurl;?>admin/assets/js/bootstrap-notify.js"></script>  
	<script src="<?php echo $siteurl;?>admin/js/jquery-ui.min.js"></script>
	<script src="<?php echo $siteurl;?>admin/ckeditor/ckeditor.js"></script>	   
	<script src="<?php echo $siteurl;?>admin/js/chosen.jquery.min.js" type="text/javascript"></script> 
	<script src="<?php echo $siteurl;?>admin/js/tooltipster.bundle.min.js" type="text/javascript"></script>
	<script src="<?php echo $siteurl;?>admin/js/dropzone.js"></script>  
	<script src="<?php echo $siteurl;?>admin/js/jquery.easy-autocomplete.min.js"></script> 
	<script src="<?php echo $siteurl;?>admin/js/script.js?v=<?php echo mt_rand(1,100000);?>" type="text/javascript"></script> 
	<?php if( $_SESSION['user_role'] == 'admin'  ) { ?>
	<script src="<?php echo $siteurl;?>admin/js/admin.js?v=<?php echo mt_rand(1,100000);?>" type="text/javascript"></script> 
	<script> 
		CKEDITOR.replace( 'embody' ); 
	</script> 
	<?php  } else {
		?>
	<script src="<?php echo $siteurl;?>admin/js/core.min.js?v=<?php echo mt_rand(1,100000);?>" type="text/javascript"></script>	
		<?php
	}?>
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

	<script type="text/javascript"> 
	
	
	$( function() {
    $( "#divtestimonials" ).sortable();
    $( "#divtestimonials" ).disableSelection();
  } );
  
  $("#aemschedule").datepicker({ dateFormat: 'yy-mm-dd' });
	$(document).ready(function() {
	  $('[data-toggle=offcanvas]').click(function() {
		$('.row-offcanvas').toggleClass('active');
	  });
	  
	  $("#emschedule").datepicker({ dateFormat: 'yy-mm-dd' });
	  
	});  
	
	
	var config = {
      '.chosen-select'           : {},
      '.chosen-select-deselect'  : {allow_single_deselect:true},
      '.chosen-select-no-single' : {disable_search_threshold:10},
      '.chosen-select-no-results': {no_results_text:'Oops, nothing found!'},
      '.chosen-select-width'     : {width:"95%"}
    }
    for (var selector in config)
	{
		$(selector).chosen(config[selector]);
    }
   
   var config = 
   {
	   '.client_pro'           : {},
	   '.client_pro-deselect'  : {allow_single_deselect:true},
	   '.client_pro-no-single' : {disable_search_threshold:10},
	   '.client_pro-no-results': {no_results_text:'Oops, nothing found!'},
	   '.client_pro-width'     : {width:"95%"}
    }
    
	for (var selector in config) {
      $(selector).chosen(config[selector]);
    }
	var config = {
      '.wiz_profession'           : {},
      '.wiz_profession-deselect'  : {allow_single_deselect:true},
      '.wiz_profession-no-single' : {disable_search_threshold:10},
      '.wiz_profession-no-results': {no_results_text:'Oops, nothing found!'},
      '.wiz_profession-width'     : {width:"95%"}
    }
    for (var selector in config) {
      $(selector).chosen(config[selector]);
    }
 
     var config = {
      '.group-select'           : {},
      '.group-select-deselect'  : {allow_single_deselect:true},
      '.group-select-no-single' : {disable_search_threshold:10},
      '.group-select-no-results': {no_results_text:'Oops, nothing found!'},
      '.group-select-width'     : {width:"95%"}
    }
    for (var selector in config) {
      $(selector).chosen(config[selector]);
    }
	 
    var config = {
      '.reverselookupvoc'           : {},
      '.reverselookupvoc-deselect'  : {allow_single_deselect:true},
      '.reverselookupvoc-no-single' : {disable_search_threshold:10},
      '.reverselookupvoc-no-results': {no_results_text:'Oops, nothing found!'},
      '.reverselookupvoc-width'     : {width:"95%"}
    }
    for (var selector in config) {
      $(selector).chosen(config[selector]);
    }
      
    var config = {
      '.revtracklocation'           : {},
      '.revtracklocation-deselect'  : {allow_single_deselect:true},
      '.revtracklocation-no-single' : {disable_search_threshold:10},
      '.revtracklocation-no-results': {no_results_text:'Oops, nothing found!'},
      '.revtracklocation-width'     : {width:"95%"}
    }
    for (var selector in config) {
      $(selector).chosen(config[selector]);
    }
 
var config = {
      '.member_tags'           : {},
      '.member_tags-deselect'  : {allow_single_deselect:true},
      '.member_tags-no-single' : {disable_search_threshold:10},
      '.member_tags-no-results': {no_results_text:'Oops, nothing found!'},
      '.member_tags-width'     : {width:"95%"}
    }
    for (var selector in config) {
      $(selector).chosen(config[selector]);
    }
	
	
		//$('.user_ques_text_add').selectize({
	 
		 $("#menu-toggle").click(function(e) {
        e.preventDefault();
        $("#wrapper").toggleClass("toggled");
    });
     $("#menu-toggle-2").click(function(e) {
        e.preventDefault();
        $("#wrapper").toggleClass("toggled-2");
        $('#menu ul').hide();
    });
 
     function initMenu() {
      $('#menu ul').hide();
      $('#menu ul').children('.current').parent().show();
      //$('#menu ul:first').show();
      $('#menu li a').click(
        function() {
          var checkElement = $(this).next();
          if((checkElement.is('ul')) && (checkElement.is(':visible'))) {
            return false;
            }
          if((checkElement.is('ul')) && (!checkElement.is(':visible'))) {
            $('#menu ul:visible').slideUp('normal');
            checkElement.slideDown('normal');
            return false;
            }
          }
        );
      }
    $(document).ready(function()
	{ 
		initMenu();
	
		$('.count').each(function () {
			$(this).prop('Counter',0).animate({
				Counter: $(this).data('value')
			}, {
				duration: 2000,
				easing: 'swing',
				step: function (now) {
					$(this).text(Math.ceil(now));
				}
			});
		});
		 
	 CKEDITOR.replace( 'postbody' );
	 CKEDITOR.replace( 'editpostbody' );
	 CKEDITOR.replace( 'emailbody' );
	 
	  
	 Dropzone.options.myKnows = {
		  paramName: "file",
		  maxFilesize: 10,
		  uploadMultiple: false,
		  maxFiles:1,
		  accept: function(file, done) {
			if (file.name == "myknow.csv") {
			  alert("Files not allowed");
			}
			else { done(); }
		  }
		};
         
        $("#tbfrom").datepicker({ dateFormat: 'yy-mm-dd' });
        $("#tbto").datepicker({ dateFormat: 'yy-mm-dd' });
 
	});  
	
	
	$(document).ready(function () {
    $(".btn-select").each(function (e) {
        var value = $(this).find("ul li.selected").html();
        if (value != undefined) {
            $(this).find(".btn-select-input").val(value);
            $(this).find(".btn-select-value").html(value);
        }
    });
});

$(document).on('click', '.btn-select', function (e) {
    e.preventDefault();
    var ul = $(this).find("ul");
    if ($(this).hasClass("active")) {
        if (ul.find("li").is(e.target)) {
            var target = $(e.target);
            target.addClass("selected").siblings().removeClass("selected");
            var value = target.html();
            $(this).find(".btn-select-input").val(value);
            $(this).find(".btn-select-value").html(value);
        }
        ul.hide();
        $(this).removeClass("active");
    }
	else
	{
        $('.btn-select').not(this).each(function ()
		{
            $(this).removeClass("active").find("ul").hide();
        });
        ul.slideDown(300);
        $(this).addClass("active");
    }
});

    $(document).on('click', function (e) {
        var target = $(e.target).closest(".btn-select");
        if (!target.length) {
            $(".btn-select").removeClass("active").find("ul").hide();
        }
        CKEDITOR.replace( 'emailtemplate' );
    });
 
    CKEDITOR.replace( 'testimonial_summary' ); 

    var tag = document.createElement('script'); 
	tag.src = "https://www.youtube.com/iframe_api";
	var firstScriptTag = document.getElementsByTagName('script')[0];
	firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);
	var player;
    function onYouTubeIframeAPIReady() {
		
		 videoID = $('#play-video').attr('data-video');
		
        player = new YT.Player('player', {
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
    $(".srchentryDate").datepicker({ dateFormat: 'yy-mm-dd' });
    $(".txtdatepicker").datepicker({ dateFormat: 'yy-mm-dd' }); 
	$("#remindermailday").datepicker({ dateFormat: 'yy-mm-dd' }); 


</script> 
   

   <script>
    $(window).load(function () { 
       getBlogName('.blog_list, .blog_list_ed, .blogNameList');
    }); 
	
    $('.abc').click(function () {
        $('.drop').slideToggle(1000);
    });

    $(document).ready(function () {
        $('.btn-file :file').on('fileselect', function (event, numFiles, label) {

            var input = $(this).parents('.input-group').find(':text'),
                log = numFiles > 1 ? numFiles + ' files selected' : label;

            if (input.length) {
                input.val(log);
            } else {
                if (log) alert(log);
            } 
        });
    });
    
	$('.sub-menu').on('click', 'li', function() {
		$('.sub-menu li.active').removeClass('active');
		//$(this).addClass('active');
	});
	$('.menu-content').on('click', 'li', function() {
		$('.menu-content li.active').removeClass('active');
		//$(this).addClass('active');
    }); 
    
    $(document).on('click', '#fetchgroupmembersvoc', function() {
	   //var groupid = $(this).data('gid' );
		var groupid = $('#e_prof1').val();
		$.ajax({
			type: 'post',
			url: 'includes/ajax.php',
			data: { reftrackervoc: 1, groupid: groupid },
			success: function(data) {
				$("#reftrackboardvoc").html(data);
			}
		});

	});
	 $(document).on('click', '.viewrefs', function() {
    $("#reflist").html('');
    var mid = $(this).data('id');
    var count = $(this).data('count');
    var goto = $(this).data('goto');
    var rs = $(this).data('rs');
    $('#loading').show();
    if (count > 0) {
        $.ajax({
            type: 'post',
            url: 'includes/ajax.php',
            data: { trackreferrals: 1, mid: mid, goto: goto, rs: rs },
            success: function(data) {
                $("#reflist").html(data);
                $('#loading').hide();
            }
        });
        if (goto == 1) {
            $('#reftrackingboard').modal('show');
        }
    }
});


$(document).on('click', '.viewrefsvoc', function() 
{
    $("#reflistvoc").html('');
    var mid = $(this).data('id');
    var count = $(this).data('count');
    var goto = $(this).data('goto');
    var rs = $(this).data('rs');
    $('#loadingvoc').show();
    if (count > 0) {
        $.ajax({
            type: 'post',
            url: 'includes/ajax.php',
            data: { trackreferralsvoc: 1, mid: mid, goto: goto, rs: rs },
            success: function(data) {
                $("#reflistvoc").html(data);
                $('#loadingvoc').hide();
            }
        });
        if (goto == 1) {
            $('#reftrackingboardvoc').modal('show');
        }
    }
});


</script>
