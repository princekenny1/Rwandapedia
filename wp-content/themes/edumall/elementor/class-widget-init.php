<?php

namespace Edumall_Elementor;

defined( 'ABSPATH' ) || exit;

class Widget_Init {

	private static $_instance = null;

	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	public function initialize() {
		add_action( 'elementor/elements/categories_registered', [ $this, 'add_elementor_widget_categories' ] );

		// Registered Widgets.
		add_action( 'elementor/widgets/register', [ $this, 'init_widgets' ] );

		add_action( 'elementor/frontend/after_register_scripts', [ $this, 'after_register_scripts' ] );
		add_action( 'elementor/frontend/after_register_styles', [ $this, 'after_register_styles' ] );

		add_action( 'elementor/editor/after_enqueue_scripts', [ $this, 'enqueue_editor_scripts' ] );

		add_filter( 'elementor/utils/get_the_archive_title', [ $this, 'change_portfolio_archive_title' ] );

		// Modify original widgets settings.
		require_once EDUMALL_ELEMENTOR_DIR . '/widgets/original/modify-base.php';
		require_once EDUMALL_ELEMENTOR_DIR . '/widgets/original/section.php';
		require_once EDUMALL_ELEMENTOR_DIR . '/widgets/original/column.php';
		require_once EDUMALL_ELEMENTOR_DIR . '/widgets/original/accordion.php';
		require_once EDUMALL_ELEMENTOR_DIR . '/widgets/original/animated-headline.php';
		require_once EDUMALL_ELEMENTOR_DIR . '/widgets/original/counter.php';
		require_once EDUMALL_ELEMENTOR_DIR . '/widgets/original/form.php';
		require_once EDUMALL_ELEMENTOR_DIR . '/widgets/original/heading.php';
		require_once EDUMALL_ELEMENTOR_DIR . '/widgets/original/image.php';
		require_once EDUMALL_ELEMENTOR_DIR . '/widgets/original/icon-box.php';
		require_once EDUMALL_ELEMENTOR_DIR . '/widgets/original/progress.php';
		require_once EDUMALL_ELEMENTOR_DIR . '/widgets/original/countdown.php';
		require_once EDUMALL_ELEMENTOR_DIR . '/widgets/original/testimonial.php';

		// Required ajax handler for widgets.
		require_once EDUMALL_ELEMENTOR_DIR . '/class-widget-ajax-init.php';
	}

