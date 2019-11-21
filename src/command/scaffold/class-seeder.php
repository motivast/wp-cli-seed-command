<?php
/**
 * Class for providing `scaffold seed` command for wp-cli command line tool
 *
 * @package WP-CLI-Seed-Command/src/command/scaffold
 * @author  Motivast <support@motivast.com>
 */

namespace Motivast\WP_CLI\Seed\Command\Scaffold;

use WP_CLI\ExitException;

use Motivast\WP_CLI\Seed\Command\AbstractScaffold;
use Motivast\WP_CLI\Seed\Command\Seed;

/**
 * Class for providing `scaffold seed` command for wp-cli command line tool
 *
 * @package WP-CLI-Seed-Command/src/command/scaffold
 * @author  Motivast <support@motivast.com>
 */
class Seeder extends AbstractScaffold {

	/**
	 * Generates PHP code for seeding database.
	 *
	 * ## OPTIONS
	 *
	 * <slug>
	 * : The internal name of the seed.
	 *
	 * [--seeds-path]
	 * : Seeds path relative to cwd.
	 *
	 * [--force]
	 * : Overwrite files that already exist.
	 *
	 * ## EXAMPLES
	 *
	 *    wp scaffold seeder posts
	 *
	 * @param array $args Command arguments.
	 * @param array $assoc_args Command associative arguments.
	 *
	 * @return void
	 *
	 * @throws ExitException If WP_CLI has error code and we want to capture exit error method may throw this exception.
	 */
	public function __invoke( $args, $assoc_args ) {

		if ( strlen( $args[0] ) > 20 ) {
			\WP_CLI::error( 'Seeder slugs cannot exceed 20 characters in length.' );
		}

		$this->set_arguments( $args );
		$this->set_options( $assoc_args );

		$this->handle();
	}

	/**
	 * Get arguments map provided to retrieve arguments by name
	 *
	 * @return array
	 */
	protected function get_arguments_map() {
		return array(
			'slug',
		);
	}

	/**
	 * Get the template file for the generator.
	 *
	 * @return string
	 */
	protected function get_directory() {
		return $this->get_option( 'seeds-path', Seed::DEFAULT_SEEDS_PATH );
	}

	/**
	 * Get the template file for the generator.
	 *
	 * @return string
	 */
	protected function get_template() {
		return 'seeder.mustache';
	}

	/**
	 * Get template placeholders with strings.
	 *
	 * @return array
	 */
	protected function get_placeholders() {

		$name = $this->get_class_name( $this->get_slug_input() );

		return array(
			'DummyClass' => $name,
		);
	}
}
