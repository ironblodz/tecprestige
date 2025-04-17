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
class Testimonial extends Widget_Base {
	/**
	 * Retrieve the widget name.
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'konte-testimonial';
	}

	/**
	 * Retrieve the widget title.
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __( 'Testimonial', 'konte-addons' );
	}

	/**
	 * Retrieve the widget icon.
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'eicon-testimonial konte-elementor-widget';
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
		return [ 'testimonial', 'blockquote', 'konte' ];
	}

	/**
	 * Register the widget controls.
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @access protected
	 */
	protected function register_controls() {
		$this->start_controls_section(
			'section_testimonial',
			[ 'label' => __( 'Testimonial', 'konte-addons' ) ]
		);

		$this->add_control(
			'testimonial_content',
			[
				'label' => __( 'Content', 'konte-addons' ),
				'type' => Controls_Manager::TEXTAREA,
				'dynamic' => [
					'active' => true,
				],
				'rows' => '10',
				'default' => __( 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.', 'konte-addons' ),
			]
		);

		$this->add_control(
			'testimonial_image',
			[
				'label' => __( 'Choose Image', 'konte-addons' ),
				'type' => Controls_Manager::MEDIA,
				'dynamic' => [
					'active' => true,
				],
				'default' => [
					'url' => KONTE_ADDONS_URL . '/assets/images/person.jpg',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'name'      => 'testimonial_image',
				// Usage: `{name}_size` and `{name}_custom_dimension`, in this case `image_size` and `image_custom_dimension`.
				'default'   => 'full',
				'separator' => 'none',
			]
		);

		$this->add_control(
			'testimonial_name',
			[
				'label' => __( 'Name', 'konte-addons' ),
				'type' => Controls_Manager::TEXT,
				'default' => __( 'John Doe', 'konte-addons' ),
				'label_block' => true,
				'separator' => 'before',
				'dynamic' => [
					'active' => true,
				],
			]
		);

		$this->add_control(
			'testimonial_company',
			[
				'label' => __( 'Company/Title', 'konte-addons' ),
				'type' => Controls_Manager::TEXT,
				'default' => __( 'Company Name', 'konte-addons' ),
				'label_block' => true,
				'dynamic' => [
					'active' => true,
				],
			]
		);

		$this->end_controls_section();

		// Style.
		$this->start_controls_section(
			'section_testimonial_style',
			[
				'label' => __( 'Testimonial', 'konte-addons' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		// Content.
		$this->add_control(
			'content_style_heading',
			[
				'label'     => __( 'Content', 'konte-addons' ),
				'type'      => Controls_Manager::HEADING,
			]
		);

		$this->add_control(
			'content_color',
			[
				'label' => __( 'Color', 'konte-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .konte-testimonial__content' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'content_typography',
				'selector' => '{{WRAPPER}} .konte-testimonial__content',
			]
		);

		// Name.
		$this->add_control(
			'name_style_heading',
			[
				'label'     => __( 'Name', 'konte-addons' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'name_color',
			[
				'label' => __( 'Color', 'konte-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .konte-testimonial__name' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'name_typography',
				'selector' => '{{WRAPPER}} .konte-testimonial__name',
			]
		);

		// Company.
		$this->add_control(
			'company_style_heading',
			[
				'label'     => __( 'Company', 'konte-addons' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'company_color',
			[
				'label' => __( 'Color', 'konte-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .konte-testimonial__company' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'company_typography',
				'selector' => '{{WRAPPER}} .konte-testimonial__company',
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Render widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();

		if ( $settings['testimonial_image']['url'] ) {
			$image = Group_Control_Image_Size::get_attachment_image_html( $settings, 'testimonial_image' );
		} else {
			$image = '<img src="' . KONTE_ADDONS_URL . '/assets/images/person.jpg" alt="' . esc_attr( $settings['testimonial_name'] ) . '">';
		}

		$this->add_render_attribute( 'wrapper', 'class', ['konte-testimonial', 'konte-testimonial--elementor'] );
		$this->add_render_attribute( 'testimonial_image', 'class', ['konte-testimonial__photo'] );
		$this->add_render_attribute( 'testimonial_name', 'class', ['konte-testimonial__name'] );
		$this->add_render_attribute( 'testimonial_company', 'class', ['konte-testimonial__company'] );
		$this->add_render_attribute( 'testimonial_content', 'class', ['konte-testimonial__content'] );

		$this->add_inline_editing_attributes( 'testimonial_name', 'none' );
		$this->add_inline_editing_attributes( 'testimonial_company', 'none' );
		$this->add_inline_editing_attributes( 'testimonial_content', 'basic' );
		?>
		<div <?php echo $this->get_render_attribute_string( 'wrapper' ); ?>>
			<div <?php echo $this->get_render_attribute_string( 'testimonial_image' ); ?>>
				<?php echo $image; ?>
			</div>
			<div class="konte-testimonial__entry">
				<div <?php echo $this->get_render_attribute_string( 'testimonial_content' ); ?>>
					<?php echo wp_kses_post( $settings['testimonial_content'] ); ?>
				</div>
				<div class="konte-testimonial__author">
					<span <?php echo $this->get_render_attribute_string( 'testimonial_name' ) ?>><?php echo esc_html( $settings['testimonial_name'] ) ?></span>
					<span class="konte-testimonial__author-separator">-</span>
					<span <?php echo $this->get_render_attribute_string( 'testimonial_company' ) ?>><?php echo esc_html( $settings['testimonial_company'] ) ?></span>
				</div>
			</div>
		</div>
		<?php
	}

	/**
	 * Render widget output in the editor.
	 *
	 * Written as a Backbone JavaScript template and used to generate the live preview.
	 */
	protected function content_template() {
		?>
		<#
		view.addRenderAttribute( 'wrapper', 'class', 'konte-testimonial' );
		view.addRenderAttribute( 'testimonial_image', 'class', 'konte-testimonial__photo' );
		view.addRenderAttribute( 'testimonial_name', 'class', 'konte-testimonial__name' );
		view.addRenderAttribute( 'testimonial_company', 'class', 'konte-testimonial__company' );
		view.addRenderAttribute( 'testimonial_content', 'class', 'konte-testimonial__content' );

		view.addInlineEditingAttributes( 'testimonial_name', 'none' );
		view.addInlineEditingAttributes( 'testimonial_company', 'none' );
		view.addInlineEditingAttributes( 'testimonial_content', 'basic' );

		var imageUrl = '<?php echo KONTE_ADDONS_URL . '/assets/images/person.jpg' ?>';

		if ( settings.testimonial_image.url ) {
			var imageSettings = {
				id: settings.testimonial_image.id,
				url: settings.testimonial_image.url,
				size: settings.testimonial_image_size,
				dimension: settings.testimonial_image_custom_dimension,
				model: view.getEditModel()
			};

			imageUrl = elementor.imagesManager.getImageUrl( imageSettings );
		}
		#>
		<div {{{ view.getRenderAttributeString( 'wrapper' ) }}}>
			<div {{{ view.getRenderAttributeString( 'testimonial_image' ) }}}>
				<img src="{{ imageUrl }}">
			</div>
			<div class="konte-testimonial__entry">
				<div {{{ view.getRenderAttributeString( 'testimonial_content' ) }}}>{{{ settings.testimonial_content }}}</div>
				<div class="konte-testimonial__author">
					<span {{{ view.getRenderAttributeString( 'testimonial_name' ) }}}>{{{ settings.testimonial_name }}}</span>
					<span class="konte-testimonial__author-separator">-</span>
					<span {{{ view.getRenderAttributeString( 'testimonial_company' ) }}}>{{{ settings.testimonial_company }}}</span>
				</div>
			</div>
		</div>
		<?php
	}
}
