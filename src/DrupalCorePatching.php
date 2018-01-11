<?php

namespace AaronMcGowan\Composer\Patching;

use Composer\Composer;
use Composer\EventDispatcher\EventSubscriberInterface;
use Composer\IO\IOInterface;
use Composer\Plugin\PluginInterface;
use cweagans\Composer\PatchEvent;
use cweagans\Composer\PatchEvents;

/**
 * ...
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

  public function activate(Composer $composer, IOInterface $io) {
    $this->composer = $composer;
    $this->io = $io;
  }

  public static function getSubscribedEvents() {
    return [
      PatchEvents::PRE_PATCH_APPLY => ['onPrePatchApply'],
    ];
  }

  public function onPrePatchApply(PatchEvent $event) {
    $package = $event->getPackage();
    // if ($package->getName() == 'drupal/drupal' && $event->getInstallPath() == 'docroot/core') {

    if ($this->io->isVeryVerbose()) {
      $this->io->write(sprintf("Checking package '%s [id: %s]' to match installation path for altering..."), $package->getName(), $package->getId());
    }

    if ($event->getInstallPath() == 'docroot/core') {
      $event->setInstallPath('docroot');
      if ($this->io->isVerbose()) {
        $this->io->write(sprintf("'%s [id: %s]' installation path has been altered for patching.", $package->getName(), $package->getId()));
      }
    }
  }
}
