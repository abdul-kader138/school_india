<hr />
<div class="row">
    <div class="col-md-12">

      <table class="table table-bordered datatable" id="table_export">
          <thead>
              <tr>
                  <th width="80"><div><?php echo get_phrase('id_no');?></div></th>
                  
                  <th><div><?php echo get_phrase('name');?></div></th>
                  <th class="span3"><div><?php echo get_phrase('class');?></div></th>
                  <th><div><?php echo get_phrase('phone');?></div></th>
                  <th><div><?php echo get_phrase('options');?></div></th>
              </tr>
          </thead>
          <tbody>
              <?php
                      foreach($student_information as $row):
                      $class_id = $this->db->get_where('enroll', array('student_id' => $row['student_id']))->row()->class_id;
                      ?>
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
                          $class_id = $this->db->get_where('enroll' , array(
                              'student_id' => $row['student_id']
                          ))->row()->class_id;
                          
                          $section_id = $this->db->get_where('enroll' , array(
                              'student_id' => $row['student_id']
                          ))->row()->section_id;
                          
                          echo $this->db->get_where('class' , array(
                              'class_id' => $class_id
                          ))->row()->name;
                          echo "&nbsp;";
                          echo $this->db->get_where('section' , array(
                              'section_id' => $section_id
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
                                  <a href="#" onclick="showAjaxModal('<?php echo site_url('modal/popup/student_profile_on_modal/'.$row['student_id']);?>');">
                                      <i class="entypo-user"></i>
                                          <?php echo get_phrase('profile');?>
                                      </a>
                              </li>

                              <!-- STUDENT EDITING LINK -->
                              
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
</div>
