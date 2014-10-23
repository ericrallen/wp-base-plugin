wp-base-plugin
==============

A simple plugin base for WordPress plugins that provides some boilerplate functionality.

Read the comments in the code for explanations and steps for turning the boilerplate into your own plugin.

Getting Started
============

1. Rename the `wp-plugin-base` directory and `wp-plugin-base.php` file with the name of your plugin replacing `wp-plugin-base`.
2. Rename the `Plugin_Name.class.php` and `Plugin_Name_Options.class.php` files in the `assets/classes/` directory with the name of your plugin replacing `Plugin_Name`.
3. Edit the plugin comment meta information found at the top of the file previously named `wp-plugin-base.php`.
4. Anywhere that you see `Plugin_Name` or any derivation of that, you should change it to the actual name of your plug-in and follow the capitalization scheme that was used. I generally do a Find/Replace on all files in this plugin's directory. (Examples:  PLUGIN_NAME, plugin_name, Plugin_Name, PLUGIN NAME)
5. Define any global paths that you might need in the `wp-plugin-base.php` file. This makes it easier to refer to paths and URLs that are relative to your plug-in. It already defines `PLUGIN_NAME_DIR` and `PLUGIN_NAME_URL` for easily including other files you add to the plugin's directory.
6. Open the file previously named `assets/classes/Plugin_Name_Options.class.php` and add any tables, options, or capabilities that you need. This class has some methods for handling prefixing names and logging errors to the `debug.log` file, as well.
7. Open the file previously named `assets/classes/Plugin_Name.class.php` and begin adding any functionality you need. This class has some default methods for setitng up tables, options, and capabilities that you may find useful. It also includes a method to facilitate performing any database maintenance necessary when updating your plugin's version.
8. Add any classes that you need to the `assets/classes/` directory, and for better integration with our basic methods and options, make them extend the class previously called `Plugin_Name_Options`.
9. Add any CSS that you need to the `assets/css/` directory. The directory structure in place favors using SCSS files and SASS as a preprocessor, but you can set this directory up however you prefer. I keep any admin area styles in a separate css file with `-admin` appended to the name so that I can enqueue them separately.
10. Add any JavaScript that you need to the `assets/js/` directory. There are two very basic js files with empty modules already in this directory, you can work from them or begin creating your own files as needed. Just like with the CSS files, I keep admin area JavaScript in a separate file iwth `-admin` appended to the name.
11. Define any actions that you need in the main plugin file (it was previously named `wp-base-plugin.php`), or you can create your own initialization method that configures your actions.
12. When you update your plugin's version, be sure to change the `$v_num` attribute of the Options class (previously called `Plugin_Name_Options`) to reflect the new version. This value is stored as an option in the WordPress database so that you can easily perform any maintenance necessary when a user upgrades the plugin.
13. When you are ready to deploy your plugin to the WordPress Plugin Repository:  edit the `deploy.sh` file to reflect the SVN information for your WordPress plug-in and user account, then navigate to the folder for your plugin in Terminal, then enter `./deploy.sh`, and let the script work its magic. You will find some deployment notes at the end of this README.

Functionality
=============

**Plugin Prefix**

Define a prefix in the Plugin_Name_Options class for your option names, tables names, capabilities, page slugs, etc. so you only have to write it once:

````php
//prefix for option names, table names, and capability names
public $prefix = 'plugin_name_';
````

You can use the `$this->fix_name($slug)` method of the Plugin_Name class to append this prefix to any string.

If you want to refer to the prefix in the Plugin_Name class, you can reference it as `$this->options->prefix`.

**Plugin Debug Namespace**

Define a namespace in the Plugin_Name_Options class to be prepended to any messages you have your plugin send to the debug.log so you can easily find your debug statements in the log.

````php
//namespace for any Debug messages
public $namespace = 'PLUGIN NAME';
````

If you use the `$this->log($msg, $namespace)` method in the Plugin_Name class, you can send it a custom namespace or the namespace defined in the Plugin_Name_Options class will be used.

