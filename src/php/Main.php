<?php
/**
 * Main class file.
 *
 * @package kagg/ocp;
 */

namespace KAGG\OCP;

use WP_Post;
use WP_Query;

/**
 * Class Main
 */
class Main {
	/**
	 * Cache key.
	 */
	private const CACHE_KEY = 'products';

	/**
	 * Cache group.
	 */
	private const CACHE_GROUP = 'ocp';

	/**
	 * API instance.
	 *
	 * @var API
	 */
	private $api;

	/**
	 * Main constructor.
	 *
	 * @param API $api API instance.
	 */
	public function __construct( $api ) {
		$this->api = $api;
	}

	/**
	 * Init class.
	 */
	public function init(): void {
		add_filter( 'pre_get_document_title', [ $this, 'pre_get_document_title_filter' ], 20 );
		add_action( 'template_redirect', [ $this, 'ocp_page' ] );
		add_action( 'wp_footer', [ $this, 'ocp_footer' ] );
		add_action( 'save_post', [ $this, 'purge_cache' ], 10, 3 );
	}

	/**
	 * Filters the document title before it is generated.
	 *
	 * @param string $title Page title.
	 *
	 * @return string
	 */
	public function pre_get_document_title_filter( string $title ): string {
		if ( $this->is_ocp_page() ) {
			return 'Object Caching Practices Demo';
		}

		return $title;
	}

	/**
	 * Check if it is a plugin page.
	 *
	 * @return bool
	 */
	protected function is_ocp_page(): bool {
		if ( ! isset( $_SERVER['REQUEST_URI'] ) ) {
			return false;
		}

		$page_slug = 'ocp';
		$uri       = filter_var( wp_unslash( $_SERVER['REQUEST_URI'] ), FILTER_SANITIZE_STRING );
		$path      = wp_parse_url( $uri, PHP_URL_PATH );

		return '/' . trailingslashit( $page_slug . '/' ) === trailingslashit( $path );
	}

	/**
	 * Output ocp page.
	 */
	public function ocp_page(): void {
		if ( ! is_admin() && $this->is_ocp_page() ) {
			get_header();
			$this->show_ocp_products();
			$this->show_remote_items();
			get_footer();
			$this->php_exit();
		}
	}

	/**
	 * Show ocp products.
	 */
	public function show_ocp_products(): void {
		$found = false;
		$query = wp_cache_get( self::CACHE_KEY, self::CACHE_GROUP, false, $found );

		if ( ! $found ) {
			$args = [
				'post_type'      => 'product',
				'post_status'    => 'publish',
				'posts_per_page' => - 1,
				'orderby'        => 'title',
				'order'          => 'ASC',
				'meta_query'     => [
					'relation' => 'OR',
					[
						'key'     => 'total_sales',
						'value'   => [ 100, 1000 ],
						'compare' => 'BETWEEN',
						'type'    => 'NUMERIC',
					],
					[
						'relation' => 'AND',
						[
							'key'     => '_regular_price',
							'value'   => [ 5, 15 ],
							'compare' => 'BETWEEN',
							'type'    => 'NUMERIC',
						],
						[
							'key'     => 'total_sales',
							'value'   => 1000,
							'compare' => '>',
							'type'    => 'NUMERIC',
						],
					],
				],
			];

			$query = new WP_Query( $args );

			wp_cache_set( self::CACHE_KEY, $query, self::CACHE_GROUP );
		}

		if ( $query->have_posts() ) {
			echo '--------<br>';
			while ( $query->have_posts() ) {
				$query->the_post();
				the_title();
				?>
				<br>
				<?php
			}
			wp_reset_postdata();
			echo '--------<br>';
		}
	}

	/**
	 * Show remote items.
	 */
	public function show_remote_items(): void {
		$items = $this->api->get_items();
		$data  = $items['data'];

		echo '<table>';
		echo '<caption>' . esc_html( $items['title'] ) . '</caption>';

		echo '<thead>';
		foreach ( $data['headers'] as $header ) {
			echo '<th>' . esc_html( $header ) . '</th>';
		}
		echo '</thead>';

		echo '<tbody>';
		foreach ( $data['rows'] as $row ) {
			echo '<tr>';
			foreach ( $row as $item ) {
				echo '<td>' . esc_html( $item ) . '</td>';
			}
			echo '</tr>';
		}
		echo '<tbody>';

		echo '</table>';
	}

	/**
	 * Show ocp products in the footer.
	 */
	public function ocp_footer(): void {
		$this->show_ocp_products();
		$this->show_remote_items();
	}

	/**
	 * Purge cache.
	 * Fires once a post has been saved.
	 *
	 * @param int     $post_ID Post ID.
	 * @param WP_Post $post    Post object.
	 * @param bool    $update  Whether this is an existing post being updated.
	 */
	public function purge_cache( $post_ID, $post, $update ): void {
		wp_cache_delete( self::CACHE_KEY, self::CACHE_GROUP );
	}

	/**
	 * Exit method.
	 * We need this method to cover methods using 'exit' by phpunit test,
	 * as php 'exit' statement is impossible to mock.
	 */
	protected function php_exit(): void {
		// @codeCoverageIgnoreStart
		exit;
		// @codeCoverageIgnoreEnd
	}
}
