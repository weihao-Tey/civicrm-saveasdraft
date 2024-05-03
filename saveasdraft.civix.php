<?php

// AUTO-GENERATED FILE -- Civix may overwrite any changes made to this file

/**
 * The ExtensionUtil class provides small stubs for accessing resources of this
 * extension.
 */
class CRM_Saveasdraft_ExtensionUtil {
  const SHORT_NAME = 'saveasdraft';
  const LONG_NAME = 'saveasdraft';
  const CLASS_PREFIX = 'CRM_Saveasdraft';

  /**
   * Translate a string using the extension's domain.
   *
   * If the extension doesn't have a specific translation
   * for the string, fallback to the default translations.
   *
   * @param string $text
   *   Canonical message text (generally en_US).
   * @param array $params
   * @return string
   *   Translated text.
   * @see ts
   */
  public static function ts($text, $params = []): string {
    if (!array_key_exists('domain', $params)) {
      $params['domain'] = [self::LONG_NAME, NULL];
    }
    return ts($text, $params);
  }

  /**
   * Get the URL of a resource file (in this extension).
   *
   * @param string|NULL $file
   *   Ex: NULL.
   *   Ex: 'css/foo.css'.
   * @return string
   *   Ex: 'http://example.org/sites/default/ext/org.example.foo'.
   *   Ex: 'http://example.org/sites/default/ext/org.example.foo/css/foo.css'.
   */
  public static function url($file = NULL): string {
    if ($file === NULL) {
      return rtrim(CRM_Core_Resources::singleton()->getUrl(self::LONG_NAME), '/');
    }
    return CRM_Core_Resources::singleton()->getUrl(self::LONG_NAME, $file);
  }

  /**
   * Get the path of a resource file (in this extension).
   *
   * @param string|NULL $file
   *   Ex: NULL.
   *   Ex: 'css/foo.css'.
   * @return string
   *   Ex: '/var/www/example.org/sites/default/ext/org.example.foo'.
   *   Ex: '/var/www/example.org/sites/default/ext/org.example.foo/css/foo.css'.
   */
  public static function path($file = NULL) {
    // return CRM_Core_Resources::singleton()->getPath(self::LONG_NAME, $file);
    return __DIR__ . ($file === NULL ? '' : (DIRECTORY_SEPARATOR . $file));
  }

  /**
   * Get the name of a class within this extension.
   *
   * @param string $suffix
   *   Ex: 'Page_HelloWorld' or 'Page\\HelloWorld'.
   * @return string
   *   Ex: 'CRM_Foo_Page_HelloWorld'.
   */
  public static function findClass($suffix) {
    return self::CLASS_PREFIX . '_' . str_replace('\\', '_', $suffix);
  }

}

use CRM_Saveasdraft_ExtensionUtil as E;

/**
 * (Delegated) Implements hook_civicrm_config().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_config
 */
function _saveasdraft_civix_civicrm_config($config = NULL) {
  static $configured = FALSE;
  if ($configured) {
    return;
  }
  $configured = TRUE;

  $extRoot = __DIR__ . DIRECTORY_SEPARATOR;
  $include_path = $extRoot . PATH_SEPARATOR . get_include_path();
  set_include_path($include_path);
  // Based on <compatibility>, this does not currently require mixin/polyfill.php.
}

/**
 * Implements hook_civicrm_install().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_install
 */
function _saveasdraft_civix_civicrm_install() {
  _saveasdraft_civix_civicrm_config();
  
  $ActivityStatusID = getActivityStatusOptionGroupIds();

  if($ActivityStatusID != NULL){
    $optionValue = civicrm_api4('OptionValue', 'get', [
      'where' => [
        ['option_group_id', '=', $ActivityStatusID],
        ['label', '=', 'Draft'],
      ],
      'checkPermissions' => FALSE,
    ]);
  }

   // Check if any results were returned
   if (!empty($optionValue)) {
    if ($optionValue->rowCount == 0){
      $result = civicrm_api4('OptionValue', 'create', [
        'values' => [
          'option_group_id' => $ActivityStatusID,
          'label' => 'Draft',
        ],
        'checkPermissions' => FALSE,
      ], 0);
    }
    else{
      civi::log()->info("Activity Status already exists.");
    }
  } else {
    // Handle the case when no results are found
    // For example, you could set $optionValue to NULL or take appropriate action
    $optionValue = NULL;
  }
  // Based on <compatibility>, this does not currently require mixin/polyfill.php.
}

/**
 * Implements hook_civicrm_uninstall().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_uninstall
 */
function _saveasdraft_civix_civicrm_uninstall() {

  $ActivityStatusID = getActivityStatusOptionGroupIds();

  if ($ActivityStatusID != NULL){
    $optionValue = civicrm_api4('OptionValue', 'get', [
      'select' => [
        'label',
      ],
      'where' => [
        ['option_group_id', '=', $ActivityStatusID ],
        ['label', '=', 'Draft'],
      ],
      'checkPermissions' => FALSE,
    ]);
  }

  // Check if any results were returned
  if (!empty($optionValue)) {
    if ($optionValue->rowCount >= 1){
      $result = civicrm_api4('OptionValue', 'delete', [
        'where' => [
          ['option_group_id', '=', $ActivityStatusID ],
          ['label', '=', 'Draft'],
        ],
        'checkPermissions' => FALSE,
      ], 0);
    }
    else{
      civi::log()->info("Activity Status not found.");
    }
  } else {
    // Handle the case when no results are found
    // For example, you could set $optionValue to NULL or take appropriate action
    $optionValue = NULL;
  }
}

