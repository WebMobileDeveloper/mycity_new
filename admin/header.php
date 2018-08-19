<?php
include_once 'includes/functions.php';
if(!isset($_SESSION))session_start();
date_default_timezone_set('America/Los_Angeles');
$tagline = getPageDetails("tagline");

if ($_SERVER['HTTP_HOST'] == "localhost" || $_SERVER['HTTP_HOST'] == "mycity.test") {
    $_SERVER['HTTPS'] = false;
    $siteurl = 'http://' . $_SERVER['HTTP_HOST'] . "/";
} else {
    $siteurl = 'https://mycity.com/';
}

if (isset($_COOKIE['_mcu'])) {
    $mcu = json_decode("[" . $_COOKIE['_mcu'] . "]", true);


    //get token
    $logintoken = $mcu[0]["token"];
    if ($logintoken != '') {
        $loginlogrs = $link->query("select * from mc_user where id = (select userid from  mc_login_log where token='$logintoken') ");
        if ($loginlogrs->num_rows == 1) {
            $loginlogrow = $loginlogrs->fetch_array();

            $_SESSION['user_id'] = $loginlogrow['id'];
            $_SESSION['username'] = $loginlogrow['username'];
            $_SESSION['user_email'] = $loginlogrow['user_email'];;
            $_SESSION['user_phone'] = $loginlogrow['user_phone'];
            $_SESSION['user_role'] = $loginlogrow['user_role'];
            $_SESSION['user_pic'] = ("images/" . ((!empty($loginlogrow['image'])) ? $loginlogrow['image'] : "no-photo.png"));


            /* 
            $_SESSION['user_id'] =  $mcu[0]["id"]  ;
            $_SESSION['username'] =  $mcu[0]["name"] ;
            $_SESSION['user_email'] =  $mcu[0]["email"] ;
            $_SESSION['user_phone'] =  $mcu[0]["phone"] ;
            $_SESSION['user_role'] =  $mcu[0]["role"]  ; 
            $_SESSION['user_pic'] =     ("/images/".((!empty( $mcu[0]["image"]  ))?  $mcu[0]["image"]  :"no-photo.png"));
            */

        }
    }
}


if (isset($_COOKIE['_rmtoken'])) {

    $remembercookie = explode('"', $_COOKIE['_rmtoken'])[1];

    $param = array('remembertoken' => $remembercookie);
    $logincookie = json_decode(curlexecute($param, $siteurl . 'api/api.php/remembermecheck/'), true);


    $cookie_name = "_mcu";
    $cookie_value = json_encode($logincookie);
    setcookie($cookie_name, $cookie_value, time() + (86400 * 30), "/");

    if ($remembercookie != '') {
        $loginlogrs = $link->query("select * from mc_user where id = (select distinct userid from  mc_login_log where remembertoken='$remembercookie') ");
        if ($loginlogrs->num_rows == 1) {
            $loginlogrow = $loginlogrs->fetch_array();
            $_SESSION['user_id'] = $loginlogrow['id'];
            $_SESSION['username'] = $loginlogrow['username'];
            $_SESSION['user_email'] = $loginlogrow['user_email'];;
            $_SESSION['user_phone'] = $loginlogrow['user_phone'];
            $_SESSION['user_role'] = $loginlogrow['user_role'];
            $_SESSION['user_pic'] = ("images/" . ((!empty($loginlogrow['image'])) ? $loginlogrow['image'] : "no-photo.png"));
        }

    }
} else {
    //login has expired 
    //{"id":"19","role":"user","package":"Gold","phone":"310-736-5787","name":"Bob Friedenthal",
    //"email":"referrals@mycity.com","image":"prof-img-20170613-092605.jpg","status":"1", 
    //"profile":"http://www.mycity.com/profile/?l=19","visibility":"1","signupmode":"0", 
    //"token":"3942e24cc8f9b0ab72c71214491699af","expires":1506003420,"login_log_id":"344"} 
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="author" content="MyCity"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <?php
    $filename = basename($_SERVER['SCRIPT_FILENAME']);
    switch ($filename) {
        case "index.php":
            $page_title = 'Turn your Social Media into Relationships & Referrals';
            $page_description = 'A proactive system to grow relationships, grow referral partners, grow sales.';
            break;
        case "about.php":
            $page_title = 'About Us';
            $page_description = '';
            break;
    }
    ?>
    <title><?php if (isset($page_title)) {
            echo $page_title;
            echo " - ";
        } ?>MyCity</title>
    <?php if ($page_description != ''): ?>
        <meta content="<?php echo $page_description; ?>" name="description"/>
    <?php endif; ?>

    <link rel="stylesheet" href="css/default.css"/>
    <link rel="stylesheet" href="css/animate.css"/>
    <link rel="stylesheet" href="css/style.css?v=1.<?php echo mt_rand(1, 1000) ?>"/>
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
    <link rel='stylesheet' href='css/selectivity-jquery.css'>
    <link href="css/bootstrap-tour.min.css" rel="stylesheet">
    <script src="js/jquery.js" type="text/javascript"></script>
    <script src="js/bootstrap.min.js" type="text/javascript"></script>

    <script src="js/custom.js?v=1.<?php echo mt_rand(1, 1000) ?>" type="text/javascript"></script>

    <!-- Global site tag (gtag.js) - Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=UA-26668236-1"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag() {
            dataLayer.push(arguments);
        }
        gtag('js', new Date());

        gtag('config', 'UA-26668236-1');
    </script>

