<?php

use Motivast\WP_CLI\Seed\AbstractSeeder;

require_once __DIR__ . '/Post.php';
require_once __DIR__ . '/Options.php';

class DummyClass extends AbstractSeeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call(Post::class);
        $this->call(Options::class);

        // Execute simple WordPress functions to have sure that we have connection with database
        update_option('blogdescription', '');
        update_option('show_on_front', 'page');
    }
}
