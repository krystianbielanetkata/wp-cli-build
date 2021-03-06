<?php namespace WP_CLI_Build;

use WP_CLI;
use WP_CLI_Build\Processor\Core;
use WP_CLI_Build\Processor\Item;
use WP_CLI_Build\Helper\Utils;

class Build_Command extends \WP_CLI_Command {

	/**
	 * Installs wordpress, plugins and themes.
	 *
	 * ## OPTIONS
	 *
	 * [--file=<file>]
	 * : Specify custom build file (default: build.json)
	 *
	 * [--clean]
	 * : Deletes and re-download all plugins and themes listed in build file
	 *
	 * [--ignore-core]
	 * : Don't process core
	 *
	 * [--ignore-plugins]
	 * : Don't process plugins
	 *
	 * [--ignore-themes]
	 * : Don't process themes
	 *
	 * ## EXAMPLES
	 *
	 *     wp build
	 *     wp build --file=production.json --no-plugins
	 *
	 * @when  before_wp_load
	 */
	public function __invoke( $args = NULL, $assoc_args = NULL ) {

		$build_filename = Utils::get_build_filename( $assoc_args );
		WP_CLI::line( WP_CLI::colorize( "%GParsing %W$build_filename%n%G, please wait...%n" ) );

		// Clean mode check
		if ( ! empty( $assoc_args['clean'] ) ) {
			WP_CLI::confirm( WP_CLI::colorize( "\n%RItems will be deleted! => This will delete and re-download all plugins and themes listed in build file.\n%n%YAre you sure you want to continue?%n" ) );
		}

		// Process core.
		if ( empty( $assoc_args['no-core'] ) ) {
			$core = new Core( $assoc_args );
			$core = $core->process();
		}

		// Item processor.
		$item = new Item( $assoc_args );

		// Process plugins.
		if ( empty( $assoc_args['no-plugins'] ) ) {
			$plugins = $item->run( 'plugin' );
		}

		// Process themes.
		if ( empty( $assoc_args['no-themes'] ) ) {
			$themes = $item->run( 'theme' );
		}

		// Nothing to do!
		if ( empty( $core ) && empty( $plugins ) && empty( $themes ) ) {
			WP_CLI::line( WP_CLI::colorize( "%WNothing to do.%n" ) );
		} else {
			WP_CLI::line( WP_CLI::colorize( "%WFinished.%n" ) );
		}

	}

}