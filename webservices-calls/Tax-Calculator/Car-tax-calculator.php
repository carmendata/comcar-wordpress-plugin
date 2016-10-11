<?php 
	//create and instance of the controller and include the result in the page
	global $objWPComcarCarTaxCalculatorController;
	if (class_exists("WPComcarCarTaxCalculatorController") && !$objWPComcarCarTaxCalculatorController) {
	    $objWPComcarCarTaxCalculatorController = new WPComcarCarTaxCalculatorController();	
	    $objWPComcarCarTaxCalculatorController->controller();
	}

	//include the page
	include_once($objWPComcarCarTaxCalculatorController->thePageToInclude);

	class WPComcarCarTaxCalculatorController{

		public $thePageToInclude;

		function __construct(){

		}

		function controller(){

			global $post;
			$thePostId=$post->ID;

			//decide what page to load
			//parent or subpage?

			$arrOptions = get_option('WP_plugin_options_tax_calculator');


			
			$idThePageWhereShouldLoadThePlugin=$arrOptions["tax_calculator_cars_page"];
			if (strcmp($idThePageWhereShouldLoadThePlugin,$thePostId)==0){
				$this->thePageToInclude=WPComcar_WEBSERVICESCALLSPATH."Tax-Calculator/Car-select.php";
			}else if (in_array($thePostId,$arrOptions["cars_subpages"])){
				//check if the parent is the one expected
				if (strcmp($idThePageWhereShouldLoadThePlugin, $this->getParentId())==0){
					$theNameOfThePage=array_search($thePostId,$arrOptions["cars_subpages"]);
					$this->thePageToInclude=WPComcar_WEBSERVICESCALLSPATH."Tax-Calculator/Car-$theNameOfThePage.php";
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
