<?php
	$CI = get_instance();
	$CI->load->database();
	$CI->load->dbforge();

	$field1 = array(
        'phone' => array('type' => 'LONGTEXT')
	);
	$CI->dbforge->add_column('admin', $field1);

	$field2 = array(
        'address' => array('type' => 'LONGTEXT')
	);
	$CI->dbforge->add_column('admin', $field2);
?>
