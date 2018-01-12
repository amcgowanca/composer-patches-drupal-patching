## Patching Drupal 8 core with `cweagans/composer-patches`

This package provides a work-around for the issue in which Drupal 8 core package patches, applied through the use of `cweagans/composer-patches`, does not correctly apply patches to Drupal core itself. Instead, a common symptom of incorrectly applied patches is the creation of the directories: `docroot/core/b/` or `docroot/core/core`. This appears to be the result of a `composer.json`'s specification of the `drupal/core` project's installation path being `docroot/core`. This is commonly seen in Acquia BLT (and potentially Acquia Lightning) based projects.

This Composer Plugin provides a mechanism to resolve the issue so that patches defined in a `composer.json` for Drupal core can be applied to `docroot` vs. `docroot/core` through modification of the installation path at time of pre-patch apply events that are fired through `cweagans/composer-patches` plugin. However, this ultimately warrants modifications to the Composer Patches project to allow for the modification of the `$install_path` value which can be seen through the changes outlined [here](https://github.com/amcgowanca/composer-patches/commit/2eeba8aa7ecca90b69a6c7386522635783e553cf).

### Usage

- The project's `composer.json` file *must* specify the new repository in which the `cweagans/composer-patches` project is retrieved from. This can be done by adding the following to your `composer.json`'s `repositories` property:

```
"cweagans/composer-patches": {
  "type": "vcs",
  "url": "git@github.com:amcgowanca/composer-patches.git"
},
``` 

- Your `composer.json` should specify as a `required` package, the `cweagans/composer-patches` as the first possible package to retrieve (prior to Acquia BLT _or_ Lightning where `"merge-plugin"."replace"` is `false`). This package should have a version of `1.x-dev`.

```
"cweagans/composer-patches": "1.x-dev"
```

- Your `composer.json` should require this package, available as:

```
"amcgowanca/composer-patches-drupal-patching": "^0.0.1"
```

- Depending on your current workflow for your project, you may need to execute a "nuke" operation to rebuild your project's dependencies effectively and accurately. _Note_ that you may (and it is likely) during your first time for executing a complete `composer install` or update operation to notice that some of the defined Drupal 8 core patches no longer apply. It should be noted that these most likely did not ever apply and silently were ignored.

### Related issues & resources

The following is a collection of high-level issues and resources related to the _core_ problem that this plugin (and the modified `cweagans/composer-patches`) helps address:

* https://github.com/cweagans/composer-patches/issues/178
* https://github.com/cweagans/composer-patches/pull/165
* https://github.com/cweagans/composer-patches/issues/174
* https://www.drupal.org/project/drupal/issues/1356276?page=1#comment-12277804
* https://github.com/acquia/blt/issues/2309
* https://docs.acquia.com/article/fixing-failing-composer-patches-updating-gnu-patch

### Example repository

An example repository has been put together to contain an example of a clean Acquia BLT project setup in which the error that this Composer Plugin aims to resolve, in addition to an example in which the problem at hand is in fact solved. More information can be found at [amcgowanca/composer-patches-drupal-patching-proof](https://github.com/amcgowanca/composer-patches-drupal-patching-proof).
