<style>
.fl-lightbox .fl-builder-settings-section {
    box-sizing: border-box;
}
.fl-lightbox .c_secton {
    font-size: 20px;
    padding: 15px 25px;
    clear: left;
    background: #ddd;
    margin: 30px -5px 20px;
    display: block;
    letter-spacing: .3px;
    background: #18a3df;
    color: #fff;
}
.fl-lightbox .row:first-of-type .c_secton {
    margin-top: 0;
}
.fl-lightbox .c_secton span {
    color: rgba(255,255,255,0.8);
    padding: 6px;
    font-style: italic;
    letter-spacing: 0.5px;
}
.section { /* animacja podglądu */
    transition: padding .5s;
    -webkit-transition: padding .5s;
}
html.js .tmce-active .wp-editor-area {
    color: #000;
}

.c_secton_info {
    margin: -20px -5px 20px;
    background: #666;
    padding: 8px 25px;
    font-style: italic;
}
.c_secton_info span {
    color: rgba(255,255,255,0.8);
    letter-spacing: .25px;
}
.c_secton_info.smaller span {
    letter-spacing: 1px;
    line-height: 1;
    font-weight: 100;
    font-size: 11px;
    display: block;
}

body .fl-builder-lightbox .fl-lightbox {
	width: 840px;
	max-width: 100%;
	opacity: .3;
	-webkit-transition: .25s;
	transition: .25s;
}
body .fl-builder-lightbox .fl-lightbox:hover {
	opacity: 1;
}
body .fl-builder-settings-tab {
	width: 100%;
}


</style>


<?php 

FLBuilderModel::default_settings($settings, array(
	'post_type' => 'post',
	'order_by'  => 'date',
	'order'     => 'DESC',
	'offset'    => 0,
	'users'     => ''
));

?>
	
	
	<div class="row">
		<div class="fl-builder-settings-section col-sm-12">
			<table class="fl-form-table">
		    
			    <h3 class="fl-builder-settings-title">Icon between sections</h3>
				<?php // Fields:
				
					FLBuilder::render_settings_field('section_icon', array(
				        'type'          => 'select',
				        'label'         => 'Icon position',
				        'default'       => '',
				        'options'       => array(
				            ''      => 'Off',
				            'top'      => 'Top',
				            'bottom'      => 'Bottom'
				        ),
                        'toggle'        => array(
                            'top'      => array(
                                'fields'        => array('select_icon')
                            ),
                            'bottom'      => array(
                                'fields'        => array('select_icon')
                            )
                        )
					), $settings); 
			   
					FLBuilder::render_settings_field('select_icon', array(
				        'type'          => 'select',
				        'label'         => 'Select Icon',
				        'default'       => 'music',
				        'options'       => array(
				            'section_icon'      => 'music',
				            'brain'      => 'brain',
				            'custom'      => 'custom'
				        ),
                        'toggle'        => array(
                            'custom'      => array(
                                'fields'        => array('custom_icon')
                            )
                        )
					), $settings); 
					
					FLBuilder::render_settings_field('custom_icon', array(
				    	'type'          => 'photo',
						'label'         => 'Custom icon',
                        'show_remove'   => true
                    ), $settings); 
			   
				?>
				
			</table>
		</div>
	</div><!-- .row -->
