<?php
 $plugin_options["car_details"] = array(  
            array( 
                "label" => "Car Details page",
                "name" => "car_details_page",
                "options" => "Pages",
                "desc" => "Select which page Car details tool should be loaded into",
                "type" => "select"
                ) ,
            array( 
                "label" => "Fuel benefit override URL",
                "name" => "car_details_override",
                "desc" => "Override URL to visit prior to the calculation - leave blank if not needed",
                "type" => "text"
            )
        );

?>