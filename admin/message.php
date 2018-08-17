<style>

    section#message_page #accordion {
        margin: 15px 0;
        background: transparent;
        padding: 0px;
    }

    section#message_page .panel, .panel-default > .panel-heading {
        background: transparent !important;

    }

    section#message_page p span {
        color: #7cbc18;
        font-size: 12px !important;
    }

    section#message_page textarea {
        margin-top: 12px;
    }

    section#message_page p {
        font-family: OpenSans-semibold !important;
        font-size: 13px;
        margin-bottom: 0px !important;
        padding: 2px !important;

    }

    section#message_page #accordion .panel-heading a {
        font-family: Montserrat-Light !important;
        font-size: 25px !important;
        color: #fff !important;

    }

    section#message_page #accordion .panel-heading.border {
        border-bottom: 1px solid #D3D3D3 !important;

        background: rgb(241, 239, 239) !important;
    }

    section#message_page #accordion .panel-heading {
        padding: 0;
    }

    section#message_page .panel-default {
        border-color: transparent !important;

    }

    section#message_page #accordion .panel {
        margin: 0px !important;
        border-radius: 0px;
    }

    section#message_page #accordion .panel-default > .panel-heading + .panel-collapse > .panel-body {
        padding: 8px 10px !important;
        color: #838f9a;
        background: transparent !important;

    }

    section#message_page #accordion .panel-heading a i {
        float: right;
        font-size: 20px;
        height: 20px;
        width: 20px;
        line-height: 22px;
        margin: 12px 0;
        color: #272a3a;
        background: #fff;
        border-radius: 50%;
        text-align: center;
    }

    section#message_page #accordion .panel-heading a {
        display: block;
        height: 45px;
        line-height: 45px;
        color: #fff;
        font-size: 15px;
        background: transparent;
        color: #838f9a;
        font-family: Montserrat-Light !important;
        border-bottom: 1px solid #3f414e;
        border: 0px;
        padding: 0 10px;
    }

    section#message_page label {
        background: none !important;
        margin: 0 !important;
        display: block;
    }

    section#message_page .panel-default > .panel-heading + .panel-collapse#collapseThree > .panel-body {
        padding: 8px 5px !important;
    }

    section#message_page ul li {
        display: inline-block;
        width: 100%;
        padding: 0 0 10px;
    }

    section#message_page .socialicons .btnblock {
        width: 100%;
        background: #c9c9c9;
        border: 1px solid #c9c9c9;
        color: #133440;
        text-align: center;
    }

    section#message_page #accordion .panel-heading p span {
        color: #7cbc18;
        font-family: Montserrat-Bold;
        font-size: 21px;
        text-transform: uppercase;
        font-weight: bold;
    }

    section#message_page #accordion .panel-heading p {
        color: #656161;
        font-family: Montserrat-Light;
        font-size: 17px;
        line-height: 42px;
        margin-bottom: 0px;
    }

    section#message_page button[type=submit] {
        background: #7DCAF4;
        box-shadow: 0px 3px 0px 0px rgb(36, 56, 73);
        display: block;
        color: #243609;
        padding: 10px 25px;
        height: 41px;
        border: 0;
        margin-top: 9px;
        text-align: center;
        font-weight: 400;
        font-size: 13px;

        color: #334b0c;
        float: right;

    }

    section#message_page .chat-boxx {
        border-radius: 10px;
        margin-top: 10px !important;
        background: #fff;
        padding: 4px !important;
    }

    section#message_page .scrollbar {
        float: left;
        width: 100%;
        height: 500px;
        overflow-y: scroll;
        background: rgb(241, 239, 239) !important;

        padding: 16px;
        padding-bottom: 40px;
    }

    section#message_page .scrollbar::-webkit-scrollbar-track {
        -webkit-box-shadow: inset 0 0 6px rgba(0, 0, 0, 0.3);
        border-radius: 10px;
        background-color: #fff;
    }

    section#message_page .scrollbar::-webkit-scrollbar {
        width: 12px;
    }

    section#message_page .scrollbar::-webkit-scrollbar-thumb {
        border-radius: 10px;
        -webkit-box-shadow: inset 0 0 6px rgba(0, 0, 0, .3);
        background-color: #7cbc18;
    }

    section#message_page {
        padding: 150px 0;
    }

    section#message_page .scrollbar {
        float: left;
        width: 100%;
        height: 300px;
        overflow-y: scroll;
        background: rgb(241, 239, 239) !important;
        padding: 16px;
        padding-bottom: 40px;
    }

    section#message_page .scrollbar::-webkit-scrollbar-track {
        -webkit-box-shadow: inset 0 0 6px rgba(0, 0, 0, 0.3);
        border-radius: 10px;
        background-color: #fff;
    }

    section#message_page .scrollbar::-webkit-scrollbar {
        width: 12px;
    }

    section#message_page .scrollbar::-webkit-scrollbar-thumb {
        border-radius: 10px;
        -webkit-box-shadow: inset 0 0 6px rgba(0, 0, 0, .3);
        background-color: #7cbc18;
    }

    section#message_page .left-menu-box {
        padding: 28px 28px 30px 36px;
        border-style: solid;
        border-width: 1px;
        border-color: rgb(216, 216, 216);
        background-color: rgb(245, 245, 245);
    }

    section#message_page .panel-default > .panel-heading + .panel-collapse > .panel-body {
        padding: 8px 10px !important;
        color: #838f9a;
        background: transparent !important;
    }
