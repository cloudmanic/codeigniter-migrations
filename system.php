<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
class System {
  function System()
  {       
    // Things that should run each page load.
		$this->CI =& get_instance();
		$this->setup_system_config();
		$this->set_profitloss();
		
		// Make sure our database is up-to-date
		$this->CI->migrate->setverbose(FALSE);
		if(! $this->CI->migrate->version($this->CI->config->item('migrations_version')))
			show_error($this->CI->migrate->error);
  }

	//
	// Setup Config.
	//
	function setup_system_config()
	{		
		$this->setup_system_db();
		$query = $this->CI->db->get('Config');
		foreach ($query->result() AS $row)
			$this->CI->config->set_item($row->ConfigName, $row->ConfigData);		
		return 1;
	}
	
	//
	// Setup the required tables.
	//
	function setup_system_db()
  {
  	$this->CI->load->dbforge();
  	     
		// Setup Sessions
		if(! $this->CI->db->table_exists('Sessions')) {
			$cols = array(
				'session_id' => array('type' => 'VARCHAR', 'constraint' => '40', 'null' => FALSE, 'default' => 0),
				'ip_address' => array('type' => 'VARCHAR', 'constraint' => '16', 'null' => FALSE, 'default' => 0),
				'user_agent' => array('type' => 'VARCHAR', 'constraint' => '50', 'null' => FALSE),
				'last_activity' => array('type' => 'INT', 'constraint' => '10', 'null' => FALSE),
				'user_data' => array('type' => 'TEXT', 'null' => FALSE)
			);
			$this->CI->dbforge->add_key('session_id', TRUE);
			$this->CI->dbforge->add_field($cols);
    	$this->CI->dbforge->create_table('Sessions', TRUE);
		}
		
		// Setup Config table
		if(! $this->CI->db->table_exists('Config')) {
			$cols = array(
				'ConfigId' => array('type' => 'INT', 'constraint' => 5, 'unsigned' => TRUE, 'auto_increment' => TRUE),
				'ConfigName' => array('type' => 'VARCHAR', 'constraint' => '25', 'null' => FALSE),
				'ConfigData' => array('type' => 'TEXT', 'null' => FALSE),
				'ConfigLastUpdate' => array('type' => 'DATETIME', 'null' => FALSE)
			);
			
			// Setup Keys
			$this->CI->dbforge->add_key('ConfigId', TRUE);
			$this->CI->dbforge->add_field($cols);
			$this->CI->dbforge->add_field("ConfigCreatedAt TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP");
			$this->CI->dbforge->create_table('Config', TRUE);
			
			// Required to get boot strapped.
			$insert = array('ConfigName' => 'migrationversion', 'ConfigData' => '0');
			$this->CI->db->insert('Config', $insert);
		}
		return 1;
	}
}
?>