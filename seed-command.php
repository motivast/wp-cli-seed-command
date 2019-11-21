<?php

if ( ! class_exists( 'WP_CLI' ) ) {
	return;
}

$wpcli_scaffold_autoloader = dirname( __FILE__ ) . '/vendor/autoload.php';

if ( file_exists( $wpcli_scaffold_autoloader ) ) {
	require_once $wpcli_scaffold_autoloader;
}

use Motivast\WP_CLI\Seed\Command\Seed as SeedCommand;
use Motivast\WP_CLI\Seed\Command\Scaffold\Seeder as ScaffoldSeederCommand;

\WP_CLI::add_command( 'seed', SeedCommand::class );
\WP_CLI::add_command( 'scaffold seeder', ScaffoldSeederCommand::class );

