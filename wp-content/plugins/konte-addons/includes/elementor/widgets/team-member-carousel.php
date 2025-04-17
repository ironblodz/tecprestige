<?php
namespace KonteAddons\Elementor\Widgets;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

use Elementor\Controls_Manager;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Typography;
use KonteAddons\Elementor\Base\Carousel_Widget_Base;

/**
 * Icon Box widget
 */
class Team_Member_Carousel extends Carousel_Widget_Base {
	/**
	 * Retrieve the widget name.
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'konte-team-member-carousel';
	}

	/**
	 * Retrieve the widget title.
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __( 'Team Member Carousel', 'konte-addons' );
	}

	/**
	 * Retrieve the widget icon.
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'eicon-carousel konte-elementor-widget';
	}

	/**
	 * Retrieve the list of categories the widget belongs to.
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
		return [ 'team member carousel', 'team', 'member', 'carousel', 'konte' ];
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

		$repeater = new \Elementor\Repeater();

		$repeater->add_control(
			'image',
			[
				'label' => __( 'Image', 'konte-addons' ),
				'type' => Controls_Manager::MEDIA,
				'default' => [
					'url' => KONTE_ADDONS_URL . '/assets/images/person.jpg',
				],
			]
		);

		$repeater->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'name' => 'image',
				'default' => 'full',
				'separator' => 'none',
			]
		);

		$repeater->add_control(
			'name',
			[
				'label' => __( 'Name', 'konte-addons' ),
				'type' => Controls_Manager::TEXT,
				'default' => __( 'Member name', 'konte-addons' ),
				'separator' => 'before',
			]
		);

		$repeater->add_control(
			'job',
			[
				'label' => __( 'Job', 'konte-addons' ),
				'type' => Controls_Manager::TEXT,
				'default' => __( 'Member job', 'konte-addons' )
			]
		);

		// Socials
		$repeater->add_control(
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

		$repeater->start_popover();

		$socials = $this->get_social_icons();

		foreach( $socials as $key => $social ) {
			$repeater->add_control(
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

		$repeater->end_popover();

		$this->add_control(
			'members',
			[
				'label'       => __( 'Members', 'konte-addons' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'title_field' => '{{{ name }}}',
				'default' => [
					[
						'name' => __( 'Member name #1', 'konte-addons' ),
						'job'  => __( 'Member job #1', 'konte-addons' ),
					],
					[
						'name' => __( 'Member name #2', 'konte-addons' ),
						'job'  => __( 'Member job #2', 'konte-addons' ),
					],
					[
						'name' => __( 'Member name #3', 'konte-addons' ),
						'job'  => __( 'Member job #3', 'konte-addons' ),
					],
					[
						'name' => __( 'Member name #4', 'konte-addons' ),
						'job'  => __( 'Member job #4', 'konte-addons' ),
					],
				],
			]
		);

		$this->register_carousel_controls( [
			'slides_to_show'   => 3,
			'slides_to_scroll' => 1,
			'navigation'       => 'dots',
		] );

		$this->end_controls_section();

		// Additional settings.
		$this->start_controls_section(
			'section_additional_settings',
			[ 'label' => esc_html__( 'Additional Settings', 'konte-addons' ) ]
		);

		$this->register_carousel_controls( [
			'infinite' => 'yes',
			'autoplay' => 'yes',
			'speed'    => 800,
		] );

		$this->end_controls_section(); // End Carousel Settings

		// Style Content
		$this->start_controls_section(
			'section_style_team_member',
			[
				'label' => __( 'Team Member Carousel', 'konte-addons' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->register_carousel_controls( [
			'spacing'    => '',
			'arrow_type' => 'angle',
			'dots_align' => 'center',
		] );

		// Name
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

		if ( empty( $settings['members'] ) ) {
			return;
		}

		$this->add_render_attribute( 'wrapper', 'class', [
			'konte-team-member-carousel',
			'konte-team-member-carousel--elementor',
			'konte-carousel--elementor',
			'konte-carousel--swiper',
			'konte-carousel--nav-' . $settings['navigation'],
			'swiper-container',
		] );

		if ( is_rtl() ) {
			$this->add_render_attribute( 'wrapper', 'dir', 'rtl' );
		}

		$socials = $this->get_social_icons();
		?>
		<div <?php echo $this->get_render_attribute_string( 'wrapper' ); ?>>
			<div class="konte-team-member__list swiper-wrapper">
				<?php foreach( $settings['members'] as $index => $member ) : ?>
					<div class="konte-team-member swiper-slide">
						<?php
						if ( $member['image']['url'] ) {
							echo Group_Control_Image_Size::get_attachment_image_html( $member );
						} else {
							echo '<img src="' . KONTE_ADDONS_URL . '/assets/images/person.jpg" alt="' . esc_attr( $member['name'] ) . '">';
						}
						?>
						<div class="konte-team-member__info">
							<h5 class="konte-team-member__name"><?php echo esc_html( $member['name'] ) ?></h5>
							<span class="konte-team-member__job"><?php echo esc_html( $member['job'] ) ?></span>
							<span class="konte-team-member__socials">
								<?php
								foreach( $socials as $key => $social ) {
									if ( empty( $member[ $key ]['url'] ) ) {
										continue;
									}

									$link_key = $this->get_repeater_setting_key( 'link', 'social', $key . $index );
									$this->add_link_attributes( $link_key, $member[ $key ] );
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
				<?php endforeach; ?>
			</div>
			<?php $this->render_carousel_pagination(); ?>
		</div>
		<?php
		$this->render_carousel_navigation();
	}
}
