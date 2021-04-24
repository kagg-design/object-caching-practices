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
	 * Get items form remote server.
	 *
	 * @return array
	 *
	 * @throws \JsonException JsonException.
	 */
	public function get_items(): array {
		$response = wp_remote_get( self::END_POINT_REMOTE );

		return json_decode( $response['body'], true, 512, JSON_THROW_ON_ERROR );
	}
}
