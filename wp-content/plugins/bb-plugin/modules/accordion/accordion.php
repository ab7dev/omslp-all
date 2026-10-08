<?php

/**
 * @class FLAccordionModule
 */
class FLAccordionModule extends FLBuilderModule {

	/**
	 * @method __construct
	 */
	public function __construct() {
		parent::__construct(array(
			'name'            => __( 'Accordion', 'fl-builder' ),
			'description'     => __( 'Display a collapsible accordion of items.', 'fl-builder' ),
			'category'        => __( 'Layout', 'fl-builder' ),
			'partial_refresh' => true,
			'icon'            => 'layout.svg',
			'block_editor'    => true,
		));

		$this->add_css( 'font-awesome-5' );
	}

	/**
	 * Ensure backwards compatibility with old settings.
	 *
	 * @since 2.2
	 * @param object $settings A module settings object.
	 * @param object $helper A settings compatibility helper.
	 * @return object
	 */
	public function filter_settings( $settings, $helper ) {
		if ( isset( $settings->border_color ) ) {
			$settings->item_border          = array();
			$settings->item_border['style'] = 'solid';
			$settings->item_border['color'] = $settings->border_color;
			$settings->item_border['width'] = array(
				'top'    => '1',
				'right'  => '1',
				'bottom' => '1',
				'left'   => '1',
			);
			unset( $settings->border_color );
		}

		if ( ! isset( $settings->content_type ) ) {
			$settings->content_type = 'post_content';
		}

		if ( ! isset( $settings->more_link ) ) {
			$settings->more_link = 'hide';
		}

		// exclude current post
		$settings->exclude_self = 'yes';

		return $settings;
	}

	/**
	 * @method get_content
	 */
	public function get_content( $post_id ) {
		if ( FLBuilderModel::is_builder_enabled( $post_id ) ) {
			// Enqueue styles and scripts for the post.
			FLBuilder::enqueue_layout_styles_scripts_by_id( $post_id );
			// Start buffering so we can capture printed content.
			ob_start();
			// Print the styles if we are outside of the head tag.
			if ( did_action( 'wp_enqueue_scripts' ) && ! doing_filter( 'wp_enqueue_scripts' ) ) {
				wp_print_styles();
			}
			// Render the builder content.
			FLBuilder::render_content_by_id( $post_id );
			// Capture the content and clear it.
			return ob_get_clean();
		} else {
			// Return the WP editor content if the builder isn't enabled.
			return apply_filters( 'the_content', get_the_content( null, false, $post_id ) );
		}
	}

	/**
	 * @method get_excerpt
	 */
	public function get_excerpt( $post_id ) {
		add_filter( 'excerpt_length', array( $this, 'set_custom_excerpt_length' ), 9999 );
		add_filter( 'excerpt_more', array( $this, 'set_custom_excerpt_more' ), 9999 );
		$excerpt = '<p>' . get_the_excerpt( $post_id ) . '</p>';
		remove_filter( 'excerpt_more', array( $this, 'set_custom_excerpt_more' ), 9999 );
		remove_filter( 'excerpt_length', array( $this, 'set_custom_excerpt_length' ), 9999 );
		return $excerpt;
	}

	/**
	 * @method get_more_link
	 */
	public function get_more_link( $post_id, $more_link_text = '' ) {
		if ( empty( $more_link_text ) ) {
			return;
		}
		$output[] = '<div><a class="fl-accordion-post-more-link"';
		$output[] = 'href="' . esc_url( get_the_permalink() ) . '"';
		$output[] = 'title="' . the_title_attribute( array( 'echo' => false ) ) . '">';
		$output[] = $more_link_text . '<span class="sr-only"> about ' . the_title_attribute( array( 'echo' => false ) ) . '</span>';
		$output[] = '</a></div>';
		return join( '', $output );
	}

	/**
	 * Set the Post Excerpt
	 *
	 * @since 2.7.1
	 * @return void
	 */
	public function set_custom_excerpt_length( $length ) {
		$excerpt_length = strval( $this->settings->excerpt_length );
		if ( trim( $excerpt_length ) === '' ) {
			return $length;
		}
		return intval( $excerpt_length );
	}

	/**
	 * Set custom 'more' text for the Post Excerpt.
	 *
	 * @since 2.7.1
	 * @return void
	 */
	public function set_custom_excerpt_more( $more ) {
		return $this->settings->excerpt_more_text;
	}

