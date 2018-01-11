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

  protected $composer;

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
    if ($package->getName() == 'drupal/drupal' && $event->getInstallPath() == 'docroot/core') {
      $event->setInstallPath('docroot');
    }
  }
}
