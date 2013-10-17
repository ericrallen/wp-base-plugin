<?php

	//uninstallation
	if(!defined('WP_UNINSTALL_PLUGIN')) {
		exit();
	} else {
		if(!class_exists('Plugin_Name_Options')) {
			include_once(dirname(__FILE__) . '/assets/classes/Plugin_Name_Options.class.php');
		}

		if(!class_exists('Plugin_Name')) {
			include_once(dirname(__FILE__) . '/assets/classes/Plugin_Name.class.php');
		}

		$plugin_name = new Plugin_Name();

		$plugin_name->uninstall();
	}