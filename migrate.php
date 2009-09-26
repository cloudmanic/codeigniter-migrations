<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Migrations
 *
 * An open source utility for Code Igniter inspired by Ruby on Rails
 *
 * @package		Migrations
 * @author		Mat’as Montes
 *
 * Rewritten into a lib By: 
 * 	Spicer Matthews <spicer@cloudmanic.com>
 * 	Cloudmanic Labs, LLC
 *	http://www.cloudmanic.com
 *
 */

// ------------------------------------------------------------------------

/**
 * Migrate Class
 *
 * Utility main controller.
 *
 * @package		Migrations
 * @author		Mat’as Montes
 */
class Migrate 
{
	var $migrations_enabled = FALSE;
	var $migrations_path = ".";
	var $verbose = FALSE;
	var $error = "";
	
	function Migrate() 
	{
		$this->CI =& get_instance();
			
		if(! $this->CI->config->item('migrations_enabled')) 
			show_404();
		if(! $this->CI->config->item('migrations_path'))
			show_404();
			
		$this->CI->lang->load("migrations");
		$this->migrations_path = $this->CI->config->item('migrations_path');

		if($this->migrations_path != '' && substr($this->migrations_path, -1) != '/')
			$this->migrations_path .= '/';
		
		$this->CI->load->dbforge();
	}

	//
	// This will set if there should be verbose output or not.
	//
	function setverbose($state)
	{
		$this->verbose = $state;
	}

	/**
	* Installs the schema up to the last version
	*
	* @access	public
	* @return	void	Outputs a report of the installation
	*/
	function install() 
	{
		$files = glob($this->migrations_path."*".EXT);
		$file_count = count($files);

		for($i=0; $i < $file_count; $i++) 
		{
			// Mark wrongly formatted files as FALSE for later filtering
			$name = basename($files[$i],EXT);
			if(!preg_match('/^\d{3}_(\w+)$/',$name)) $files[$i] = FALSE;
		}

		$migrations = array_filter($files);

		if(! empty($migrations)) {
			sort($migrations);
			$last_migration = basename(end($migrations));

			// Calculate the last migration step from existing migration
			// filenames and procceed to the standard version migration
			$last_version =	substr($last_migration,0,3);
			return $this->version(intval($last_version,10));
		} else {
			$this->error = $this->CI->lang->line("no_migrations_found");
			return 0;
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Migrate to a schema version
	 *
	 * Calls each migration step required to get to the schema version of
	 * choice
	 *
	 * @access	public
	 * @param $version integer	Target schema version
	 * @return	void			Outputs a report of the migration
	 */
	function version($version = 0) 
	{
		$schema_version = $this->_get_schema_version();
		$start = $schema_version;
		$stop = $version;

		if($version > $schema_version) {
			// Moving Up
			$start++;
			$stop++;
			$step = 1;
		} else {
			// Moving Down
			$step = -1;
		}

		$method = $step == 1 ? 'up' : 'down';
		$migrations = array();

		// We now prepare to actually DO the migrations

		// But first let's make sure that everything is the way it should be
		for($i=$start; $i != $stop; $i += $step) 
		{
			$f = glob(sprintf($this->migrations_path . '%03d_*.php', $i));
			
			// Only one migration per step is permitted
			if(count($f) > 1) { 
				$this->error = sprintf($this->CI->lang->line("multiple_migrations_version"),$i);
				return 0;
			}
			
			// Migration step not found
			if(count($f) == 0) { 
				// If trying to migrate up to a version greater than the last
				// existing one, migrate to the last one.
				if ($step == 1) 
					break;

				// If trying to migrate down but we're missing a step,
				// something must definitely be wrong.
				$this->error = sprintf($this->CI->lang->line("migration_not_found"),$i);
				return 0;
			}

			$file = basename($f[0]);
			$name = basename($f[0],EXT);

			// Filename validations
			if(preg_match('/^\d{3}_(\w+)$/', $name, $match)) {
				$match[1] = strtolower($match[1]);
				
				// Cannot repeat a migration at different steps
				if(in_array($match[1], $migrations)) {
					$this->error = sprintf($this->CI->lang->line("multiple_migrations_name"),$match[1]);
					return 0;
				}
				
				include $f[0];
				$class = ucfirst($match[1]);

				if(! class_exists($class)) {
					$this->error = sprintf($this->CI->lang->line("migration_class_doesnt_exist"),$class);
					return 0;
				}
				
				if(! is_callable(array($class,"up")) || ! is_callable(array($class,"down"))) {
					$this->error = sprintf($this->CI->lang->line('wrong_migration_interface'),$class);
					return 0;
				}

				$migrations[] = $match[1];
			} else { 
				$this->error = sprintf($this->CI->lang->line("invalid_migration_filename"),$file);
				return 0;
			}
		}

		$version = $i + ($step == 1 ? -1 : 0);

		// If there is any migration to proccess
		if(count($migrations)) { 
			if($this->verbose) {
				echo "<p>Current schema version: ".$schema_version."<br/>";
				echo "Moving ".$method." to version ".$version."</p>";
				echo "<hr/>";
			}
			
			// Loop Through the migrations
			foreach($migrations AS $m) 
			{
				if($this->verbose) {
					echo "$m:<br />";
					echo "<blockquote>";
				}
				
				$class = ucfirst($m);
				call_user_func(array($class, $method));
				
				if($this->verbose) {
					echo "</blockquote>";
					echo "<hr/>";
				}
				
				
				$schema_version += $step;
				$this->_update_schema_version($schema_version);
			}

			if($this->verbose) 
				echo "<p>All done. Schema is at version $schema_version.</p>";

		} else 
			if($this->verbose)
				echo "Nothing to do, bye!\n";
		
		return 1;
	}

	// --------------------------------------------------------------------

	/**
	 * Retrieves current schema version
	 *
	 * @access	private
	 * @return	integer	Current Schema version
	 */
	function _get_schema_version() 
	{
		return $this->CI->config->item('migrationversion');
	}

	// --------------------------------------------------------------------

	/**
	 * Stores the current schema version
	 *
	 * @access	private
	 * @param $schema_version integer	Schema version reached
	 * @return	void					Outputs a report of the migration
	 */
	function _update_schema_version($schema_version) 
	{
		$data = array('Config_Data' => $schema_version, 'Config_Last_Update' => date("Y-m-d g:i:s"));
		$this->CI->db->where('Config_Name', 'migrationversion');
		$this->CI->db->update('config', $data); 		
		return 1;
	}

}

?>
