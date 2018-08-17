 
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
	<li><a href="terms.php">Terms & Conditions </a></li>
	<li>|</li>
	<li><a href="privacy.php">Privacy Policy </a></li>
	<li>|</li> 
	<li><a href="faqs.php">FAQs</a></li>
	<li>|</li> 
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

<div class="modal fade liimportmodal" tabindex="-1" role="dialog" aria-labelledby="liimportmodal" id="liimportmodal">
        <div class="modal-dialog " >
            <div class="modal-content">
               <div class="modal-header">
					<button type="button" id="close-video" class="close" data-dismiss="modal" aria-hidden="true">×</button>
					<h4 class="modal-title">Import LinkedIn </h4>
				</div>
                <div class="modal-body  "  > 
					 <form action="includes/uploader-4.php"
								  class="dropzone"
								  id="linkimport">
						<input type="hidden" id='hidliuserid' name='hidliuserid' /> 
					</form>
					<div class='form-group pad10 text-center'>
						<button class='btn btn-primary btn-lg linkedinimportba'>Start Import</button>
						<a data-toggle="tab" href="#menu40"  class='btn btn-danger btn-lg linkedinimportbalist'> View LinkedIn Import List</a> 
					</div>
				</div>
            </div>
        </div>
</div>

<div class="modal fade surverymodal" tabindex="-1" role="dialog" aria-labelledby="surverymodal" id="surverymodal">
        <div class="modal-dialog " >
            <div class="modal-content">
               <div class="modal-header">
					<button type="button" id="close-video" class="close" data-dismiss="modal" aria-hidden="true">×</button>
					<h4 class="modal-title">Simple Survey</h4>
				</div>
                <div class="modal-body  "  >  
					<div class="main"> 
					<div> 
					<div class="question-container"></div> 
					<div class='pad10'>
					<a id="backBtn" href="#" class="btn btn-primary">« Back</a>
					<a id="nextBtn" href="#" class="btn btn-primary">Continue »</a>
					</div>

					</div><div class="completed-message"></div>
					</div> 
				</div>
            </div>
        </div>
</div>

<div class="modal fade" id="addblog" tabindex="-1" role="dialog">
	<div class="modal-dialog">
		<div class="modal-content">
            <div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Add blog</h4>
            </div>
            <div class="modal-body content-inner">
                <div>
					<ul class="nav nav-tabs" role="tablist">
                        <li role="presentation" class="active"><a href="#add_blogs_name" aria-controls="home" role="tab" data-toggle="tab">Add Blog</a></li>
                        <li role="presentation"><a href="#add_content" aria-controls="profile" role="tab" data-toggle="tab">Add Content Blog</a></li>
                    </ul>
                    <div class="tab-content">
						<div role="tabpanel" class="tab-pane active" id="add_blogs_name" style="margin-top: 15px">
                            <input type="text" class="form-control addBLogName" placeholder="Add Blog">
                            <input type="button" value="Save" class="btnblock saveBlogName">
                        </div>
						<div role="tabpanel" class="tab-pane" id="add_content" style="margin-top: 15px">
                            <select name="" id="" class="form-control blog_list"></select>
                            <input type="text" class="form-control blogTitle" placeholder="Content Title">
                            <textarea name="" id="" cols="30" rows="10" class="form-control blogContent" placeholder="Content"></textarea>
                            <div class="input-group file-group">
                                    <span class="input-group-btn">
                                        <span class="btn btn-primary btn-file">
                                            Browse image… <input type="file" class="blogImage" accept="image/*">
                                        </span>
                                    </span>
                                <input type="text" class="form-control" readonly="">
                            </div>
                            <div class="input-group file-group">
								<span class="input-group-btn">
									<span class="btn btn-primary btn-file">
										Browse Video… <input type="file" class="blogVideo" accept="video/*">
									</span>
                                </span>
                            <input type="text" class="form-control" readonly="">
                            </div>
                            <input type="button" class="btnblock addBlogContent" value="Save Now">
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div> 
<div class="modal fade" id="edit-2" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Edit Blog Content</h4>
            </div>
            <div class="modal-body content-inner">
                <div style="margin-top: 30px;" class="editBlogDetails">
                    <select class="form-control blog_list_ed"></select>
                    <input type="text" class="form-control blogTitle_ed" placeholder="Content Title">
                    <textarea cols="30" rows="10" class="form-control blogContent_ed" placeholder="Content"></textarea>
                    <div class="input-group file-group">
                        <span class="input-group-btn">
                            <span class="btn btn-primary btn-file">
                                Browse to change image… <input type="file" accept="image/*" class="image_ed">
                            </span>
                        </span>
                        <input type="text" class="form-control" readonly="">
                    </div>
                    <div class="input-group file-group">
                        <span class="input-group-btn">
                            <span class="btn btn-primary btn-file">
                                Browse to change video… <input type="file" accept="video/*" class="video_ed">
                            </span>
                        </span>
                        <input type="text" class="form-control" readonly="">
                    </div>
                    <div class="media"></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary updblogData">Save changes</button>
            </div>
        </div>
    </div>
</div> 
<div class="modal fade" id="edit-1" tabindex="-1" role="dialog">
	<div class="modal-dialog">
		<div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Customize Blog Titles</h4>
            </div>
            <div class="modal-body content-inner">
                <div style="margin-top: 30px;">
                    <select class="form-control blogNameList"></select>
                    <input type="text" class="form-control blogSelectedVal" style="display: none;">
                    <input type="button" value="Edit" class="btnblock editValBlog">
                    <input type="button" value="Update Title" class="btnblock saveValBlog" style="display:none;">
                    <input type="button" value="Remove" class="btnblock removeBlogName">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div> 
