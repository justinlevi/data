<?php
/**
 * @file
 * Contains \Drupal\ctools\Access\TempstoreAccess.
 */

namespace Drupal\ctools\Access;


use Drupal\Core\Access\AccessResult;
use Drupal\Core\Routing\Access\AccessInterface as CoreAccessInterface;
use Drupal\Core\Routing\RouteMatch;
use Drupal\Core\Session\AccountInterface;
use Drupal\ctools\Access\AccessInterface as CToolsAccessInterface;
use Drupal\user\SharedTempStoreFactory;
use Symfony\Component\Routing\Route;

class TempstoreAccess implements CoreAccessInterface {

  /**
   * The shared tempstore factory.
   *
   * @var \Drupal\user\SharedTempStoreFactory
   */
  protected $tempstore;

  public function __construct(SharedTempStoreFactory $tempstore) {
    $this->tempstore = $tempstore;
  }

  protected function getTempstore() {
    return $this->tempstore;
  }

  public function access(Route $route, RouteMatch $match, AccountInterface $account) {
    $tempstore_id = $route->getDefault('tempstore_id');
    $id = $match->getParameter($route->getRequirement('_ctools_access'));
    if ($tempstore_id && $id) {
      $cached_values = $this->getTempstore()->get($tempstore_id)->get($id);
      if (!empty ($cached_values['access']) && ($cached_values['access'] instanceof CToolsAccessInterface)) {
        return $cached_values['access']->access($account);
      }
    }
    return AccessResult::forbidden();
  }
}