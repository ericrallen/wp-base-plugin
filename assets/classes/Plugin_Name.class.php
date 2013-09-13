<?php

	//class for our plug-in logic
	if(!class_exists('Plugin_Name')) {

		class Plugin_Name {

			public function __construct($opts = null) {
				global $wpdb;

				//store a reference to $wpdb as $this->db
				//so we don't have to keep retyping 'global $wpdb;'
				$this->db = $wpdb;

				//if options were provided
				if(is_object($opts)) {
					//store the options for reference
					$this->options = $opts;
				//if they weren't provided
				} else {
					//check if the Plugin_Name_Options class exists
					if(class_exists('Plugin_Name_Options')) {
						//store the Plugin_Name_Options object
						//for reference to capabilties, tables, and options
						$this->options = new Plugin_Name_Options();
					}
				}
			}

			//our plug-in activation
			public function activate() {
				//call methods to initialize plug-in functionality
				$this->set_options();
				$this->set_tables();
				$this->add_caps();
			}

			//our plug-in deactivation
			public function deactivate() {
				//call methods to remove options and capabilities
				//we don't remove the tables here, they are removed in uninstall.php
				$this->unset_options();
				$this->remove_caps();
			}

			//add capabilities
			private function add_caps() {
				//get roles object
				global $wp_roles;

				//iterate through all roles and add the capabilities
				foreach($wp_roles->role_names as $role => $info) {
					//get the role
					$role_obj = get_role($role);

					//iterate through capabilities in the options
					//this gives us an array of capabilities and the capability they require
					foreach($this->options->caps as $req => $caps) {
						//iterate through our capabilities
						foreach($caps as $cap) {
							//if this role has the required capability
							//but not the capability we want to add
							if(!$role_obj->has_cap($cap) && $role_obj->has_cap($req)) {
								//add capability
								$role_obj->add_cap($cap, true);
							}
						}
					}
				}
			}

			//remove capabilities
			private function remove_caps() {
				//get roles object
				global $wp_roles;

				//iterate through all roles and remove the capabilities
				foreach($wp_roles->roles as $role => $info) {
					//get the role
					$role_obj = get_role($role);

					//iterate through capabilities in the options
					//this gives us an array of capabilities and the capability they require
					foreach($this->options->caps as $req => $caps) {
						//iterate through our capabilities
						foreach($caps as $cap) {
							//if this role has our capability
							if($role_obj->has_cap($cap)) {
								//remove the capability
								$role_obj->remove_cap($cap);
							}
						}
					}
				}
			}

			//this method creates any necessary tables
			public function set_tables() {
				//loop through each table
				foreach($this->options->tables as $name => $sql) {
					//check to see if we need to create the table
					$this->check_DB($name, $sql);
				}
			}

			//this method checks to make sure tables don't exist before trying to create them
			private function check_DB($table, $sql) {
				//if we can't find the table
				if($this->db->get_var("show tables like '". $table . "'") != $table) {
					//run the table's CREATE statement
					$this->db->query($sql);
				}
			}

			//this method sets any necessary options
			private function set_options() {
				//iterate through our options
				foreach($this->options->opts as $name => $val) {
					//run the option through our update method
					$this->update_option($name, $val);
				}
			}

			//this method removes any necessary options
			private function unset_options() {
				//iterate through our options
				foreach($this->options->opts as $name => $val) {
					//remove the option
					delete_option($name);
				}
			}

			//this method allows us to run some checks when updating versions and changing options
			private function update_option($option, $value) {
				//if the option exists
				if($curr_value = get_option($option)) {
					//if the current value isn't what we want
					if($curr_value !== $value) {
						//check with the pre_update_option method which lets us perform any necessary actions when updating our options
						if($this->pre_update_option($option, $curr_value, $value)) {
							//update the option value
							update_option($option, $value);
						}
					}
				//if it doesn't add it
				} else {
					add_option($option, $value);
				}
			}

			//this method performs checks against specific option names to run update functions prior to saving the option
			private function pre_update_option($name, $old, $new) {
				//we'll make this true when the option is safe to update
				$good_to_go = false;

				//if this is our version number
				if($name === $this->options->opts[$this->options->prefix . 'version']) {

					//IMPORTANT: call necessary update functions for each version here

					$good_to_go = true;
				//otherwise
				} else {
					//if we've got some values in there, we're good
					if($old || $new) {
						$good_to_go = true;
					}
				}

				return $good_to_go;
			}

			//WP_DEBUG logging method
			public function log($message, $namespace = null) {
				//if debugging is enabled
				if(WP_DEBUG) {
					//if we weren't given a namespace
					if(!is_string($namespace)) {
						//use the one defined in the class initialization
						$namespace = $this->options->namespace;
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
