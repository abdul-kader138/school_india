<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/*
 *  @author   : Creativeitem
 *  date    : 14 september, 2017
 *  Ekattor School Management System Pro
 *  http://codecanyon.net/user/Creativeitem
 *  http://support.creativeitem.com
 */

require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Teacher extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->library('session');
        $this->load->model('Barcode_model');
        $this->load->model(array('Ajaxdataload_model' => 'ajaxload'));
        /*cache control*/
        $this->output->set_header('Last-Modified: ' . gmdate("D, d M Y H:i:s") . ' GMT');
        $this->output->set_header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
        $this->output->set_header('Pragma: no-cache');
        $this->output->set_header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
    }

    /***default functin, redirects to login page if no teacher logged in yet***/
    public function index()
    {
        if ($this->session->userdata('teacher_login') != 1)
            redirect(site_url('login'), 'refresh');
        if ($this->session->userdata('teacher_login') == 1)
            redirect(site_url('teacher/dashboard'), 'refresh');
    }

    /***TEACHER DASHBOARD***/
    function dashboard()
    {
        if ($this->session->userdata('teacher_login') != 1)
            redirect(base_url(), 'refresh');
        $page_data['page_name'] = 'dashboard';
        $page_data['page_title'] = get_phrase('teacher_dashboard');
        $this->load->view('backend/index', $page_data);
    }


    /*ENTRY OF A NEW STUDENT*/


    /****MANAGE STUDENTS CLASSWISE*****/

    function student_information($class_id = '')
    {
        if ($this->session->userdata('teacher_login') != 1)
            redirect('login', 'refresh');

        $page_data['page_name'] = 'student_information';
        $page_data['page_title'] = get_phrase('student_information') . " - " . get_phrase('class') . " : " .
            $this->crud_model->get_class_name($class_id);
        $page_data['class_id'] = $class_id;
        $this->load->view('backend/index', $page_data);
    }

    function student_profile($student_id)
    {
        if ($this->session->userdata('teacher_login') != 1) {
            redirect(base_url(), 'refresh');
        }
        $page_data['page_name'] = 'student_profile';
        $page_data['page_title'] = get_phrase('student_profile');
        $page_data['student_id'] = $student_id;
        $this->load->view('backend/index', $page_data);
    }

    function student_marksheet($student_id = '')
    {
        if ($this->session->userdata('teacher_login') != 1)
            redirect('login', 'refresh');
        $class_id = $this->db->get_where('enroll', array(
            'student_id' => $student_id, 'year' => $this->db->get_where('settings', array('type' => 'running_year'))->row()->description
        ))->row()->class_id;
        $student_name = $this->db->get_where('student', array('student_id' => $student_id))->row()->name;
        $class_name = $this->db->get_where('class', array('class_id' => $class_id))->row()->name;
        $page_data['page_name'] = 'student_marksheet';
        $page_data['page_title'] = get_phrase('marksheet_for') . ' ' . $student_name . ' (' . get_phrase('class') . ' ' . $class_name . ')';
        $page_data['student_id'] = $student_id;
        $page_data['class_id'] = $class_id;
        $this->load->view('backend/index', $page_data);
    }

    function student_marksheet_print_view($student_id, $exam_id)
    {
        if ($this->session->userdata('teacher_login') != 1)
            redirect('login', 'refresh');
        $class_id = $this->db->get_where('enroll', array(
            'student_id' => $student_id, 'year' => $this->db->get_where('settings', array('type' => 'running_year'))->row()->description
        ))->row()->class_id;
        $class_name = $this->db->get_where('class', array('class_id' => $class_id))->row()->name;

        $page_data['student_id'] = $student_id;
        $page_data['class_id'] = $class_id;
        $page_data['exam_id'] = $exam_id;
        $this->load->view('backend/teacher/student_marksheet_print_view', $page_data);
    }


    function get_class_section($class_id)
    {
        $sections = $this->db->get_where('section', array(
            'class_id' => $class_id
        ))->result_array();
        foreach ($sections as $row) {
            echo '<option value="' . $row['section_id'] . '">' . $row['name'] . '</option>';
        }
    }

    function get_class_subject($class_id)
    {
        $subject = $this->db->get_where('subject', array(
            'class_id' => $class_id, 'teacher_id' => $this->session->userdata('teacher_id')
        ))->result_array();
        foreach ($subject as $row) {
            echo '<option value="' . $row['subject_id'] . '">' . $row['name'] . '</option>';
        }
    }

    /****MANAGE TEACHERS*****/
    function teacher_list($param1 = '', $param2 = '')
    {
        if ($this->session->userdata('teacher_login') != 1)
            redirect(base_url(), 'refresh');

        if ($param1 == 'personal_profile') {
            $page_data['personal_profile'] = true;
            $page_data['current_teacher_id'] = $param2;
        }
        $page_data['teachers'] = $this->db->get('teacher')->result_array();
        $page_data['page_name'] = 'teacher';
        $page_data['page_title'] = get_phrase('teacher_list');
        $this->load->view('backend/index', $page_data);
    }


    /****MANAGE SUBJECTS*****/
    function subject($param1 = '', $param2 = '', $param3 = '')
    {
        if ($this->session->userdata('teacher_login') != 1)
            redirect(base_url(), 'refresh');
        if ($param1 == 'create') {
            $data['name'] = $this->input->post('name');
            $data['class_id'] = $this->input->post('class_id');

            if ($this->input->post('teacher_id') != null) {
                $data['teacher_id'] = $this->input->post('teacher_id');
            }
            $data['year'] = $this->db->get_where('settings', array('type' => 'running_year'))->row()->description;
            if ($data['class_id'] != '') {
                $this->db->insert('subject', $data);
                $this->session->set_flashdata('flash_message', get_phrase('data_added_successfully'));
            } else {
                $this->session->set_flashdata('error_message', get_phrase('select_class'));
            }

            redirect(site_url('teacher/subject/' . $data['class_id']), 'refresh');
        }
        if ($param1 == 'do_update') {
            $data['name'] = $this->input->post('name');
            $data['class_id'] = $this->input->post('class_id');

            if ($this->input->post('teacher_id') != null) {
                $data['teacher_id'] = $this->input->post('teacher_id');
            } else {
                $data['teacher_id'] = null;
            }
            $data['year'] = $this->db->get_where('settings', array('type' => 'running_year'))->row()->description;
            if ($data['class_id'] != '') {
                $this->db->where('subject_id', $param2);
                $this->db->update('subject', $data);
                $this->session->set_flashdata('flash_message', get_phrase('data_updated'));
            } else {
                $this->session->set_flashdata('error_message', get_phrase('select_class'));
            }

            redirect(site_url('teacher/subject/' . $data['class_id']), 'refresh');
        } else if ($param1 == 'edit') {
            $page_data['edit_data'] = $this->db->get_where('subject', array(
                'subject_id' => $param2
            ))->result_array();
        }
        if ($param1 == 'delete') {
            $this->db->where('subject_id', $param2);
            $this->db->delete('subject');
            redirect(site_url('teacher/subject/' . $param3), 'refresh');
        }
        $page_data['class_id'] = $param1;
        $page_data['subjects'] = $this->db->get_where('subject', array(
            'class_id' => $param1,
            'year' => $this->db->get_where('settings', array('type' => 'running_year'))->row()->description
        ))->result_array();
        $page_data['page_name'] = 'subject';
        $page_data['page_title'] = get_phrase('manage_subject');
        $this->load->view('backend/index', $page_data);
    }


    /****MANAGE EXAM MARKS*****/
    function marks_manage()
    {
        if ($this->session->userdata('teacher_login') != 1)
            redirect(base_url(), 'refresh');
        $page_data['page_name'] = 'marks_manage';
        $page_data['page_title'] = get_phrase('manage_exam_marks');
        $this->load->view('backend/index', $page_data);
    }

    function marks_manage_view($exam_id = '', $class_id = '', $section_id = '', $subject_id = '')
    {
        if ($this->session->userdata('teacher_login') != 1)
            redirect(base_url(), 'refresh');
        $page_data['exam_id'] = $exam_id;
        $page_data['class_id'] = $class_id;
        $page_data['subject_id'] = $subject_id;
        $page_data['section_id'] = $section_id;
        $page_data['page_name'] = 'marks_manage_view';
        $page_data['page_title'] = get_phrase('manage_exam_marks');
        $this->load->view('backend/index', $page_data);
    }

    function marks_selector()
    {
        if ($this->session->userdata('teacher_login') != 1)
            redirect(base_url(), 'refresh');

        $data['exam_id'] = $this->input->post('exam_id');
        $data['class_id'] = $this->input->post('class_id');
        $data['section_id'] = $this->input->post('section_id');
        $data['subject_id'] = $this->input->post('subject_id');
        $data['year'] = $this->db->get_where('settings', array('type' => 'running_year'))->row()->description;
        if ($data['class_id'] != '' && $data['exam_id'] != '') {
            $query = $this->db->get_where('mark', array(
                'exam_id' => $data['exam_id'],
                'class_id' => $data['class_id'],
                'section_id' => $data['section_id'],
                'subject_id' => $data['subject_id'],
                'year' => $data['year']
            ));
            if ($query->num_rows() < 1) {
                $students = $this->db->get_where('enroll', array(
                    'class_id' => $data['class_id'], 'section_id' => $data['section_id'], 'year' => $data['year']
                ))->result_array();
                foreach ($students as $row) {
                    $data['student_id'] = $row['student_id'];
                    $this->db->insert('mark', $data);
                }
            }
            redirect(site_url('teacher/marks_manage_view/' . $data['exam_id'] . '/' . $data['class_id'] . '/' . $data['section_id'] . '/' . $data['subject_id']), 'refresh');

        } else {
            $this->session->set_flashdata('error_message', get_phrase('select_all_the_fields'));
            $page_data['page_name'] = 'marks_manage';
            $page_data['page_title'] = get_phrase('manage_exam_marks');
            $this->load->view('backend/index', $page_data);
        }
    }

    function marks_update($exam_id = '', $class_id = '', $section_id = '', $subject_id = '')
    {
        $running_year = $this->db->get_where('settings', array('type' => 'running_year'))->row()->description;
        if ($class_id != '' && $exam_id != '') {
            $marks_of_students = $this->db->get_where('mark', array(
                'exam_id' => $exam_id,
                'class_id' => $class_id,
                'section_id' => $section_id,
                'year' => $running_year,
                'subject_id' => $subject_id
            ))->result_array();
            foreach ($marks_of_students as $row) {
                $obtained_marks = $this->input->post('marks_obtained_' . $row['mark_id']);
                $comment = $this->input->post('comment_' . $row['mark_id']);
                $this->db->where('mark_id', $row['mark_id']);
                $this->db->update('mark', array('mark_obtained' => $obtained_marks, 'comment' => $comment));
            }
            $this->session->set_flashdata('flash_message', get_phrase('marks_updated'));
            redirect(site_url('teacher/marks_manage_view/' . $exam_id . '/' . $class_id . '/' . $section_id . '/' . $subject_id), 'refresh');
        } else {
            $this->session->set_flashdata('error_message', get_phrase('select_all_the_fields'));
            $page_data['page_name'] = 'marks_manage';
            $page_data['page_title'] = get_phrase('manage_exam_marks');
            $this->load->view('backend/index', $page_data);
        }
    }

    function marks_get_subject($class_id)
    {
        $page_data['class_id'] = $class_id;
        $this->load->view('backend/teacher/marks_get_subject', $page_data);
    }


    // ACADEMIC SYLLABUS
    function academic_syllabus($class_id = '')
    {
        if ($this->session->userdata('teacher_login') != 1)
            redirect(base_url(), 'refresh');
        // detect the first class
        if ($class_id == '')
            $class_id = $this->db->get('class')->first_row()->class_id;

        $page_data['page_name'] = 'academic_syllabus';
        $page_data['page_title'] = get_phrase('academic_syllabus');
        $page_data['class_id'] = $class_id;
        $this->load->view('backend/index', $page_data);
    }

    function upload_academic_syllabus()
    {
        $data['academic_syllabus_code'] = substr(md5(rand(0, 1000000)), 0, 7);
        $data['title'] = $this->input->post('title');
        $data['description'] = $this->input->post('description');
        $data['class_id'] = $this->input->post('class_id');
        if ($this->input->post('subject_id') != null) {
            $data['subject_id'] = $this->input->post('subject_id');
        }
        $data['uploader_type'] = $this->session->userdata('login_type');
        $data['uploader_id'] = $this->session->userdata('login_user_id');
        $data['year'] = $this->db->get_where('settings', array('type' => 'running_year'))->row()->description;
        $data['timestamp'] = strtotime(date("Y-m-d H:i:s"));
        //uploading file using codeigniter upload library
        $files = $_FILES['file_name'];
        $this->load->library('upload');
        $config['upload_path'] = 'uploads/syllabus/';
        $config['allowed_types'] = '*';
        $_FILES['file_name']['name'] = $files['name'];
        $_FILES['file_name']['type'] = $files['type'];
        $_FILES['file_name']['tmp_name'] = $files['tmp_name'];
        $_FILES['file_name']['size'] = $files['size'];
        $this->upload->initialize($config);
        $this->upload->do_upload('file_name');

        $data['file_name'] = $_FILES['file_name']['name'];

        $this->db->insert('academic_syllabus', $data);
        $this->session->set_flashdata('flash_message', get_phrase('syllabus_uploaded'));
        redirect(site_url('teacher/academic_syllabus/' . $data['class_id']), 'refresh');

    }

    function download_academic_syllabus($academic_syllabus_code)
    {
        $file_name = $this->db->get_where('academic_syllabus', array(
            'academic_syllabus_code' => $academic_syllabus_code
        ))->row()->file_name;
        $this->load->helper('download');
        $data = file_get_contents("uploads/syllabus/" . $file_name);
        $name = $file_name;

        force_download($name, $data);
    }

    /*****BACKUP / RESTORE / DELETE DATA PAGE**********/
    function backup_restore($operation = '', $type = '')
    {
        if ($this->session->userdata('teacher_login') != 1)
            redirect(base_url(), 'refresh');

        if ($operation == 'create') {
            $this->crud_model->create_backup($type);
        }
        if ($operation == 'restore') {
            $this->crud_model->restore_backup();
            $this->session->set_flashdata('backup_message', 'Backup Restored');
            redirect(site_url('teacher/backup_restore'), 'refresh');
        }
        if ($operation == 'delete') {
            $this->crud_model->truncate($type);
            $this->session->set_flashdata('backup_message', 'Data removed');
            redirect(site_url('teacher/backup_restore'), 'refresh');
        }

        $page_data['page_info'] = 'Create backup / restore from backup';
        $page_data['page_name'] = 'backup_restore';
        $page_data['page_title'] = get_phrase('manage_backup_restore');
        $this->load->view('backend/index', $page_data);
    }

    /******MANAGE OWN PROFILE AND CHANGE PASSWORD***/
    function manage_profile($param1 = '', $param2 = '', $param3 = '')
    {
        if ($this->session->userdata('teacher_login') != 1)
            redirect(site_url('login'), 'refresh');
        if ($param1 == 'update_profile_info') {
            $data['name'] = $this->input->post('name');
            $data['email'] = $this->input->post('email');
            $validation = email_validation_for_edit($data['email'], $this->session->userdata('teacher_id'), 'teacher');
            if ($validation == 1) {
                $this->db->where('teacher_id', $this->session->userdata('teacher_id'));
                $this->db->update('teacher', $data);
                move_uploaded_file($_FILES['userfile']['tmp_name'], 'uploads/teacher_image/' . $this->session->userdata('teacher_id') . '.jpg');
                $this->session->set_flashdata('flash_message', get_phrase('account_updated'));
            } else {
                $this->session->set_flashdata('error_message', get_phrase('this_email_id_is_not_available'));
            }
            redirect(site_url('teacher/manage_profile/'), 'refresh');
        }
        if ($param1 == 'change_password') {
            $data['password'] = sha1($this->input->post('password'));
            $data['new_password'] = sha1($this->input->post('new_password'));
            $data['confirm_new_password'] = sha1($this->input->post('confirm_new_password'));

            $current_password = $this->db->get_where('teacher', array(
                'teacher_id' => $this->session->userdata('teacher_id')
            ))->row()->password;
            if ($current_password == $data['password'] && $data['new_password'] == $data['confirm_new_password']) {
                $this->db->where('teacher_id', $this->session->userdata('teacher_id'));
                $this->db->update('teacher', array(
                    'password' => $data['new_password']
                ));
                $this->session->set_flashdata('flash_message', get_phrase('password_updated'));
            } else {
                $this->session->set_flashdata('error_message', get_phrase('password_mismatch'));
            }
            redirect(site_url('teacher/manage_profile/'), 'refresh');
        }
        $page_data['page_name'] = 'manage_profile';
        $page_data['page_title'] = get_phrase('manage_profile');
        $page_data['edit_data'] = $this->db->get_where('teacher', array(
            'teacher_id' => $this->session->userdata('teacher_id')
        ))->result_array();
        $this->load->view('backend/index', $page_data);
    }

    /**********MANAGING CLASS ROUTINE******************/
    function class_routine($class_id)
    {
        if ($this->session->userdata('teacher_login') != 1)
            redirect(base_url(), 'refresh');
        $page_data['page_name'] = 'class_routine';
        $page_data['class_id'] = $class_id;
        $page_data['page_title'] = get_phrase('class_routine');
        $this->load->view('backend/index', $page_data);
    }

    function class_routine_print_view($class_id, $section_id)
    {
        if ($this->session->userdata('teacher_login') != 1)
            redirect('login', 'refresh');
        $page_data['class_id'] = $class_id;
        $page_data['section_id'] = $section_id;
        $this->load->view('backend/teacher/class_routine_print_view', $page_data);
    }

    /****** DAILY ATTENDANCE *****************/
    function manage_attendance()
    {
        if ($this->session->userdata('teacher_login') != 1)
            redirect(site_url('login'), 'refresh');

        $page_data['page_name'] = 'manage_attendance';
        $page_data['page_title'] = get_phrase('manage_attendance_of_class');
        $this->load->view('backend/index', $page_data);
    }

    function manage_attendance_view($class_id = '', $section_id = '', $timestamp = '', $subject_id = '', $teacher_id = '', $period = '')
    {
        if ($this->session->userdata('teacher_login') != 1)
            redirect(site_url('login'), 'refresh');

        $class_name = $this->db->get_where('class', array(
            'class_id' => $class_id
        ))->row()->name;
        $page_data['class_id'] = $class_id;
        $page_data['subject_id'] = $subject_id;
        $page_data['period'] = $period;
        $page_data['teacher_id'] = $teacher_id;
        $page_data['timestamp'] = $timestamp;
        $page_data['page_name'] = 'manage_attendance_view';
        $section_name = $this->db->get_where('section', array(
            'section_id' => $section_id
        ))->row()->name;
        $page_data['section_id'] = $section_id;
        $page_data['page_title'] = get_phrase('manage_attendance_of_class') . ' ' . $class_name . ' : ' . get_phrase('section') . ' ' . $section_name;
        $this->load->view('backend/index', $page_data);
    }


    function get_section($class_id)
    {
        $page_data['class_id'] = $class_id;
        $this->load->view('backend/teacher/manage_attendance_section_holder', $page_data);
    }

    function get_subject1($class_id)
    {
        $page_data['class_id'] = $class_id;
        $this->load->view('backend/teacher/manage_attendance_subject_holder1', $page_data);
    }


    function attendance_selector()
    {
        $data['class_id'] = $this->input->post('class_id');
        $data['year'] = $this->input->post('year');
        $data['timestamp'] = strtotime($this->input->post('timestamp'));
        $data['section_id'] = $this->input->post('section_id');
        $data['subject_id'] = $this->input->post('subject_id');
        $data['teacher_id'] = $this->input->post('teacher_id');
        $data['period'] = $this->input->post('period');

        $query = $this->db->get_where('attendance', array(
            'class_id' => $data['class_id'],
            'section_id' => $data['section_id'],
            'teacher_id' => $data['teacher_id'],
            'period' => $data['period'],
            'subject_id' => $data['subject_id'],
            'year' => $data['year'],
            'timestamp' => $data['timestamp']
        ));
        if ($query->num_rows() < 1) {
            $students = $this->db->get_where('enroll', array(
                'class_id' => $data['class_id'], 'section_id' => $data['section_id'], 'year' => $data['year']
            ))->result_array();

            foreach ($students as $row) {
                $attn_data['class_id'] = $data['class_id'];
                $attn_data['year'] = $data['year'];
                $attn_data['timestamp'] = $data['timestamp'];
                $attn_data['section_id'] = $data['section_id'];
                $attn_data['student_id'] = $row['student_id'];
                $attn_data['subject_id'] = $data['subject_id'];
                $attn_data['teacher_id'] = $data['teacher_id'];
                $attn_data['period'] = $data['period'];
                $this->db->insert('attendance', $attn_data);
            }

        }
        redirect(site_url('teacher/manage_attendance_view/' . $data['class_id'] . '/' . $data['section_id'] . '/' . $data['timestamp'] . '/' . $data['subject_id'] . '/' . $data['teacher_id'] . '/' . $data['period']), 'refresh');
    }

    function attendance_update($class_id = '', $section_id = '', $timestamp = '', $subject_id = '', $teacher_id = '', $period = '')
    {
        $running_year = $this->db->get_where('settings', array('type' => 'running_year'))->row()->description;
        //$active_sms_service = $this->db->get_where('settings' , array('type' => 'active_sms_service'))->row()->description;
        $attendance_of_students = $this->db->get_where('attendance', array(
            'class_id' => $class_id, 'section_id' => $section_id, 'year' => $running_year, 'subject_id' => $subject_id, 'period' => $period, 'timestamp' => $timestamp
        ))->result_array();
        foreach ($attendance_of_students as $row) {
            $attendance_status = $this->input->post('status_' . $row['attendance_id']);
            $this->db->where('attendance_id', $row['attendance_id']);
            $this->db->update('attendance', array('status' => $attendance_status));

            /* if ($attendance_status == 2) {

                if ($active_sms_service != '' || $active_sms_service != 'disabled') {
                    $student_name   = $this->db->get_where('student' , array('student_id' => $row['student_id']))->row()->name;
                    $parent_id      = $this->db->get_where('student' , array('student_id' => $row['student_id']))->row()->parent_id;
                    $message        = 'Your child' . ' ' . $student_name . 'is absent today.';
                    if($parent_id != null && $parent_id != 0){
                        $receiver_phone = $this->db->get_where('parent' , array('parent_id' => $parent_id))->row()->phone;
                        if($receiver_phone != '' || $receiver_phone != null){
                            $this->sms_model->send_sms($message,$receiver_phone);
                        }
                        else{
                            $this->session->set_flashdata('error_message' , get_phrase('parent_phone_number_is_not_found'));
                        }
                    }
                    else{
                        $this->session->set_flashdata('error_message' , get_phrase('parent_phone_number_is_not_found'));
                    }
                }
            }*/
        }
        $this->session->set_flashdata('flash_message', get_phrase('attendance_updated'));
        $this->sms_model->send_sms_attendance_update($period, $class_id, $section_id, $subject_id);
        redirect(site_url('teacher/manage_attendance/'), 'refresh');
    }


    ///////ATTENDANCE REPORT /////
    function attendance_report()
    {
        $page_data['month'] = date('m');
        $page_data['page_name'] = 'attendance_report';
        $page_data['page_title'] = get_phrase('attendance_report');
        $this->load->view('backend/index', $page_data);
    }

    function attendance_report_view($class_id = '', $section_id = '', $unix_timestamp = '')
    {
        if ($this->session->userdata('teacher_login') != 1)
            redirect(base_url(), 'refresh');

        $class_name = $this->db->get_where('class', array('class_id' => $class_id))->row()->name;
        $section_name = $this->db->get_where('section', array('section_id' => $section_id))->row()->name;
        $page_data['teacher'] = $this;
        $page_data['class_id'] = $class_id;
        $page_data['section_id'] = $section_id;
        $page_data['timestamp'] = $unix_timestamp;
        $page_data['page_name'] = 'attendance_report_view';
        $page_data['page_title'] = get_phrase('daily_attendance_report');
        $this->load->view('backend/index', $page_data);
    }

    function attendance_report_print_view($class_id = '', $section_id = '', $unix_timestamp = '')
    {
        if ($this->session->userdata('teacher_login') != 1)
            redirect(base_url(), 'refresh');

        $page_data['class_id'] = $class_id;
        $page_data['section_id'] = $section_id;
        $page_data['timestamp'] = $unix_timestamp;

        $this->load->view('backend/teacher/attendance_report_print_view', $page_data);
    }

    function attendance_report_selector()
    {
        $data['class_id'] = $this->input->post('class_id');
        $data['section_id'] = $this->input->post('section_id');
        $date_time = $this->input->post('timestamp');
        $unix_timestamp = strtotime($date_time);


        $data['running_year'] = $this->input->post('year');
        redirect(site_url('teacher/attendance_report_view/' . $data['class_id'] . '/' . $data['section_id'] . '/' . $unix_timestamp), 'refresh');
    }


    ///////ATTENDANCE REPORT /////
    function attendance_report_monthly()
    {
        $page_data['month'] = date('m');
        $page_data['page_name'] = 'attendance_report_monthly';
        $page_data['page_title'] = get_phrase('attendance_report_monthly');
        $this->load->view('backend/index', $page_data);
    }

    function attendance_report_view_monthly($class_id = '', $section_id = '', $month = '', $sessional_year = '')
    {
        if ($this->session->userdata('teacher_login') != 1)
            redirect(base_url(), 'refresh');

        $class_name = $this->db->get_where('class', array('class_id' => $class_id))->row()->name;
        $section_name = $this->db->get_where('section', array('section_id' => $section_id))->row()->name;
        $page_data['class_id'] = $class_id;
        $page_data['section_id'] = $section_id;
        $page_data['month'] = $month;
        $page_data['sessional_year'] = $sessional_year;
        $page_data['page_name'] = 'attendance_report_view_monthly';
        $page_data['teacher'] = $this;
        $page_data['page_title'] = get_phrase('attendance_report_monthly');
        $this->load->view('backend/index', $page_data);
    }

    function attendance_report_print_view_monthly($class_id = '', $section_id = '', $month = '', $sessional_year = '')
    {
        if ($this->session->userdata('teacher_login') != 1)
            redirect(base_url(), 'refresh');

        $page_data['class_id'] = $class_id;
        $page_data['section_id'] = $section_id;
        $page_data['month'] = $month;
        $page_data['sessional_year'] = $sessional_year;
        $this->load->view('backend/teacher/attendance_report_print_view_monthly', $page_data);
    }

    function attendance_report_selector_monthly()
    {
        if ($this->input->post('class_id') == '' || $this->input->post('sessional_year') == '') {
            $this->session->set_flashdata('error_message', get_phrase('please_make_sure_class_and_sessional_year_are_selected'));
            redirect(site_url('teacher/attendance_report_monthly'), 'refresh');
        }
        $data['class_id'] = $this->input->post('class_id');
        $data['section_id'] = $this->input->post('section_id');
        $data['month'] = $this->input->post('month');
        $data['sessional_year'] = $this->input->post('sessional_year');
        redirect(site_url('teacher/attendance_report_view_monthly/' . $data['class_id'] . '/' . $data['section_id'] . '/' . $data['month'] . '/' . $data['sessional_year']), 'refresh');
    }


    ///////ATTENDANCE REPORT YEARLY/////
    function attendance_report_yearly()
    {
        $page_data['month'] = date('m');
        $page_data['page_name'] = 'attendance_report_yearly';
        $page_data['page_title'] = get_phrase('attendance_report_yearly');
        $this->load->view('backend/index', $page_data);
    }

    function attendance_report_view_yearly($class_id = '', $section_id = '', $month = '', $sessional_year = '')
    {
        if ($this->session->userdata('teacher_login') != 1)
            redirect(base_url(), 'refresh');

        $page_data['class_id'] = $class_id;
        $page_data['section_id'] = $section_id;
        $page_data['month'] = $month;
        $page_data['sessional_year'] = $sessional_year;
        $page_data['teacher'] = $this;
        $page_data['page_name'] = 'attendance_report_view_yearly';
        $page_data['page_title'] = get_phrase('attendance_report_yearly');
        $this->load->view('backend/index', $page_data);
    }

    function attendance_report_print_view_yearly($class_id = '', $section_id = '', $month = '', $sessional_year = '')
    {
        if ($this->session->userdata('teacher_login') != 1)
            redirect(base_url(), 'refresh');

        $page_data['class_id'] = $class_id;
        $page_data['section_id'] = $section_id;
        $page_data['month'] = $month;
        $page_data['sessional_year'] = $sessional_year;
        $this->load->view('backend/teacher/attendance_report_print_view_yearly', $page_data);
    }

    function attendance_report_selector_yearly()
    {
        if ($this->input->post('class_id') == '' || $this->input->post('sessional_year') == '') {
            $this->session->set_flashdata('error_message', get_phrase('please_make_sure_class_and_sessional_year_are_selected'));
            redirect(site_url('teacher/attendance_report_yearly'), 'refresh');
        }
        $data['class_id'] = $this->input->post('class_id');
        $data['section_id'] = $this->input->post('section_id');
        $data['month'] = $this->input->post('month');
        $data['sessional_year'] = $this->input->post('sessional_year');
        redirect(site_url('teacher/attendance_report_view_yearly/' . $data['class_id'] . '/' . $data['section_id'] . '/' . $data['month'] . '/' . $data['sessional_year']), 'refresh');
    }


    ///////ATTENDANCE REPORT Subject Wise/////
    function attendance_report_subject()
    {
        $page_data['month'] = date('m');
        $page_data['page_name'] = 'attendance_report_subject';
        $page_data['page_title'] = get_phrase('attendance_report_subject_wise');
        $this->load->view('backend/index', $page_data);
    }

    function attendance_report_view_subject($class_id = '', $section_id = '', $month = '', $sessional_year = '', $subject_id = '')
    {
        if ($this->session->userdata('teacher_login') != 1)
            redirect(base_url(), 'refresh');
        $page_data['class_id'] = $class_id;
        $page_data['section_id'] = $section_id;
        $page_data['subject_id'] = $subject_id;
        $page_data['month'] = $month;
        $page_data['sessional_year'] = $sessional_year;
        $page_data['teacher'] = $this;
        $page_data['page_name'] = 'attendance_report_view_subject';
        $page_data['page_title'] = get_phrase('attendance_report_subject_wise');
        $this->load->view('backend/index', $page_data);
    }

    function attendance_report_print_view_subject($class_id = '', $section_id = '', $month = '', $sessional_year = '')
    {
        if ($this->session->userdata('teacher_login') != 1)
            redirect(base_url(), 'refresh');

        $page_data['class_id'] = $class_id;
        $page_data['section_id'] = $section_id;
        $page_data['month'] = $month;
        $page_data['sessional_year'] = $sessional_year;
        $this->load->view('backend/teacher/attendance_report_print_view_subject', $page_data);
    }



    //a.kader
    ///////ATTENDANCE REPORT Subject Wise/////
    function attendance_report_subject_yearly()
    {
        $page_data['month'] = date('m');
        $page_data['page_name'] = 'attendance_report_subject_yearly';
        $page_data['page_title'] = get_phrase('attendance_report_subject_yearly');
        $this->load->view('backend/index', $page_data);
    }

    function attendance_report_selector_subject_yearly()
    {
        if ($this->input->post('class_id') == '' || $this->input->post('sessional_year') == '') {
            $this->session->set_flashdata('error_message', get_phrase('please_make_sure_class_and_sessional_year_are_selected'));
            redirect(site_url('teacher/attendance_report_subject_yearly'), 'refresh');
        }
        $data['class_id'] = $this->input->post('class_id');
        $data['section_id'] = $this->input->post('section_id');
        $data['month'] = $this->input->post('month');
        $data['subject_id'] = $this->input->post('subject_id');
        $data['sessional_year'] = $this->input->post('sessional_year');
        redirect(site_url('teacher/attendance_report_view_subject_yearly/' . $data['class_id'] . '/' . $data['section_id'] . '/' . $data['sessional_year'] . '/' . $data['subject_id']), 'refresh');
    }

    function attendance_report_view_subject_yearly($class_id = '', $section_id = '', $sessional_year = '', $subject_id = '')
    {
        if ($this->session->userdata('teacher_login') != 1)
            redirect(base_url(), 'refresh');
        $page_data['class_id'] = $class_id;
        $page_data['section_id'] = $section_id;
        $page_data['subject_id'] = $subject_id;
        $page_data['sessional_year'] = $sessional_year;
        $page_data['teacher'] = $this;
        $page_data['page_name'] = 'attendance_report_view_subject_yearly';
        $page_data['page_title'] = get_phrase('attendance_report_view_subject_yearly');
        $this->load->view('backend/index', $page_data);
    }