</head>
<body class="no-padd" style="padding: 0 !important; ">
<div class="modal fade bs-example-modal-sm" id="signin" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"></h4>
            </div>
            <div class="modal-body">
                <h2 class="title">Sign in</h2>
                <div class="form-group">
                    <input id="login_username" name="username" class="form-control" placeholder="Your email">
                </div>
                <div class="form-group">
                    <input id="login_password" type="password" class="form-control" name="password" placeholder="Password">
                </div>
                <button type="submit" id="sign_in_button" class="flatbutton">Let’s go</button>
                <!--<p class="forgot_password"><a href="javascript:void(0)">Forgot your password?</a></p>
                <p class="strikey">or</p>
                <button id="log_in_facebook" class="facebook_button flatbutton">Sign in</button>-->
                <p class="forgot_password"><span data-toggle="modal" data-target="#forgetPW" style="cursor:pointer;">Forgot your password?</span></p>
            </div>
        </div>
    </div>
</div>

<div class="modal fade bs-example-modal-sm" id="forgetPW" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"></h4>
            </div>
            <div class="modal-body">
                <h2 class="title">Forgot Password</h2>
                <div class="form-group">
                    <input id="forgPWEmail" type="email" class="form-control" name="forgPWEmail" placeholder="Type your email">
                </div>
                <button type="button" id="resPWBtn" class="flatbutton">Reset Password</button>
            </div>
        </div>
    </div>
</div>

<section class="header">
    <div class="container">
        <div class="row">
            <div class="col-xs-9 col-md-4">

                <?php
                if (!isset($_SESSION['user_id'])) {
                    echo '<a href="index.php"><img src="/images/logo.png" alt="MyCity" id="logo"></a>';
                } else {
                    echo '<a href="dashboard.php"><img src="/images/logo.png" alt="MyCity" id="logo"></a>';
                }
                ?>
                <a id="play-video" href='#watch-mycity-video'
                   class='noborder watchvideo' data-toggle="modal" data-target="#videomodal"
                   data-video='zUzISiLmqMw' class='play-video-home'>
                    <img src='images/bob-profile.png' class='profile'/>
                </a>
            </div>
            <div class="col-xs-3 col-md-3 text-right pull-right">
                <?php
                if (!isset($_SESSION['user_id'])) {
                    if (basename($_SERVER['PHP_SELF']) == "login.php")
                        echo "<ul><li><a class='btn btn-reg' href='/'>Register  </a></li></ul>";
                    else
                        echo "<ul><li><a class='btn btn-reg' href='" . $siteurl . "login'>Sign in </a></li></ul>";
                } else {
                    echo "<ul>
								<li><a href='dashboard.php'><i style='font-size: 36px;' class='fa fa-home' title='Home'></i></a></li>
								<li><a href='message.php'><i style='font-size: 36px;' class='fa fa-envelope' title='Messages'></i></a></li>
								<li><a href='logout.php'><i style='font-size: 36px;' class='fa fa-sign-out' title='Logout'></i></a></li>
							</ul>";
                }
                ?>
            </div>

            <div class="col-xs-12 col-md-5 ">
                <div class="global-searchd">
                    <form action='member-search.php' method='post'>
                        <p id='ts-head'>Search MyCity Members</p>
                        <div class='top-search'>
                            <div class='top-search-inner'>
                                <input type="text" id="gskey" name="gskey" placeholder="Name or vocation">
                                <input type="text" id='gscityorzip' name='gscityorzip' placeholder="City or Zip Code">
                            </div>
                            <div class=''>
                                <button type='submit' id='gsearch'><i class='fa fa-search'></i></button>
                            </div>
                            <div class='clearer'></div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>