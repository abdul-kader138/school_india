<hr />


<div class="row">
    <div class="col-md-12">

        <ul class="nav nav-tabs bordered">
            <li class="active">
                <a href="#home" data-toggle="tab">
                    <span class="visible-xs"><i class="entypo-users"></i></span>
                    <span class="hidden-xs"><?php echo get_phrase('all_students');?></span>
                </a>
            </li>
        <?php
            $query = $this->db->get_where('section' , array('class_id' => $class_id));
            if ($query->num_rows() > 0):
                $sections = $query->result_array();
                foreach ($sections as $row):
        ?>
            <li>
                <a href="#<?php echo $row['section_id'];?>" data-toggle="tab">
                    <span class="visible-xs"><i class="entypo-user"></i></span>
                    <span class="hidden-xs"><?php echo get_phrase('section');?> <?php echo $row['name'];?></span>
                </a>
            </li>
        <?php endforeach;?>
        <?php endif;?>
        </ul>

        <div class="tab-content">
        <br>
            <div class="tab-pane active" id="home">

                <table class="table table-bordered datatable">
                    <thead>
                        <tr>
                            <th width="40"><div><?php echo get_phrase('id');?></div></th>
                         
                            <th><div><?php echo get_phrase('name');?></div></th>
                            <th class="span3"><div><?php echo get_phrase('phone');?></div></th>
                           
                            <th><div><?php echo get_phrase('options');?></div></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                                $students   =   $this->db->get_where('enroll' , array(
                                    'class_id' => $class_id , 'year' => $running_year
                                ))->result_array();
                                foreach($students as $row):?>
                        <tr>
                            <td><?php echo $this->db->get_where('student' , array(
                                    'student_id' => $row['student_id']
                                ))->row()->student_code;?></td>
                  
                            <td>
                                <?php
                                    echo $this->db->get_where('student' , array(
                                        'student_id' => $row['student_id']
                                    ))->row()->name;
                                ?>
                            </td>
                            <td>
                                <?php
                                    echo $this->db->get_where('student' , array(
                                        'student_id' => $row['student_id']
                                    ))->row()->phone;
                                ?>
                            </td>
                            
                            <td>

                                <div class="btn-group">
                                    <button type="button" class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown">
                                        Action <span class="caret"></span>
                                    </button>
                                    <ul class="dropdown-menu dropdown-default pull-right" role="menu">

                                        <!-- STUDENT MARKSHEET LINK  -->
                                        <li>
                                            <a href="<?php echo site_url('teacher/student_marksheet/'.$row['student_id']);?>">
                                                <i class="entypo-chart-bar"></i>
                                                    <?php echo get_phrase('mark_sheet');?>
                                                </a>
                                        </li>


                                        <!-- STUDENT PROFILE LINK -->
                                        <li>
                                            <a href="<?php echo site_url('teacher/student_profile/'.$row['student_id']);?>">
                                                <i class="entypo-user"></i>
                                                    <?php echo get_phrase('profile');?>
                                                </a>
                                        </li>
                                        
                                        <li>
                                          <a href="#" onclick="showAjaxModal('<?php echo site_url('modal/popup/student_id/'.$row['student_id']);?>');">
                                              <i class="entypo-vcard"></i>
                                              <?php echo get_phrase('generate_id');?>
                                          </a>
                                        </li>

                                        <!-- STUDENT EDITING LINK -->
                                    </ul>
                                </div>

                            </td>
                        </tr>
                        <?php endforeach;?>
                    </tbody>
                </table>

            </div>
        <?php
            $query = $this->db->get_where('section' , array('class_id' => $class_id));
            if ($query->num_rows() > 0):
                $sections = $query->result_array();
                foreach ($sections as $row):
        ?>
            <div class="tab-pane" id="<?php echo $row['section_id'];?>">

                <table class="table table-bordered datatable">
                    <thead>
                        <tr>
                            <th width="40"><div><?php echo get_phrase('id');?></div></th>
                            
                            <th><div><?php echo get_phrase('name');?></div></th>
                            <th class="span3"><div><?php echo get_phrase('phone');?></div></th>
                           
                            <th><div><?php echo get_phrase('options');?></div></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                                $students   =   $this->db->get_where('enroll' , array(
                                    'class_id'=>$class_id , 'section_id' => $row['section_id'] , 'year' => $running_year
                                ))->result_array();
                                foreach($students as $row):?>
                        <tr>
                            <td><?php echo $this->db->get_where('student',array('student_id'=>$row['student_id']))->row()->student_code;?></td>
                            <td>
                                <?php
                                    echo $this->db->get_where('student' , array(
                                        'student_id' => $row['student_id']
                                    ))->row()->name;
                                ?>
                            </td>
                            <td>
                                <?php
                                    echo $this->db->get_where('student' , array(
                                        'student_id' => $row['student_id']
                                    ))->row()->phone;
                                ?>
                            </td>
                            
                            <td>

                                <div class="btn-group">
                                    <button type="button" class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown">
                                        Action <span class="caret"></span>
                                    </button>
                                    <ul class="dropdown-menu dropdown-default pull-right" role="menu">

                                        <!-- STUDENT PROFILE LINK -->
                                        <li>
                                            <a href="#" onclick="showAjaxModal('<?php echo site_url('modal/popup/modal_student_profile/'.$row['student_id']);?>');">
                                                <i class="entypo-user"></i>
                                                    <?php echo get_phrase('profile');?>
                                                </a>
                                        </li>
                                        
                                        <li>
                                          <a href="#" onclick="showAjaxModal('<?php echo site_url('modal/popup/student_id/'.$row['student_id']);?>');">
                                              <i class="entypo-vcard"></i>
                                              <?php echo get_phrase('generate_id');?>
                                          </a>
                                        </li>

                                    </ul>
                                </div>

                            </td>
                        </tr>
                        <?php endforeach;?>
                    </tbody>
                </table>

            </div>
        <?php endforeach;?>
        <?php endif;?>

        </div>


    </div>
</div>



<!-----  DATA TABLE EXPORT CONFIGURATIONS ---->
<script type="text/javascript">

    jQuery(document).ready(function($)
    {


        var datatable = $(".datatable").dataTable();
    });

</script>
