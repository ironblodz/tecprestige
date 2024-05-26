<?php
namespace KonteAddons\Elementor\Widgets;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use KonteAddons\Elementor\Base\Products_Widget_Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Pricing Table widget
 */
class Products_Masonry extends Products_Widget_Base {
	/**
	 * Retrieve the widget name.
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'konte-products-masonry';
	}

	/**
	 * Retrieve the widget title.
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __( '[Konte] Products Masonry', 'konte-addons' );
	}

	/**
	 * Retrieve the widget icon.
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'eicon-posts-masonry';
	}

	/**
	 * Retrieve the list of categories the widget belongs to.
	 *
	 * @return array Widget categories.
	 */
	public function get_categories() {
		return ['konte'];
	}

	/**
	 * Get widget keywords.
	 * Retrieve the list of keywords the widget belongs to.
	 *
	 * @return array Widget keywords.
	 */
	public function get_keywords() {
		return [ 'products masonry', 'products', 'masonry', 'woocommerce', 'konte' ];
	}

	/**
	 * Register the widget controls.
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @access protected
	 */
	protected function register_controls() {
		$this->start_controls_section(
			'section_products_masonry',
			[
				'label' => __( 'Products Masonry', 'konte-addons' ),
			]
		);

		$this->register_products_controls( 'all' );

		$this->update_control(
			'limit',
			[
				'default'   => 4,
			]
		);

		$this->add_control(
			'columns',
			[
				'label' => __( 'Columns', 'konte-addons' ),
				'type' => Controls_Manager::HIDDEN,
				'default' => '4',
			]
		);

		$this->add_control(
			'prepend_title_block',
			[
				'label' => __( 'Heading Block', 'konte-addons' ),
				'description' => __( 'Add a heading block as the the first item', 'konte-addons' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'separator' => 'before',
			]
		);

		$this->add_control(
			'title',
			[
				'label' => __( 'Title & Description', 'konte-addons' ),
				'type' => Controls_Manager::TEXT,
				'default' => __( 'This is the heading', 'konte-addons' ),
				'placeholder' => __( 'Enter your heading', 'konte-addons' ),
				'label_block' => true,
				'condition' => [
					'prepend_title_block' => 'yes',
				],
			]
		);

		$this->add_control(
			'description',
			[
				'label'       => __( 'Description', 'konte-addons' ),
				'type'        => Controls_Manager::TEXTAREA,
				'default'     => __( 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.', 'konte-addons' ),
				'placeholder' => __( 'Enter your description', 'konte-addons' ),
				'rows'        => 10,
				'show_label'  => false,
				'condition' => [
					'prepend_title_block' => 'yes',
				],
			]
		);

		$this->end_controls_section();

		// Style section.
		$this->start_controls_section(
			'section_style_products_masonry',
			[
				'label' => __( 'Title and Description', 'konte-addons' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		// Title.
		$this->add_control(
			'title_style_heading',
			[
				'label'     => __( 'Title', 'konte-addons' ),
				'type'      => Controls_Manager::HEADING,
				'condition' => [
					'prepend_title_block' => 'yes',
				],
			]
		);

		$this->add_control(
			'title_color',
			[
				'label' => __( 'Color', 'konte-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .konte-product-masonry__title' => 'color: {{VALUE}}',
					'{{WRAPPER}} .konte-product-masonry__head .konte-dash' => 'color: {{VALUE}}',
				],
				'condition' => [
					'prepend_title_block' => 'yes',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'title_typography',
				'selector' => '{{WRAPPER}} .konte-product-masonry__title',
				'condition' => [
					'prepend_title_block' => 'yes',
				],
			]
		);

		// Desc.
		$this->add_control(
			'description_style_heading',
			[
				'label'     => __( 'Description', 'konte-addons' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'prepend_title_block' => 'yes',
				],
			]
		);

		$this->add_control(
			'description_color',
			[
				'label' => __( 'Color', 'konte-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .konte-product-masonry__description'   => 'color: {{VALUE}}',
				],
				'condition' => [
					'prepend_title_block' => 'yes',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'description_typography',
				'selector' => '{{WRAPPER}} .konte-product-masonry__description',
				'condition' => [
					'prepend_title_block' => 'yes',
				],
			]
		);

		$this->add_control(
			'products_style_heading',
			[
				'label'     => __( 'Products', 'konte-addons' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'product_color',
			[
				'label' => __( 'Color', 'konte-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} li.product, {{WRAPPER}} li.product a:not(.add-to-wishlist-button)' => 'color: {{VALUE}}',
					'{{WRAPPER}} ul.products:not(.hover-simple) .product-inner:hover a' => 'color: #161619',
					'{{WRAPPER}} .konte-tabs__panels:after' => 'color: {{VALUE}}',
				],
			]
		);

		$this->end_controls_section();
	}


	/**
	 * Render widget output on the frontend.
	 * Written in PHP and used to generate the final HTML.
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();

		$this->add_render_attribute( 'wrapper', 'class', [
			'konte-products-masonry',
			'konte-products',
			'konte-product-masonry--elementor',
		] );

		$content = $this->get_products_loop_content();

		if ( $settings['prepend_title_block'] && ! empty( $settings['title'] ) || ! empty( $settings['description'] ) ) {
			$this->add_render_attribute( 'wrapper', 'class', ['has-heading', 'konte-product-masonry--has-heading'] );

			$desc = ! empty( $settings['description'] ) ? '<div class="konte-product-masonry__description">' . $settings['description'] . '</div>' : '';
			$title = '<li class="product konte-product-masonry__head"><div class="konte-dash text-default"><span class="konte-dash__line"></span></div><h2 class="konte-product-masonry__title">' . $settings['title'] . '</h2>' . $desc . '</li>';
			$content = preg_replace( '/<ul class=["\']products[^>]+>/i', '$0' . $title, $content );
		}
		?>
		<div <?php echo $this->get_render_attribute_string( 'wrapper' ) ?>>
			<?php echo $content; ?>
		</div>
		<?php
	}
}