/**
 * (Delegated) Implements hook_civicrm_enable().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_enable
 */
function _saveasdraft_civix_civicrm_enable(): void {
  _saveasdraft_civix_civicrm_config();

  $ActivityStatusID = getActivityStatusOptionGroupIds();

  if ($ActivityStatusID != NULL){
    $optionValue = civicrm_api4('OptionValue', 'get', [
      'select' => [
        'is_active',
      ],
      'where' => [
        ['option_group_id', '=', $ActivityStatusID],
        ['label', '=', 'Draft'],
        ['is_active', '=', FALSE],
      ],
      'checkPermissions' => FALSE,
    ]);
  }

    // Check if any results were returned
      if(!empty($optionValue)){
        $result = civicrm_api4('OptionValue', 'update', [
          'values' => [
            'is_active' => TRUE,
          ],
          'where' => [
            ['label', '=', 'Draft'],
          ],
          'checkPermissions' => FALSE,
        ], 0);
      }
      else{
        civi::log()->info("Draft Status is already enabled.");
      }
  // Based on <compatibility>, this does not currently require mixin/polyfill.php.
}

/**
 * (Delegated) Implements hook_civicrm_enable().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_enable
 */
function _saveasdraft_civix_civicrm_disable(): void {

  $ActivityStatusID = getActivityStatusOptionGroupIds();

  if ($ActivityStatusID != NULL){
    $optionValue = civicrm_api4('OptionValue', 'get', [
      'select' => [
        'is_active',
      ],
      'where' => [
        ['option_group_id', '=', $ActivityStatusID],
        ['label', '=', 'Draft'],
        ['is_active', '=', TRUE],
      ],
      'checkPermissions' => FALSE,
    ]);
  }

   // Check if any results were returned
   if (!empty($optionValue)) {
    if ($optionValue->rowCount != 0){
      $result = civicrm_api4('OptionValue', 'update', [
        'values' => [
          'is_active' => FALSE,
        ],
        'where' => [
          ['label', '=', 'Draft'],
        ],
        'checkPermissions' => FALSE,
      ], 0);
    }
    else{
      civi::log()->info("Draft Status is already disabled.");
    }
  } else {
    // Handle the case when no results are found
    // For example, you could set $optionValue to NULL or take appropriate action
    $optionValue = NULL;
  }
}

/**
 * Inserts a navigation menu item at a given place in the hierarchy.
 *
 * @param array $menu - menu hierarchy
 * @param string $path - path to parent of this item, e.g. 'my_extension/submenu'
 *    'Mailing', or 'Administer/System Settings'
 * @param array $item - the item to insert (parent/child attributes will be
 *    filled for you)
 *
 * @return bool
 */
function _saveasdraft_civix_insert_navigation_menu(&$menu, $path, $item) {
  // If we are done going down the path, insert menu
  if (empty($path)) {
    $menu[] = [
      'attributes' => array_merge([
        'label' => $item['name'] ?? NULL,
        'active' => 1,
      ], $item),
    ];
    return TRUE;
  }
  else {
    // Find an recurse into the next level down
    $found = FALSE;
    $path = explode('/', $path);
    $first = array_shift($path);
    foreach ($menu as $key => &$entry) {
      if ($entry['attributes']['name'] == $first) {
        if (!isset($entry['child'])) {
          $entry['child'] = [];
        }
        $found = _saveasdraft_civix_insert_navigation_menu($entry['child'], implode('/', $path), $item);
      }
    }
    return $found;
  }
}

/**
 * (Delegated) Implements hook_civicrm_navigationMenu().
 */
function _saveasdraft_civix_navigationMenu(&$nodes) {
  if (!is_callable(['CRM_Core_BAO_Navigation', 'fixNavigationMenu'])) {
    _saveasdraft_civix_fixNavigationMenu($nodes);
  }
}

/**
 * Given a navigation menu, generate navIDs for any items which are
 * missing them.
 */
function _saveasdraft_civix_fixNavigationMenu(&$nodes) {
  $maxNavID = 1;
  array_walk_recursive($nodes, function($item, $key) use (&$maxNavID) {
    if ($key === 'navID') {
      $maxNavID = max($maxNavID, $item);
    }
  });
  _saveasdraft_civix_fixNavigationMenuItems($nodes, $maxNavID, NULL);
}

function _saveasdraft_civix_fixNavigationMenuItems(&$nodes, &$maxNavID, $parentID) {
  $origKeys = array_keys($nodes);
  foreach ($origKeys as $origKey) {
    if (!isset($nodes[$origKey]['attributes']['parentID']) && $parentID !== NULL) {
      $nodes[$origKey]['attributes']['parentID'] = $parentID;
    }
    // If no navID, then assign navID and fix key.
    if (!isset($nodes[$origKey]['attributes']['navID'])) {
      $newKey = ++$maxNavID;
      $nodes[$origKey]['attributes']['navID'] = $newKey;
      $nodes[$newKey] = $nodes[$origKey];
      unset($nodes[$origKey]);
      $origKey = $newKey;
    }
    if (isset($nodes[$origKey]['child']) && is_array($nodes[$origKey]['child'])) {
      _saveasdraft_civix_fixNavigationMenuItems($nodes[$origKey]['child'], $maxNavID, $nodes[$origKey]['attributes']['navID']);
    }
  }
}

function getActivityStatusOptionGroupIds() {
  $optionGroup = civicrm_api4('OptionGroup', 'get', [
    'select' => [
      'id',
    ],
    'where' => [
      ['title', '=', 'Activity Status'],
    ],
    'checkPermissions' => FALSE,
  ], 0);
  return $optionGroup['id'] ?? NULL;
}