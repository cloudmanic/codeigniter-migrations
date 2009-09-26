<?php
class Dbmigrate extends Controller {
	//
	// Constructor.
	//
	function Dbmigrate()
	{
		parent::Controller();	
		$this->load->library('migrate');
		$this->migrate->setverbose(TRUE);
		
		/**
		/** VERY IMPORTANT - only turn this on when you need it. 
		/** 
		*/
		die();
	}
	
	//
	// This will migrate up to the configed migration version
	//
	function configversion()
	{
		if(! $this->migrate->version($this->config->item('migrations_version')))
			show_error($this->migrate->error);
		else
			echo "<br />Migration Successful<br />";
	}
	
	//
	// Install up to the most up-to-date version.
	//
	function install()
	{	
		if(! $this->migrate->install())
			show_error($this->migrate->error);
		else
			echo "<br />Migration Successful<br />";
	}
	
	//
	// Migrate to a particular version.
	//
	function version($id = NULL)
	{
		if(is_null($id)) 
			show_error("Must pass in an id.");
			
		if(! $this->migrate->version($id))
			show_error($this->migrate->error);
		else
			echo "<br />Migration Successful<br />";
	}
}
?>
