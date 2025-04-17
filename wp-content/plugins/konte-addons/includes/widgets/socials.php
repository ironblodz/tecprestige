<?php
/**
 * Social links widget
 *
 * @package Konte
 */

/**
 * Class Konte_Addons_Social_Links_Widget
 */
class Konte_Addons_Social_Links_Widget extends WP_Widget {
	/**
	 * Holds widget settings defaults, populated in constructor.
	 *
	 * @var array
	 */
	protected $default;

	/**
	 * List of supported socials
	 *
	 * @var array
	 */
	protected $socials;

	/**
	 * Constructor
	 */
	function __construct() {
		$socials = array(
			'facebook'    => esc_html__( 'Facebook', 'konte-addons' ),
			'twitter'     => esc_html__( 'Twitter', 'konte-addons' ),
			'google-plus' => esc_html__( 'Google Plus', 'konte-addons' ),
			'tumblr'      => esc_html__( 'Tumblr', 'konte-addons' ),
			'linkedin'    => esc_html__( 'Linkedin', 'konte-addons' ),
			'pinterest'   => esc_html__( 'Pinterest', 'konte-addons' ),
			'flickr'      => esc_html__( 'Flickr', 'konte-addons' ),
			'instagram'   => esc_html__( 'Instagram', 'konte-addons' ),
			'dribbble'    => esc_html__( 'Dribbble', 'konte-addons' ),
			'behance'     => esc_html__( 'Behance', 'konte-addons' ),
			'stumbleupon' => esc_html__( 'StumbleUpon', 'konte-addons' ),
			'github'      => esc_html__( 'Github', 'konte-addons' ),
			'youtube'     => esc_html__( 'Youtube', 'konte-addons' ),
			'vimeo'       => esc_html__( 'Vimeo', 'konte-addons' ),
			'houzz'       => esc_html__( 'Houzz', 'konte-addons' ),
			'rss'         => esc_html__( 'RSS', 'konte-addons' ),
		);

		$this->socials = apply_filters( 'konte_social_media', $socials );
		$this->default = array(
			'title' => '',
		);

		foreach ( $this->socials as $k => $v ) {
			$this->default["{$k}_title"] = $v;
			$this->default["{$k}_url"]   = '';
		}

		parent::__construct(
			'social-links-widget',
			esc_html__( 'Konte - Social Links', 'konte-addons' ),
			array(
				'classname'                   => 'social-links-widget social-links',
				'description'                 => esc_html__( 'Display links to social media networks.', 'konte-addons' ),
				'customize_selective_refresh' => true,
			),
			array( 'width' => 600 )
		);
	}

	/**
	 * Outputs the content for the current widget instance.
	 *
	 * @param array $args     Display arguments including 'before_title', 'after_title',
	 *                        'before_widget', and 'after_widget'.
	 * @param array $instance Settings for the current widget instance.
	 */
	function widget( $args, $instance ) {
		$instance = wp_parse_args( $instance, $this->default );

		echo $args['before_widget'];

		if ( $title = apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base ) ) {
			echo $args['before_title'] . $title . $args['after_title'];
		}

		echo '<div class="social-links">';

		foreach ( $this->socials as $social => $label ) {
			if ( empty( $instance[ $social . '_url' ] ) ) {
				continue;
			}

			$icon = $social;

			if ( 'youtube' == $social ) {
				$icon = 'youtube-play';
			}

			printf(
				'<a href="%s" class="%s social" rel="nofollow" title="%s" data-toggle="tooltip" data-placement="top" target="_blank"><i class="fa fa-%s"></i></a>',
				esc_url( $instance[ $social . '_url' ] ),
				esc_attr( $social ),
				esc_attr( $instance[ $social . '_title' ] ),
				esc_attr( $icon )
			);
		}

		echo '</div>';

		echo $args['after_widget'];
	}

	/**
	 * Displays the form for this widget on the Widgets page of the WP Admin area.
	 *
	 * @param array $instance
	 *
	 * @return string|void
	 */
	function form( $instance ) {
		$instance = wp_parse_args( $instance, $this->default );
		?>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Title', 'konte-addons' ); ?></label>
			<input type="text" class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" value="<?php echo esc_attr( $instance['title'] ); ?>" />
		</p>

		<?php
		foreach ( $this->socials as $social => $label ) {
			printf(
				'<div style="width: 280px; float: left; margin-right: 10px;">
					<label>%s</label>
					<p><input type="text" class="widefat" name="%s" placeholder="%s" value="%s"></p>
				</div>',
				$label,
				$this->get_field_name( $social . '_url' ),
				esc_html__( 'URL', 'konte-addons' ),
				$instance[ $social . '_url' ]
			);
		}
	}
}