	/**
	 * Checks if the accordion item is opened.
	 *
	 * @since 2.11
	 * @method is_item_opened
	 * @param integer $index The index of the accordion item
	 * @return bool
	 */
	public function is_item_opened( $index ) {
		// Open the first item if the setting is enabled or if the builder is active to preview content styling.
		return 0 === $index && ( '1' === $this->settings->open_first || FLBuilderModel::is_builder_active() );
	}

	/**
	 * Gets the wrapper tag attributes of the accordion tab.
	 *
	 * @since 2.11
	 * @method get_wrapper_attributes
	 * @return string
	 */
	public function get_wrapper_attributes() {
		$attributes = array();
		$classes    = array( 'fl-accordion', 'fl-accordion-' . sanitize_html_class( $this->settings->label_size ) );
		if ( $this->settings->collapse ) {
			$classes[] = 'fl-accordion-collapse';
		} else {
			$attributes['multiselectable'] = 'true';
		}
		$attributes['class'] = join( ' ', $classes );
		return FLBuilderModuleUtils::join_html_attributes( $attributes );
	}

	/**
	 * Gets the wrapping item attributes of the accordion tab.
	 *
	 * @since 2.11
	 * @method get_item_attributes
	 * @param integer $index The index of the accordion item
	 * @return string
	 */
	public function get_item_attributes( $index ) {
		$attributes = array();
		if ( ! empty( $this->settings->id ) ) {
			$attributes['id'] = sanitize_html_class( $this->settings->id ) . '-' . $index;
		}
		$classes = array( 'fl-accordion-item' );
		if ( $this->is_item_opened( $index ) ) {
			$classes[] = 'fl-accordion-item-active';
		}
		$attributes['class'] = join( ' ', $classes );
		return FLBuilderModuleUtils::join_html_attributes( $attributes );
	}

	/**
	 * Gets the heading tag while ensuring backward compatibility for deprecated versions.
	 *
	 * @since 2.11
	 * @method get_heading_tag
	 * @return string
	 */
	public function get_heading_tag() {
		if ( 2 < $this->version ) {
			return $this->settings->label_tag;
		}
		return '';
	}

	/**
	 * Gets the heading attributes of the accordion tab, ensuring backward compatibility for deprecated versions.
	 *
	 * @since 2.11
	 * @method get_heading_attributes
	 * @return string
	 */
	public function get_heading_attributes() {
		if ( 2 < $this->version ) {
			$attributes['class'] = 'fl-accordion-heading';
			if ( false === strpos( $this->settings->label_tag, 'h' ) ) {
				$attributes['role']       = 'heading';
				$attributes['aria-level'] = '2';
			}
			return FLBuilderModuleUtils::join_html_attributes( $attributes );
		}
		return '';
	}

	/**
	 * Gets the button tag while ensuring backward compatibility for deprecated versions.
	 *
	 * @since 2.11
	 * @method get_button_tag
	 * @return string
	 */
	public function get_button_tag() {
		if ( 2 < $this->version ) {
			return 'button';
		}
		return 'div';
	}

	/**
	 * Gets the button attributes of the accordion tab, ensuring backward compatibility for deprecated versions.
	 *
	 * @since 2.11
	 * @method get_button_attributes
	 * @param integer $index The index of the accordion item
	 * @return string
	 */
	public function get_button_attributes( $index ) {
		$classes    = array( 'fl-accordion-button' );
		$attributes = array(
			'id'            => 'fl-accordion-' . $this->node . '-button-' . $index,
			'aria-controls' => 'fl-accordion-' . $this->node . '-content-' . $index,
			'aria-expanded' => $this->is_item_opened( $index ) ? 'true' : 'false',
		);
		if ( 2 < $this->version ) {
			$attributes['type'] = 'button';
			$classes[]          = 'fl-content-ui-button';
		} else {
			$attributes['role']     = 'button';
			$attributes['tabindex'] = '0';
		}
		$attributes['class'] = join( ' ', $classes );
		return FLBuilderModuleUtils::join_html_attributes( $attributes );
	}

	/**
	 * Get the label tag while ensuring backward compatibility for deprecated versions.
	 *
	 * @since 2.11
	 * @method get_label_tag
	 * @return string
	 */
	public function get_label_tag() {
		if ( 3 > $this->version ) {
			return $this->settings->label_tag;
		}
		return 'span';
	}