<div class="modal fade" id="changeAccSett" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header ">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title text-center">Action / Changes</h3>
            </div>
            <div class="modal-body text-left fixheight">
                <div id="action">
				
				<div class='row'>
                    <div class="col-xs-12 col-sm-12"> 
						<div id='profilemsg'></div> 
					</div>
			 <div class="col-xs-12 col-sm-12 col-md-6"> 	
			 <h5>Personal Options</h5>			 
				<div class="form-group">
					  <input type="text" <?php if($_user_role != 'admin' ) echo "readonly"; ?> class="form-control" name="upd_username" placeholder="Full name">
				</div>
				<div class="form-group">
					<input type="text" class="form-control" name="upd_phone" placeholder="Phone">
				</div>
				<div class="form-group">
					<select name="upd_country" class="form-control">
                                        <option selected disabled="disabled" value="null">-select your country-</option>
                                        <option value="Afghanistan">Afghanistan</option>
                                        <option value="Åland Islands">Åland Islands</option>
                                        <option value="Albania">Albania</option>
                                        <option value="Algeria">Algeria</option>
                                        <option value="American Samoa">American Samoa</option>
                                        <option value="Andorra">Andorra</option>
                                        <option value="Angola">Angola</option>
                                        <option value="Anguilla">Anguilla</option>
                                        <option value="Antarctica">Antarctica</option>
                                        <option value="Antigua and Barbuda">Antigua and Barbuda</option>
                                        <option value="Argentina">Argentina</option>
                                        <option value="Armenia">Armenia</option>
                                        <option value="Aruba">Aruba</option>
                                        <option value="Australia">Australia</option>
                                        <option value="Austria">Austria</option>
                                        <option value="Azerbaijan">Azerbaijan</option>
                                        <option value="Bahamas">Bahamas</option>
                                        <option value="Bahrain">Bahrain</option>
                                        <option value="Bangladesh">Bangladesh</option>
                                        <option value="Barbados">Barbados</option>
                                        <option value="Belarus">Belarus</option>
                                        <option value="Belgium">Belgium</option>
                                        <option value="Belize">Belize</option>
                                        <option value="Benin">Benin</option>
                                        <option value="Bermuda">Bermuda</option>
                                        <option value="Bhutan">Bhutan</option>
                                        <option value="Bolivia Plurinational State of">Bolivia Plurinational State of</option>
                                        <option value="Bonaire Sint Eustatius and Saba">Bonaire Sint Eustatius and Saba</option>
                                        <option value="Bosnia and Herzegovina">Bosnia and Herzegovina</option>
                                        <option value="Botswana">Botswana</option>
                                        <option value="Bouvet Island">Bouvet Island</option>
                                        <option value="Brazil">Brazil</option>
                                        <option value="British Indian Ocean Territory">British Indian Ocean Territory</option>
                                        <option value="Brunei Darussalam">Brunei Darussalam</option>
                                        <option value="Bulgaria">Bulgaria</option>
                                        <option value="Burkina Faso">Burkina Faso</option>
                                        <option value="Burundi">Burundi</option>
                                        <option value="Cambodia">Cambodia</option>
                                        <option value="Cameroon">Cameroon</option>
                                        <option value="Canada">Canada</option>
                                        <option value="Cape Verde">Cape Verde</option>
                                        <option value="Cayman Islands">Cayman Islands</option>
                                        <option value="Central African Republic">Central African Republic</option>
                                        <option value="Chad">Chad</option>
                                        <option value="Chile">Chile</option>
                                        <option value="China">China</option>
                                        <option value="Christmas Island">Christmas Island</option>
                                        <option value="Cocos (Keeling) Islands">Cocos (Keeling) Islands</option>
                                        <option value="Colombia">Colombia</option>
                                        <option value="Comoros">Comoros</option>
                                        <option value="Congo">Congo</option>
                                        <option value="Congo the Democratic Republic">Congo the Democratic Republic</option>
                                        <option value="Cook Islands">Cook Islands</option>
                                        <option value="Costa Rica">Costa Rica</option>
                                        <option value="Côte dIvoire">Côte dIvoire</option>
                                        <option value="Croatia">Croatia</option>
                                        <option value="Cuba">Cuba</option>
                                        <option value="Curaçao">Curaçao</option>
                                        <option value="Cyprus">Cyprus</option>
                                        <option value="Czech Republic">Czech Republic</option>
                                        <option value="Denmark">Denmark</option>
                                        <option value="Djibouti">Djibouti</option>
                                        <option value="Dominica">Dominica</option>
                                        <option value="Dominican Republic">Dominican Republic</option>
                                        <option value="Ecuador">Ecuador</option>
                                        <option value="Egypt">Egypt</option>
                                        <option value="El Salvador">El Salvador</option>
                                        <option value="Equatorial Guinea">Equatorial Guinea</option>
                                        <option value="Eritrea">Eritrea</option>
                                        <option value="Estonia">Estonia</option>
                                        <option value="Ethiopia">Ethiopia</option>
                                        <option value="Falkland Islands (Malvinas)">Falkland Islands (Malvinas)</option>
                                        <option value="Faroe Islands">Faroe Islands</option>
                                        <option value="Fiji">Fiji</option>
                                        <option value="Finland">Finland</option>
                                        <option value="France">France</option>
                                        <option value="French Guiana">French Guiana</option>
                                        <option value="French Polynesia">French Polynesia</option>
                                        <option value="French Southern Territories">French Southern Territories</option>
                                        <option value="Gabon">Gabon</option>
                                        <option value="Gambia">Gambia</option>
                                        <option value="Georgia">Georgia</option>
                                        <option value="Germany">Germany</option>
                                        <option value="Ghana">Ghana</option>
                                        <option value="Gibraltar">Gibraltar</option>
                                        <option value="Greece">Greece</option>
                                        <option value="Greenland">Greenland</option>
                                        <option value="Grenada">Grenada</option>
                                        <option value="Guadeloupe">Guadeloupe</option>
                                        <option value="Guam">Guam</option>
                                        <option value="Guatemala">Guatemala</option>
                                        <option value="Guernsey">Guernsey</option>
                                        <option value="Guinea">Guinea</option>
                                        <option value="BissauGuinea-Bissau">BissauGuinea-Bissau</option>
                                        <option value="Guyana">Guyana</option>
                                        <option value="Haiti">Haiti</option>
                                        <option value="Heard Island and McDonald Islands">Heard Island and McDonald Islands</option>
                                        <option value="Holy See (Vatican City State)">Holy See (Vatican City State)</option>
                                        <option value="Honduras">Honduras</option>
                                        <option value="Hong Kong">Hong Kong</option>
                                        <option value="Hungary">Hungary</option>
                                        <option value="Iceland">Iceland</option>
                                        <option value="India">India</option>
                                        <option value="Indonesia">Indonesia</option>
                                        <option value="Iran, Islamic Republic of">Iran, Islamic Republic of</option>
                                        <option value="Iraq">Iraq</option>
                                        <option value="Ireland">Ireland</option>
                                        <option value="im">Isle of Man</option>
                                        <option value="Israel">Israel</option>
                                        <option value="Italy">Italy</option>
                                        <option value="Jamaica">Jamaica</option>
                                        <option value="Japan">Japan</option>
                                        <option value="Jersey">Jersey</option>
                                        <option value="Jordan">Jordan</option>
                                        <option value="Kazakhstan">Kazakhstan</option>
                                        <option value="Kenya">Kenya</option>
                                        <option value="Kiribati">Kiribati</option>
                                        <option value="Korea, Democratic People Republic of">Korea, Democratic People Republic of</option>
                                        <option value="Korea, Republic of">Korea, Republic of</option>
                                        <option value="Kuwait">Kuwait</option>
                                        <option value="Kyrgyzstan">Kyrgyzstan</option>
                                        <option value="Lao People Democratic Republic">Lao People Democratic Republic</option>
                                        <option value="Latvia">Latvia</option>
                                        <option value="Lebanon">Lebanon</option>
                                        <option value="Lesotho">Lesotho</option>
                                        <option value="Liberia">Liberia</option>
                                        <option value="Libya">Libya</option>
                                        <option value="Liechtenstein">Liechtenstein</option>
                                        <option value="Lithuania">Lithuania</option>
                                        <option value="Luxembourg">Luxembourg</option>
                                        <option value="Macao">Macao</option>
                                        <option value="Madagascar">Madagascar</option>
                                        <option value="Malawi">Malawi</option>
                                        <option value="Malaysia">Malaysia</option>
                                        <option value="Maldives">Maldives</option>
                                        <option value="Mali">Mali</option>
                                        <option value="Malta">Malta</option>
                                        <option value="Marshall Islands">Marshall Islands</option>
                                        <option value="Martinique">Martinique</option>
                                        <option value="Mauritania">Mauritania</option>
                                        <option value="Mauritius">Mauritius</option>
                                        <option value="Mayotte">Mayotte</option>
                                        <option value="Mexico">Mexico</option>
                                        <option value="Micronesia Federated States of">Micronesia Federated States of</option>
                                        <option value="Moldova Republic of">Moldova Republic of</option>
                                        <option value="Monaco">Monaco</option>
                                        <option value="Mongolia">Mongolia</option>
                                        <option value="Montenegro">Montenegro</option>
                                        <option value="Montserrat">Montserrat</option>
                                        <option value="Morocco">Morocco</option>
                                        <option value="Mozambique">Mozambique</option>
                                        <option value="Myanmar">Myanmar</option>
                                        <option value="Namibia">Namibia</option>
                                        <option value="Nauru">Nauru</option>
                                        <option value="Nepal">Nepal</option>
                                        <option value="Netherlands">Netherlands</option>
                                        <option value="New Caledonia">New Caledonia</option>
                                        <option value="New Zealand">New Zealand</option>
                                        <option value="Nicaragua">Nicaragua</option>
                                        <option value="Niger">Niger</option>
                                        <option value="Nigeria">Nigeria</option>
                                        <option value="Niue">Niue</option>
                                        <option value="Norfolk Island">Norfolk Island</option>
                                        <option value="Northern Mariana Islands">Northern Mariana Islands</option>
                                        <option value="Norway">Norway</option>
                                        <option value="Oman">Oman</option>
                                        <option value="Pakistan">Pakistan</option>
                                        <option value="Palau">Palau</option>
                                        <option value="ps">Palestine, State of</option>
                                        <option value="Panama">Panama</option>
                                        <option value="Papua New Guinea">Papua New Guinea</option>
                                        <option value="Paraguay">Paraguay</option>
                                        <option value="Peru">Peru</option>
                                        <option value="Philippines">Philippines</option>
                                        <option value="Pitcairn">Pitcairn</option>
                                        <option value="Poland">Poland</option>
                                        <option value="Portugal">Portugal</option>
                                        <option value="Puerto Rico">Puerto Rico</option>
                                        <option value="Qatar">Qatar</option>
                                        <option value="Republic of Macedonia">Republic of Macedonia</option>
                                        <option value="Réunion">Réunion</option>
                                        <option value="Romania">Romania</option>
                                        <option value="Russian Federation">Russian Federation</option>
                                        <option value="Rwanda">Rwanda</option>
                                        <option value="Saint Barthélemy">Saint Barthélemy</option>
                                        <option value="Saint Helena Ascension and Tristan da Cunha">Saint Helena Ascension and Tristan da Cunha</option>
                                        <option value="Saint Kitts and Nevis">Saint Kitts and Nevis</option>
                                        <option value="Saint Lucia">Saint Lucia</option>
                                        <option value="Saint Martin (French part)">Saint Martin (French part)</option>
                                        <option value="Saint Pierre and Miquelon">Saint Pierre and Miquelon</option>
                                        <option value="Saint Vincent and the Grenadines">Saint Vincent and the Grenadines</option>
                                        <option value="Samoa">Samoa</option>
                                        <option value="San Marino">San Marino</option>
                                        <option value="Sao Tome and Principe">Sao Tome and Principe</option>
                                        <option value="Saudi Arabia">Saudi Arabia</option>
                                        <option value="Senegal">Senegal</option>
                                        <option value="Serbia">Serbia</option>
                                        <option value="Seychelles">Seychelles</option>
                                        <option value="Sierra Leone">Sierra Leone</option>
                                        <option value="Singapore">Singapore</option>
                                        <option value="Sint Maarten (Dutch part)">Sint Maarten (Dutch part)</option>
                                        <option value="Slovakia">Slovakia</option>
                                        <option value="Slovenia">Slovenia</option>
                                        <option value="Solomon Islands">Solomon Islands</option>
                                        <option value="Somalia">Somalia</option>
                                        <option value="South Africa">South Africa</option>
                                        <option value="South Georgia and the South Sandwich Islands">South Georgia and the South Sandwich Islands</option>
                                        <option value="South Sudan">South Sudan</option>
                                        <option value="Spain">Spain</option>
                                        <option value="Sri Lanka">Sri Lanka</option>
                                        <option value="Sudan">Sudan</option>
                                        <option value="Suriname">Suriname</option>
                                        <option value="Svalbard and Jan Mayen">Svalbard and Jan Mayen</option>
                                        <option value="Swaziland">Swaziland</option>
                                        <option value="Sweden">Sweden</option>
                                        <option value="Switzerland">Switzerland</option>
                                        <option value="Syrian Arab Republic">Syrian Arab Republic</option>
                                        <option value="Taiwan">Taiwan</option>
                                        <option value="Tajikistan">Tajikistan</option>
                                        <option value="Tanzania United Republic of">Tanzania United Republic of</option>
                                        <option value="Thailand">Thailand</option>
                                        <option value="Timor-Leste">Timor-Leste</option>
                                        <option value="Togo">Togo</option>
                                        <option value="Tokelau">Tokelau</option>
                                        <option value="Tonga">Tonga</option>
                                        <option value="Trinidad and Tobago">Trinidad and Tobago</option>
                                        <option value="Tunisia">Tunisia</option>
                                        <option value="Turkey">Turkey</option>
                                        <option value="Turkmenistan">Turkmenistan</option>
                                        <option value="Turks and Caicos Islands">Turks and Caicos Islands</option>
                                        <option value="Tuvalu">Tuvalu</option>
                                        <option value="Uganda">Uganda</option>
                                        <option value="Ukraine">Ukraine</option>
                                        <option value="United Arab Emirates">United Arab Emirates</option>
                                        <option value="United Kingdom">United Kingdom</option>
                                        <option value="United States">United States</option>
                                        <option value="United States Minor Outlying Islands">United States Minor Outlying Islands</option>
                                        <option value="Uruguay">Uruguay</option>
                                        <option value="Uzbekistan">Uzbekistan</option>
                                        <option value="Vanuatu">Vanuatu</option>
                                        <option value="Venezuela Bolivarian Republic of">Venezuela Bolivarian Republic of</option>
                                        <option value="Viet Nam">Viet Nam</option>
                                        <option value="Virgin Islands British">Virgin Islands British</option>
                                        <option value="Virgin Islands U.S.">Virgin Islands U.S.</option>
                                        <option value="Wallis and Futuna">Wallis and Futuna</option>
                                        <option value="Western Sahara">Western Sahara</option>
                                        <option value="Yemen">Yemen</option>
                                        <option value="Zambia">Zambia</option>
                                        <option value="Zimbabwe">Zimbabwe</option>
                                    </select>
				</div>
				<div class="form-group">
					<input type="text" class="form-control" name="upd_street" placeholder="Street Address">
				</div>
				<div class="form-group">
					<select  name="upd_city" placeholder="City"  class="form-control" placeholder="Business Location">
								<?php
								echo $citynames;
								?></select>  
				</div>
				<div class="form-group">
					 <input type="text" class="form-control" name="upd_zip" placeholder="Zip">
				</div>
				 
				
				
			</div>		
			<div class="col-xs-12 col-sm-12 col-md-6"> 
				<h5>Account Management</h5>
				<div class='row'>
				<div class="col-xs-12 col-sm-12 col-md-8"> 
					<div class="form-group">
						<input type="text" class="form-control" name="upd_email" placeholder="Email">  
					</div>
				</div>
				<div class="col-xs-12 col-sm-12 col-md-4"> 
					<div class="form-group"> 
						<button class="btn btn-primary btn-sm btnblock changePass">Change Password</button>
					</div>
				</div>
				</div>
				
				 <div class="form-group" style="display: none;" data-type="changePass">
				 <input type="password" class="form-control" name="old_pass" placeholder="Old password">
				 </div>
				 <div class="form-group" style="display: none;" data-type="changePass">
				 <input type="password" class="form-control" name="new_pass" placeholder="New password">
				 </div>
				 <div class="form-group" style="display: none;" data-type="changePass">
				 <button class="btn btn-primary savePass">Update password</button>
				 </div>
				<h5>LinkedIn URL</h5>
				<div class="form-group">
					<input type="text" class="form-control" value='<?php echo $row->linkedin_profile ;?>' name="linkedin_profile" placeholder="LinkedIn URL">  
				</div>
			</div> 
			</div>
			
			<div class='row'> 
			<div class="col-xs-12 col-sm-12 col-md-6"> 
				<h5>Make Profile</h5>
				<div class="form-group">
					Public <input type="radio" id="upd_public" style="display:inline" name="upd_public_private"  value="public" > 
					Private <input type="radio" id="upd_private" style="display:inline" name="upd_public_private" value="private" >
				</div>
			</div>
			<div class="col-xs-12 col-sm-12 col-md-6"> 
				<h5>Reminder Email</h5>
				<div class="form-group">
					Yes <input type="radio" id="upd_reminder_yes" style="display:inline" name="upd_reminder_email"  value="yes" > 
					No <input type="radio" id="upd_reminder_no" style="display:inline" name="upd_reminder_email" value="no" >
				</div>
			</div>
			</div>
			<div class='row'>  
			<div class="col-xs-12 col-sm-12 col-md-12"> 
			 <?php  
				 if ($_user_role == 'admin') :
			 ?>
				<h5>Add Tags</h5>
				<div class="form-group">
					<select data-placeholder='Specify Tags ...'  multiple  name='member_tags'  class='form-control chosen-select member_tags'>
					<?php  
						foreach ($alltags as $tagitem)
						{
							   
							echo "<option  value='" . $tagitem['tagname'] . "'>" . $tagitem['tagname'] . "</option>"; 
						} 
					  ?>
					 </select>
				</div>
			<?php  
				endif;
			 ?>	
				<h5>About YourSelf</h5>
				<div class="form-group">
					<textarea type="text" style="height: 150px!important;" class="form-control" name="about_your_self" placeholder="Please start writing.."></textarea>
				</div>
				<h5>Professional Settings</h5> 
				<div class="form-group">
					<label for="city_names">Your City(s)</label> 
					<div class="form-control hidedthead" style="height: 150px !important; overflow-y: auto; overflow-x: hidden;"> 
						<table id="city_names"  style="width:100%">
							<thead>
								<tr><th>City</th> </tr>
							</thead>
							<tbody>
								<?php
								foreach ($getGroups as $item) {
									echo "<tr><td><input type='checkbox' name='upd_usergrp' value='" . $item['id'] . "'/> " . $item['name'] . "</td></tr>";
								}
								?>  
							</tbody> 
						</table> 
					</div> 
				</div>
				<div class="form-group">
					<label for="vocation_names">Your Vocation(s)</label>
					<div class="form-control hidedthead" style="height: 150px !important; overflow-y: auto; overflow-x: hidden;"> 
						<table id="vocation_names"  style="width:100%">
							<thead>
								<tr><th>Your vocations</th> </tr>
							</thead>
							<tbody>
								<?php
								foreach ($vocations as $vocation) {
									echo "<tr><td><input type='checkbox' name='upd_uservoc' value='" . $vocation['name'] . "'/> " . $vocation['name'] . "</td></tr>";
								}
								?>  
							</tbody> 
						</table> 
					</div> 
				</div>
				<div class="form-group">
					<label for="targetclient_names">Target Client(s)</label>
					<div class="form-control hidedthead" style="height: 150px !important; overflow-y: auto; overflow-x: hidden;"> 
						<table id="targetclient_names"  style="width:100%">
							<thead>
								<tr><th>Target Client</th> </tr>
							</thead>
							<tbody>
								<?php
								foreach ($vocations as $vocation) {
									echo "<tr><td><input type='checkbox' name='upd_usertarget' value='" . $vocation['name'] . "'/> " . $vocation['name'] . "</td></tr>";
								}
								?>  
							</tbody> 
						</table> 
					</div> 
					 
				</div>
				<div class="form-group">
					<label for="targetref_names">Target Referral Partner(s)</label>
					<div class="form-control hidedthead" style="height: 150px !important; overflow-y: auto; overflow-x:hidden">
                        <table id="targetref_names"  style="width:100%">
							<thead>
								<tr><th>Target Referral Partner(s)</th></tr>
							</thead>
							<tbody>
								<?php 
								foreach ($vocations as $vocation)
								{
									echo "<tr><td><input type='checkbox' name='upd_usertargetreferral' value='" . $vocation['name'] . "'/> " . $vocation['name'] .  "</td></tr>";
                                }
								?>  
							</tbody> 
						</table> 
					</div> 
				</div>
				 
			</div>
			
			<div class="col-xs-12 col-sm-12 col-md-12"> 
				<h5>Business Information</h5>
				<div class="form-group">
					<select name="membertype_edit" class="form-control" data-placeholder="Business" data-class="form-large"
                                tabindex="-1" aria-hidden="true">
                            <option selected disabled="disabled" value="null">- Select User Type -</option>
                            <option value="1">Business Information</option> 
                        </select> 
				</div>
				<div class="form-group">
					<input name="busi_name_edit" disabled="disabled" type="text" class="form-control" placeholder="Business Name" value=""> 
				</div>
				<div class="form-group">
					<input name="busi_location_street_edit" disabled="disabled" type="text" class="form-control" placeholder="Street Address" value="">  
				</div>
				<div class="form-group">
					<select name="busi_location_edit" disabled="disabled"  class="form-control" placeholder="Business Location">
					<?php
					echo $citynames;
					?></select> 
				</div>
				<div class="form-group">
					<select  name="busi_type_edit" disabled="disabled"  class="form-control" placeholder="Business Type">
						<?php echo $vocaoptions; ?>
						</select>
				</div>
				<div class="form-group">
					<input name="busi_hours_edit" disabled="disabled" type="text" class="form-control" placeholder="Business Hours" value=""> 
				</div>
				<div class="form-group">
					<input name="busi_website_edit"  disabled="disabled" type="text" class="form-control" placeholder="Website" value=""> 
				</div>
			 
				
				  </div>
			</div>
			</div>
			  
 
            </div>
            <div class="modal-footer">
                <div class="col-xs-12">
                    <button class="btn btn-primary btnblock updateUserProf">Save changes</button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="myModal" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header ">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title">Name: </h4>
            </div>
            <div class="modal-body text-left">
                <small>Your name</small>
                <input type="text" class="form-control" id="sender_name" placeholder="" readonly="readonly" required="" value="<?php echo $username ?>">
                <small>Your email</small>
                <input type="email" class="form-control" id="sender_email" placeholder="" readonly="readonly" required="" value="<?php echo $user_email ?>">
                <small>Message</small>
                <textarea id="sender_msg" class="form-control" cols="30" rows="4"
                          placeholder="The vocation and the rating of their person you would like an  introduction.."></textarea>
            </div>
            <div class="modal-footer">
                <div class="col-xs-12">
                    <small><span>Why you looking for?</span></small>
                    <br>
                </div>
                <div class="col-xs-12">
                    <button class="btnblock leaveUserMsg">SUBMIT NOW</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="edit_package" tabindex="-1" role="dialog" data-id="0">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header ">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Add New Package</h4>
            </div>
            <div class="modal-body text-left">

                <small>Package Name</small>
                <input type="text" class="form-control" name="package_name" placeholder="Package name">

                <small>Price/mo</small>
                <div class="clearfix"></div>
                <div class="col-sm-1 padd-3 text-center" style="font-size: 25px">$</div>
                <div class="col-sm-11 no-padd">
                    <input type="text" class="form-control" name="package_price" placeholder="e.g. 99.99">
                </div>
                <div class="clearfix"></div>

                <small>Minimum Package Purchasing Limit</small>
                <select type="text" class="form-control" name="package_dur">
                    <?php
                    for ($i = 0; $i <= 12; $i++) {
                        echo "<option value='$i'>$i Month(s)</option>";
                    }
                    ?>
                </select>

                <div>
                    <small>Referrals Sharing Limit/mo</small>
                    <div class="form-group">
                        <div class="col-sm-3">
                            <input type="text" class="form-control" name="ref_sh_conn" placeholder="Type 0 for unlimited" data-toggle="tooltip" title="Type 0 for unlimited">
                        </div>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" name="ref_sh_conn_desc" placeholder="Type description...">
                        </div>
                    </div>
                </div> 
                <div>
                    <small>Referrals Adding Limit/mo</small>
                    <div class="form-group">
                        <div class="col-sm-3">
                            <input type="text" class="form-control" name="ref_conn" placeholder="Type 0 for unlimited" data-toggle="tooltip" title="Type 0 for unlimited">
                        </div>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" name="ref_conn_desc" placeholder="Type description...">
                        </div>
                    </div>
                </div>

                <div>
                    <small>Targeted Connections</small>
                    <div class="form-group">
                        <div class="col-sm-3">
                            <input type="text" class="form-control" name="tar_conn" placeholder="Type 0 for unlimited" data-toggle="tooltip" title="Type 0 for unlimited">
                        </div>
                        <div class="col-sm-9"><input type="text" class="form-control" name="tar_conn_desc" placeholder="Type description..."></div>
                    </div>
                </div>

                <div class="services">
                    <small>Package Services (leave empty if no service)</small>
                    <div class="form-group">
                        <div class="col-sm-11 padd-5">
                            <input name="package_services" class="form-control" placeholder="Package service"/>
                        </div>
                        <div class="col-sm-1 padd-5">
                            <button class="fa fa-plus btn btn-default addNewService"></button>
                        </div>
                    </div>
                </div>
                <div class="clearfix"></div>
            </div>
            <div class="modal-footer">
                <div class="col-xs-12">
                    <button class="btnblock savePkgDetails">SUBMIT NOW</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="editContent" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header ">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Edit Page</h4>
            </div>
            <div class="modal-body text-left">
                <input type="text" class="form-control" placeholder="Content Title" name="about_title_ed">
                <textarea name="about_content_ed" id="" cols="30" rows="10" class="form-control" placeholder="Content"></textarea>
            </div>
            <div class="modal-footer">
                <div class="col-xs-12">
                    <button class="btnblock updPgContent">Save changes</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</div>