// End A.Kader


    function attendance_report_selector_subject()
    {
        if ($this->input->post('class_id') == '' || $this->input->post('sessional_year') == '') {
            $this->session->set_flashdata('error_message', get_phrase('please_make_sure_class_and_sessional_year_are_selected'));
            redirect(site_url('teacher/attendance_report_subject'), 'refresh');
        }
        $data['class_id'] = $this->input->post('class_id');
        $data['section_id'] = $this->input->post('section_id');
        $data['month'] = $this->input->post('month');
        $data['subject_id'] = $this->input->post('subject_id');
        $data['sessional_year'] = $this->input->post('sessional_year');
        redirect(site_url('teacher/attendance_report_view_subject/' . $data['class_id'] . '/' . $data['section_id'] . '/' . $data['month'] . '/' . $data['sessional_year'] . '/' . $data['subject_id']), 'refresh');
    }


    /**********MANAGE LIBRARY / BOOKS********************/
    function book($param1 = '', $param2 = '', $param3 = '')
    {
        if ($this->session->userdata('teacher_login') != 1)
            redirect('login', 'refresh');

        $page_data['books'] = $this->db->get('book')->result_array();
        $page_data['page_name'] = 'book';
        $page_data['page_title'] = get_phrase('manage_library_books');
        $this->load->view('backend/index', $page_data);

    }
    /**********MANAGE TRANSPORT / VEHICLES / ROUTES
     * function transport($param1 = '', $param2 = '', $param3 = '')
     * {
     * if ($this->session->userdata('teacher_login') != 1)
     * redirect('login', 'refresh');
     *
     * $page_data['transports'] = $this->db->get('transport')->result_array();
     * $page_data['page_name']  = 'transport';
     * $page_data['page_title'] = get_phrase('manage_transport');
     * $this->load->view('backend/index', $page_data);
     *
     * } ********************/

    /***MANAGE EVENT / NOTICEBOARD, WILL BE SEEN BY ALL ACCOUNTS DASHBOARD**/
    function noticeboard($param1 = '', $param2 = '', $param3 = '')
    {
        if ($this->session->userdata('teacher_login') != 1)
            redirect(base_url(), 'refresh');

        if ($param1 == 'create') {
            $data['notice_title'] = $this->input->post('notice_title');
            $data['notice'] = $this->input->post('notice');
            $data['create_timestamp'] = strtotime($this->input->post('create_timestamp'));
            $this->db->insert('noticeboard', $data);
            redirect(site_url('teacher/noticeboard/'), 'refresh');
        }
        if ($param1 == 'do_update') {
            $data['notice_title'] = $this->input->post('notice_title');
            $data['notice'] = $this->input->post('notice');
            $data['create_timestamp'] = strtotime($this->input->post('create_timestamp'));
            $this->db->where('notice_id', $param2);
            $this->db->update('noticeboard', $data);
            $this->session->set_flashdata('flash_message', get_phrase('notice_updated'));
            redirect(site_url('teacher/noticeboard/'), 'refresh');
        } else if ($param1 == 'edit') {
            $page_data['edit_data'] = $this->db->get_where('noticeboard', array(
                'notice_id' => $param2
            ))->result_array();
        }
        if ($param1 == 'delete') {
            $this->db->where('notice_id', $param2);
            $this->db->delete('noticeboard');
            redirect(site_url('teacher/noticeboard/'), 'refresh');
        }
        $page_data['page_name'] = 'noticeboard';
        $page_data['page_title'] = get_phrase('manage_noticeboard');
        $page_data['notices'] = $this->db->get_where('noticeboard', array('status' => 1))->result_array();
        $this->load->view('backend/index', $page_data);
    }


    /**********MANAGE DOCUMENT / home work FOR A SPECIFIC CLASS or ALL*******************/
    function document($do = '', $document_id = '')
    {
        if ($this->session->userdata('teacher_login') != 1)
            redirect('login', 'refresh');
        if ($do == 'upload') {
            move_uploaded_file($_FILES["userfile"]["tmp_name"], "uploads/document/" . $_FILES["userfile"]["name"]);
            $data['document_name'] = $this->input->post('document_name');
            $data['file_name'] = $_FILES["userfile"]["name"];
            $data['file_size'] = $_FILES["userfile"]["size"];
            $this->db->insert('document', $data);
            redirect(site_url('teacher/manage_document'), 'refresh');
        }
        if ($do == 'delete') {
            $this->db->where('document_id', $document_id);
            $this->db->delete('document');
            redirect(site_url('teacher/manage_document'), 'refresh');
        }
        $page_data['page_name'] = 'manage_document';
        $page_data['page_title'] = get_phrase('manage_documents');
        $page_data['documents'] = $this->db->get('document')->result_array();
        $this->load->view('backend/index', $page_data);
    }

    /*********MANAGE STUDY MATERIAL************/
    function study_material($task = "", $document_id = "")
    {
        if ($this->session->userdata('teacher_login') != 1) {
            $this->session->set_userdata('last_page', current_url());
            redirect(base_url(), 'refresh');
        }

        if ($task == "create") {
            $this->crud_model->save_study_material_info();
            $this->session->set_flashdata('flash_message', get_phrase('study_material_info_saved_successfuly'));
            redirect(site_url('teacher/study_material/'), 'refresh');
        }

        if ($task == "update") {
            $this->crud_model->update_study_material_info($document_id);
            $this->session->set_flashdata('flash_message', get_phrase('study_material_info_updated_successfuly'));
            redirect(site_url('teacher/study_material/'), 'refresh');
        }

        if ($task == "delete") {
            $this->crud_model->delete_study_material_info($document_id);
            redirect(site_url('teacher/study_material/'), 'refresh');
        }

        $data['study_material_info'] = $this->crud_model->select_study_material_info_for_teacher();
        $data['page_name'] = 'study_material';
        $data['page_title'] = get_phrase('study_material');
        $this->load->view('backend/index', $data);
    }

    /* private messaging */

    function message($param1 = 'message_home', $param2 = '', $param3 = '')
    {
        if ($this->session->userdata('teacher_login') != 1) {
            $this->session->set_userdata('last_page', current_url());
            redirect(base_url(), 'refresh');
        }

        $max_size = 2097152;
        if ($param1 == 'send_new') {

            // Folder creation
            if (!file_exists('uploads/private_messaging_attached_file/')) {
                $oldmask = umask(0);  // helpful when used in linux server
                mkdir('uploads/private_messaging_attached_file/', 0777);
            }
            if ($_FILES['attached_file_on_messaging']['name'] != "") {
                if ($_FILES['attached_file_on_messaging']['size'] > $max_size) {
                    $this->session->set_flashdata('error_message', get_phrase('file_size_can_not_be_larger_that_2_Megabyte'));
                    redirect(site_url('teacher/message/message_new/'), 'refresh');
                } else {
                    $file_path = 'uploads/private_messaging_attached_file/' . $_FILES['attached_file_on_messaging']['name'];
                    move_uploaded_file($_FILES['attached_file_on_messaging']['tmp_name'], $file_path);
                }
            }
            $message_thread_code = $this->crud_model->send_new_private_message();
            $this->session->set_flashdata('flash_message', get_phrase('message_sent!'));
            redirect(site_url('teacher/message/message_read/' . $message_thread_code), 'refresh');
        }

        if ($param1 == 'send_reply') {

            if (!file_exists('uploads/private_messaging_attached_file/')) {
                $oldmask = umask(0);  // helpful when used in linux server
                mkdir('uploads/private_messaging_attached_file/', 0777);
            }
            if ($_FILES['attached_file_on_messaging']['name'] != "") {
                if ($_FILES['attached_file_on_messaging']['size'] > $max_size) {
                    $this->session->set_flashdata('error_message', get_phrase('file_size_can_not_be_larger_that_2_Megabyte'));
                    redirect(site_url('teacher/message/message_read/' . $param2), 'refresh');

                } else {
                    $file_path = 'uploads/private_messaging_attached_file/' . $_FILES['attached_file_on_messaging']['name'];
                    move_uploaded_file($_FILES['attached_file_on_messaging']['tmp_name'], $file_path);
                }
            }
            $this->crud_model->send_reply_message($param2);  //$param2 = message_thread_code
            $this->session->set_flashdata('flash_message', get_phrase('message_sent!'));
            redirect(site_url('teacher/message/message_read/' . $param2), 'refresh');
        }

        if ($param1 == 'message_read') {
            $page_data['current_message_thread_code'] = $param2;  // $param2 = message_thread_code
            $this->crud_model->mark_thread_messages_read($param2);
        }

        $page_data['message_inner_page_name'] = $param1;
        $page_data['page_name'] = 'message';
        $page_data['page_title'] = get_phrase('private_messaging');
        $this->load->view('backend/index', $page_data);
    }

    //GROUP MESSAGE
    function group_message($param1 = "group_message_home", $param2 = "")
    {
        if ($this->session->userdata('teacher_login') != 1)
            redirect(base_url(), 'refresh');
        $max_size = 2097152;

        if ($param1 == 'group_message_read') {
            $page_data['current_message_thread_code'] = $param2;
        } else if ($param1 == 'send_reply') {
            if (!file_exists('uploads/group_messaging_attached_file/')) {
                $oldmask = umask(0);  // helpful when used in linux server
                mkdir('uploads/group_messaging_attached_file/', 0777);
            }
            if ($_FILES['attached_file_on_messaging']['name'] != "") {
                if ($_FILES['attached_file_on_messaging']['size'] > $max_size) {
                    $this->session->set_flashdata('error_message', get_phrase('file_size_can_not_be_larger_that_2_Megabyte'));
                    redirect(site_url('teacher/group_message/group_message_read/' . $param2), 'refresh');
                } else {
                    $file_path = 'uploads/group_messaging_attached_file/' . $_FILES['attached_file_on_messaging']['name'];
                    move_uploaded_file($_FILES['attached_file_on_messaging']['tmp_name'], $file_path);
                }
            }

            $this->crud_model->send_reply_group_message($param2);  //$param2 = message_thread_code
            $this->session->set_flashdata('flash_message', get_phrase('message_sent!'));
            redirect(site_url('teacher/group_message/group_message_read/' . $param2), 'refresh');
        }
        $page_data['message_inner_page_name'] = $param1;
        $page_data['page_name'] = 'group_message';
        $page_data['page_title'] = get_phrase('group_messaging');
        $this->load->view('backend/index', $page_data);
    }

    // MANAGE QUESTION PAPERS
    function question_paper($param1 = "", $param2 = "")
    {
        if ($this->session->userdata('teacher_login') != 1) {
            $this->session->set_userdata('last_page', current_url());
            redirect(base_url(), 'refresh');
        }

        if ($param1 == "create") {
            $this->crud_model->create_question_paper();
            $this->session->set_flashdata('flash_message', get_phrase('data_created_successfully'));
            redirect(site_url('teacher/question_paper'), 'refresh');
        }

        if ($param1 == "update") {
            $this->crud_model->update_question_paper($param2);
            $this->session->set_flashdata('flash_message', get_phrase('data_updated_successfully'));
            redirect(site_url('teacher/question_paper'), 'refresh');
        }

        if ($param1 == "delete") {
            $this->crud_model->delete_question_paper($param2);
            $this->session->set_flashdata('flash_message', get_phrase('data_deleted_successfully'));
            redirect(site_url('teacher/question_paper'), 'refresh');
        }

        $data['page_name'] = 'question_paper';
        $data['page_title'] = get_phrase('question_paper');
        $this->load->view('backend/index', $data);
    }

    //new code
    function print_id($id)
    {
        if ($this->session->userdata('teacher_login') != 1)
            redirect(site_url('login'), 'refresh');
        $data['id'] = $id;
        $this->load->view('backend/teacher/print_id', $data);
    }


    function create_barcode($student_id)
    {

        return $this->Barcode_model->create_barcode($student_id);

    }

    // Details of searched student
    function student_details()
    {
        if ($this->session->userdata('teacher_login') != 1)
            redirect(base_url(), 'refresh');

        $student_identifier = $this->input->post('student_identifier');
        $query_by_code = $this->db->get_where('student', array('student_code' => $student_identifier));

        if ($query_by_code->num_rows() == 0) {
            $this->db->like('name', $student_identifier);
            $query_by_name = $this->db->get('student');
            if ($query_by_name->num_rows() == 0) {
                $this->session->set_flashdata('error_message', get_phrase('no_student_found'));
                redirect(site_url('teacher/dashboard'), 'refresh');
            } else {
                $page_data['student_information'] = $query_by_name->result_array();
            }
        } else {
            $page_data['student_information'] = $query_by_code->result_array();
        }
        $page_data['page_name'] = 'search_result';
        $page_data['page_title'] = get_phrase('search_result');
        $this->load->view('backend/index', $page_data);
    }

    function get_teachers()
    {
        if ($this->session->userdata('teacher_login') != 1)
            redirect(site_url('login'), 'refresh');

        $columns = array(
            0 => 'teacher_id',
            1 => 'photo',
            2 => 'name',
            3 => 'email',
            4 => 'phone',
            5 => 'teacher_id'
        );

        $limit = $this->input->post('length');
        $start = $this->input->post('start');
        $order = $columns[$this->input->post('order')[0]['column']];
        $dir = $this->input->post('order')[0]['dir'];

        $totalData = $this->ajaxload->all_teachers_count();
        $totalFiltered = $totalData;

        if (empty($this->input->post('search')['value'])) {
            $teachers = $this->ajaxload->all_teachers($limit, $start, $order, $dir);
        } else {
            $search = $this->input->post('search')['value'];
            $teachers = $this->ajaxload->teacher_search($limit, $start, $search, $order, $dir);
            $totalFiltered = $this->ajaxload->teacher_search_count($search);
        }

        $data = array();
        if (!empty($teachers)) {
            foreach ($teachers as $row) {

                $photo = '<img src="' . $this->crud_model->get_image_url('teacher', $row->teacher_id) . '" class="img-circle" width="30" />';

                $nestedData['teacher_id'] = $row->teacher_id;
                $nestedData['photo'] = $photo;
                $nestedData['name'] = $row->name;
                $nestedData['email'] = $row->email;
                $nestedData['phone'] = $row->phone;

                $data[] = $nestedData;
            }
        }

        $json_data = array(
            "draw" => intval($this->input->post('draw')),
            "recordsTotal" => intval($totalData),
            "recordsFiltered" => intval($totalFiltered),
            "data" => $data
        );

        echo json_encode($json_data);
    }

    function get_books()
    {
        if ($this->session->userdata('teacher_login') != 1)
            redirect(site_url('login'), 'refresh');

        $columns = array(
            0 => 'book_id',
            1 => 'name',
            2 => 'author',
            3 => 'description',
            4 => 'price',
            5 => 'class',
            6 => 'download',
            7 => 'book_id'
        );

        $limit = $this->input->post('length');
        $start = $this->input->post('start');
        $order = $columns[$this->input->post('order')[0]['column']];
        $dir = $this->input->post('order')[0]['dir'];

        $totalData = $this->ajaxload->all_books_count();
        $totalFiltered = $totalData;

        if (empty($this->input->post('search')['value'])) {
            $books = $this->ajaxload->all_books($limit, $start, $order, $dir);
        } else {
            $search = $this->input->post('search')['value'];
            $books = $this->ajaxload->book_search($limit, $start, $search, $order, $dir);
            $totalFiltered = $this->ajaxload->book_search_count($search);
        }

        $data = array();
        if (!empty($books)) {
            foreach ($books as $row) {
                if ($row->file_name == null)
                    $download = '';
                else
                    $download = '<a href="' . site_url("uploads/document/$row->file_name") . '" class="btn btn-blue btn-icon icon-left"><i class="entypo-download"></i>' . get_phrase('download') . '</a>';

                $nestedData['book_id'] = $row->book_id;
                $nestedData['name'] = $row->name;
                $nestedData['author'] = $row->author;
                $nestedData['description'] = $row->description;
                $nestedData['price'] = $row->price;
                $nestedData['class'] = $this->db->get_where('class', array('class_id' => $row->class_id))->row()->name;
                $nestedData['download'] = $download;

                $data[] = $nestedData;
            }
        }

        $json_data = array(
            "draw" => intval($this->input->post('draw')),
            "recordsTotal" => intval($totalData),
            "recordsFiltered" => intval($totalFiltered),
            "data" => $data
        );

        echo json_encode($json_data);
    }

