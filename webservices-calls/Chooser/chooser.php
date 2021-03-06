<?php 
    global $post;
    $thePostId = $post->ID;
    $WPComcar_arrOptions = get_option( 'WP_plugin_options_chooser' ); 
   
    switch ( $thePostId ) {
        case $WPComcar_arrOptions["chooser_car_page"]:
            include_once ( WPComcar_WEBSERVICESCALLSPATH.'Carmen-Data-Web-Services-Common-Files/requiredForCarTools.php' );        
            $loadContent = true;
        break;
        case $WPComcar_arrOptions["chooser_van_page"]:
            include_once ( WPComcar_WEBSERVICESCALLSPATH.'Carmen-Data-Web-Services-Common-Files/requiredForVanTools.php' );
            $loadContent = true;
        break;
        
        default:
            $loadContent = false;
        break;
    }

    if (  $loadContent  ) {
       
        try {
            // connect to the webservice
            $WPComcar_ws = new SoapClient( $WPComcar_services['chooser'], array( 'cache_wsdl' => 0 ) );
            $stage = "select";

            if (array_key_exists( 'maxprice', $_POST )) {
                $stage = "model";
            }    
       
            if (array_key_exists( 'ID', $_GET )) {
                 $stage = "options";            
            }   
  
            if (array_key_exists( 'AnnCon', $_POST )) {
                $stage = "calculation";



            // Get fuel benefit check URL if it is active to redirect the links inside
            // tax calculation

                $fuelbenefit_arrOptions = get_option( 'WP_plugin_options_fuel_benefit_check' ); 
                $general_arrOptions = get_option( 'WP_plugin_options_general' );
    
    
                if ( array_key_exists('fuel_benefit_check', $general_arrOptions['pluginsOptions'] ) ) {
                    $_GET['fuel_benefit_url']= get_permalink( $fuelbenefit_arrOptions['fuel_benefit_check_page'] );    
                }
            }   


            $_POST['stage'] = $stage;
            $_POST['type_vehicle'] = $type_vehicle;
             // Convert structure into JSON
            $WPComcar_jsonPost = json_encode(  $_POST );
            $WPComcar_jsonGet = json_encode(  $_GET );

            $WPComcar_resultsHTML = fixForSsl( $WPComcar_ws -> GetHTML( $WPComcar_pubhash, $WPComcar_clk, $stage, $WPComcar_jsonPost, $WPComcar_jsonGet  ));
            $WPComcar_resultsJS = fixForSsl( $WPComcar_ws -> GetJS( $WPComcar_pubhash, $WPComcar_clk,   $stage,  $WPComcar_jsonGet ) );
            $WPComcar_resultsCSS = fixForSsl( $WPComcar_ws -> GetCSS( $WPComcar_pubhash, $WPComcar_clk,   $stage ) );
                
        } catch ( Exception $WPComcar_e ) {
            // Error handling code if soap request fails 
            $WPComcar_msg = $WPComcar_msg.'The webservice failed to load the selector<br />';
        }
    
        include_once ( WPComcar_WEBSERVICESCALLSPATH . 'Carmen-Data-Web-Services-Template/template.php');
    } 

?>