	/**
	 * Gets the label attributes of the accordion tab, ensuring backward compatibility for deprecated versions.
	 *
	 * @since 2.11
	 * @method get_label_attributes
	 * @return string
	 */
	public function get_label_attributes() {
		$attributes['class'] = 'fl-accordion-button-label';
		if ( 3 > $this->version ) {
			$attributes['role'] = 'none';
		}
		return FLBuilderModuleUtils::join_html_attributes( $attributes );
	}

	/**
	 * Get the icon tag while ensuring backward compatibility for deprecated versions.
	 *
	 * @since 2.11
	 * @method get_icon_tag
	 * @return string
	 */
	public function get_icon_tag() {
		if ( 1 === $this->version ) {
			return 'a';
		} elseif ( 2 === $this->version ) {
			return 'button';
		}
		return 'span';
	}

	/**
	 * Gets the icon attributes of the accordion tab, ensuring backward compatibility for deprecated versions.
	 *
	 * @since 2.11
	 * @method get_icon_attributes
	 * @param integer $index The index of the accordion item
	 * @return string
	 */
	public function get_icon_attributes( $index ) {
		$classes = array( 'fl-accordion-button-icon', 'fl-accordion-button-icon-' . $this->settings->label_icon_position );
		if ( 2 === $this->version ) {
			$classes[] = 'fl-content-ui-button';
		}
		$attributes = array(
			'id'    => 'fl-accordion-' . $this->node . '-icon-' . $index,
			'class' => join( ' ', $classes ),
		);
		if ( 3 > $this->version ) {
			$attributes['role']        = 'none';
			$attributes['tabindex']    = '-1';
			$attributes['aria-hidden'] = 'true';
		}
		return FLBuilderModuleUtils::join_html_attributes( $attributes );
	}

	/**
	 * Gets the icon content for the accordion tab.
	 *
	 * @since 2.11
	 * @method get_icon_content
	 * @param integer $index The index of the accordion item
	 * @return string
	 */
	public function get_icon_content( $index ) {
		$opened       = $this->is_item_opened( $index );
		$label        = FLBuilderModuleUtils::get_icon_classes( $this->settings, 'label_' );
		$active_class = FLBuilderModuleUtils::get_icon_classes( $this->settings, 'label_active_' );
		$current      = $opened ? $active_class : $label;
		$text         = $opened ? __( 'Collapse', 'fl-builder' ) : __( 'Expand', 'fl-builder' );
		return '<i class="fl-accordion-button-icon ' . $current . '" data-label-icon="' . esc_attr( $label ) . '" data-active-icon="' . esc_attr( $active_class ) . '"><span class="sr-only">' . $text . '</span></i>';
	}

	/**
	 * Gets the accordion tab data for the specified index.
	 *
	 * @since 2.11
	 * @method get_tab_data
	 * @param integer $index The index of the accordion item
	 * @return string
	 */
	public function get_tab_data( $index ) {
		$tags     = array(
			'heading' => $this->get_heading_tag(),
			'button'  => $this->get_button_tag(),
			'label'   => $this->get_label_tag(),
			'icon'    => $this->get_icon_tag(),
		);
		$heading  = sprintf( '<%1$s %2$s>', $tags['heading'], $this->get_heading_attributes() );
		$button   = sprintf( '<%1$s %2$s>', $tags['button'], $this->get_button_attributes( $index ) );
		$label    = sprintf( '<%1$s %2$s>', $tags['label'], $this->get_label_attributes( $index ) );
		$icon     = sprintf( '<%1$s %2$s>', $tags['icon'], $this->get_icon_attributes( $index ) );
		$html     = sprintf( '%1$s%2$s</%3$s>', $icon, $this->get_icon_content( $index ), $tags['icon'] );
		$output[] = empty( $tags['heading'] ) ? $button : $heading . $button;
		if ( 'left' === $this->settings->label_icon_position ) {
			$output[] = $html;
		}
		$output[] = $label;
		if ( 'content' === $this->settings->source ) {
			$output[] = wp_kses_post( $this->settings->items[ $index ]->label );
		} elseif ( 'post' === $this->settings->source ) {
			$output[] = get_the_title();
		}
		$output[] = sprintf( '</%1$s>', $tags['label'] );
		if ( 'right' === $this->settings->label_icon_position ) {
			$output[] = $html;
		}
		$output[] = sprintf( '</%1$s>', $tags['button'] );
		if ( ! empty( $tags['heading'] ) ) {
			$output[] = sprintf( '</%s>', $tags['heading'] );
		}
		return join( '', $output );
	}

