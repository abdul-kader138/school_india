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
		<img src="<?php echo base_url('uploads/logo.png');?>" style="max-height : 60px;"><br>
		<h3 style="font-weight: 100;"><?php echo $system_name;?></h3>
		<?php echo get_phrase('attendance_sheet');?><br>
		<?php echo get_phrase('class') . ' ' . $class_name;?><br>
		<?php echo get_phrase('section').' '.$section_name;?><br>
        <?php echo $m . ', ' . $sessional_year; ?>
		
	</center>
        
          <table border="1" style="width:100%; border-collapse:collapse;border: 1px solid #ccc; margin-top: 10px;">
                <thead>
                    <tr>
                        <td style="text-align: center;">
    <?php echo get_phrase('students'); ?> <i class="entypo-down-thin"></i> | <?php echo get_phrase('date'); ?> <i class="entypo-right-thin"></i>
                        </td>
    <?php
    $year = explode('-', $running_year);
    $days = cal_days_in_month(CAL_GREGORIAN, $month, $sessional_year);
    for ($i = 1; $i <= $days; $i++) {
        ?>
                            <td style="text-align: center;"><?php echo $i; ?></td>
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
                            <?php
                            $status = 0;
                            for ($i = 1; $i <= $days; $i++) {
                                $timestamp = strtotime($i . '-' . $month . '-' . $sessional_year);
                                //$this->db->group_by('timestamp');
                                $attendance1 = $this->db->get_where('attendance', array('section_id' => $section_id, 'class_id' => $class_id, 'year' => $running_year, 'timestamp' => $timestamp, 'period' => '1', 'student_id' => $row['student_id']))->result_array();
                                $attendance2 = $this->db->get_where('attendance', array('section_id' => $section_id, 'class_id' => $class_id, 'year' => $running_year, 'timestamp' => $timestamp, 'period' => '2', 'student_id' => $row['student_id']))->result_array();
                                $attendance3 = $this->db->get_where('attendance', array('section_id' => $section_id, 'class_id' => $class_id, 'year' => $running_year, 'timestamp' => $timestamp, 'period' => '3', 'student_id' => $row['student_id']))->result_array();
                                $attendance4 = $this->db->get_where('attendance', array('section_id' => $section_id, 'class_id' => $class_id, 'year' => $running_year, 'timestamp' => $timestamp, 'period' => '4', 'student_id' => $row['student_id']))->result_array();
                                $attendance5 = $this->db->get_where('attendance', array('section_id' => $section_id, 'class_id' => $class_id, 'year' => $running_year, 'timestamp' => $timestamp, 'period' => '5', 'student_id' => $row['student_id']))->result_array();
                                $attendance6 = $this->db->get_where('attendance', array('section_id' => $section_id, 'class_id' => $class_id, 'year' => $running_year, 'timestamp' => $timestamp, 'period' => '6', 'student_id' => $row['student_id']))->result_array();
                               

                               foreach ($attendance1 as $row1):
                                    $month_dummy = date('d', $row1['timestamp']);

                                    if ($i == $month_dummy)
                                    
                                    
                                   
                                   $status1 = $row1['status'];
								   
								   
									if ($status1 == '1')
									   {
										   $status_final1 = "1";
									   }
								
									else 
										{
											$status_final1 = "0";
										}
                                   
                                endforeach;
                                
                                
								foreach ($attendance2 as $row2):
                                    $month_dummy = date('d', $row2['timestamp']);

                                    if ($i == $month_dummy)
                                   
                                   $status2 = $row2['status'];
                                   if ($status2 == '1')
									   {
										   $status_final2 = "1";
									   }
									
									else 
										{
											$status_final2 = "0";
										}
                                endforeach;
                                
                                
								foreach ($attendance3 as $row3):
                                    $month_dummy = date('d', $row3['timestamp']);

                                    if ($i == $month_dummy)
                                   
                                   $status3 = $row3['status'];
                                  if ($status3 == '1')
									   {
										   $status_final3 = "1";
									   }
									
									else 
										{
											$status_final3 = "0";
										}
                                endforeach;
                                
                                
								
								foreach ($attendance4 as $row4):
                                    $month_dummy = date('d', $row4['timestamp']);

                                    if ($i == $month_dummy)
                                   
                                   $status4 = $row4['status'];
                                   if ($status4 == '1')
									   {
										   $status_final4 = "1";
									   }
									else 
										{
											$status_final4 = "0";
										}
                                endforeach;
                                
                                
								
								foreach ($attendance5 as $row5):
                                    $month_dummy = date('d', $row5['timestamp']);

                                    if ($i == $month_dummy)
                                   
                                   $status5 = $row5['status'];
                                   if ($status5 == '1')
									   {
										   $status_final5 = "1";
									   }
									else 
										{
											$status_final5 = "0";
										}
                                endforeach;
                                
                                
								
								foreach ($attendance6 as $row6):
                                    $month_dummy = date('d', $row6['timestamp']);

                                    if ($i == $month_dummy)
                                   
                                   $status6 = $row6['status'];
                                   if ($status6 == '1')
									   {
										   $status_final6 = "1";
									   }
									else 
										{
											$status_final6 = "0";
										}
                                endforeach;
                                ?>
                                
								
								<td style="text-align: center;" data-class="">
            <?php 
			
			 echo $status_final1 + $status_final2 + $status_final3 + $status_final4 + $status_final5 + $status_final6;
			?>
                                </td>

        <?php } ?>
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