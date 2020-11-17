<?php

use Behat\Behat\Context\Context;
use Behat\Behat\Hook\Scope\AfterScenarioScope;

use Behat\Gherkin\Node\PyStringNode;

use PHPUnit\Framework\Assert;

use Motivast\WP_CLI\Seed\Command\Seed;

/**
 * Defines application features from the specific context.
 */
class FeatureContext implements Context
{
    /**
     * WordPress installation path
     *
     * @var string
     */
    private $wpPath;

    /**
     * Seed command initialization file path
     *
     * @var string
     */
    private $seedCommandPath;

    /**
     * Seeds path
     *
     * @var string
     */
    private $seedsPath = seed::DEFAULT_SEEDS_PATH;

    /**
     * Seeder file
     *
     * @var string
     */
    private $seederFile = seed::DEFAULT_SEEDER_FILE;

    /**
     * Any command output string
     *
     * @var array
     */
    private $output;

    /**
     * Initializes context.
     *
     * Every scenario gets its own context instance.
     * You can also pass arbitrary arguments to the
     * context constructor through behat.yml.
     *
     * @param string $wpPath          WordPress installation path
     * @param string $seedCommandPath Seed command initialization file path
     */
    public function __construct( $wpPath = 'wordpress', $seedCommandPath = 'seed-command.php' )
    {
        $this->wpPath = realpath( $wpPath );
        $this->seedCommandPath = realpath( $seedCommandPath );
    }

    /**
     * @Given WordPress is installed
     */
    public function wordPressIsInstalled()
    {
        Assert::assertDirectoryExists($this->wpPath);
        Assert::assertFileExists("{$this->wpPath}/wp-config.php");
    }

    /**
     * @When I run command :command
     *
     * @param string $command Command to execute
     *
     * @return void
     */
    public function iRunCommand($command)
    {
        $pathOption     = sprintf("--path=%s", $this->wpPath);
        $requireOptions = sprintf("--require=%s", $this->seedCommandPath);
        $commandFull    = sprintf('%s %s %s', $command, $pathOption, $requireOptions);

        // Add ' 2>&1' to catch STDERR besides STDOUT
        exec($commandFull . ' 2>&1', $this->output);
    }

    /**
     * @When I create seeder :seeder in :dir directory
     *
     * @param string $seeder
     * @param string $dir
     *
     * @return void
     */
    public function iCreateSeederInDirectory($seeder, $dir)
    {
        $this->seederFile = $seeder;
        $this->seedsPath  = $dir;

        $cwd        = getcwd();
        $seeds      = sprintf("%s/%s", $cwd, $this->seedsPath);
        $srcSeeder  = sprintf("%s/features/resources/Seeder.php", $cwd);
        $destSeeder = sprintf("%s/%s", $seeds, $this->seederFile);

        if(!is_dir($seeds)) {
            mkdir($seeds);
        }

        copy( $srcSeeder, $destSeeder );

        $str = file_get_contents( $destSeeder );
        $str = str_replace( "DummyClass", pathinfo( $this->seederFile, PATHINFO_FILENAME ),$str);
        file_put_contents( $destSeeder, $str );
    }

    /**
     * @Then It should be created :dir directory
     *
     * @param $dir
     */
    public function itShouldBeCreatedDirectory($dir)
    {
        $this->seedsPath = $dir;

        $cwd  = getcwd();
        $full = sprintf("%s/%s", $cwd, $this->seedsPath);

        Assert::assertDirectoryExists($full);
    }

    /**
     * @Then It should be created :file file in :dir directory
     */
    public function itShouldBeCreatedFileInDirectory($file, $dir)
    {
        $cwd  = getcwd();
        $full = sprintf("%s/%s/%s", $cwd, $dir, $file);

        Assert::assertFileExists($full);
    }

    /**
     * @Then :file file in :dir directory should contain
     */
    public function fileInDirectoryShouldContain($file, $dir, PyStringNode $string)
    {
        $cwd  = getcwd();
        $full = sprintf("%s/%s/%s", $cwd, $dir, $file);

        Assert::assertFileExists($full);
        Assert::assertEquals($string, file_get_contents($full));
    }

    /**
     * @Then STDOUT should contain
     */
    public function stdoutShouldContain(PyStringNode $string)
    {
        $stdout = join( PHP_EOL, $this->output );

        Assert::assertEquals($string, $stdout);
    }

    /**
     * Cleans up files after every scenario.
     *
     * @param AfterScenarioScope $event
     *
     * @AfterScenario @scaffold
     * @AfterScenario @seed
     */
    public function cleanUpFiles($event) {

        $cwd  = getcwd();
        $full = sprintf("%s/%s", $cwd, $this->seedsPath);

        $this->delTree($full);
    }

    /**
     * Delete all files in directory and directory itself
     *
     * @param $dir
     *
     * @return bool
     */
    public function delTree($dir) {
        $files = array_diff(scandir($dir), array('.','..'));

        foreach ($files as $file) {
            ( is_dir("$dir/$file")) ? $this->delTree("$dir/$file") : unlink("$dir/$file");

        }
        return rmdir($dir);
    }
}
