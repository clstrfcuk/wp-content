<?php

/**
 * Localize strings for javascript
 */

$accept  = __( 'Yes, proceed', '__x__' );
$decline = __( 'No, take me back', '__x__' );

return array(

  'integration-mode'            => 'pro',
  'editor-tab-logo-path'        => 'svg/logo-flat-content',
	'edit-with-cornerstone'       => __( 'Edit with Pro', '__x__' ),
  'visual-tab'                  => __( 'Visual', '__x__' ),
  'text-tab'                    => __( 'Text', '__x__' ),
	'cornerstone-tab'             => __( 'Pro', '__x__' ),
	'insert-cornerstone'          => __( 'Insert Shortcode', '__x__' ),
	'updating'                    => __( 'Updating', '__x__' ),
	'confirm-yep'                 => __( 'Yep', '__x__' ),
	'confirm-nope'                => __( 'Nope', '__x__' ),
	'manual-edit-warning'         => __( 'Hold up! You&apos;re welcome to make changes to the content. However, these will not be reflected in the content builder. If you edit the page in the content builder again, any changes made here will be overwritten. Do you wish to continue?', '__x__' ),
	'overwrite-warning'           => __( 'Hold up! The content has been modified outside of the content builder. Editing in the content builder will replace the current content. Do you wish to continue?', '__x__' ),
	'manual-edit-accept'          => $accept,
	'manual-edit-decline'         => $decline,
	'overwrite-accept'            => $accept,
	'overwrite-decline'           => $decline,
	'default-title'               => __( 'Content Draft', '__x__'),

  'dashboard-title'                  => __( 'Pro', '__x__' ),
  'dashboard-menu-title'             => __( 'Home', '__x__'),
  'dashboard-settings-title'         => __( 'Settings', '__x__' ),
  'dashboard-settings-path'          => 'pro-settings',
  'dashboard-settings-save-title'    => __( 'Save Settings', '__x__' ),
  'dashboard-settings-save-update'   => __( 'Update', '__x__' ),
  'dashboard-settings-save-info'     => __( 'Once you are satisfied with your settings, click the button below to save them.', '__x__' ),
  'dashboard-settings-save-updating' => __( 'Updating&hellip;', '__x__' ),
  'dashboard-settings-save-updated'  => __( 'Settings Saved!', '__x__' ),
  'dashboard-settings-save-error'    => __( 'Sorry! Unable to Save', '__x__' ),

  'dashboard-settings-system-title' => __( 'System', '__x__' ),

  'dashboard-settings-system-clear-style-cache-title' => __( 'Clear Style Cache', '__x__' ),
  'dashboard-settings-system-clear-style-cache-description' => __( 'For quicker page loads V2 Elements will remember the CSS generated when they were last saved. This is automatically cleared when Pro is updated. It may be useful to clear manually if any V2 Elements are missing styling.', '__x__' ),
  'dashboard-settings-system-clear-style-cache-button'      => __( 'Clear Style Cache', '__x__' ),
  'dashboard-settings-system-clear-style-cache-button-clearing' => __( 'Clearing&hellip;', '__x__' ),
  'dashboard-settings-system-clear-style-cache-button-cleared'  => __( 'Cleared!', '__x__' ),
  'dashboard-settings-system-clear-style-cache-button-error'    => __( 'Unable to clear!', '__x__' ),

  'permissions.title'            => __( 'Permissions', '__x__' ),
  'permissions.preferences'      => __( 'User Preferences', '__x__' ),
  'permissions.general'          => __( 'General', '__x__' ),
  'permissions.v2-elements'      => __( 'V2 Elements', '__x__' ),
  'permissions.classic-elements' => __( 'Classic Elements', '__x__' ),
  'permissions.feature'          => __( 'Feature', '__x__' ),
  'permissions.name'             => __( 'Name', '__x__' ),
  'permissions.allow'            => __( 'Allow', '__x__' ),
  'permissions.access'           => __( 'Access', '__x__' ),
  'permissions.config'           => __( 'Configure', '__x__' ),
  'permissions.close'            => __( 'Close', '__x__' ),
  'permissions.additional'       => __( 'Additional Permissions', '__x__' ),
  'permissions.enabled'          => __( 'Enabled', '__x__' ),
  'permissions.enable-access'    => __( 'Enable access before setting additional permissions.', '__x__' ),
  'permissions.set-additional'   => __( 'Click to set additional permissions.', '__x__' ),
  'permissions.toggle-all'       => __( 'Toggle All Permissions', '__x__' ),
  'permissions.macros'           => __( 'Macros', '__x__' ),
  'permissions.macros-label'     => __( 'Make a Selection', '__x__' ),
  'permissions.macros-desc'      => __( 'Applying a macro will automatically configure multiple permissions to achieve a desired effect.', '__x__' ),
  'permissions.macros-apply'     => __( 'Apply', '__x__' ),
  'permissions.macros-warning'   => __( 'This will alter your currently selected permissions for this role. Would you like to continue?', '__x__' ),
  'permissions.macros-continue'  => __( 'Yes, Proceed', 'cornerstone' ),
  'permissions.macros-go-back'   => __( 'No, Go Back', 'cornerstone' ),

  'permissions.macro-text-only'                => __( 'Enforce Text Only', '__x__' ),
  'permissions.macro-disable-preferences'      => __( 'Disable User Preferences', '__x__' ),
  'permissions.macro-enable-v2-elements'       => __( 'Enable All V2 Elements', '__x__' ),
  'permissions.macro-disable-v2-elements'      => __( 'Disable All V2 Elements', '__x__' ),
  'permissions.macro-enable-classic-elements'  => __( 'Enable All Classic Elements', '__x__' ),
  'permissions.macro-disable-classic-elements' => __( 'Disable All Classic Elements', '__x__' ),
  'permissions.macro-reset'                    => __( 'Reset to Defaults', '__x__' ),

  'permissions.preference-user' => __( 'User Preference', '__x__' ),
  'permissions.preference-on'   => __( 'Always On', '__x__' ),
  'permissions.preference-off'  => __( 'Always Off', '__x__' ),

  'permissions.inspect'            => __( 'Inspect', '__x__' ),
  'permissions.show-in-library'    => __( 'Show In Library','__x__' ),
  'permissions.apply-preset'       => __( 'Apply Preset', '__x__' ),
  'permissions.save-preset'        => __( 'Save Preset', '__x__' ),
  'permissions.design-controls'    => __( 'Design Controls', '__x__' ),
  'permissions.customize-controls' => __( 'Customize Controls', '__x__' ),

  'permissions.create' => __( 'Create', '__x__' ),
  'permissions.change' => __( 'Change', '__x__' ),
  'permissions.rename' => __( 'Rename', '__x__' ),
  'permissions.delete' => __( 'Delete', '__x__' ),

  'permissions.manage-google'  => __( 'Manage Google', '__x__' ),
  'permissions.manage-typekit' => __( 'Manage Typekit', '__x__' ),

  'permissions.create-from-template'   => __( 'Create From Template', '__x__' ),
  'permissions.save-as-template'       => __( 'Save As Template', '__x__' ),
  'permissions.manage-assignments'     => __( 'Manage Assignments', '__x__' ),
  'permissions.elements-create'        => __( 'Add Elements', '__x__' ),
  'permissions.elements-delete'        => __( 'Delete Elements', '__x__' ),
  'permissions.elements-move'          => __( 'Move Elements', '__x__' ),
  'permissions.elements-inspect'       => __( 'Inspect Elements', '__x__' ),

  'permissions.publish'                => __( 'Publish', '__x__' ),
  'permissions.insert-templates'       => __( 'Insert Templates', '__x__' ),
  'permissions.save-templates'         => __( 'Save Templates', '__x__' ),
  'permissions.manage-layout'          => __( 'Manage Layout', '__x__' ),
  'permissions.apply-presets'          => __( 'Apply Presets', '__x__' ),
  'permissions.save-presets'           => __( 'Save Presets', '__x__' ),
  'permissions.edit-custom-css'        => __( 'Edit Custom CSS', '__x__' ),
  'permissions.edit-custom-js'         => __( 'Edit Custom JS', '__x__' ),
  'permissions.settings'               => __( 'Access Settings', '__x__' ),
  'permissions.skeleton-mode'          => __( 'Skeleton Mode', '__x__' ),

  'permissions.import'                 => __( 'Import', '__x__' ),
  'permissions.download'               => __( 'Download', '__x__' ),
  'permissions.update-preview-image'   => __( 'Update Preview Image', '__x__' ),
  'permissions.hide'                   => __( 'Hide', '__x__' ),
  'permissions.manage-default-presets' => __( 'Manage Default Presets', '__x__' ),
  'permissions.design-cloud'           => __( 'Access Design Cloud', '__x__' ),


  'plugin-update-nothing'   => __( 'Nothing to report.', '__x__' ),
  'plugin-update-new'       => __( 'New version available!', '__x__' ),
  'plugin-update-error'     => __( 'Unable to check for updates. Try again later.', '__x__' ),
  'plugin-update-checking'  => __( 'Checking&hellip;', '__x__' ),
  'plugin-update-changelog' => __( 'Visit the <a href="http://theme.co/changelog/#pro">Themeco Changelog</a> for more information.', '__x__' ),
  'plugin-update-notice'    => __( '<a href="%s">Validate to enable automatic updates</a>', '__x__' ),

  'huebert-select-color' => __( 'Select Color', '__x__' ),

  'x-shortcodes-notice' => __( '<strong>X &ndash; Shortcodes has been deactivated</strong>. This plugin is no longer a requirement of X, and can safely be deleted.', '__x__' ),

);
