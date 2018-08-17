    <section class="footer">
        <div class="container">
            <div class="row">
                <div class=" col-xs-12 col-sm-1">
                    <img src="images/logo.png" alt="logo">
                </div>
                <div class=" col-xs-12 col-sm-11 text-right">
                    <ul>
						<?php
							if(!isset($_SESSION['user_id'])) {
								echo '<li><a href="index.php">Home </a></li>';
							} else {
								echo '<li><a href="dashboard.php">Home </a></li>';
							}
						?>
                        <li>|</li>
                        <li><a href="about.php">About</a></li>
                        <li>|</li>
						<li><a href="blog.php">Blog</a></li>
                        <li>|</li>
                        <li><a href="http://edgeupnetworks.com/">Find Partners </a></li>
                        <li>|</li>
                        <li><a href="packages.php">Services & Pricing </a></li>
                        <li>|</li>
						<li><a href="testimonial.php"> Testimonials </a></li>
                        <li>|</li>
						<li><a href="terms.php">Terms & Conditions </a></li>
                        <li>|</li>
						<li><a href="privacy.php">Privacy Policy </a></li>
                        <li>|</li> 
						<li><a href="faqs.php">FAQs</a></li>
	<li>|</li> 
                        <li><a href="contact.php">Contact us</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </section>
	
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

	<link rel="stylesheet" href="css/tooltipster.bundle.min.css">
    <script src="js/jquery.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/owl.carousel.js"></script>
    <script src="js/scroll.js"></script>
    <script src="js/jquery.scrollme.min.js"></script>
	<script src="js/core.js"></script>
	<script src="js/dropdown.js"></script>
	<script src="js/myscript.js"></script>
	<script src="js/jquery-ui.min.js"></script>
	<script src="ckeditor/ckeditor.js"></script>	   
	<script src="js/chosen.jquery.min.js" type="text/javascript"></script>
	<script src="js/tooltipster.bundle.min.js" type="text/javascript"></script>
	<script src="js/jquery.easy-autocomplete.min.js"></script>
	<script src="js/dropzone.js"></script>
<script src="js/jquery-editable-select.js"></script>
	
	<script src='js/selectivity-jquery.js'></script>
	 <script src="js/bootstrap-combobox.js"></script>
	<link rel="stylesheet" href="css/bootstrap-combobox.min.css"> 
	<link rel="stylesheet" href="css/jquery-editable-select.css"> 
	<script type="text/javascript">
     
	$(document).ready(function()
	{
		 $('.my_vocs').editableSelect({ effects: 'fade' });
		  $('.target_clients').editableSelect({ effects: 'fade' });
		   $('.target_referrals').editableSelect({ effects: 'fade' });
   
	}); 
			
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
		
		
			
 
	var config = {
      '.chosen-select'           : {},
      '.chosen-select-deselect'  : {allow_single_deselect:true},
      '.chosen-select-no-single' : {disable_search_threshold:10},
      '.chosen-select-no-results': {no_results_text:'Oops, nothing found!'},
      '.chosen-select-width'     : {width:"95%"}
    }
    for (var selector in config) {
      $(selector).chosen(config[selector]);
    }
   
   var config = {
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

   
    $(".srchentryDate").datepicker({ dateFormat: 'yy-mm-dd' });
    $(".txtdatepicker").datepicker({ dateFormat: 'yy-mm-dd' }); 


</script> 
   

   <script>
    $(window).load(function () {
      getUserClients('1');
      getQues();
	 getSearchlogs(1);
	 getHomeSearchlogs(1);
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
$(document).on('click', '.viewrefsvoc', function() {
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
</body>
</html>