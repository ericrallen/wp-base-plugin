<?php

	//options for our plug-in
	class Plugin_Name_Options {
		//IMPORTANT: Update the version number here whenever you release a new version
		public $v_num = '0.0.1';

		//prefix for option names, table names, and capability names
		public $prefix = 'plugin_name_';

		//namespace for any Debug messages
		public $namespace = 'PLUGIN NAME';

		//initialize vars for tables, options, and capabilities
		public $tables;
		public $opts;
		public $caps;
		public $load;

		//initialize options
		public function __construct() {
			$this->set_options();
			$this->set_capabilities();
			$this->set_tables();
		}

		//set up options array
		private function set_options() {
			$this->opts = array(
				$this->prefix . 'version' => $this->v_num,
				$this->prefix . 'options' => array(

					//add options to this array as 'option_name' => 'option_value'
					//this allows us to only store two options in the table
					//one will keep our version number and the other will keep a JSON encoded
					//string of all of our other options

				)
			);
		}

		//set up capability array
		private function set_capabilities() {
			//add capabilities to this array as 'required_capability' => 'capability_to_grant'
			$this->caps = array(
				'manage_options' => array(
					$this->prefix . 'capability'
				)
			);
		}

		//set up table array
		private function set_tables() {
			//set the table name as a key for the $this->tables array
			//and add the MySQL CREATE statement as the value for that key
			$this->tables['main'] = "CREATE TABLE `" . $this->prefix . 'main' . "` (
					`ID` int(15) NOT NULL AUTO_INCREMENT,
					`column_name` varchar(255),
					PRIMARY KEY (`ID`)
				) ENGINE=InnoDB DEFAULT CHARSET=latin1"
			;
		}
	}

?>