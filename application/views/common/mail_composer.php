<div class="modal fade" id="composedirectmodal" tabindex="-1" role="dialog" aria-labelledby="composedirectmodal" >
	<div class="modal-dialog  modal-lg">
        <div class="modal-content">
		<?php echo form_open();?>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Compose Email</h4>
            </div>
            <div class="modal-body text-left mailpreview">
				<div class='row'>
					<div class='col-md-5'> 
						<h3>Member Profile</h3>
						<div id='memberprofilepreview2' style='height: 580px; overflow-y: scroll;'></div>
					</div>
					<div class='col-md-7'> 
						<h3>Compose Email</h3> 
						<label>Subject:</label>
						<input name='membermailsubject' class="form-control directmailsubject" id='membermailsubject'  placeholder="Subject">
						<br/>
						<label>Email Body:</label>	
						<textarea name="mailbody" id="previewdirectmail"></textarea> 
					</div>
				</div>	 
            </div>
            <div class="modal-footer ">
				<input name='sendermail' id='sendermail' type='hidden'/>
				<input name='senderphone' id='senderphone' type='hidden'/>
				<input name='receipentid'  id='receipentid' type='hidden'/> 
                <button type='submit' name='btn_send_email' value='send_email' class="btn btn-success" id="btnsenddirectemail">Send</button>
            </div> 
			<?php echo form_close();?>
          </div>
        </div>
</div>