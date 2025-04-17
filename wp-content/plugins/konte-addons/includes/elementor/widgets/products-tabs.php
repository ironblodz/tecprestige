<?php
namespace KonteAddons\Elementor\Widgets;

use KonteAddons\Elementor\Base\Products_Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Pricing Table widget
 */
class Products_Tabs extends Products_Widget_Base {
	/**
	 * Retrieve the widget name.
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'konte-products-tabs';
	}

	/**
	 * Retrieve the widget title.
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __( 'Products Tabs', 'konte-addons' );
	}

	/**
	 * Retrieve the widget icon.
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'eicon-product-tabs konte-elementor-widget';
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
		return [ 'product tabs', 'products', 'tabs', 'grid', 'woocommerce', 'konte' ];
	}

	/**
	 * Register the widget controls.
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @access protected
	 */
	protected function register_controls() {
	   	$this->start_controls_section(
			'section_product_tabs',
			[ 'label' => __( 'Product Tabs', 'konte-addons' ) ]
		);

		$this->register_products_controls( [
			'limit' => 8,
		] );

		$this->add_control(
			'columns',
			[
				'label' => __( 'Columns', 'konte-addons' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					3 => __( '3 Columns', 'konte-addons' ),
					4 => __( '4 Columns', 'konte-addons' ),
					5 => __( '5 Columns', 'konte-addons' ),
				],
				'default' => 4,
			]
		);

		$this->add_control(
			'tabs_type',
			[
				'label'   => esc_html__( 'Tabs Type', 'konte-addons' ),
				'type'    => Controls_Manager::SELECT,
				'options' => [
					'category' => esc_html__( 'Categories', 'konte-addons' ),
					'tag'      => esc_html__( 'Tags', 'konte-addons' ),
					'groups'   => esc_html__( 'Groups', 'konte-addons' )
				],
				'default'   => 'groups',
				'toggle'    => false,
				'separator' => 'before',
			]
		);

		$repeater = new \Elementor\Repeater();

		$repeater->add_control(
			'title',
			[
				'label'       => esc_html__( 'Title', 'konte-addons' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'This is heading', 'konte-addons' ),
				'label_block' => true,
			]
		);

		$repeater->add_control(
			'type',
			[
				'label'   => esc_html__( 'Products', 'konte-addons' ),
				'type'    => Controls_Manager::SELECT,
				'options' => $this->get_options_product_type(),
				'default' => 'recent_products',
				'toggle'  => false,
			]
		);

		$repeater->add_control(
			'orderby',
			[
				'label' => __( 'Order By', 'konte-addons' ),
				'type' => Controls_Manager::SELECT,
				'options' => $this->get_options_product_orderby(),
				'default' => 'menu_order',
				'condition' => [
					'type' => ['featured', 'sale']
				],
			]
		);

		$repeater->add_control(
			'order',
			[
				'label' => __( 'Order', 'konte-addons' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'ASC'  => __( 'Ascending', 'konte-addons' ),
					'DESC' => __( 'Descending', 'konte-addons' ),
				],
				'default' => 'ASC',
				'condition' => [
					'type' => ['featured', 'sale'],
					'orderby!' => ['', 'rand'],
				],
			]
		);

		$this->add_control(
			'groups',
			[
				'label'         => '',
				'type'          => Controls_Manager::REPEATER,
				'fields'        => $repeater->get_controls(),
				'default'       => [
					[
						'title' => esc_html__( 'New Arrivals', 'konte-addons' ),
						'type'  => 'recent_products'
					],
					[
						'title' => esc_html__( 'Best Sellers', 'konte-addons' ),
						'type'  => 'best_selling_products'
					],
					[
						'title' => esc_html__( 'Sale Products', 'konte-addons' ),
						'type'  => 'sale_products'
					]
				],
				'title_field'   => '{{{ title }}}',
				'prevent_empty' => false,
				'condition'     => [
					'tabs_type' => 'groups',
				],
			]
		);

		// Product Cats
		$repeater = new \Elementor\Repeater();

		$repeater->add_control(
			'category', [
				'label'       => esc_html__( 'Category', 'konte-addons' ),
				'type'        => Controls_Manager::SELECT2,
				'options' 	  => \KonteAddons\Elementor\Utils::get_terms_options( 'product_cat' ),
				'label_block' => true,
			]
		);

		$this->add_control(
			'category_tabs',
			[
				'label'         => esc_html__( 'Categories', 'konte-addons' ),
				'type'          => Controls_Manager::REPEATER,
				'fields'        => $repeater->get_controls(),
				'default'       => [ ],
				'prevent_empty' => false,
				'condition'     => [
					'tabs_type' => 'category',
				],
				'title_field'   => '{{{ category }}}',
			]
		);

		// Product Tag
		$repeater = new \Elementor\Repeater();

		$repeater->add_control(
			'tag', [
				'label'       => esc_html__( 'Tag', 'konte-addons' ),
				'type'        => Controls_Manager::SELECT2,
				'options'     => \KonteAddons\Elementor\Utils::get_terms_options( 'product_tag' ),
				'label_block' => true,
			]
		);

		$this->add_control(
			'tag_tabs',
			[
				'label'         => esc_html__( 'Tags', 'konte-addons' ),
				'type'          => Controls_Manager::REPEATER,
				'fields'        => $repeater->get_controls(),
				'default'       => [ ],
				'prevent_empty' => false,
				'condition'     => [
					'tabs_type' => 'tag',
				],
				'title_field'   => '{{{ tag }}}',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_products_tabs',
			[
				'label' => esc_html__( 'Products Tabs', 'konte-addons' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'tabs_style_heading',
			[
				'label'     => __( 'Tabs', 'konte-addons' ),
				'type'      => Controls_Manager::HEADING,
			]
		);

		$this->add_control(
			'tab_color',
			[
				'label' => __( 'Color', 'konte-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .konte-tabs__nav li' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'tab_typography',
				'selector' => '{{WRAPPER}} .konte-tabs__nav li',
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
			'konte-product-tabs',
			'konte-product-tabs--elementor',
			'konte-product-tabs__' . $settings['tabs_type'],
			'konte-tabs',
			'konte-tabs--elementor',
		] );

		$tabs = $this->get_tabs_data();
		$query_args = [];

		if ( empty( $tabs ) ) {
			return;
		}

		$this->add_render_attribute( 'panel', 'class', [
			'konte-product-grid',
			'konte-products',
			'konte-product-tabs__panel',
			'konte-tabs__panel',
			'active',
		] );

		$this->add_render_attribute( 'panel', 'data-panel', '1' );
		?>
		<div <?php echo $this->get_render_attribute_string( 'wrapper' ); ?>>
			<ul class="konte-product-tabs__tabs konte-tabs__nav">
				<?php foreach ( $tabs as $key => $tab ) : ?>
					<?php
					$tab_key = $this->get_repeater_setting_key( 'tab', 'products_tab', $key );
					$this->add_render_attribute( $tab_key, [
						'data-target' => $tab['index'],
						'data-atts'   => json_encode( $tab['args'] ),
					] );

					if ( 1 === $tab['index'] ) {
						$this->add_render_attribute( $tab_key, 'class', 'active' );
						$query_args = $tab['args'];
					}
					?>
					<li <?php echo $this->get_render_attribute_string( $tab_key ) ?>><?php echo esc_html( $tab['title'] ); ?></li>
				<?php endforeach; ?>
			</ul>
			<div class="konte-product-tabs__panels konte-tabs__panels">
				<div <?php echo $this->get_render_attribute_string( 'panel' ) ?>>
					<?php $this->render_products( $query_args ); ?>
				</div>
			</div>
		</div>
		<?php
	}

	/**
	 * Get the tabs data.
	 *
	 * @return array
	 */
	protected function get_tabs_data() {
		$settings = $this->get_settings_for_display();
		$index = 1;
		$tabs  = [];

		switch ( $settings['tabs_type'] ) {
			case 'category' :
			case 'tag':
				$tabs_type = $settings[ 'tabs_type' ];
				$taxonomy  = 'category' == $tabs_type ? 'product_cat' : 'product_tag';
				$tabs_key  = $tabs_type . '_tabs';

				if ( empty( $settings[ $tabs_key ] ) ) {
					break;
				}

				foreach( $settings[ $tabs_key ] as $i => $tab ) {
					if ( empty( $tab[ $tabs_type ] ) ) {
						continue;
					}

					$term = get_term_by( 'slug', $tab[ $tabs_type ], $taxonomy );

					if ( ! $term || is_wp_error( $term ) ) {
						continue;
					}

					$args = $this->parse_settings( $tab );
					$args['limit'] = $settings['limit'];
					$args['columns'] = isset( $settings['columns'] ) ? $settings['columns'] : 4;
					unset( $args['title'] );

					$tabs[ $term->slug ] = [
						'index' => $index++,
						'args'  => $args,
						'title' => $term->name,
					];
				}

				break;

			case 'groups' :
				if ( empty( $settings['groups'] ) ) {
					break;
				}

				foreach( $settings['groups'] as $i => $tab ) {
					$args = $this->parse_settings( $tab );
					$args['limit'] = $settings['limit'];
					$args['columns'] = isset( $settings['columns'] ) ? $settings['columns'] : 4;
					unset( $args['title'] );

					$tabs[ $tab['type'] . $i ] = [
						'index' => $index++,
						'args'  => $args,
						'title' => $tab['title'],
					];
				}

				break;
		}

		return $tabs;
	}
}