	/**
	 * Gets the content tag attributes for the accordion item.
	 *
	 * @since 2.11
	 * @method get_content_attributes
	 * @param integer $index The index of the accordion item
	 * @return string
	 */
	public function get_content_attributes( $index ) {
		$attributes = array(
			'id'              => 'fl-accordion-' . $this->node . '-content-' . $index,
			'class'           => 'fl-accordion-content fl-clearfix',
			'role'            => 'region',
			'aria-hidden'     => ( $this->is_item_opened( $index ) ) ? 'false' : 'true',
			'aria-labelledby' => 'fl-accordion-' . $this->node . '-button-' . $index,
		);
		return FLBuilderModuleUtils::join_html_attributes( $attributes );
	}

	/**
	 * Gets the content data for the accordion item.
	 *
	 * @since 2.11
	 * @method get_content_data
	 * @param integer $index The index of the accordion item
	 * @param object $embed The WP embed instance to retrieve content data
	 * @return string
	 */
	public function get_content_data( $index, $embed = null ) {
		if ( 'content' === $this->settings->source ) {
			if ( 'none' === $this->settings->items[ $index ]->saved_layout ) {
				return FLBuilderUtils::wpautop( $embed->autoembed( $this->settings->items[ $index ]->content ), $this );
			} else {
				$post_id = $this->settings->items[ $index ]->{'saved_' . $this->settings->items[ $index ]->saved_layout};
				if ( ! empty( $post_id ) ) {
					return $this->get_content( $post_id );
				}
			}
		} elseif ( 'post' === $this->settings->source ) {
			$post_id = get_the_id();
			if ( ! empty( $this->settings->content_type ) && 'post_content' === $this->settings->content_type ) {
				return $this->get_content( $post_id );
			} else {
				$more_link_text = ( ! empty( $this->settings->more_link ) && 'show' === $this->settings->more_link ) ? $this->settings->more_link_text : '';
				return $this->get_excerpt( $post_id ) . $this->get_more_link( $post_id, $more_link_text );
			}
		}
	}

	/**
	 * Build the whole accordion item structure.
	 *
	 * @since 2.11
	 * @method build_item_structure
	 * @param integer $index The index of the accordion item
	 * @param object $embed The WP embed instance to retrieve content data
	 * @return string
	 */
	public function build_item_structure( $index, $embed = null ) {
		$item     = sprintf( '<div %s>', $this->get_item_attributes( $index ) );
		$content  = sprintf( '<div %s>', $this->get_content_attributes( $index ) );
		$output[] = $item . $this->get_tab_data( $index );
		$output[] = $content . $this->get_content_data( $index, $embed );
		$output[] = '</div></div>';
		return join( '', $output );
	}
}

/**
 * Register the module and its form settings.
 */
