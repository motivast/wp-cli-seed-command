# WP CLI Seed Command

[![CircleCI](https://circleci.com/gh/motivast/wp-cli-seed-command.svg?style=svg)](https://circleci.com/gh/motivast/wp-cli-seed-command)

WP CLI Seed Command is an extension for WP-CLI to seed database with sample data.

## Why?

Working with advanced WordPress project require to provide test data for other developers or testing scripts. Working with MySQL dumps or exported data has a couple of disadvantages. Data from files like import file or MySQL dump are static you can not quickly scale from 10 random to 100 random posts, also you can not import local media files.

This command-line tools aim to solve these problems. You can write your seeds in PHP which gives you unlimited possibilities.

## Installation

You can install WP CLI Seed Command like any other WP-CLI extension

```
wp package install motivast/wp-cli-seed-command
```

If you want to install WP CLI Seed Command locally you can use composer in your project root directory

```
composer require motivast/wp-cli-seed-command
```

## Getting started

WP CLI Seed is providing two commands `wp seed` for seeding database with data and `wp scaffold seeder` to quickly create new seeders. To create the main seeder the following command.

```
wp scaffold seeder seeder
```

This command will create a `Seeder.php` file in the `seeds` directory with the following content.

```php
<?php

use Motivast\WP_CLI\Seed\AbstractSeeder;

class Seeder extends AbstractSeeder
{
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		//
	}
}
```
Everything inside `run` method will be executed during seeding database. Inside it you can add any PHP code including WordPress functions. Let's change some WordPress options and add basic pages.

```php
<?php

use Motivast\WP_CLI\Seed\AbstractSeeder;

class Seeder extends AbstractSeeder
{
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		update_option('blogname', 'WP CLI Seed Command');
		update_option('blogdescription', '');

		$defaults = [
			'post_type' => 'page',
			'post_status' => 'publish',
		];

		$home_id = wp_insert_post( wp_parse_args( [
			'post_title' => 'Home'
		], $defaults ) );

		$about_id = wp_insert_post( wp_parse_args( [
			'post_title' => 'About'
		], $defaults ) );

		$contact_id = wp_insert_post( wp_parse_args( [
			'post_title' => 'Contact'
		], $defaults ) );

 		// Update homepage
		update_option( 'show_on_front', 'page' );
		update_option( 'page_on_front', $home_id );
	}
}
```

Now you can import your seeds executing following command.

```
wp seed
```

Your WordPress options should change and new pages should be created.

### Splitting files
When your project will grow you might want to split seeds into multiple files. Based on the example above you can split this into two files. Create 'Options.php' and 'Pages.php' seeders.
```
wp scaffold seeder options
```
```
wp scaffold seeder pages
```

```php
<?php

use Motivast\WP_CLI\Seed\AbstractSeeder;

class Options extends AbstractSeeder
{
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		update_option('blogname', 'WP CLI Seed Command');
		update_option('blogdescription', '');
	}
}
```

```php
<?php

use Motivast\WP_CLI\Seed\AbstractSeeder;

class Pages extends AbstractSeeder
{
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		$defaults = [
			'post_type' => 'page',
			'post_status' => 'publish',
		];

		$home_id = wp_insert_post( wp_parse_args( [
			'post_title' => 'Home'
		], $defaults ) );

		$about_id = wp_insert_post( wp_parse_args( [
			'post_title' => 'About'
		], $defaults ) );

		$contact_id = wp_insert_post( wp_parse_args( [
			'post_title' => 'Contact'
		], $defaults ) );

 		// Update homepage
		update_option( 'show_on_front', 'page' );
		update_option( 'page_on_front', $home_id );
	}
}
```
Newlly created seeders have to be included and executed in your main `Seeder.php` file. Change your already existing `Seeder.php` to handle new seeders.

```php
<?php

use Motivast\WP_CLI\Seed\AbstractSeeder;

require_once __DIR__ . '/Options.php';
require_once __DIR__ . '/Pages.php';

class Seeder extends AbstractSeeder
{
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		$this->call(Options::class);
		$this->call(Pages::class);
	}
}
```

## Contribute
Please make sure to read the [Contribution guide](https://github.com/motivast/wp-cli-seed-command/blob/master/CONTRIBUTING.md) before making a pull request.

Thank you to all the people who already contributed to WP-CLI Seed Command!

## License
The project is licensed under the [MIT](https://github.com/motivast/wp-cli-seed-command/blob/master/LICENSE).

Copyright (c) 2019-present, Motivast
