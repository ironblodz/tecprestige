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
class Countdown_Banner extends Widget_Base {
	/**
	 * Retrieve the widget name.
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'konte-countdown-banner';
	}

	/**
	 * Retrieve the widget title.
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __( 'Countdown Banner', 'konte-addons' );
	}

	/**
	 * Retrieve the widget icon.
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'eicon-number-field konte-elementor-widget';
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
		return [ 'countdown banner',  'banner', 'countdown', 'konte' ];
	}

	/**
	 * Register the widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @access protected
	 */
	protected function register_controls() {
		// Banner.
		$this->start_controls_section(
			'section_banner',
			[ 'label' => __( 'Banner', 'konte-addons' ) ]
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
				'label'   => esc_html__( 'Choose Image', 'konte-addons' ),
				'type'    => Controls_Manager::MEDIA,
				'default' => [
					'url' => Utils::get_placeholder_image_src(),
				],
				'condition' => [
					'image_source' => 'library',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'name'      => 'image',
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
					'image_source' => 'external',
				],
			]
		);

		$this->add_control(
			'tagline',
			[
				'label'       => __( 'Tagline', 'konte-addons' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => __( 'Tagline', 'konte-addons' ),
				'label_block' => true,
				'separator'   => 'before',
			]
		);
		$this->add_control(
			'title',
			[
				'label'       => __( 'Title', 'konte-addons' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => __( 'This is the title', 'konte-addons' ),
				'label_block' => true,
			]
		);

		$this->add_control(
			'button_text',
			[
				'label'       => __( 'Button Text', 'konte-addons' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => __( 'Click here', 'konte-addons' ),
				'placeholder' => __( 'Button text', 'konte-addons' ),
				'separator'   => 'before'
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
			]
		);
		$this->end_controls_section();

		$this->start_controls_section(
			'section_date',
			[ 'label' => __( 'Countdown', 'konte-addons' ) ]
		);

		$this->add_control(
			'date',
			[
				'label' => __( 'Date', 'konte-addons' ),
				'type' => Controls_Manager::DATE_TIME,
				'default' => date( 'Y/m/d', strtotime( '+5 days' ) ),
				'picker_options' => [
					'dateFormat' => 'Y/m/d H:i:S',
				],
			]
		);

		$this->add_control(
			'labels_heading',
			[
				'label' => __( 'Labels', 'konte-addons' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'day_text',
			[
				'label' => __( 'Day', 'konte-addons' ),
				'type' => Controls_Manager::TEXT,
				'default' => __( 'Days', 'konte-addons' ),
				'label_block' => false,
			]
		);

		$this->add_control(
			'hour_text',
			[
				'label' => __( 'Hour', 'konte-addons' ),
				'type' => Controls_Manager::TEXT,
				'default' => __( 'Hours', 'konte-addons' ),
				'label_block' => false,
			]
		);

		$this->add_control(
			'min_text',
			[
				'label' => __( 'Minute', 'konte-addons' ),
				'type' => Controls_Manager::TEXT,
				'default' => __( 'Minutes', 'konte-addons' ),
				'label_block' => false,
			]
		);

		$this->add_control(
			'sec_text',
			[
				'label' => __( 'Second', 'konte-addons' ),
				'type' => Controls_Manager::TEXT,
				'default' => __( 'Seconds', 'konte-addons' ),
				'label_block' => false,
			]
		);
		$this->end_controls_section();

		// Banner style options.
		$this->start_controls_section(
			'section_content_style',
			[
				'label' => __( 'Content', 'konte-addons' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		// Tagline
		$this->add_control(
			'tagline_style_heading',
			[
				'label'     => __( 'Tagline', 'konte-addons' ),
				'type'      => Controls_Manager::HEADING,
			]
		);

		$this->add_control(
			'tagline_color',
			[
				'label' => __( 'Color', 'konte-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .konte-banner-countdown__tagline' => 'color: {{VALUE}}',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'tagline_typography',
				'selector' => '{{WRAPPER}} .konte-banner-countdown__tagline',
			]
		);

		// Title
		$this->add_control(
			'title_style_heading',
			[
				'label'     => __( 'Title', 'konte-addons' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'title_color',
			[
				'label' => __( 'Color', 'konte-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .konte-banner-countdown__text' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'title_typography',
				'selector' => '{{WRAPPER}} .konte-banner-countdown__text',
			]
		);

		// Button
		$this->add_control(
			'button_style_heading',
			[
				'label'     => __( 'Button', 'konte-addons' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'button_color',
			[
				'label' => __( 'Color', 'konte-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .konte-banner-countdown .konte-button' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'button_typography',
				'selector' => '{{WRAPPER}} .konte-banner-countdown .konte-button',
			]
		);

		$this->end_controls_section();

		// Countdown style options.
		$this->start_controls_section(
			'section_style_countdown',
			[
				'label' => __( 'Countdown', 'konte-addons' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'digit_style_heading',
			[
				'label'     => __( 'Time', 'konte-addons' ),
				'type'      => Controls_Manager::HEADING,
			]
		);

		$this->add_control(
			'digit_color',
			[
				'label' => __( 'Color', 'konte-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .konte-countdown__box .time' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'digit_typography',
				'selector' => '{{WRAPPER}} .konte-countdown__box .time',
			]
		);

		$this->add_control(
			'label_style_heading',
			[
				'label'     => __( 'Label', 'konte-addons' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'label_color',
			[
				'label' => __( 'Color', 'konte-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .konte-countdown__box-label' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'label_typography',
				'selector' => '{{WRAPPER}} .konte-countdown__box-label',
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

		$this->add_render_attribute( 'wrapper', 'class', [ 'konte-banner-countdown', 'konte-banner-countdown--elementor' ] );

		if ( 'library' == $settings['image_source'] ) {
			$image = Group_Control_Image_Size::get_attachment_image_html( $settings );
		} else {
			$image = '<img src="' . esc_url( $settings['image_url'] ) . '" alt="' . esc_attr( $settings['title'] ) . '">';
		}

		$button_tag = 'span';

		if ( ! empty( $settings['link']['url'] ) ) {
			$button_tag = 'a';
			$this->add_link_attributes( 'button', $settings['link'] );

			$image = '<a ' . $this->get_render_attribute_string( 'button' ) . '>' . $image . '</a>';
		}

		$this->add_render_attribute( 'button', 'class', 'konte-button button-underline underline-small underline-center medium' );
		$this->add_render_attribute( 'tagline', 'class', [ 'konte-banner-countdown__tagline' ] );
		$this->add_render_attribute( 'title', 'class', [ 'konte-banner-countdown__text' ] );

		$this->add_inline_editing_attributes( 'tagline', 'none' );
		$this->add_inline_editing_attributes( 'title', 'basic' );
		?>
		<div <?php echo $this->get_render_attribute_string( 'wrapper' ); ?>>
			<?php echo $image; ?>
			<div class="konte-banner-countdown__banner">
				<?php if ( $settings['tagline'] ) : ?>
					<p <?php echo $this->get_render_attribute_string( 'tagline' ); ?>><?php echo $settings['tagline'] ?></p>
				<?php endif; ?>
				<div <?php echo $this->get_render_attribute_string( 'title' ); ?>><?php echo $settings['title'] ?></div>
				<?php if ( $settings['button_text'] ) : ?>
					<<?php echo $button_tag; ?> <?php echo $this->get_render_attribute_string( 'button' ); ?>><?php echo esc_html( $settings['button_text'] ); ?></<?php echo $button_tag ?>>
				<?php endif; ?>
			</div>
			<div class="konte-countdown konte-countdown--elementor type-small">
				<div class="timers" data-date="<?php echo esc_attr( $settings['date'] ); ?>">
					<div class="timer-day konte-countdown__box">
						<span class="time day"></span>
						<span class="konte-countdown__box-label"><?php echo esc_html( $settings['day_text'] ); ?></span>
					</div>
					<div class="timer-hour konte-countdown__box">
						<span class="time hour"></span>
						<span class="konte-countdown__box-label"><?php echo esc_html( $settings['hour_text'] ); ?></span>
					</div>
					<div class="timer-min konte-countdown__box">
						<span class="time min"></span>
						<span class="konte-countdown__box-label"><?php echo esc_html( $settings['min_text'] ); ?></span>
					</div>
					<div class="timer-secs konte-countdown__box">
						<span class="time secs"></span>
						<span class="konte-countdown__box-label"><?php echo esc_html( $settings['sec_text'] ); ?></span>
					</div>
				</div>
			</div>
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
		view.addRenderAttribute( 'wrapper', 'class', ['konte-banner-countdown', 'konte-banner-countdown--elementor'] );

		var imageUrl = '<?php echo Utils::get_placeholder_image_src(); ?>';

		if ( 'library' === settings.image_source ) {
			imageUrl = image_url = elementor.imagesManager.getImageUrl( {
				id: settings.image.id,
				url: settings.image.url,
				size: settings.image_size,
				dimension: settings.image_custom_dimension,
				model: view.getEditModel()
			} );
		} else {
			imageUrl = settings.image_url;
		}

		view.addRenderAttribute( 'tagline', 'class', 'konte-banner-countdown__tagline' );
		view.addRenderAttribute( 'title', 'class', 'konte-banner-countdown__text' );
		view.addRenderAttribute( 'button', 'href', settings.link.href );
		view.addRenderAttribute( 'button', 'class', ['konte-button button-underline underline-small underline-center medium'] );

		view.addInlineEditingAttributes( 'tagline', 'none' );
		view.addInlineEditingAttributes( 'title', 'basic' );
		#>

		<div {{{ view.getRenderAttributeString( 'wrapper' ) }}}>
			<# if ( imageUrl ) { #>
				<img src="{{ imageUrl }}">
			<# } #>
			<div class="konte-banner-countdown__banner">
				<# if ( settings.tagline ) { #>
					<p {{{ view.getRenderAttributeString( 'tagline' ) }}}>{{{ settings.tagline }}}</p>
				<# } #>

				<div {{{ view.getRenderAttributeString( 'title' ) }}}>{{{ settings.title }}}</div>

				<# if ( settings.button_text ) { #>
					<a {{{ view.getRenderAttributeString( 'button' ) }}}>{{{ settings.button_text }}}</a>
				<# } #>
			</div>
			<div class="konte-countdown konte-countdown--elementor type-small">
				<div class="timers" data-date="{{settings.date}}">
					<div class="timer-day konte-countdown__box"><span class="time day"></span><span class="konte-countdown__box-label">{{{settings.day_text}}}</span></div>
					<div class="timer-hour konte-countdown__box"><span class="time hour"></span><span class="konte-countdown__box-label">{{{settings.hour_text}}}</span></div>
					<div class="timer-min konte-countdown__box"><span class="time min"></span><span class="konte-countdown__box-label">{{{settings.min_text}}}</span></div>
					<div class="timer-secs konte-countdown__box"><span class="time secs"></span><span class="konte-countdown__box-label">{{{settings.sec_text}}}</span></div>
				</div>
			</div>
		</div>
		<?php
	}
}
