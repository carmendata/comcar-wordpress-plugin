<?php
/**
 * Plugin Name:  Comcar Tools
 * Plugin URI: http://github.com/carmendata/comcar-wordpress-plugin/wiki
 * Description: Includes the Tax Calculator, Vehicle Comparator amd Emissions Footprint Calculator from comcar.co.uk.
 * Version: 0.20.1
 * Author: Carmen data
 * Author URI: http://carmendata.co.uk/
 * License: GPL2
 */

 ini_set( 'error_reporting', E_ALL );
ini_set( 'display_errors', true );
define("WPComcar_PLUGINVERSION","0.20");
include_once(__DIR__."/wp-comcar-constants.php");

// shall I ask if it is admin to include it?
require_once(dirname(__FILE__)."/admin/wp-comcar-plugins-admin-html.php");


add_action("wp", 'plugin_redirection');    
add_action("wp_head",'activate_page_plugins');   


// decode url from base64
function decodeURLParam( $str_to_decode ) {
	// decode string (can't use hex2bin prior to php5.4)
    $cnt_code = strlen( $str_to_decode ); 
    $unhexed_taxcalculatorcode = "";   
    $i = 0; 
    while($i < $cnt_code ) {       
        $a = substr( $str_to_decode, $i, 2 );           
        $c = pack( "H*", $a ); 
        if ( $i == 0 ) {
        	$unhexed_taxcalculatorcode = $c;
       	} else {
       		$unhexed_taxcalculatorcode .= $c;
       	} 
        $i += 2; 
    } 
    return base64_decode( $unhexed_taxcalculatorcode );
}



    /********************** ACTIONS TO TAKE BEFORE RENDERING PLUGIN**************************/
        function plugin_redirection() {
            // process any actions that need to be done before page rendering
            global $pagename;
            global $post;
            $post_id = $post->ID;
        
            $WPTax_calc_arrOptions = get_option( "WP_plugin_options_tax_calculator" ); 
            $WPComparator_arrOptions = get_option( "WP_plugin_options_comparator" );
            $WPComcar_arrOptions = array_merge ( $WPTax_calc_arrOptions, $WPComparator_arrOptions );
// echo '<div style=" background-color:red;">';
// echo '<br>';
// echo $post_id;
// echo '<br>';
// print_r( $WPComparator_arrOptions);
// echo '</div>';

            switch( $post_id ) {
                case $WPComcar_arrOptions["tax_calculator_cars_subpage_calc"] : 
                    // check for calculation redirect
                    $WPComcar_tax_calc_override = $WPComcar_arrOptions["tax_calculator_cars_calc_override"];

                    if( isset($_GET['taxcalculatorcode'] ) ) {

                        // if there is encoded data put it back into the form
                        $encoded_taxcalculatorcode = htmlspecialchars( $_GET[ 'taxcalculatorcode' ] );

                        $decoded_taxcalculatorcode =   decodeURLParam( $encoded_taxcalculatorcode );

                        $arr_decoded = explode( '~', $decoded_taxcalculatorcode );

                        $_POST['id'] = $arr_decoded[ 0 ];

                        if( count( $arr_decoded > 1 ) ) {
                            $_POST['CapCon'] = $arr_decoded[ 1 ];
                            $_POST['AnnCon'] = $arr_decoded[ 2 ];
                            $_POST['frm_listID'] = $arr_decoded[ 3 ];
                            $_POST['optTotal'] = $arr_decoded[ 4 ];
                        }

                    } else if ( $WPComcar_tax_calc_override ) {
                        // if an override exists, encode data and transmit
						$_GET['car']		= isset( $_GET['car']) ? $_GET['car'] : "";
                      	$_POST['car']		= isset( $_POST['car']) ? $_POST['car'] : $_GET['car'];
                      	$_GET['id']			= isset( $_GET['id']) ? $_GET['car'] : $_POST['car'];
                      	$_POST['id']		= isset( $_POST['id']) ? $_POST['id'] : $_GET['id'];
                      	$_POST['CapCon']	= isset( $_POST['CapCon']) ? $_POST['CapCon'] : "";
                      	$_POST['AnnCon']	= isset( $_POST['AnnCon']) ? $_POST['AnnCon'] : "";
                      	$_POST['frm_listID']= isset( $_POST['frm_listID']) ? $_POST['frm_listID'] : "";
                      	$_POST['optTotal']	= isset( $_POST['optTotal']) ? $_POST['optTotal'] : "";
                      	 
                        // create formData string to encode as base64
                        $WPComcar_formData = 	$_POST['id']."~"
                        						.$_POST['CapCon']."~"
                        						.$_POST['AnnCon']."~"
                        						.$_POST['frm_listID']."~"
                        						.$_POST['optTotal'];

                        $WPComcar_hashedData = bin2hex( base64_encode( $WPComcar_formData ) );
                        header( "Location: $WPComcar_tax_calc_override?taxcalculatorcode=$WPComcar_hashedData");
                        exit(1);
                    }

                    break;

                case $WPComcar_arrOptions["comparator_cars_subpage_details"]:   
                   
                    $WPComcar_comparator_override= $WPComcar_arrOptions["comparator_cars_comp_override"];       

                    if( isset($_GET['comparatorcode'])) {
                        $_POST =  (array) json_decode(base64_decode($_GET['comparatorcode']));  
                    } else if ( $WPComcar_comparator_override ) {
                        if( !isset( $_POST ) ) {  
                            $_POST=$_GET;  
                        }
                        $WPComcar_hashedData = base64_encode(json_encode($_POST));
                        
                        header( "Location: $WPComcar_comparator_override?comparatorcode=$WPComcar_hashedData");
                        exit(1);
                    }
                
                break;
            }

        }




