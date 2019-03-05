<center>
    <?php
    $class_name = $this->db->get_where('class', array('class_id' => $class_id))->row()->name;
    $section_name = $this->db->get_where('section', array('section_id' => $section_id))->row()->name;
    ?>
    <h4><strong>Year:</strong> <?php echo $sessional_year ?></h4>
    <h4><strong>Class:</strong> <?php echo $class_name; ?></h4>
    <h4><strong>Section:</strong> <?php echo $section_name; ?></h4>
    <button type="button" class="btn btn-xs btn-default no-print pull-right" style="margin-right:15px;"
            onclick="window.print();">
        <i class="fa fa-print"></i> Print
    </button>
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
                    <td style="text-align: center;"><?php echo $teacher->getMonthName($i); ?></td>
                <?php } ?>
                <td style="text-align: center;">Total</td>
            </tr>
            </thead>

            <tbody>
            <?php
            $data = array();

            // get all attendence Data - codelover138@gmail.com
            $sql = 'select ids, present,name,day,dates,month from (SELECT count(attendance_id) as present,from_unixtime(timestamp,"%Y-%m-%d") dates,DAY(FROM_UNIXTIME(timestamp)) as day,month(FROM_UNIXTIME(timestamp)) as month,student_id as ids  FROM `attendance` where status=1 and Year(FROM_UNIXTIME(timestamp))=' . $sessional_year . ' and section_id=' . $section_id . '  and attendance.class_id=' . $class_id . ' and  attendance.year="' . $running_year . '" group by month(from_unixtime(timestamp,"%Y-%m-%d")), student_id order by student_id,from_unixtime(timestamp,"%Y-%m-%d")) as atten INNER join student on atten.ids=student.student_id ORDER by name,dates';
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
                        $present = $teacher->getYearlyPresentHistory($data, $j, $row->student_id);
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
    });

</script>

