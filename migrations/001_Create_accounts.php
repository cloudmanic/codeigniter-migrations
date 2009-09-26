<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
class Create_accounts {
	function up() 
	{
		$CI =& get_instance();
		if($CI->migrate->verbose)
			echo "Creating table accounts...";
		
		if(! $CI->db->table_exists('accounts')) {
			$cols = array(
				'Id' => array('type' => 'INT', 'constraint' => 5, 'unsigned' => TRUE, 'auto_increment' => TRUE),
				'Company_Name' => array('type' => 'VARCHAR', 'constraint' => '200', 'null' => FALSE),
				'First_Name' => array('type' => 'VARCHAR', 'constraint' => '200', 'null' => FALSE),
				'Last_Name' => array('type' => 'VARCHAR', 'constraint' => '200', 'null' => FALSE),
				'Phone' => array('type' => 'TEXT', 'null' => FALSE),
				'Email' => array('type' => 'TEXT', 'null' => FALSE),
				'Websites' => array('type' => 'TEXT', 'null' => FALSE),
				'Address' => array('type' => 'TEXT', 'null' => FALSE),
				'Last_Update' => array('type' => 'DATETIME', 'null' => FALSE)
			);
			
			// Setup Keys
			$CI->dbforge->add_key('Id', TRUE);
			$CI->dbforge->add_field($cols);
			$CI->dbforge->add_field("Created_At TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP");
			$CI->dbforge->create_table('accounts', TRUE);
		}
	}

	function down() 
	{
		$CI =& get_instance();
		if($CI->migrate->verbose)
			echo "Dropping table accounts...";
		$CI->dbforge->drop_table('accounts');
	}
}

?>
