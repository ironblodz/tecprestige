<?php
namespace KonteAddons\Elementor\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Image_Size;
use Elementor\Utils;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Pricing Table widget
 */
class Category_Banner extends Widget_Base {
	/**
	 * Retrieve the widget name.
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'konte-category-banner';
	}

	/**
	 * Retrieve the widget title.
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __( 'Category Banner', 'konte-addons' );
	}

	/**
	 * Retrieve the widget icon.
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'eicon-single-page konte-elementor-widget';
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
	 *
	 * Retrieve the list of keywords the widget belongs to.
	 *
	 * @return array Widget keywords.
	 */
	public function get_keywords() {
		return [ 'category banner', 'banner', 'konte' ];
	}

	/**
	 * Register the widget controls.
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @access protected
	 */
	protected function register_controls() {
		$this->start_controls_section(
			'section_banner',
			[ 'label' => __( 'Category Banner', 'konte-addons' ) ]
		);

		$this->add_control(
			'image_source',
			[
				'label'   => esc_html__( 'Image Source', 'konte-addons' ),
				'type'    => Controls_Manager::CHOOSE,
				'default' => 'library',
				'options' => [
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
				'label'   => esc_html__( 'Banner Image', 'konte-addons' ),
				'type'    => Controls_Manager::MEDIA,
				'default' => [
					'url' => Utils::get_placeholder_image_src(),
				],
				'condition' => [
					'image_source' => 'library'
				],
			]
		);

		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'name'      => 'image',
				// Usage: `{name}_size` and `{name}_custom_dimension`, in this case `image_size` and `image_custom_dimension`.
				'default'   => 'full',
				'condition' => [
					'image_source' => 'library'
				],
			]
		);

		$this->add_control(
			'image_url',
			[
				'label' => __( 'Image URL', 'konte-addons' ),
				'type' => Controls_Manager::TEXT,
				'default' => 'https://via.placeholder.com/640x400.png?text=Place+Holder',
				'label_block' => true,
				'condition' => [
					'image_source' => 'external'
				],
			]
		);

		$this->add_control(
			'enable_sub_image', [
				'label'   => esc_html__( 'Sub Image', 'konte-addons' ),
				'type'    => Controls_Manager::SWITCHER,
			]
		);

		$this->add_control(
			'sub_image', [
				'type'    => Controls_Manager::MEDIA,
				'description' => esc_html__( 'The size of this image is fixed to 200x200.', 'konte-addons' ),
				'condition' => [
					'enable_sub_image' => 'yes',
					'image_source' => 'library',
				],
			]
		);

		$this->add_control(
			'sub_image_url', [
				'type'    => Controls_Manager::TEXT,
				'description' => esc_html__( 'The size of this image is fixed to 200x200.', 'konte-addons' ),
				'default' => 'https://via.placeholder.com/200x200.png?text=Place+Holder',
				'label_block' => true,
				'condition' => [
					'enable_sub_image' => 'yes',
					'image_source' => 'external',
				],
			]
		);