FLBuilder::register_module('FLAccordionModule', array(
	'items' => array(
		'title'    => __( 'Items', 'fl-builder' ),
		'sections' => array(
			'general' => array(
				'title'  => '',
				'fields' => array(
					'source' => array(
						'type'    => 'select',
						'label'   => __( 'Content Source', 'fl-builder' ),
						'default' => 'content',
						'options' => array(
							'post'    => __( 'Post', 'fl-builder' ),
							'content' => __( 'Custom Content', 'fl-builder' ),
						),
						'toggle'  => array(
							'post'    => array(
								'sections' => array( 'post' ),
								'fields'   => array( 'content_type', 'more_link', 'more_link_text' ),
							),
							'content' => array(
								'sections' => array( 'content' ),
								'fields'   => array( 'content_typography' ),
							),
						),
					),
				),
			),
			'post'    => array(
				'title' => __( 'Post', 'fl-builder' ),
				'file'  => FL_BUILDER_DIR . 'includes/ui-simple-loop.php',
			),
			'content' => array(
				'title'  => __( 'Custom Content', 'fl-builder' ),
				'fields' => array(
					'items' => array(
						'type'         => 'form',
						'label'        => __( 'Item', 'fl-builder' ),
						'form'         => 'accordion_items_form',
						'preview_text' => 'label',
						'multiple'     => true,
					),
				),
			),
			'display' => array(
				'title'  => __( 'Display', 'fl-builder' ),
				'fields' => array(
					'label_tag'         => array(
						'type'     => 'select',
						'label'    => __( 'Label Tag', 'fl-builder' ),
						'default'  => 'h2',
						'sanitize' => array( 'FLBuilderUtils::esc_tags', 'h2' ),
						'options'  => array(
							'a'    => 'a',
							'h1'   => 'h1',
							'h2'   => 'h2',
							'h3'   => 'h3',
							'h4'   => 'h4',
							'h5'   => 'h5',
							'h6'   => 'h6',
							'div'  => 'div',
							'span' => 'span',
						),
						'preview'  => array(
							'type' => 'refresh',
						),
					),
					'content_type'      => array(
						'type'    => 'select',
						'label'   => __( 'Content Type', 'fl-builder' ),
						'default' => 'post_content',
						'options' => array(
							'post_content' => __( 'Post Content', 'fl-builder' ),
							'post_excerpt' => __( 'Post Excerpt', 'fl-builder' ),
						),
						'toggle'  => array(
							'post_excerpt' => array(
								'fields' => array( 'excerpt_length', 'excerpt_more_text', 'more_link', 'more_link_text' ),
							),
						),
					),
					'excerpt_length'    => array(
						'type'    => 'unit',
						'units'   => array( 'words' ),
						'label'   => __( 'Excerpt Length', 'fl-builder' ),
						'default' => '',
						'slider'  => array(
							'min'  => 0,
							'max'  => 1000,
							'step' => 1,
						),
					),
					'excerpt_more_text' => array(
						'type'    => 'text',
						'label'   => __( 'Excerpt More Text', 'fl-builder' ),
						'default' => __( '...', 'fl-builder' ),
					),
					'more_link'         => array(
						'type'    => 'select',
						'label'   => __( 'More Link', 'fl-builder' ),
						'default' => 'hide',
						'options' => array(
							'show' => __( 'Show', 'fl-builder' ),
							'hide' => __( 'Hide', 'fl-builder' ),
						),
						'toggle'  => array(
							'show' => array(
								'fields' => array( 'more_link_text' ),
							),
						),
					),
					'more_link_text'    => array(
						'type'    => 'text',
						'label'   => __( 'More Link Text', 'fl-builder' ),
						'default' => __( 'Read More', 'fl-builder' ),
					),
					'expand_on_tab'     => array(
						'type'    => 'select',
						'label'   => __( 'Expand on Tab', 'fl-builder' ),
						'default' => '0',
						'options' => array(
							'1' => __( 'Yes', 'fl-builder' ),
							'0' => __( 'No', 'fl-builder' ),
						),
						'help'    => __( 'Expand Accordion using the Tab key.', 'fl-builder' ),
						'preview' => array(
							'type' => 'none',
						),
					),
					'collapse'          => array(
						'type'    => 'select',
						'label'   => __( 'Collapse Inactive', 'fl-builder' ),
						'default' => '1',
						'options' => array(
							'1' => __( 'Yes', 'fl-builder' ),
							'0' => __( 'No', 'fl-builder' ),
						),
						'help'    => __( 'Choosing yes will keep only one item open at a time. Choosing no will allow multiple items to be open at the same time.', 'fl-builder' ),
						'preview' => array(
							'type' => 'none',
						),
					),
					'open_first'        => array(
						'type'    => 'select',
						'label'   => __( 'Expand First Item', 'fl-builder' ),
						'default' => '0',
						'options' => array(
							'0' => __( 'No', 'fl-builder' ),
							'1' => __( 'Yes', 'fl-builder' ),
						),
						'help'    => __( 'Choosing yes will expand the first item by default.', 'fl-builder' ),
					),
				),
			),

		),
	),
	'style' => array(
		'title'    => __( 'Style', 'fl-builder' ),
		'sections' => array(
			'general' => array(
				'title'  => '',
				'fields' => array(
					'label_size'   => array(
						'type'    => 'select',
						'label'   => __( 'Item Size', 'fl-builder' ),
						'default' => 'small',
						'options' => array(
							'small'  => _x( 'Small', 'Label size.', 'fl-builder' ),
							'medium' => _x( 'Medium', 'Label size.', 'fl-builder' ),
							'large'  => _x( 'Large', 'Label size.', 'fl-builder' ),
						),
						'preview' => array(
							'type' => 'none',
						),
					),
					'item_spacing' => array(
						'type'       => 'unit',
						'label'      => __( 'Item Spacing', 'fl-builder' ),
						'default'    => '10',
						'responsive' => true,
						'slider'     => true,
						'units'      => array( 'px' ),
						'preview'    => array(
							'type'     => 'css',
							'selector' => '.fl-accordion-item',
							'property' => 'margin-bottom',
							'unit'     => 'px',
						),
					),
					'item_border'  => array(
						'type'       => 'border',
						'label'      => __( 'Item Border', 'fl-builder' ),
						'responsive' => true,
						'default'    => array(
							'style' => 'solid',
							'color' => 'e5e5e5',
							'width' => array(
								'top'    => '1',
								'right'  => '1',
								'bottom' => '1',
								'left'   => '1',
							),
						),
						'preview'    => array(
							'type'     => 'css',
							'selector' => '.fl-accordion-item',
						),
					),
				),
			),
			'label'   => array(
				'title'  => __( 'Label', 'fl-builder' ),
				'fields' => array(
					'label_text_color' => array(
						'type'        => 'color',
						'connections' => array( 'color' ),
						'label'       => __( 'Text Color', 'fl-builder' ),
						'show_reset'  => true,
						'show_alpha'  => true,
						'preview'     => array(
							'type'     => 'css',
							'selector' => '.fl-accordion-button *, .fl-accordion-button-icon',
							'property' => 'color',
						),
					),
					'label_bg_color'   => array(
						'type'        => 'color',
						'connections' => array( 'color' ),
						'label'       => __( 'Background Color', 'fl-builder' ),
						'show_reset'  => true,
						'show_alpha'  => true,
						'preview'     => array(
							'type'     => 'css',
							'selector' => '.fl-accordion-button',
							'property' => 'background-color',
						),
					),
					'label_padding'    => array(
						'type'       => 'dimension',
						'label'      => __( 'Padding', 'fl-builder' ),
						'responsive' => true,
						'slider'     => true,
						'units'      => array(
							'px',
							'em',
							'%',
						),
						'preview'    => array(
							'type'     => 'css',
							'selector' => '.fl-accordion-button',
							'property' => 'padding',
						),
					),
					'label_typography' => array(
						'type'       => 'typography',
						'label'      => __( 'Typography', 'fl-builder' ),
						'responsive' => true,
						'preview'    => array(
							'type'      => 'css',
							'selector'  => '.fl-accordion-button, .fl-accordion-button-label',
							'important' => true,
						),
					),
				),
			),
			'icon'    => array(
				'title'  => __( 'Icon', 'fl-builder' ),
				'fields' => array(
					'label_icon_position' => array(
						'type'    => 'select',
						'label'   => __( 'Icon Position', 'fl-builder' ),
						'default' => 'right',
						'options' => array(
							'left'  => __( 'Left', 'fl-builder' ),
							'right' => __( 'Right', 'fl-builder' ),
						),
					),
					'label_icon'          => array(
						'type'               => 'icon',
						'label'              => __( 'Icon', 'fl-builder' ),
						'default'            => 'fas fa-plus',
						'show_extra_classes' => true,
						'connections'        => array( 'icon' ),
					),
					'label_active_icon'   => array(
						'type'               => 'icon',
						'label'              => __( 'Active Icon', 'fl-builder' ),
						'default'            => 'fas fa-minus',
						'show_extra_classes' => true,
						'connections'        => array( 'icon' ),
					),
					'duo_color1'          => array(
						'label'       => __( 'DuoTone Icon Primary Color', 'fl-builder' ),
						'type'        => 'color',
						'connections' => array( 'color' ),
						'default'     => '',
						'show_reset'  => true,
						'preview'     => array(
							'type'      => 'css',
							'selector'  => '.fl-accordion-button-icon i.fad:before',
							'property'  => 'color',
							'important' => true,
						),
					),
					'duo_color2'          => array(
						'label'       => __( 'DuoTone Icon Secondary Color', 'fl-builder' ),
						'type'        => 'color',
						'connections' => array( 'color' ),
						'default'     => '',
						'show_reset'  => true,
						'preview'     => array(
							'type'      => 'css',
							'selector'  => '.fl-accordion-button-icon i.fad:after',
							'property'  => 'color',
							'important' => true,
						),
					),
				),
			),
			'content' => array(
				'title'  => __( 'Content', 'fl-builder' ),
				'fields' => array(
					'content_text_color' => array(
						'type'        => 'color',
						'connections' => array( 'color' ),
						'label'       => __( 'Text Color', 'fl-builder' ),
						'show_reset'  => true,
						'show_alpha'  => true,
						'preview'     => array(
							'type'     => 'css',
							'selector' => '.fl-accordion-content :where( p, span, li )',
							'property' => 'color',
						),
					),
					'content_bg_color'   => array(
						'type'        => 'color',
						'connections' => array( 'color' ),
						'label'       => __( 'Background Color', 'fl-builder' ),
						'show_reset'  => true,
						'show_alpha'  => true,
						'preview'     => array(
							'type'     => 'css',
							'selector' => '.fl-accordion-content',
							'property' => 'background-color',
						),
					),
					'content_padding'    => array(
						'type'       => 'dimension',
						'label'      => __( 'Padding', 'fl-builder' ),
						'responsive' => true,
						'slider'     => true,
						'units'      => array(
							'px',
							'em',
							'%',
						),
						'preview'    => array(
							'type'     => 'css',
							'selector' => '.fl-accordion-content',
							'property' => 'padding',
						),
					),
					'content_typography' => array(
						'type'       => 'typography',
						'label'      => __( 'Typography', 'fl-builder' ),
						'responsive' => true,
						'preview'    => array(
							'type'      => 'css',
							'selector'  => '.fl-accordion-content :where( p, span, li )',
							'important' => true,
						),
					),
				),
			),
		),
	),
));