<!--Profile pic update-->
<div class="modal fade" id="changepicture" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="dashboard.php" method="post" enctype="multipart/form-data">
			<div class="modal-header ">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title">Update Profile Picture</h4>
            </div>
            <div class="modal-body text-left">
				  <small>Select Image:</small>
                <input type="file" name="prof_img" required="" >
            </div>
            <div class="modal-footer">
                
                <div class="col-xs-12">
                    <button class="btn btn-default btnblock" type="submit" name="upload_btn">Upload Now</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
			</form>
        </div>
    </div>
</div>
<!--Profile pic update--> 
<!--Profile pic update-->
<div class="modal fade" id="changememberpicture" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            
			<div class="modal-header ">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title">Update Profile Picture</h4>
            </div>
            <div class="modal-body ">
				<div class='row'>
				<div class="col-xs-12 col-md-4"> 
					<h4>Member Photo</h4>
					<div id='curmemphoto'></div>
				 </div>
				<div class="col-xs-12 col-md-8"> 
					<h4>Drag &amp; Drop New Member Photo</h4>
					 <form  action="includes/profilephotoupload.php" class="dropzone" id="memberprofileimage">
					 <div class="dz-message" data-dz-message><span>Upload Profile Picture</span></div>
					 
					 <input type="hidden" id='hidmid' name='hidmid' />  

					 </form> 
				</div>	 </div>
            </div>
            <div class="modal-footer">
                
                <div class="col-xs-12">
                    <button class="btn btn-primary btnblock" type="submit" id="btnupdatememphoto">Update Profile Photo</button>
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                </div>
            </div>
			 
        </div>
    </div>