If you want to refer to the namespace in the Plugin_Name class, you can reference it as `$this->options->namespace`.

**Capabilities**

Define capabilities in the Plugin_Name_Options class by adding them to the associative array in the `set_capabilities()` method.

The array key for a set of capabilities should be the required capability a role needs for your capability to be added.

In the example below we are adding a capability that requires the `manage_options` capability. **NOTE**: We are using `$this->prefix` to prepend our plugin prefix to the capability name

````php
//set up capability array
private function set_capabilities() {
	//add capabilities to this array as 'required_capability' => array('capability_to_grant')
	$this->caps = array(
		'manage_options' => array(
			$this->prefix . 'capability'
		)
	);
}
````

I generally refer to these capabilities when adding admin pages or checking user access by refering to their array key:  `$this->options->caps['manage_options'][0]`.

If you have a lot of various capabilities, you may want to set up an array mapping of capability slugs and reference them that way.

In the future I may shift to using a slug for each capabilities key and mapping them to a simpler point of reference.

**Options**

Define options in the Plugin_Name_Options class by adding them to the associative array `$this->options`.

The options array will be JSON encoded and decoded when being stored and retrieved from the DB.

````php
//set up options array
private function set_options() {
	$this->options = array(
		$this->prefix . 'version' => $this->v_num,
		$this->prefix . 'options' => array(

			//add options to this array as 'option_name' => 'option_value'
			//this allows us to only store two options in the table
			//one will keep our version number and the other will keep a JSON encoded
			//string of all of our other options

		)
	);
}
````

To access any of the options in the Plugin_Name class, you can reference them like this:  `$this->options['slug']`.

To access the current options values in the Plugin_Name class, you can reference the settings array:  `$this->settings['slug']`.

**Tables**

Define tables in the Plugin_Name_Options class by adding them to the associative array `$this->tables`.

````php
//set up table array
private function set_tables() {
	//set the table name slug as a key for the $this->tables array
	//and add the MySQL CREATE statement as the value for that key
	$this->tables['main'] = "CREATE TABLE `" . $this->prefix . 'main' . "` (
			`ID` int(15) NOT NULL AUTO_INCREMENT,
			`column_name` varchar(255),
			PRIMARY KEY (`ID`)
		) ENGINE=InnoDB DEFAULT CHARSET=latin1"
	;
}
````

To access the table names in the Plugin_Name class, you can reference them like this:  `$this->tables['slug']`.

**Updating**

If you need to perform maintenance when updating your plugin, tie into the `pre_update_option()` method in the Plugin_Name class.

There is a comment that starts `//IMPORTANT` to show you where to inject your update code.

**WordPress Plug-in Repository**

Make sure to fill out the `readme.txt` before submitting to the WordPress repository and include some screenshots if you can.

Here is a link to WordPress' example `readme.txt`:  http://wordpress.org/plugins/about/readme.txt

**Deploying**

So, you want to host your plug-in on GitHub but also have it available in the WordPress Plug-in Repository? No problem.

I've included a version of [@BFTrick](https://github.com/BFTrick)'s' `deploy.sh` script found here:  https://gist.github.com/BFTrick/3767319

All you need to do is edit the script to reflect the SVN information for your WordPress plug-in and then navigate to the folder for your plugin and type `./deploy.sh` and let the script work its magic.

A few notes about deploying:

* The script is pretty literal in it's reading of the plugin header comment, so don't muck with it's formatting or placement.
* The script will create tags for you based on the version number in your plugin header comment and the release version in your `readme.txt` so make sure they are the same.
* The script will push the master branch and latest tag to GitHub so you don't have to worry about pushing before you deploy.
* WordPress (or SVN, not sure which) doesn't seem to like letters in its version numbers, so I stick with semantic versioning format:  `0.0.1`, `0.0.2`, etc.
* You should add `deploy.sh` to your `.gitignore` ([I recommend globally](http://stackoverflow.com/questions/7335420/global-git-ignore#answer-7335487)) so you don't give anyone access to deploy your plugin.