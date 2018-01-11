<?php

namespace AaronMcGowan\Composer\Patching;

use Composer\Composer;
use Composer\EventDispatcher\EventSubscriberInterface;
use Composer\IO\IOInterface;
use Composer\Plugin\PluginInterface;
use cweagans\Composer\PatchEvent;
use cweagans\Composer\PatchEvents;

/**
 * Provides a plugin to act on `cweagans/composer-patches` PatchEvents.
 *
 * This plugin is designed to provide assistance until upstream packages, such
 * as the `cweagans/composer-patches` and Composer's `install-paths` property
 * can effectively handle patches applied to Drupal 8 core.
 *
 * Patches applied to the Drupal 8 core using the composer-patches
 * package are applied to `docroot/core` when the Drupal 8 core project's
 * installation path is set to `docroot/core`. This is problematic as a result
 * of patches needing to be applied to the directory, `docroot` vs. the
 * composer.json specified `docroot/core`. Typical symptoms of incorrect patches
 * being applied are the creation of new files in directories such as:
 *
 *   - `docroot/core/b`
 *   - `docroot/core/core`
 *
 * This plugin simply acts on the PatchEvents::PRE_PATCH_APPLY event when a
 * modified version of cweagans/composer-patches is used that allows for the
 * alteration of the directory in which the patches are applied to.
 */
class DrupalCorePatching implements PluginInterface, EventSubscriberInterface {

  /**
   * @var Composer
   */
  protected $composer;

  /**
   * @var IOInterface
   */
  protected $io;

  /**
   * {@inheritdoc}
   */
  public function activate(Composer $composer, IOInterface $io) {
    $this->composer = $composer;
    $this->io = $io;
  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    return [
      PatchEvents::PRE_PATCH_APPLY => ['onPrePatchApply'],
    ];
  }

  /**
   * Acts on the PatchEvents::PRE_PATCH_APPLY.
   *
   * Modifies the installation path in which patches are applied for the
   * Drupal 8 package (`drupal/core`) when the install path is set to
   * `docroot/core`.
   *
   * @param \cweagans\Composer\PatchEvent $event
   *   The event.
   */
  public function onPrePatchApply(PatchEvent $event) {
    $package = $event->getPackage();
    if ($package->getName() == 'drupal/core' && $event->getInstallPath() == 'docroot/core') {
      $event->setInstallPath('docroot');
      if ($this->io->isVerbose()) {
        $this->io->write(sprintf("'%s [id: %s]' installation path has been altered for patching.", $package->getName(), $package->getId()));
      }
    }
  }

}