	/**
	 * Register scripts for widgets.
	 */
	public function after_register_scripts() {
		$min = \Edumall_Enqueue::instance()->get_min_suffix();

		// Fix Wordpress old version not registered this script.
		if ( ! wp_script_is( 'imagesloaded', 'registered' ) ) {
			wp_register_script( 'imagesloaded', EDUMALL_THEME_URI . '/assets/libs/imagesloaded/imagesloaded.min.js', array( 'jquery' ), null, true );
		}

		wp_register_script( 'circle-progress', EDUMALL_THEME_URI . '/assets/libs/circle-progress/circle-progress.min.js', array( 'jquery' ), null, true );
		wp_register_script( 'edumall-widget-circle-progress', EDUMALL_ELEMENTOR_URI . '/assets/js/widgets/widget-circle-progress.js', array(
			'jquery',
			'circle-progress',
		), null, true );

		wp_register_script( 'edumall-swiper-wrapper', EDUMALL_THEME_URI . "/assets/js/swiper-wrapper{$min}.js", [
			'jquery',
			'swiper',
		], EDUMALL_THEME_VERSION, true );
		wp_register_script( 'edumall-group-widget-carousel', EDUMALL_ELEMENTOR_URI . '/assets/js/widgets/group-widget-carousel.js', [
			'edumall-swiper-wrapper',
		], null, true );
		$edumall_swiper_js = array(
			'fractionPrefixText' => esc_html__( 'Show', 'edumall' ),
			'prevText'           => esc_html__( 'Prev', 'edumall' ),
			'nextText'           => esc_html__( 'Next', 'edumall' ),
		);
		wp_localize_script( 'edumall-swiper-wrapper', '$edumallSwiper', $edumall_swiper_js );

		wp_register_script( 'isotope-masonry', EDUMALL_THEME_URI . "/assets/js/isotope.pkgd{$min}.js", array( 'jquery' ), EDUMALL_THEME_VERSION, true );
		wp_register_script( 'isotope-packery', EDUMALL_THEME_URI . "/assets/libs/packery-mode/packery-mode.pkgd{$min}.js", array( 'jquery' ), EDUMALL_THEME_VERSION, true );

		wp_register_script( 'edumall-grid-layout', EDUMALL_THEME_ASSETS_URI . "/js/grid-layout{$min}.js", array(
			'jquery',
			'imagesloaded',
			'matchheight',
			'isotope-masonry',
			'isotope-packery',
		), null, true );
		wp_register_script( 'edumall-grid-query', EDUMALL_ELEMENTOR_URI . '/assets/js/widgets/grid-query.js', array( 'jquery' ), null, true );

		wp_register_script( 'edumall-widget-grid-post', EDUMALL_ELEMENTOR_URI . '/assets/js/widgets/widget-grid-post.js', array( 'edumall-grid-layout' ), null, true );
		wp_register_script( 'edumall-group-widget-grid', EDUMALL_ELEMENTOR_URI . '/assets/js/widgets/group-widget-grid.js', array( 'edumall-grid-layout' ), null, true );

		wp_register_script( 'edumall-widget-google-map', EDUMALL_ELEMENTOR_URI . '/assets/js/widgets/widget-google-map.js', array( 'jquery' ), null, true );

		wp_register_script( 'vivus', EDUMALL_ELEMENTOR_URI . "/assets/libs/vivus/vivus{$min}.js", array( 'jquery' ), null, true );
		wp_register_script( 'edumall-widget-icon-box', EDUMALL_ELEMENTOR_URI . '/assets/js/widgets/widget-icon-box.js', array(
			'jquery',
			'vivus',
		), null, true );

		wp_register_script( 'count-to', EDUMALL_ELEMENTOR_URI . "/assets/libs/countTo/jquery.countTo{$min}.js", array( 'jquery' ), null, true );
		wp_register_script( 'edumall-widget-counter', EDUMALL_ELEMENTOR_URI . '/assets/js/widgets/widget-counter.js', array(
			'jquery',
			'count-to',
		), null, true );

		wp_register_script( 'countdown', EDUMALL_THEME_ASSETS_URI . '/libs/jquery.countdown/js/jquery.countdown.min.js', array( 'jquery' ), null, true );
		wp_register_script( 'edumall-widget-countdown', EDUMALL_ELEMENTOR_URI . '/assets/js/widgets/widget-countdown.js', array(
			'jquery',
			'countdown',
		), null, true );

		wp_register_script( 'edumall-widget-flip-box', EDUMALL_ELEMENTOR_URI . '/assets/js/widgets/widget-flip-box.js', array(
			'jquery',
			'imagesloaded',
		), null, true );

		wp_register_script( 'edumall-widget-accordion', EDUMALL_ELEMENTOR_URI . '/assets/js/widgets/widget-accordion.js', array(
			'jquery',
		), null, true );

		wp_register_script( 'edumall-widget-gallery-justified-content', EDUMALL_ELEMENTOR_URI . '/assets/js/widgets/widget-gallery-justified-content.js', array(
			'justifiedGallery',
		), null, true );
	}

	/**
	 * enqueue scripts in editor mode.
	 */
	public function enqueue_editor_scripts() {
		wp_enqueue_script( 'edumall-elementor-editor', EDUMALL_ELEMENTOR_URI . '/assets/js/editor.js', array( 'jquery' ), null, true );
	}

	/**
	 * Register styles for widgets.
	 */
	public function after_register_styles() {

	}

	/**
	 * @param \Elementor\Elements_Manager $elements_manager
	 *
	 * Add category.
	 */
	function add_elementor_widget_categories( $elements_manager ) {
		$elements_manager->add_category( 'edumall', [
			'title' => esc_html__( 'By Edumall', 'edumall' ),
			'icon'  => 'fa fa-plug',
		] );
	}