</div>
<!--Profile pic update--> 


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
                            foreach ($vocations as $vocation) {
                                echo "<option value='" . $vocation['name'] . "'>" . $vocation['name'] . "</option>";
                            }
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
<?php if ($_user_role == 'admin') { ?>
    <div class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel"
         id="userModal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">User Details</h4>
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
					<div class="userDetails">
                        
					</div>
                    </div>
                </div>
            </div>
        </div>
    </div> 
    <div class="modal fade  modallistconnects" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel"
         id="modallistconnects">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title">Referral Connection Lists</h4>
                </div>
                <div class="modal-body" style='max-height: 520px; overflow-y:scroll; text-align:left'>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Reference Name</th>
                                    <th>Vocation</th>
                                    <th>Contact Info</th> 
                                    <th>Location</th>  
                                </tr>
                            </thead>
                            <tbody class="connectionlists"></tbody>
                        </table>
                    </div>
                </div>
				 <div class="modal-footer" >
				 </div>
            </div>
        </div>
    </div>  
<?php } ?> 
<div class="modal fade  modalreferalintrolist" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" id="modalreferalintrolist">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title">Referral Connection Lists</h4>
            </div>
            <div class="modal-body" style='max-height: 520px; overflow-y:scroll; text-align:left'>
                <div class="table-responsive">
                    <table class="table">
                           <thead>
                            <tr>
                                <th>Introducee</th>
                                <th>Introduction Receipent</th>
                                <th>Email Status</th>  
                            </tr>
                            </thead>
                            <tbody class="connectionlists"></tbody>
                        </table>
                    </div>
                </div>
				 <div class="modal-footer" >
				 </div>
            </div>
    </div>
