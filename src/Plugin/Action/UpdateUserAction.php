<?php

namespace Drupal\bulk_author_update\Plugin\Action;

use Drupal\Core\Action\ActionBase;
use Drupal\Core\Session\AccountInterface;

/**
 * Action to close webforms in bulk.
 *
 * @Action(
 *   id = "update_user_action",
 *   label = @Translation("Update author"),
 *   type = "node",
 *   confirm = TRUE,
 * )
 */
class UpdateUserAction extends ActionBase {

  /**
   * {@inheritdoc}
   */
  public function execute($entity = NULL) {
    if (\Drupal::state()->get('bulk_author_update_config')) {
      $config_data = \Drupal::state()->get('bulk_author_update_config');
      $new_author = $config_data['author'];
      $current_author = $entity->getOwnerId();

      if ($current_author != $new_author) {
        $entity->setOwnerId($new_author);
        $entity->setNewRevision();
        $entity->save();
      }
    }
    else {
      $messenger = \Drupal::messenger();
      $messenger->addMessage($this->t('No author was set in the configuration form. Please select an author.'), $messenger::TYPE_ERROR);
    }
  }

  /**
   * {@inheritdoc}
   */
  public function access($object, AccountInterface $account = NULL, $return_as_object = FALSE) {
    if ($object->getEntityType() === 'node') {
      $access = $object->access('update', $account, TRUE)
        ->andIf($object->status->access('edit', $account, TRUE));
      return $return_as_object ? $access : $access->isAllowed();
    }
    return TRUE;
  }

}
