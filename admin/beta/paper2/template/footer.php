 
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
    <li><a href="about.php">About</a></li>
    <li class='bar'>|</li>
    <li><a href="blog.php">Blog</a></li>
    <li class='bar'>|</li>
    <li><a href="http://edgeupnetworks.com/">Find Partners </a></li>
    <li class='bar'>|</li>
    <li><a href="packages.php">Services & Pricing </a></li>
    <li class='bar'>|</li>
    <li><a href="testimonial.php"> Testimonials </a></li>
    <li class='bar'>|</li>
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
