<?php
/**
 * Main class file.
 *
 * @package kagg/ocp;
 */

namespace KAGG\OCP;

use WP_Query;

/**
 * Class Main
 */
class Main {

	/**
	 * Init class.
	 */
	public function init(): void {
		add_action( 'template_redirect', [ $this, 'ocp_page' ] );
		add_action( 'wp_footer', [ $this, 'ocp_footer' ] );
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
			get_footer();
			$this->php_exit();
		}
	}

	/**
	 * Show ocp products.
	 */
	public function show_ocp_products(): void {
		$args = [
			'post_type'      => 'product',
			'post_status'    => 'publish',
			'posts_per_page' => - 1,
			'meta_query'     => [
				'relation' => 'OR',
				[
					'key'     => 'total_sales',
					'value'   => 100,
					'compare' => '>',
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
						'key'     => '_regular_price',
						'value'   => 1000,
						'compare' => '=',
						'type'    => 'NUMERIC',
					],
				],
			],
		];

		$query = new WP_Query( $args );

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
			echo '<br>--------';
		}
	}

	/**
	 * Show ocp products in the footer.
	 */
	public function ocp_footer(): void {
		$this->show_ocp_products();
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