/**
 * Register a settings form to use in the "form" field type above.
 */
FLBuilder::register_settings_form('accordion_items_form', array(
	'title' => __( 'Add Item', 'fl-builder' ),
	'tabs'  => array(
		'general' => array(
			'title'    => __( 'General', 'fl-builder' ),
			'sections' => array(
				'general'      => array(
					'title'  => '',
					'fields' => array(
						'label' => array(
							'type'        => 'text',
							'label'       => __( 'Label', 'fl-builder' ),
							'connections' => array( 'string' ),
						),
					),
				),
				'content_type' => array(
					'title'  => __( 'Content Type', 'fl-builder' ),
					'fields' => array(
						'saved_layout'   => array(
							'type'    => 'select',
							'label'   => __( 'Type', 'fl-builder' ),
							'default' => 'none',
							'help'    => __( 'This setting allows you to show saved layout in the slide.', 'fl-builder' ),
							'options' => array(
								'row'      => __( 'Saved Row', 'fl-builder' ),
								'column'   => __( 'Saved Column', 'fl-builder' ),
								'module'   => __( 'Saved Module', 'fl-builder' ),
								'template' => __( 'Saved Template', 'fl-builder' ),
								'none'     => __( 'Custom Content', 'fl-builder' ),
							),
							'toggle'  => array(
								'none'     => array(
									'sections' => array( 'content' ),
								),
								'row'      => array(
									'fields' => array( 'saved_row' ),
								),
								'column'   => array(
									'fields' => array( 'saved_column' ),
								),
								'module'   => array(
									'fields' => array( 'saved_module' ),
								),
								'template' => array(
									'fields' => array( 'saved_template' ),
								),
							),
						),
						'saved_row'      => array(
							'type'       => 'select',
							'label'      => __( 'Select Row', 'fl-builder' ),
							'saved_data' => 'row',
						),
						'saved_column'   => array(
							'type'       => 'select',
							'label'      => __( 'Select Column', 'fl-builder' ),
							'saved_data' => 'column',
						),
						'saved_module'   => array(
							'type'       => 'select',
							'label'      => __( 'Select Modules', 'fl-builder' ),
							'saved_data' => 'module',
						),
						'saved_template' => array(
							'type'       => 'select',
							'label'      => __( 'Select Template', 'fl-builder' ),
							'saved_data' => 'layout',
						),
					),
				),
				'content'      => array(
					'title'  => __( 'Content', 'fl-builder' ),
					'fields' => array(
						'content' => array(
							'type'        => 'editor',
							'label'       => '',
							'wpautop'     => false,
							'connections' => array( 'string' ),
						),
					),
				),
			),
		),
	),
));