</style>
<?php
include_once "header.php";
include_once 'includes/db.php';

if(!isset($_SESSION['user_id'])){
    echo "<script>window.open('.','_self')</script>";
}
$userID = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : "";
?>
<section id="message_page">
    <div class="container default">
        <div class="row">
            <div class="col-md-3">
                <div class="left-menu-box">
                    <ul class="msgs">
                        <?php
                        $msgQ2 = $link->query("SELECT * FROM user_messages WHERE user_id='$userID' OR sender_id='$userID' GROUP BY sender_id");
                        if($msgQ2->num_rows < 1){
                            echo "<li>No messages found.</li>";
                        } else {
                            while($fetMsg = $msgQ2->fetch_assoc()){
                                $sender_id = $fetMsg['sender_id'];
                                $sender_name = $fetMsg['sender_name'];
                                if($sender_id != $userID){
                                    echo '<li><a href="#" class="msgFrom" data-sendid="'.$sender_id.'">'.$sender_name.'</a></li>';
                                }
                            ?>
                        <?php
                            }
                        }
                        ?>
                    </ul>
                </div>
            </div>
            <div class="col-xs-12 col-sm-8 col-md-9">
                <div id="inbox" class="">
                    <div class="panel-body no-padd msgThread">
                        <h4>Select name to view message thread</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<script>
    // fetch messages
    function fetChatMsg(){
        console.log('msgSenderID',window.msgSenderID);
        $.ajax({
            url: "includes/ajax.php",
            type: 'post',
            data: {fetChatMsg:window.msgSenderID},
            success: function (data) {
                var results = jQuery.parseJSON(JSON.stringify(data));
                $('.msgThread').html(results.msgHtml);
                $('.msgThread .replBtn').attr('data-toid',window.msgSenderID);
                waitFunc('disable')
            },
            error: function (textStatus, errorThrown) {
                waitFunc('disable');
                console.log('Status:', textStatus, " errorThrown:", errorThrown);
                alertFunc('danger', 'Sorry, unable to fetch data. Check your internet connection please.');
            }
        });
    }
    $(document).on('click', '.msgFrom', function (e) {
        e.stopImmediatePropagation();
        window.msgSenderID = parseInt($(this).attr('data-sendid').trim());
        fetChatMsg();
    });

    // Send reply to message
    // fetch messages
    function sendChatMsg(repArr){
        $.ajax({
            url: "includes/ajax.php",
            type: 'post',
            data: {sendChatMsg:repArr},
            success: function (data) {
                var results = jQuery.parseJSON(JSON.stringify(data));
                if (results.MsgType == "Done") {

                    var sendMsgHtml = '<div class="col-md-6 no-padd chat-boxx"><p>' + repArr.replMsg + '</p>' +
                        '<p class="pull-left"><span>You just now</span></p></div>';

                    $('.scrollbar').append(sendMsgHtml);
                    $('.msgThread textarea').val('');
                } else {
                    alertFunc('danger', results.Msg);
                    console.log(results);
                }
                waitFunc('disable')
            },
            error: function (textStatus, errorThrown) {
                waitFunc('disable');
                console.log('Status:', textStatus, " errorThrown:", errorThrown);
                alertFunc('danger', 'Sorry, unable to fetch data. Check your internet connection please.');
            }
        });
    }
    $(document).on('click', '.replBtn', function (e) {
        e.stopImmediatePropagation();
        var toID = $(this).attr('data-toid').trim(),
            replMsg = $(this).parents('.msgThread').find('textarea').val().trim(),
            repArr = {
                toID:toID,
                replMsg:replMsg
            };
        console.log(repArr);
        sendChatMsg(repArr);
    });

    setInterval(function () {
        fetChatMsg();
    },15000)
</script>
<?php include("footer.php") ?>