function preg_grep_keys($pattern, $input) {
    return array_intersect_key($input, array_flip(preg_grep($pattern, array_keys($input))));
}





    //this function is called once every post and will call the desired plugin function
            function activate_page_plugins(){           

                $loadCssAndJavascript = false;
                //if it is a page the one that is being loaded (not a POST)
                $arrGeneralSettings = get_option("WP_plugin_options_general");
// echo '<div style=" background-color:red;">';
// echo '<br><br>';
// print_r(get_option("WPComcar_plugin_options_general"));
// echo '<br>_________________';
// print_r(get_option("WP_plugin_options_general"));
// echo '</div>';
//                 exit();
                //for all the plugins in comcar but for the general
                require_once(dirname(__FILE__)."/admin/wp-comcar-plugins-global-objects.php");
               
                global $plugin_nav;        
                global $current_tool_name;
                global $post;

                $idOfTheCurrentPage = get_post( $post )->ID;

                // foreach ( array_slice( $plugin_nav , 1 ) as $thisPluginName => $plugin_info ) {
                foreach (  $plugin_nav as $thisPluginName => $plugin_info ) {
                
                    //if it is not activated
                    if (!isset($arrGeneralSettings["pluginsOptions"][$thisPluginName])){
                        continue;
                    }
   
                    //options of the current plugin
                    $arrOptions = get_option('WP_plugin_options_'.$thisPluginName);
             
                    // If the arrOption is empty jump to the next one
                    if ( !isset( $arrOptions ) ) {
                        continue;
                    }
                     
                    //page where we should load the plugin
                    // $idPageWhereShouldBeLoadedThePlugin = isset($arrOptions[$thisPluginName]) ? $arrOptions[$thisPluginName]: "";
                

                    //LOAD THE PLUGIN IF WE ARE IN THE FIRST PAGE OR IN A SUBPAGE
                    /********************* TAX CALCULATOR AND COMPARATOR *************************/
                      
                    if ( isset( $arrOptions["pages"] ) && 
                        is_array( $arrOptions["pages"] ) ) {     
                        //foreach vans and cars...                
              
                        foreach($arrOptions["pages"] as $key => $page){
                            // $idPageWhereShouldBeLoadedThePlugin = $arrOptions[$thisPluginName.'_'.$page.'_page'];

                            $arr_pages = preg_grep_keys( '#^'.$thisPluginName.'_'.$page.'_subpage_(.*)$#i', $arrOptions );
                           
                            if ( isset($arr_pages)){
                            // Include also parent page
                                array_push( $arr_pages, $arrOptions[ $thisPluginName."_".$page ."_page" ] );

                                foreach( $arr_pages as $label=>$value ) {
                                    




                                    if ( $value == $idOfTheCurrentPage ) {  
                                        $current_tool_name = $thisPluginName.'_'.$page;
                                        break 2;
                                    }
                                }
                            }
                        }
                    } else {    
                        $value = $arrOptions[ $thisPluginName."_page" ];
                        $current_tool_name = $thisPluginName;
                    }


                    if ( $value == $idOfTheCurrentPage ) {       
                        $loadCssAndJavascript = true;         
                        add_filter( 'the_content',  'getToolContent' );
                        break;
                    }

                }



                if ( $loadCssAndJavascript ) {
                    echo "<script> $=jQuery; </script>";
                    wp_enqueue_script('comcar-javascript');
                    wp_enqueue_style('comcar-style');
                }
            }



function getToolContent(  ) {
    global $current_tool_name;
        if( is_page() && is_main_query() ) { 

        //             // lets include the code

            switch ( $current_tool_name ) {

                case 'tax_calculator_cars':
                    $path_to_include = "Tax-Calculator/Car-tax-calculator.php";                  
                break;
                case 'tax_calculator_vans':
                    $path_to_include = "Tax-Calculator/Van-tax-calculator.php";
                break;
                case 'comparator_cars':
                    $path_to_include = "Comparator/Car-comparator.php";
                break;
                case 'comparator_vans':
                    $path_to_include = "Comparator/Van-comparator.php";
                break;
                case 'footprint':
                    $path_to_include = "Footprint-Calculator/Footprint-Calculator.php";
                break;
                case 'electric_comparator_cars': 
                    $path_to_include = "Electric-Comparator/Electric-Comparator.php";
                break;
                default:
                    $path_to_include = '';
                break;
            }
                         
        	include_once(WPComcar_WEBSERVICESCALLSPATH.$path_to_include);
          
                     

            $WPComcar_theResultOfTheWebservice=isset($WPComcar_theResultOfTheWebservice) ? $WPComcar_theResultOfTheWebservice : "";
   			$content = isset( $content ) ? $content : "";
            $content = $content.$WPComcar_theResultOfTheWebservice;
            return $content;
        }   
}


            
?>
