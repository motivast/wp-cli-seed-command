<?php
/**
 * Class for providing seed command for wp-cli command line tool
 *
 * @package WP-CLI-Seed-Command/src/command
 * @author  Motivast <support@motivast.com>
 */

namespace Motivast\WP_CLI\Seed\Command;

use WP_CLI\ExitException;

/**
 * Class for providing seed command for wp-cli command line tool
 *
 * @package WP-CLI-Seed-Command/src/command
 * @author  Motivast <support@motivast.com>
 */
class Seed {

	/**
	 * Default seeds path relative to cwd directory
	 *
	 * @var string
	 */
	const DEFAULT_SEEDS_PATH = 'seeds';

	/**
	 * Default seeder file
	 *
	 * @var string
	 */
	const DEFAULT_SEEDER_FILE = 'Seeder.php';

	/**
	 * Seed the database with records
	 *
	 * [--seeds-path]
	 * : Seeds path relative to cwd.
	 * ---
	 * default: seeds
	 *
	 * [--seeder]
	 * : Seeder class to seed database.
	 * ---
	 * default: Seeder.php
	 *
	 * ## EXAMPLES
	 *
	 *    wp seed
	 *
	 * @param array $args Command arguments.
	 * @param array $assoc_args Command associative arguments.
	 *
	 * @return void
	 *
	 * @throws ExitException If WP_CLI has error code and we want to capture exit error method may throw this exception.
	 *
	 * @when after_wp_load
	 */
	public function __invoke( $args, $assoc_args ) {
		$seeds_path  = \WP_CLI\Utils\get_flag_value( $assoc_args, 'seeds-path', self::DEFAULT_SEEDS_PATH );
		$seeder_file = \WP_CLI\Utils\get_flag_value( $assoc_args, 'seeder', self::DEFAULT_SEEDER_FILE );

		$this->seed( $seeds_path, $seeder_file );
	}

	/**
	 * Find seed class and seed the database with records
	 *
	 * @param string $seeds_path  Seeds path directory relative to cwd.
	 * @param string $seeder_file Seeder file to seed database.
	 *
	 * @return void
	 *
	 * @throws ExitException If WP_CLI has error code and we want to capture exit error method may throw this exception.
	 */
	private function seed( $seeds_path, $seeder_file ) {

		$seeds_full_path   = sprintf( '%s/%s', getcwd(), $seeds_path );
		$seeder_class      = pathinfo( $seeder_file, PATHINFO_FILENAME );
		$seeder_class_path = sprintf( '%s/%s', $seeds_full_path, $seeder_file );

		if ( ! file_exists( $seeder_class_path ) ) {
			\WP_CLI::error( sprintf( 'There is no "%s.php" class in "%s" directory.', $seeder_class, basename( $seeds_full_path ) ) );
		}

		require_once $seeder_class_path;

		$seed = new $seeder_class();
		$seed->__invoke();
	}
}
