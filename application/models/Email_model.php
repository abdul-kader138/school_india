<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Email_model extends CI_Model {

	function __construct()
    {
        parent::__construct();
    }

	function account_opening_email($account_type = '' , $email = '', $password = '')
	{
		$system_name	=	$this->db->get_where('settings' , array('type' => 'system_name'))->row()->description;

		//$email_msg		=	"<img src=https://xms.stxaviersjaipur.org/uploads/header.jpg align=center width=100%><br><br>";
		$email_msg		=	"Welcome to Xavier's Management System - St. Xavier's College, Jaipur\n\n";
		$email_msg		.=	"Your account is now ready, You can log in to the XMS Portal using the below credentials.\n";
		$email_msg		.=	"Your Account Type : ".$account_type."\n\n";
		$email_msg		.=	"Your Login Id : ".$email."\n";
		$email_msg		.=	"Your Login Password : ". $password ."\n\n";
		$email_msg		.=	"Login Here : https://xms.stxaviersjaipur.org \n\n";

		$email_sub		=	"XMS - Account opening email";
		$email_to		=	$email;

		//$this->do_email($email_msg , $email_sub , $email_to);
		$this->send_php_mail($email_msg , $email_sub , $email_to);
	}


	function password_reset_email($new_password = '' , $account_type = '' , $email = '')
	{
		$query			=	$this->db->get_where($account_type , array('email' => $email));
		if($query->num_rows() > 0)
		{

			$email_msg	=	"Your account type is : ".$account_type."<br />";
			$email_msg	.=	"Your password is : ".$new_password."<br />";

			$email_sub	=	"Password reset request";
			$email_to	=	$email;
			//$this->do_email($email_msg , $email_sub , $email_to);
			$this->send_php_mail($email_msg , $email_sub , $email_to);
			return true;
		}
		else
		{
			return false;
		}
	}

	function contact_message_email($email_from, $email_to, $email_message) {
		$email_sub = "Message from School Website";
		//$this->do_email($email_message, $email_sub, $email_to, $email_from);
		$this->send_php_mail($email_message, $email_sub, $email_to, $email_from);
	}

    function personal_message_email($email_from, $email_to, $email_message) {
        $email_sub = "Message from School Website";
        //$this->do_email($email_message, $email_sub, $email_to, $email_from);
        $this->send_php_mail($email_message, $email_sub, $email_to, $email_from);
    }

	/***custom email sender****/
	function do_email($msg=NULL, $sub=NULL, $to=NULL, $from=NULL)
	{

		$config = array();
        $config['useragent']	= "CodeIgniter";
        $config['mailpath']		= "/usr/bin/sendmail"; // or "/usr/sbin/sendmail"
        $config['protocol']		= "smtp";
        $config['smtp_host']	= "localhost";
        $config['smtp_port']	= "25";
        $config['mailtype']		= 'html';
        $config['charset']		= 'utf-8';
        $config['newline']		= "\r\n";
        $config['wordwrap']		= TRUE;

        $this->load->library('email');

        $this->email->initialize($config);

		$system_name	=	$this->db->get_where('settings' , array('type' => 'system_name'))->row()->description;
		if($from == NULL)
			$from		=	$this->db->get_where('settings' , array('type' => 'system_email'))->row()->description;

		$this->email->from($from, $system_name);
		$this->email->from($from, $system_name);
		$this->email->to($to);
		$this->email->subject($sub);

		$msg	=	$msg."<br><br><hr><center>&copy; St. Xavier's College, Jaipur | Powered by <a href=\"https://broodle.xyz\">Broodle</a> | <a href=\"http://bit.ly/broodle-chat\">Need Help?</a></center><br>";
		$this->email->message($msg);

		$this->email->send();

		//echo $this->email->print_debugger();
	}

	public function send_php_mail($msg=NULL, $sub=NULL, $to=NULL, $from=NULL, $attachment_url=NULL) {

	   $from = $this->db->get_where('settings' , array('type' => 'system_email'))->row()->description;

	   $headers = "From: ".$from."\r\n";
	   $headers .= "Reply-To: ".$to."\r\n";
	   $headers .= "Return-Path: ".$to."\r\n";
	   //$headers .= "CC: almobin777@gmail.com\r\n";
	   //$headers .= "BCC: instance.of.venture@gmail.com\r\n";
	   if ( $attachment_url != NULL) {
		   $msg .=	"\r\nAttachment URL: ".$attachment_url;
	   }
	   if ( mail($to,$sub,$msg,$headers) ) {

	   } else {
		   echo "The email has failed!";
	   }
   }
}
