<?php
include("header.php");
include_once 'includes/db.php';
include_once 'includes/functions.php'; 

$ques_data = getQues($link);
$vocations = getVocations($link); 
$len = $_GET['l']; 
$hash = $_GET['hid'];
$lengths = explode('s', $len);
$id = substr($_GET['hid'], $lengths[0],$lengths[1]);

$_SESSION['linkedincid'] = $id;

?>
<div id="fb-root"></div>
	<div id="contact" class="about">
		<div class="container">
			<div class="row">
				<div class="col-md-12 col-sm-12 col-xs-12">
					<h4 style="margin-bottom: 65px; margin-top: 150px; font-weight: bold;">Signup to MyCity.com </h4>
				 
				</div> 
			</div>
		</div>
	</div> 

	<div class="container">
        <div class="row"> 
			<div class='landing-form'>
        		<div class="top-head ">
                            <div class="col-xs-12 col-sm-6 col-sm-offset-3 text-center">
                                <h4>RATE YOURSELF HERE</h4>
								<p>5 is the highest rating</p>
                            </div>
                            <div class="clearfix"></div>
                        </div>
						<?php
				  
                        //if ($_user_role == 'admin' OR $user_pkg != 'free') {
                        echo '<div class="col-xs-12 col-sm-6 col-sm-offset-3  padd-5">';
                        $i = 1;
						$textquestion ='';
                        foreach ($ques_data as $item) {
                            $name = "rating0" . $i;
                            $q_id = $item['id'];
                            $question = $item['question'];
                            $q_type = $item['question_type'];
							if($q_type == "rating" && $question != 'Would you refer'):  
                            echo "<div class='col-xs-12 col-sm-12 padd-5 pad-bor'>
                                <div class='col-sm-6 padd-5 col-xs-12'><label class='custom-label'>$question</label></div>
                                <div class='col-sm-6 col-xs-12 padd-5'>
                                <span class='starRating main luser_ques_main' data-ques='$q_id'>";
                                  echo "<input id='rating01$i' type='radio' class='luser_ques' name='$name' value='5' checked><label for='rating01$i'><span></span></label><label for='rating01$i'>5</label>
                                        <input id='rating02$i' type='radio' class='luser_ques' name='$name' value='4'><label for='rating02$i'><span></span></label>
                                        <label for='rating02$i'>4</label>
                                        <input id='rating03$i' type='radio'  class='luser_ques' name='$name' value='3'><label for='rating03$i'><span></span></label>
                                        <label for='rating03$i'>3</label>
                                        <input id='rating04$i' type='radio'  class='luser_ques' name='$name' value='2'><label for='rating04$i'><span></span></label>
                                        <label for='rating04$i'>2</label>
                                        <input id='rating05$i' type='radio' class='luser_ques' name='$name' value='1'><label for='rating05$i'><span></span></label>
                                        <label for='rating05$i'>1</label>"; 
                                echo "</span>
                                </div>
                            </div>
                                </span>";
							else: 
								$textquestion = "<div class='col-xs-12 col-sm-6 col-sm-offset-3 padd-5 pad-bor'>
								 <div class=col-sm-12 col-xs-12>
									<label class='custom-label'>$question</label> 
								</div>
								<div class=col-sm-12 col-xs-12> 
                                <span class='starRating main luser_ques_main' data-ques='$q_id'>";
								 
								$textquestion .= 
								"<select id='answer$q_id' data-ques='$q_id'  name='$name' 
								data-placeholder='Choose vocations ...' class='chosen-select luser_ques_text_add' multiple  >
								<option value=''></option>";
								 
								foreach($vocations as $vocitem)
								{
									$textquestion .= "<option value='" .$vocitem['name']  . "'>" . $vocitem['name'] . "</option>";
								}
								$textquestion .= "</select>
							<label for='rating01$i'><span></span></label> 
                            </span></div></div>";
							endif;
                            $i++;
                        }
                        echo '</div>';
                        //}
                    echo $textquestion;    
						?> 
				 <div class="col-xs-12 col-sm-6 col-sm-offset-3 padd-5 pad-bor text-center">
                     <button type="button" class="btn btn-success btn-lg btnlinkedinsignup"  id='lsignup' >Submit</button>
                 </div>
 		 </div>
        </div> 
    </div> 
  
<?php include("footer.php") ?>
