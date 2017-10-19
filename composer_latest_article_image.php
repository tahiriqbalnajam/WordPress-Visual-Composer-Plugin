<?php
/**
 * Plugin Name: VC Latest Image
 * Plugin URI: http://idlbridge.com
 * Description: A Plugin for Visual Composer to show featured image of latest article with category link.
 * Version: 1.0.0
 * Author: Tahir Iqbal
 * Author URI: http://tinajam.wordpress.com
 * License: GPL2
 */



add_action( 'vc_before_init', 'IDL_latestarticle_init_actions' );
 
function IDL_latestarticle_init_actions() {
     
    class latestArticleCategoryImage extends WPBakeryShortCode {
     
    // Element Init
    function __construct() {
        add_action( 'init', array( $this, 'IDL_latestarticle_mapping' ) );
        add_shortcode( 'IDL_latestarticle', array( $this, 'IDL_latestarticle_html' ) );
    }
     
    // Element Mapping
    public function IDL_latestarticle_mapping() {
         
        // Stop all if VC is not enabled
        if ( !defined( 'WPB_VC_VERSION' ) ) {
            return;
        }
        
        $categories_array = array();
		$categories = get_categories();
		$categories_array['select category'] = 'Select Category';
		foreach( $categories as $category ){
			$begin = __(' (ID: ', 'js_composer');
            $end = __(')', 'js_composer');
            $categories_array[ $category->name . $begin. $category->term_id . $end ] = $category->term_id;
		}
        // Map the block with vc_map()
        vc_map( 
            array(
                'name' => __('View Latest Article Image', 'text-domain'),
                'base' => 'IDL_latestarticle',
                'description' => __('Latest article featured image view', 'text-domain'), 
                'category' => __('IDL Elements', 'text-domain'),   
                'icon' => '<i class="vc_general vc_element-icon icon-wpb-application-icon-large"></i>',            
                'params' => array(   
                         
                    array(
                        'type' => 'textfield',
                        'holder' => 'h3',
                        'class' => 'title-class',
                        'heading' => __( 'Title', 'text-domain' ),
                        'param_name' => 'title',
                        'value' => __( 'Default value', 'text-domain' ),
                        'description' => __( 'Box Title', 'text-domain' ),
                        'admin_label' => false,
                        'weight' => 0,
                        'group' => 'Custom Group',
                    ),  
                     
                    array(
                        'type' => 'dropdown',
                        'holder' => 'div',
                        'class' => 'text-class',
                        'heading' => __( 'Select Category', 'text-domain' ),
                        'param_name' => 'categoryid',
                        'value' => $categories_array,
                        'description' => __( 'Latest article\'s category', 'text-domain' ),
                        'admin_label' => false,
                        'weight' => 0,
                        'group' => 'Custom Group',
                    ),                      
                        
                ),
            )
        );                                
        
    }
     
     
    // Element HTML
    public function IDL_latestarticle_html( $atts ) {
         
        // Params extraction
        extract(
            shortcode_atts(
                array(
                    'title'   => '',
                    'categoryid' => '',
                ), 
                $atts
            )
        );
        
        if($categoryid)
        {
        	$category_link = get_category_link( $categoryid );
        	$catquery = new WP_Query("cat=$categoryid&&posts_per_page=1");
        	while($catquery->have_posts()) : 
        		$catquery->the_post();
        		$bgurl = wp_get_attachment_url( get_post_thumbnail_id($post->ID) );

        		$output = "
					<section class='vc_cta3-container' style='background-image: url(".$bgurl."); background-size:cover; background-color:transparent;'>
								<div class='vc_general vc_cta3 vc_cta3-align-center' style='background:rgba(0,0,0,0.3); padding:100px 0'>
									<div class='vc_cta3_content-container'>
										<div class='vc_cta3-content'>
											<header class='vc_cta3-content-header'>
												<h2 style='color: #ffffff' class='vc_custom_heading'>".$title."</h2>	
											</header>
										</div>
										<div class='vc_cta3-actions'>
											<div class='vc_btn3-container vc_btn3-center'>
												<a class='vc_general vc_btn3 vc_btn3-size-sm vc_btn3-shape-square vc_btn3-style-outline vc_btn3-icon-left vc_btn3-color-white' href='".esc_url( $category_link )."' title='''>
												<i class='vc_btn3-icon fa fa-chevron-circle-down'></i> Click
												</a>
											</div>
										</div>	
									</div>
								</div>
						</section>";

        	endwhile;

        	return $output;
        }
        else
        	return 'Please select a category first';
         
    }
     
} // End Element Class
 
 
// Element Class Init
new latestArticleCategoryImage();    
     
}