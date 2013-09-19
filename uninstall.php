<?php

	//uninstallation
	if(!defined('WP_UNINSTALL_PLUGIN')) {
		exit();
	} else {
		if(!class_exists('Plugin_Name_Options')) {
			include_once('./assets/classes/Plugin_Name_Options.class.php');
		}

		if(!class_exists('Plugin_Name')) {
			include_once('./assets/classes/Plugin_Name.class.php');
		}

		$plugin_options = new Plugin_Name_Options();

		$plugin = new Plugin_Name($plugin_options);

		$plugin->uninstall();
	}