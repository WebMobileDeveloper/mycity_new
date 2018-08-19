<?php
include("header.php");
include_once 'includes/db.php';
include_once 'includes/functions.php';
if (isset($_SESSION['user_id']))
{
    header('location: dashboard.php');
}
 
 
if(isset($_POST['btnlandingsignup']))
{
    $landingzip = $_POST['landingzip'];
    $landingcity = $_POST['landingcity']; 
}
if(  $_SERVER['HTTP_HOST'] == "localhost" || $_SERVER['HTTP_HOST'] == "mycity.dev")
{
    $siteurl = 'http://'. $_SERVER['HTTP_HOST'] . "/";
} 
else
{
    $siteurl =  'https://mycity.com/';
}
$param = array('id' => '0'); 
$groups = json_decode(   curlexecute($param, $siteurl . 'api/api.php/groups/'), true);  //  getGroups($link); 
$vocations =    json_decode(   curlexecute($param, $siteurl . 'api/api.php/vocations/'), true); 
$cities = json_decode(   curlexecute($param, $siteurl . 'api/api.php/cities/'), true); 

 
 $vocaoptions ='';
 foreach ($vocations as $vocation) 
 {
	  $vocaoptions .= "<option value='" . $vocation['voc_name'] . "'>" . $vocation['voc_name'] . "</option>"; 
 }

