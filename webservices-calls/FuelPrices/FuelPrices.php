<?php
	// include authentication and other required variables
	include(WPComcar_WEBSERVICESCALLSPATH."/Carmen-Data-Web-Services-Common-Files/requiredForCarTools.php");

	// Extra config info. All the parameters are optional: if not provided, the defaults will be used
	$WPComcar_objConfig = array();
	// Whether or not the HTML returned should be wrapped in a FORM tag
	// Set this to false if your application has an external FORM tag wrapping the part of the code where the HTML of the web service will go
	// In particular, .NET developers would probably want to set this parameter to false
	// $objConfig['isFormWrapped'] = true;	// default="true"

	// Merge POST data and config variables into single object
	$WPComcar_objDataAndConfig = array();
	$WPComcar_objDataAndConfig['data'] = $_POST;	// get data from POST
	$WPComcar_objDataAndConfig['config'] = $WPComcar_objConfig;

	// Serialize object to JSON
	$WPComcar_jsnDataAndConfig = json_encode( $WPComcar_objDataAndConfig );

	try {
		// connect to the webservice
		$WPComcar_ws = new SoapClient($WPComcar_services['fuelprices'], array('cache_wsdl' => 0));
		// call the required functions and store the returned data
		$WPComcar_resultsHTML = $WPComcar_ws->GetHTML($WPComcar_pubhash, $WPComcar_clk, 'FuelPrices.php', $WPComcar_jsnDataAndConfig);
		
	} catch (Exception $e) {
		// Error handling code if soap request fails 
		$WPComcar_msg = $WPComcar_msg.'The webservice failed to load the selector<br />';
	}
	
	include_once (WPComcar_WEBSERVICESCALLSPATH.'Carmen-Data-Web-Services-Template/template.php');
?>
