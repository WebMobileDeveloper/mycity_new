<?php 
include("header.php"); 
include_once 'includes/db.php';
?>
<div id="fb-root"></div>
<section id="contact" class="packages">
    <div class="container">
        <div class="row">
            <div class="col-md-12 col-sm-12 col-xs-12">
                <h4 style="margin-bottom: 115px">Packages</h4>
            </div>
            <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="col-md-12 col-sm-12 col-xs-12">
                  <?php 
					$userPkgQ = $link->query("SELECT * FROM `packages` where pkg_status='activate' and package_title <> 'Invite'");
					if ($userPkgQ->num_rows > 0) {
						while($userpkg = $userPkgQ->fetch_assoc())
						{
			  ?>
                    <div class="col-md-4 col-sm-4 col-xs-12">
                        <div class="box">
                            <h4 class="bg"><?php echo $userpkg['package_title']; ?></h4>
                            <h4><span>$<?php echo (($userpkg['package_price']==0.00)?"Free":$userpkg['package_price']); ?>/mo</span></h4><h3><?php echo $userpkg['package_limit']; ?> months minimum</h3>
                            <ul>
							 <?php 
								$PkgQ_service = $link->query("SELECT services FROM `package_services` where  pkg_id=".$userpkg['id']);
								if ($PkgQ_service->num_rows > 0) {
								while($service = $PkgQ_service->fetch_assoc())
								{	
							 ?>
                                <li><?php echo $service['services']; ?></li>
								<?php }} ?>
                            </ul>
							<div class='btn-area'  >
                            <i class="fa fa-phone" style="font-size: 25px; line-height: 1.7555;"> Call 310 736-5787</i>
                            <a href="http://www.edgeupnetworks.com/get-started" target="_blank" class="bg">JOIN</a>
							</div>
                        </div>
                    </div>
					<?php } } ?>
                </div>
            </div>
        </div>
</section>
<?php include("footer.php") ?>