//- codelover138@gmail.com
    public function getPresentHistory($attendences_details, $counter, $student_id)
    {
        $present = 0;
        foreach ($attendences_details as $info) {
            if ($info->ids == $student_id && $info->day == $counter) {
                $present = $info->present;
                break;
            }
        }
        return $present;
    }

    public function getDailyPresentHistory($attendences_details, $counter, $student_id)
    {
        $present_info = 0;
        foreach ($attendences_details as $info) {
            if ($info->ids == $student_id && $info->period == $counter) {
                $present_info = $info->status;
                break;
            }
        }
        return $present_info;
    }

    public function getYearlyPresentHistory($attendences_details, $counter, $student_id)
    {
        $present = "0";
        foreach ($attendences_details as $info) {
            if ($info->ids == $student_id && $info->month == $counter) {
                $present = $info->present;
                break;
            }
        }
        return $present;
    }

    public function getYearlyPresentSubject($attendences_details, $counter, $student_id)
    {
        $present = "0";
        foreach ($attendences_details as $info) {
            $ids = $this->getMonthIDFinancial($info->month);
            if ($info->ids == $student_id && $ids == $counter) {
                $present = $info->present;
                break;
            }
        }
        return $present;
    }

    public function getMonthName($val)
    {
        $month = "";
        switch ($val) {
            case "1":
                $month = "January";
                break;
            case "2":
                $month = "February";
                break;
            case "3":
                $month = "March";
                break;
            case "4":
                $month = "April";
                break;
            case "5":
                $month = "May";
                break;
            case "6":
                $month = "June";
                break;
            case "7":
                $month = "July";
                break;
            case "8":
                $month = "August";
                break;
            case "9":
                $month = "September";
                break;
            case "10":
                $month = "October";
                break;
            case "11":
                $month = "November";
                break;
            case "12":
                $month = "December";
                break;
            case "13":
                $month = "Total";
                break;
            default:
                $month = "";
                break;
        }
        return $month;
    }


    public function getMonthNameFinancial($val)
    {
        $month = "";
        switch ($val) {
            case "7":
                $month = "January";
                break;
            case "8":
                $month = "February";
                break;
            case "9":
                $month = "March";
                break;
            case "10":
                $month = "April";
                break;
            case "11":
                $month = "May";
                break;
            case "12":
                $month = "June";
                break;
            case "1":
                $month = "July";
                break;
            case "2":
                $month = "August";
                break;
            case "3":
                $month = "September";
                break;
            case "4":
                $month = "October";
                break;
            case "5":
                $month = "November";
                break;
            case "6":
                $month = "December";
                break;
            case "13":
                $month = "Total";
                break;
            default:
                $month = "";
                break;
        }
        return $month;
    }


    public function getCellByMonthDay($val)
    {
        $month = "";
        switch ($val) {
            case "1":
                $month = "B";
                break;
            case "2":
                $month = "C";
                break;
            case "3":
                $month = "D";
                break;
            case "4":
                $month = "E";
                break;
            case "5":
                $month = "F";
                break;
            case "6":
                $month = "G";
                break;
            case "7":
                $month = "H";
                break;
            case "8":
                $month = "I";
                break;
            case "9":
                $month = "J";
                break;
            case "10":
                $month = "K";
                break;
            case "11":
                $month = "L";
                break;
            case "12":
                $month = "M";
                break;
            case "13":
                $month = "N";
                break;
            case "14":
                $month = "O";
                break;
            case "15":
                $month = "P";
                break;
            case "16":
                $month = "Q";
                break;
            case "17":
                $month = "R";
                break;
            case "18":
                $month = "S";
                break;
            case "19":
                $month = "T";
                break;
            case "20":
                $month = "U";
                break;
            case "21":
                $month = "V";
                break;
            case "22":
                $month = "W";
                break;
            case "23":
                $month = "X";
                break;
            case "24":
                $month = "Y";
                break;
            case "25":
                $month = "Z";
                break;
            case "26":
                $month = "AA";
                break;
            case "27":
                $month = "AB";
                break;
            case "28":
                $month = "AC";
                break;
            case "29":
                $month = "AD";
                break;
            case "30":
                $month = "AE";
                break;
            case "31":
                $month = "AF";
                break;
            default:
                $month = "";
                break;
        }
        return $month;
    }


    function teacher_actions()
    {
        if ($this->session->userdata('teacher_login') != 1)
            redirect(site_url('login'), 'refresh');
        if ($this->input->post('form_action') == 'export_excel') {
            if ($this->input->post('form_action') == 'export_excel') {
                $class_id = $this->input->post('class_id');
                $section_id = $this->input->post('section_id');
                $subject_id = $this->input->post('subject_id');
                $sessional_year = $this->input->post('sessional_year');
                $running_year = $this->db->get_where('settings', array('type' => 'running_year'))->row()->description;

                $class_name = $this->db->get_where('class', array('class_id' => $class_id))->row()->name;
                $section_name = $this->db->get_where('section', array('section_id' => $section_id))->row()->name;
                $subject_name = $this->db->get_where('subject', array('subject_id' => $subject_id))->row()->name;
                $name = "Attendance_Report_Subject_Wise_Yearly_" . $class_name . "_" . $section_name . "_" . $subject_name . "_" . $sessional_year;
                $start_date = "'" . $sessional_year . '-07-31' . "'";
                $end_year = $sessional_year + 1;
                $end_date = "'" . $end_year . '-06-30' . "'";

                $spreadsheet = new Spreadsheet();
                $spreadsheet->setActiveSheetIndex(0);
                $spreadsheet->getActiveSheet()->setTitle("Subject_Wise_Yearly_Report");
                $spreadsheet->getActiveSheet()->SetCellValue('D1', "St. Xavier's College, Jaipur");
                $spreadsheet->getActiveSheet()->getStyle('D1')->getFont()->setSize(16);
                $spreadsheet->getActiveSheet()->SetCellValue('D2', "Year:");
                $spreadsheet->getActiveSheet()->SetCellValue('E2', $sessional_year);
                $spreadsheet->getActiveSheet()->SetCellValue('D3', "Class:");
                $spreadsheet->getActiveSheet()->SetCellValue('E3', $class_name);
                $spreadsheet->getActiveSheet()->SetCellValue('D4', "Section:");
                $spreadsheet->getActiveSheet()->SetCellValue('E4', $section_name);
                $spreadsheet->getActiveSheet()->SetCellValue('D5', "Subject:");
                $spreadsheet->getActiveSheet()->SetCellValue('E5', $subject_name);
                $spreadsheet->getActiveSheet()->SetCellValue('A7', "Name");
                $spreadsheet->getActiveSheet()->SetCellValue('B7', $this->getMonthNameFinancial(1));
                $spreadsheet->getActiveSheet()->SetCellValue('C7', $this->getMonthNameFinancial(2));
                $spreadsheet->getActiveSheet()->SetCellValue('D7', $this->getMonthNameFinancial(3));
                $spreadsheet->getActiveSheet()->SetCellValue('E7', $this->getMonthNameFinancial(4));
                $spreadsheet->getActiveSheet()->SetCellValue('F7', $this->getMonthNameFinancial(5));
                $spreadsheet->getActiveSheet()->SetCellValue('G7', $this->getMonthNameFinancial(6));
                $spreadsheet->getActiveSheet()->SetCellValue('H7', $this->getMonthNameFinancial(7));
                $spreadsheet->getActiveSheet()->SetCellValue('I7', $this->getMonthNameFinancial(8));
                $spreadsheet->getActiveSheet()->SetCellValue('J7', $this->getMonthNameFinancial(9));
                $spreadsheet->getActiveSheet()->SetCellValue('K7', $this->getMonthNameFinancial(10));
                $spreadsheet->getActiveSheet()->SetCellValue('L7', $this->getMonthNameFinancial(11));
                $spreadsheet->getActiveSheet()->SetCellValue('M7', $this->getMonthNameFinancial(12));
                $spreadsheet->getActiveSheet()->SetCellValue('N7', $this->getMonthNameFinancial(13));


                $sql = 'select ids, present,name,day,dates,month from (SELECT count(attendance_id) as present,(DATE_FORMAT(CONVERT_TZ(FROM_UNIXTIME(timestamp), "+00:00", "+00:30"), "%Y-%m-%d")) dates,DAY(DATE_FORMAT(CONVERT_TZ(FROM_UNIXTIME(timestamp), "+00:00", "+00:30"), "%Y-%m-%d")) as day,month(DATE_FORMAT(CONVERT_TZ(FROM_UNIXTIME(timestamp), "+00:00", "+00:30"), "%Y-%m-%d")) as month,student_id as ids  FROM `attendance` where status=1 and  section_id=' . $section_id . '  and attendance.class_id=' . $class_id . ' and attendance.subject_id=' . $subject_id . '  and  DATE_FORMAT(CONVERT_TZ(FROM_UNIXTIME(timestamp), "+00:00", "+00:30"), "%Y-%m-%d") between ' . $start_date . ' and  ' . $end_date . 'group by month(DATE_FORMAT(CONVERT_TZ(FROM_UNIXTIME(timestamp), "+00:00", "+00:30"), "%Y-%m-%d")), student_id order by student_id,(DATE_FORMAT(CONVERT_TZ(FROM_UNIXTIME(timestamp), "+00:00", "+00:30"), "%Y-%m-%d"))) as atten INNER join student on atten.ids=student.student_id ORDER by name,dates';
                $data = array();
                $q = $this->db->query($sql);
                if ($q->num_rows() > 0) {
                    foreach (($q->result()) as $row) {
                        $data[] = $row;
                    }
                }


                $sql_enroll = 'SELECT * FROM enroll inner join student on enroll.student_id=student.student_id where enroll.class_id=' . $class_id . ' and enroll.section_id=' . $section_id . ' and year="' . $running_year . '" order by student.name';
                $student_data = array();
                $q = $this->db->query($sql_enroll);
                if ($q->num_rows() > 0) {
                    foreach (($q->result()) as $row) {
                        $student_data[] = $row;
                    }
                }
                $rows = 8;
                foreach ($student_data as $row) {
                    $spreadsheet->getActiveSheet()->SetCellValue('A' . $rows, $row->name);
                    $total = 0;
                    for ($j = 1; $j <= 12; $j++) {
                        $present = $this->getYearlyPresentSubject($data, $j, $row->student_id);
                        $total = $total + $present;
                        if ($j == 1) {
                            if ($present != 0) $spreadsheet->getActiveSheet()->SetCellValue('B' . $rows, $present);
                            else $spreadsheet->getActiveSheet()->SetCellValue('B' . $rows, "-");
                        }
                        if ($j == 2) {
                            if ($present != 0) $spreadsheet->getActiveSheet()->SetCellValue('C' . $rows, $present);
                            else $spreadsheet->getActiveSheet()->SetCellValue('C' . $rows, "-");
                        }
                        if ($j == 3) {
                            if ($present != 0) $spreadsheet->getActiveSheet()->SetCellValue('D' . $rows, $present);
                            else $spreadsheet->getActiveSheet()->SetCellValue('D' . $rows, "-");
                        }
                        if ($j == 4) {
                            if ($present != 0) $spreadsheet->getActiveSheet()->SetCellValue('E' . $rows, $present);
                            else $spreadsheet->getActiveSheet()->SetCellValue('E' . $rows, "-");
                        }
                        if ($j == 5) {
                            if ($present != 0) $spreadsheet->getActiveSheet()->SetCellValue('F' . $rows, $present);
                            else $spreadsheet->getActiveSheet()->SetCellValue('F' . $rows, "-");
                        }
                        if ($j == 6) {
                            if ($present != 0) $spreadsheet->getActiveSheet()->SetCellValue('G' . $rows, $present);
                            else $spreadsheet->getActiveSheet()->SetCellValue('G' . $rows, "-");
                        }
                        if ($j == 7) {
                            if ($present != 0) $spreadsheet->getActiveSheet()->SetCellValue('H' . $rows, $present);
                            else $spreadsheet->getActiveSheet()->SetCellValue('H' . $rows, "-");
                        }
                        if ($j == 8) {
                            if ($present != 0) $spreadsheet->getActiveSheet()->SetCellValue('I' . $rows, $present);
                            else $spreadsheet->getActiveSheet()->SetCellValue('I' . $rows, "-");
                        }
                        if ($j == 9) {
                            if ($present != 0) $spreadsheet->getActiveSheet()->SetCellValue('J' . $rows, $present);
                            else $spreadsheet->getActiveSheet()->SetCellValue('J' . $rows, "-");
                        }
                        if ($j == 10) {
                            if ($present != 0) $spreadsheet->getActiveSheet()->SetCellValue('K' . $rows, $present);
                            else $spreadsheet->getActiveSheet()->SetCellValue('K' . $rows, "-");;
                        }
                        if ($j == 11) {
                            if ($present != 0) $spreadsheet->getActiveSheet()->SetCellValue('L' . $rows, $present);
                            else $spreadsheet->getActiveSheet()->SetCellValue('L' . $rows, "-");
                        }
                        if ($j == 12) {
                            if ($present != 0) $spreadsheet->getActiveSheet()->SetCellValue('M' . $rows, $present);
                            else $spreadsheet->getActiveSheet()->SetCellValue('M' . $rows, "-");
                        }

                    }
                    $spreadsheet->getActiveSheet()->SetCellValue('N' . $rows, $total);
                    $rows++;
                }
//                $writer = new Xlsx($spreadsheet);
//                header('Content-Type: application/vnd.ms-excel');
//                header('Content-Disposition: attachment;filename="'. $name .'.xlsx"');
//                header('Cache-Control: max-age=0');
//                $writer->save('php://output');


                header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
                header('Content-Disposition: attachment;filename="' . $name . '.xlsx"');
                header('Cache-Control: max-age=0');

                $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');

                $writer->save('php://output');
            }
        } else {
            redirect(site_url('teacher/dashboard'), 'refresh');
        }
    }

    function teacher_actions_yearly()
    {
        if ($this->session->userdata('teacher_login') != 1)
            redirect(site_url('login'), 'refresh');
        if ($this->input->post('form_action') == 'export_excel') {
            if ($this->input->post('form_action') == 'export_excel') {
                $class_id = $this->input->post('class_id');
                $section_id = $this->input->post('section_id');
                $sessional_year = $this->input->post('sessional_year');
                $running_year = $this->db->get_where('settings', array('type' => 'running_year'))->row()->description;

                $class_name = $this->db->get_where('class', array('class_id' => $class_id))->row()->name;
                $section_name = $this->db->get_where('section', array('section_id' => $section_id))->row()->name;
                $name = "Attendance_Report_Yearly_" . $class_name . "_" . $section_name . "_" . $sessional_year;
                $start_date = "'" . $sessional_year . '-07-31' . "'";
                $end_year = $sessional_year + 1;
                $end_date = "'" . $end_year . '-06-30' . "'";

                $spreadsheet = new Spreadsheet();
                $spreadsheet->setActiveSheetIndex(0);
                $spreadsheet->getActiveSheet()->setTitle("Yearly_Attendance_Report");
                $spreadsheet->getActiveSheet()->SetCellValue('D1', "St. Xavier's College, Jaipur");
                $spreadsheet->getActiveSheet()->getStyle('D1')->getFont()->setSize(16);
                $spreadsheet->getActiveSheet()->SetCellValue('D2', "Year:");
                $spreadsheet->getActiveSheet()->SetCellValue('E2', $sessional_year);
                $spreadsheet->getActiveSheet()->SetCellValue('D3', "Class:");
                $spreadsheet->getActiveSheet()->SetCellValue('E3', $class_name);
                $spreadsheet->getActiveSheet()->SetCellValue('D4', "Section:");
                $spreadsheet->getActiveSheet()->SetCellValue('E4', $section_name);
                $spreadsheet->getActiveSheet()->SetCellValue('A7', "Name");
                $spreadsheet->getActiveSheet()->SetCellValue('B7', $this->getMonthNameFinancial(1));
                $spreadsheet->getActiveSheet()->SetCellValue('C7', $this->getMonthNameFinancial(2));
                $spreadsheet->getActiveSheet()->SetCellValue('D7', $this->getMonthNameFinancial(3));
                $spreadsheet->getActiveSheet()->SetCellValue('E7', $this->getMonthNameFinancial(4));
                $spreadsheet->getActiveSheet()->SetCellValue('F7', $this->getMonthNameFinancial(5));
                $spreadsheet->getActiveSheet()->SetCellValue('G7', $this->getMonthNameFinancial(6));
                $spreadsheet->getActiveSheet()->SetCellValue('H7', $this->getMonthNameFinancial(7));
                $spreadsheet->getActiveSheet()->SetCellValue('I7', $this->getMonthNameFinancial(8));
                $spreadsheet->getActiveSheet()->SetCellValue('J7', $this->getMonthNameFinancial(9));
                $spreadsheet->getActiveSheet()->SetCellValue('K7', $this->getMonthNameFinancial(10));
                $spreadsheet->getActiveSheet()->SetCellValue('L7', $this->getMonthNameFinancial(11));
                $spreadsheet->getActiveSheet()->SetCellValue('M7', $this->getMonthNameFinancial(12));
                $spreadsheet->getActiveSheet()->SetCellValue('N7', $this->getMonthNameFinancial(13));


                $data = array();
                $sql = 'select ids, present,name,day,dates,month from (SELECT count(attendance_id) as present,(DATE_FORMAT(CONVERT_TZ(FROM_UNIXTIME(timestamp), "+00:00", "+00:30"), "%Y-%m-%d")) dates,DAY(DATE_FORMAT(CONVERT_TZ(FROM_UNIXTIME(timestamp), "+00:00", "+00:30"), "%Y-%m-%d")) as day,month(DATE_FORMAT(CONVERT_TZ(FROM_UNIXTIME(timestamp), "+00:00", "+00:30"), "%Y-%m-%d")) as month,student_id as ids  FROM `attendance` where status=1 and  DATE_FORMAT(CONVERT_TZ(FROM_UNIXTIME(timestamp), "+00:00", "+00:30"), "%Y-%m-%d") between ' . $start_date . ' and  ' . $end_date . ' and section_id=' . $section_id . '  and attendance.class_id=' . $class_id . ' group by month(DATE_FORMAT(CONVERT_TZ(FROM_UNIXTIME(timestamp), "+00:00", "+00:30"), "%Y-%m-%d")), student_id order by student_id,(DATE_FORMAT(CONVERT_TZ(FROM_UNIXTIME(timestamp), "+00:00", "+00:30"), "%Y-%m-%d"))) as atten INNER join student on atten.ids=student.student_id ORDER by name,dates';
                $q = $this->db->query($sql);
                if ($q->num_rows() > 0) {
                    foreach (($q->result()) as $row) {
                        $data[] = $row;
                    }
                }


                $sql_enroll = 'SELECT * FROM enroll inner join student on enroll.student_id=student.student_id where enroll.class_id=' . $class_id . ' and enroll.section_id=' . $section_id . ' and year="' . $running_year . '" order by student.name';
                $student_data = array();
                $q = $this->db->query($sql_enroll);
                if ($q->num_rows() > 0) {
                    foreach (($q->result()) as $row) {
                        $student_data[] = $row;
                    }
                }
                $rows = 8;
                foreach ($student_data as $row) {
                    $spreadsheet->getActiveSheet()->SetCellValue('A' . $rows, $row->name);
                    $total = 0;
                    for ($j = 1; $j <= 12; $j++) {
                        $present = $this->getYearlyPresentSubject($data, $j, $row->student_id);
                        $total = $total + $present;
                        if ($j == 1) {
                            if ($present != 0) $spreadsheet->getActiveSheet()->SetCellValue('B' . $rows, $present);
                            else $spreadsheet->getActiveSheet()->SetCellValue('B' . $rows, "-");
                        }
                        if ($j == 2) {
                            if ($present != 0) $spreadsheet->getActiveSheet()->SetCellValue('C' . $rows, $present);
                            else $spreadsheet->getActiveSheet()->SetCellValue('C' . $rows, "-");
                        }
                        if ($j == 3) {
                            if ($present != 0) $spreadsheet->getActiveSheet()->SetCellValue('D' . $rows, $present);
                            else $spreadsheet->getActiveSheet()->SetCellValue('D' . $rows, "-");
                        }
                        if ($j == 4) {
                            if ($present != 0) $spreadsheet->getActiveSheet()->SetCellValue('E' . $rows, $present);
                            else $spreadsheet->getActiveSheet()->SetCellValue('E' . $rows, "-");
                        }
                        if ($j == 5) {
                            if ($present != 0) $spreadsheet->getActiveSheet()->SetCellValue('F' . $rows, $present);
                            else $spreadsheet->getActiveSheet()->SetCellValue('F' . $rows, "-");
                        }
                        if ($j == 6) {
                            if ($present != 0) $spreadsheet->getActiveSheet()->SetCellValue('G' . $rows, $present);
                            else $spreadsheet->getActiveSheet()->SetCellValue('G' . $rows, "-");
                        }
                        if ($j == 7) {
                            if ($present != 0) $spreadsheet->getActiveSheet()->SetCellValue('H' . $rows, $present);
                            else $spreadsheet->getActiveSheet()->SetCellValue('H' . $rows, "-");
                        }
                        if ($j == 8) {
                            if ($present != 0) $spreadsheet->getActiveSheet()->SetCellValue('I' . $rows, $present);
                            else $spreadsheet->getActiveSheet()->SetCellValue('I' . $rows, "-");
                        }
                        if ($j == 9) {
                            if ($present != 0) $spreadsheet->getActiveSheet()->SetCellValue('J' . $rows, $present);
                            else $spreadsheet->getActiveSheet()->SetCellValue('J' . $rows, "-");
                        }
                        if ($j == 10) {
                            if ($present != 0) $spreadsheet->getActiveSheet()->SetCellValue('K' . $rows, $present);
                            else $spreadsheet->getActiveSheet()->SetCellValue('K' . $rows, "-");;
                        }
                        if ($j == 11) {
                            if ($present != 0) $spreadsheet->getActiveSheet()->SetCellValue('L' . $rows, $present);
                            else $spreadsheet->getActiveSheet()->SetCellValue('L' . $rows, "-");
                        }
                        if ($j == 12) {
                            if ($present != 0) $spreadsheet->getActiveSheet()->SetCellValue('M' . $rows, $present);
                            else $spreadsheet->getActiveSheet()->SetCellValue('M' . $rows, "-");
                        }

                    }
                    $spreadsheet->getActiveSheet()->SetCellValue('N' . $rows, $total);
                    $rows++;
                }
//                $writer = new Xlsx($spreadsheet);
//                header('Content-Type: application/vnd.ms-excel');
//                header('Content-Disposition: attachment;filename="'. $name .'.xlsx"');
//                header('Cache-Control: max-age=0');
//                $writer->save('php://output');


                header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
                header('Content-Disposition: attachment;filename="' . $name . '.xlsx"');
                header('Cache-Control: max-age=0');

                $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');

                $writer->save('php://output');
            }
        } else {
            redirect(site_url('teacher/dashboard'), 'refresh');
        }
    }

    function teacher_actions_monthly()
    {
        if ($this->session->userdata('teacher_login') != 1)
            redirect(site_url('login'), 'refresh');
        if ($this->input->post('form_action') == 'export_excel') {
            if ($this->input->post('form_action') == 'export_excel') {
                $class_id = $this->input->post('class_id');
                $section_id = $this->input->post('section_id');
                $month = $this->input->post('month');
                $sessional_year = $this->input->post('sessional_year');
                $running_year = $this->db->get_where('settings', array('type' => 'running_year'))->row()->description;

                $class_name = $this->db->get_where('class', array('class_id' => $class_id))->row()->name;
                $section_name = $this->db->get_where('section', array('section_id' => $section_id))->row()->name;

                $monthName = date("F", mktime(0, 0, 0, $month, 10));
                $name = "Attendance_Report_Monthly_" . $class_name . "_" . $section_name . "_" . $monthName;
                $year = explode('-', $running_year);
                $days = cal_days_in_month(CAL_GREGORIAN, $month, $sessional_year);

                $spreadsheet = new Spreadsheet();
                $spreadsheet->setActiveSheetIndex(0);
                $spreadsheet->getActiveSheet()->setTitle("Monthly_Attendance_Report");
                $spreadsheet->getActiveSheet()->SetCellValue('D1', "St. Xavier's College, Jaipur");
                $spreadsheet->getActiveSheet()->getStyle('D1')->getFont()->setSize(16);
                $spreadsheet->getActiveSheet()->SetCellValue('D2', "Year:");
                $spreadsheet->getActiveSheet()->SetCellValue('E2', $sessional_year);
                $spreadsheet->getActiveSheet()->SetCellValue('D3', "Month:");
                $spreadsheet->getActiveSheet()->SetCellValue('E3', $monthName);
                $spreadsheet->getActiveSheet()->SetCellValue('D4', "Class:");
                $spreadsheet->getActiveSheet()->SetCellValue('E4', $class_name);
                $spreadsheet->getActiveSheet()->SetCellValue('D5', "Section:");
                $spreadsheet->getActiveSheet()->SetCellValue('E5', $section_name);
                $spreadsheet->getActiveSheet()->SetCellValue("A7", "Name");
                for ($i = 1; $i <= $days; $i++) {
                    $spreadsheet->getActiveSheet()->SetCellValue($this->getCellByMonthDay($i) . "7", $i);
                }
                $spreadsheet->getActiveSheet()->SetCellValue('AG7', "Total");


                $sql = 'select ids, present,name,day,dates,month from (SELECT count(attendance_id) as present,(DATE_FORMAT(CONVERT_TZ(FROM_UNIXTIME(timestamp), "+00:00", "+00:30"), "%Y-%m-%d")) dates,DAY(DATE_FORMAT(CONVERT_TZ(FROM_UNIXTIME(timestamp), "+00:00", "+00:30"), "%Y-%m-%d")) as day,MONTH(FROM_UNIXTIME(timestamp)) as month,student_id as ids  FROM `attendance` where status=1 and MONTH(DATE_FORMAT(CONVERT_TZ(FROM_UNIXTIME(timestamp), "+00:00", "+00:30"), "%Y-%m-%d"))=' . $month . ' and section_id=' . $section_id . '  and attendance.class_id=' . $class_id . ' and  Year(DATE_FORMAT(CONVERT_TZ(FROM_UNIXTIME(timestamp), "+00:00", "+00:30"), "%Y-%m-%d"))="' . $sessional_year . '" group by (DATE_FORMAT(CONVERT_TZ(FROM_UNIXTIME(timestamp), "+00:00", "+00:30"), "%Y-%m-%d")), student_id order by student_id,(DATE_FORMAT(CONVERT_TZ(FROM_UNIXTIME(timestamp), "+00:00", "+00:30"), "%Y-%m-%d"))) as atten INNER join student on atten.ids=student.student_id ORDER by name,dates';
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
                $rows = 8;
                foreach ($student_data as $row) {
                    $spreadsheet->getActiveSheet()->SetCellValue('A' . $rows, $row->name);
                    $total = 0;
                    for ($j = 1; $j <= $days; $j++) {
                        $present = $this->getPresentHistory($data, $j, $row->student_id);
                        $total = $total + $present;
                        if ($present != 0) $spreadsheet->getActiveSheet()->SetCellValue($this->getCellByMonthDay($j) . $rows, $present);
                        else $spreadsheet->getActiveSheet()->SetCellValue($this->getCellByMonthDay($j) . $rows, "-");

                    }
                    $spreadsheet->getActiveSheet()->SetCellValue('AG' . $rows, $total);
                    $rows++;
                }

                header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
                header('Content-Disposition: attachment;filename="' . $name . '.xlsx"');
                header('Cache-Control: max-age=0');
                $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
                $writer->save('php://output');
            }
        } else {
            redirect(site_url('teacher/dashboard'), 'refresh');
        }
    }


    function student_information_actions()
    {
        if ($this->session->userdata('teacher_login') != 1)
            redirect(site_url('login'), 'refresh');
        if ($this->input->post('form_action') == 'export_excel') {
            if ($this->input->post('form_action') == 'export_excel') {
                $class_id = $this->input->post('class_id');
                $section_id = $this->input->post('section_id');
                $year = $this->input->post('year');
                $running_year = $this->db->get_where('settings', array('type' => 'running_year'))->row()->description;

                $class_name = $this->db->get_where('class', array('class_id' => $class_id))->row()->name;
                $section_name = $this->db->get_where('section', array('section_id' => $section_id))->row()->name;

                $spreadsheet = new Spreadsheet();
                $spreadsheet->setActiveSheetIndex(0);
                $spreadsheet->getActiveSheet()->setTitle("Subject_Wise_Yearly_Report");
                $spreadsheet->getActiveSheet()->SetCellValue('D1', "St. Xavier's College, Jaipur");
                $spreadsheet->getActiveSheet()->getStyle('D1')->getFont()->setSize(16);
                $spreadsheet->getActiveSheet()->SetCellValue('D2', "Year:");
                $spreadsheet->getActiveSheet()->SetCellValue('E2', $year);
                $spreadsheet->getActiveSheet()->SetCellValue('D3', "Class:");
                $spreadsheet->getActiveSheet()->SetCellValue('E3', $class_name);
                $spreadsheet->getActiveSheet()->SetCellValue('A7', "Student_Registration_No");
                $spreadsheet->getActiveSheet()->SetCellValue('B7', "Section_Name");
                $spreadsheet->getActiveSheet()->SetCellValue('C7', "Name");
                $spreadsheet->getActiveSheet()->SetCellValue('D7', "Sex");
                $spreadsheet->getActiveSheet()->SetCellValue('E7', "Address");
                $spreadsheet->getActiveSheet()->SetCellValue('F7', "Phone");
                $spreadsheet->getActiveSheet()->SetCellValue('G7', "Email");
                $spreadsheet->getActiveSheet()->SetCellValue('H7', "Parent_Name");
                $spreadsheet->getActiveSheet()->SetCellValue('I7', "Parent_Phone");
                $spreadsheet->getActiveSheet()->SetCellValue('J7', "Parent_Address");
                $spreadsheet->getActiveSheet()->SetCellValue('K7', "Parent_Email");

                $data = array();
                $this->db->select('student.*,enroll.student_id,enroll.section_id,section.name as sname,parent.name as pname, parent.address as paddress, parent.email as pemail,parent.phone as pphone')
                    ->join('student', 'student.student_id=enroll.student_id', 'left')
                    ->join('section', 'section.section_id=enroll.section_id', 'left')
                    ->join('parent', 'parent.parent_id=student.parent_id', 'left');
                $this->db->order_by('enroll.section_id', 'asc');
                $q = $this->db->get_where('enroll', array(
                    'enroll.class_id' => $class_id, 'year' => $running_year
                ));
                if ($q->num_rows() > 0) {
                    foreach (($q->result()) as $row) {
                        $data[] = $row;
                    }
                }
                $rows = 8;
                foreach ($data as $row) {
                    $spreadsheet->getActiveSheet()->SetCellValue('A' . $rows, $row->student_code);
                    $spreadsheet->getActiveSheet()->SetCellValue('B' . $rows, $row->sname);
                    $spreadsheet->getActiveSheet()->SetCellValue('C' . $rows, $row->name);
                    $spreadsheet->getActiveSheet()->SetCellValue('D' . $rows, $row->sex);
                    $spreadsheet->getActiveSheet()->SetCellValue('E' . $rows, $row->address);
                    $spreadsheet->getActiveSheet()->SetCellValue('F' . $rows, $row->phone);
                    $spreadsheet->getActiveSheet()->SetCellValue('G' . $rows, $row->email);
                    $spreadsheet->getActiveSheet()->SetCellValue('H' . $rows, $row->pname);
                    $spreadsheet->getActiveSheet()->SetCellValue('I' . $rows, $row->pphone);
                    $spreadsheet->getActiveSheet()->SetCellValue('J' . $rows, $row->paddress);
                    $spreadsheet->getActiveSheet()->SetCellValue('K' . $rows, $row->pemail);
                    $rows++;
                }
//                $writer = new Xlsx($spreadsheet);
//                header('Content-Type: application/vnd.ms-excel');
//                header('Content-Disposition: attachment;filename="'. $name .'.xlsx"');
//                header('Cache-Control: max-age=0');
//                $writer->save('php://output');


                header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
                header('Content-Disposition: attachment;filename="' . $class_name . '.xlsx"');
                header('Cache-Control: max-age=0');

                $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');

                $writer->save('php://output');
            }
        } else {
            redirect(site_url('teacher/dashboard'), 'refresh');
        }
    }


    public function getMonthIDFinancial($val)
    {
        $month = 0;
        switch ($val) {
            case 7:
                $month = 1;
                break;
            case 8:
                $month = 2;
                break;
            case 9:
                $month = 3;
                break;
            case 10:
                $month = 4;
                break;
            case 11:
                $month = 5;
                break;
            case 12:
                $month = 6;
                break;
            case 1:
                $month = 7;
                break;
            case 2:
                $month = 8;
                break;
            case 3:
                $month = 9;
                break;
            case 4:
                $month = 10;
                break;
            case 5:
                $month = 11;
                break;
            case 6:
                $month = 12;
                break;
            default:
                $month = 0;
                break;
        }
        return $month;
    }

}
