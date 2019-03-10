<hr />
<div class="row" style="text-align: center;">
    <div class="col-sm-4"></div>
    <div class="col-sm-4">
        <div class="tile-stats tile-gray">
            <div class="icon"><i class="entypo-chart-area"></i></div>

            <h3 style="color: #696969;"><?php echo get_phrase('attendance_for_class <b>'); ?> 
            
            <?php echo $this->db->get_where('class', array('class_id' => $class_id))->row()->name; ?> </b></h3>
            
            <h4 style="color: #696969;">
                <?php echo get_phrase('section'); ?> <?php echo $this->db->get_where('section', array('section_id' => $section_id))->row()->name; ?>
            </h4>
            <h4 style="color: #696969;">
                <?php echo get_phrase('<b>Subject:</b>'); ?> <?php echo $this->db->get_where('subject', array('subject_id' => $subject_id))->row()->name; ?>
            </h4>
            
            
            <h4 style="color: #696969;">
                <?php echo get_phrase('<b>Attendance By:</b>'); ?> <?php
														$name = $this->db->get_where($this->session->userdata('login_type'), array($this->session->userdata('login_type').'_id' => $this->session->userdata('login_user_id')))->row()->name;
														echo $name;
													?>
            </h4>
            <h4 style="color: #696969;"><b>Date: </b>
                <?php echo date("d M Y", $timestamp); ?>
            </h4>
            <h4 style="color: #696969;">
                <?php echo get_phrase('<b>Period:</b>'); ?> <?php echo $period; ?>
            </h4>
        </div>
    </div>
    <div class="col-sm-4"></div>
</div>

<center>
    <a class="btn btn-default" onclick="mark_all_present()">
        <i class="entypo-check"></i> <?php echo get_phrase('mark_all_present'); ?>
    </a>
    <a class="btn btn-default"  onclick="mark_all_absent()">
        <i class="entypo-cancel"></i> <?php echo get_phrase('mark_all_absent'); ?>
    </a>
</center>
<br>

<div class="row">

    <div class="col-md-2"></div>

    <div class="col-md-8">

        <?php echo form_open(site_url('teacher/attendance_update/'. $class_id . '/' . $section_id . '/' . $timestamp . '/' . $subject_id . '/' . $teacher_id . '/' . $period)); ?>
        <div id="attendance_update">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>#</th>
                        <th><?php echo get_phrase('id'); ?></th>
                        <th><?php echo get_phrase('name'); ?></th>
                        <th><?php echo get_phrase('status'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $count = 1;
                    $select_id = 0;
                    $attendance_of_students = $this->db->get_where('attendance', array(
                                'class_id' => $class_id,
                                'section_id' => $section_id,
                                'subject_id' => $subject_id,
                                'teacher_id' => $teacher_id,
                                'period' => $period,
                                'year' => $running_year,
                                'timestamp' => $timestamp
                            ))->result_array();
                    foreach ($attendance_of_students as $row):
                        ?>
                        <tr>
                            <td><?php echo $count++; ?></td>
                            <td>
                                <?php echo $this->db->get_where('student', array('student_id' => $row['student_id']))->row()->student_code; ?>
                            </td>
                            <td>
                                <?php echo $this->db->get_where('student', array('student_id' => $row['student_id']))->row()->name; ?>
                            </td>
                            <td>
                                <input type="radio" name="status_<?php echo $row['attendance_id']; ?>" value="0" <?php if ($row['status'] == 0) echo 'checked'; ?>>&nbsp;<?php echo get_phrase('undefined'); ?> &nbsp;
                                <input type="radio" name="status_<?php echo $row['attendance_id']; ?>" value="1" <?php if ($row['status'] == 1) echo 'checked'; ?>>&nbsp;<?php echo get_phrase('present'); ?> &nbsp;
                                <input type="radio" name="status_<?php echo $row['attendance_id']; ?>" value="2" <?php if ($row['status'] == 2) echo 'checked'; ?>>&nbsp;<?php echo get_phrase('absent'); ?> &nbsp;
                            </td>
                        </tr>
                    <?php
                    $select_id++;
                    endforeach; ?>
                </tbody>
            </table>
        </div>
        <center>
            <button type="submit" class="btn btn-success" id="submit_button">
                <i class="entypo-thumbs-up"></i> <?php echo get_phrase('save_changes'); ?>
            </button>
        </center>
        <?php echo form_close(); ?>

    </div>



</div>


<script type="text/javascript">

var class_selection = "";
jQuery(document).ready(function($) {
    $('#submit').attr('disabled', 'disabled');
});

    function select_section(class_id) {
        if (class_id !== '') {
        $.ajax({
            url: '<?php echo site_url('teacher/get_section/'); ?>' + class_id,
            success:function (response)
            {
                jQuery('#section_holder').html(response);
            }
        });
    }
}
    function mark_all_present() {
        var count = <?php echo count($attendance_of_students); ?>;
        for(var i = 0; i < count; i++){
            $(":radio[value=1]").prop('checked', true);
        }
    }

    function mark_all_absent() {
        var count = <?php echo count($attendance_of_students); ?>;
        for(var i = 0; i < count; i++)
            $(":radio[value=2]").prop('checked', true);
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
</script>
