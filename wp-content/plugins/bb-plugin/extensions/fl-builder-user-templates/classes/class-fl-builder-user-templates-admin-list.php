<?php

/**
 * Logic for the user templates admin list table.
 *
 * @since 1.10
 */
final class FLBuilderUserTemplatesAdminList {

	/**
	 * Initialize hooks.
	 *
	 * @since 1.10
	 * @return void
	 */
	static public function init() {
		/* Actions */
		add_action( 'plugins_loaded', __CLASS__ . '::redirect' );
		add_action( 'wp', __CLASS__ . '::page_heading' );
		add_action( 'pre_get_posts', __CLASS__ . '::pre_get_posts' );
		add_action( 'admin_enqueue_scripts', __CLASS__ . '::admin_enqueue_scripts' );
		add_action( 'manage_fl-builder-template_posts_custom_column', __CLASS__ . '::add_column_content', 10, 2 );

		/* Filters */
		add_filter( 'views_edit-fl-builder-template', __CLASS__ . '::modify_views' );
		add_filter( 'manage_fl-builder-template_posts_columns', __CLASS__ . '::add_column_headings' );
		add_filter( 'post_row_actions', __CLASS__ . '::row_actions' );
		add_action( 'restrict_manage_posts', __CLASS__ . '::restrict_listings' );
		add_filter( 'manage_edit-fl-builder-template_sortable_columns', __CLASS__ . '::add_sortable_columns' );
		/**
		 * Remove "view" as it is not public and lands on a 404
		 */
		add_filter( 'fl-builder-template-category_row_actions', function ( $actions, $tag ) {
			unset( $actions['view'] );
			return $actions;
		}, 10, 2 );
	}

	/**
	 * Enqueue scripts and styles for user templates.
	 *
	 * @since 1.10
	 * @return void
	 */
	static public function admin_enqueue_scripts() {
		global $pagenow;

		$screen  = get_current_screen();
		$slug    = 'fl-builder-user-templates-admin-';
		$url     = FL_BUILDER_USER_TEMPLATES_URL;
		$version = FL_BUILDER_VERSION;
		$js_url  = FLBuilder::plugin_url() . 'js/';

		if ( 'edit.php' == $pagenow && 'fl-builder-template' == $screen->post_type ) {

			wp_enqueue_style( 'fl-builder-css' );
			wp_enqueue_style( $slug . 'list', $url . 'css/' . $slug . 'list.css', array(), $version );
			wp_enqueue_script( $slug . 'list', $url . 'js/' . $slug . 'list.js', array(), $version );
			wp_enqueue_script( 'clipboard', $js_url . 'libs/clipboard.min.js', array(), $version );

			wp_localize_script( $slug . 'list', 'FLBuilderConfig', array(
				'userTemplateType' => isset( $_GET['fl-builder-template-type'] ) ? sanitize_key( $_GET['fl-builder-template-type'] ) : 'layout',
				'addNewURL'        => admin_url( '/edit.php?post_type=fl-builder-template&page=fl-builder-add-new' ),
			) );
		}
	}

	/**
	 * Redirects the list table to show layout templates if no
	 * template type is set. We never want to show all templates
	 * (layouts, rows, modules) in a list table together.
	 *
	 * @since 1.10
	 * @return void
	 */
	static public function redirect() {
		global $pagenow;

		$post_type     = isset( $_GET['post_type'] ) ? $_GET['post_type'] : null;
		$template_type = isset( $_GET['fl-builder-template-type'] ) ? $_GET['fl-builder-template-type'] : null;
		$page          = isset( $_GET['page'] ) ? $_GET['page'] : null;
		$template_cat  = isset( $_GET['fl-builder-template-category'] ) ? sanitize_key( $_GET['fl-builder-template-category'] ) : 0;

		if ( 'edit.php' == $pagenow && 'fl-builder-template' == $post_type && ! $template_type && ! $page ) {

			$url = admin_url( '/edit.php?post_type=fl-builder-template&fl-builder-template-type=layout&fl-builder-template-category=' . $template_cat );

			wp_redirect( $url );

			exit;
		}
	}

