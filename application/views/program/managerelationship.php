<?php 
?>
<div class='col-md-9'>  
	 <div class="panel panel-default  panel-success"> 
	 <div class="panel-heading">
		<h4>3 Touch Program Relationship Management</h4>
	 </div>
		<div class="panel-body"> 
		<div class="col-xs-12 col-md-12"> 
		  <div id='pprelations'>
		  <?php 
		 
		  if( !empty($participants->relations)   ):
		   
			  foreach($participants->relations->result() as $row)
			  {
				  echo '<a href="' .  $base. 'program/relations/timeline/' . $row->a . '"  class="nlink" name="showtimeline" value="show_timeline">' . $row->b .'</a>' ; }
		  
		  endif;
		  ?>
		  </div>
		</div>
		<div class="col-xs-12 col-md-12"> 
		<?php echo form_open('program/relations/'); ?>
	<div class="globalsearch">
	<label for="em_client">Search Relationship:</label> 
	<div class="row">
	 <div class="col-xs-12 col-sm-5">
		<div class="form-group"> 
			<input type="text" class="form-control " name="tb_3trelation" placeholder="Relationship Name"> 
		</div> 
	</div>
	<div class="col-xs-12 col-sm-5">
		<div class="form-group"> 
			<select data-placeholder="Select Tags" name="3tsearchtag[]" class='chosen-select' multiple >
				<?php
				foreach ($tags->result() as $tag)
				{
					echo "<option value='" . $tag->tagname  . "'>" . $tag->tagname  . "</option>";
				}
				?>
			</select>
			<small class="pull-right">(Multiple tags can be selected)</small>
		</div> 
	</div>
	<div class="col-xs-12 col-sm-2"> 
		<div class="form-group">  
			<button type="submit" name='search_know' value='search' class="btn btn-primary " id="btn_src3trelation">Search</button>
		</div> 
	</div>	
	 </div> 
	</div>
	<?php form_close(); ?>
	</div>
	<div class="col-xs-12 col-sm-12">  
		<?php
		if($allknows != null):
		 
			if( $allknows['results']->num_rows() > 0) :
		?>
		<table class='table table-responsive table-bordered'>
			<tr ><th>Name</th><th>Vocation</th> <th>Select</th><th>Action</th></tr> 
			<?php 
			$i=1;
				foreach( $allknows['results']->result() as $item)
				{
					echo "<tr id='row '>" . 
					"<td id='qi_$i'>" .$item->client_name . "</td>" .
					"<td id='qt_$i'>" . $item->client_profession . "</td>" ; 
					echo "<td><input type='checkbox'  name='cb_relatedmembers' value='" . $item->id  . "'> </td>" .   
					"<td>";
					echo "<div class='dropdown '><a  href='#' class='dropdown-toggle btn btn-primary' data-toggle='dropdown' role='button' aria-haspopup='true' aria-expanded='false' ><i class='fa fa-cog'></i> </a>"  . 
				"<ul class='dropdown-menu pull-right'> "   .
				"<li><a href='#' data-toggle='tab' class='track3tprogress' data-prog='1' data-id='" . $item->id . "' data-name='" .$item->client_name.  "' >Track Progress</a></li>"  . 
				"</ul> " ;
				echo "</div></td></tr>";
				$i++;
		} 
		if( $i > 0)
		{
			echo  "<tr><td colspan='3'  class='text-right'>
			<a href='" . $base. "program/relations?c=1' class='btn btn-primary'>Clear Search Result</a> 
			<input type='button' class='btn btn-success btn3taddrelationship'  value='Add as 3 Touch Relationship'  >" . 
			"</td><td ></td></tr>";
		} 
		?>
	</table> 
	<?php
		
		if($this->session->has_userdata('keyword') )
		{
			$pager_config['base_url'] = $this->config->item('base_url') . 'program/relations/search/';
		}			
		else 
		{
			$pager_config['base_url'] = $this->config->item('base_url') . 'program/relations/' ;
		}
		$pager_config['total_rows'] = $allknows['num_rows'];
		$choice = ( $allknows["num_rows"] / 10 > 20 ? 20 : $allknows["num_rows"] / 10 );
		
		
		$pager_config["num_links"] = round($choice);
		$this->pagination->initialize($pager_config); 
		echo $this->pagination->create_links();	
	
	   endif;
	endif; 
	?>
	</div>
	</div>
</div>
<div id='programprogress'></div> 
<?php 
if (intval($relationid) > 0 ):
   
?>
<div class="tl-box" >
		<div class="pad10 text-center">
			<h4 class='white'>3 Touch Program Progress <span id="3tp_progress"></span></h4>
			<hr/>	
			</div> 
			<div id='programtrack'> 
				<?php 
				  
				if($timeline->num_rows() > 0)
				{
					echo '<ul id="program-tl">';
					$index= 1;
					foreach($timeline->result() as $item )
					{
						if( $item->a == ""|| $item->a == null )
						{
							$nulitem = "<li ><span></span>" ; 
							$answer  = "<br/><strong>ANSWER: </strong> ";  
							$answer .= "<textarea  style='resize: vertical !important;' id='ans" .  $item->i . "' class='form-control'></textarea>";
						}
						else 
						{
							$answer  = "";
							$nulitem = "<li class='processed'><span></span>" ;
							$answer  = "<br/><strong>ANSWER: </strong>"  ; 
							$answer .= "<textarea  style='resize: vertical !important;' id='ans" . $item->i . "'  class='form-control'>" . $item->a . "</textarea>"; 
						}
						
						$buttons  = "<hr/><button data-qno='" .  $item->qno . "' data-desc='" .  $item->a . "'  data-id='" .  $item->i . "' class='btn btn-primary btn-xs btnsv3tans'>Save Answer</button>" ;
						$nulitem .= "<div class='title'>Question #" . ( $index++) . "</div>" . 
						"<div class='info'>".$item->q ;
						$nulitem .= $answer ;
						$nulitem .= (  $item->af  == 'null' || $item->af  == '' || $item->af  ==  null   ? '' : $item->af );
						$nulitem .=  $buttons . " </div> </li>" ;
						echo $nulitem;	  
						} 
				echo '</ul>';
				 
				}
				else 
				{
					echo '<ul id="program-tl">';
					echo "<li><span></span>" .
						"<div class='title'>Program Participant Message</div>" .
						"<div class='info'>No activity found on this program participant.</div>" .
						"</li>" ; 
					echo '</ul>'; 
				}
				?>
			</div>  
		
 
 <div class="pad10 ">
		<hr>
		<a href="javascript:void(0);" data-id="<?php echo $relationid;?>" data-name=" " class="btn btn-primary btnadd3tq" id="add" >Add New Question</a> 
			<a href="javascript:void(0);" data-id="<?php echo $relationid;?>" data-name=" " class="btn btn-danger btndel3tq" id="add" >Remove Relation</a> 
	</div> 	</div> 	
<?php endif;?>	
	 
</div>  
</div><!-- row -->
</div><!-- container -->

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
                    <button  data-relid='<?php echo $relationid; ?>' class="btn btn-primary up3tquestions">Save changes</button>
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</div>
