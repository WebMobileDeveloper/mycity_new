<?php
session_start();
if ($_SERVER['SERVER_NAME']=='localhost') {
    define("ENVIRONMENT", "development");
    define("BASE_URL", "http://localhost:81");
} else {
    define("ENVIRONMENT", "production");
    define("BASE_URL", "https://mycity.com");
}
include_once 'includes/db.php';

$headers  = 'MIME-Version: 1.0' . "\r\n";
$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
$headers .= 'From: noreply@mycity.com';

// User registration email to Admin
$msg1 = "<!DOCTYPE html><html>
<head>
    <meta http-equiv='Content-Type' content='text/html; charset=utf-8'/>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'/>
    <title>Thanks for registration</title>
    <style type='text/css'>
        body {margin: 10px 0; padding: 0 10px; background: #f3f3f3; font-size: 14px;}
        table {border-collapse: collapse;}
        td {font-family: arial, sans-serif; color: #333333;}

        @media only screen and (max-width: 480px) {
            body,table,td,p,a,li,blockquote {
                -webkit-text-size-adjust:none !important;
            }
            table {width: 100% !important;}

            .responsive-image img {
                height: auto !important;
                max-width: 100% !important;
                width: 100% !important;
            }
        }
    </style>
</head>
<body>
<table border='0' cellpadding='0' cellspacing='0' width='100%'>
    <tr>
        <td>
            <table border='0' cellpadding='0' cellspacing='0' align='center' width='640' bgcolor='#FFFFFF' style='color: #333333'>
                <tr>
                    <td bgcolor='#333333' style='font-size: 30px; color:#fff; padding: 0 10px;border-bottom:10px solid #78b0d1;' height='100' align='center'>
                        <a href='http://www.mycity.com' target='_blank'>
                            <img src='http://www.mycity.com/images/logo.png' width='100' alt='www.mycity.com' />
                        </a>
                    </td>
                </tr>
                <tr><td style='font-size: 30px; line-height: 3; text-align: center' height='30'><span style='font-weight: bold;'>User Logout</span></td></tr>
                <tr>
                    <td style='padding: 10px 10px 30px 10px;'>
                        <div style='font-size: 16px;'>Dear: <span style='font-weight: bold;'>Admin MyCity</span></div>
                        <br />
                        <div>A user is logged-out on www.mycity.com</div>
                        <br />
                        <div>Username: ".$_SESSION['username']."</div>
                        <br />
                        <div>Email: ".$_SESSION['user_email']."</div>
                        <br />
                    </td>
                </tr>
                <tr>
                <td style='padding: 0 10px 10px 10px;'>If you are not concerned person, ignore this email please.</td>
                </tr>
                <tr><td style='font-size: 0; line-height: 0;' height='1' bgcolor='#eeeeee'>&nbsp;</td></tr>
                <tr><td style='font-size: 0; line-height: 0;' height='40'>&nbsp;</td></tr>
                <tr>
                    <td bgcolor='#333333' style='border-top:4px solid #78b0d1;'>
                        <table border='0' cellpadding='0' cellspacing='0' width='100%'>
                            <tr><td style='font-size: 0; line-height: 0;' height='20'>&nbsp;</td></tr>
                            <tr>
                                <td style='padding: 0 10px; color: #cccccc;' align='center'>
                                    Copyright &copy; ".date('Y')." | All Rights Reserved.
                                </td>
                            </tr>
                            <tr><td style='font-size: 0; line-height: 0;' height='20'>&nbsp;</td></tr>
                        </table>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
</body>
</html>";
mail('mycityalerts@gmail.com','User signout on MyCity',$msg1,$headers);  
$loginlog = $_SESSION['login_log_id']; 
$link->query("update mc_login_log set logouttime=NOW() where id='$loginlog'");
session_destroy();


//clearing all cookies 
if (isset($_COOKIE['_mcu'])) 
{
    unset($_COOKIE['_mcu']);   
    setcookie('_mcu', null, -1, '/');  
	setcookie('_mcu', null, -1, '/admin');  
    setcookie('_mcu', null, -1, '/admin/');
}
if (isset($_COOKIE['_rmtoken'])) {
    unset($_COOKIE['_rmtoken']); 
    setcookie('_rmtoken', null, -1, '/admin');  
	setcookie('_rmtoken', null, -1, '/admin/');  
    setcookie('_rmtoken', null, -1, '/');  
    
}
echo "<script>window.open('". BASE_URL ."', '_self')</script>";
?>