</div> 
<div class="modal fade " tabindex="-1" role="dialog" aria-labelledby="reftrackermodal" id="reftrackermodal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Tracking Referrals</h4>
                </div>
                <div class="modal-body" style='max-height: 520px; overflow-y:scroll; text-align:left'>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                            <tr>
                                <th>Reference Name</th>
                                <th>Vocation</th>
                                <th>Contact Info</th> 
                                <th>Location</th>  
                            </tr>
                            </thead>
                            <tbody class="connectionlists"></tbody>
                        </table>
                    </div>
                </div>
				 <div class="modal-footer" >
				 </div>
            </div>
        </div>
</div>


<div class="modal fade" id="suggestedreferral" tabindex="-1" role="dialog" aria-labelledby="suggestedref">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="suggestedref">Referral Introduction</h4>
      </div>
      <div class="modal-body" id='suggreff' style='padding: 15px!important; height: 540px; overflow-y: scroll; '> 
		  <div class="form-group">
			 <h2>Contact ontroduction details</h2>
		  </div>
          <div class='row'>
          <div class='col-md-6'>
                <div class="panel panel-primary">
                <div class="panel-heading"><strong>Introducee</strong></div>
                <div class="panel-body">
                    <p><i class='fa fa-user dark'></i> <span id="spconnectname" ></span><br/>
                     <i class='fa fa-envelope dark'></i> <span id="spconnectemail" ></span><br/>
                     <i class='fa fa-phone dark'></i> <span id="spconnectphone" ></span><br/>
					 <i class='fa fa-briefcase  dark'></i> <span id="spconnectprofession" ></span>
					 </p>
 
					<input type='hidden' id="connectname"  />
                    <input type='hidden' id="connectemail" />
                    <input type='hidden' id="connectphone" />
					<input type="hidden"  id="connectprofession" value="">
			   </div>
                </div> 
            </div> 
            <div class='col-md-6'>
                <div class="panel panel-success">
                <div class="panel-heading"><strong>Contact Receipent</strong></div>
                <div class="panel-body">
                    <p id='introduceto' ></p>
                </div>
                </div> 
            </div> 
          </div>
          <div class='row'>
          <div class='col-md-12'>
            <h3 class=" text-center">Below are the available trigger mails. Select the one email</h3> 
            <br/><br/>
         </div></div>
          
            <?php
            
                $rowindex=1;
                echo '<div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">';
                $counter=1 ;
                if( sizeof($mailtemplates)  > 0)
                {
                    foreach ($mailtemplates as $item )
                    {
                        $mailbody = html_entity_decode(  $item['mailbody'] )  ;
                        if( strcasecmp( $item['mailtype']  , 'Introduction Mail' ) == 0 )
                        {
                            echo '<div class="panel panel-default panel-emailtemplates">
                                    <div class="panel-heading" role="tab" id="heading' . $counter .'">
                                        <h4 class="panel-title">
                                            <a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapse' . $counter .'" aria-expanded="true" aria-controls="collapse' . $counter .'">
                                                '. $item['template'] .'
                                            </a>
                                        </h4>
                                        </div>
                                        <div id="collapse' . $counter .'" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="heading' . $counter .'">
                                        <div class="panel-body">';
                                        $mailbody = str_replace("{receipent}","<span class='tplvar_receipent'>name</span>", $mailbody ) ;
                                        $mailbody = str_replace("{user}","<span class='tplvar_user'>" . $username  ."</span>", $mailbody ) ;
                                        $mailbody = str_replace("{rated_by}","<span class='tplvar_rated_by'>name</span>", $mailbody ) ;
                                        $mailbody = str_replace("{introducee}","<span class='tplvar_introducee'>introducee</span>", $mailbody ) ;
                                        $mailbody = str_replace("{introducee_profession}","<span class='tplvar_introducee_profession'>introducee_profession</span>", $mailbody ) ;
                                        $mailbody = str_replace("{introducee_email}","<span class='tplvar_introducee_email'>introducee_email</span>", $mailbody ) ;
                                        $mailbody = str_replace("{introducee_phone}","<span class='tplvar_introducee_phone'>introducee_phone</span>", $mailbody ) ;
                                        echo $mailbody . '<button data-tid="' . $item['id'] . '" type="button" data-dismiss="modal"  class="btn btn-success sendintromail">Send Mail</button>
                                        </div>
                                        </div>
                                    </div>'; 
                                   } 
                                    $counter++;
                                }
                            }  
                            else 
                                echo '<h4 class="text-center">No email template has been configured yet. Please contact admin!</h4>';  
                            
                                echo "</div>"; 
                         ?> 
 
      </div>
      <div class="modal-footer"> 
		<input type="hidden"  id="receipent" value=""/> 
		<input type="hidden"  id="receipentname" value=""/> 
		<input type="hidden"  id="receipentphone" value=""/> 
		<input type="hidden"  id="receipentprof" value=""/> 
		<input type="hidden"  id="suggestid" value=""/> 
		<input type='hidden' id='mailogid' value=''/>
		<input type='hidden' id='clientid' value=''/>
		<input type='hidden' id='cc1' value=''/>
		<input type='hidden' id='ccname1' value=''/> 
      </div>
    </div>
  </div>
</div> 

<div class="modal fade mine-modal" id="mailreader" tabindex="-1" role="dialog" aria-labelledby="mailreaderbox">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="suggestedref">Online Contact Message</h4>
      </div>
      <div class="modal-body" id='mailreaderbox'>
       
      </div>
      <div class="modal-footer">  
        <button type="button" data-dismiss="modal"  class="btn btn-danger">Close</button> 
      </div>
    </div>
  </div>
</div> 


