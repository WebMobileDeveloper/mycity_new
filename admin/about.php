<?php
include_once "header.php";
include_once "includes/functions.php";

$aboutUs = getPageDetails('about');
?>
<div id="fb-root"></div>
<section id="contact" class="about">
    <div class="container">
        <div class="row">
            <div class="col-md-12 col-sm-12 col-xs-12">
                <h4 style="margin-bottom: 65px; margin-top: 150px; font-weight: bold; text-decoration: underline;">About Us</h4>
            </div>
            <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="col-md-12 col-sm-12 col-xs-12 text-left">
                        <?php
                        foreach ($aboutUs as $aboutU) {
                            echo "<h3>".$aboutU['page_title']."</h3><p style='font-size:18px'>".nl2br($aboutU['page_content'])."</p>";
                        }
                        ?>
                </div>
            </div>
        </div>
</section>
<?php include("footer.php") ?>
