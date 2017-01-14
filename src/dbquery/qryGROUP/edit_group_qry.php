<?php
$element = "Group";
$element_function = "EDITED";
//Define Variables for the form
require_once "./resources/library/groups.php";
$groupDbAdapter = new groupDataAdapter($dsn, $user_name, $pass_word);

// TODO: Resolve undefined variable: What is $general_seed used for? Is currently undefined here
$GRP_id_active = pg_encrypt($_POST['group_id'].$general_seed,$pg_encrypt_key,"decode"); //$_POST['group_id'];

$GRP_name = $_POST['group_name'];
//echo "--------------------------------------------------------------------------------------------   "  . $transfer_GRP_id.  "   ------ ".$GRP_id_active;
$QUERY_PROCESS = $groupDbAdapter->Update($GRP_id_active,$GRP_name);


?>