		$this->add_control(
			'cat_name',
			[
				'label' => __( 'Category Name', 'konte-addons' ),
				'type' => Controls_Manager::TEXT,
				'default' => __( 'Category Name', 'konte-addons' ),
				'label_block' => true,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'title',
			[
				'label' => __( 'Title', 'konte-addons' ),
				'type' => Controls_Manager::TEXT,
				'default' => __( 'This is the title', 'konte-addons' ),
				'label_block' => true,
			]
		);

		$this->add_control(
			'title_position',
			[
				'label' => __( 'Position', 'konte-addons' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'bottom' => [
						'title' => __( 'Bottom', 'konte-addons' ),
						'icon'  => 'eicon-v-align-bottom',
					],
					'middle' => [
						'title' => __( 'Middle', 'konte-addons' ),
						'icon'  => 'eicon-v-align-middle',
					],
				],
				'default' => 'bottom',
				'toggle'  => false,
			]
		);

		$this->add_control(
			'button_text',
			[
				'label'       => __( 'Button Text', 'konte-addons' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => __( 'Click here', 'konte-addons' ),
				'placeholder' => __( 'Button text', 'konte-addons' ),
				'separator'   => 'before',
			]
		);

		$this->add_control(
			'link',
			[
				'label'       => __( 'Link', 'konte-addons' ),
				'type'        => Controls_Manager::URL,
				'placeholder' => __( 'https://your-link.com', 'konte-addons' ),
				'default'     => [
					'url' => '#',
				],
				'dynamic' => [
					'active' => true,
				],
			]
		);

		$this->add_control(
			'align',
			[
				'label'   => __( 'Alignment', 'konte-addons' ),
				'type'    => Controls_Manager::CHOOSE,
				'options' => [
					'left' => [
						'title' => __( 'Left', 'konte-addons' ),
						'icon'  => 'eicon-h-align-left',
					],
					'right' => [
						'title' => __( 'Right', 'konte-addons' ),
						'icon'  => 'eicon-h-align-right',
					],
				],
				'default'   => 'left',
				'toggle'    => false,
				'separator' => 'before',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_banner_style',
			[
				'label' => __( 'Category Banner', 'konte-addons' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'cat_name_style_heading',
			[
				'label' => __( 'Category Name', 'konte-addons' ),
				'type' => Controls_Manager::HEADING,
			]
		);

		$this->add_control(
			'cat_color',
			[
				'label' => __( 'Color', 'konte-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .konte-category-banner__category' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'cat_typography',
				'selector' => '{{WRAPPER}} .konte-category-banner__category',
			]
		);

		$this->add_control(
			'title_style_heading',
			[
				'label' => __( 'Title', 'konte-addons' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'title_color',
			[
				'label' => __( 'Color', 'konte-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .konte-category-banner__title' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'title_typography',
				'selector' => '{{WRAPPER}} .konte-category-banner__title',
			]
		);

		$this->add_control(
			'button_style_heading',
			[
				'label' => __( 'Button', 'konte-addons' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'button_color',
			[
				'label' => __( 'Color', 'konte-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .konte-category-banner__button' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'button_typography',
				'selector' => '{{WRAPPER}} .konte-category-banner__button',
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
			'konte-category-banner',
			'align-' . $settings['align'],
			'title-' . $settings['title_position'],
		] );
		$this->add_render_attribute( 'title', 'class', 'konte-category-banner__title' );
		$this->add_render_attribute( 'link', 'class', 'konte-category-banner__link' );

		$tag = 'span';
		$sub_image = '';

		if ( ! empty( $settings['link']['url'] ) ) {
			$this->add_link_attributes( 'link', $settings['link'] );

			$tag = 'a';
		}

		$this->add_inline_editing_attributes( 'title', 'none' );

		if ( 'library' == $settings['image_source'] ) {
			$image = Group_Control_Image_Size::get_attachment_image_html( $settings );

			if ( $settings['enable_sub_image'] ) {
				$sub_image = Group_Control_Image_Size::get_attachment_image_html( [
					'sub_image' => [
						'id'  => $settings['sub_image']['id'],
						'url' => $settings['sub_image']['url'],
					],
					'sub_image_size'             => 'custom',
					'sub_image_custom_dimension' => [
						'width'  => '200',
						'height' => '200',
					],
				], 'sub_image' );
			}
		} else {
			$image = '<img src="' . esc_url( $settings['image_url'] ) . '" alt="' . esc_attr( $settings['title'] ) . '">';

			if ( $settings['enable_sub_image'] ) {
				$sub_image = '<img src="' . esc_attr( $settings['sub_image_url'] ) . '" alt="' . esc_attr( $settings['title'] ) . '" width="200">';
			}
		}

		if ( $sub_image ) {
			$this->add_render_attribute( 'wrapper', 'class', ['has-sub-image'] );
		}
		?>
		<div <?php echo $this->get_render_attribute_string( 'wrapper' ); ?>>
			<<?php echo $tag; ?> <?php echo $this->get_render_attribute_string( 'link' ); ?>>
				<?php if ( $settings['cat_name'] ) : ?>
					<span class="konte-category-banner__category"><?php echo esc_html( $settings['cat_name'] ); ?></span>
				<?php endif; ?>
				<span class="konte-category-banner__image">
					<?php
					echo $image;
					echo $sub_image ? '<span class="konte-category-banner__sub-image">' . $sub_image . '</span>' : '';
					?>
				</span>
				<span class="konte-category-banner__content">
					<h4 <?php echo $this->get_render_attribute_string( 'title' ); ?>><?php echo wp_kses_post( $settings['title'] ); ?></h4>
					<?php if ( $settings['button_text'] ) : ?>
						<span class="konte-category-banner__button"><?php echo esc_html( $settings['button_text'] ); ?></span>
					<?php endif; ?>
				</span>
			</<?php echo $tag; ?>>
		</div>
		<?php
	}

	/**
	 * Render widget output in the editor.
	 * Written as a Backbone JavaScript template and used to generate the live preview.
	 */
	protected function content_template() {
		?>
		<#
		view.addRenderAttribute( 'wrapper', 'class', [
			'konte-category-banner',
			'align-' + settings.align,
			'title-' + settings.title_position,
		] );

		view.addRenderAttribute( 'category', 'class', 'konte-category-banner__category' );
		view.addRenderAttribute( 'title', 'class', 'konte-category-banner__title' );
		view.addRenderAttribute( 'link', 'class', 'konte-category-banner__link' );

		view.addInlineEditingAttributes( 'title', 'none' );

		var tag = 'span';

		if ( settings.link.href ) {
			tag = 'a';

			view.addRenderAttribute( 'link', 'href', settings.link.href );

			if ( settings.link.is_external ) {
				view.addRenderAttribute( 'link', 'target', '_blank' );
			}

			if ( settings.link.nofollow ) {
				view.addRenderAttribute( 'link', 'rel', 'nofollow' );
			}
		}

		var imageUrl = '',
			subImageUrl = '';

		if ( 'library' === settings.image_source ) {
			imageUrl = elementor.imagesManager.getImageUrl( {
				id: settings.image.id,
				url: settings.image.url,
				size: settings.image_size,
				dimension: settings.image_custom_dimension,
				model: view.getEditModel()
			} );

			if ( settings.enable_sub_image ) {
				subImageUrl = elementor.imagesManager.getImageUrl( {
					id: settings.sub_image.id,
					url: settings.sub_image.url,
					size: settings.sub_image_size,
					dimension: settings.sub_image_custom_dimension,
					model: view.getEditModel()
				} );
			}
		} else {
			imageUrl = settings.image_url;

			if ( settings.enable_sub_image ) {
				subImageUrl = settings.sub_image_url;
			}
		}
		#>
		<div {{{ view.getRenderAttributeString( 'wrapper' ) }}}>
			<{{{ tag }}} {{{ view.getRenderAttributeString( 'link' ) }}}>
				<# if ( settings.cat_name ) { #>
					<span {{{ view.getRenderAttributeString( 'category' ) }}}>{{{ settings.cat_name }}}</span>
				<# } #>
				<span class="konte-category-banner__image">
					<# if ( imageUrl ) { #>
						<img src="{{ imageUrl }}">
					<# } #>
					<# if ( subImageUrl ) { #>
						<span class="konte-category-banner__sub-image">
							<img src="{{ subImageUrl }}">
						</span>
					<# }#>
				</span>
				<span class="konte-category-banner__content">
					<h4 {{{ view.getRenderAttributeString( 'title' ) }}}>{{{ settings.title }}}</h4>
					<# if ( settings.button_text ) { #>
						<span class="konte-category-banner__button">{{{ settings.button_text }}}</span>
					<# } #>
				</span>
			</{{{ tag }}}>
		</div>
		<?php
	}
}
