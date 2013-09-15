<?php

	//plug-in uninstall action

	//remove tables and version number option

	require_once(dirname(__FILE__) . '/assets/classes/plugin.class.php');

	$plugin = new Plugin_Name();

	$plugin->uninstall();