<div class="modal fade mine-modal" id="refmailreader" tabindex="-1" role="dialog" aria-labelledby="refmailreaderbox">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="suggestedref">Mail Content</h4>
      </div>
      <div class="modal-body text-left" id='refmailreaderbox'>
       
      </div>
      <div class="modal-footer">  
        <button type="button" data-dismiss="modal"  class="btn btn-danger">Close</button> 
      </div>
    </div>
  </div>
</div> 
 
<div class="modal fade data-processiing" id="processing" tabindex="-1" role="dialog" aria-labelledby="systembusy">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        
        <h4 class="modal-title text-center" id="suggestedref">Regenerating your connects. Pleat wait ...</h4>
      </div>
      <div class="modal-body" id='systembusy'>
       	<img src='<?php echo $siteurl;?>images/processing.gif' alt='Please wait ...'/>
      </div> 
    </div>
  </div>
</div> 


<div class="modal fade data-processiing" id="waitmodal" tabindex="-1" role="dialog" aria-labelledby="systembusy">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        
        <h4 class="modal-title text-center" id="waitmodaltitle">Pleat wait ...</h4>
      </div>
      <div class="modal-body" id='systembusy'>
	  <div class='row'>
	  <div class='col-md-4'>
       	<img src='<?php echo $siteurl;?>images/processing.gif' alt='Please wait ...'/>
	  </div>
	  <div class='col-md-8'>
          <div class='waitmsg'></div>
	  </div>
	  </div>
      </div> 
    </div>
  </div>
</div> 

 
<div class="modal fade suggestconnectmodal" tabindex="-1" role="dialog" aria-labelledby="suggestconnectmodal"
         id="modallistconnects">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span></button>
                    <h2 class="modal-title" >Referral Suggestion for <span id='cname'></span></h2>
					<small   id='cprofession'></small>
                </div>
                <div class="modal-body modal-body-no-pad" style='max-height: 520px; overflow-y:scroll; text-align:left'> 
					<div class='track_dashboard'>
					</div>
                </div>
				 <div class="modal-footer" >
				 </div>
            </div>
        </div>
</div>
<div class="modal fade suggestconnectmodal" tabindex="-1" role="dialog" aria-labelledby="suggestwizard" id="suggestwizard">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span></button>
                    <h2 class="modal-title" >Referral Suggestion Wizard</h2> 
                </div>
                <div class="modal-body modal-body-no-pad" style='height: 450px; overflow-y:scroll;text-align:left'>
					<div class='bs-wizard'>
						<div id='wizstep1' class="col-xs-4 bs-wizard-step disabled">
						  <div class="text-center bs-wizard-stepnum">Step 1</div>
						  <div class="progress"><div class="progress-bar"></div></div>
						  <a href="#" class="bs-wizard-dot"></a>
						  <div class="bs-wizard-info text-center">Search member by vocations</div>
						  <div class="form-group pad10  ">
							<select data-placeholder='Vocation' class="form-control wiz_profession" name="wiz_profession" id="wiz_profession" >
                                <?php
                                foreach ($vocations as $vocation)
                                {
									echo "<option value='" . $vocation['name'] . "'>" . $vocation['name'] . "</option>";
                                }
                                ?>
							</select>
						</div>
			<div class="form-group pad10  ">
			   <button class='btn btn-success btn-sm wiz_step1_show_member'>Show Connections</button>
		    </div>
		</div> 
        <div id='wizstep2' class="col-xs-4 bs-wizard-step disabled"><!-- complete -->
			<div class="text-center bs-wizard-stepnum">Step 2</div>
				<div class="progress"><div class="progress-bar"></div></div>
                    <a href="#" class="bs-wizard-dot"></a>
                    <div class="bs-wizard-info text-center">Select person to introduce</div>
                    <div class="form-group pad10">
				<select   class="form-control wiz_memberleft" id='wiz_memberleft'  name="wiz_memberleft">
				</select>
			</div>
		</div> 
			<div id='wizstep3' class="col-xs-4 bs-wizard-step disabled">
				<div class="text-center bs-wizard-stepnum">Step 3</div>
				<div class="progress"><div class="progress-bar"></div></div>
					<a href="#" class="bs-wizard-dot"></a>
				<div class="bs-wizard-info text-center">Select member who will receive introduction</div>
				<div class="form-group pad10"> 
					<input class="form-control wiz_memberright" id="provider-remote" />  
				</div>
				<div class="form-group pad10  ">
					<input type='hidden' id="rmid" /> 
					<input type='hidden' class="refereruid" />  
					<input type='hidden' class="referername" />  
					<input type='hidden' class="refereremail" />  
					<input type='hidden' class="refererrole" />  					
					<button data-rightid='' class='btn btn-success btn-sm wiz_step_show_summary'>Show Referral Summary</button>
				</div>
			</div> 
		</div> 
		<div class='text-center'>
			<span class='alertinfofix ref_directtodirectwizard'>If you know the person to introduce, switch to Direct to Direct Referral Wizard</span> 
		</div>
	   <div id='wiz_summary'></div> 
	</div> 
	<div class="modal-footer clearfix" >
			<button data-dismiss="modal"  class='btn btn-primary'>Cancel</button>
		</div> 
	   </div>
    </div>
</div> 
 
<div class="modal fade onetooneintroduction" tabindex="-1" role="dialog" aria-labelledby="onetooneintroduction" id="onetooneintroduction">
         <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span></button>
                    <h2 class="modal-title" >Direct to Direct Introduction Wizard</h2> 
                </div>
                <div class="modal-body modal-body-no-pad" style='height: 450px; overflow-y:scroll; text-align:left'>
					<div class='bs-wizard'>
						<div id='wizstep1' class="col-xs-6 bs-wizard-step disabled">
						 
						  <div class="bs-wizard-info text-center"><h5>Search member to introduce</h5></div>
						  <div class="form-group pad10  "> 
						  <input class="form-control dwiz_memberleft" id="dtdleftmember" />  
						</div> 
		</div>  
			<div id='wizstep3' class="col-xs-6 bs-wizard-step disabled">  
				<div class="bs-wizard-info text-center"><h5>Select member who will receive introduction</h5></div>
				<div class="form-group pad10"> 
					<input class="form-control dwiz_memberright" id="dtdrightmember" />  
				</div>
				<div class="form-group pad10  ">
				<input type='hidden' id="lmid" /> 
					<input type='hidden' id="dtdlmid" /> 
					<input type='hidden' id="dtdrmid" /> 
					
				</div>
			</div> 
			
	   <div class='text-center'>
				<button data-rightid='' class='btn btn-success btn-sm dwiz_step_show_summary'>Show Referral Summary</button><br/>
		</div> 
		</div> 
		<div class='text-center'>
			<span class='alertinfofix  ref_wizard'  >If you want to search know by vocation first, switch to Referral Suggestion Wizard</span>
			 
		</div>
	   <div id='dtdwiz_summary'></div> 
	</div>
		<div class="modal-footer clearfix" >
			<button data-dismiss="modal"  class='btn btn-primary'>Cancel</button>
		</div> 
	   </div>
    </div>
</div>  

 
<div class="modal fade suggest_wiz_3" tabindex="-1" role="dialog" aria-labelledby="suggest_wiz_3" id="suggest_wiz_3">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span></button>
                    <h2 class="modal-title" >Rated 6 Referral Suggestion</h2> 
                </div>
                <div class="modal-body modal-body-no-pad" style='height: 450px; overflow-y:scroll;text-align:left'>
					<div class='bs-wizard'>
					<div id='wiz_list' class="col-xs-12">
					</div>
					
		 
					<input type='hidden' id="rated6_lmid" />  
					<input type='hidden' class="refereruid" />  
					<input type='hidden' class="referername" />  
					<input type='hidden' class="refereremail" />  
					<input type='hidden' class="refererrole" />  					
				 
		</div> 
		 
	   <div id='wiz_rated6_summary'></div> 
	</div> 
	<div class="modal-footer clearfix" >
			<button data-dismiss="modal"  class='btn btn-primary'>Cancel</button>
		</div> 
	   </div>
    </div>
</div>


