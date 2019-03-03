<!-- Footer -->
<footer class="main">
	&copy; 2018 <strong> <?php echo $this->db->get_where('settings' , array('type'=>'system_name'))->row()->description; ?> | Version 5.5</strong>
    Developed by
	<a href="http://creativeitem.com"
    	target="_blank">Creativeitem</a>
</footer>
