<?php
	$folder = null; // set default value or error in navbar. XDebug
    require_once('config/appconfig.php');
    require_once("resources/library/appinfo.php");
    $appInfoDbAdapter = new AppInfo($dsn, $user_name, $pass_word);
    $system_version =$appInfoDbAdapter->Get('System Version');
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>DISD - Workorders</title>

    <?php require_once('./includes/headlinks.php'); ?>
    <?php require_once('./includes/headlinksfb.php'); ?>

</head>

<body>

    <div id="wrapper">
        <!-- Navigation -->
        <?php require_once('./includes/navbar.php') ?>
        <!-- Form creation code -->
        <?php
            require_once('./config/db.php');
            require_once('./resources/library/forms_db_controller.php');
            require_once('./resources/library/workorder.php');
            require_once('./resources/library/workflow.php');
            require_once('./resources/library/approver.php');
            require_once('./resources/library/pacman.php');
            require_once('./resources/library/email.php');
        ?>
        <!-- Render Form from post data -->
        <?php
        $illegalGroupChars = array(" ", "/"); // Include illegal characters for group name.
        $replaceIllegalGroupChars = "-"; // Group name is used for HTML element id's so replace illegal chars with this char.

        $currentUserEmail = $_SESSION['user_email'];
        $currentUserGroup = str_replace($illegalGroupChars, $replaceIllegalGroupChars, $_SESSION['user_group']);
        $hideFormClassString = "";
        $fromEmailAddress = 'noreply@dumasisd.org';
        $formSubmissionMessage = "";
        $hideFormRenderingClassString = "";
        // Handle post
        if (strtoupper($_SERVER['REQUEST_METHOD']) === 'POST')
        {
            // When id is sent then render the form
            if(isset($_POST['id']))
            {
                $formId = $_POST['id'];
                $formsDataAdapter = new FormsDataController($dsn, $user_name, $pass_word);
                $formToRender = $formsDataAdapter->getFormById($formId);
                $formName = $formToRender['FormName'];
                $formDescription = $formToRender['Description'];
                $formXml = base64_decode($formToRender['FormXml']);
            } else {
                $formSubmissionMessage = "<div class='container'><div class='alert alert-danger'><i class='fa fa-exclamation-triangle'></i> Bad request. </div></div>";                
            }
            
        } else {
            // TODO: redirect to unauth page. Page only handles POST
            $formSubmissionMessage = "<div class='container'><div class='alert alert-danger'><i class='fa fa-exclamation-triangle'></i> Bad request. </div></div>";
        }
        
        ?>
        <div id="page-wrapper">
            <div class="container-fluid">
                <!-- Page Heading -->
                <div class="row">
                    <div class="col-lg-12">
                        <h1 class="page-header">
                           <?php echo $formName; ?>
                        </h1>
                        <ol class="breadcrumb">
                            <li class="active">
                                <i class="fa fa-file"></i> <?php echo $formDescription; ?>
                            </li>
                        </ol>
                        <?php echo $formSubmissionMessage; ?>
                    </div>
                </div>
                <!-- /.row -->

                <div class="row" <?php echo $hideFormRenderingClassString; ?>>
                    <div class="col-lg-3"></div>
                    <div class="col-lg-6">
                        <textarea id="form-builder-template" hidden><?php echo $formXml; ?></textarea>
                        <div id="rendered-form">
                            <form id="workorderform" class="form-horizontal" method="post" action="./?I=<?php echo  pg_encrypt("WORKORDER-create",$pg_encrypt_key,"encode"); ?>"></form>
                            <button id="formSubmitButton" class="btn btn-success pull-right">Send</button>
                        </div>
                    </div>
                    <div class="col-lg-3"></div>
                </div>
            </div>
            <!-- /.container-fluid -->
        </div>
        <!-- /#page-wrapper -->
    </div>
    <!-- /#wrapper -->

    <?php require_once('./includes/jsbs.php'); ?>
    <?php require_once('./includes/jsfb.php'); ?>
    <?php require_once('./includes/jsjqvalidation.php'); ?>

    <script>
        var template = document.getElementById('form-builder-template');
        var formContainer = document.getElementById('rendered-form');
        $(template).formRender({
            container: jQuery('form', formContainer)
        });

        jQuery('#formSubmitButton').click(function(){
            jQuery('#workorderform').submit();
        });

        jQuery("#workorderform").validate({
            submitHandler: function(form){
                // Set the xml data string before sending to server.
                 $xml = jQuery('#form-builder-template').val();
                 $('<input />').attr('type', 'hidden')
                    .attr('name', "form-xml-schema")
                    .attr('value', $xml)
                    .appendTo(form);
                 $('<input />').attr('type', 'hidden')
                    .attr('name', "form-name")
                    .attr('value', <?php echo "'" . $formName . "'"; ?>)
                    .appendTo(form);
                 $('<input />').attr('type', 'hidden')
                    .attr('name', "form-id")
                    .attr('value', <?php echo "'" . $formId . "'"; ?>)
                    .appendTo(form);
                 $('<input />').attr('type', 'hidden')
                    .attr('name', "form-description")
                    .attr('value', <?php echo "'" . $formDescription . "'"; ?>)
                    .appendTo(form);
                 $('<input />').attr('type', 'hidden')
                    .attr('name', "post_type")
                    .attr('value', <?php echo "'" . pg_encrypt("qryWORKORDER-add_workorder_qry",$pg_encrypt_key,"encode") . "'"; ?>)
                    .appendTo(form);

                form.submit();
            },
            ignore: [],
        });

    </script>
</body>

</html>
