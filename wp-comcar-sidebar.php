<?php

	// find plugins directory and include tax calculator HTML
	$plugin_dir = dirname( plugin_dir_path( __FILE__ ) )
					. '/'
					. $instance[ 'plugin_target' ]
					. '/';

	include $plugin_dir . 'webservices-calls/Tax-Calculator/Car-select.php';

	echo urldecode($WPComcar_resultsHTML);
?>
