<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
class Create_leads {
	function up() 
	{
		$CI =& get_instance();
		
		if($CI->migrate->verbose)
			echo "Creating table leads, lead_notes, lead_status...";
		
		// Setup leads table
		if(! $CI->db->table_exists('leads')) {
		  $leadstable = array(
		  	'Leads_Id' => array('type' => 'INT', 'constraint' => 5, 'unsigned' => TRUE, 'auto_increment' => TRUE),
		  	'Name' => array('type' => 'VARCHAR', 'constraint' => '200', 'null' => FALSE),
		  	'Contact_First_Name' => array('type' => 'VARCHAR', 'constraint' => '200', 'null' => FALSE),
		  	'Contact_Last_Name' => array('type' => 'VARCHAR', 'constraint' => '200', 'null' => FALSE),
		  	'Office_Phone' => array('type' => 'VARCHAR', 'constraint' => '100', 'null' => FALSE),
		  	'Cell_Phone' => array('type' => 'VARCHAR', 'constraint' => '100', 'null' => FALSE),
		  	'Contact_Email' => array('type' => 'VARCHAR', 'constraint' => '300', 'null' => FALSE),
		  	'Fax' => array('type' => 'VARCHAR', 'constraint' => '100', 'null' => FALSE),
		  	'Website' => array('type' => 'VARCHAR', 'constraint' => '300', 'null' => FALSE),
		  	'Address1' => array('type' => 'VARCHAR', 'constraint' => '200', 'null' => FALSE),
		  	'Address2' => array('type' => 'VARCHAR', 'constraint' => '200', 'null' => FALSE),
		  	'City' => array('type' => 'VARCHAR', 'constraint' => '200', 'null' => FALSE),
		  	'State' => array('type' => 'VARCHAR', 'constraint' => '100', 'null' => FALSE),
		  	'Zip' => array('type' => 'VARCHAR', 'constraint' => '200', 'null' => FALSE),
		  	'Site_City' => array('type' => 'INT', 'constraint' => 5, 'unsigned' => TRUE),
		  	'Lead_Status' => array('type' => 'INT', 'constraint' => 5, 'unsigned' => TRUE),
		  	'Lead_Owner' => array('type' => 'INT', 'constraint' => 5, 'unsigned' => TRUE),
		  	'Tags' => array('type' => 'TEXT', 'null' => FALSE)
		  );
		  
		  // Setup Keys
		  $CI->dbforge->add_key('Leads_Id', TRUE);
		  $CI->dbforge->add_key('Name');
		  $CI->dbforge->add_field($leadstable);
		  $CI->dbforge->add_field("Created_At TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP");
		  $CI->dbforge->create_table('leads', TRUE);
		}


		// Setup Status
		if(! $CI->db->table_exists('lead_status')) {
		  $status = array(
		  	'Lead_Status_Id' => array('type' => 'INT', 'constraint' => 5, 'unsigned' => TRUE, 'auto_increment' => TRUE),
		  	'Lead_Status_Name' => array('type' => 'VARCHAR', 'constraint' => '100', 'null' => FALSE)			
		  );
		  $CI->dbforge->add_field($status);
			$CI->dbforge->add_field("Lead_Status_Created_At TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP");
		  $CI->dbforge->add_key('Lead_Status_Id', TRUE);
		  $CI->dbforge->create_table('lead_status', TRUE);
		  $insert = array('Lead_Status_Name' => 'Not Reviewed');
		  $CI->db->insert('lead_status', $insert);
		  $insert = array('Lead_Status_Name' => 'Contact Tag');
		  $CI->db->insert('lead_status', $insert);
		  $insert = array('Lead_Status_Name' => 'Not Interested');
		  $CI->db->insert('lead_status', $insert);
		  $insert = array('Lead_Status_Name' => 'Check Back Later');
		  $CI->db->insert('lead_status', $insert);
		  $insert = array('Lead_Status_Name' => 'Pre-Sales');
		  $CI->db->insert('lead_status', $insert);
		  $insert = array('Lead_Status_Name' => 'Lost');
		  $CI->db->insert('lead_status', $insert);
		  $insert = array('Lead_Status_Name' => 'Junk Lead');
		  $CI->db->insert('lead_status', $insert);
		}
		
		// Setup Lead Notes
		if(! $CI->db->table_exists('lead_notes')) {
		  $notes = array(
		  	'Id' => array('type' => 'INT', 'constraint' => 5, 'unsigned' => TRUE, 'auto_increment' => TRUE),
		  	'Lead_Id' => array('type' => 'INT', 'constraint' => 12, 'unsigned' => TRUE),
		  	'Lead_Note' => array('type' => 'TEXT', 'null' => FALSE),
		  	'Lead_Note_Title' => array('type' => 'VARCHAR', 'constraint' => '200', 'null' => FALSE),
		  	'Lead_Note_Owner' => array('type' => 'INT', 'constraint' => 12, 'unsigned' => TRUE)		
		  );
		  $CI->dbforge->add_field($notes);
		  $CI->dbforge->add_field("Lead_Note_Created_At TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP");
		  $CI->dbforge->add_key('Id', TRUE);
		  $CI->dbforge->create_table('lead_notes', TRUE);
		}
	}

	function down() 
	{
		$CI =& get_instance();
		if($CI->migrate->verbose)
			echo "Dropping table leads, lead_notes, lead_status...";
		$CI->dbforge->drop_table('leads');
		$CI->dbforge->drop_table('lead_notes');
		$CI->dbforge->drop_table('lead_status');
	}
}

?>
