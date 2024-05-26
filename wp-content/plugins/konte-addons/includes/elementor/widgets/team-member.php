<?php
namespace KonteAddons\Elementor\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Typography;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Icon Box widget
 */
class Team_Member extends Widget_Base {
	/**
	 * Retrieve the widget name.
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'konte-team-member';
	}

	/**
	 * Retrieve the widget title.
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __( '[Konte] Team Member', 'konte-addons' );
	}

	/**
	 * Retrieve the widget icon.
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'eicon-person';
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
		return [ 'team member', 'team', 'member', 'konte' ];
	}

	/**
	 * Register the widget controls.
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @access protected
	 */
	protected function register_controls() {
		$this->start_controls_section(
			'section_team_member',
			[ 'label' => __( 'Team Member', 'konte-addons' ) ]
		);

		$this->add_control(
			'image',
			[
				'label' => __( 'Image', 'konte-addons' ),
				'type' => Controls_Manager::MEDIA,
				'default' => [
					'url' => KONTE_ADDONS_URL . '/assets/images/person.jpg',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'name' => 'image', // Usage: `{name}_size` and `{name}_custom_dimension`, in this case `image_size` and `image_custom_dimension`.
				'default' => 'full',
				'separator' => 'none',
			]
		);

		$this->add_control(
			'name',
			[
				'label' => __( 'Name', 'konte-addons' ),
				'type' => Controls_Manager::TEXT,
				'default' => __( 'Member name', 'konte-addons' ),
				'separator' => 'before',
			]
		);

		$this->add_control(
			'job',
			[
				'label' => __( 'Job', 'konte-addons' ),
				'type' => Controls_Manager::TEXT,
				'default' => __( 'Member job', 'konte-addons' )
			]
		);

		// Socials
		$this->add_control(
			'socials_toggle',
			[
				'label' => __( 'Socials', 'konte-addons' ),
				'type' => \Elementor\Controls_Manager::POPOVER_TOGGLE,
				'label_off' => __( 'Default', 'konte-addons' ),
				'label_on' => __( 'Custom', 'konte-addons' ),
				'return_value' => 'yes',
				'separator' => 'before',
			]
		);

		$this->start_popover();

		$socials = $this->get_social_icons();

		foreach( $socials as $key => $social ) {
			$this->add_control(
				$key,
				[
					'label'       => $social['label'],
					'type'        => Controls_Manager::URL,
					'placeholder' => __( 'https://your-link.com', 'konte-addons' ),
					'default'     => [
						'url' => '',
					],
				]
			);
		}

		$this->end_popover();

		$this->end_controls_section();

		// Style Icon
		$this->start_controls_section(
			'section_team_member_style',
			[
				'label' => __( 'Team Member', 'konte-addons' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		// Name
		$this->add_control(
			'name_style_heading',
			[
				'label'     => __( 'Name', 'konte-addons' ),
				'type'      => Controls_Manager::HEADING,
			]
		);

		$this->add_control(
			'name_color',
			[
				'label' => __( 'Color', 'konte-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .konte-team-member__name' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'name_typography',
				'selector' => '{{WRAPPER}} .konte-team-member__name',
			]
		);

		// Job
		$this->add_control(
			'job_style_heading',
			[
				'label'     => __( 'Job', 'konte-addons' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'job_color',
			[
				'label' => __( 'Color', 'konte-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .konte-team-member__job' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'job_typography',
				'selector' => '{{WRAPPER}} .konte-team-member__job',
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Get Team Member Socials
	 */
	protected function get_social_icons() {
		$socials = [
			'facebook' => [
				'icon' => 'fa fa-facebook',
				'label' => __( 'Facebook', 'konte-addons' )
			],
			'twitter' => [
				'icon' => 'fa fa-twitter',
				'label' => __( 'Twitter', 'konte-addons' )
			],
			'youtube' => [
				'icon' => 'fa fa-youtube-play',
				'label' => __( 'Youtube', 'konte-addons' )
			],
			'dribbble' => [
				'icon' => 'fa fa-dribbble',
				'label' => __( 'Dribbble', 'konte-addons' )
			],
			'instagram' => [
				'icon' => 'fa fa-instagram',
				'label' => __( 'Instagram', 'konte-addons' )
			],
			'linkedin' => [
				'icon' => 'fa fa-linkedin',
				'label' => __( 'Linkedin', 'konte-addons' )
			],
			'pinterest' => [
				'icon' => 'fa fa-pinterest-p',
				'label' => __( 'Pinterest', 'konte-addons' )
			],
		];

		return $socials;
	}

	/**
	 * Render widget output on the frontend.
	 * Written in PHP and used to generate the final HTML.
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();

		if ( $settings['image']['url'] ) {
			$image = Group_Control_Image_Size::get_attachment_image_html( $settings );
		} else {
			$image = '<img src="' . KONTE_ADDONS_URL . '/assets/images/person.jpg" alt="' . esc_attr( $settings['name'] ) . '">';
		}

		$this->add_render_attribute( 'wrapper', 'class', ['konte-team-member'] );
		$this->add_render_attribute( 'name', 'class', 'konte-team-member__name' );
		$this->add_render_attribute( 'job', 'class', 'konte-team-member__job' );

		$this->add_inline_editing_attributes( 'name', 'none' );
		$this->add_inline_editing_attributes( 'job', 'none' );
		?>
		<div <?php echo $this->get_render_attribute_string( 'wrapper' ); ?>>
			<?php echo $image ?>
			<div class="konte-team-member__info">
				<h5 <?php echo $this->get_render_attribute_string( 'name' ); ?>><?php echo esc_html( $settings['name'] ) ?></h5>
				<span <?php echo $this->get_render_attribute_string( 'job' ); ?>><?php echo esc_html( $settings['job'] ) ?></span>
				<span class="konte-team-member__socials">
					<?php
					$socials = $this->get_social_icons();
					foreach( $socials as $key => $social ) {
						if ( empty( $settings[ $key ]['url'] ) ) {
							continue;
						}

						$link_key = $this->get_repeater_setting_key( 'link', 'social', $key );
						$this->add_link_attributes( $link_key, $settings[ $key ] );
						$this->add_render_attribute( $link_key, 'title', $social['label'] );
						?>
						<a <?php echo $this->get_render_attribute_string( $link_key ); ?>>
							<i class="<?php echo esc_attr( $social['icon'] ) ?>"></i>
						</a>
						<?php
					}
					?>
				</span>
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
		view.addRenderAttribute( 'wrapper', 'class', ['konte-team-member', 'konte-team-member--elementor'] );
		view.addRenderAttribute( 'name', 'class', 'konte-team-member__name' );
		view.addRenderAttribute( 'job', 'class', 'konte-team-member__job' );

		view.addInlineEditingAttributes( 'name', 'none' );
		view.addInlineEditingAttributes( 'job', 'none' );

		var imageUrl = '<?php echo KONTE_ADDONS_URL . '/assets/images/person.jpg' ?>';

		if ( settings.image.url ) {
			var image = {
				id: settings.image.id,
				url: settings.image.url,
				size: settings.image_size,
				dimension: settings.image_custom_dimension,
				model: view.getEditModel()
			};

			imageUrl = elementor.imagesManager.getImageUrl( image );
		}

		var socials = {
			facebook : 'fa fa-facebook',
			twitter  : 'fa fa-twitter',
			youtube  : 'fa fa-youtube-play',
			dribbble : 'fa fa-dribbble',
			instagram: 'fa fa-instagram',
			linkedin : 'fa fa-linkedin',
			pinterest: 'fa fa-pinterest-p'
		};
		#>
		<div {{{ view.getRenderAttributeString( 'wrapper' ) }}}>
			<img src="{{ imageUrl }}">

			<div class="konte-team-member__info">
				<h5 {{{ view.getRenderAttributeString( 'name' ) }}}>{{{ settings.name }}}</h5>
				<span {{{ view.getRenderAttributeString( 'job' ) }}}>{{{ settings.job }}}</span>
				<span class="konte-team-member__socials">
					<#
					_.each( socials, function( value, key ) {
						var link = settings[key];
						if ( link.url ) {
						#>
							<a href="{{ link.url }}"><i class="{{ value }}"></i></a>
						<# }
					} );
					#>
				</span>
			</div>
		</div>
		<?php
	}
}
