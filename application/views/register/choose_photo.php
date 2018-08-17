 
<section id="sec_twelve" class="next-sections form-large" style="display: block; pointer-events: auto;">
        <div class="container">
            <div class="row">
               <?php echo form_open_multipart();?>
                    <div class="col-md-12">
                        <h1 class="description">Choose a photo</h1>
                        <p class="description">Optional. Make yourself more recognizable.</p>
                    </div>
                    <div class="col-md-4 col-md-offset-4 logo-background">
					<?php if(isset($error) ) echo "<div class='alert alert-danger'>" . $error . "</div>";?>
					
                        <div class="form-group">
                            <div class="change-photo">
                                <div class="img">
                                    <img id="blah" src="<?php echo $base .$image; ?>no-photo.png"
									style="border-radius: 50%; width: 130px; height: 130px;" alt="abc">
                                    <input name="usrImg" type="file" onchange="readURL(this);" required="">
                                </div>
                                <p>Click to add</p>
                            </div>
                        </div>
                        <div class="form-group">
                            <button type="submit" name='btn_update_photo' value='change_photo' class="btn btn-block button green submit regdet_update">Update account</button>
                        </div>
                    </div>
               <?php echo form_close() ; ?>
		</div>
	</div>
</section>