?>
    <section id="main-section" class="welcome-sec next-sections">
        <div class="container">
            <div class="row">
                <div class="col-md-12 col-xs-12 col-sm-12 text-center">
                    <h3>Welcome to MyCity</h3>
                    <h4>Join for FREE to see people rated in your area</h4>

                    <div class="col-md-8 col-md-offset-2">
                        <p>Work with your networking partners and proactively seek new introductions through our database and
                            rating system. Experience 21st century networking. And we'll help you with your LinkedIn account
                            and turn your LinkedIn into Revenue.</p>
                    </div>
                    <div class="col-md-6 col-md-offset-3" style="background-color: rgba(0,0,0,.6) ;color:#000;padding-bottom:15px">
                        <div class="input-group">
                            <input id="email1" name="email1" type="text" class="form-control user_email" placeholder="Email Address...."
                                   aria-describedby="basic-addon2" style="padding-left: 20px;">
                            <span class="input-group-addon button green submit-type" id="basic-addon2">
                                <a href="#" class="nextBtn" data-sec="#sec_three" id="nextBtn1">GET STARTED</a>
                            </span>
                        </div>
		 		 
				 
				 <div class="   panel-search-home"> 
            <div class="panel-body">
			<p class='txt-lg'>Search Business</p> 
                 <div class="form-group">   
 <select data-placeholder="City" id="tbsearchbycity" name="tbsearchbycity"  class="form-control  "  > 
                            <option value=''>Select City</option> 
                 <?php 
                                foreach ($cities as $city)
                                {if($city['name'] != '')
                                    echo "<option value='" . $city['name'] . "'>" . $city['name'] . "</option>";
                                }
              ?>
        </select>
  </div>
  <div class="form-group">   
   <select data-placeholder='Vocations ...' class="form-control    " name="tbsearchbyvoc" id="tbsearchbyvoc"  > 
       <?php
	    echo $vocaoptions;
	  ?>
  </select>  
  </div>
  <button type="submit" id="form_search_business" class="flatbutton">Search</button>
  </div>
  </div>  
  
  
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section  >
        <div class="container">
            <div class="row">
                <div class="col-md-12 col-xs-12 col-sm-12 text-center">
                    <h3 style="font-size: 45px; color: #fff; margin-bottom: 40px">I am looking for an opportunity to</h3>

                    <div class="col-md-12 select-type">
                        <div class="row">
                            <div class="col-md-2 col-xs-8 col-sm-6 col-xs-offset-2 col-sm-offset-4 col-md-offset-2">
                                <a href="#" class="sales-lead flash-error" data-type="sl">
                                    <h3>Find sales leads</h3>
                                </a>
                            </div>
                            <div class="col-md-2 col-xs-8 col-xs-offset-2 col-sm-offset-4 col-sm-6 col-md-offset-0">
                                <a href="#" class="employee flash-error" data-type="js">
                                    <h3>Find a job</h3>
                                </a>
                            </div>
                            <div class="col-md-2 col-xs-8 col-xs-offset-2 col-sm-offset-4 col-sm-6 col-md-offset-0">
                                <a href="#" class="manager flash-error" data-type="hm">
                                    <h3>Find employees</h3>
                                </a>
                            </div>
                            <div class="col-md-2 col-xs-8 col-xs-offset-2 col-sm-offset-4 col-sm-6 col-md-offset-0">
                                <a href="#" class="networking flash-error" data-type="nw">
                                    <h3>Grow my network</h3>
                                </a>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-12 col-sm-4 col-sm-offset-4">
                                <div class="form-group">
                                    <button type="button" data-sec="#sec_three" class="btn btn-block button green nextBtn submit-type">Next</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    
	  <section  >
        <div class="container">
            <div class="row sec_two">
                <div class="col-md-12">
                    <h1 class="description">Tell us about yourself</h1>
                    <p class="description">Create Your Account. You know the drill...</p>
                </div>
                <div class="col-md-8 col-md-offset-2 logo-background "> 
                    <div class="form-group ">
                        <select name="membertype" class="form-control select2 signup select2-hidden-accessible" data-placeholder="Business" data-class="form-large"
                                tabindex="-1" aria-hidden="true">
                            <option selected disabled="disabled" value="null">- Select User Type -</option>
                            <option value="1">Business</option>
                            <option value="0">Individual</option>
                        </select>
                        <label class="message"></label>
                        <span class="required">*</span>
                    </div>
	 </div>
	 <div id='busininfoarea' class='hide' >
	 <div class="col-md-4  col-md-offset-2  ">
                    <div class="form-group">
                        <input name="busi_name" type="text" class="form-control" placeholder="Business Name" value="">
                        <label class="message"></label>
                        <span class="required">*</span>
                    </div>

 </div>
  <div class="col-md-4  ">
                    <div class="form-group">
                        <input name="busi_type" type="text" class="form-control" placeholder="Business Type" value="">
                        <label class="message"></label>
                        <span class="required">*</span>
                    </div>
		</div>			
	 <div class="col-md-4  col-md-offset-2 ">
                    <div class="form-group">
                        <input name="busi_location_street" type="text" class="form-control" placeholder="Business Street" value="">
                        <label class="message"></label>
                        <span class="required">*</span>
                    </div>
	 </div>
	
<div class="col-md-4  ">
                    <div class="form-group">
                        <input name="busi_location" type="text" class="form-control" placeholder="Business City" value="">
                        <label class="message"></label>
                        <span class="required">*</span>
                    </div>
	 </div>
	 <div class="col-md-4 col-md-offset-2  ">
                    <div class="form-group">
                        <input name="busi_hours" type="text" class="form-control" placeholder="Business Hours" value="">
                        <label class="message"></label>
                        <span class="required">*</span>
                    </div></div>
	 <div class="col-md-4  ">
                    <div class="form-group">
                        <input name="busi_website" type="text" class="form-control" placeholder="Website" value="">
                        <label class="message"></label>
                        <span class="required">*</span>
                    </div>
	 </div>
	 </div>
	 <div class="col-md-4 col-md-offset-2  ">
	 
                    <div class="form-group">
                        <input name="first_name" type="text" class="form-control" placeholder="First name" value="">
                        <label class="message"></label>
                        <span class="required">*</span>
                    </div>
             </div>
	 <div class="col-md-4 ">       
                    <div class="form-group ">
                        <input name="last_name" type="text" class="form-control" placeholder="Last name" value="">
                        <label class="message"></label>
                        <span class="required">*</span>
                    </div>