	/**
	 * Overrides the list table page headings for saved rows, cols and modules.
	 *
	 * @since 1.10
	 * @return void
	 */
	static public function page_heading() {
		global $pagenow;
		global $wp_post_types;

		if ( ! is_admin() ) {
			return;
		}

		$screen = get_current_screen();

		if ( 'edit.php' == $pagenow && isset( $_GET['fl-builder-template-type'] ) ) {

			if ( 'row' == $_GET['fl-builder-template-type'] ) {
				$wp_post_types['fl-builder-template']->labels->name = __( 'Saved Rows', 'fl-builder' );
			} elseif ( 'column' == $_GET['fl-builder-template-type'] ) {
				$wp_post_types['fl-builder-template']->labels->name      = __( 'Saved Columns', 'fl-builder' );
				$wp_post_types['fl-builder-template']->cap->create_posts = false;
			} elseif ( 'module' == $_GET['fl-builder-template-type'] ) {
				$wp_post_types['fl-builder-template']->labels->name = __( 'Saved Modules', 'fl-builder' );
			}
		}
	}

	/**
	 * Orders templates by title.
	 *
	 * @since 2.0.6
	 * @param object $query
	 * @return void
	 */
	static public function pre_get_posts( $query ) {
		if ( ! isset( $_GET['post_type'] ) || 'fl-builder-template' != $_GET['post_type'] ) {
			return;
		} elseif ( $query->is_main_query() && ! $query->get( 'orderby' ) ) {
			$query->set( 'orderby', 'title' );
			$query->set( 'order', 'ASC' );
		}
	}

	/**
	 * Modifies the views links to remove the counts since they
	 * are not correct for our list table approach.
	 *
	 * @since 1.10
	 * @param array $views
	 * @return array
	 */
	static public function modify_views( $views ) {
		$slug = 'fl-builder-template';
		$type = isset( $_GET['fl-builder-template-type'] ) ? sanitize_key( $_GET['fl-builder-template-type'] ) : 'layout';

		foreach ( $views as $key => $view ) {

			if ( strstr( $view, $slug ) ) {
				$view          = str_replace( $slug, $slug . '&#038;fl-builder-template-type=' . $type, $view );
				$view          = preg_replace( '/<span(.*)span>/', '', $view );
				$views[ $key ] = $view;
			}
		}

		return $views;
	}

	/**
	 * Adds the custom list table column headings.
	 *
	 * @since 1.10
	 * @param array $columns
	 * @return array
	 */
	static public function add_column_headings( $columns ) {
		if ( ! isset( $_GET['fl-builder-template-type'] ) ) {
			return;
		}

		if ( in_array( $_GET['fl-builder-template-type'], array( 'row', 'column', 'module' ) ) ) {
			$columns['fl-builder-template-title'] = __( 'Title', 'fl-builder' );
			unset( $columns['title'] );

			$columns['taxonomy-fl-builder-template-category'] = __( 'Categories', 'fl-builder' );
			$columns['code']                                  = __( 'ShortCode', 'fl-builder' );
			$columns['screenshot']                            = __( 'Screenshot', 'fl-builder' );
			$columns['notes']                                 = __( 'Notes', 'fl-builder' );

			return [
				'cb'                        => $columns['cb'],
				'fl-builder-template-title' => $columns['fl-builder-template-title'],
				'category'                  => $columns['taxonomy-fl-builder-template-category'],
				'code'                      => $columns['code'],
				'screenshot'                => $columns['screenshot'],
				'notes'                     => $columns['notes'],
			];

		}

		if ( 'layout' === $_GET['fl-builder-template-type'] ) {
			$columns['code'] = __( 'ShortCode', 'fl-builder' );
		}

		$columns['taxonomy-fl-builder-template-category'] = __( 'Categories', 'fl-builder' );

		if ( isset( $_GET['post_status'] ) && 'trash' === $_GET['post_status'] ) {
			unset( $columns['code'] );
		}

		unset( $columns['date'] );

		return $columns;
	}

