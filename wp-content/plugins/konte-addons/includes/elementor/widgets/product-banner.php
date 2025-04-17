<?php
namespace KonteAddons\Elementor\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Typography;
use Elementor\Utils;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Pricing Table widget
 */
class Product_Banner extends Widget_Base {
	/**
	 * Retrieve the widget name.
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'konte-product-banner';
	}

	/**
	 * Retrieve the widget title.
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __( 'Product Banner', 'konte-addons' );
	}

	/**
	 * Retrieve the widget icon.
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'eicon-product-description konte-elementor-widget';
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
		return [ 'product banner', 'banner', 'woocommerce', 'konte' ];
	}

	/**
	 * Register the widget controls.
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @access protected
	 */
	protected function register_controls() {
	   	$this->start_controls_section(
			'section_product_banner',
			[ 'label' => __( 'Product Banner', 'konte-addons' ) ]
		);

		$this->add_control(
			'product',
			[
				'label'       => __( 'Product ID', 'konte-addons' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => '',
			]
		);

		$this->add_control(
			'button_text',
			[
				'label'       => __( 'Button text', 'konte-addons' ),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => __( 'Button text', 'konte-addons' ),
			]
		);

		$this->add_control(
			'custom_image',
			[
				'label'     => __( 'Custom Image', 'konte-addons' ),
				'type'      => Controls_Manager::CHOOSE,
				'separator' => 'before',
				'default'   => 'none',
				'toggle'    => false,
				'options' => [
					'none' => [
						'title' => __( 'None', 'konte-addons' ),
						'icon'  => 'eicon-ban',
					],
					'library' => [
						'title' => __( 'Media Library', 'konte-addons' ),
						'icon'  => 'eicon-upload',
					],
					'external' => [
						'title' => __( 'External Image', 'konte-addons' ),
						'icon'  => 'eicon-editor-link',
					],
				],
			]
		);

		$this->add_control(
			'image',
			[
				'label'      => '',
				'type'       => Controls_Manager::MEDIA,
				'default'    => [
					'url' => Utils::get_placeholder_image_src(),
				],
				'show_label' => false,
				'condition' => [
					'custom_image' => 'library',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'name'      => 'image',
				'default'   => 'full',
				'condition' => [
					'custom_image' => 'library',
				],
			]
		);

		$this->add_control(
			'image_url',
			[
				'label' => __( 'Image URL', 'konte-addons' ),
				'type' => Controls_Manager::TEXT,
				'default' => 'https://via.placeholder.com/640x640.png?text=Place+Holder',
				'label_block' => true,
				'condition' => [
					'custom_image' => 'external',
				],
			]
		);

		$this->end_controls_section();

		// Style
		$this->start_controls_section(
			'section_product_style',
			[
				'label' => __( 'Product', 'konte-addons' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'color',
			[
				'label' => __( 'Color', 'konte-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .konte-product--elementor' => 'color: {{VALUE}}',
					'{{WRAPPER}} .konte-product--elementor a' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'label' => __( 'Title Typography', 'konte-addons' ),
				'name'     => 'title_typography',
				'selector' => '{{WRAPPER}} .konte-product-banner__title',
			]
		);

		$this->end_controls_section();
	}


	/**
	 * Render widget output on the frontend.
	 * Written in PHP and used to generate the final HTML.
	 */
	protected function render() {
		$settings   = $this->get_settings_for_display();
		$product_id = $settings['product'] ? $settings['product'] : $this->get_default_product_id();
		$_product   = wc_get_product( absint( $settings['product'] ) );

		if ( ! $_product ) {
			return;
		}

		$this->add_render_attribute( 'wrapper', 'class', [
			'woocommerce',
			'konte-product',
			'konte-product--elementor',
			'product-type-' . $_product->get_type(),
			'konte-product-banner',
		] );

		$this->add_render_attribute( 'button', [
			'href'             => $_product->add_to_cart_url(),
			'data-product_id'  => $_product->get_id(),
			'data-product_sku' => $_product->get_sku(),
			'aria-label'       => $_product->add_to_cart_description(),
			'data-quantity'    => '1',
			'rel'              => 'nofollow',
			'class'            => [
				'konte-product-banner__button',
				'underline-hover',
				'short-line',
				'add-to-cart',
				$_product->is_purchasable() && $_product->is_in_stock() ? 'add_to_cart_button' : '',
				$_product->supports( 'ajax_add_to_cart' ) ? 'ajax_add_to_cart' : '',
			],
		]);

		if ( 'library' == $settings['custom_image'] ) {
			$image = Group_Control_Image_Size::get_attachment_image_html( $settings );
		} elseif ( 'external' == $settings['custom_image'] ) {
			$image = '<img src="' . esc_url( $settings['image_url'] ) . '" alt="' . esc_attr( $_product->get_title() ) . '">';
		} else {
			$image = Group_Control_Image_Size::get_attachment_image_html( [
				'image' => [
					'id'  => $_product->get_image_id(),
					'url' => '',
				],
				'image_size' => $settings['image_size'],
				'image_custom_dimension' => $settings['image_custom_dimension'],
			] );
		}
		?>
		<div <?php echo $this->get_render_attribute_string( 'wrapper' ) ?>>
			<?php if ( ! empty( $image ) ) : ?>
				<a href="<?php echo esc_url( $_product->get_permalink() ); ?>" class="konte-product-banner__image product-image"><?php echo $image ?></a>
			<?php endif; ?>
			<div class="konte-product-banner__wrapper konte-product__wrapper">
				<a href="<?php echo esc_url( $_product->get_permalink() ); ?>" class="konte-product__hidden-url" rel="nofollow">
					<?php echo esc_html( $_product->get_title() ) ?>
				</a>
				<?php echo get_the_term_list( $_product->get_id(), 'product_cat', '<span class="product-cats">', ',', '</span>' ); ?>
				<h3 class="konte-product-banner__title product-title">
					<a href="<?php echo esc_url( $_product->get_permalink() ); ?>"><?php echo esc_html( $_product->get_title() ) ?></a>
				</h3>
				<p class="konte-product-banner__price product-price"><?php echo $_product->get_price_html(); ?></p>
				<a <?php echo $this->get_render_attribute_string( 'button' ) ?>><?php echo $settings['button_text'] ? esc_html( $settings['button_text'] ) : $_product->add_to_cart_text() ?></a>
			</div>
		</div>
		<?php
	}

	/**
	 * Get the latest product ID as the default
	 */
	protected function get_default_product_id() {
		$product_ids = get_posts('post_type=product&numberposts=1&fields=ids');

		return ! empty( $product_ids ) ? $product_ids[0] : 0;
	}
}
