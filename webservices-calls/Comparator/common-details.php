<?php
	// Vehicle type ('Car' or 'Van'). The variable is defined in either 'Car-details.php' or 'Van-details.php'
	if(!isset($WPComcar_vehicleType)){    $WPComcar_vehicleType="Car";    }

	// get data from POST and/or from URL.id
	if(!isset($_GET['id'])){	$_GET['id'] = '';	}
	if(!isset($_POST['id'])){	$_POST['id'] = $_GET['id'];	}
	$_POST['thisPage'] = $WPComcar_vehicleType.'-details.php';
	//CHANGE THE FORM SUBMISSION TO THE NEXT PAGE in Wordpress
	$WPComcar_arrOptions=get_option("WPComcar_plugin_options_comparator");
	$WPComcar_vehicleTypeForIncluding=strtolower($WPComcar_vehicleType.'s');		
	//callback page
	$WPComcar_actionName= $WPComcar_arrOptions[$WPComcar_vehicleTypeForIncluding."_subpages"]["details"];
	$WPComcar_actionName= WPComcar_getPageUrlById($WPComcar_actionName);
	
	$_POST['thisPage'] = $WPComcar_actionName;

	$_POST['pageTaxcalc'] = '../Tax-Calculator/'.$WPComcar_vehicleType.'-calc.php';

	//get the values from the options for the webservice request
	$WPComcar_webServiceRequest=$WPComcar_arrOptions["comparator_general_texts"];
	foreach($WPComcar_webServiceRequest as $key=>$value){
		$_POST[$key]=$value;
	}
	$WPComcar_jsnData = json_encode($_POST);
	
	try {

	
		// connect to the webservice
		$WPComcar_ws = new SoapClient($WPComcar_services['comparator'], array('cache_wsdl' => 0));
		// call the required functions and store the returned data

		$WPComcar_actionName= $WPComcar_arrOptions[$WPComcar_vehicleTypeForIncluding."_subpages"]["callback"];
		$WPComcar_actionName= WPComcar_getPageUrlById($WPComcar_actionName);

		// $WPComcar_actionName= plugin_dir_url(__DIR__).WPComcar_COMCARCALLBACK;
		// $WPComcar_actionName=str_replace("Car-",$WPComcar_vehicleType."-",$WPComcar_actionName);
		//exit(var_dump($WPComcar_actionName)); 

		$WPComcar_resultsJS   = $WPComcar_ws -> GetJS   ( $WPComcar_pubhash, $WPComcar_clk, 'details', $WPComcar_actionName);
		$WPComcar_resultsCSS  = $WPComcar_ws -> GetCSS  ( $WPComcar_pubhash, $WPComcar_clk, 'details');

		//select page
		$WPComcar_actionName= $WPComcar_arrOptions[$WPComcar_vehicleTypeForIncluding."_subpages"]["select"];
		$WPComcar_actionName= WPComcar_getPageUrlById($WPComcar_actionName);

		$WPComcar_resultsHTML = $WPComcar_ws -> GetHTML ( $WPComcar_pubhash, $WPComcar_clk, 'details', $WPComcar_actionName, $WPComcar_jsnData);
	} catch (Exception $WPComcar_e) {
		// Error handling code if soap request fails 
		$WPComcar_msg = $WPComcar_msg.'The webservice failed to load the Details page<br />';
	}
	
	$WPComcar_pageTitle = 'Vehicle comparator';
	include_once (WPComcar_WEBSERVICESCALLSPATH.'Carmen-Data-Web-Services-Template/template.php');
?>
