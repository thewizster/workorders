<?php
/************************************************************************************************
Adds a submitted workorder
Author: Raymond Brady
Date Created: 1/14/2017
************************************************************************************************/
require_once('./resources/library/forms_db_controller.php');
require_once('./resources/library/workorder.php');
require_once('./resources/library/workflow.php');
require_once('./resources/library/approver.php');
require_once('./resources/library/pacman.php');
require_once('./resources/library/email.php');

$element = "Workorder";
$element_function = "Added";

if (!isset($_SESSION['user_email']) || !isset($_SESSION['user_group'])) {
    $QUERY_PROCESS = "ERROR|Required information is missing.";
    return;
}

$illegalGroupChars = array(" ", "/"); // Include illegal characters for group name.
$replaceIllegalGroupChars = "-"; // Group name is used for HTML element id's so replace illegal chars with this char.

// Gather data, process POST, and update the workorder data
$currentUserEmail = $_SESSION['user_email'];
$currentUserGroup = str_replace($illegalGroupChars, $replaceIllegalGroupChars, $_SESSION['user_group']);
$hideFormClassString = "";
$fromEmailAddress = 'noreply@dumasisd.org';
$formSubmissionMessage = "";
$hideFormRenderingClassString = "";


//$QUERY_PROCESS = $workorderDataAdapter->UpdateFormData($formPostHandler->woId, $formPostHandler->asJSON());

try {
    // POST['id'] is not present. Should handle posted form data here
    $formPostHandler = new Pacman($_POST);
    // Need to load the form. Form data is stored with new workorder.
    $formsDataAdapter = new FormsDataController($dsn, $user_name, $pass_word);
    $form = $formsDataAdapter->getFormById($formPostHandler->formId);
    // Setup data needed for creating workorder and rendering page
    $hideFormRenderingClassString = "hidden";
    $formName = $formPostHandler->formName; //$_POST['form-name'];
    $formDescription = $formPostHandler->formDescription; //$_POST['form-description'];
    // Form Workflow field holds array of approver email addresses. We need to transform this to work with the data.
    $approverArray = ApproverHelper::ParseRawWorkflowData($form['Workflow']);
    $approvers = ApproverHelper::NewApproverArrayFromEmailArray($approverArray);
    // Get the groupWorkflow for the users group
    $groupWorkflows = $form['GroupWorkflows'];
    $groupWorkflows = json_decode($groupWorkflows, true);
    $userGroupApproverArray = $groupWorkflows[$currentUserGroup];
    $groupApprovers = ApproverHelper::NewApproverArrayFromEmailArray($userGroupApproverArray);
    // merge the approver arrays with group first and create the workflow for the form
    $mergedApprovers = ApproverHelper::MergeApproverArrays($groupApprovers, $approvers);
    $workflow =  new Workflow($formName . ' Workflow', $mergedApprovers);
    
    $wo = new Workorder();
    $wo->formName = $formPostHandler->formName;
    $wo->formId = $formPostHandler->formId;
    $wo->description = $formPostHandler->formDescription;
    $wo->formXml = $formPostHandler->asFormXML();
    $wo->formData = $formPostHandler->asJSON();
    $wo->currentApprover = ApproverHelper::setNextOrFirstCurrent($mergedApprovers, $currentUserEmail)->email;
    $wo->workflow = $workflow->asJSON();
    $wo->approveState = ApproveState::PendingApproval;
    $wo->approverKey = generateApproverKey();
    $wo->viewOnlyKey = generateApproverKey(); // TODO: rename key gen function. It generates basic key. Not exclusive to approvers.
    $wo->createdBy = $currentUserEmail;
    $wo->updatedBy = $currentUserEmail;
    $wo->notifyOnFinalApproval = $form['notifyOnFinalApproval'];

    $woDataAdapter = new WorkorderDataAdapter($dsn, $user_name, $pass_word, $currentUserEmail);
    $dbResponse = $woDataAdapter->Insert($wo);
    $wo->id = $woDataAdapter->lastInsertId;

    // create a vew model for the email adapter to use. Helps to generate more detailed emails.
    $woViewModel = new WorkorderViewModel($wo, $wo->approverKey);
    // send emails.
    $emailAdapter = new WorkorderEmailAdapter($fromEmailAddress);
    $emailAdapter->SendViewOnlyCreatedToCreator($wo, $woViewModel);
    $emailAdapter->SendNeedsApprovalToCurrentApprover($wo, $woViewModel);

    $formSubmissionMessage = "<div class='container'><div class='alert alert-success'><i class='fa fa-info-circle'></i><b> " . $formName . "</b> was saved. Check your email.</div></div>";

    $QUERY_PROCESS = $dbResponse;

} catch (Exception $e) {
    $QUERY_PROCESS = "ERROR|Problem while adding new workorder. " . $e;
    $formSubmissionMessage = "<div class='container'><div class='alert alert-danger'><i class='fa fa-exclamation-triangle'></i> There was a problem saving " . $formName . ".<p>" . $e->getMessage . "</p></div></div>";
}

?>