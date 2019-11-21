<?php

use Robo\Robo;
use Robo\Result;

use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * This is project's console commands configuration for Robo task runner.
 *
 * @see http://robo.li/
 */
class RoboFile extends \Robo\Tasks
{
	/**
	 * RoboFile constructor
	 */
	public function __construct(){

		// Change working directory to project root directory
		chdir(dirname(__DIR__));

		// Read .env.example in case .env do not exist e.g. in ci environment
		$dotenvFile = file_exists('.env') ? '.env' : '.env.example';

		$dotenv = Dotenv\Dotenv::create(getcwd(), $dotenvFile);
		$dotenv->load();

		$env = $this->getEnvironment();

		// Load Robo configuration
		Robo::loadConfiguration([__DIR__ . '/config.yml']);

		$config_env = sprintf( 'config.%s.yml', $env);
		$config_env_path = sprintf( '%s/%s', __DIR__, $config_env);

		// Load environment Robo configuration
		if( file_exists($config_env_path)) {
			Robo::loadConfiguration([$config_env_path]);
		}
	}

	/**
	 * Get environment in which robo should execute scripts
	 *
	 * @return string
	 */
	private function getEnvironment() {
		return ( getenv('ROBO_ENV') !== false) ? getenv('ROBO_ENV') : 'production';
	}

	/**
	 * Build project alias for build:all
	 *
	 * @command build
	 */
	public function build(SymfonyStyle $io)
	{
		$this->buildAll($io);
	}

	/**
	 * Build project, execute build:clean, build:clone, build:install, build:assets
	 *
	 * @command build:all
	 */
	public function buildAll(SymfonyStyle $io)
	{
		$env = $this->getEnvironment();

		$io->newLine();
		$io->title("Build project for \"${env}\" environment");

		$this->buildClean($io);
		$this->buildClone($io);
		$this->buildInstall($io);
	}

	/**
	 * Remove build artifacts
	 *
	 * @command build:clean
	 *
	 * @return Result
	 */
	public function buildClean(SymfonyStyle $io)
	{
		$io->newLine();
		$io->section("Remove build artifacts");

		$buildDir = Robo::Config()->get('build.dir');

		return $this->taskDeleteDir([$buildDir])->run();
	}

	/**
	 * Clone repository
	 *
	 * @command build:clone
	 *
	 * @return Result
	 */
	public function buildClone(SymfonyStyle $io)
	{
		$io->newLine();
		$io->section("Clone repository");

		$buildDir   = Robo::Config()->get('build.dir');
		$repository = Robo::Config()->get('build.repository');

		return $this->taskGitStack()
			->cloneRepo($repository, $buildDir)
			->run();
	}

	/**
	 * Install composer and npm dependencies
	 *
	 * @command build:install
	 *
	 * @return Result
	 */
	public function buildInstall(SymfonyStyle $io)
	{
		$io->newLine();
		$io->section("Install composer and npm dependencies");

		$buildDir = Robo::Config()->get('build.dir');

		return $this->taskComposerInstall()
			->workingDir($buildDir)
			->noScripts()
			->noDev()
			->run();
	}

	/**
	 * Initialize WordPress installation
	 *
	 * @command wp:init
	 *
	 * @return Result
	 */
	public function wpInit(SymfonyStyle $io)
	{
		$io->newLine();
		$io->title("Initialize WordPress installation");

		$collection = $this->collectionBuilder();
		$collection->setProgressIndicator(null);

		$collection->addCode(function() use ($io){ return $this->wpConfig($io); });
		$collection->addCode(function() use ($io){ return $this->wpDbCreate($io); });
		$collection->addCode(function() use ($io){ return $this->wpInstall($io); });
		$collection->addCode(function() use ($io){ return $this->wpTheme($io); });

		$result = $collection->run();

		if($result->wasSuccessful()) {
			$io->newLine();
			$io->success("WordPress was successfully initialized");
		} else {
			$io->newLine();
			$io->error("There was a problem with WordPress initialization");
		}

		// Always return result to pass proper exit code
		return $result;
	}

	/**
	 * Reset WordPress installation
	 *
	 * @command wp:reset
	 *
	 * @return Result
	 */
	public function wpReset(SymfonyStyle $io)
	{
		$io->newLine();
		$io->title("Reset WordPress installation");

		$collection = $this->collectionBuilder();
		$collection->setProgressIndicator(null);

		$collection->addCode(function() use ($io){ return $this->wpConfig($io); });
		$collection->addCode(function() use ($io){ return $this->wpDbDrop($io); });
		$collection->addCode(function() use ($io){ return $this->wpDbCreate($io); });
		$collection->addCode(function() use ($io){ return $this->wpInstall($io); });
		$collection->addCode(function() use ($io){ return $this->wpTheme($io); });

		$result = $collection->run();

		if($result->wasSuccessful()) {
			$io->newLine();
			$io->success("WordPress was successfully reset");
		} else {
			$io->newLine();
			$io->error("There was a problem with WordPress reset");
		}

		// Always return result to pass proper exit code
		return $result;
	}

	/**
	 * Generate new WordPress config based on environmental variables
	 *
	 * @command wp:config
	 *
	 * @return Result
	 */
	public function wpConfig(SymfonyStyle $io) {

		$io->newLine();
		$io->section("Generate WordPress config");

		$dbName     = getenv('DB_NAME');
		$dbUser     = getenv('DB_USER');
		$dbPassword = getenv('DB_PASSWORD');
		$dbHost     = getenv('DB_HOST');

		return $this->wpExec('config create')
			->option('dbname', $dbName, '=')
			->option('dbuser', $dbUser, '=')
			->option('dbpass', $dbPassword, '=')
			->option('dbhost', $dbHost, '=')
			->option('force')
			->run();
	}

