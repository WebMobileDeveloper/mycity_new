
<div class="modal fade mine-modal" id="edit_people_details" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Edit People You Know Details</h4>
            </div>
            <div class='editPeopleContent'>
            <div class="modal-body text-left " style="height: 450px; overflow-y: scroll;">
                <div class="col-xs-12 col-sm-12 padd-5">
                    <div class="col-xs-12"><label class="custom-label">Name:</label></div>
                    <div class="col-xs-12"><input type="text" class="form-control client_name" name="e_name"
                                                  required=""></div>
                </div>
                <div class="col-xs-12 col-sm-12 padd-5">
                    <div class="col-sm-12 col-xs-12"><label class="custom-label">Vocation(s):</label></div>
                    <div class="col-sm-12 col-xs-12">
                        <select class="form-control client_pro" name="e_profession[]" id="e_prof" size="1">
                            <?php
                           
                            ?>
                        </select>
                        <small class="pull-right">(Enter comma seperated)</small>
                    </div>
                </div>
                <div class="col-xs-12 col-sm-12 padd-5">
                    <div class="col-sm-12 col-xs-12"><label class="custom-label">Phone:</label></div>
                    <div class="col-sm-12 col-xs-12"><input type="text" class="form-control client_ph" name="e_phone"
                                                            required=""></div>
                </div>
                <div class="col-xs-12 col-sm-12 padd-5">
                    <div class="col-sm-12 col-xs-12"><label class="custom-label">Email:</label></div>
                    <div class="col-sm-12 col-xs-12"><input type="text" class="form-control client_email" name="e_email"
                                                            required=""></div>
                </div>
                <div class="col-xs-12 col-sm-12 padd-5">
                    <div class="col-sm-12 col-xs-12"><label class="custom-label">Location(s):<br>
                        </label></div>
                    <div class="col-sm-12 col-xs-12">
                        <input type="text" name="e_location" class="form-control client_location">
                        <small class="pull-right">(Enter comma separated)</small>
                    </div>
					<div class="col-sm-12 col-xs-12"><label class="custom-label">Note(s):<br>
                        </label></div>
                    <div class="col-sm-12 col-xs-12">
                        <input type="text" name="e_note" class="form-control client_note">
                        <small class="pull-right">(Enter comma separated)</small>
                    </div>
                    <div class="col-sm-12 col-xs-12"><label class="custom-label">Groups :<br>
                        </label></div>
                    <div class="col-sm-12 col-xs-12">
                        <select name="" id="" class="form-control">
                            <option value=""></option>
                        </select>
                    </div>
                </div>
                <div class="col-xs-12 col-sm-12 padd-5 pad-bor">
                    <div class="col-sm-6 padd-5 col-xs-12"><label class="custom-label">Wants more business12</label>
                    </div>
                    <div class="col-sm-6 col-xs-12 padd-5">
                        <span class="starRating main">
                            <input id="rating01" type="radio" name="rating01" value="5" checked>
                            <label for="rating01"><span></span></label><label for="rating01">5</label>

                            <input id="rating02" type="radio" name="rating01" value="4"><label
                                for="rating02"><span></span></label>
                            <label for="rating02">4</label>

                            <input id="rating03" type="radio" name="rating01" value="3"><label
                                for="rating03"><span></span></label>
                            <label for="rating03">3</label>

                            <input id="rating04" type="radio" name="rating01" value="2"><label
                                for="rating04"><span></span></label>
                            <label for="rating04">2</label>

                            <input id="rating05" type="radio" name="rating01" value="1"><label
                                for="rating05"><span></span></label>
                            <label for="rating05">1</label>
                        </span>
                    </div>
                </div>
                <div class="col-xs-12 col-sm-12 padd-5 pad-bor">
                    <div class="col-sm-6 padd-5 col-xs-12"><label class="custom-label">Willing to Give Referrals</label>
                    </div>
                    <input type="hidden" name="e_question2" value="2">
                    <div class="col-sm-6 padd-5 col-xs-12">
                                <span class="starRating main">
                                    <input id="rating11" type="radio" name="rating11" value="5" checked><label
                                        for="rating11"><span></span></label>
                                    <label for="rating11">5</label>
                                    <input id="rating12" type="radio" name="rating11" value="4"><label
                                        for="rating12"><span></span></label>
                                    <label for="rating12">4</label>
                                    <input id="rating13" type="radio" name="rating11" value="3"><label
                                        for="rating13"><span></span></label>
                                    <label for="rating13">3</label>
                                    <input id="rating14" type="radio" name="rating11" value="2"><label
                                        for="rating14"><span></span></label>
                                    <label for="rating14">2</label>
                                    <input id="rating15" type="radio" name="rating11" value="1"><label
                                        for="rating15"><span></span></label>
                                    <label for="rating15">1</label>
                                </span>
                    </div>
                </div>
                <div class="col-xs-12 col-sm-12 padd-5 pad-bor">
                    <div class="col-sm-6 padd-5  col-xs-12"><label class="custom-label">Expert Level in Their
                            field</label></div>
                    <input type="hidden" name="e_question3" value="3">
                    <div class="col-sm-6 padd-5 col-xs-12">
                                <span class="starRating main">
                                    <input id="rating21" type="radio" name="rating21" value="5" checked><label
                                        for="rating21"><span></span></label>
                                    <label for="rating21">5</label>
                                    <input id="rating22" type="radio" name="rating21" value="4"><label
                                        for="rating22"><span></span></label>
                                    <label for="rating22">4</label>
                                    <input id="rating23" type="radio" name="rating21" value="3"><label
                                        for="rating23"><span></span></label>
                                    <label for="rating23">3</label>
                                    <input id="rating24" type="radio" name="rating21" value="2"><label
                                        for="rating24"><span></span></label>
                                    <label for="rating24">2</label>
                                    <input id="rating25" type="radio" name="rating21" value="1"><label
                                        for="rating25"><span></span></label>
                                    <label for="rating25">1</label>
				</span>
		</div>
		</div>
		<div class="col-xs-12 col-sm-12 padd-5 pad-bor">
			<div class="col-sm-6 padd-5  col-xs-12"><label class="custom-label">Would you refer</label></div>
				<input type="hidden" name="e_question4" value="4">
                    <div class="col-sm-6 padd-5  col-xs-12">
                                <span class="starRating main">
                                    <input id="rating31" type="radio" name="rating31" value="5" checked><label
                                        for="rating31"><span></span></label>
                                    <label for="rating31">5</label>
                                    <input id="rating32" type="radio" name="rating31" value="4"><label
                                        for="rating32"><span></span></label>
                                    <label for="rating32">4</label>
                                    <input id="rating33" type="radio" name="rating31" value="3"><label
                                        for="rating33"><span></span></label>
                                    <label for="rating33">3</label>
                                    <input id="rating34" type="radio" name="rating31" value="2"><label
                                        for="rating34"><span></span></label>
                                    <label for="rating34">2</label>
                                    <input id="rating35" type="radio" name="rating31" value="1"><label
                                        for="rating35"><span></span></label>
                                    <label for="rating35">1</label>
                                </span>
                    </div>
                </div>
                <div class="col-xs-12 col-sm-12 padd-5 pad-bor">
                    <div class="col-sm-6 padd-5  col-xs-12"><label class="custom-label">Willing to Network</label></div>
                    <input type="hidden" name="e_question5" value="5">
                    <div class="col-sm-6 padd-5  col-xs-12">
                                <span class="starRating main">
                                    <input id="rating41" type="radio" name="rating41" value="5" checked><label
                                        for="rating41"><span></span></label>
                                    <label for="rating41">5</label>
                                    <input id="rating42" type="radio" name="rating41" value="4"><label
                                        for="rating42"><span></span></label>
                                    <label for="rating42">4</label>
                                    <input id="rating43" type="radio" name="rating41" value="3"><label
                                        for="rating43"><span></span></label>
                                    <label for="rating43">3</label>
                                    <input id="rating44" type="radio" name="rating41" value="2"><label
                                        for="rating44"><span></span></label>
                                    <label for="rating44">2</label>
                                    <input id="rating45" type="radio" name="rating41" value="1"><label
                                        for="rating45"><span></span></label>
                                    <label for="rating45">1</label>
                                </span>
                    </div>
                </div> 
                <div class="clearfix"></div>
            </div> 
            <div class="modal-footer">
            <input type="button" value="Submit" class="btn btn-primary btnblock pull-right addClientUser">
            </div>
            </div> 
        </div>
    </div>
</div>