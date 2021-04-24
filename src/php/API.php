<?php
/**
 * API class file.
 *
 * @package kagg/ocp;
 */

namespace KAGG\OCP;

/**
 * Class Main
 */
class API {

	/**
	 * Endpoint remote.
	 */
	private const END_POINT_REMOTE = 'https://miusage.com/v1/challenge/1/';

	/**
	 * Transient name.
	 */
	private const TRANSIENT = 'ocp-api';

	/**
	 * Get items form remote server.
	 *
	 * @return array
	 *
	 * @throws \JsonException JsonException.
	 */
	public function get_items(): array {
		$items = get_transient( self::TRANSIENT );
		if ( ! $items ) {
			$response = wp_remote_get( self::END_POINT_REMOTE );
			$items    = json_decode( $response['body'], true, 512, JSON_THROW_ON_ERROR );
			set_transient( self::TRANSIENT, $items, 10 );
		}

		return $items;
	}
}
