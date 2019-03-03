<hr />

<?php echo form_open(site_url('teacher/attendance_report_selector/')); ?>
<div class="row">

    <?php
    $query = $this->db->get('class');
    if ($query->num_rows() > 0):
        $class = $query->result_array();

        ?>

        <div class="col-md-3">
            <div class="form-group">
                <label class="control-label" style="margin-bottom: 5px;"><?php echo get_phrase('class'); ?></label>
                <select class="form-control selectboxit" name="class_id" onchange="select_subject(this.value); select_section(this.value)">
                    <option value=""><?php echo get_phrase('select_class'); ?></option>
                    <?php foreach ($class as $row): ?>
                    <option value="<?php echo $row['class_id']; ?>" ><?php echo $row['name']; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
    <?php endif; ?>

    <div id="section_holder">
        <div class="col-md-3">
            <div class="form-group">
                <label class="control-label" style="margin-bottom: 5px;"><?php echo get_phrase('section'); ?></label>
                <select class="form-control selectboxit" name="section_id">
                    <option value=""><?php echo get_phrase('select_class_first') ?></option>

                </select>
            </div>
        </div>
    </div>
    
    
 
	
	
	
	<div class="col-md-3">
		<div class="form-group">
		<label class="control-label" style="margin-bottom: 5px;"><?php echo get_phrase('date');?></label>
			<input type="text" class="form-control datepicker" name="timestamp" data-format="dd-mm-yyyy"
				value="<?php echo date("d-m-Y");?>"/>
		</div>
	</div>
	
	
	
    

    <input type="hidden" name="operation" value="selection">
    <input type="hidden" name="year" value="<?php echo $running_year;?>">

	<div class="col-md-2" style="margin-top: 20px;">
		<button type="submit" class="btn btn-info"><?php echo get_phrase('show_report');?></button>
	</div>
</div>

<?php echo form_close(); ?>

<script type="text/javascript">
    function select_section(class_id) {

        $.ajax({
            url: '<?php echo site_url('teacher/get_section/'); ?>' + class_id,
            success: function (response)
            {

                jQuery('#section_holder').html(response);
            }
        });
    }
    
    
    function select_subject(class_id) {

        $.ajax({
            url: '<?php echo site_url('teacher/get_subject1/'); ?>' + class_id,
            success: function (response)
            {

                jQuery('#subject_holder').html(response);
            }
        });
    }
    
    
    
    
</script>
