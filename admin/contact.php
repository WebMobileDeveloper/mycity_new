<?php include("header.php"); ?>
<div id="fb-root"></div>
<section id="contact">
    <div class="container">
        <div class="row">
            <div class="col-md-12 col-sm-12 col-xs-12" style="margin-bottom: 50px">
                <h4>Contact us</h4>
                <h5><i class="fa fa-phone"></i> 310-736-5787</h5>
                <h5> <i class="fa fa-envelope"></i>bob@mycity.com</h5>
            </div>

            <div class="clearfix"></div>

            <div class="col-md-12">
			    <form id="contectForm" method="post">
                <fieldset class="form-horizontal">
                    <div class="form-group">
                        <span class="col-xs-1 col-xs-offset-2 text-center"><i class="fa fa-user"></i></span>
                        <div class="col-xs-8">
                            <input id="fname" name="fname" type="text" placeholder="Your Name" class="form-control">
                        </div>
                    </div>
                    <div class="form-group">
                        <span class="col-xs-1 col-xs-offset-2 text-center"><i class="fa fa-envelope-o "></i></span>
                        <div class="col-xs-8">
                            <input id="email" name="email" type="text" placeholder="Email Address" class="form-control">
                        </div>
                    </div>
                    <div class="form-group">
                        <span class="col-xs-1 col-xs-offset-2 text-center"><i class="fa fa-users"></i></span>
                        <div class="col-xs-8">
                            <input id="lname" name="company" type="text" placeholder="Company Name" class="form-control">
                        </div>
                    </div>
                    <div class="form-group">
                        <span class="col-xs-1 col-xs-offset-2 text-center"><i class="fa fa-phone-square "></i></span>
                        <div class="col-xs-8">
                            <input id="email" name="phone" type="text" placeholder="Phone Number" class="form-control">
                        </div>
                    </div>
                    <div class="form-group">
                        <span class="col-xs-1 col-xs-offset-2 text-center"><i class="fa fa-pencil-square-o "></i></span>
                        <div class="col-xs-8">
                            <textarea class="form-control" id="message" name="message" placeholder="Enter your massage for us here. We will get back to you soon." rows="7"></textarea>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-xs-11 text-right">
                            <button name="submit_contact" class="btn btn-primary btn-lg">Submit</button>
                        </div>
                    </div>
                </fieldset>
				</form>
            </div>
        </div>
    </div>
</section>
<?php include("footer.php") ?>
