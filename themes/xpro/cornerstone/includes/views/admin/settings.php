<div class="tco-reset tco-wrap tco-wrap-settings tco-alt-cs" data-tco-module="cs-settings">
  <div class="tco-content">
    <div class="wrap"><h2>WordPress Wrap</h2></div>

    <form class="tco-form" data-tco-module-target="form">

      <div class="tco-main">
        <div class="tco-row">
          <div class="tco-column">
            <div class="tco-box">
              <header class="tco-box-header">
                <h2 class="tco-box-title"><?php e_csi18n('admin.dashboard-settings-title'); ?></h2>
              </header>
              <div class="tco-box-content tco-pan"> <?php $this->settings_handler->render_form(); ?> </div>
            </div>
          </div>
        </div>
        <div class="tco-row">
          <div class="tco-column" data-tco-module="cs-role-manager">
            <div class="tco-box tco-box-permissions">
              <header class="tco-box-header">
                <h2 class="tco-box-title"><?php e_csi18n('admin.dashboard-permissions-title'); ?></h2>
              </header>

              <?php
                $wp_roles = wp_roles();
                $active_roles = array();//$wp_roles->roles;
              ?>
              <div class="tco-box-content tco-pan">

                <ul class="roles">
                  <?php foreach ($active_roles as $role => $value) : ?>
                    <li class="<?php echo $role;?>" data-role-trigger="<?php echo $role;?>"><?php echo $value['name']; ?></li>
                  <?php endforeach; ?>
                </ul>
                <?php foreach ($active_roles as $role => $value) : ?>
                <div class="role-permissions <?php echo $role;?>">
                  <div>
                    <h3>General</h3>
                    <table>
                      <thead>
                        <tr>
                          <th class="name">Feature</th>
                          <th class="enabled">Access</th>
                          <th class="permissions">Additional Permissions</th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr>
                          <td class="name">Headers</td>
                          <td class="enabled">
                            <label class="tco-rc tco-checkbox" for="<?php echo "$role-post-type-enabled-headers";?>">
                              <span class="tco-form-control-indicator-label">Enable</span>
                              <input <?php checked( 'administrator' === $role ); ?> id="<?php echo "$role-post-type-enabled-headers";?>" name="hide_access_path" value="enabled" class="tco-form-control tco-form-control-max" type="checkbox" data-cs-control="checkbox">
                              <span class="tco-form-control-indicator"></span>
                            </label>
                          </td>
                          <td class="permissions">
                              <span class="empty">Enable Access before setting additional permissions.</span>
                            <?php
                            echo CS()->view( 'admin/forms/multi-select', false, array(
                              'type' => 'multi-select',
                              'name' => "$role-post-type-permissions-headers",
                              'value' => 'administratoraaaaa' !== $role ? 'create,create_from_template,delete,save_as_template,manage_assignments,rename,publish,translate,insert_templates,save_templates,manage_layout,elements_create,elements_delete,elements_move,elements_inspect,apply_presets,save_presets,edit_custom_css,edit_custom_js' : '',
                              'options' => array(
                                'placeholder' => __( 'Click to set additional permissions.', 'cornerstone' ),
                                'choices' => array(
                                  array( 'value' => 'create',                 'label' => 'Create' ),
                                  array( 'value' => 'create_from_template',   'label' => 'Create From Template' ),
                                  array( 'value' => 'rename',                 'label' => 'Rename' ),
                                  array( 'value' => 'delete',                 'label' => 'Delete' ),
                                  array( 'value' => 'save_as_template',       'label' => 'Save As Template' ),
                                  array( 'value' => 'manage_assignments',     'label' => 'Manage Assignments' ),
                                  array( 'value' => 'elements_create',        'label' => 'Add Elements' ),
                                  array( 'value' => 'elements_delete',        'label' => 'Delete Elements' ),
                                  array( 'value' => 'elements_move',          'label' => 'Move Elements' ),
                                  array( 'value' => 'elements_inspect',       'label' => 'Inspect Elements' ),
                                  array( 'value' => 'apply_presets', 'label' => 'Apply Presets' ),
                                  array( 'value' => 'save_presets',  'label' => 'Save Presets' ),
                                  array( 'value' => 'edit_custom_css',        'label' => 'Edit Custom CSS' ),
                                  array( 'value' => 'edit_custom_js',         'label' => 'Edit Custom JS' ),
                                  array( 'value' => 'text_only',              'label' => 'Text Only' ),
                                )
                              )
                            ), true );?>
                          </td>
                        </tr>
                        <tr>
                          <td class="name">Pages</td>
                          <td class="enabled">
                            <label class="tco-rc tco-checkbox" for="<?php echo "$role-post-type-enabled-page";?>">
                              <span class="tco-form-control-indicator-label">Enable</span>
                              <input <?php checked( 'administrator' === $role ); ?> id="<?php echo "$role-post-type-enabled-page";?>" name="hide_access_path" value="enabled" class="tco-form-control tco-form-control-max" type="checkbox" data-cs-control="checkbox">
                              <span class="tco-form-control-indicator"></span>
                            </label>
                          </td>
                          <td class="permissions">
                              <span class="empty">Enable Access before setting additional permissions.</span>
                            <?php
                            echo CS()->view( 'admin/forms/multi-select', false, array(
                              'type' => 'multi-select',
                              'name' => "$role-post-type-permissions-page",
                              'value' => 'administratoraaaaa' !== $role ? 'publish,translate,insert_templates,save_templates,manage_layout,elements_create,elements_delete,elements_move,elements_inspect,apply_presets,save_presets,edit_custom_css,edit_custom_js,settings,skeleton_mode' : '',
                              'options' => array(
                                'placeholder' => __( 'Click to set additional permissions.', 'cornerstone' ),
                                'choices' => array(
                                  array( 'value' => 'publish',                'label' => 'Publish' ),
                                  array( 'value' => 'insert_templates',       'label' => 'Insert Templates' ),
                                  array( 'value' => 'save_templates',         'label' => 'Save Templates' ),
                                  array( 'value' => 'manage_layout',          'label' => 'Manage Layout' ),
                                  array( 'value' => 'elements_create',        'label' => 'Add Elements' ),
                                  array( 'value' => 'elements_delete',        'label' => 'Delete Elements' ),
                                  array( 'value' => 'elements_move',          'label' => 'Move Elements' ),
                                  array( 'value' => 'elements_inspect',       'label' => 'Inspect Elements' ),
                                  array( 'value' => 'apply_presets', 'label' => 'Apply Presets' ),
                                  array( 'value' => 'save_presets',  'label' => 'Save Presets' ),
                                  array( 'value' => 'edit_custom_css',        'label' => 'Edit Custom CSS' ),
                                  array( 'value' => 'edit_custom_js',         'label' => 'Edit Custom JS' ),
                                  array( 'value' => 'settings',  'label' => 'Access Settings' ),
                                  array( 'value' => 'skeleton_mode',    'label' => 'Skeleton Mode' ),
                                  array( 'value' => 'text_only',              'label' => 'Text Only' ),
                                )
                              )
                            ), true ); ?>
                          </td>
                        </tr>
                        <?php

                        $post_types = get_post_types( array(
                    			'public'   => true,
                    			'show_ui' => true,
                    			'exclude_from_search' => false
                    		) , 'objects' );

                    		unset( $post_types['attachment'] );
                        unset( $post_types['page'] );

                        $types = array();
                    		foreach ( $post_types as $name => $post_type ) {
                    			$type = array( 'value' => $name );
                    			$type['label'] = ( isset( $post_type->labels->name ) ) ? $post_type->labels->name : $name;
                          $types[] = $type;
                    		}

                        foreach ( $types as $type ) :

                        ?>
                        <tr>
                          <td class="name"><?php echo $type['label']; ?></td>
                          <td class="enabled">
                            <label class="tco-rc tco-checkbox" for="<?php echo "$role-post-type-enabled-" . $type['value'];?>">
                              <span class="tco-form-control-indicator-label">Enable</span>
                              <input <?php checked( 'postz' === $type['value'] && 'administrator' === $role ); ?> id="<?php echo "$role-post-type-enabled-" . $type['value'];?>" name="hide_access_path" value="enabled" class="tco-form-control tco-form-control-max" type="checkbox" data-cs-control="checkbox">
                              <span class="tco-form-control-indicator"></span>
                            </label>
                          </td>
                          <td class="permissions">
                              <span class="empty">Enable Access before setting additional permissions.</span>
                            <?php
                            echo CS()->view( 'admin/forms/multi-select', false, array(
                              'type' => 'multi-select',
                              'name' => "$role-post-type-permissions-" . $type['value'],
                              'value' => 'administratoraaaaa' !== $role ? 'publish,translate,insert_templates,save_templates,manage_layout,elements_create,elements_delete,elements_move,elements_inspect,apply_presets,save_presets,edit_custom_css,edit_custom_js,settings,skeleton_mode' : '',
                              'options' => array(
                                'placeholder' => __( 'Click to set additional permissions.', 'cornerstone' ),
                                'choices' => array(
                                  array( 'value' => 'publish',                'label' => 'Publish' ),
                                  array( 'value' => 'insert_templates',       'label' => 'Insert Templates' ),
                                  array( 'value' => 'save_templates',         'label' => 'Save Templates' ),
                                  array( 'value' => 'manage_layout',          'label' => 'Manage Layout' ),
                                  array( 'value' => 'elements_create',        'label' => 'Add Elements' ),
                                  array( 'value' => 'elements_delete',        'label' => 'Delete Elements' ),
                                  array( 'value' => 'elements_move',          'label' => 'Move Elements' ),
                                  array( 'value' => 'elements_inspect',       'label' => 'Inspect Elements' ),
                                  array( 'value' => 'apply_presets', 'label' => 'Apply Presets' ),
                                  array( 'value' => 'save_presets',  'label' => 'Save Presets' ),
                                  array( 'value' => 'edit_custom_css',        'label' => 'Edit Custom CSS' ),
                                  array( 'value' => 'edit_custom_js',         'label' => 'Edit Custom JS' ),
                                  array( 'value' => 'settings',  'label' => 'Access Settings' ),
                                  array( 'value' => 'skeleton_mode',    'label' => 'Skeleton Mode' ),
                                  array( 'value' => 'text_only',              'label' => 'Text Only' ),
                                )
                              )
                            ), true ); ?>
                          </td>
                        </tr>
                        <?php endforeach;?>
                        <tr>
                          <td class="name">Footers</td>
                          <td class="enabled">
                            <label class="tco-rc tco-checkbox" for="<?php echo "$role-post-type-enabled-footers";?>">
                              <span class="tco-form-control-indicator-label">Enable</span>
                              <input <?php checked( 'administrator' === $role ); ?> id="<?php echo "$role-post-type-enabled-footers";?>" name="hide_access_path" value="enabled" class="tco-form-control tco-form-control-max" type="checkbox" data-cs-control="checkbox">
                              <span class="tco-form-control-indicator"></span>
                            </label>
                          </td>
                          <td class="permissions">
                              <span class="empty">Enable Access before setting additional permissions.</span>
                            <?php
                            echo CS()->view( 'admin/forms/multi-select', false, array(
                              'type' => 'multi-select',
                              'name' => "$role-post-type-permissions-footers",
                              'value' => 'administratoraaaaa' !== $role ? 'create,create_from_template,delete,save_as_template,manage_assignments,rename,publish,translate,insert_templates,save_templates,manage_layout,elements_create,elements_delete,elements_move,elements_inspect,apply_presets,save_presets,edit_custom_css,edit_custom_js' : '',
                              'options' => array(
                                'placeholder' => __( 'Click to set additional permissions.', 'cornerstone' ),
                                'choices' => array(
                                  array( 'value' => 'create',                 'label' => 'Create' ),
                                  array( 'value' => 'create_from_template',   'label' => 'Create From Template' ),
                                  array( 'value' => 'rename',                 'label' => 'Rename' ),
                                  array( 'value' => 'delete',                 'label' => 'Delete' ),
                                  array( 'value' => 'save_as_template',       'label' => 'Save As Template' ),
                                  array( 'value' => 'manage_assignments',     'label' => 'Manage Assignments' ),
                                  array( 'value' => 'elements_create',        'label' => 'Add Elements' ),
                                  array( 'value' => 'elements_delete',        'label' => 'Delete Elements' ),
                                  array( 'value' => 'elements_move',          'label' => 'Move Elements' ),
                                  array( 'value' => 'elements_inspect',       'label' => 'Inspect Elements' ),
                                  array( 'value' => 'apply_presets', 'label' => 'Apply Presets' ),
                                  array( 'value' => 'save_presets',  'label' => 'Save Presets' ),
                                  array( 'value' => 'edit_custom_css',        'label' => 'Edit Custom CSS' ),
                                  array( 'value' => 'edit_custom_js',         'label' => 'Edit Custom JS' ),
                                  array( 'value' => 'text_only',              'label' => 'Text Only' ),
                                )
                              )
                            ), true );?>
                          </td>
                        </tr>
                        <tr>
                          <td class="name">Templates</td>
                          <td class="enabled">
                            <label class="tco-rc tco-checkbox" for="<?php echo "$role-manager-enabled-templates";?>">
                              <span class="tco-form-control-indicator-label">Enable</span>
                              <input <?php checked( 'administrator' === $role ); ?> id="<?php echo "$role-manager-enabled-templates";?>" name="hide_access_path" value="enabled" class="tco-form-control tco-form-control-max" type="checkbox" data-cs-control="checkbox">
                              <span class="tco-form-control-indicator"></span>
                            </label>
                          </td>
                          <td class="permissions">
                              <span class="empty">Enable Access before setting additional permissions.</span>
                            <?php echo CS()->view( 'admin/forms/multi-select', false, array(
                              'type' => 'multi-select',
                              'name' => "$role-manager-permissions-templates",
                              'value' => 'administratoraaaaa' !== $role ? 'import,download,delete,rename,update_preview_image,hide,design_cloud' : '',
                              'options' => array(
                                'placeholder' => __( 'Click to set additional permissions.', 'cornerstone' ),
                                'choices' => array(
                                  array( 'value' => 'import',                 'label' => 'Import' ),
                                  array( 'value' => 'download',               'label' => 'Download' ),
                                  array( 'value' => 'delete',                 'label' => 'Delete' ),
                                  array( 'value' => 'rename',                 'label' => 'Rename' ),
                                  array( 'value' => 'update_preview_image',   'label' => 'Update Preview Image' ),
                                  array( 'value' => 'hide',                   'label' => 'Hide' ),
                                  array( 'value' => 'manage_default_presets', 'label' => 'Manage Default Presets' ),
                                  array( 'value' => 'design_cloud',           'label' => 'Access Design Cloud' ),
                                )
                              )
                            ), true );?>
                          </td>
                        </tr>
                        <tr>
                          <td class="name">Global Blocks</td>
                          <td class="enabled">
                            <label class="tco-rc tco-checkbox" for="<?php echo "$role-post-type-enabled-global-blocks";?>">
                              <span class="tco-form-control-indicator-label">Enable</span>
                              <input <?php checked( 'administrator' === $role ); ?> id="<?php echo "$role-post-type-enabled-global-blocks";?>" name="hide_access_path" value="enabled" class="tco-form-control tco-form-control-max" type="checkbox" data-cs-control="checkbox">
                              <span class="tco-form-control-indicator"></span>
                            </label>
                          </td>
                          <td class="permissions">
                              <span class="empty">Enable Access before setting additional permissions.</span>
                            <?php
                            echo CS()->view( 'admin/forms/multi-select', false, array(
                              'type' => 'multi-select',
                              'name' => "$role-post-type-permissions-global-blocks",
                              'value' => 'administratoraaaaa' !== $role ? 'create,delete,rename,publish,translate,insert_templates,save_templates,manage_layout,elements_create,elements_delete,elements_move,elements_inspect,apply_presets,save_presets,edit_custom_css,edit_custom_js,skeleton_mode' : '',
                              'options' => array(
                                'placeholder' => __( 'Click to set additional permissions.', 'cornerstone' ),
                                'choices' => array(
                                  array( 'value' => 'create',                 'label' => 'Create' ),
                                  array( 'value' => 'delete',                 'label' => 'Delete' ),
                                  array( 'value' => 'rename',                 'label' => 'Rename' ),
                                  array( 'value' => 'insert_templates',       'label' => 'Insert Templates' ),
                                  array( 'value' => 'save_templates',         'label' => 'Save Templates' ),
                                  array( 'value' => 'manage_layout',          'label' => 'Manage Layout' ),
                                  array( 'value' => 'elements_create',        'label' => 'Add Elements' ),
                                  array( 'value' => 'elements_delete',        'label' => 'Delete Elements' ),
                                  array( 'value' => 'elements_move',          'label' => 'Move Elements' ),
                                  array( 'value' => 'elements_inspect',       'label' => 'Inspect Elements' ),
                                  array( 'value' => 'apply_presets',  'label' => 'Apply Presets' ),
                                  array( 'value' => 'save_presets',   'label' => 'Save Presets' ),
                                  array( 'value' => 'edit_custom_css',        'label' => 'Edit Custom CSS' ),
                                  array( 'value' => 'edit_custom_js',         'label' => 'Edit Custom JS' ),
                                  array( 'value' => 'skeleton_mode',          'label' => 'Skeleton Mode' ),
                                  array( 'value' => 'text_only',              'label' => 'Text Only' ),
                                )
                              )
                            ), true );?>
                          </td>
                        </tr>
                        <tr>
                          <td class="name">Colors</td>
                          <td class="enabled">
                            <label class="tco-rc tco-checkbox" for="<?php echo "$role-manager-enabled-colors";?>">
                              <span class="tco-form-control-indicator-label">Enable</span>
                              <input <?php checked( 'administrator' === $role ); ?> id="<?php echo "$role-manager-enabled-colors";?>" name="hide_access_path" value="enabled" class="tco-form-control tco-form-control-max" type="checkbox" data-cs-control="checkbox">
                              <span class="tco-form-control-indicator"></span>
                            </label>
                          </td>
                          <td class="permissions">
                              <span class="empty">Enable Access before setting additional permissions.</span>
                            <?php echo CS()->view( 'admin/forms/multi-select', false, array(
                              'type' => 'multi-select',
                              'name' => "$role-manager-permissions-colors",
                              'value' => 'administratoraaaaa' !== $role ? 'create,change,rename,delete' : '',
                              'options' => array(
                                'placeholder' => __( 'Click to set additional permissions.', 'cornerstone' ),
                                'choices' => array(
                                  array( 'value' => 'create', 'label' => 'Create' ),
                                  array( 'value' => 'change', 'label' => 'Change' ),
                                  array( 'value' => 'rename', 'label' => 'Rename' ),
                                  array( 'value' => 'delete', 'label' => 'Delete' ),
                                )
                              )
                            ), true );?>
                          </td>
                        </tr>
                        <tr>
                          <td class="name">Fonts</td>
                          <td class="enabled">
                            <label class="tco-rc tco-checkbox" for="<?php echo "$role-manager-enabled-fonts";?>">
                              <span class="tco-form-control-indicator-label">Enable</span>
                              <input <?php checked( 'administrator' === $role ); ?> id="<?php echo "$role-manager-enabled-fonts";?>" name="hide_access_path" value="enabled" class="tco-form-control tco-form-control-max" type="checkbox" data-cs-control="checkbox">
                              <span class="tco-form-control-indicator"></span>
                            </label>
                          </td>
                          <td class="permissions">
                              <span class="empty">Enable Access before setting additional permissions.</span>
                            <?php echo CS()->view( 'admin/forms/multi-select', false, array(
                              'type' => 'multi-select',
                              'name' => "$role-manager-permissions-fonts",
                              'value' => 'administratoraaaaa' !== $role ? 'create,change,rename,delete' : '',
                              'options' => array(
                                'placeholder' => __( 'Click to set additional permissions.', 'cornerstone' ),
                                'choices' => array(
                                  array( 'value' => 'create', 'label' => 'Create' ),
                                  array( 'value' => 'change', 'label' => 'Change' ),
                                  array( 'value' => 'rename', 'label' => 'Rename' ),
                                  array( 'value' => 'delete', 'label' => 'Delete' ),
                                )
                              )
                            ), true ); ?>
                          </td>
                        </tr>
                        <tr>
                          <td class="name">Theme Options</td>
                          <td class="enabled">
                            <label class="tco-rc tco-checkbox" for="<?php echo "$role-theme-options-enabled";?>">
                              <span class="tco-form-control-indicator-label">Enable</span>
                              <input <?php checked( 'administrator' === $role ); ?> id="<?php echo "$role-theme-options-enabled";?>" name="hide_access_path" value="enabled" class="tco-form-control tco-form-control-max" type="checkbox" data-cs-control="checkbox">
                              <span class="tco-form-control-indicator"></span>
                            </label>
                          </td>
                          <td class="permissions">
                              <span class="empty">Enable Access before setting additional permissions.</span>
                            <?php echo CS()->view( 'admin/forms/multi-select', false, array(
                              'type' => 'multi-select',
                              'name' => "$role-theme-options-permissions",
                              'value' => 'administratoraaaaa' !== $role ? 'edit_custom_css,edit_custom_js' : '',
                              'options' => array(
                                'placeholder' => __( 'Click to set additional permissions.', 'cornerstone' ),
                                'choices' => array(
                                  array( 'value' => 'edit_custom_css', 'label' => 'Edit Custom CSS' ),
                                  array( 'value' => 'edit_custom_js', 'label' => 'Edit Custom JS' ),
                                )
                              )
                            ), true ); ?>
                          </td>
                        </tr>
                        <tr>
                          <td class="name">Advanced Mode</td>
                          <td class="enabled" colspan=2 >
                            <?php echo CS()->view( 'admin/forms/select', false, array(
                              'type' => 'select',
                              'name' => "$role-advanced-mode",
                              'value' => 'user',
                              'options' => array(
                                'choices' => array(
                                  'user' => 'User Preference',
                                  'on' => 'Always On',
                                  'off' => 'Always Off',
                                )
                              )
                            ), true ); ?>
                          </td>
                        </tr>
                        <tr>
                          <td class="name">WordPress Toolbar</td>
                          <td class="enabled" colspan=2 >
                            <?php echo CS()->view( 'admin/forms/select', false, array(
                              'type' => 'select',
                              'name' => "$role-show-wordpress-toolbar",
                              'value' => 'user',
                              'options' => array(
                                'choices' => array(
                                  'user' => 'User Preference',
                                  'on' => 'On',
                                  'off' => 'Off',
                                )
                              )
                            ), true ); ?>
                          </td>
                        </tr>
                        <tr>
                          <td class="name">Help Text</td>
                          <td class="enabled" colspan=2 >
                            <?php echo CS()->view( 'admin/forms/select', false, array(
                              'type' => 'select',
                              'name' => "$role-show-help-text",
                              'value' => 'user',
                              'options' => array(
                                'choices' => array(
                                  'user' => 'User Preference',
                                  'on' => 'On',
                                  'off' => 'Off',
                                )
                              )
                            ), true ); ?>
                          </td>
                        </tr>
                        <tr>
                          <td class="name">Responsive Switcher</td>
                          <td class="enabled" colspan=2 >
                            <?php echo CS()->view( 'admin/forms/select', false, array(
                              'type' => 'select',
                              'name' => "$role-responsive-switcher",
                              'value' => 'user',
                              'options' => array(
                                'choices' => array(
                                  'user' => 'User Preference',
                                  'on' => 'Available',
                                  'off' => 'Disabled',
                                )
                              )
                            ), true ); ?>
                          </td>
                        </tr>
                        <tr>
                          <td class="name">Workspace Collapse</td>
                          <td class="enabled" colspan=2 >
                            <?php echo CS()->view( 'admin/forms/select', false, array(
                              'type' => 'select',
                              'name' => "$role-workspace-collapse",
                              'value' => 'user',
                              'options' => array(
                                'choices' => array(
                                  'user' => 'User Preference',
                                  'on' => 'Available',
                                  'off' => 'Disabled',
                                )
                              )
                            ), true ); ?>
                          </td>
                        </tr>
                        <tr>
                          <td class="name">Cheatsheet</td>
                          <td class="access" colspan=2 >
                            <?php echo CS()->view( 'admin/forms/select', false, array(
                              'type' => 'select',
                              'name' => "$role-cheatsheet",
                              'value' => 'user',
                              'options' => array(
                                'choices' => array(
                                  'user' => 'User Preference',
                                  'on' => 'Available',
                                  'off' => 'Disabled',
                                )
                              )
                            ), true ); ?>
                          </td>
                        </tr>
                        <tr>
                          <td class="name">Content Elements</td>
                          <td class="enabled" colspan=2 >
                            <?php echo CS()->view( 'admin/forms/select', false, array(
                              'type' => 'select',
                              'name' => "$role-content-builder-elements",
                              'value' => 'user',
                              'options' => array(
                                'choices' => array(
                                  'user'    => 'User Preference',
                                  'all'     => __( 'All Elements', 'cornerstone' ),
                                  'classic' => __( 'Elements with standard controls (Classic)', 'cornerstone' ),
                                  'v2'      => __( 'Elements with advanced controls (V2)', 'cornerstone' ),
                                )
                              )
                            ), true ); ?>
                          </td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                </div>
                <?php endforeach; ?>
              </div>
            </div>
          </div>
        </div>
        <div class="tco-row">
          <div class="tco-column">
            <div class="tco-box">
              <header class="tco-box-header">
                <h2 class="tco-box-title"><?php e_csi18n('admin.dashboard-settings-system-title'); ?></h2>
              </header>
              <div class="tco-box-content tco-pan">
                <div class="tco-form-setting" data-tco-module="cs-clear-style-cache">
                  <div class="tco-form-setting-info">
                    <label for="cs-control-custom_app_slug">
                      <strong><?php e_csi18n('admin.dashboard-settings-system-clear-style-cache-title'); ?></strong>
                      <span><?php e_csi18n('admin.dashboard-settings-system-clear-style-cache-description'); ?></span>
                    </label>
                  </div>
                  <div class="tco-form-setting-control">
                    <button data-tco-module-target="button" class="tco-btn"><?php e_csi18n('admin.dashboard-settings-system-clear-style-cache-button'); ?></button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <?php $this->view( 'admin/settings-sidebar' ); ?>

    </form>

  </div>
</div>
