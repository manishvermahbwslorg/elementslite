<?php
/**
 * Title: Parallax Element
 *
 * Description: Adds parallax effect to different elements.
 *
 * Please do not edit this file. This file is part of the Cyber Chimps Framework and all modifications
 * should be made in a child theme.
 *
 * @category Cyber Chimps Framework
 * @package  Framework
 * @since    1.0
 * @author   CyberChimps
 * @license  http://www.opensource.org/licenses/gpl-license.php GPL v3.0 (or later)
 * @link     http://www.cyberchimps.com/
 */

// Don't load directly
if ( !defined( 'ABSPATH' ) ) {
	die( '-1' );
}

if ( !class_exists( 'CyberchimpsParallax' ) ) {
	class CyberChimpsParallax {

		protected static $instance;
		public $options;

		/* Static Singleton Factory Method */
		public static function instance() {
			if ( !isset( self::$instance ) ) {
				$className      = __CLASS__;
				self::$instance = new $className;
			}

			return self::$instance;
		}

		/**
		 * Initializes plugin variables and sets up WordPress hooks/actions.
		 *
		 * @return void
		 */
		protected function __construct() {
			$this->options = get_option( 'cyberchimps_options' );
			add_action( 'wp_enqueue_scripts', array( $this, 'cyberchimps_parallax_scripts' ) );
			add_action( 'carousel_section', array( $this, 'render_display' ) );
			add_action( 'wp_footer', array( $this, 'cyberchimps_parallax_render' ) );

			add_filter( 'cyberchimps_field_filter', array( $this, 'cyberchimps_parallax_fields' ) );

		}

		/**
		 * Sets up scripts for parallax
		 */
		public function cyberchimps_parallax_scripts() {

			// Add parallax js library.
			wp_enqueue_script( 'parallax-js', get_template_directory_uri() . '/elements/lib/js/jquery.parallax.js', array( 'jquery' ) );
		}

		/**
		 * Adds option fields to blog options
		 *
		 * @param $original
		 *
		 * @return mixed
		 */
		public function cyberchimps_parallax_fields( $original ) {

			// Slider parallax toggle.
			$new_field[][2] = array(
				'name'    => __( 'Parallax', 'cyberchimps_elements' ),
				'id'      => 'cyberchimps_blog_slider_parallax',
				'type'    => 'toggle',
				'std'     => 1,
				'section' => 'cyberchimps_blog_slider_lite_section',
				'heading' => 'cyberchimps_blog_heading'
			);

			// Slider parallax image.
			$new_field[][3] = array(
				'name'    => __( 'Background image for parallax', 'cyberchimps_elements' ),
				'desc'    => __( 'Enter URL or upload file', 'cyberchimps_elements' ),
				'id'      => 'cyberchimps_blog_slider_parallax_image',
				'class'   => 'cyberchimps_blog_slider_parallax_toggle',
				'type'    => 'upload',
				'std'     => get_template_directory_uri() . '/images/parallax/sun.jpg',
				'section' => 'cyberchimps_blog_slider_lite_section',
				'heading' => 'cyberchimps_blog_heading'
			);

			// Portfolio parallax toggle.
			$new_field[][2] = array(
				'name'    => __( 'Parallax', 'cyberchimps_elements' ),
				'id'      => 'cyberchimps_blog_portfolio_parallax',
				'type'    => 'toggle',
				'std'     => 1,
				'section' => 'cyberchimps_blog_portfolio_lite_section',
				'heading' => 'cyberchimps_blog_heading'
			);

			// Portfolio parallax image.
			$new_field[][3] = array(
				'name'    => __( 'Background image for parallax', 'cyberchimps_elements' ),
				'desc'    => __( 'Enter URL or upload file', 'cyberchimps_elements' ),
				'id'      => 'cyberchimps_blog_portfolio_parallax_image',
				'class'   => 'cyberchimps_blog_portfolio_parallax_toggle',
				'type'    => 'upload',
				'std'     => get_template_directory_uri() . '/images/parallax/trees.jpg',
				'section' => 'cyberchimps_blog_portfolio_lite_section',
				'heading' => 'cyberchimps_blog_heading'
			);

			// Boxes parallax toggle.
			$new_field[][2] = array(
				'name'    => __( 'Parallax', 'cyberchimps_elements' ),
				'id'      => 'cyberchimps_blog_boxes_parallax',
				'type'    => 'toggle',
				'std'     => 1,
				'section' => 'cyberchimps_blog_boxes_lite_section',
				'heading' => 'cyberchimps_blog_heading'
			);

			// Boxes parallax image.
			$new_field[][3] = array(
				'name'    => __( 'Background image for parallax', 'cyberchimps_elements' ),
				'desc'    => __( 'Enter URL or upload file', 'cyberchimps_elements' ),
				'id'      => 'cyberchimps_blog_boxes_parallax_image',
				'class'   => 'cyberchimps_blog_boxes_parallax_toggle',
				'type'    => 'upload',
				'std'     => get_template_directory_uri() . '/images/parallax/rocks.jpg',
				'section' => 'cyberchimps_blog_boxes_lite_section',
				'heading' => 'cyberchimps_blog_heading'
			);

			// Body parallax toggle.
			$new_field[][1] = array(
				'name'    => __( 'Parallax', 'cyberchimps_elements' ),
				'id'      => 'cyberchimps_body_parallax',
				'desc'    => __( 'Set the background image at Appearance > Background to get parallax effect on whole body.', 'cyberchimps_elements' ),
				'type'    => 'toggle',
				'std'     => 1,
				'section' => 'cyberchimps_custom_layout_section',
				'heading' => 'cyberchimps_design_heading'
			);

			$new_fields = cyberchimps_array_field_organizer( $original, $new_field );

			return $new_fields;
		}

// Set parallax to individual elements by checking toggle.
		public function cyberchimps_parallax_render() {

			// Get slider parallax options.
			$slider_parallax_toggle = $this->options['cyberchimps_blog_slider_parallax'];
			$slider_parallax_image  = $this->options['cyberchimps_blog_slider_parallax_image'];

			// Get portfolio parallax options.
			$portfolio_parallax_toggle = $this->options['cyberchimps_blog_portfolio_parallax'];
			$portfolio_parallax_image  = $this->options['cyberchimps_blog_portfolio_parallax_image'];

			// Get boxes parallax options.
			$boxes_parallax_toggle = $this->options['cyberchimps_blog_boxes_parallax'];
			$boxes_parallax_image  = $this->options['cyberchimps_blog_boxes_parallax_image'];

			// Get boxes parallax options.
			$body_parallax_toggle = $this->options['cyberchimps_body_parallax'];
			?>
			<script>
				jQuery(document).ready(function () {
					<?php
					// Add parallax to slider.
					if( $slider_parallax_toggle && $slider_parallax_image ) { ?>
					jQuery('#slider_lite_section').css({
						'background': 'url("<?php echo $slider_parallax_image;?>")',
						'background-size': '100%'
					});
					jQuery('#slider_lite_section').parallax('50%', 0.5);
					<?php }
					// Add parallax to portfolio.
					if( $portfolio_parallax_toggle && $portfolio_parallax_image ) { ?>
					jQuery('#portfolio_lite_section').css({
						'background': 'url("<?php echo $portfolio_parallax_image;?>")',
						'background-size': '100%'
					});
					jQuery('#portfolio_lite_section').parallax('50%', 0.5);
					<?php }
					// Add parallax to boxes.
					if( $boxes_parallax_toggle && $boxes_parallax_image ) { ?>
					jQuery('#boxes_lite_section').css({
						'background': 'url("<?php echo $boxes_parallax_image;?>")',
						'background-size': '100%'
					});
					jQuery('#boxes_lite_section').parallax('50%', 0.5);
					<?php }
					// Add parallax to body.
					if( $body_parallax_toggle ) { ?>
					jQuery('body').parallax('50%', 0.3);
					<?php } ?>

				});
			</script>
		<?php
		}
	}
}