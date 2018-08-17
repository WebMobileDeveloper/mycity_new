<?php
$qndet =   '' ; $id = 0 ;
$program_id ='1';

if(isset($question_edit) && $question_edit !=null)
{
	$question =$question_edit->row();
	$qndet =   $question->question;
	$id =   $question->id;
	$program_id =   $question->program_id;
}
?>
<div class='col-md-9'>
	<div class='profile-item'> 
	<h2>Program Question</h2>
	<div class='hr-sm '></div>
				
	 <?php
		echo "<div class='row'><div class='col-md-10 col-md-offset-1'>";
		if($this->session->msg_error  )
		{
			echo "<p class='  alertinfofix text-center marg1'> " . $this->session->msg_error . "</p>";
			$this->session->unset_userdata('msg_error');
		} 
		echo "</div></div>";
	?>
	
	
	
<form method='post' action='<?php echo $base; ?>index.php/program/question/'>
	<div class='form-group marg1'>   
		<label>Program Name:</label>
			<div class='form-group'>
				<select id='programname' name='programname' class='form-control'>
					<option value='1'   <?php if($program_id ==1) echo "selected";?>  >3 Touch Program</option>
				</select>
			</div>
		<label>New Question:</label>
		<div class='form-group'> 
		<textarea type='text' name='tbquest' id='tbquest' rows='10' class='form-control' placeholder='Question for client' ><?php echo $qndet;?></textarea> 
		</div>
		<div class='form-group'>
		<input type='hidden' value='<?php echo $id;?>' name='qno'/>
			<input type='submit'   name='btnsavequest'  id='btnsavequest' class='btn btn-primary' Value='Save Question' /> 
			<input type='submit' id='btncancel' class='btn btn-danger' Value='Cancel' />
		</div>
	</div>
	</form>
	</div>
<?php 	 
 if($allqs->num_rows() > 0) :
?>

<div class='profile-item'> 
	<h2>All Question</h2>
	<div class='hr-sm '></div>
	
 <table class='table table-responsive table-bordered'>
	<tr ><th>Sl. No.</th><th>Question</th> <th>Edit</th><th>Delete</th></tr> 
	<?php
	$i=1;
		foreach( $allqs->result() as $item)
		 {
			 echo "<tr id='row '>" . 
			 "<td id='qi_$i'>" . $i . "</td>" .
			 "<td id='qt_$i'>" . $item->c  . "</td>" .  
			 "<td>" .
			 "<a  href='". $base . "program/question/change/" . $item->a ."' class='btn btn-primary btn-xs editclques' > Edit</button>" .
			 "</td><td><button type='button' class='btn btn-danger btn-xs delclques' data-i='" . $item->a	 ."' > Delete</button>" .
			 "</td>"  ; 
			echo  " </tr>";
			$i++;
		 }
	?>
 </table> 
 </div>
<?php 	 
endif;
?>
</div>  
</div><!-- row -->
</div><!-- container -->