<div class="modal fade rated6_intromail" 
tabindex="-1" role="dialog" 
aria-labelledby="rated6_intromail" id="rated6_intromail">
	<div class="modal-dialog modal-lg"  >
			 <div class="modal-content">
			 <div class="modal-header">
			 <button type="button" class="close" data-dismiss="modal" aria-label="Close">
			 <span aria-hidden="true">&times;</span></button>
			 <h2 class="modal-title" >Email Preview</h2> 
			 </div>
			 <div class="modal-body text-left " style="height: 360px; overflow-y:scroll"  >
			<div style="visibility: hidden; display: none;" id="rated6_mailbody"></div>
			<div id="rated6_wiz_emailbody" class="rated6_wiz_emailbody"></div> 
			</div>
			<div class="modal-footer clearfix" >
			<button   class="btn btn-primary wiz_rated6_send_referral_mail" >Send Mail</button>
			<button data-dismiss="modal"  class="btn btn-danger" >Cancel</button>
			</div>
			</div>
		</div>
	</div>
</div>
 


<div class="modal fade" id="editinvitemailtemplate" tabindex="-1" role="dialog" aria-labelledby="editinvitemailtemplate" >
    <div class="modal-dialog ">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Edit Invite Mail Body</h4>
            </div>
            <div class="modal-body text-left mailpreview">
              <textarea name="previewinvitemail" id="previewinvitemail"></textarea> 
            </div>
            <div class="modal-footer ">
                <button class="btn btn-success" id="btnsaveinvitemail">Update</button>
            </div> 
          </div>
        </div>
</div>


<div class="modal fade" id="composedirectmodal" tabindex="-1" role="dialog" aria-labelledby="composedirectmodal" >
    <div class="modal-dialog  modal-lg">
        <div class="modal-content">
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
						<input  class="form-control directmailsubject" id='membermailsubject'  placeholder="Subject">
						<br/>
						<label>Email Body:</label>	
						<textarea name="previewdirectmail" id="previewdirectmail"></textarea> 
					</div>
				</div>	 
            </div>
            <div class="modal-footer ">
                <button class="btn btn-success" id="btnsenddirectemail">Send</button>
            </div> 
          </div>
        </div>
</div>


<div class="modal fade" id="composeinvitemail" tabindex="-1" role="dialog" aria-labelledby="composeinvitemail" >
	<div class="modal-dialog  modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
				<span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Compose Email</h4>
            </div>
            <div class="modal-body text-left mailpreview">
				<div class='row'>
					<div class='col-md-5'> 
						<h3>Know Profile</h3>
						<div id='knowprofilesummary' class='globalsearch'  ></div>
					</div>
					<div class='col-md-7'> 
						<h3>Compose Email</h3> 
						<label>Subject:</label>
						<input  class="form-control knowinvitemailsubject" id='knowinvitemailsubject' placeholder="Subject" value='Claim your MyCity.com Profile'>
						<br/>
						<label>Compose Email:</label>	
						<textarea name="knowinviteemail" id="knowinviteemail"></textarea> 
					</div>
				</div>	 
            </div>
            <div class="modal-footer ">
                <button class="btn btn-success" id="btnsendclaimprofile">Send</button>
            </div> 
          </div>
        </div>
</div> 


<div class="modal fade" id="commonconnects" tabindex="-1" role="dialog" aria-labelledby="commonconnects" >
    <div class="modal-dialog modal-lg">
        <div class="modal-content" style='height: 600px; overflow-y:none;'>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Common Connections</h4>
            </div>
            <div class="modal-body text-left "> 
              <div class='table-upper  ' style='height: 200px; overflow-y:scroll; border: 1px solid #efefef; margin-bottom: 10px'>
					<h5 class='hd-sm'>Members &amp; Common Connections Summary</h5> 
					<div class='cctable' ></div>
			  </div> 
			  <div class='table-upper  ' style='height: 220px; overflow-y:scroll; border: 1px solid #efefef; '>
					<h5 class='hd-sm'>Common Connections Summary</h5> 
					<div class='ccviewtable' ></div>
			  </div>
            </div>
            <div class="modal-footer ">
                <button class="btn btn-success" data-dismiss="modal" aria-label="Close" >Close</button>
            </div> 
          </div>
        </div>
</div>


<div class='modal' id='previewinviteemail' tabindex='-1' role='dialog' aria-labelledby='previewinviteemail' >
		<div class='modal-dialog modal-lg'>
		<div class='modal-content'>
		<div class='modal-header'>
		<button type='button' class='close' data-dismiss='modal' aria-label='Close'>
                <span aria-hidden='true'>&times;</span></button>
              <h4 class='modal-title'>Mail Preview</h4>
           </div>
           <div class='modal-body text-left mailpreview'>
          <div class='row'>
                <div class='col-md-4'>
                    <label>Receipent:</label> 
                   <input class='form-control' readonly id='eptbreceipent' />
                    <label>Receipent Email:</label> 
                  <input class='form-control' readonly id='eptbreceipentemail' />  
             </div>
                <div class='col-md-8'> 
                  <textarea name='mailpreview' id='mailpreview'></textarea>
               </div>
          </div>  
           </div>
           <div class='modal-footer'>
              <button class='btn btn-success' id='btnsendinvitemail'>Send</button>
           </div> 
         </div>
       </div>
</div> 

<div class='modal' id='memberratingmodal' tabindex='-1' role='dialog' aria-labelledby='memberratingmodal' >
		<div class='modal-dialog '>
		<div class='modal-content'>
		<div class='modal-header'>
		<button type='button' class='close' data-dismiss='modal' aria-label='Close'>
                <span aria-hidden='true'>&times;</span></button>
              <h4 class='modal-title'>Rate Member</h4>
           </div>
           <div class='modal-body text-left  '>
			  <div id='member_rating_box'></div>  
           </div>
           <div class='modal-footer'>
              <button class='btn btn-success' data-mid='0' id='btnsavememberrating'>Save</button>
           </div> 
         </div>
       </div>
</div> 

<div class="modal fade" id="knowprofilemodal" tabindex="-1" role="dialog" aria-labelledby="knowprofilemodal" >
    <div class="modal-dialog modal-lg ">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Know Profile</h4>
            </div>
            <div class="modal-body text-left mailpreview">
               <div id='knowprofile'></div>
            </div>
            <div class="modal-footer ">
                <button class="btn btn-danger" data-dismiss="modal" aria-label="Close" >Close</button>
            </div> 
          </div>
        </div>
</div>
<div class="modal  " id="commonvocmodal" tabindex="-1" role="dialog" aria-labelledby="commonvocmodal" >
		<div class="modal-dialog modal-lg ">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
							aria-hidden="true">&times;</span></button>
					<h4 class="modal-title">Manage Common Vocations</h4>
				</div>
				<div class="modal-body text-left mailpreview">
				   <div class="row">
				   <div class="col-md-5">
						<h4>Current Vocations</h4>
						<div id="memexistvoc">
						</div>
				   </div>
				   <div class="col-md-7">
					   <h4>Common Vocations for User's Knows</h4>
					   <div id="memcommvoc">
					   </div>
				   </div> 
				   </div>
				</div>
				<div class="modal-footer ">
					<button class="btn btn-danger" data-dismiss="modal" aria-label="Close" >Close</button>
				</div> 
          </div>
        </div>
