<?php
use CRM_Saveasdraft_ExtensionUtil as E;

return [
    'activity_status' => [
      'group_name' => 'Save as Draft Settings',
      'group' => 'saveasdraft',
      'name' => 'activity_status',
      'type' => 'Integer',
      'title' => E::ts('Activity Status'),
      'description' => E::ts('Select the activity status to set after saving a draft activity.'),
    //   'help_text' => E::ts('When selecting assignees for an activity, limit the available individuals to those in the specified group'),
      'html_type' => 'select',
      'html_attributes' => ['options' => 'STATUS'],
      'quick_form_type' => 'Element',
      'is_required' => FALSE,
      'pseudoconstant' => [
        'callback' => 'CRM_Core_PseudoConstant::activityStatus',
      ],
      'settings_pages' => ['saveasdraftsettings' => ['weight' => 20], 'search' => ['weight' => 20]],
    ],
  ];
  