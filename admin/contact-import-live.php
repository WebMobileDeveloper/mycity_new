<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="author" content="MyCity"/> 
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <title></title>
    <link rel="stylesheet" href="css/default.css"/>
    <link rel="stylesheet" href="css/style.css"/>
    <link rel="stylesheet" href="css/style_2.css"/>
    <link rel="stylesheet" href="css/font-awesome.min.css">
    <link rel="stylesheet" href="css/owl.theme.css">
    <link rel="stylesheet" href="css/owl.carousel.css">
    <link rel="stylesheet" href="css/bootstrap.min.css"/>
    <link rel="stylesheet" href="css/custom.css"/>
	<link rel="stylesheet" href="css/dropdown.css"/>
	<link rel="stylesheet" href="css/light.css"/>
	<link rel="stylesheet" href="css/jquery-ui.min.css">
	<link rel="stylesheet" href="css/chosen.css">
	<link rel="stylesheet" href="css/easy-autocomplete.min.css">
	<link href="css/bootstrap-tour.min.css" rel="stylesheet">
    <script src="js/jquery.js" type="text/javascript"></script>
    <script src="js/bootstrap.min.js" type="text/javascript"></script>
    
	<script src="js/custom.js" type="text/javascript"></script>
	
    <script>
        (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
                (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
            m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
        })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

        ga('create', 'UA-26668236-1', 'auto');
        ga('send', 'pageview');
    </script>

<script src="//js.live.net/v5.0/wl.js"></script>

 </head>
 <body>
 <section id="contact">

    <div class="container">

        <div class="row"> 
            <div class="col-md-12 col-sm-12 col-xs-12" style="margin-bottom: 50px"> 
               <a href="#" id="import">Import contacts</a>
            </div>
 
            <div class="clearfix"></div> 
        </div> 
    </div> 
</section>  
</body>
<script>

WL.init({
    client_id: APP_CLIENT_ID,
    redirect_uri: REDIRECT_URL,
    scope: ["wl.basic", "wl.contacts_emails"],
    response_type: "token"
});




jQuery( document ).ready(function()
{
	//live.com api
	jQuery('#import').click(function(e)
	{
		e.preventDefault();
	    WL.login({
	        scope: ["wl.basic", "wl.contacts_emails"]
	    }).then(function (response) 
	    {
			WL.api({
	            path: "me/contacts",
	            method: "GET"
	        }).then(
	            function (response) {
                        //your response data with contacts 
	            	console.log(response.data);
	            },
	            function (responseFailed) {
	            	//console.log(responseFailed);
	            }
	        );
	    },
	    function (responseFailed) 
	    {
	        //console.log("Error signing in: " + responseFailed.error_description);
	    });
	});

});</script>

<?php include("footer.php") ?> 