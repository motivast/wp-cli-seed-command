# Contribution guide
I’m really excited that you are interested in contributing to WP CLI Seed Command project. Before submitting your contribution though, please make sure to take a moment and read through the following guidelines.

## Development setup

### Clone project
```
git clone git@github.com:motivast/wp-cli-seed-command.git
cd wp-cli-seed-command
```

### Copy dotenv and fill with your properties
```
cp .env.example .env
```

### Install dependencies
```
composer install
```

### Setup WordPress
```
composer run wp:init
```

This command will try to create database and install WordPress with configuration from .env file. After installation you should have fully working WordPress instance.

If you already have database delete it or execute command
```
composer run wp:reset
```
which will delete database for you and execute `wp:init` tasks.

### Setup tests
To execute test you only need working WordPress instance in `wordpress` directory. If you successfully setup WordPress from **Setup WordPress** section test should work out of the box.

### Code inspection and tests
Be sure to execute code inspection and test before before making a pull request.

Run code inspection
```
composer run inspect
```

Run code tests
```
composer run tests
```

## Commit message guidelines
A well-structured and described commit message helps better maintain open source project.

* current developers and users can quickly understand what new changes are about
* future developers have a clear history of changes in the code
* changelog is automatically generated from commit messages

Commit message is divided into sections **type**, **scope**, **subject**, **body**, **footer**. Only **type** and **subject** are required.

```
<type>(<scope>): <subject>
<BLANK LINE>
<body>
<BLANK LINE>
<footer>
```

Commit message header **type**, **scope**, **subject** should not be longer than 72 chars.

### Type
Type describe what type of changes commit message introduces. You can choose from the following types:

* **feat**: New feature for the user, not a new feature for build script
* **fix**: Bug fix for the user, not a fix to a build script
* **docs**: Changes to  the documentation
* **style**: Changes that do not affect the meaning of the code (white-space, formatting, missing semi-colons, etc.)
* **refactor**: A code change that neither fixes a bug or adds a feature
* **perf**: A code change that improves performance
* **test**: Adding tests or correcting existing tests
* **chore**: Changes that affect the build system or external dependencies (example scopes: composer, npm, robo, travis)

If the prefix is `feat`, `fix` or `perf`, it will appear in the changelog. However, if there is any `BREAKING CHANGE`, the commit will always appear in the changelog.

### Scope
Scope describes which part of the code is affected by changes. There are no strict rules on what values scope can accept.

### Subject
Subject contains a short and concise description of changes in the code. Use the following rules to create subject:
* always start from capital letter
* do not end subject line with a period `.`
* start from keyword like "Add", "Fix", "Change", "Replace"
* use the imperative mode, "Fix bug" not "Fixed bug" or "Fixes bug"

### Body
The body contains a long description of changes in the code. As in the subject, use the imperative mode. Please write why changes to the code were required and how changes affect the behavior of the software compared to the previous version.

### Footer

#### Breaking changes
All breaking changes have to be included in the footer and start with `BREAKING CHANGE:`. Point which parts of the API have been changed and write an example of how API was used `before` changes and how should be used `after`. Also, provide a description how to migrate from previous to next version.

#### Referring to issues
If commit closes some issues please refer them in the footer from the beginning of the new line.

```
Closes #123
```

or in case of many issues

```
Closes #123, #234, #345
```
