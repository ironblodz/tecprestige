<?php
/**
 * WPML compatibility functions
 */

class Konte_WPML {
	const CAMPAIGNS_DOMAIN = 'Campaign Bar';
	const CAMPAIGN_PREFIX = 'campaign_';
	const SEARCH_LINKS_DOMAIN = 'Search Links';
	const SEARCH_LINK_PREFIX = 'search_link_';

	/**
	 * The single instance of the class
	 *
	 * @var Konte_WPML
	 */
	protected static $instance = null;

	/**
	 * Main instance
	 *
	 * @return Konte_WPML
	 */
	public static function instance() {
		if ( null == self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Constructor
	 */
	public function __construct() {
		add_action( 'customize_save_after', array( $this, 'register_strings' ) );

		add_filter( 'konte_campaign_item_args', array( $this, 'translate_campaign_item_args' ), 10, 2 );
		add_filter( 'konte_search_quicklinks', array( $this, 'translate_search_quicklinks' ) );

		add_filter( 'konte_featured_post_ids_for_cache', array( $this, 'featured_post_ids' ), 10, 2 );
		add_filter( 'konte_get_featured_post_ids', array( $this, 'translate_featured_post_ids' ) );

		add_filter( 'wpml_pb_shortcode_encode', array( $this, 'shortcode_encode_urlencoded_json' ), 10, 3 );
		add_filter( 'wpml_pb_shortcode_decode', array( $this, 'shortcode_decode_urlencoded_json' ), 10, 3 );

		add_action( 'konte_currency_switcher', array( $this, 'currency_switcher' ) );
	}

	/**
	 * Register special theme strings for translation
	 *
	 * @return void
	 */
	public function register_strings() {
		$this->register_campaign_strings();
		$this->register_search_link_strings();
	}

	/**
	 * Register campaign strings for translation
	 */
	public function register_campaign_strings() {
		$campaigns = array_filter( (array) konte_get_option( 'campaign_items' ) );

		if ( empty( $campaigns ) ) {
			return;
		}

		foreach ( $campaigns as $id => $campaign ) {
			$count = $id + 1;

			do_action( 'wpml_register_single_string', self::CAMPAIGNS_DOMAIN, self::CAMPAIGN_PREFIX . $count . '_tag', $campaign['tag'] );
			do_action( 'wpml_register_single_string', self::CAMPAIGNS_DOMAIN, self::CAMPAIGN_PREFIX . $count . '_text', $campaign['text'] );
			do_action( 'wpml_register_single_string', self::CAMPAIGNS_DOMAIN, self::CAMPAIGN_PREFIX . $count . '_button', $campaign['button'] );
			do_action( 'wpml_register_single_string', self::CAMPAIGNS_DOMAIN, self::CAMPAIGN_PREFIX . $count . '_link', $campaign['link'] );
		}
	}

	/**
	 * Register header search links for translation
	 */
	public function register_search_link_strings() {
		$links = konte_get_option( 'header_search_links' );

		if ( empty( $links ) ) {
			return;
		}

		foreach ( $links as $id => $link ) {
			$count = $id + 1;

			do_action( 'wpml_register_single_string', self::SEARCH_LINKS_DOMAIN, self::SEARCH_LINK_PREFIX . $count . '_text', $link['text'] );
			do_action( 'wpml_register_single_string', self::SEARCH_LINKS_DOMAIN, self::SEARCH_LINK_PREFIX . $count . '_url', $link['url'] );
		}
	}

	/**
	 * Apply the WPML translation for campaign items
	 *
	 * @param array $args
	 * @param int $id
	 *
	 * @return array
	 */
	public function translate_campaign_item_args( $args, $id ) {
		$count = $id + 1;

		$args['tag']    = apply_filters( 'wpml_translate_single_string', $args['tag'], self::CAMPAIGNS_DOMAIN, self::CAMPAIGN_PREFIX . $count . '_tag' );
		$args['text']   = apply_filters( 'wpml_translate_single_string', $args['text'], self::CAMPAIGNS_DOMAIN, self::CAMPAIGN_PREFIX . $count . '_text' );
		$args['button'] = apply_filters( 'wpml_translate_single_string', $args['button'], self::CAMPAIGNS_DOMAIN, self::CAMPAIGN_PREFIX . $count . '_button' );
		$args['link']   = apply_filters( 'wpml_translate_single_string', $args['link'], self::CAMPAIGNS_DOMAIN, self::CAMPAIGN_PREFIX . $count . '_link' );

		return $args;
	}

	/**
	 * Apply the WPML translation for search quick links
	 *
	 * @param array $links
	 *
	 * @return array
	 */
	public function translate_search_quicklinks( $links ) {
		if ( empty( $links ) ) {
			return $links;
		}

		foreach ( $links as $id => $link ) {
			$count = $id + 1;

			$links[ $id ]['text'] = apply_filters( 'wpml_translate_single_string', $link['text'], self::SEARCH_LINKS_DOMAIN, self::SEARCH_LINK_PREFIX . $count . '_text' );
			$links[ $id ]['url']  = apply_filters( 'wpml_translate_single_string', $link['url'], self::SEARCH_LINKS_DOMAIN, self::SEARCH_LINK_PREFIX . $count . '_url' );
		}

		return $links;
	}

	/**
	 * Query featured post ids in the default language.
	 * Translate them in another filter.
	 *
	 * @param array $ids
	 * @param array $query_args
	 * @return array
	 */
	public function featured_post_ids( $ids, $query_args ) {
		$current_lang = apply_filters( 'wpml_current_language', null );
		$default_lang = apply_filters( 'wpml_default_language', null );

		do_action( 'wpml_switch_language', $default_lang );

		$query = new WP_Query( $query_args );
		$ids   = $query->posts;

		do_action( 'wpml_switch_language', $current_lang );

		return $ids;
	}

	/**
	 * Translate featured posts IDs
	 *
	 * @param array $ids
	 * @return array
	 */
	public function translate_featured_post_ids( $ids ) {
		$ids = array_map( array( $this, 'translate_post_id' ), $ids );
		$ids = array_filter( $ids );

		return $ids;
	}

	/**
	 * Translate a single post ID
	 *
	 * @return ID|null Translated post ID
	 */
	public function translate_post_id( $post_id ) {
		return apply_filters( 'wpml_object_id', $post_id, 'post', false, null );
	}

	/**
	 * Encode the param_groups type of js_composer
	 *
	 * @param string $string
	 * @param string $encoding
	 * @param array $original_string
	 * @return string
	 */
	public function shortcode_encode_urlencoded_json( $string, $encoding, $original_string ) {
		if ( 'urlencoded_json' === $encoding ) {
			$output = array();

			foreach ( $original_string as $combined_key => $value ) {
				$parts = explode( '_', $combined_key );
				$i     = array_pop( $parts );
				$key   = implode( '_', $parts );

				$output[ $i ][ $key ] = $value;
			}

			$string = urlencode( json_encode( $output ) );
		}

		return $string;
	}

	/**
	 * Decode urleconded string of param_groups type of js_composer
	 *
	 * @param string $string
	 * @param string $encoding
	 * @param string $original_string
	 * @return string
	 */
	public function shortcode_decode_urlencoded_json( $string, $encoding, $original_string ) {
		if ( 'urlencoded_json' === $encoding ) {
			$rows   = json_decode( urldecode( $original_string ), true );
			$string = array();
			$atts   = array( 'title', 'label', 'value' );

			foreach ( (array) $rows as $i => $row ) {
				foreach ( $row as $key => $value ) {
				if ( in_array( $key, $atts ) ) {
						$string[ $key . '_' . $i ] = array( 'value' => $value, 'translate' => true );
					} else {
						$string[ $key . '_' . $i ] = array( 'value' => $value, 'translate' => false );
					}
				}
			}
		}

		return $string;
	}

	/**
	 * Display the currency switcher of WooCommerce Multilingual
	 */
	public function currency_switcher( $args ) {
		if ( ! function_exists( 'wcml_is_multi_currency_on' ) || ! wcml_is_multi_currency_on() ) {
			return;
		}

		$args = wp_parse_args( $args, array(
			'label'     => '',
			'direction' => 'down',
			'class'     => '',
		) );

		$classes = array(
			'currency',
			'currency-switcher--wcml',
			$args['direction'],
			$args['class'],
		);

		printf( '<div class="%s">', esc_attr( implode( ' ', $classes ) ) );

		if ( ! empty( $args['label'] ) ) :
			echo '<span class="label">' . esc_html( $args['label'] ) . '</span>';
		endif;

		do_action( 'wcml_currency_switcher', array( 'format' => '%code%' ) );

		echo '</div>';
	}
}

Konte_WPML::instance();
