# Kanawa-Mura 
### A Drupal based resource for the 4th edition of Legend of the Five Rings role-playing game.

## Overview
Big thanks to all the wiki editors who made Last Haiku [Last Haiku](https://lasthaiku.wikidot.com/ "Last Haiku"). Without their work, this site wouldn't exist.

### Installing
1. Have a working LAMP stack
2. Install composer
3. Install git
4. Clone the repo
5. Run "composer install" in the repo to install Drupal and all of the dependencies
6. Follow the drush steps below.

### How It Works
I take the wiki source and put it in a .wiki file (all found in /haiku/source). In most cases, there's some fixing that needs to be done = Last Haiku (being edited by multiple people) is not consistent often. I also sometimes add some random symbols to the start of certain lines so it's easy to find them when parsing. For example, by defaut Last Haiku separates out the Clan Schools out but I wanted to do them all in one file - so each clan has a header ike "+++ Crab CLan". The .wiki files are the "master" source for content - if I find a typo or something in need of correction, I change it in the .wiki file.

Once the .wiki file is ready, I run it through the appropriate parse file (all found in /haiku/parsers). These are all fairly similar, they read the wiki files line by line and structure the data into an array. That array is then saved into the "Last Haiku Import" module (/web/modules/custom/last_haiku_import/json) as a JSON file. For the lazy, you can simply run the parse.php file in /haiku to parse everything at once.

The next step is to take that JSON file and turn it into Drupal content. On module enable, Drupal runs that module's .install file - in this cast last_haiku_import.install. This file generates all of the *actual* content while the structure is handled by Drupal's configuration system.

Assuming you have a LAMP stack, the steps I run when I want to make an update are (replacing the placeholders with actual values):

**You will need to have a correct settings.local.php file in /web/sites/default - see below for an example**.

```bash
drush si --account-name="<nme>" --account-mail="<email>" --account-pass="<password>" --site-name="<site name>" --existing-config -y
drush en last_haiku_import -y
drush cim -y
drush cr
```

This does a fresh install of Drupal then imports all of the configuration settings. Next, it enables the import which then makes all of the nodes and taxonomy terms. I then run the config importer again, which has the effect of uninstalling last_haiku_import since it's not needed anymore. Then I clear the caches (because clearing the caches is always good!).

### settings.local.php ###
```php
// Set up the database connection
$databases = [];

$databases['default']['default'] = [
     'database' => '<mysql database>',
     'username' => '<mysql username>',
     'password' => '<mysql user password>',
     'host' => 'localhost',
     'port' => '3306',
     'driver' => 'mysql',
     'prefix' => '',
     'collation' => 'utf8mb4_general_ci',
];

// A salt is required for Drupal to be happy, though on a localdev it's pretty pointless
$settings[ 'hash_salt' ] = '<some random hash salt>';

// These are some development settings and are not production safe
// See example.settings.local.php in /web/sites/ for what these all do
$settings['container_yamls'][] = DRUPAL_ROOT . '/sites/development.services.yml';
$settings['container_yamls'][] = DRUPAL_ROOT . '/sites/default/default.services.pantheon.preproduction.yml';
$config['system.logging']['error_level'] = 'verbose';
$config['system.performance']['css']['preprocess'] = FALSE;
$config['system.performance']['js']['preprocess'] = FALSE;
$settings['cache']['bins']['render'] = 'cache.backend.null';
$settings['cache']['bins']['discovery_migration'] = 'cache.backend.memory';
$settings['cache']['bins']['page'] = 'cache.backend.null';
$settings['cache']['bins']['dynamic_page_cache'] = 'cache.backend.null';
$settings['rebuild_access'] = TRUE;
$settings['skip_permissions_hardening'] = TRUE;
```


# THIS IS A DUMB WAY TO DO A SITE #
Note, this is not how I'd recommend making a site. I made Kanawa-Mura as a hobby project, and don't expect anyone other than a very small group of people to be interested in making edits. Completely trashing the content every time to make an update in the wiki files is really not something that's sustainable on an actual production site. Any content changes made in Drupal would be lost as soon as I run the install. Writing the code for the import it was easier to just make new content every time I wrote some code. This ended up creating a lot of orphaned paragraph entities and also messed up all the entity counts. It got up to something like 100,000 entity IDs used even though each import only pulled in (at the time) 500 node or so. So, I decided to just embrace the "wipe and go" process and assume that there is no content in the Drupal system and make it on install without bothering to see if such content already exists.

Like I said, a dumb way to make a site. But whatever, it works.

