<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Sms_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }
    
    
    
    function send_sms_attendance_update($period = '' , $class_id = '', $section_id = '', $subject_id = '', $yes = '')
    {
        //MSG91 - Automatic SMS
                                            date_default_timezone_set("Asia/Kolkata");
                                            $currentdate = date("F j, Y, g:i A"); 
                                            $teachername = $this->db->get_where($this->session->userdata('login_type'), array($this->session->userdata('login_type').'_id' => $this->session->userdata('login_user_id')))->row()->name;
                                            $teachermobile = $this->db->get_where($this->session->userdata('login_type'), array($this->session->userdata('login_type').'_id' => $this->session->userdata('login_user_id')))->row()->phone;
                                            $classname = $this->db->get_where('class', array('class_id' => $class_id))->row()->name;
                                            $sectionname = $this->db->get_where('section', array('section_id' => $section_id))->row()->name;
                                            $subjectname = $this->db->get_where('subject', array('subject_id' => $subject_id))->row()->name;
                                           
                                            //Your authentication key
                                            $authKey = "241180AHm3MP4Jt8Of5bb70449";
                                            
                                            $admin_mobile = '8769323385';
                                            
                                            
                                            //Sender ID,While using route4 sender id should be 6 characters long.
                                            $senderId = "XMSERP";
                                            
                                            //Your message to send, Add URL encoding here.
                                            $message = 'Dear '.$teachername.', Attendance has been updated successfully for the Class '.$classname.' Section '.$sectionname.', Period '.$period.' and Subject: '.$subjectname.'. Updation Time: '.$currentdate.'.';
                                            
                                            //Define route 
                                            $route = "4";
                                            //Prepare you post parameters
                                            $postData = array(
                                                'authkey' => $authKey,
                                                'mobiles' => $teachermobile,
                                                'message' => $message,
                                                'sender' => $senderId,
                                                'route' => $route
                                            );
                                            
                                            //API URL
                                            $url="http://api.msg91.com/api/sendhttp.php";
                                            
                                            // init the resource
                                            $ch = curl_init();
                                            curl_setopt_array($ch, array(
                                                CURLOPT_URL => $url,
                                                CURLOPT_RETURNTRANSFER => true,
                                                CURLOPT_POST => true,
                                                CURLOPT_POSTFIELDS => $postData
                                                //,CURLOPT_FOLLOWLOCATION => true
                                            ));
                                            
                                            
                                            //Ignore SSL certificate verification
                                            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
                                            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
                                            
                                            
                                            //get response
                                            $output = curl_exec($ch);
                                            
                                            //Print error if any
                                            if(curl_errno($ch))
                                            {
                                                echo 'error:' . curl_error($ch);
                                            }
                                            
                                            curl_close($ch);
                                            
                                            //MSG91 - Automatic SMS Ends
        
    }
    
    
    
    
    
    
    
    
    
    

    //COMMON FUNCTION FOR SENDING SMS
    function send_sms($message = '' , $reciever_phone = '')
    {
        $active_sms_service = $this->db->get_where('settings' , array(
            'type' => 'active_sms_service'
        ))->row()->description;
        if ($active_sms_service == '' || $active_sms_service == 'disabled')
            return;

        if ($active_sms_service == 'clickatell') {
            $this->send_sms_via_clickatell($message , $reciever_phone );
        }
        if ($active_sms_service == 'twilio') {
            $this->send_sms_via_twilio($message , $reciever_phone );
        }
        if ($active_sms_service == 'msg91') {
            $this->send_sms_via_msg91($message , $reciever_phone );
        }
    }

    // SEND SMS VIA CLICKATELL API
    function send_sms_via_clickatell($message = '' , $reciever_phone = '') {

        $clickatell_user       = $this->db->get_where('settings', array('type' => 'clickatell_user'))->row()->description;
        $clickatell_password   = $this->db->get_where('settings', array('type' => 'clickatell_password'))->row()->description;
        $clickatell_api_id     = $this->db->get_where('settings', array('type' => 'clickatell_api_id'))->row()->description;
        $clickatell_baseurl    = "http://api.clickatell.com";

        $text   = urlencode($message);
        $to     = $reciever_phone;

        // auth call
        $url = "$clickatell_baseurl/http/auth?user=$clickatell_user&password=$clickatell_password&api_id=$clickatell_api_id";

        // do auth call
        $ret = file($url);

        // explode our response. return string is on first line of the data returned
        $sess = explode(":",$ret[0]);
        print_r($sess);echo '<br>';
        if ($sess[0] == "OK") {

            $sess_id = trim($sess[1]); // remove any whitespace
            $url = "$clickatell_baseurl/http/sendmsg?session_id=$sess_id&to=$to&text=$text";

            // do sendmsg call
            $ret = file($url);
            $send = explode(":",$ret[0]);
            print_r($send);echo '<br>';
            if ($send[0] == "ID") {
                echo "successnmessage ID: ". $send[1];
            } else {
                echo "send message failed";
            }
        } else {
            echo "Authentication failure: ". $ret[0];
        }
    }


    // SEND SMS VIA TWILIO API
    function send_sms_via_twilio($message = '' , $reciever_phone = '') {

        // LOAD TWILIO LIBRARY
        require_once(APPPATH . 'libraries/twilio_library/Twilio.php');


        $account_sid    = $this->db->get_where('settings', array('type' => 'twilio_account_sid'))->row()->description;
        $auth_token     = $this->db->get_where('settings', array('type' => 'twilio_auth_token'))->row()->description;
        $client         = new Services_Twilio($account_sid, $auth_token);

        $client->account->messages->create(array(
            'To'        => $reciever_phone,
            'From'      => $this->db->get_where('settings', array('type' => 'twilio_sender_phone_number'))->row()->description,
            'Body'      => $message
        ));

    }

    //SMS via msg91
    function send_sms_via_msg91($message = '' , $reciever_phone = '') {

        $authKey       = $this->db->get_where('settings', array('type' => 'msg91_authentication_key'))->row()->description;
        $senderId      = $this->db->get_where('settings', array('type' => 'msg91_sender_ID'))->row()->description;
        $country_code  = $this->db->get_where('settings', array('type' => 'msg91_country_code'))->row()->description;
        $route         = $this->db->get_where('settings', array('type' => 'msg91_route'))->row()->description;

        //Multiple mobiles numbers separated by comma
        $mobileNumber = $reciever_phone;

        //Your message to send, Add URL encoding here.
        $message = urlencode($message);

        //Prepare you post parameters
        $postData = array(
            'authkey' => $authKey,
            'mobiles' => $mobileNumber,
            'message' => $message,
            'sender' => $senderId,
            'route' => $route,
            'country' => $country_code
        );
        //API URL
        $url="http://api.msg91.com/api/sendhttp.php";

        // init the resource
        $ch = curl_init();
        curl_setopt_array($ch, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $postData
            //,CURLOPT_FOLLOWLOCATION => true
        ));


        //Ignore SSL certificate verification
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);


        //get response
        $output = curl_exec($ch);

        //Print error if any
        if(curl_errno($ch))
        {
            echo 'error:' . curl_error($ch);
        }

        curl_close($ch);
    }

    public function send_otp($mobile_no=""){
        $authKey = "241180AHm3MP4Jt8Of5bb70449";
        $admin_mobile = $mobile_no;
        $senderId = "XMSERP";
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "http://api.msg91.com/api/sendotp.php?authkey=".$authKey."&mobile=".$admin_mobile."&message=##OTP##"."&sender=".$senderId."&otp_expiry=1&otp_length=6",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => "",
            CURLOPT_SSL_VERIFYHOST => 0,
            CURLOPT_SSL_VERIFYPEER => 0,
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            return "cURL Error #:" . $err;
        } else {
            return $response;
        }
    }

    public function verify_otp($mobile_no="",$otp=""){
        $authKey = "241180AHm3MP4Jt8Of5bb70449";
        $admin_mobile = $mobile_no;
//        /$admin_mobile = '8769323385';
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "http://api.msg91.com/api/verifyRequestOTP.php?authkey=".$authKey."&mobile=".$admin_mobile."&otp=".$otp,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => "",
            CURLOPT_SSL_VERIFYHOST => 0,
            CURLOPT_SSL_VERIFYPEER => 0,
            CURLOPT_HTTPHEADER => array(
                "content-type: application/x-www-form-urlencoded"
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
        if ($err) {
            return "cURL Error #:" . $err;
        } else {
            return $response;
        }
    }

    public function resend_otp($mobile_no=""){
        $authKey = "241180AHm3MP4Jt8Of5bb70449";
        $admin_mobile = $mobile_no;
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "http://control.msg91.com/api/retryotp.php?authkey=".$authKey."&mobile=".$admin_mobile."&retrytype=text",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => "",
            CURLOPT_SSL_VERIFYHOST => 0,
            CURLOPT_SSL_VERIFYPEER => 0,
            CURLOPT_HTTPHEADER => array(
                "content-type: application/x-www-form-urlencoded"
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
        if ($err) {
            return "cURL Error #:" . $err;
        } else {
            return $response;
        }
    }
}
