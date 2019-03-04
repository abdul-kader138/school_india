<center>
    <?php
    $class_name = $this->db->get_where('class', array('class_id' => $class_id))->row()->name;
    $section_name = $this->db->get_where('section', array('section_id' => $section_id))->row()->name;
    ?>
    <h4><strong>Date:</strong> <?php echo date("d-m-Y", $timestamp); ?></h4>
    <h4><strong>Class:</strong> <?php echo $class_name; ?></h4>
    <h4><strong>Section:</strong> <?php echo $section_name; ?></h4>
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
                    | <?php echo get_phrase('period'); ?> <i class="entypo-right-thin"></i>
                </td>
                <?php

                for ($i = 1; $i <= 6; $i++) {
                    ?>
                    <td style="text-align: center;">Period <?php echo $i; ?></td>
                <?php } ?>

            </tr>
            </thead>

            <tbody>

            <?php

            $data = array();
            // get all enrolled student  - codelover138@gmail.com
            $sql = 'select ids,period,status, classes, dates  from (SELECT student_id as ids,period,status, class_id as classes,from_unixtime(timestamp,"%Y-%m-%d") as dates, timestamp as times FROM `attendance` where attendance.class_id=' . $class_id . ' and attendance.section_id=' . $section_id . ' and timestamp=' . $timestamp . '  and  attendance.year="' . $running_year . '"group by attendance.period,attendance.student_id order by attendance.student_id,attendance.period) atten INNER join student on atten.ids=student.student_id ORDER by ids,period';
            $q = $this->db->query($sql);
            if ($q->num_rows() > 0) {
                foreach (($q->result()) as $row) {
                    $data[] = $row;
                }
            }

            // get all enrolled student  - codelover138@gmail.com
            $sql_enroll = 'SELECT * FROM enroll inner join student on enroll.student_id=student.student_id where enroll.class_id=' . $class_id . ' and enroll.section_id=' . $section_id . ' and year="' . $running_year . '" order by enroll.student_id';
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
                for ($j = 1; $j <= 6; $j++) { ?>
                    <td style="text-align: center;">
                        <?php
                        $present = $teacher->getDailyPresentHistory($data, $j, $row->student_id);
                        if ($present == 1) echo '<i class="entypo-record" style="color: #00a651;">Present</i>';
                        if ($present == 2) echo '<i class="entypo-record" style="color: #ee4749;">Absent</i>';
                        if ($present == 0) echo '-';
                        $total = $total + $present; ?>
                    </td>
                <?php } ?>
                <?php endforeach; ?>
            </tr>

            <?php ?>


            </tbody>
        </table>
        <center>
            <a href="<?php echo site_url('teacher/attendance_report_print_view/' . $class_id . '/' . $section_id . '/' . $timestamp); ?>"
               class="btn btn-primary" target="_blank">
                <?php echo get_phrase('print_attendance_sheet'); ?>
            </a>
        </center>
    </div>
</div>


