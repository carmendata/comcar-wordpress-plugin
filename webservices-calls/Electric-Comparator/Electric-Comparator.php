<?php

    //create and instance of the controller and include the result in the page
    global $objWPElectricComparatorController;
    if (class_exists("WPElectricComparatorController") && !$objWPElectricComparatorController) {
        $objWPElectricComparatorController = new WPElectricComparatorController();    
        $objWPElectricComparatorController->controller();
    }

    //include the page
    include_once($objWPElectricComparatorController->thePageToInclude);

    class WPElectricComparatorController{

        public $thePageToInclude;

        function __construct(){
        }

        function controller(){
            global $post;
            $thePostId=$post->ID;

            //decide what page to load
            //parent or subpage?
            $arrOptions = get_option('WPComcar_plugin_options_electric_comparator');

            //check if the parent title is the one expected
            $theIdPageWhereShouldLoadThePlugin=$arrOptions["electric_comparator_cars_page"];

            if (strcmp($theIdPageWhereShouldLoadThePlugin,$thePostId)==0){
                $this->thePageToInclude=WPComcar_WEBSERVICESCALLSPATH."Electric-Comparator/Car-details.php";
            } else if (in_array($thePostId,$arrOptions["electric_comparator_cars_subpage"])) {
                if (strcmp($theIdPageWhereShouldLoadThePlugin, $this->getParentId())==0){
                    $theNameOfThePage=array_search($thePostId,$arrOptions["electric_comparator_cars_subpage"]);

                    $this->thePageToInclude=WPComcar_WEBSERVICESCALLSPATH."Electric-Comparator/Car-$theNameOfThePage.php";
                }
            }

        }

        function getParentId(){
            global $post;
            $thePageParents = get_post_ancestors( $post->ID );
            /* Get the top Level page->ID count base 1, array base 0 so -1 */ 
            $parentId = ($thePageParents) ? $thePageParents[count($thePageParents)-1]: $post->ID;

            return $parentId;
        }       
    }



?>
