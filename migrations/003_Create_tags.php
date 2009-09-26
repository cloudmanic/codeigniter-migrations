<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
class Create_tags {
	function up() 
	{
		$CI =& get_instance();
		
		if($CI->migrate->verbose)
			echo "Creating table tags...";
		
		if(! $CI->db->table_exists('tags')) {
			$cols = array(
		  	'Tags_Id' => array('type' => 'INT', 'constraint' => 5, 'unsigned' => TRUE, 'auto_increment' => TRUE),
		  	'Tags_Name' => array('type' => 'VARCHAR', 'constraint' => '50', 'null' => FALSE)
		  );
		  
		  // Setup Keys
		  $CI->dbforge->add_key('Tags_Id', TRUE);
		  $CI->dbforge->add_key('Tags_Name');
		  $CI->dbforge->add_field($cols);
		  $CI->dbforge->add_field("Created_At TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP");
		  $CI->dbforge->create_table('tags', TRUE);
		}
	}

	function down() 
	{
		$CI =& get_instance();
		if($CI->migrate->verbose)
			echo "Dropping table tags...";
		$CI->dbforge->drop_table('tags');
	}
}

?>
