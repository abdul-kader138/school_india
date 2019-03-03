<?php 
	$class_name		= $this->db->get_where('class' , array('class_id' => $class_id))->row()->name;
	$section_name  		= $this->db->get_where('section' , array('section_id' => $section_id))->row()->name;
	$system_name        =	$this->db->get_where('settings' , array('type'=>'system_name'))->row()->description;
	$running_year       =	$this->db->get_where('settings' , array('type'=>'running_year'))->row()->description;
        if($month == 1) $m = 'January';
        else if($month == 2) $m='February';
        else if($month == 3) $m='March';
        else if($month == 4) $m='April';
        else if($month == 5) $m='May';
        else if($month == 6) $m='June';
        else if($month == 7) $m='July';
        else if($month == 8) $m='August';
        else if($month == 9) $m='Sepetember';
        else if($month == 10) $m='October';
        else if($month == 11) $m='November';
        else if($month == 12) $m='December';
?>
<div id="print">
	<script src="<?php echo base_url('assets/js/jquery-1.11.0.min.js');?>"></script>
	<style type="text/css">
		td {
			padding: 5px;
		}
	</style>

	<center>
		<img src="<?php echo base_url('uploads/xaviers-logo-black.png');?>" style="max-height : 60px;"><br>
		<h3 style="font-weight: 100;"><strong><?php echo $system_name;?></strong></h3>
		<h3 style="font-weight: 100;"><?php echo get_phrase('daily_attendance_sheet');?></h3>
		<?php echo get_phrase('class') . ' ' . $class_name;?><br>
		<?php echo get_phrase('section').' '.$section_name;?><br>
        Date: <?php echo date("d-m-Y", $timestamp); ?><br><br>
		
	</center>
        
          <table border="1" style="width:100%; border-collapse:collapse;border: 1px solid #ccc; margin-top: 10px;">
                <thead>
                    <tr>
                        <th style="text-align: center;">
    <?php echo get_phrase('students'); ?> <i class="entypo-down-thin"></i> | <?php echo get_phrase('period'); ?> <i class="entypo-right-thin"></i>
                        </th>
    <?php

 

    for ($i = 1; $i <= 6; $i++) {
        ?>
                            <th style="text-align: center;">Period <?php echo $i; ?></th>
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
</div>



<script type="text/javascript">

	jQuery(document).ready(function($)
	{
		var elem = $('#print');
		PrintElem(elem);
		Popup(data);

	});

    function PrintElem(elem)
    {
        Popup($(elem).html());
    }

    function Popup(data) 
    {
        var mywindow = window.open('', 'my div', 'height=400,width=600');
        mywindow.document.write('<html><head><title></title>');
        //mywindow.document.write('<link rel="stylesheet" href="assets/css/print.css" type="text/css" />');
        mywindow.document.write('</head><body >');
        //mywindow.document.write('<style>.print{border : 1px;}</style>');
        mywindow.document.write(data);
        mywindow.document.write('</body></html>');

        mywindow.document.close(); // necessary for IE >= 10
        mywindow.focus(); // necessary for IE >= 10

        mywindow.print();
        mywindow.close();

        return true;
    }
</script>