</div>
	 <div class="col-md-4 col-md-offset-2  ">
        <div class="form-group ">
                        <input name="email2" type="email" class="form-control" placeholder="Email address" value="">
                        <label class="message"></label><span class="required">*</span>
                    </div>
                   </div>
	 <div class="col-md-4   "> 
                    <div class="form-group">
                        <input name="password" type="password" class="form-control" placeholder="Create password">
                        <label class="message"></label>
                        <span class="required">*</span>
                    </div> 
	</div>
	 <div class="col-md-8 col-md-offset-2 ">				
                   <div class="form-group ">
                        <select name="country" class="form-control select2 signup select2-hidden-accessible" data-placeholder="Country" data-class="form-large"
                                tabindex="-1" aria-hidden="true">
                            <option selected disabled="disabled" value="null">-select your country-</option>
                            <option value="United States">United States</option>
                            <option value="United States Minor Outlying Islands">United States Minor Outlying Islands</option>
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
                        </select> <span class="required">*</span>
                    </div>
                   
</div>
	<div class="col-md-8 col-md-offset-2 "> 
					 <div class="form-group">
                        <input name="street" type="text" class="form-control" placeholder="Street Address" value='<?php echo $landingzip; ?>'>
                        <label class="message"></label>
                        <span class="required">*</span>
                    </div>
      </div>
	 <div class="col-md-4 col-md-offset-2  ">
		<div class="form-group">
			<input name="city" type="text" class="form-control" placeholder="City" value='<?php echo $landingcity; ?>'>
            <label class="message"></label>
            <span class="required">*</span>
        </div>
		
		</div>
	
	 <div class="col-md-4   ">
		<div class="form-group hidden">
			<select name="province" class="form-control select2 signup select2-hidden-accessible" data-placeholder="Province"
                                data-class="form-large" tabindex="-1" aria-hidden="true">
                            <option></option>
                            <option value="Alberta">Alberta</option>
                            <option value="British Columbia">British Columbia</option>
                            <option value="Manitoba">Manitoba</option>
                            <option value="New Brunswick">New Brunswick</option>
                            <option value="Newfoundland and Labrador">Newfoundland and Labrador</option>
                            <option value="Northwest Territories">Northwest Territories</option>
                            <option value="Nova Scotia">Nova Scotia</option>
                            <option value="Nunavut">Nunavut</option>
                            <option value="Ontario">Ontario</option>
                            <option value="Prince Edward Island">Prince Edward Island</option>
                            <option value="Quebec">Quebec</option>
                            <option value="Saskatchewan">Saskatchewan</option>
                            <option value="Yukon">Yukon</option>
            </select>
			<span class="select2 select2-container select2-container--default" dir="ltr" style="width: 385px;"><span class="selection"></span>
			<span class="required">*</span>
			</div>
</div>

  <div class="col-md-4   ">
			<div class="form-group">
                        <input name="zip" type="text" class="form-control" placeholder="ZIP" value='<?php echo $landingzip; ?>'>
                        <label class="message"></label>
                        <span class="required">*</span>
                    </div>	  
      </div>
	  
			
	  <div class="col-md-8 col-md-offset-2  "> 
			<div class="form-group">
				<button type="button"  data-sec="#sec_sucess_msg"  class="btn btn-block button green submit regUser">Create account</button>
			</div>
	</div>
 <div class="col-md-10 col-md-offset-1  "> 	
			<div class="form-group">
				<p>By clicking "Create Account" you agree to the Mycity.com
                                <a href="/terms-of-service.php" target="_blank">Terms of Services</a> and
                                <a href="/privacy-policy.php" target="_blank">Privacy Policy</a>.
				</p>
			</div>
        </div>
	 </div>
  </div>
