<?php
/**
 * Posts slider widget
 *
 * @package Konte
 */

/**
 * Class Konte_Addons_Posts_Slider_Widget
 */
class Konte_Addons_Posts_Slider_Widget extends WP_Widget {
	/**
	 * Holds widget settings defaults, populated in constructor.
	 *
	 * @var array
	 */
	protected $default;

	/**
	 * Class constructor
	 * Set up the widget
	 */
	public function __construct() {
		$this->defaults = array(
			'title' => esc_html__( 'Posts', 'konte-addons' ),
			'tag'   => 'staffs-pick',
			'limit' => 5,
		);

		parent::__construct(
			'posts-slider-widget',
			esc_html__( 'Konte - Posts Slider', 'konte-addons' ),
			array(
				'classname'                   => 'posts-slider-widget',
				'description'                 => esc_html__( 'Display a posts slider', 'konte-addons' ),
				'customize_selective_refresh' => true,
			)
		);
	}

	/**
	 * Outputs the content for the current widget instance.
	 *
	 * @param array $args     Display arguments including 'before_title', 'after_title',
	 *                        'before_widget', and 'after_widget'.
	 * @param array $instance Settings for the current widget instance.
	 */
	public function widget( $args, $instance ) {
		$instance = wp_parse_args( $instance, $this->defaults );

		echo $args['before_widget'];

		if ( $title = apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base ) ) {
			echo $args['before_title'] . $title . $args['after_title'];
		}

		if ( ! function_exists( 'konte_post_thumbnail' ) ) {
			esc_html_e( 'This widget works with the Konte theme only.', 'konte-addons' );
		} else {
			$query_args = array(
				'post_type'              => 'post',
				'post_status'            => 'publish',
				'posts_per_page'         => $instance['limit'],
				'no_found_rows'          => true,
				'update_post_term_cache' => false,
				'update_post_meta_cache' => false,
				'cache_results'          => false,
				'ignore_sticky_posts'    => true,
				'suppress_filters'       => false,
			);

			if ( ! empty( $instance['tag'] ) ) {
				$query_args['tag'] = $instance['tag'];
			}

			$query = new WP_Query( $query_args );

			if ( $query->have_posts() ) : ?>
				<div class="posts-slider" <?php echo is_rtl() ? 'dir="rtl"' : ''; ?>>

					<?php while ( $query->have_posts() ) : $query->the_post(); ?>

						<div class="post">
							<?php konte_post_thumbnail(); ?>

							<div class="cat-links">
								<?php the_category( ', ' ); ?>
							</div>

							<p class="entry-title"><a href="<?php the_permalink(); ?>"><?php the_title() ?></a></p>
						</div>

					<?php endwhile; ?>

				</div>
				<?php
				wp_reset_postdata();
			endif;
		}

		echo $args['after_widget'];
	}

	/**
	 * Outputs the settings form for the widget.
	 *
	 * @param array $instance Current settings.
	 */
	public function form( $instance ) {
		$instance = wp_parse_args( $instance, $this->defaults );
		?>

		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php esc_html_e( 'Title:', 'konte-addons' ); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $instance['title'] ); ?>" />
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'tag' ); ?>"><?php esc_html_e( 'Tags:', 'konte-addons' ); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'tag' ); ?>" name="<?php echo $this->get_field_name( 'tag' ); ?>" type="text" value="<?php echo esc_attr( $instance['tag'] ); ?>" />
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'limit' ); ?>"><?php esc_html_e( 'Number of posts:', 'konte-addons' ); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'limit' ); ?>" name="<?php echo $this->get_field_name( 'limit' ); ?>" type="text" value="<?php echo intval( $instance['limit'] ); ?>" />
		</p>

		<?php
	}
}