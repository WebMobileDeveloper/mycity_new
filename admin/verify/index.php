<?php
/**
 * Created by PhpStorm.
 * User: mtfz
 * Date: 4/19/2016
 * Time: 4:27 PM
 */

session_start();
include_once '../includes/db.php';

$date_time = date("Y-m-d H:i:s");

$toke = isset($_GET['token']) ? $_GET['token'] : "";
$userID = isset($_GET['id']) ? $_GET['id'] : "";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="author" content="MyCity"/>
    <meta name="description" content="A professional network"/>
    <meta name="keywords" content="MyCity, My city, mycity, Social, Social media, Linkedin, LinkedIn, Professionals, Vocations, Find a person"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <title>MyCity - Reset Password</title>
    <link href="../images/logo.png" rel="icon" type="image/x-icon">
    <link rel="stylesheet" href="../css/default.css"/>
    <link rel="stylesheet" href="../css/style.css"/>
    <link rel="stylesheet" href="../css/style_2.css"/>
    <link rel="stylesheet" href="../css/font-awesome.min.css">
    <link rel="stylesheet" href="../css/owl.theme.css">
    <link rel="stylesheet" href="../css/owl.carousel.css">
    <link rel="stylesheet" href="../css/bootstrap.min.css"/>
    <link rel="stylesheet" href="../css/custom.css"/>
    <script src="../js/jquery.js" type="text/javascript"></script>
    <script src="../js/bootstrap.min.js" type="text/javascript"></script>
    <script src="../js/custom.js" type="text/javascript"></script>
</head>
<body>
<div class="container-fluid" style="max-width: 80%; margin-top: 5%">
    <?php
    $fetQ = $link->query("SELECT * FROM mc_user WHERE id='$userID' ");
    if($fetQ->num_rows > 0){
        $panelClass = 'danger';
        $panelHeading = 'Something went wrong !';
        $panelText = 'Unable to find anything on server, try again please.';

        $fet = $fetQ->fetch_assoc();

        $userToke = $fet['resPWToken'];
        $userTokeExp = $fet['resPWExp'];
        $userEmail = $fet['user_email'];
        $userName = $fet['username'];

        if($userToke != $toke || $date_time >= $userTokeExp){
            ?>
            <div class="panel panel-danger">
                <div class="panel-heading">
                    <h3 class="panel-title">Token expired !</h3>
                </div>
                <div class="panel-body text-center">
                    <span class="text-warning" style="font-weight: bold;font-size: medium">
                        This token has been expired. Would like to send it again ?
                    </span>
                    <button class="btn btn-default resendBtn"
                            data-email="<?php echo $userEmail ?>" data-username="<?php echo $userName ?>">Resend</button>
                </div>
                <div class="panel-footer"></div>
            </div>
            <?php
        }
        else {
            ?>
            <div class="panel panel-success">
                <div class="panel-heading">
                    <h3 class="panel-title">Type new password</h3>
                </div>
                <div class="panel-body text-center">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="newPW">Type new password</label>
                                    <input id="newPW" type="password" class="form-control" placeholder="New password">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="newPW2">Re-type password</label>
                                    <input id="newPW2" type="password" class="form-control" placeholder="Confirm password">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="panel-footer text-center">
                    <button class="btn btn-info updPWBtn">Update password</button>
                </div>
            </div>
            <?php
        }
    } else { ?>
        <div class="panel panel-danger">
            <div class="panel-heading">
                <h3 class="panel-title">User details not found !</h3>
            </div>
            <div class="panel-body">Sorry, details of this user not found on server.</div>
            <div class="panel-footer"></div>
        </div>
        <?php
    }
    ?>
    <script>
        // reset token
        function resetToken(resetArr,$thisBtn){
            $thisBtn.button('Loading..');
            $.ajax({
                url: "../includes/ajax.php",
                type: 'post',
                data: {resetToken:resetArr},
                success: function (data) {
                    //var results = jQuery.parseJSON(JSON.stringify(data));
                    if (data == "success") {
                        alertFunc(data, 'Reset password link sent in email.');
                        location.href = '../';
                    } else {
                        alertFunc('danger', data);
                    }
                    $thisBtn.button('Resend');
                },
                error: function (textStatus, errorThrown) {
                    waitFunc('disable');
                    console.log('Status:', textStatus, " errorThrown:", errorThrown);
                    alertFunc('danger', 'Sorry, unable to fetch data. Check your internet connection please.');
                }
            });
        }
        $(document).on('click', '.resendBtn', function (e) {
            e.stopImmediatePropagation();
            var $thisBtn = $(this),
                userID = '<?php echo $userID ?>',
                userEmail = $thisBtn.attr('data-email'),
                username = $thisBtn.attr('data-username'),
                resetArr = {
                    userID:userID,
                    userEmail:userEmail,
                    username:username
                };
            resetToken(resetArr,$thisBtn);
        });

        // update password
        function updPW(updPWArr,$thisBtn) {
            $thisBtn.button('Loading..');
            $.ajax({
                url: "../includes/ajax.php",
                type: 'post',
                data: {updPW: updPWArr},
                success: function (data) {
                    //var results = jQuery.parseJSON(JSON.stringify(data));
                    if (data == "success") {
                        alertFunc(data, 'Password updated');
                        location.href = '../';
                    } else {
                        alertFunc('danger', data);
                    }
                    $thisBtn.button('Update password');
                },
                error: function (textStatus, errorThrown)
                {
                    waitFunc('disable');
                    console.log('Status:', textStatus, " errorThrown:", errorThrown);
                    alertFunc('danger', 'Sorry, unable to fetch data. Check your internet connection please.');
                }
            });
        }
        $(document).on('click', '.updPWBtn', function (e) {
            e.stopImmediatePropagation();
            var $thisBtn = $(this),
                userID = '<?php echo $userID ?>',
                userEmail = '<?php echo $userEmail ?>',
                userName = '<?php echo $userName ?>',
                newPW = $('#newPW').val().trim(),
                newPW2 = $('#newPW2').val().trim(),
                updPWArr = {
                    userID:userID,
                    userEmail:userEmail,
                    userName:userName,
                    newPW:newPW,
                    newPW2:newPW2
                };
            if(newPW != newPW2){
                alertFunc('warning','Password not matched.');
            }
            else {
                updPW(updPWArr, $thisBtn);
            }
        });
    </script>
</div>
</body>
</html>
