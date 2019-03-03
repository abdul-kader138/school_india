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
    <hr />

    <div class="row">
        <div class="col-md-12">
            <table class="table table-bordered" id="my_table">
                <thead>
                    <tr>
                        <td style="text-align: center;">
    <?php echo get_phrase('students'); ?> <i class="entypo-down-thin"></i> | <?php echo get_phrase('period'); ?> <i class="entypo-right-thin"></i>
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

                            $students = $this->db->get_where('enroll', array('class_id' => $class_id, 'year' => $running_year, 'section_id' => $section_id))->result_array();

                            foreach ($students as $row):
                                ?>
                        <tr>
                            <td style="text-align: center;">
                            <?php echo $this->db->get_where('student', array('student_id' => $row['student_id']))->row()->name; ?>
                            </td>
                            
                            
                            
                            
                            <td style="text-align: center;">
                            <?php
                                $status = 0;
                                
                                $attendance = $this->db->get_where('attendance', array('section_id' => $section_id, 'class_id' => $class_id, 'year' => $running_year, 'timestamp' => $timestamp, 'period' => 1, 'student_id' => $row['student_id']))->result_array();


                                foreach ($attendance as $row1):
                                  
    
                                    $status = $row1['status'];


                                endforeach;
                                ?>
                                
                                <?php if ($status == 1) { ?>
                                                            <i class="entypo-record" style="color: #00a651;"> Present</i>
                                                <?php  } if($status == 2)  { ?>
                                                            <i class="entypo-record" style="color: #ee4749;"> Absent</i>
                                <?php  } $status =0;?>


                                </td>
                                
                                
                                <td style="text-align: center;">
                            <?php
                                $status = 0;
                                
                                $attendance = $this->db->get_where('attendance', array('section_id' => $section_id, 'class_id' => $class_id, 'year' => $running_year, 'timestamp' => $timestamp, 'period' => 2, 'student_id' => $row['student_id']))->result_array();


                                foreach ($attendance as $row1):
                                  
    
                                    $status = $row1['status'];


                                endforeach;
                                ?>
                                
                                <?php if ($status == 1) { ?>
                                                            <i class="entypo-record" style="color: #00a651;"> Present</i>
                                                <?php  } if($status == 2)  { ?>
                                                            <i class="entypo-record" style="color: #ee4749;"> Absent</i>
                                <?php  } $status =0;?>


                                </td>
                                
                                <td style="text-align: center;">
                                    <?php
                                    $status = 0;
                                    
                                    $attendance = $this->db->get_where('attendance', array('section_id' => $section_id, 'class_id' => $class_id, 'year' => $running_year, 'timestamp' => $timestamp, 'period' => 3, 'student_id' => $row['student_id']))->result_array();
    
    
                                    foreach ($attendance as $row1):
                                      
        
                                        $status = $row1['status'];
    
    
                                    endforeach;
                                    ?>
                                    
                                    <?php if ($status == 1) { ?>
                                                            <i class="entypo-record" style="color: #00a651;"> Present</i>
                                                <?php  } if($status == 2)  { ?>
                                                            <i class="entypo-record" style="color: #ee4749;"> Absent</i>
                                    <?php  } $status =0;?>
                                </td>
                                
                                
                                <td style="text-align: center;">
                                    <?php
                                    $status = 0;
                                    
                                    $attendance = $this->db->get_where('attendance', array('section_id' => $section_id, 'class_id' => $class_id, 'year' => $running_year, 'timestamp' => $timestamp, 'period' => 4, 'student_id' => $row['student_id']))->result_array();
    
    
                                    foreach ($attendance as $row1):
                                      
        
                                        $status = $row1['status'];
    
    
                                    endforeach;
                                    ?>
                                    
                                    <?php if ($status == 1) { ?>
                                                            <i class="entypo-record" style="color: #00a651;"> Present</i>
                                                <?php  } if($status == 2)  { ?>
                                                            <i class="entypo-record" style="color: #ee4749;"> Absent</i>
                                    <?php  } $status =0;?>
                                </td>
                                
                                <td style="text-align: center;">
                                    <?php
                                    $status = 0;
                                    
                                    $attendance = $this->db->get_where('attendance', array('section_id' => $section_id, 'class_id' => $class_id, 'year' => $running_year, 'timestamp' => $timestamp, 'period' => 5, 'student_id' => $row['student_id']))->result_array();
    
    
                                    foreach ($attendance as $row1):
                                      
        
                                        $status = $row1['status'];
    
    
                                    endforeach;
                                    ?>
                                    
                                    <?php if ($status == 1) { ?>
                                                            <i class="entypo-record" style="color: #00a651;"> Present</i>
                                                <?php  } if($status == 2)  { ?>
                                                            <i class="entypo-record" style="color: #ee4749;"> Absent</i>
                                    <?php  } $status =0;?>
                                </td>
                                
                                <td style="text-align: center;">
                                    <?php
                                    $status = 0;
                                    
                                    $attendance = $this->db->get_where('attendance', array('section_id' => $section_id, 'class_id' => $class_id, 'year' => $running_year, 'timestamp' => $timestamp, 'period' => 6, 'student_id' => $row['student_id']))->result_array();
    
    
                                    foreach ($attendance as $row1):
                                      
        
                                        $status = $row1['status'];
    
    
                                    endforeach;
                                    ?>
                                    
                                    <?php if ($status == 1) { ?>
                                                            <i class="entypo-record" style="color: #00a651;"> Present</i>
                                                <?php  } if($status == 2)  { ?>
                                                            <i class="entypo-record" style="color: #ee4749;"> Absent</i>
                                    <?php  } $status =0;?>
                                </td>


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


