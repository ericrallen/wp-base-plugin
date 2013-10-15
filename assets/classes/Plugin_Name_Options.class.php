<?php

	if(!class_exists('Plugin_Name_Options')) {

		//options for our plug-in
		class Plugin_Name_Options {

			//IMPORTANT: Update the version number here whenever you release a new version
			protected $v_num = '0.0.1';

			//prefix for option names, table names, and capability names
			protected $prefix = 'plugin_name_';

			//namespace for any Debug messages
			protected $namespace = 'PLUGIN NAME';

			//initialize vars for plugin options
			protected $db;
			protected $options;
			protected $caps;
			protected $tables;

			//initialize options
			public function __construct() {
				//reference global $wpdb class instance
				global $wpdb;

				//store reference to $wpdb so we don't have to declare it constantly
				$this->db = $wpdb;

				$this->plugin_options();
				$this->plugin_capabilities();
				$this->plugin_tables();
			}

			//set up options array
			protected function plugin_options() {
				$this->options = array(
					$this->fix_name('version') => $this->v_num,
					$this->fix_name('options') => array(
						'test' => 'Testing'
						//add options to this array as 'option_name' => 'option_value'
						//this allows us to only store two options in the table
						//one will keep our version number and the other will keep a JSON encoded
						//string of all of our other options

					)
				);
			}

			//set up capability array
			protected function plugin_capabilities() {
				$this->caps  = array(
					'manage_options' => array(
						$this->fix_name( 'capability')
					)
					//add capabilities to this array as 'required_capability' => array('capability_to_grant')
				);
			}

			//set up table array
			protected function plugin_tables() {
				//set the table name as a key for the $this->tables array
				//and add the MySQL CREATE statement as the value for that key
				$this->tables = array(
					'main' => "CREATE TABLE `" . $this->fix_name('main', true) . "` (
							`ID` int(15) NOT NULL AUTO_INCREMENT,
							`column_name` varchar(255),
							PRIMARY KEY (`ID`)
						) ENGINE=InnoDB DEFAULT CHARSET=latin1"
				);
			}

			//create a prefixed version of a table name or option name
			protected function fix_name($short_name = null, $db = false) {
				//see if short_name was provided
				if(isset($short_name)) {
					//if short_name doesn't start with _ and prefix doesn't end with _
					if(substr($this->prefix, -1, 1) != '_' && substr($short_name, 0, 1) != '_') {
						//add an _ between prefix and short_name
						$name = $this->prefix . '_' . $short_name;
					//if short_name starts with _ and prefix ends with _
					} elseif(substr($this->prefix, -1, 1) == '_' && substr($short_name, 0, 1) == '_') {
						//remove _ from short_name and prepend prefix
						$name = $this->prefix . substr($short_name, 0, 1);
					//if only one has an _
					} else {
						//concatenate the prefix and short_name
						$name = $this->prefix . $short_name;
					}

					//check if this is a table and needs the $wpdb->prefix added
					if($db) {
						$name = $this->db->prefix . $name;
					}

					//return the newly generated name
					return $name;
				}
			}

			//WP_DEBUG logging method
			protected function log($message, $namespace = null) {
				//if debugging is enabled
				if(WP_DEBUG) {
					//if we weren't given a namespace
					if(!is_string($namespace)) {
						//use the one defined in the class initialization
						$namespace = $this->namespace;
					//if we were
					} else {
						//convert it to caps so it's easily recognizable in the debug.log
						$namespace = strtoupper($namespace);
					}

					//append a colon and a space
					$namespace .= ': ';

					//if the message is an object or an array
					if(is_array($message) || is_object($message)) {
						//print out the object or array structure
						error_log($namespace . print_r($message, true));
					//if it isn't
					} else {
						//just echo out the message
						error_log($namespace . $message);
					}
				}
			}
		}
	}