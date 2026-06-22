<?php

$mailster_capabilities = array(
	// Campaign Management Capabilities
	'read_newsletter'                     => array(
		'title' => esc_html__( 'Read Campaign', 'mailster' ),
		'roles' => array( 'contributor', 'author', 'editor' ),
		'help'  => esc_html__( 'Allows viewing of campaign content and details', 'mailster' ),
	),

	'edit_newsletters'                    => array(
		'title' => esc_html__( 'Edit Campaigns', 'mailster' ),
		'roles' => array( 'contributor', 'author', 'editor' ),
		'help'  => esc_html__( 'Allows creating and editing new campaigns', 'mailster' ),
	),

	'edit_newsletter'                     => array(
		'title' => esc_html__( 'Edit Campaign', 'mailster' ),
		'roles' => array( 'contributor', 'author', 'editor' ),
		'help'  => esc_html__( 'Allows editing of existing campaigns', 'mailster' ),
	),

	'edit_others_newsletters'             => array(
		'title' => esc_html__( 'Edit Others Campaigns', 'mailster' ),
		'roles' => array( 'editor' ),
		'help'  => esc_html__( 'Allows editing of campaigns created by other users', 'mailster' ),
	),

	'edit_private_newsletters'            => array(
		'title' => esc_html__( 'Edit Private Campaigns', 'mailster' ),
		'roles' => array( 'editor' ),
		'help'  => esc_html__( 'Allows editing of private/draft campaigns', 'mailster' ),
	),

	'edit_published_newsletters'          => array(
		'title' => esc_html__( 'Edit Published Campaigns', 'mailster' ),
		'roles' => array( 'editor' ),
		'help'  => esc_html__( 'Allows editing of published campaigns', 'mailster' ),
	),

	'publish_newsletters'                 => array(
		'title' => esc_html__( 'Send Campaigns', 'mailster' ),
		'roles' => array( 'author', 'editor' ),
		'help'  => esc_html__( 'Allows sending campaigns to subscribers', 'mailster' ),
	),

	'read_private_newsletters'            => array(
		'title' => esc_html__( 'Read Private Campaigns', 'mailster' ),
		'roles' => array( 'author', 'editor' ),
		'help'  => esc_html__( 'Allows viewing of private/draft campaigns', 'mailster' ),
	),

	'delete_newsletter'                   => array(
		'title' => esc_html__( 'Delete Campaign', 'mailster' ),
		'roles' => array( 'contributor', 'author', 'editor' ),
		'help'  => esc_html__( 'Allows deletion of a campaign', 'mailster' ),
	),

	'delete_newsletters'                  => array(
		'title' => esc_html__( 'Delete Campaigns', 'mailster' ),
		'roles' => array( 'contributor', 'author', 'editor' ),
		'help'  => esc_html__( 'Allows deletion of campaigns', 'mailster' ),
	),

	'delete_private_newsletters'          => array(
		'title' => esc_html__( 'Delete Private Campaigns', 'mailster' ),
		'roles' => array( 'contributor', 'author', 'editor' ),
		'help'  => esc_html__( 'Allows deletion of private/draft campaigns', 'mailster' ),
	),

	'delete_published_newsletters'        => array(
		'title' => esc_html__( 'Delete Published Campaigns', 'mailster' ),
		'roles' => array( 'contributor', 'author', 'editor' ),
		'help'  => esc_html__( 'Allows deletion of published campaigns', 'mailster' ),
	),

	'delete_others_newsletters'           => array(
		'title' => esc_html__( 'Delete Others Campaigns', 'mailster' ),
		'roles' => array( 'editor' ),
		'help'  => esc_html__( 'Allows deletion of campaigns created by other users', 'mailster' ),
	),

	'duplicate_newsletters'               => array(
		'title' => esc_html__( 'Duplicate Campaigns', 'mailster' ),
		'roles' => array( 'author', 'editor' ),
		'help'  => esc_html__( 'Allows creating copies of existing campaigns', 'mailster' ),
	),

	'duplicate_others_newsletters'        => array(
		'title' => esc_html__( 'Duplicate Others Campaigns', 'mailster' ),
		'roles' => array( 'editor' ),
		'help'  => esc_html__( 'Allows creating copies of campaigns created by other users', 'mailster' ),
	),

	// Autoresponder Management
	'mailster_edit_autoresponders'        => array(
		'title' => esc_html__( 'Edit Autoresponders', 'mailster' ),
		'roles' => array( 'editor' ),
		'help'  => esc_html__( 'Allows creating and editing automated email sequences', 'mailster' ),
	),

	'mailster_edit_others_autoresponders' => array(
		'title' => esc_html__( 'Edit Others Autoresponders', 'mailster' ),
		'roles' => array( 'editor' ),
		'help'  => esc_html__( 'Allows editing of autoresponders created by other users', 'mailster' ),
	),

	// Workflow Management
	'read_mailster-workflow'              => array(
		'title' => esc_html__( 'View Workflow', 'mailster' ),
		'roles' => array( 'contributor', 'author', 'editor' ),
		'help'  => esc_html__( 'Allows viewing of workflow configurations', 'mailster' ),
	),

	'edit_mailster-workflows'             => array(
		'title' => esc_html__( 'Edit Workflows', 'mailster' ),
		'roles' => array( 'contributor', 'author', 'editor' ),
		'help'  => esc_html__( 'Allows creating and editing workflow configurations', 'mailster' ),
	),

	'edit_mailster-workflow'              => array(
		'title' => esc_html__( 'Edit Workflow', 'mailster' ),
		'roles' => array( 'contributor', 'author', 'editor' ),
		'help'  => esc_html__( 'Allows editing of existing workflow configurations', 'mailster' ),
	),

	'delete_mailster-workflow'            => array(
		'title' => esc_html__( 'Delete Workflows', 'mailster' ),
		'roles' => array( 'contributor', 'author', 'editor' ),
		'help'  => esc_html__( 'Allows deletion of workflow configurations', 'mailster' ),
	),

	'edit_others_mailster-workflows'      => array(
		'title' => esc_html__( 'Edit Others Workflows', 'mailster' ),
		'roles' => array( 'editor' ),
		'help'  => esc_html__( 'Allows editing of workflows created by other users', 'mailster' ),
	),

	'publish_mailster-workflows'          => array(
		'title' => esc_html__( 'Activate Workflows', 'mailster' ),
		'roles' => array( 'author', 'editor' ),
		'help'  => esc_html__( 'Allows activating and deactivating workflow configurations', 'mailster' ),
	),

	'read_private_mailster-workflows'     => array(
		'title' => esc_html__( 'View Inactive Workflows', 'mailster' ),
		'roles' => array( 'author', 'editor' ),
		'help'  => esc_html__( 'Allows viewing of inactive workflow configurations', 'mailster' ),
	),

	'delete_others_mailster-workflows'    => array(
		'title' => esc_html__( 'Delete Others Workflows', 'mailster' ),
		'roles' => array( 'editor' ),
		'help'  => esc_html__( 'Allows deletion of workflows created by other users', 'mailster' ),
	),

	'duplicate_mailster-workflows'        => array(
		'title' => esc_html__( 'Duplicate Workflows', 'mailster' ),
		'roles' => array( 'author', 'editor' ),
		'help'  => esc_html__( 'Allows creating copies of existing workflow configurations', 'mailster' ),
	),

	'duplicate_others_mailster-workflows' => array(
		'title' => esc_html__( 'Duplicate Others Workflows', 'mailster' ),
		'roles' => array( 'editor' ),
		'help'  => esc_html__( 'Allows creating copies of workflows created by other users', 'mailster' ),
	),

	// Form Management
	'read_mailster-form'                  => array(
		'title' => esc_html__( 'View Form', 'mailster' ),
		'roles' => array( 'contributor', 'author', 'editor' ),
		'help'  => esc_html__( 'Allows viewing of form configurations', 'mailster' ),
	),

	'edit_mailster-forms'                 => array(
		'title' => esc_html__( 'Edit Forms', 'mailster' ),
		'roles' => array( 'contributor', 'author', 'editor' ),
		'help'  => esc_html__( 'Allows creating and editing form configurations', 'mailster' ),
	),

	'edit_mailster-form'                  => array(
		'title' => esc_html__( 'Edit Form', 'mailster' ),
		'roles' => array( 'contributor', 'author', 'editor' ),
		'help'  => esc_html__( 'Allows editing of existing form configurations', 'mailster' ),
	),

	'delete_mailster-form'                => array(
		'title' => esc_html__( 'Delete Forms', 'mailster' ),
		'roles' => array( 'contributor', 'author', 'editor' ),
		'help'  => esc_html__( 'Allows deletion of form configurations', 'mailster' ),
	),

	'edit_others_mailster-forms'          => array(
		'title' => esc_html__( 'Edit Others Forms', 'mailster' ),
		'roles' => array( 'editor' ),
		'help'  => esc_html__( 'Allows editing of forms created by other users', 'mailster' ),
	),

	'publish_mailster-forms'              => array(
		'title' => esc_html__( 'Activate Forms', 'mailster' ),
		'roles' => array( 'author', 'editor' ),
		'help'  => esc_html__( 'Allows activating and deactivating form configurations', 'mailster' ),
	),

	'read_private_mailster-forms'         => array(
		'title' => esc_html__( 'View Private Forms', 'mailster' ),
		'roles' => array( 'author', 'editor' ),
		'help'  => esc_html__( 'Allows viewing of private form configurations', 'mailster' ),
	),

	'delete_others_mailster-forms'        => array(
		'title' => esc_html__( 'Delete Others Forms', 'mailster' ),
		'roles' => array( 'editor' ),
		'help'  => esc_html__( 'Allows deletion of forms created by other users', 'mailster' ),
	),

	'duplicate_mailster-forms'            => array(
		'title' => esc_html__( 'Duplicate Forms', 'mailster' ),
		'roles' => array( 'author', 'editor' ),
		'help'  => esc_html__( 'Allows creating copies of existing form configurations', 'mailster' ),
	),

	'duplicate_others_mailster-forms'     => array(
		'title' => esc_html__( 'Duplicate Others Forms', 'mailster' ),
		'roles' => array( 'editor' ),
		'help'  => esc_html__( 'Allows creating copies of forms created by other users', 'mailster' ),
	),

	// Template Management
	'mailster_change_template'            => array(
		'title' => esc_html__( 'Change Template', 'mailster' ),
		'roles' => array( 'editor' ),
		'help'  => esc_html__( 'Allows changing email template designs', 'mailster' ),
	),
	'mailster_save_template'              => array(
		'title' => esc_html__( 'Save Template', 'mailster' ),
		'roles' => array( 'editor' ),
		'help'  => esc_html__( 'Allows saving custom email template designs', 'mailster' ),
	),

	'mailster_see_codeview'               => array(
		'title' => esc_html__( 'See Codeview', 'mailster' ),
		'roles' => array( 'editor' ),
		'help'  => esc_html__( 'Allows viewing and editing HTML code of email templates', 'mailster' ),
	),

	'mailster_change_plaintext'           => array(
		'title' => esc_html__( 'Change Text Version', 'mailster' ),
		'roles' => array( 'editor' ),
		'help'  => esc_html__( 'Allows modifying plain text versions of emails', 'mailster' ),
	),

	// Subscriber Management
	'mailster_edit_subscribers'           => array(
		'title' => esc_html__( 'Edit Subscribers', 'mailster' ),
		'roles' => array( 'editor' ),
		'help'  => esc_html__( 'Allows editing subscriber information', 'mailster' ),
	),

	'mailster_add_subscribers'            => array(
		'title' => esc_html__( 'Add Subscribers', 'mailster' ),
		'roles' => array( 'editor' ),
		'help'  => esc_html__( 'Allows adding new subscribers to the system', 'mailster' ),
	),

	'mailster_delete_subscribers'         => array(
		'title' => esc_html__( 'Delete Subscribers', 'mailster' ),
		'roles' => array( 'editor' ),
		'help'  => esc_html__( 'Allows removing subscribers from the system', 'mailster' ),
	),

	'mailster_restore_subscribers'        => array(
		'title' => esc_html__( 'Restore Subscribers', 'mailster' ),
		'roles' => array( 'editor' ),
		'help'  => esc_html__( 'Allows restoring previously deleted subscribers', 'mailster' ),
	),

	'mailster_manage_subscribers'         => array(
		'title' => esc_html__( 'Manage Subscribers', 'mailster' ),
		'roles' => array( 'editor' ),
		'help'  => esc_html__( 'Allows comprehensive subscriber management', 'mailster' ),
	),

	'mailster_import_subscribers'         => array(
		'title' => esc_html__( 'Import Subscribers', 'mailster' ),
		'roles' => array( 'editor' ),
		'help'  => esc_html__( 'Allows importing subscribers from external sources', 'mailster' ),
	),

	'mailster_import_wordpress_users'     => array(
		'title' => esc_html__( 'Import WordPress Users', 'mailster' ),
		'roles' => array( 'editor' ),
		'help'  => esc_html__( 'Allows importing WordPress users as subscribers', 'mailster' ),
	),

	'mailster_export_subscribers'         => array(
		'title' => esc_html__( 'Export Subscribers', 'mailster' ),
		'roles' => array( 'editor' ),
		'help'  => esc_html__( 'Allows exporting subscriber data to external files', 'mailster' ),
	),

	'mailster_bulk_delete_subscribers'    => array(
		'title' => esc_html__( 'Bulk Delete Subscribers', 'mailster' ),
		'roles' => array( 'editor' ),
		'help'  => esc_html__( 'Allows mass deletion of multiple subscribers', 'mailster' ),
	),

	// List Management
	'mailster_manage_lists'               => array(
		'title' => esc_html__( 'Manage Lists', 'mailster' ),
		'roles' => array( 'editor' ),
		'help'  => esc_html__( 'Allows comprehensive subscriber list management', 'mailster' ),
	),

	'mailster_add_lists'                  => array(
		'title' => esc_html__( 'Add Lists', 'mailster' ),
		'roles' => array( 'editor' ),
		'help'  => esc_html__( 'Allows creating new subscriber lists', 'mailster' ),
	),

	'mailster_edit_lists'                 => array(
		'title' => esc_html__( 'Edit Lists', 'mailster' ),
		'roles' => array( 'editor' ),
		'help'  => esc_html__( 'Allows modifying existing subscriber lists', 'mailster' ),
	),

	'mailster_delete_lists'               => array(
		'title' => esc_html__( 'Delete Lists', 'mailster' ),
		'roles' => array( 'editor' ),
		'help'  => esc_html__( 'Allows removing subscriber lists', 'mailster' ),
	),

	// Tag Management
	'mailster_add_tags'                   => array(
		'title' => esc_html__( 'Add Tags', 'mailster' ),
		'roles' => array( 'editor' ),
		'help'  => esc_html__( 'Allows creating new subscriber tags', 'mailster' ),
	),

	'mailster_edit_tags'                  => array(
		'title' => esc_html__( 'Edit Tags', 'mailster' ),
		'roles' => array( 'editor' ),
		'help'  => esc_html__( 'Allows modifying existing subscriber tags', 'mailster' ),
	),

	'mailster_delete_tags'                => array(
		'title' => esc_html__( 'Delete Tags', 'mailster' ),
		'roles' => array( 'editor' ),
		'help'  => esc_html__( 'Allows removing subscriber tags', 'mailster' ),
	),

	// System Management
	'mailster_manage_templates'           => array(
		'title' => esc_html__( 'Manage Templates', 'mailster' ),
		'roles' => array( 'editor' ),
		'help'  => esc_html__( 'Allows comprehensive email template management', 'mailster' ),
	),

	'mailster_edit_templates'             => array(
		'title' => esc_html__( 'Edit Templates', 'mailster' ),
		'roles' => array( 'editor' ),
		'help'  => esc_html__( 'Allows modifying email template designs', 'mailster' ),
	),

	'mailster_delete_templates'           => array(
		'title' => esc_html__( 'Delete Templates', 'mailster' ),
		'roles' => array( 'editor' ),
		'help'  => esc_html__( 'Allows removing email templates', 'mailster' ),
	),

	'mailster_upload_templates'           => array(
		'title' => esc_html__( 'Upload Templates', 'mailster' ),
		'roles' => array( 'editor' ),
		'help'  => esc_html__( 'Allows uploading new email templates', 'mailster' ),
	),

	'mailster_update_templates'           => array(
		'title' => esc_html__( 'Update Templates', 'mailster' ),
		'roles' => array(),
		'help'  => esc_html__( 'Allows updating email templates to newer versions', 'mailster' ),
	),

	'mailster_manage_addons'              => array(
		'title' => esc_html__( 'Manage Addons', 'mailster' ),
		'roles' => array( 'editor' ),
		'help'  => esc_html__( 'Allows managing Mailster addon installations', 'mailster' ),
	),

	'mailster_view_logs'                  => array(
		'title' => esc_html__( 'View Logs', 'mailster' ),
		'roles' => array(),
		'help'  => esc_html__( 'Allows viewing system logs and error reports', 'mailster' ),
	),

	'mailster_dashboard'                  => array(
		'title' => esc_html__( 'Access Dashboard', 'mailster' ),
		'roles' => array( 'editor' ),
		'help'  => esc_html__( 'Allows accessing the Mailster dashboard', 'mailster' ),
	),

	'mailster_dashboard_widget'           => array(
		'title' => esc_html__( 'See Dashboard Widget', 'mailster' ),
		'roles' => array( 'editor' ),
		'help'  => esc_html__( 'Allows viewing Mailster widgets in the WordPress dashboard', 'mailster' ),
	),

	'mailster_manage_capabilities'        => array(
		'title' => esc_html__( 'Manage Capabilities', 'mailster' ),
		'roles' => array(),
		'help'  => esc_html__( 'Allows managing user role capabilities and permissions', 'mailster' ),
	),

	'mailster_manage_licenses'            => array(
		'title' => esc_html__( 'Manage Licenses', 'mailster' ),
		'roles' => array(),
		'help'  => esc_html__( 'Allows managing Mailster and addon licenses', 'mailster' ),
	),

	// Legacy Form Management
	'mailster_edit_forms'                 => array(
		'title' => esc_html__( 'Edit Forms', 'mailster' ) . ' (legacy)',
		'roles' => array( 'editor' ),
		'help'  => esc_html__( 'Allows editing of legacy form configurations', 'mailster' ),
	),

	'mailster_add_forms'                  => array(
		'title' => esc_html__( 'Add Forms', 'mailster' ) . ' (legacy)',
		'roles' => array( 'editor' ),
		'help'  => esc_html__( 'Allows creating new legacy form configurations', 'mailster' ),
	),

	'mailster_delete_forms'               => array(
		'title' => esc_html__( 'Delete Forms', 'mailster' ) . ' (legacy)',
		'roles' => array( 'editor' ),
		'help'  => esc_html__( 'Allows removing legacy form configurations', 'mailster' ),
	),
);

$mailster_capabilities = apply_filters( 'mailster_capabilities', $mailster_capabilities );
