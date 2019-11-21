Feature: Seed database

  Background:
    Given WordPress is installed

  @seed
  Scenario: Seed database
    When I run command "wp scaffold seeder post"
    And I run command "wp scaffold seeder options"
    And I create seeder "Seeder.php" in "seeds" directory
    And I run command "wp seed"
    Then STDOUT should contain
      """
      Success: File "Post.php" has been successfully created.
      Success: File "Options.php" has been successfully created.
      Seeding: Post
      Success: Seeded Post in (0 seconds)
      Seeding: Options
      Success: Seeded Options in (0 seconds)
      """

  @seed
  Scenario: Seed database with specified seeder
    When I run command "wp scaffold seeder post"
    And I run command "wp scaffold seeder options"
    And I create seeder "SeederCustom.php" in "seeds" directory
    And I run command "wp seed --seeder=SeederCustom.php"
    Then STDOUT should contain
      """
      Success: File "Post.php" has been successfully created.
      Success: File "Options.php" has been successfully created.
      Seeding: Post
      Success: Seeded Post in (0 seconds)
      Seeding: Options
      Success: Seeded Options in (0 seconds)
      """

  @seed
  Scenario: Seed database with specified seeds path
    When I run command "wp scaffold seeder post --seeds-path=seeds-custom"
    And I run command "wp scaffold seeder options --seeds-path=seeds-custom"
    And I create seeder "Seeder.php" in "seeds-custom" directory
    And I run command "wp seed --seeds-path=seeds-custom"
    Then STDOUT should contain
      """
      Success: File "Post.php" has been successfully created.
      Success: File "Options.php" has been successfully created.
      Seeding: Post
      Success: Seeded Post in (0 seconds)
      Seeding: Options
      Success: Seeded Options in (0 seconds)
      """

  @seed
  Scenario: Seed database with specified seeder and seeds path
    When I run command "wp scaffold seeder post --seeds-path=seeds-custom"
    And I run command "wp scaffold seeder options --seeds-path=seeds-custom"
    And I create seeder "SeederCustom.php" in "seeds-custom" directory
    And I run command "wp seed --seeder=SeederCustom.php --seeds-path=seeds-custom"
    Then STDOUT should contain
      """
      Success: File "Post.php" has been successfully created.
      Success: File "Options.php" has been successfully created.
      Seeding: Post
      Success: Seeded Post in (0 seconds)
      Seeding: Options
      Success: Seeded Options in (0 seconds)
      """

  Scenario: Seed database without Seeder.php file
    When I run command "wp seed"
    Then STDOUT should contain
      """
      Error: There is no "Seeder.php" class in "seeds" directory.
      """

  Scenario: Seed database without custom SeederCustom.php file
    When I run command "wp seed --seeder=SeederCustom.php"
    Then STDOUT should contain
      """
      Error: There is no "SeederCustom.php" class in "seeds" directory.
      """

  Scenario: Seed database without custom SeederCustom.php file and without seeds-custom directory
    When I run command "wp seed --seeder=SeederCustom.php --seeds-path=seeds-custom"
    Then STDOUT should contain
      """
      Error: There is no "SeederCustom.php" class in "seeds-custom" directory.
      """
