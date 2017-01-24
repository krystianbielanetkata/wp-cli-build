<?php namespace WP_CLI_Build\Helper;

use Requests;

class WP_API {

	public static function plugin_info( $slug = NULL, $version = NULL ) {
		if ( ! empty( $slug ) ) {

			$response = Requests::post(
				'http://api.wordpress.org/plugins/info/1.0/' . $slug . '.json',
				[ ],
				[ 'action' => 'plugin_information' ]
			);

			if ( ! empty( $response->body ) ) {
				$plugin = json_decode( $response->body );
				if ( ( ! empty( $version ) ) && ( $plugin->version != $version ) ) {
					if ( ! empty( $plugin->download_link ) ) {
						$plugin = self::_get_item_download_link( $plugin, $version );
					}
				}

				return $plugin;
			}

		}

		return NULL;
	}

	// Changes item download link with the specified version.
	private static function _get_item_download_link( $response, $version ) {

		// WordPress.org forces https, but still sometimes returns http
		// See https://twitter.com/nacin/status/512362694205140992
		$response->download_link = str_replace( 'http://', 'https://', $response->download_link );

		list( $link ) = explode( $response->slug, $response->download_link );

		if ( 'dev' == $version ) {
			$response->download_link = $link . $response->slug . '.zip';
			$response->version       = 'Development Version';
		} else {
			$response->download_link = $link . $response->slug . '.' . $version . '.zip';
			$response->version       = $version;
		}

		return $response;

	}

}