</section>
	 <section  >
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <h1 class="description">Your account has been created successfully.</h1>

                    <p class="description">Click next to become a successfull member.</p>
                </div>
                <div class="col-md-4 col-md-offset-4 logo-background sec_two">

       
                    <div class="form-group">
                        <button type="button" data-sec="#sec_five" id="nextBtn2" class="nextBtn  btn btn-block  button green submit">Next</button>
                    </div>
					  
                </div>
            </div>
        </div>
    </section>
     
    <section  >
        <div class="container">
            <div class="row">
                <form>
                    <div class="col-md-12">
                        <h1 class="description">Please specify your location</h1>

                        <p class="description">The better we know your location, the more opportunities we can deliver.</p>
                    </div>

                    <div class="col-md-4 col-md-offset-4 logo-background">

                        <div class="form-group" data-country="Pakistan" data-state="">
                            <input type="hidden" name="lat" value="30.375321">
                            <input type="hidden" name="lng" value="69.34511599999996">
                            <div id="map" style="position: relative; overflow: hidden; transform: translateZ(0px); background-color: rgb(229, 227, 223);">
                                <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d14140969.356001861!2d60.33919713135139!3d30.083155533278386!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x38db52d2f8fd751f%3A0x46b7a1f7e614925c!2sPakistan!5e0!3m2!1sen!2s!4v1457012176499"
                                    width="100%" height="450" frameborder="0" style="border:0" allowfullscreen></iframe>
                            </div>
                        </div>

                        <div class="form-group">
                            <button type="button" data-sec="#sec_five" class="nextBtn btn btn-block button green submit">Next</button>
                        </div>

                    </div>
                </form>
            </div>
        </div>
    </section>
    <section  >
        <div class="container">
            <div class="row">
                <form>
                    <div class="col-md-12">
                        <h1 class="description">Select your vocation</h1>
                    </div>

                    <div class="col-md-4 col-md-offset-4 logo-background">

                        <div class="multi-select interests " data-name="interests" data-required="1" data-max="5" data-numbered="1" data-clearable="1">
                            <div class="vocation_append">
                                <div class="form-group">
                                    <select name="interests[1]" data-name="interests" class="form-control select2 signup select2-hidden-accessible"
                                            data-placeholder="Select your vocation..." data-class="form-large" tabindex="-1" aria-hidden="true">
                                        <option value="null">Select your vocation</option>
                                        <?php
											echo $vocaoptions;
                                        ?>
                                    </select>
                                    <label class="message"></label>
                                </div>
                                <div class="form-group" style="margin-top: 50px">
                                    <p class="description">Other ways of describing your vocation.</p>
                                    <select name="interests[2]" data-name="interests" class="form-control select2 signup select2-hidden-accessible"
                                            data-placeholder="Select your vocation..." data-class="form-large" tabindex="-1" aria-hidden="true">
                                        <option value="null">Select your vocation</option>
                                        <?php
                                         echo $vocaoptions;
                                        ?>
                                    </select>
                                    <label class="message"></label>
                                </div>
                                <div class="form-group">
                                    <select name="interests[3]" data-name="interests"
                                            class="form-control select2 signup select2-hidden-accessible" data-placeholder="Select your vocation..."
                                            data-class="form-large" tabindex="-1" aria-hidden="true" style="width: 100%;">
                                        <option value="null">Select your vocation</option>
                                        <?php
                                         echo $vocaoptions;
                                        ?>
                                    </select>
                                    <label class="message"></label>
                                </div>
                            </div>
                            <div class="form-group" style="display: block;">
                                <p><a class="add-more" id="add_more_vocation" data-for="interests"><i class="fa fa-plus-circle"></i> Add another</a>
                                </p>
                            </div>
                            <div class="form-group">
                                <button type="button" data-sec="#sec_six" id="nextBtn3" class="nextBtn btn btn-block button green submit">Next</button>
                            </div>

                        </div>
                </form>
            </div>
        </div>
    </section>
    <section  >
        <div class="container">
            <div class="row">
                <form>
                    <div class="col-md-12">
                        <h1 class="description">Select your city</h1>
                    </div>

                    <div class="col-md-4 col-md-offset-4 logo-background groups">
                        <div class="multi-select ages groups_append" data-name="ages" data-required="1" data-max="3" data-numbered="0" data-clearable="1">
                            <div class="form-group">
                                <select name="groups[1]" data-name="ages" class="form-control select2 signup select2-hidden-accessible"
                                        data-class="form-large" tabindex="-1" aria-hidden="true">
                                    <option value="null">City Name</option>
                                    <?php
                                    foreach ($groups as $group) {
                                        echo "<option value='" . $group['id'] . "'>" . $group['name'] . "</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <p><a class="add-more" id="add_more_groups" data-for="ages"><i class="fa fa-plus-circle"></i> Add another</a></p>
                        </div>
                        <div class="form-group">
                            <button type="button" data-sec="#sec_seven" id="nextBtn4" class="nextBtn btn btn-block button green submit">Next</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </section>
    <section  >
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <h1 class="description">Who is your target client?</h1>
                </div>
                <div class="col-md-4 col-md-offset-4 logo-background targeted_client_main">
                    <div class="multi-select industries targeted_client_append" data-name="industries" data-required="1" data-max="5" data-numbered="1"
                         data-clearable="1">
                        <div class="form-group">
                            <select name="targeted_clients[1]" data-name="industries" class="form-control select2 signup select2-hidden-accessible"
                                    data-placeholder="Select your client type..." data-class="form-large" tabindex="-1" aria-hidden="true">
                                <option value="null">Select your client type...</option>
                                <?php
                                 echo $vocaoptions;
                                ?>
                            </select>
                            <span class="required">*</span></div>
                        <div class="form-group">
                            <select name="targeted_clients[2]" data-name="industries" class="form-control select2 signup select2-hidden-accessible"
                                    data-placeholder="Select your client type..." data-class="form-large" tabindex="-1" aria-hidden="true">
                                <option value="null">Select your client type...</option>
                                <?php
                                 echo $vocaoptions;
                                ?>
                            </select>

                        </div>
                        <div class="form-group">
                            <select name="targeted_clients[3]" data-name="industries" class="form-control select2 signup select2-hidden-accessible"
                                    data-placeholder="Select your client type..." data-class="form-large" tabindex="-1" aria-hidden="true">
                                <option value="null">Select your client type...</option>
                                <?php
                                 echo $vocaoptions;
                                ?>
                            </select>

                        </div>
                    </div>
                    <div class="form-group">
                        <p><a id="add_more_trargeted_clients" class="add-more" data-for="industries"><i class="fa fa-plus-circle"></i> Add another industry</a>
                        </p>
                    </div>
                    <div class="form-group">
                        <button type="button" data-sec="#sec_eight" id="nextBtn5" class="nextBtn btn btn-block button green submit">Next</button>
                    </div>
                </div>
            </div>
        </div>
    </section>
	
	<section  >
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <h1 class="description">Who is your targeted referral partners?</h1>
                </div>
                <div class="col-md-4 col-md-offset-4 logo-background targeted_client_main">
                    <div class="multi-select industries targeted_referral_append" data-name="industries" data-required="1" data-max="5" data-numbered="1"
                         data-clearable="1">
                        <div class="form-group">
                            <select name="targeted_referral_partners[1]" data-name="industries" class="form-control select2 signup select2-hidden-accessible"
                                    data-placeholder="Select your referral vocation..." data-class="form-large" tabindex="-1" aria-hidden="true">
                                <option value="null">Select your referral vocation...</option>
                                <?php
                                 echo $vocaoptions;
                                ?>
                            </select>
                            <span class="required">*</span></div>
                        <div class="form-group">
                            <select name="targeted_referral_partners[2]" data-name="industries" class="form-control select2 signup select2-hidden-accessible"
                                    data-placeholder="Select your referral vocation..." data-class="form-large" tabindex="-1" aria-hidden="true">
                                <option value="null">Select your referral vocation...</option>
                                <?php
                                 echo $vocaoptions;
                                ?>
                            </select>

                        </div>
                        <div class="form-group">
                            <select name="targeted_referral_partners[3]" data-name="industries" class="form-control select2 signup select2-hidden-accessible"
                                    data-placeholder="Select your referral vocation..." data-class="form-large" tabindex="-1" aria-hidden="true">
                                <option value="null">Select your referral vocation...</option>
                                <?php
                                 echo $vocaoptions;
                                ?>
                            </select>

                        </div>
                    </div>
                    <div class="form-group">
                        <p><a id="add_more_targeted_referral" class="add-more" data-for="industries"><i class="fa fa-plus-circle"></i> Add another industry</a>
                        </p>
                    </div>
                    <div class="form-group">
                        <button type="button" data-sec="#sec_twelve" id="nextBtn7" class="nextBtn btn btn-block button green submit">Next</button>
                    </div>
                </div>
            </div>
        </div>
    </section>
	
    <section  >
        <div class="container">
            <div class="row">
                <form>
                    <div class="col-md-12">
                        <h1 class="description">People you know</h1>
                    </div>
                    <div class="col-md-4 col-md-offset-4 logo-background">
                        <div class="form-group">
                            <input name="phone" type="text" class="form-control" placeholder="Phone">
                            <label class="message"></label>
                        </div>
                        <div class="form-group">
                            <input name="linkedin" type="text" class="form-control" placeholder="Your LinkedIn profile URL">
                            <label class="message"></label>
                        </div>
                        <div class="form-group">
                            <input name="linkedin" type="text" class="form-control" placeholder="Your Social Sync profile URL">
                            <label class="message"></label>
                        </div>

                        <div class="form-group">
                            <button type="button" data-sec="#sec_nine" class="nextBtn btn btn-block button green submit">Next</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </section>
    <section  >
        <div class="container">
            <div class="row">
                <form>
                    <div class="col-md-12">
                        <h1 class="description">Where Would You Like To Network?</h1>

                        <p class="description">Add up to 5 Regions</p>
                    </div>
                    <div class="col-md-4 col-md-offset-4 logo-background">
                        <div class="multi-select regions_signup" data-name="regions_signup" data-max="5" data-numbered="1" data-clearable="1">
                            <div class="form-group" data-url="/edit-profile/regions">
                                <select name="regions_signup[1]" class="form-control select2 signup select2-hidden-accessible"
                                        data-placeholder="Your age (range)" data-class="form-large" tabindex="-1" aria-hidden="true">
                                    <option>Select a region...</option>
                                    <option></option>
                                </select>
                                <span class="order">1</span>
                                <span class="clear">x</span>
                            </div>
                        </div>
                        <div class="form-group">
                            <p><a href="#" class="add-more" data-for="regions_signup"><i class="fa fa-plus-circle"></i> Add another region</a></p>
                        </div>
                        <div class="form-group">
                            <button type="button" data-sec="#sec_ten" class="nextBtn btn btn-block button green submit">Next</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </section>
    <section id="sec_twelve" class="next-sections form-large">
        <div class="container">
            <div class="row">
                <form>
                    <div class="col-md-12">
                        <h1 class="description">Choose a photo</h1>
                        <p class="description">Optional. Make yourself more recognizable.</p>
                    </div>
                    <div class="col-md-4 col-md-offset-4 logo-background">
                        <div class="form-group">
                            <div class="change-photo">
                                <div class="img">
                                    <img id="blah" src="images/no-photo.png" style="border-radius: 50%;" alt="abc">
                                    <input id="usrImg" type="file" onchange="readURL(this);" required/>
                                </div>
                                <p>Click to add</p>
                            </div>
                        </div>
                        <div class="form-group">
                            <button type="button" class="btn btn-block button green submit regdet_update">Update account</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </section>
    <script>
        function readURL(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function (e) {
                    $('#blah')
                        .attr('src', e.target.result)
                        .width(130)
                        .height(130);
                };
                reader.readAsDataURL(input.files[0]);
                $(".hideafter").hide();
                $("#blah").show();
            }
        }
    </script>

<?php include("footer.php") ?>