	/**
	 * Drop existing database
	 *
	 * @command wp:db:drop
	 *
	 * @return Result
	 */
	public function wpDbDrop(SymfonyStyle $io)
	{
		$io->newLine();
		$io->section("Drop existing database");

		return $this->wpExec('db drop')
			->option('yes')
			->run();
	}

	/**
	 * Create database if not already exists
	 *
	 * @command wp:db:create
	 *
	 * @return Result
	 */
	public function wpDbCreate(SymfonyStyle $io)
	{
		$io->newLine();
		$io->section("Create database if not already exists");

		return $this->wpExec('db create')
			->run();
	}

	/**
	 * Install WordPress
	 *
	 * @command wp:install
	 *
	 * @return Result
	 */
	public function wpInstall(SymfonyStyle $io)
	{
		$io->newLine();
		$io->section("Install WordPress");

		$url           = getenv('WP_HOME');
		$adminUser     = getenv('WP_ADMIN_USER');
		$adminPassword = getenv('WP_ADMIN_PASSWORD');
		$adminEmail    = getenv('WP_ADMIN_EMAIL');

		$wpTitle  = Robo::Config()->get('wp.title');

		return $this->wpExec('core install')
			->option('url', $url, '=')
			->option('title', $wpTitle, '=')
			->option('admin_user', $adminUser, '=')
			->option('admin_password', $adminPassword, '=')
			->option('admin_email', $adminEmail, '=')
			->run();
	}

	/**
	 * Activate installed theme
	 *
	 * @command wp:theme
	 *
	 * @return Result
	 */
	public function wpTheme(SymfonyStyle $io)
	{
		$io->newLine();
		$io->section("Activate installed theme");

		$wpTheme = Robo::Config()->get('wp.theme');

		return $this->wpExec('theme activate')
			->arg($wpTheme)
			->run();
	}

	private function wpExec($command) {

		$wpPath = Robo::Config()->get('wp.core.dir');

		return $this->taskExec('./vendor/bin/wp')
			->rawArg($command)
			->option('path', $wpPath, '=');
	}

	/**
	 * Inspect code
	 *
	 * @command inspect
	 *
	 * @return Result
	 */
	public function inspect(SymfonyStyle $io)
	{
		$io->newLine();
		$io->title("Inspect code");

		$collection = $this->collectionBuilder();
		$collection->setProgressIndicator(false);

		$collection->addCode(function() use ($io){ return $this->lint($io); });
		$collection->addCode(function() use ($io){ return $this->phpcs($io); });

		$result = $collection->run();

		if($result->wasSuccessful()) {
			$io->newLine();
			$io->success("Eagle landed!");
		} else {
			$io->newLine();
			$io->error("Houston, we have a problem!");
		}

		// Always return result to pass proper exit code
		return $result;
	}

	/**
	 * Fix code
	 *
	 * @command inspect:fix
	 *
	 * @return Result
	 */
	public function inspectFix(SymfonyStyle $io)
	{
		$io->newLine();
		$io->title("Fix code");

		$collection = $this->collectionBuilder();
		$collection->setProgressIndicator(false);

		$collection->addCode(function() use ($io){ return $this->phpcbf($io); });

		$result = $collection->run();

		if($result->wasSuccessful()) {
			$io->newLine();
			$io->success("Eagle landed!");
		} else {
			$io->newLine();
			$io->error("Houston, we have a problem!");
		}

		// Always return result to pass proper exit code
		return $result;
	}

	/**
	 * Check possible syntax errors
	 *
	 * @command lint
	 *
	 * @return Result
	 */
	public function lint(SymfonyStyle $io){

		$io->newLine();
		$io->section("Check possible syntax errors");

		return $this->taskExec('for i in $(find . \( -path ./vendor -o -path ./wordpress \) -prune -o -name \'*.php\' -print); do php -l $i; done')
			->run();
	}

	/**
	 * Check possible code styling errors
	 *
	 * @command phpcs
	 *
	 * @return Result
	 */
	public function phpcs(SymfonyStyle $io){

		$io->newLine();
		$io->section("Check possible code styling errors");

		return $this->taskExec('./vendor/bin/phpcs')
			->option('extensions', 'php', '=')
			->option('standard', './rules/phpcs.xml', '=')
			->run();
	}

	/**
	 * Fix possible code styling errors
	 *
	 * @command phpcbf
	 *
	 * @return Result
	 */
	public function phpcbf(SymfonyStyle $io){

		$io->newLine();
		$io->section("Fix possible code styling errors");

		return $this->taskExec('./vendor/bin/phpcbf')
			->option('extensions', 'php', '=')
			->option('standard', './rules/phpcs.xml', '=')
			->run();
	}

	/**
	 * Run tests
	 *
	 * @command tests
	 *
	 * @return Result
	 */
	public function tests(SymfonyStyle $io){

		$io->newLine();
		$io->title("Run tests");

		$collection = $this->collectionBuilder();
		$collection->setProgressIndicator(null);

		$collection->addCode(function() use ($io){ return $this->behat($io); });

		$result = $collection->run();

		if($result->wasSuccessful()) {
			$io->newLine();
			$io->success("Eagle landed!");
		} else {
			$io->newLine();
			$io->error("Houston, we have a problem!");
		}

		// Always return result to pass proper exit code
		return $result;
	}

	/**
	 * Run behat tests
	 *
	 * @command behat
	 *
	 * @return Result
	 */
	public function behat(SymfonyStyle $io){

		$io->newLine();
		$io->title("Run behat tests");

		return $this->taskBehat()
			->colors()
			->noInteraction()
			->run();
	}
}
