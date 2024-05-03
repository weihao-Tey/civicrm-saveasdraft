<?php

require_once 'saveasdraft.civix.php';
$SaveAsDraftClicked = FALSE;
$SaveClicked = FALSE;

use CRM_Saveasdraft_ExtensionUtil as E;

/**
 * Implements hook_civicrm_config().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_config/
 */
function saveasdraft_civicrm_config(&$config): void {
  _saveasdraft_civix_civicrm_config($config);
}

/**
 * Lifecycle hook :: install().
 * Implements hook_civicrm_install().
 * 
 * Draft Activity Status will be added if it exists.
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_install
 */
function saveasdraft_civicrm_install(): void {
  _saveasdraft_civix_civicrm_install();
}

/**
 * Lifecycle hook :: uninstall().
 * Implements hook_civicrm_uninstall().
 * 
 * Draft Activity Staus will be deleted if it exists.
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_uninstall
 */
function saveasdraft_civicrm_uninstall(): void {
  _saveasdraft_civix_civicrm_uninstall();
}

/**
 * Lifecycle hook :: enable().
 * Implements hook_civicrm_enable().
 *
 * Draft Status will be enabled.
 * 
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_enable
 */
function saveasdraft_civicrm_enable(): void {
  _saveasdraft_civix_civicrm_enable();
}

/**
 * Lifecycle hook :: disable().
 * Implements hook_civicrm_disable().
 *
 * Draft Status will be disabled.
 * 
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_disable
 */
function saveasdraft_civicrm_disable(): void {
  _saveasdraft_civix_civicrm_disable();
}

function getDraftStatusID(){
  $optionGroup = civicrm_api4('OptionGroup', 'get', [
    'select' => [
      'id',
    ],
    'where' => [
      ['title', '=', 'Activity Status'],
    ],
    'checkPermissions' => FALSE,
  ], 0);

  $ActivityStatusGroupID = $optionGroup['id'];

  $optionValues = civicrm_api4('OptionValue', 'get', [
    'select' => [
      'value',
    ],
    'where' => [
      ['option_group_id', '=', $ActivityStatusGroupID],
      ['label', '=', 'Draft'],
    ],
    'checkPermissions' => FALSE,
  ], 0);

  return $optionValues['value'];
}

function getSelectedStatusID(){
  $selected_status = Civi::settings()->get('activity_status'); 
  return $selected_status;
}


/**
 * Implementation of hook_civicrm_buildForm
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_buildForm
 */
function saveasdraft_civicrm_buildForm($formName, &$form) {

  
  $editContactActivity = $formName == 'CRM_Activity_Form_Activity' && $form->getAction() == CRM_Core_Action::UPDATE;
  $addContactActivity = $formName == 'CRM_Activity_Form_Activity' && $form->getAction() == CRM_Core_Action::ADD;
  $editCaseActivity = $formName == 'CRM_Case_Form_Activity' && $form->getAction() == CRM_Core_Action::UPDATE;
  $addCaseActivity = $formName == 'CRM_Case_Form_Activity' && $form->getAction() == CRM_Core_Action::ADD;

  if ($editContactActivity || $addContactActivity || $editCaseActivity || $addCaseActivity) {

    // $message = array(
    //   'info' => ts('This is a custom message to display.'),
    // );

    // $js = array(
    //   'onclick' => "alert(" . json_encode($message['info']) . "); return false;"
    // );
      
    foreach ($form->_elementIndex as $element => $index) {
      if ($element =='buttons') {
        if ($form->_elements[$index]->_elements['0']->_attributes['value'] != 'Delete' &&
            $form->_action != 4) {
          $form->addButtons(array(
              array(
                'type' => 'upload',
                'name' => ts('Save'),
                'isDefault' => TRUE
              ),
              array(
                'type' => 'upload', 
                'name' => ts('Save as Draft'),
                'spacing' => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;',
                'subName' => 'draft',
                'icon' => 'fa-floppy-o',
                // 'js' => $js,
              ),
              array(
                'type' => 'cancel',
                'name' => ts('Cancel')
              )
            )
          );
        }
      }
    }

    // Set a GLOBAL that tells the hook_civicrm_pre(). if the Save as Draft button has been clicked.
    $buttonName = $form->controller->getButtonName();
    if ($buttonName == $form->getButtonName('upload', 'draft')) {
      global $SaveAsDraftClicked;
      $SaveAsDraftClicked = TRUE;
    }
    elseif ($buttonName == $form->getButtonName('upload')){
      global $SaveClicked;
      $SaveClicked = TRUE;
    }
  }
  if($editContactActivity || $addContactActivity){
    Civi::resources()->addScriptFile('saveasdraft','disable.js');
  }
}

/**
 * Implements hook_civicrm_pre().
 *
 * @param string $op
 * @param string $objectName
 * @param int $id
 * @param array $params
 */
function saveasdraft_civicrm_pre($op, $objectName, $id, &$params) {
  global $SaveAsDraftClicked;
  global $SaveClicked;
  if ($op == 'create' && $objectName == 'Activity') {
    if($SaveAsDraftClicked == TRUE){
    $statusID = getDraftStatusID();
    // Clear assignee_contact_id if "Save as Draft" button is clicked
      $params['assignee_contact_id'] = "";
      $params['status_id'] = $statusID;
    }
    elseif ($SaveClicked == TRUE && $params['status_id'] == getDraftStatusID()){
      $SelectedStatusID = getSelectedStatusID();
      $params['status_id'] = $SelectedStatusID;
    }
  }
  elseif ($op == "edit" && $objectName == 'Activity'){
    if($SaveAsDraftClicked == TRUE){
      $statusID = getDraftStatusID();
      // Clear assignee_contact_id if "Save as Draft" button is clicked
        $params['assignee_contact_id'] = "";
        $params['status_id'] = $statusID;
    }
    elseif ($SaveClicked == TRUE && $params['status_id'] == getDraftStatusID()){
      $SelectedStatusID = getSelectedStatusID();
      $params['status_id'] = $SelectedStatusID;
    }
  }
}

/**
 * Implements hook_civicrm_navigationMenu().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_navigationMenu
 */
function saveasdraft_civicrm_navigationMenu(&$menu) {
  _saveasdraft_civix_insert_navigation_menu($menu, "Administer/System Settings", [
    'label' => ts('Save as Draft Settings', ['domain' => 'saveasdraft']),
    'name' => 'save_as_draft',
    'url' => 'civicrm/saveasdraftsettings?reset=1',
    'permission' => 'administer CiviCRM',
    'operator' => 'OR',
    'separator' => 0,
  ]);
  _saveasdraft_civix_navigationMenu($menu);
} 
