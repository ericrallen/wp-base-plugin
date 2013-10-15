<?php

	//class for our plug-in logic
	if(!class_exists('Plugin_Name') && class_exists('Plugin_Name_Options')) {

		class Plugin_Name extends Plugin_Name_Options {

			private $table_map;

			public function __construct() {
				//run Plugin_Name_Options constructor
				parent::__construct();

				//get options from DB and store them
				$this->get_settings();

				//map table names to slugs so we can refer to them more easily
				$this->map_tables();
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
				//call methods to remove capabilities
				//we don't remove the tables or options here, they are removed in uninstall.php
				$this->remove_caps();
			}

			//our plug-in uninstall
			public function uninstall() {
				//call methods to remove tables and unset options
				//other plugin data should have been removed on deactivation
				$this->unset_options();
				$this->unset_tables();
			}

			//get current options
			public function get_settings() {
				//get options, use defaults from plugin-options.php if they aren't found
				$opts = get_option($this->fix_name('options'), $this->options->opts[$this->fix_name('options')]);

				//decode the JSON string into an array and save it to $this->current
				if(is_string($opts)) {
					$this->settings = json_decode($opts, true);
				} else {
					$this->settings = $opts;
				}
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
					foreach($this->caps as $req => $caps) {
						//iterate through our capabilities
						foreach($caps as $key => $cap) {
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
					foreach($this->caps as $req => $caps) {
						//iterate through our capabilities
						foreach($caps as $key => $cap) {
							//if this role has our capability
							if($role_obj->has_cap($cap)) {
								//remove the capability
								$role_obj->remove_cap($cap);
							}
						}
					}
				}
			}

			private function map_tables() {
				//loop through tables and store them as an array of slug => table_name for easy reference in other methods
				foreach($this->tables as $slug => $sql) {
					//now we can refer to our tables as $this->table_map['slug'];
					$this->table_map[$slug] = $this->fix_name($slug, true);
				}
			}

			//this method creates any necessary tables
			private function set_tables() {
				//loop through each table
				foreach($this->tables as $slug => $sql) {
					//check to see if we need to create the table
					$this->check_DB($this->fix_name($slug, true), $sql);
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

			//this method removes tables from the DB
			private function unset_tables() {
				foreach($this->tables as $slug => $sql) {
					$this->db->query("DROP table `" . $this->fix_name($slug, true) . "`");
				}
			}

			//this method sets any necessary options
			private function set_options() {
				//iterate through our options
				foreach($this->options as $name => $val) {
					//if this is our options array
					if($name == $this->fix_name('options')) {
						//iterate through each value
						foreach($val as $key => $value) {
							//check it against the current settings
							if($this->settings[$key] != $value) {
								//if the setting was different, store the current setting, not our default
								$val[$key] = $this->settings[$key];
							}
						}

						//json encode our options array into a string
						$val = json_encode($val);
					}

					//run the option through our update method
					$this->update_option($name, $val);
				}
			}

			//this method removes any necessary options
			public function unset_options() {
				//iterate through our options
				foreach($this->options as $name => $val) {
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
				if($name === $this->options[$this->fix_name('version')]) {

					//IMPORTANT: call necessary update functions for each version here

					$good_to_go = true;

				//add other elseif branches based on other option updates that might require custom update functionality here

				//otherwise
				} else {
					//if we've got some values in there, we're good
					if($old || $new) {
						$good_to_go = true;
					}
				}

				return $good_to_go;
			}

		}

	}