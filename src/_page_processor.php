<?php

require_once("config/db.php");
require_once("classes/Login.php");
require_once('config/appconfig.php');

require_once "./resources/library/appinfo.php";
$appInfoDbAdapter = new AppInfo($dsn, $user_name, $pass_word);

//appInfo->Set('RegDomain', $_POST['value here']);
$system_version =$appInfoDbAdapter->Get('System Version');



// checking for minimum PHP version
if (version_compare(PHP_VERSION, '5.3.7', '<')) {
    exit("Sorry, Simple PHP Login does not run on a PHP version smaller than 5.3.7 !");
} else if (version_compare(PHP_VERSION, '5.5.0', '<')) {
    // if you are using PHP 5.3 or PHP 5.4 you have to include the password_api_compatibility_library.php
    // (this library adds the PHP 5.5 password hashing functions to older versions of PHP)
    require_once("libraries/password_compatibility_library.php");
}

// ... ask if we are logged in here:
if (isset($login)) {
    // the user is logged in. you can do whatever you want here.
    //@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
	// GLOBAL DEFINITIONS: These variables are used site wide.  Befoere creating a variable or running a query please make sure
	// it has not been first defined here.  No need for extra queries and variables to make things more confusing
	//@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
	
	// include the configs / constants for the database connection
	
	//this is the key used by the pg_encrypt function used to identify pages
	
	
	
	//User Profile items pulled from query
	//if you change this make sure to change it in the update_query.php under qryPROFILE


	
	//@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
	// GLOBAL DEFINITIONS: End of Globals
	//@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
		
	?>
	
	<!DOCTYPE html>
	<html lang="en">
	
	<head>
	
		<?php
	//All static / Required CSS and JS goes in this file
	include "page_content/header.php";	
	/*
	@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
	 POST PROCESSOR: This section is used for post data as information is gathered from $_POST[post_type]
	@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
	*/	
			//every form in the site should have a hidden field with id and name of post_type
			//this defines what post we are actually doing
			// for instance <input type='hidden' id='post_type' name='post_type' value='update_prof'>
			// will will call dbquery/profile/index.php and will pull the qry_update_prof.php
		//**** if a form posts wih the name='post_type' and a vlaue of  pg_encrypt(POSTFOLDER-POSTPAGE,$pg_encrypt_key,"encode")
		//      the system will load that page process the sql or class needed to run the post.  This keeps all queries in one place.
			
			if(isset($_POST['post_type'])){
				//all post operators are found in the dbquery/index.php file
				$post_page = $_POST['post_type'];
				$post_page = pg_encrypt($post_page,$pg_encrypt_key,"decode");
				$post_page = str_replace('-','/',$post_page);
				include_once "dbquery/".$post_page.".php";

			}
			
	/*
	@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
	 END POST PROCESSOR
	@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@  
	
	
	@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
	 PAGE Processor
	@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@	
	*/
	
	
			$folder = null; // set default value or error in navbar. XDebug
			if(isset($include_address) == false || $include_address == ""){
				$include_address = "page_content/index.php";
			}
			if(isset($_GET['I'])){
				//set the page we are trying to access
				$page = $_GET['I'];
	
				$header_GET_array = explode("|",pg_encrypt($page,$pg_encrypt_key,"decode"));
				$page = $header_GET_array[0];
				unset($header_GET_array[0]); //remove index 0
				//header_GET_array can be referenced in the include below for specific elements such as ID and other passthroughs
				$header_GET_array = array_values($header_GET_array); //reset array index
				
				//pages are retrieved from the GET function ie dashboard.php?I=community-calendar
				//replace the - with / so we have a usable data structure for the actual directory and file
				$FOLDER_EXP = explode("-",$page);
				$folder = $FOLDER_EXP[0];
				$page = $FOLDER_EXP[1];
				//$page = str_replace('-','/',$page);
					$include_address = "page_content/".$folder."/".$page.".php";
					
			}  
	
	
	/*
	@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
	 End Page Processor
	@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@  
	*/
	?>	
	<!--
	@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
	 pAGE hEADER
	@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@	
	-->
	<?php
				
				
				if(isset($_GET['I'])){
					$file_header = 'page_content/'.$folder.'/header_'.$page.'.php';
					if( file_exists( $file_header )){
						include $file_header;
	
						//rename( $f, $f.".willnotwork" ); //It gives a warning 
					}

				}							
	?>
	<!--
	@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
	 END PAGE HEADER
	@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
	-->
	
	</head>
	
	<body >
    
        <div id="wrapper">

       <?php

	   
	   include "includes/navbar.php";
	   ?>

        <div id="page-wrapper">

            <div class="container-fluid">
            <?php
			if(getenv("WO_ENV_ENABLED") == 1 || $BASE_URL == "http://localhost/workorder"){
                ?>
                    <div class="col-lg-12">
                        <div class="alert alert-danger alert-dismissable">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                            <i class="fa fa-info-circle"></i>  <strong>ALERT </strong> You are on the local server
                        </div>
                    </div>
			<?php
            }
            ?>
                    <?php
			include "dbquery/QUERY_PROCESS_SUB.php";
//@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
// PAGE PROCESSOR: All pages are pulled from $_GET[pg]
//@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@		

			if((@include $include_address) === false)
				{
					// handle error
					include "page_content/404.php";
				}
		
//@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
// END PAGE PROCESSOR
//@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@		
   ?>
<?php
/*
              
*/
?>
                        </div>
                        <!-- /.container-fluid -->
            
                    </div>
                    <!-- /#page-wrapper -->
            
                </div>
                <!-- /#wrapper -->
            <div style="width:60%; margin:auto; text-align:center; color:#DBDBDB; padding-top:10px;">
            <img style="width:25%" src="./IMG/workorder.png">
            <br><br>

            Product built by Webextant and KeoFleX
            </div>
                <!-- jQuery 
                <script src="js/jquery.js"></script>
            -->
                <!-- Bootstrap Core JavaScript -->
           

                <!-- underscore -->
                <script src="js/underscore-min.js"></script>
                
                <!-- Bootstrap Core JavaScript -->
                <script src="js/bootstrap.min.js"></script>
            
            
            
            </body>
            
            </html>
    <?php
	

} else {
    // the user is not logged in. you can do whatever you want here.
    //include("views/not_logged_in.php");
			Header("Location: ./");    // redirect him to protected.php

}

