Feature: Scaffold seeder

  Background:
    Given WordPress is installed

  @scaffold
  Scenario: Scaffold seed
    When I run command "wp scaffold seeder post"
    Then It should be created "seeds" directory
    Then It should be created "Post.php" file in "seeds" directory
    Then "Post.php" file in "seeds" directory should contain
      """
      <?php

      use Motivast\WP_CLI\Seed\AbstractSeeder;

      class Post extends AbstractSeeder
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
      """
    Then STDOUT should contain
      """
      Success: File "Post.php" has been successfully created.
      """

  @scaffold
  Scenario: Scaffold seed when file already exists
    When I run command "wp scaffold seeder post"
    And  I run command "wp scaffold seeder post"
    Then STDOUT should contain
      """
      Success: File "Post.php" has been successfully created.
      Error: File "Post.php" already exists.
      """

  @scaffold
  Scenario: Scaffold seed when file already exists and we use --force command
    When I run command "wp scaffold seeder post"
    And  I run command "wp scaffold seeder post --force"
    Then It should be created "seeds" directory
    Then It should be created "Post.php" file in "seeds" directory
    Then "Post.php" file in "seeds" directory should contain
      """
      <?php

      use Motivast\WP_CLI\Seed\AbstractSeeder;

      class Post extends AbstractSeeder
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
      """
    Then STDOUT should contain
      """
      Success: File "Post.php" has been successfully created.
      Success: File "Post.php" has been successfully created.
      """

  @scaffold
  Scenario: Scaffold seed with specified seeds path
    When I run command "wp scaffold seeder post --seeds-path=seeds-custom"
    Then It should be created "seeds-custom" directory
    Then It should be created "Post.php" file in "seeds-custom" directory
    Then "Post.php" file in "seeds-custom" directory should contain
      """
      <?php

      use Motivast\WP_CLI\Seed\AbstractSeeder;

      class Post extends AbstractSeeder
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
      """
    Then STDOUT should contain
      """
      Success: File "Post.php" has been successfully created.
      """

  @scaffold
  Scenario: Scaffold seed when file already exists with specified seeds path
    When I run command "wp scaffold seeder post --seeds-path=seeds-custom"
    And  I run command "wp scaffold seeder post --seeds-path=seeds-custom"
    Then It should be created "seeds-custom" directory
    Then STDOUT should contain
      """
      Success: File "Post.php" has been successfully created.
      Error: File "Post.php" already exists.
      """

  @scaffold
  Scenario: Scaffold seed when file already exists with specified seeds path and we use --force command
    When I run command "wp scaffold seeder post --seeds-path=seeds-custom"
    And  I run command "wp scaffold seeder post  --seeds-path=seeds-custom --force"
    Then It should be created "seeds-custom" directory
    Then It should be created "Post.php" file in "seeds-custom" directory
    Then "Post.php" file in "seeds-custom" directory should contain
      """
      <?php

      use Motivast\WP_CLI\Seed\AbstractSeeder;

      class Post extends AbstractSeeder
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
      """
    Then STDOUT should contain
      """
      Success: File "Post.php" has been successfully created.
      Success: File "Post.php" has been successfully created.
      """