	/**
	 * Adds the custom list table column content.
	 *
	 * @since 1.10
	 * @param array $columns
	 * @return array
	 */
	static public function add_column_content( $column, $post_id ) {
		global $post;

		switch ( $column ) {
			case 'fl-builder-template-title':
				$badge       = '';
				$post_title  = get_the_title( $post_id );
				$link        = get_edit_post_link( $post_id );
				$title_attr  = the_title_attribute( [
					'echo' => false,
					'post' => get_post( $post_id ),
				] );
				$aria_label  = $post_title . ' ( ' . __( 'Edit', 'fl-builder' ) . ')';
				$post        = get_post( $post_id );
				$post_status = _post_states( $post, false );

				if ( FLBuilderModel::is_post_global_node_template( $post_id ) ) {
					$badgelabel = __( 'Global', 'fl-builder' );
					$badgeclass = 'fl-global-template-message-label';
					$badge      = '<span class="' . $badgeclass . '">' . $badgelabel . '</span>';

					if ( FLBuilderModel::is_post_dynamic_editing_node_template( $post->ID ) ) {
						$badgelabel = __( 'Component', 'fl-builder' );
						$badgeclass = 'fl-dynamic-template-message-label';
						$badge      = '<span class="' . $badgeclass . '">' . $badgelabel . '</span>';
					}
				}

				printf( '<strong><a href="%s" class="row-title" title="%s" aria-label="%s">%s</a>%s%s</strong>',
					$link,
					$title_attr,
					$aria_label,
					$post_title,
					$post_status,
					$badge
				);

				get_inline_data( $post );

				break;
			case 'code':
				$shortcode = sprintf( '[fl_builder_insert_layout id=%s]', $post_id );
				printf( '<pre class="shortcode" data-clipboard-text="%s">%s</pre>', $shortcode, $shortcode );
				break;
			case 'notes':
				echo $post->post_excerpt;
				break;
			case 'screenshot':
				echo get_the_post_thumbnail( $post_id, 'thumbnail', array( 'class' => 'center' ) );
				break;
			case 'category':
				$terms = get_the_terms( $post_id, 'fl-builder-template-category' );
				$links = [];
				foreach ( (array) $terms as $term ) {
					if ( isset( $term->term_id ) ) {
						$args    = array(
							'post_type'                    => 'fl-builder-template',
							'fl-builder-template-category' => $term->slug,
							'fl-builder-template-type'     => $_GET['fl-builder-template-type'],
						);
						$edit    = add_query_arg( $args, admin_url( 'edit.php' ) );
						$links[] = sprintf( '<a href="%s">%s</a>', $edit, $term->name );
					}
				}
				echo join( __( ', ', 'fl-builder' ), $links );
				break;
		}
	}

	/**
	 * Removes the quick edit link as we don't need it.
	 *
	 * @since 1.10
	 * @param array $actions
	 * @return array
	 */
	static public function row_actions( $actions = array() ) {
		if ( isset( $_GET['post_type'] ) && 'fl-builder-template' == $_GET['post_type'] ) {
			unset( $actions['inline hide-if-no-js'] );
		}

		return $actions;
	}

	/**
	 * Add filter dropdown for Categories
	 *
	 * @since 1.10.8
	 */
	static public function restrict_listings() {
		global $typenow;
		if ( 'fl-builder-template' == $typenow ) {
			$taxonomy = 'fl-builder-template-category';
			$tax      = get_taxonomy( $taxonomy );
			$term     = empty( $_GET['fl-builder-template-category'] ) ? 0 : $_GET['fl-builder-template-category'];
			wp_dropdown_categories(
				array(
					'show_option_all' => __( 'Show All Categories', 'fl-builder' ),
					'taxonomy'        => $taxonomy,
					'value_field'     => 'slug',
					'orderby'         => 'name',
					'selected'        => $term,
					'name'            => $taxonomy,
					'depth'           => 1,
					'show_count'      => false,
					'hide_empty'      => false,
				)
			);
		}
	}

	/**
	 * Make Title column sortable for Module, Column and Row Templates
	 *
	 * @since 2.10
	 */
	static public function add_sortable_columns( $columns ) {

		if ( isset( $_GET['fl-builder-template-type'] ) && in_array( $_GET['fl-builder-template-type'], [ 'row', 'column', 'module' ] ) ) {
			$columns['fl-builder-template-title'] = 'fl-builder-template-title';
		}

		return $columns;
	}
}

FLBuilderUserTemplatesAdminList::init();
