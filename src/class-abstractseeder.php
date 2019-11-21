<?php
/**
 * Abstract class provided for Seed classes
 *
 * @package WP-CLI-Seed-Command/src
 * @author  Motivast <support@motivast.com>
 */

namespace Motivast\WP_CLI\Seed;

/**
 * Abstract class provided for Seed classes
 *
 * @package WP-CLI-Seed-Command/src
 * @author  Motivast <support@motivast.com>
 */
abstract class AbstractSeeder {

	/**
	 * Seed given collection
	 *
	 * @param  array|string $class  Class or classes to be seeded.
	 *
	 * @return $this
	 */
	public function call( $class ) {
		$classes = $this->wrap( $class );

		foreach ( $classes as $class ) {
			$seeder = new $class();
			$name   = get_class( $seeder );

			\WP_CLI::log( "Seeding: {$name}" );

			$start_time = microtime( true );
			$seeder->__invoke();
			$run_time = round( microtime( true ) - $start_time, 2 );

			\WP_CLI::success( "Seeded {$name} in ({$run_time} seconds)" );
		}

		return $this;
	}

	/**
	 * Run the database seeds.
	 *
	 * @return mixed
	 *
	 * @throws InvalidArgumentException When class is missing run method.
	 */
	public function __invoke() {
		if ( ! method_exists( $this, 'run' ) ) {
			throw new InvalidArgumentException( 'Method [run] missing from ' . get_class( $this ) );
		}

		return $this->run();
	}

	/**
	 * Wrap value with array
	 *
	 * @param mixed $value Value to wrap.
	 *
	 * @return array
	 */
	private function wrap( $value ) {
		if ( is_null( $value ) ) {
			return array();
		}

		return is_array( $value ) ? $value : array( $value );
	}
}
