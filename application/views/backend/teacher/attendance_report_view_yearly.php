<style>
    #action-form-submit {
        visibility: hidden;
    }
</style>
<center>
    <?php
    $class_name = $this->db->get_where('class', array('class_id' => $class_id))->row()->name;
    $section_name = $this->db->get_where('section', array('section_id' => $section_id))->row()->name;
    $start_date = "'" . $sessional_year . '-07-31' . "'";
    $end_year = $sessional_year + 1;
    $end_date = "'" . $end_year . '-06-30' . "'";
    ?>
    <h4><strong>Year:</strong> <?php echo $sessional_year ?></h4>
    <h4><strong>Class:</strong> <?php echo $class_name; ?></h4>
    <h4><strong>Section:</strong> <?php echo $section_name; ?></h4>
    <button type="button" class="btn btn-xs btn-default no-print pull-right" style="margin-right:15px;"
            onclick="window.print();">
        <i class="fa fa-print"></i> Print
    </button>
    <?php
    echo form_open('teacher/teacher_actions_yearly', 'id="action-form"');
    ?>
    <button type="button" class="btn btn-xs no-print pull-right" style="margin-right:15px;">
        <a href="#" id="excel" data-action="export_excel"><i class="fa fa-file-excel-o"></i> Export To Excel</a>
    </button>
    <input type="hidden" name="class_id" id="class_id" value="<?php echo $class_id; ?>"/>
    <input type="hidden" name="section_id" id="section_id" value="<?php echo $section_id; ?>"/>
    <input type="hidden" name="sessional_year" id="sessional_year" value="<?php echo $sessional_year; ?>"/>
    <input type="hidden" name="form_action" value="" id="form_action"/>
    <?= form_submit('performAction', 'performAction', 'id="action-form-submit"') ?>
    <?= form_close() ?>
</center>
<br>
<hr/>

<div class="row">
    <div class="col-md-12">
        <table class="table table-bordered" id="my_table">
            <thead>
            <tr>
                <td style="text-align: center;">
                    <?php echo get_phrase('students'); ?> <i class="entypo-down-thin"></i>
                    | <?php echo get_phrase('date'); ?> <i class="entypo-right-thin"></i>
                </td>
                <?php
                $year = explode('-', $running_year);
                $days = cal_days_in_month(CAL_GREGORIAN, $month, $sessional_year);

                for ($i = 1; $i <= 12; $i++) {
                    ?>
                    <td style="text-align: center;"><?php echo $teacher->getMonthNameFinancial($i); ?></td>
                <?php } ?>
                <td style="text-align: center;">Total</td>
            </tr>
            </thead>

            <tbody>
            <?php
            $data = array();

            // get all attendence Data - codelover138@gmail.com
            $sql = 'select ids, present,name,day,dates,month from (SELECT count(attendance_id) as present,(DATE_FORMAT(CONVERT_TZ(FROM_UNIXTIME(timestamp), "+00:00", "+00:30"), "%Y-%m-%d")) dates,DAY(DATE_FORMAT(CONVERT_TZ(FROM_UNIXTIME(timestamp), "+00:00", "+00:30"), "%Y-%m-%d")) as day,month(DATE_FORMAT(CONVERT_TZ(FROM_UNIXTIME(timestamp), "+00:00", "+00:30"), "%Y-%m-%d")) as month,student_id as ids  FROM `attendance` where status=1 and  DATE_FORMAT(CONVERT_TZ(FROM_UNIXTIME(timestamp), "+00:00", "+00:30"), "%Y-%m-%d") between ' . $start_date . ' and  ' . $end_date . ' and section_id=' . $section_id . '  and attendance.class_id=' . $class_id . ' group by month(DATE_FORMAT(CONVERT_TZ(FROM_UNIXTIME(timestamp), "+00:00", "+00:30"), "%Y-%m-%d")), student_id order by student_id,(DATE_FORMAT(CONVERT_TZ(FROM_UNIXTIME(timestamp), "+00:00", "+00:30"), "%Y-%m-%d"))) as atten INNER join student on atten.ids=student.student_id ORDER by name,dates';
            $data = array();
            $q = $this->db->query($sql);
            if ($q->num_rows() > 0) {
                foreach (($q->result()) as $row) {
                    $data[] = $row;
                }
            }
            // get all enrolled student  - codelover138@gmail.com
            $sql_enroll = 'SELECT * FROM enroll inner join student on enroll.student_id=student.student_id where enroll.class_id=' . $class_id . ' and enroll.section_id=' . $section_id . ' and year="' . $running_year . '" order by student.name';
            $student_data = array();
            $q = $this->db->query($sql_enroll);
            if ($q->num_rows() > 0) {
                foreach (($q->result()) as $row) {
                    $student_data[] = $row;
                }
            }
            foreach ($student_data

            as $row): ?>
            <tr>
                <td style="text-align: center;">
                    <?php echo $row->name; ?>
                </td>
                <?php
                $total = 0;
                for ($j = 1; $j <= 12; $j++) { ?>
                    <td style="text-align: center;">
                        <?php
                        $present = $teacher->getYearlyPresentSubject($data, $j, $row->student_id);
                        $total = $total + $present;
                        if ($present != 0) echo $present;
                        else echo "-";
                        ?>
                    </td>
                <?php } ?>
                <td><?= $total; ?></td>
                <?php endforeach; ?>
            </tr>
            </tbody>
        </table>
    </div>
</div>


<script type="text/javascript">

    // ajax form plugin calls at each modal loading,
    $(document).ready(function () {

        // SelectBoxIt Dropdown replacement
        if ($.isFunction($.fn.selectBoxIt)) {
            $("select.selectboxit").each(function (i, el) {
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


        $('body').on('click', '#excel', function (e) {
            e.preventDefault();
            $('#form_action').val($(this).attr('data-action'));
            $('#action-form-submit').trigger('click');
        });
    });

</script>

