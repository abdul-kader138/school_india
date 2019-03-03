<div class="col-md-3">
	<div class="form-group">
	<label class="control-label" style="margin-bottom: 5px;"><?php echo get_phrase('subject_id');?></label>
		<select name="subject_id" id="subject_id" class="form-control selectboxit">
			<?php 
				$subjects = $this->db->get_where('subject_id' , array(
					'class_id' => $class_id 
				))->result_array();
				foreach($subjects as $row):
			?>
			<option value="<?php echo $row['subject_id'];?>"><?php echo $row['name'];?></option>
			<?php endforeach;?>
		</select>
	</div>
</div>

<script type="text/javascript">
   
    $(document).ready(function () {

        // SelectBoxIt Dropdown replacement
        if ($.isFunction($.fn.selectBoxIt))
        {
            $("select.selectboxit").each(function (i, el)
            {
                var $this = $(el),
                        opts = {
                            showFirstOption: attrDefault($this, 'first-option', true),
                            'native': attrDefault($this, 'native', false),
                            defaultText: attrDefault($this, 'text', ''),
                        };

                $this.addClass('visible');
                $this.selectBoxIt(opts);
            });
        }

    });

</script>