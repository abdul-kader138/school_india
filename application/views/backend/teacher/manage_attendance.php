<hr />


<?php echo form_open(site_url('teacher/attendance_selector/'));?>
<div class="row">

	<div class="col-md-3">
		<div class="form-group">
		<label class="control-label" style="margin-bottom: 5px;"><?php echo get_phrase('class');?></label>
			<select name="class_id" class="form-control selectboxit" onchange="select_subject(this.value); select_section(this.value)" id = "class_selection">
				<option value=""><?php echo get_phrase('select_class');?></option>
				<?php
					$classes = $this->db->get('class')->result_array();
					foreach($classes as $row):
                                            
				?>
                                
				<option value="<?php echo $row['class_id'];?>"
					><?php echo $row['name'];?></option>
                                
				<?php endforeach;?>
			</select>
		</div>
	</div>

	
    <div id="section_holder">
	<div class="col-md-3">
		<div class="form-group">
		<label class="control-label" style="margin-bottom: 5px;"><?php echo get_phrase('section');?></label>
			<select class="form-control selectboxit" name="section_id">
                            <option value=""><?php echo get_phrase('select_class_first') ?></option>
				
			</select>
		</div>
	</div>
    </div>
    
 
    
     <div id="subject_holder">
	<div class="col-md-3">
		<div class="form-group">
		<label class="control-label" style="margin-bottom: 5px;"><?php echo get_phrase('subject');?></label>
			<select class="form-control selectboxit" name="subject_id">
                            <option value=""><?php echo get_phrase('select_class_first') ?></option>
				
			</select>
		</div>
	</div>
    </div>
    

	<div class="col-md-3">
		<div class="form-group">
		<label class="control-label" style="margin-bottom: 5px;"><?php echo get_phrase('period');?></label>
			<select class="form-control selectboxit" name="period">
                            <option value="1">1st Period</option>
                            <option value="2">2nd Period</option>
                            <option value="3">3rd Period</option>
                            <option value="4">4th Period</option>
                            <option value="5">5th Period</option>
                            <option value="6">6th Period</option>
				
			</select>
		</div>
	</div>
  
    
    <?php
	$teacher_id = $this->session->userdata('login_user_id');
	?>
    
    

    <div class="col-md-3">
		<div class="form-group">
		<label class="control-label" style="margin-bottom: 5px;"><?php echo get_phrase('date');?></label>
			<input type="text" class="form-control datepicker" name="timestamp" data-format="dd-mm-yyyy"
				value="<?php echo date("d-m-Y");?>"/>
		</div>
	</div>

	
	
	<input type="hidden" name="year" value="<?php echo $running_year;?>">
	<?php $teacher_id = $this->session->userdata('login_user_id');?>
	<input type="hidden" name="teacher_id" value="<?php echo $teacher_id;?>">
</div>
        
        <div class="col-md-3" style="margin-top: 20px;">
        		<button type="submit" id = "submit" class="btn btn-info"><?php echo get_phrase('manage_attendance');?></button>
        	</div>
        

</div>
<?php echo form_close();?>

<script type="text/javascript">
var class_selection = "";
jQuery(document).ready(function($) {
	$('#submit').attr('disabled', 'disabled');
});

//Select Subject Function
function select_subject(class_id) {
	if(class_id !== ''){
		$.ajax({
			url: '<?php echo site_url('teacher/get_subject1/'); ?>' + class_id,
			success:function (response)
			{

			jQuery('#subject_holder').html(response);
			}
		});
	}
}

function check_validation(){
	if(class_selection !== ''){
		$('#submit').removeAttr('disabled')
	}
	else{
		$('#submit').attr('disabled', 'disabled');
	}
}

$('#class_selection').change(function(){
	class_selection = $('#class_selection').val();
	check_validation();
});

//Subject Function End


//Select Section Function
function select_section(class_id) {
	if(class_id !== ''){
		$.ajax({
			url: '<?php echo site_url('teacher/get_section/'); ?>' + class_id,
			success:function (response)
			{

			jQuery('#section_holder').html(response);
			}
		});
	}
}

function check_validation(){
	if(class_selection !== ''){
		$('#submit').removeAttr('disabled')
	}
	else{
		$('#submit').attr('disabled', 'disabled');
	}
}

$('#class_selection').change(function(){
	class_selection = $('#class_selection').val();
	check_validation();
});

//Section Function End


</script>