</div>


 <?php if($_user_role == 'admin'  ) { ?>
 
 <div class="modal fade seo_modal" tabindex="-1" role="dialog" aria-labelledby="liimportmodal" id="seo_modal">
        <div class="modal-dialog modal-lg" >
            <div class="modal-content">
               <div class="modal-header">
					<button type="button" id="close-video" class="close" data-dismiss="modal" aria-hidden="true">×</button>
					<h4 class="modal-title"> SEO Keyword and Meta Tags</h4>
				</div>
                <div class="modal-body  "  > 
				
					<form>
					  <div class="form-group">
						<label for="seo_tags">Meta Tags</label>
						<textarea  class="form-control" id="seo_tags" placeholder="Meta Tags"></textarea>
					  </div>
					  <div class="form-group">
						<label for="seo_keywords">Keywords</label>
						<textarea  class="form-control" id="seo_keywords" placeholder="Keywords"></textarea>
					
					<p class="help-block">Specify keywords each separated by comma</p>
						
					 </div>
					   
					</form> 
					 
					<div class='form-group pad10 text-center'>
						<input type='hidden' id='hidseomid' />
						<button class='btn btn-primary btn-lg btn_save_seo'>Save</button>
						<button data-dismiss="modal" class='btn btn-danger btn-lg  '> Cancel</button> 
					</div>
				</div>
            </div>
        </div>
</div>


<div class="modal fade search_client" tabindex="-1" role="dialog" aria-labelledby="search_client" id="search_client">
        <div class="modal-dialog " >
            <div class="modal-content">
               <div class="modal-header">
					<button type="button" id="close-video" class="close" data-dismiss="modal" aria-hidden="true">×</button>
					<h4 class="modal-title">Search Client</h4>
				</div>
                <div class="modal-body  " style='height: 450px; overflow-y: scroll'  > 
					<div class='marg4'>

			  <!-- Nav tabs -->
			  <ul class="nav nav-tabs" role="tablist">
				<li role="presentation" class="active"><a href="#membergrid_act" aria-controls="homein" role="tab" data-toggle="tab"> Active Members</a></li>
				<li role="presentation" ><a href="#membergrid_old" aria-controls="conreqin" role="tab" data-toggle="tab"> Old Members</a></li>
				 </ul>

			  <!-- Tab panes -->
			  <div class="tab-content">
				<div role="tabpanel" class="tab-pane active" id="membergrid_act">
					<div id='searchresultgrid'></div>  
				</div> 
				
				<div role="tabpanel" class="tab-pane  " id="membergrid_old">
					<div id='searchresultgrid_old'></div>  
				</div> 
				
			  </div>

			</div>
				</div>
            
			
			<div class="modal-footer ">
					<button class="btn btn-danger" data-dismiss="modal" aria-label="Close" >Close</button>
				</div> 
				
				</div>
        </div>
</div>

 
<div class="modal fade mod_assignemail" tabindex="-1" role="dialog" aria-labelledby="mod_assignemail" id="mod_assignemail">
        <div class="modal-dialog modal-lg" >
            <div class="modal-content">
               <div class="modal-header">
					<button type="button" id="close-video" class="close" data-dismiss="modal" aria-hidden="true">×</button>
					<h4 class="modal-title">Configure Email </h4>
				</div>
                <div class="modal-body  " style='height: 450px; overflow-y: scroll'  > 
					
				<div class="col-xs-12 col-sm-6">
					<div class="form-group">
						<label for="aemschedule">Scheduled On</label>
						<input type="text" class="form-control" id="aemschedule" placeholder="Scheduled On">
					  </div> 
				</div>
				<div class="col-xs-12 col-sm-2">
					<div class="form-group">
						<label for="aemschedulehr">Hour</label> 
						<select class="form-control" id="aemschedulehr">
							<option>00</option>
							<option>01</option>
							<option>02</option>
							<option>03</option>
							<option>04</option>
							<option>05</option>
							<option>06</option>
							<option>07</option>
							<option>08</option>
							<option>09</option>
							<option>10</option>
							<option>11</option>
							<option>12</option>
						</select>
					  </div> 
				</div>
				<div class="col-xs-12 col-sm-2">
					<div class="form-group">
						<label for="aemschedulemin">Minute</label> 
						<select class="form-control" id="aemschedulemin">
							<option>00</option>
							<option>05</option>
							<option>10</option>
							<option>15</option>
							<option>20</option>
							<option>25</option>
							<option>30</option>
							<option>35</option>
							<option>40</option>
							<option>45</option>
							<option>50</option>
							<option>55</option>
						</select> 
					  </div> 
				</div> 
				<div class="col-xs-12 col-sm-2">
					<div class="form-group">
						<label for="aemscheduleper">Period</label> 
						<select class="form-control" id="aemscheduleper">
							<option>AM</option>
							<option>PM</option> 
						</select> 
					  </div> 
				</div> 
				
				<div class="col-xs-12 col-sm-12">
					<div id='assignemailgrid'></div>
				</div>
				
				</div> 
			<div class="modal-footer ">
					<button class="btn btn-danger email_select" data-dismiss="modal" aria-label="Close" >Select</button>
			 </div> 
			 </div>
        </div>
</div>

<div class="modal fade mod_changeschedule" tabindex="-1" role="dialog" aria-labelledby="mod_changeschedule" id="mod_changeschedule">
        <div class="modal-dialog modal-md"   >
            <div class="modal-content">
               <div class="modal-header">
					<button type="button" id="close-video" class="close" data-dismiss="modal" aria-hidden="true">×</button>
					<h4 class="modal-title">Change Email Schedule</h4>
				</div>
                <div class="modal-body  " style='height:90px; overflow-y: scroll'  > 
					
				<div class="col-xs-12 col-sm-6">
					<div class="form-group">
						<label for="aemschedule">Email Sending Scheduled On</label>
						<input type="text" class="form-control" id="rescheduledate" placeholder="Scheduled On">
						 
					  </div> 
				</div>
				<div class="col-xs-12 col-sm-2">
					<div class="form-group">
						<label for="aemschedulehr">Hour</label>
						<select class="form-control" id="aemreschedulehr">
							<option>00</option>
							<option>01</option>
							<option>02</option>
							<option>03</option>
							<option>04</option>
							<option>05</option>
							<option>06</option>
							<option>07</option>
							<option>08</option>
							<option>09</option>
							<option>10</option>
							<option>11</option>
							<option>12</option>
						</select>
						
					  </div> 
				</div> 
				
				<div class="col-xs-12 col-sm-2">
					<div class="form-group">
						<label for="aemschedulehr">Minute</label>
						<select class="form-control" id="aemreschedulemin">
							<option>00</option>
							<option>05</option>
							<option>10</option>
							<option>15</option>
							<option>20</option>
							<option>25</option>
							<option>30</option>
							<option>35</option>
							<option>40</option>
							<option>45</option>
							<option>50</option>
							<option>55</option>
						</select>
						
					  </div> 
				</div> 
				
				<div class="col-xs-12 col-sm-2">
					<div class="form-group">
						<label for="aemscheduleper">Period</label> 
						<select class="form-control" id="aemrescheduleper">
							<option>AM</option>
							<option>PM</option> 
						</select> 
					  </div> 
				</div> 
				  
				</div> 
			<div class="modal-footer ">
			 
					<button class="btn btn-danger email_schupdate"  data-dismiss="modal" aria-label="Close" >Update</button>
			 </div> 
			 </div>
        </div>
</div> 
<div class="modal fade modalsetemptask" tabindex="-1" role="dialog" aria-labelledby="modalsetemptask"
         id="modalsetemptask">
        <div class="modal-dialog  ">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Assign Task to Employee</h4>
                </div>
                <div class="modal-body">
				<div class="col-xs-12 col-sm-6">
				<div class='form-group'  >  
					<label for="taskdate">Notification Time:</label> 
				 <input class='form-control ' id='taskdate' placeholder='Task Notification Date'> 
				 </div>
				
				</div>
				<div class="col-xs-12 col-sm-6">
				<div class='form-group'  > 
				 <label for="empname">Employee Assigned:</label> 
				 <select class='form-control ' id='empname' placeholder='Task Notification Date'>
				 <?php 
					$employees = $link->query( "select * from mc_user where is_employee='1' order by username" );
					while($row = $employees->fetch_array() )
					{
						 echo "<option value='" .  $row['id'] . "'>". $row['username']. "</option>";  
					} 
					
				 ?>
				 </select>				 
				 </div> 
				</div>
			 
				<div class="col-xs-12 col-sm-12">
				<div class='form-group'  > <label for="taskdesc">Task Details:</label> 
				  <textarea row='5' class='form-control ' id='taskdesc' placeholder='Task Details'></textarea> 
				 </div>
				
				</div>
				<div class="col-xs-12 col-sm-12"> 
				  <div class='form-group'  > 
				  <button   class='btn btn-primary btn-xs btn-xs btn_assignemployee'>Save Task</button> 
				  </div>  </div> 
				  
				  <div class='clearfix'></div>
                </div>
            </div>
        </div>
    </div>
 <?php  }  ?>

 
  
<div class="modal fade" id="modal3tquestion" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content ">
            <div class="modal-header ">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Add Question for 3 Touch Program Relationship</h4>
            </div>
            <div class="modal-body text-left ">
                <div id='3tqa'></div>
            </div>
            <div class="modal-footer">
                <div class="col-xs-12">
                    <button class="btn btn-primary up3tquestions">Save changes</button>
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</div>
 
<div id="stop" class="scrollTop">
	<span><a href=""><i class='fa fa-arrow-up'></i></a></span>
</div>