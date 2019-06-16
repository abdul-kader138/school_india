<style>
    #action-form-submit
    {
        visibility: hidden;
    }
</style>
<hr/>
<div class="row">
    <div class="col-md-12">

        <ul class="nav nav-tabs bordered">
            <li class="active">
                <a href="#home" data-toggle="tab">
                    <span class="visible-xs"><i class="entypo-users"></i></span>
                    <span class="hidden-xs"><?php echo get_phrase('all_students'); ?></span>
                </a>
            </li>
            <?php
            $query = $this->db->get_where('section', array('class_id' => $class_id));
            if ($query->num_rows() > 0):
                $sections = $query->result_array();
                foreach ($sections as $row):
                    ?>
                    <li>
                        <a href="#<?php echo $row['section_id']; ?>" data-toggle="tab">
                            <span class="visible-xs"><i class="entypo-user"></i></span>
                            <span class="hidden-xs"><?php echo get_phrase('section'); ?><?php echo $row['name']; ?></span>
                        </a>
                    </li>
                <?php endforeach; ?>
            <?php endif; ?>
        </ul>

        <?php
        echo form_open('teacher/student_information_actions', 'id="action-form"');
        ?>
        <button type="button" class="btn btn-xs no-print pull-right" style="margin-top:15px;">
            <a href="#" id="excel" data-action="export_excel"><i class="fa fa-file-excel-o"></i> Export To Excel</a>
        </button>
        <input type="hidden" name="class_id" id="class_id" value="<?php echo $class_id; ?>"/>
        <input type="hidden" name="section_id" id="section_id" value="<?php echo $section_id; ?>"/>
        <input type="hidden" name="year" id="year" value="<?php echo $running_year; ?>"/>
        <input type="hidden" name="form_action" value="" id="form_action"/>
        <?= form_submit('performAction', 'performAction', 'id="action-form-submit"') ?>
        <?= form_close() ?>

        <div class="tab-content">
            <br>
            <div class="tab-pane active" id="home">

                <table id="student_info" class="table table-bordered datatable">
                    <thead>
                    <tr>
                        <th width="40">
                            <div><?php echo get_phrase('id'); ?></div>
                        </th>

                        <th>
                            <div><?php echo get_phrase('name'); ?></div>
                        </th>
                        <th class="span3">
                            <div><?php echo get_phrase('phone'); ?></div>
                        </th>

                        <th>
                            <div><?php echo get_phrase('options'); ?></div>
                        </th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    $data = array();
                    $this->db->select('enroll.*,student.name as nam,student.student_code,student.phone')
                        ->join('student', 'student.student_id=enroll.student_id', 'left');
                    $q = $this->db->get_where('enroll', array(
                        'class_id' => $class_id, 'year' => $running_year
                    ));
                    if ($q->num_rows() > 0) {
                        foreach (($q->result()) as $row) {
                            $data[] = $row;
                        }
                    }
                    foreach ($data as $row):?>
                        <tr>
                            <td><?php echo $row->student_code; ?></td>
                            <td> <?php echo $row->nam; ?> </td>
                            <td><?php echo $row->phone; ?></td>
                            <td>

                                <div class="btn-group">
                                    <button type="button" class="btn btn-default btn-sm dropdown-toggle"
                                            data-toggle="dropdown">
                                        Action <span class="caret"></span>
                                    </button>
                                    <ul class="dropdown-menu dropdown-default pull-right" role="menu">

                                        <!-- STUDENT MARKSHEET LINK  -->
                                        <li>
                                            <a href="<?php echo site_url('teacher/student_marksheet/' . $row->student_id); ?>">
                                                <i class="entypo-chart-bar"></i>
                                                <?php echo get_phrase('mark_sheet'); ?>
                                            </a>
                                        </li>


                                        <!-- STUDENT PROFILE LINK -->
                                        <li>
                                            <a href="<?php echo site_url('teacher/student_profile/' . $row->student_id); ?>">
                                                <i class="entypo-user"></i>
                                                <?php echo get_phrase('profile'); ?>
                                            </a>
                                        </li>

                                        <li>
                                            <a href="#"
                                               onclick="showAjaxModal('<?php echo site_url('modal/popup/student_id/' . $row->student_id); ?>');">
                                                <i class="entypo-vcard"></i>
                                                <?php echo get_phrase('generate_id'); ?>
                                            </a>
                                        </li>

                                        <!-- STUDENT EDITING LINK -->
                                    </ul>
                                </div>

                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>

            </div>
            <?php
            $query = $this->db->get_where('section', array('class_id' => $class_id));
            if ($query->num_rows() > 0):
                $sections = $query->result_array();
                foreach ($sections as $row):
                    ?>
                    <div class="tab-pane" id="<?php echo $row['section_id']; ?>">

                        <table id="student_info_sec" class="table table-bordered datatable">
                            <thead>
                            <tr>
                                <th width="40">
                                    <div><?php echo get_phrase('id'); ?></div>
                                </th>

                                <th>
                                    <div><?php echo get_phrase('name'); ?></div>
                                </th>
                                <th class="span3">
                                    <div><?php echo get_phrase('phone'); ?></div>
                                </th>

                                <th>
                                    <div><?php echo get_phrase('options'); ?></div>
                                </th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            $datas = array();
                            $this->db->select('enroll.*,student.name as nam,student.student_code,student.phone')
                                ->join('student', 'student.student_id=enroll.student_id', 'left');
                            $q = $this->db->get_where('enroll', array(
                                'class_id' => $class_id, 'section_id' => $row['section_id'], 'year' => $running_year
                            ));
                            if ($q->num_rows() > 0) {
                                foreach (($q->result()) as $rows) {
                                    $datas[] = $rows;
                                }
                            }
                            foreach ($datas as $rows):?>
                                <tr>
                                    <td><?php echo $rows->student_code; ?></td>
                                    <td> <?php echo $rows->nam; ?> </td>
                                    <td><?php echo $rows->phone; ?></td>

                                    <td>

                                        <div class="btn-group">
                                            <button type="button" class="btn btn-default btn-sm dropdown-toggle"
                                                    data-toggle="dropdown">
                                                Action <span class="caret"></span>
                                            </button>
                                            <ul class="dropdown-menu dropdown-default pull-right" role="menu">

                                                <!-- STUDENT PROFILE LINK -->
                                                <li>
                                                    <a href="#"
                                                       onclick="showAjaxModal('<?php echo site_url('modal/popup/modal_student_profile/' . $rows->student_id); ?>');">
                                                        <i class="entypo-user"></i>
                                                        <?php echo get_phrase('profile'); ?>
                                                    </a>
                                                </li>

                                                <li>
                                                    <a href="#"
                                                       onclick="showAjaxModal('<?php echo site_url('modal/popup/student_id/' . $rows->student_id); ?>');">
                                                        <i class="entypo-vcard"></i>
                                                        <?php echo get_phrase('generate_id'); ?>
                                                    </a>
                                                </li>

                                            </ul>
                                        </div>

                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>

                    </div>
                <?php endforeach; ?>
            <?php endif; ?>

        </div>


    </div>
</div>


<!-----  DATA TABLE EXPORT CONFIGURATIONS ---->
<script type="text/javascript">

    jQuery(document).ready(function ($) {
        var datatable = $(".datatable").dataTable();

        $('body').on('click', '#excel', function (e) {
            e.preventDefault();
            $('#form_action').val($(this).attr('data-action'));
            $('#action-form-submit').trigger('click');
        });
    });

</script>
