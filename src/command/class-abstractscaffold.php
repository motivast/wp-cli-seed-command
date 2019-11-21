<?php
/**
 * Abstract class provided for Scaffold classes
 *
 * @package WP-CLI-Seed-Command/src/command
 * @author  Motivast <support@motivast.com>
 */

namespace Motivast\WP_CLI\Seed\Command;

use InvalidArgumentException;

use WP_CLI\Utils;
use WP_CLI\ExitException;

/**
 * Abstract class provided for Scaffold classes
 *
 * @package WP-CLI-Seed-Command/src/command
 * @author  Motivast <support@motivast.com>
 */
abstract class AbstractScaffold {

	/**
	 * Command arguments
	 *
	 * @var array
	 */
	private $args;

	/**
	 * Command options
	 *
	 * @var array
	 */
	private $options;

	/**
	 * Get arguments map provided to retrieve arguments by name
	 *
	 * @return array
	 */
	abstract protected function get_arguments_map();

	/**
	 * Get the directory name where file should be located.
	 *
	 * @return string
	 */
	abstract protected function get_directory();

	/**
	 * Get the template file for the generator.
	 *
	 * @return string
	 */
	abstract protected function get_template();

	/**
	 * Get template path
	 *
	 * @return string
	 */
	protected function get_template_path() {

		return __DIR__ . '/../../templates/' . $this->get_template();

	}

	/**
	 * Execute the console command.
	 *
	 * @return void
	 *
	 * @throws ExitException If WP_CLI has error code and we want to capture exit error method may throw this exception.
	 */
	public function handle() {
		$wp_filesystem = $this->wp_filesystem();

		$name = $this->get_class_name( $this->get_slug_input() );
		$path = $this->get_path( $name );

		// First we will check to see if the class already exists. If it does, we don't want
		// to create the class and overwrite the user's code. So, we will bail out so the
		// code is untouched. Otherwise, we will continue generating this class' files.
		if ( ( ! $this->has_option( 'force' ) || ! $this->get_option( 'force' ) ) && $wp_filesystem->exists( $path ) ) {
			\WP_CLI::error( sprintf( 'File "%s" already exists.', basename( $path ) ) );
		}

		// Next, we will generate the path to the location where this class' file should get
		// written. Then, we will build the class and make the proper replacements on the
		// stub files so that it gets the correctly formatted namespace and class name.
		$wp_filesystem->mkdir( $this->get_directory() );

		$result = $wp_filesystem->put_contents( $path, $this->build_class() );

		if ( ! $result ) {
			\WP_CLI::error( sprintf( 'File "%s" can not be written.', basename( $path ) ) );
		}

		\WP_CLI::success( sprintf( 'File "%s" has been successfully created.', basename( $path ) ) );
	}

	/**
	 * Get the desired file name from the input.
	 *
	 * @return string
	 */
	protected function get_slug_input() {
		return trim( $this->get_argument( 'slug' ) );
	}

	/**
	 * Get the desired class name from the slug.
	 *
	 * @param string $slug Slug name.
	 *
	 * @return string
	 */
	protected function get_class_name( $slug ) {
		return ucfirst( $slug );
	}

	/**
	 * Get path where file should be located.
	 *
	 * @param string $class Class name.
	 *
	 * @return string
	 */
	protected function get_path( $class ) {
		$wp_filesystem = $this->wp_filesystem();

		$root = $wp_filesystem->cwd();
		$dir  = $this->get_directory();

		return sprintf( '%s/%s/%s.php', $root, $dir, $class );
	}

	/**
	 * Build the class with the given name.
	 *
	 * @return string
	 */
	protected function build_class() {
		return $this->replace_placeholders( Utils\mustache_render( $this->get_template_path() ) );
	}

	/**
	 * Replace placeholders in template
	 *
	 * @param string $template Template content.
	 *
	 * @return string
	 */
	protected function replace_placeholders( $template ) {
		$placeholders = $this->get_placeholders();

		return str_replace( array_keys( $placeholders ), array_values( $placeholders ), $template );
	}

	/**
	 * Set command arguments
	 *
	 * @param array $args Array of command line arguments.
	 *
	 * @return void
	 */
	protected function set_arguments( $args ) {
		$this->args = $args;
	}

	/**
	 * Set command options
	 *
	 * @param array $options Options array.
	 *
	 * @return void
	 */
	protected function set_options( $options ) {
		$this->options = $options;
	}

	/**
	 * Get single command argument value
	 *
	 * @param string $name Command argument name.
	 *
	 * @return string
	 *
	 * @throws InvalidArgumentException When used argument is not used in this command.
	 */
	protected function get_argument( $name ) {

		$arguments_map = $this->get_arguments_map();

		if ( ! empty( $arguments_map ) ) {
			if ( array_search( $name, $arguments_map, true ) === false ) {
				throw new InvalidArgumentException( sprintf( 'Current command do not use "%s" argument.', $name ) );
			}
		}

		$index = array_search( $name, $arguments_map, true );

		return \WP_CLI\Utils\get_flag_value( $this->args, $index, false );
	}

	/**
	 * Get single option
	 *
	 * @param string $name Command option name.
	 * @param mixed  $default Default value in case option would not be available.
	 *
	 * @return mixed
	 */
	protected function get_option( $name, $default = false ) {
		return \WP_CLI\Utils\get_flag_value( $this->options, $name, $default );
	}

	/**
	 * Check if option exists
	 *
	 * @param string $name Command option name.
	 *
	 * @return bool
	 */
	protected function has_option( $name ) {
		return isset( $this->options[ $name ] );
	}

	/**
	 * Initializes WP_Filesystem.
	 */
	private function wp_filesystem() {
		global $wp_filesystem;
		WP_Filesystem();

		return $wp_filesystem;
	}
}
