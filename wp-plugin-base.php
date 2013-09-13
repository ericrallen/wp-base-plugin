<?php

	/*
	Plugin Name: Plug-In Name
	Plugin URI: http://www.example.com/plug-in-name/
	Description: Simple plug-in base
	Version: 0.0.1
	Author: Author Name
	Author URI: http://www.example.com/
	License: GPL2
	*/

	/* =======================================================
		NOTE:  Anywhere that you see 'Plugin_Name' or
		any derivation of that, you should change it to
		the actual name of your plug-in and follow the
		capitalization scheme that was used.
	======================================================= */

	// GLOBAL PATHS

	/* =======================================================
		Define any global paths that you might 
		want to use later on. This makes it easier to refer
		to paths and URLs that are relative to your plug-in.
		Feel free to add more.
	======================================================= */

	//this is the plug-in directory name
	if(!defined("PLUGIN_NAME")) {
		define("PLUGIN_NAME", trim(dirname(plugin_basename(__FILE__)), '/'));
	}

	//this is the path to the plug-in's directory
	if(!defined("PLUGIN_NAME_DIR")) {
		define("PLUGIN_NAME_DIR", WP_PLUGIN_DIR . '/' . PLUGIN_NAME);
	}

	//this is the url to the plug-in's directory
	if(!defined("PLUGIN_NAME_URL")) {
		define("PLUGIN_NAME_URL", WP_PLUGIN_URL . '/' . DONATOMETER);
	}

	/* =======================================================
		Open the plugin-options.php file in 
		this directory and add any tables, options, or
		capabilities that you need added.
	======================================================= */

	// INCLUDES
	require_once(PLUGIN_NAME_DIR . '/plugin-options.php');

	/* =======================================================
		Open /assets/classes/plugin_name.class.php
		and begin adding any functionality you need. This 
		class has some default methods you may find useful.
	======================================================= */

	require_once(PLUGIN_NAME_DIR . '/assets/classes/plugin.class.php');

	if(class_exists('Plugin_Name_Options') && class_exists('Plugin_Name')) {
		$plugin_options = new Plugin_Name_Options();

		$plugin = new Plugin_Name($plugin_options);

		register_activation_hook(__FILE__, array($plugin, 'activate'));

		register_deactivation_hook(__FILE__, array($plugin, 'deactivate'));

		/* =======================================================
			I like to keep my actions bound here instead of
			inside a method in my classes because it makes
			it easier to keep actions that rely on multiple
			classes in one spot
		======================================================= */
	}