	/**
	 * Init Widgets
	 *
	 * Include widgets files and register them
	 *
	 * @param \Elementor\Widgets_Manager $widget_manager
	 *
	 * @since  1.0.0
	 *
	 * @access public
	 * @throws \Exception
	 */
	public function init_widgets( $widget_manager ) {

		// Include Widget files.
		require_once EDUMALL_ELEMENTOR_DIR . '/widgets/base.php';
		require_once EDUMALL_ELEMENTOR_DIR . '/widgets/form/form-base.php';
		require_once EDUMALL_ELEMENTOR_DIR . '/widgets/posts/posts-base.php';
		require_once EDUMALL_ELEMENTOR_DIR . '/widgets/terms/terms-base.php';
		require_once EDUMALL_ELEMENTOR_DIR . '/widgets/carousel/carousel-base.php';
		require_once EDUMALL_ELEMENTOR_DIR . '/widgets/carousel/posts-carousel-base.php';
		require_once EDUMALL_ELEMENTOR_DIR . '/widgets/carousel/terms-carousel-base.php';
		require_once EDUMALL_ELEMENTOR_DIR . '/widgets/carousel/static-carousel.php';
		require_once EDUMALL_ELEMENTOR_DIR . '/widgets/carousel/carousel-nav-buttons.php';

		require_once EDUMALL_ELEMENTOR_DIR . '/widgets/accordion.php';
		require_once EDUMALL_ELEMENTOR_DIR . '/widgets/tabs.php';
		require_once EDUMALL_ELEMENTOR_DIR . '/widgets/button.php';
		require_once EDUMALL_ELEMENTOR_DIR . '/widgets/circle-progress-chart.php';
		require_once EDUMALL_ELEMENTOR_DIR . '/widgets/counter.php';
		require_once EDUMALL_ELEMENTOR_DIR . '/widgets/countdown.php';
		require_once EDUMALL_ELEMENTOR_DIR . '/widgets/google-map.php';
		require_once EDUMALL_ELEMENTOR_DIR . '/widgets/heading.php';
		require_once EDUMALL_ELEMENTOR_DIR . '/widgets/typed-headline.php';
		require_once EDUMALL_ELEMENTOR_DIR . '/widgets/icon.php';
		require_once EDUMALL_ELEMENTOR_DIR . '/widgets/icon-box.php';
		require_once EDUMALL_ELEMENTOR_DIR . '/widgets/image-box.php';
		require_once EDUMALL_ELEMENTOR_DIR . '/widgets/image-layers.php';
		require_once EDUMALL_ELEMENTOR_DIR . '/widgets/image-gallery.php';
		require_once EDUMALL_ELEMENTOR_DIR . '/widgets/form/register-form.php';
		require_once EDUMALL_ELEMENTOR_DIR . '/widgets/gallery-justified-content.php';
		require_once EDUMALL_ELEMENTOR_DIR . '/widgets/banner.php';
		require_once EDUMALL_ELEMENTOR_DIR . '/widgets/shapes.php';
		require_once EDUMALL_ELEMENTOR_DIR . '/widgets/flip-box.php';
		require_once EDUMALL_ELEMENTOR_DIR . '/widgets/attribute-list.php';
		require_once EDUMALL_ELEMENTOR_DIR . '/widgets/gradation.php';
		require_once EDUMALL_ELEMENTOR_DIR . '/widgets/timeline.php';
		require_once EDUMALL_ELEMENTOR_DIR . '/widgets/list.php';
		require_once EDUMALL_ELEMENTOR_DIR . '/widgets/pricing-table.php';
		require_once EDUMALL_ELEMENTOR_DIR . '/widgets/twitter.php';
		require_once EDUMALL_ELEMENTOR_DIR . '/widgets/team-member.php';
		require_once EDUMALL_ELEMENTOR_DIR . '/widgets/social-networks.php';
		require_once EDUMALL_ELEMENTOR_DIR . '/widgets/star-rating.php';
		require_once EDUMALL_ELEMENTOR_DIR . '/widgets/popup-video.php';
		require_once EDUMALL_ELEMENTOR_DIR . '/widgets/separator.php';
		require_once EDUMALL_ELEMENTOR_DIR . '/widgets/table.php';
		require_once EDUMALL_ELEMENTOR_DIR . '/widgets/full-page.php';
		require_once EDUMALL_ELEMENTOR_DIR . '/widgets/portfolio-details.php';

		require_once EDUMALL_ELEMENTOR_DIR . '/widgets/grid/grid-base.php';
		require_once EDUMALL_ELEMENTOR_DIR . '/widgets/grid/static-grid.php';
		require_once EDUMALL_ELEMENTOR_DIR . '/widgets/grid/client-logo.php';
		require_once EDUMALL_ELEMENTOR_DIR . '/widgets/grid/view-demo.php';

		require_once EDUMALL_ELEMENTOR_DIR . '/widgets/posts/blog.php';
		require_once EDUMALL_ELEMENTOR_DIR . '/widgets/posts/portfolio.php';

		require_once EDUMALL_ELEMENTOR_DIR . '/widgets/faq/faq-cards.php';

		require_once EDUMALL_ELEMENTOR_DIR . '/widgets/testimonial-grid.php';
		require_once EDUMALL_ELEMENTOR_DIR . '/widgets/testimonial-carousel-3d.php';
		require_once EDUMALL_ELEMENTOR_DIR . '/widgets/carousel/testimonial-carousel.php';

		require_once EDUMALL_ELEMENTOR_DIR . '/widgets/carousel/team-member-carousel.php';
		require_once EDUMALL_ELEMENTOR_DIR . '/widgets/carousel/portfolio-carousel.php';
		require_once EDUMALL_ELEMENTOR_DIR . '/widgets/carousel/image-carousel.php';
		require_once EDUMALL_ELEMENTOR_DIR . '/widgets/carousel/rich-carousel.php';
		require_once EDUMALL_ELEMENTOR_DIR . '/widgets/carousel/modern-carousel.php';
		require_once EDUMALL_ELEMENTOR_DIR . '/widgets/carousel/modern-slider.php';

		// Register Widgets.
		$widget_manager->register( new Widget_Accordion() );
		$widget_manager->register( new Widget_Tabs() );
		$widget_manager->register( new Widget_Button() );
		$widget_manager->register( new Widget_Client_Logo() );
		$widget_manager->register( new Widget_Circle_Progress_Chart() );
		$widget_manager->register( new Widget_Counter() );
		$widget_manager->register( new Widget_Countdown() );
		$widget_manager->register( new Widget_Google_Map() );
		$widget_manager->register( new Widget_Heading() );
		$widget_manager->register( new Widget_Typed_Headline() );
		$widget_manager->register( new Widget_Icon() );
		$widget_manager->register( new Widget_Icon_Box() );
		$widget_manager->register( new Widget_Image_Box() );
		$widget_manager->register( new Widget_Image_Layers() );
		$widget_manager->register( new Widget_Image_Gallery() );
		$widget_manager->register( new Widget_Carousel_Nav_Buttons() );
		$widget_manager->register( new Widget_Image_Carousel() );
		$widget_manager->register( new Widget_Rich_Carousel() );
		$widget_manager->register( new Widget_Gallery_Justified_Content() );
		$widget_manager->register( new Widget_Banner() );
		$widget_manager->register( new Widget_Shapes() );
		$widget_manager->register( new Widget_Modern_Carousel() );
		$widget_manager->register( new Widget_Modern_Slider() );
		$widget_manager->register( new Widget_Flip_Box() );
		$widget_manager->register( new Widget_Blog() );
		$widget_manager->register( new Widget_FAQ_Cards() );
		$widget_manager->register( new Widget_Register_Form() );
		$widget_manager->register( new Widget_Portfolio() );
		$widget_manager->register( new Widget_Portfolio_Carousel() );
		$widget_manager->register( new Widget_Portfolio_Details() );
		$widget_manager->register( new Widget_Attribute_List() );
		$widget_manager->register( new Widget_List() );
		$widget_manager->register( new Widget_Gradation() );
		$widget_manager->register( new Widget_Timeline() );
		$widget_manager->register( new Widget_Pricing_Table() );
		$widget_manager->register( new Widget_Twitter() );
		$widget_manager->register( new Widget_Team_Member() );
		$widget_manager->register( new Widget_Team_Member_Carousel() );
		$widget_manager->register( new Widget_Testimonial_Carousel() );
		$widget_manager->register( new Widget_Testimonial_Carousel_3D() );
		$widget_manager->register( new Widget_Testimonial_Grid() );
		$widget_manager->register( new Widget_Social_Networks() );
		$widget_manager->register( new Widget_Star_Rating() );
		$widget_manager->register( new Widget_Popup_Video() );
		$widget_manager->register( new Widget_Separator() );
		$widget_manager->register( new Widget_Table() );
		$widget_manager->register( new Widget_Full_Page() );
		$widget_manager->register( new Widget_View_Demo() );

		/**
		 * Include & Register Dependency Widgets.
		 */
		if ( \Edumall_Woo::instance()->is_activated() ) {
			require_once EDUMALL_ELEMENTOR_DIR . '/widgets/posts/product.php';
			require_once EDUMALL_ELEMENTOR_DIR . '/widgets/product-categories.php';

			$widget_manager->register( new Widget_Product() );
			$widget_manager->register( new Widget_Product_Categories() );
		}

		if ( \Edumall_Tutor::instance()->is_activated() ) {
			require_once EDUMALL_ELEMENTOR_DIR . '/widgets/carousel/course-carousel.php';
			require_once EDUMALL_ELEMENTOR_DIR . '/widgets/carousel/course-category-carousel.php';
			require_once EDUMALL_ELEMENTOR_DIR . '/widgets/terms/course-category-cards.php';
			require_once EDUMALL_ELEMENTOR_DIR . '/widgets/posts/course.php';
			require_once EDUMALL_ELEMENTOR_DIR . '/widgets/posts/course-tabs.php';
			require_once EDUMALL_ELEMENTOR_DIR . '/widgets/form/course-search-form.php';

			$widget_manager->register( new Widget_Course() );
			$widget_manager->register( new Widget_Course_Tabs() );
			$widget_manager->register( new Widget_Course_Carousel() );
			$widget_manager->register( new Widget_Course_Category_Carousel() );
			$widget_manager->register( new Widget_Course_Category_Cards() );
			$widget_manager->register( new Widget_Course_Search_Form() );

			if ( class_exists( 'Edumall_Addons' ) ) {
				require_once EDUMALL_ELEMENTOR_DIR . '/widgets/terms/course-languages.php';

				$widget_manager->register( new Widget_Course_Languages() );
			}
		}

		if ( \Edumall_Event::instance()->is_activated() ) {
			require_once EDUMALL_ELEMENTOR_DIR . '/widgets/posts/event.php';
			require_once EDUMALL_ELEMENTOR_DIR . '/widgets/carousel/event-carousel.php';

			$widget_manager->register( new Widget_Event() );
			$widget_manager->register( new Widget_Event_Carousel() );
		}

		if ( \Edumall_Zoom_Meeting::instance()->is_activated() ) {
			require_once EDUMALL_ELEMENTOR_DIR . '/widgets/zoom-meeting.php';

			$widget_manager->register( new Widget_Zoom_Meeting() );
		}

		if ( defined( 'WPCF7_VERSION' ) ) {
			require_once EDUMALL_ELEMENTOR_DIR . '/widgets/form/contact-form-7.php';

			$widget_manager->register( new Widget_Contact_Form_7() );
		}

		if ( function_exists( 'mc4wp_get_forms' ) ) {
			require_once EDUMALL_ELEMENTOR_DIR . '/widgets/form/mailchimp-form.php';

			$widget_manager->register( new Widget_Mailchimp_Form() );
		}
	}

	public function change_portfolio_archive_title( $title ) {
		if ( \Edumall_Portfolio::instance()->is_archive() ) {
			$title = \Edumall::setting( 'portfolio_archive_title_bar_title' );
		}

		if ( '' === $title ) {
			$title = 'Archive Title';
		}

		return $title;
	}
}

Widget_Init::instance()->initialize();
