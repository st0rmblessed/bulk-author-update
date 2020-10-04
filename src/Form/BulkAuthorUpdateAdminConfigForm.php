<?php

namespace Drupal\bulk_author_update\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\State\StateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Entity\EntityFieldManagerInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\user\Entity\User;

/**
 * BulkAuthorUpdate form.
 */
class BulkAuthorUpdateAdminConfigForm extends FormBase {

  /**
   * The state.
   *
   * @var \Drupal\Core\State\StateInterface
   */
  protected $state;

  /**
   * Provides an interface for an entity field manager.
   *
   * @var \Drupal\Core\Entity\EntityFieldManagerInterface
   */
  protected $entityFieldManager;

  /**
   * Provides an interface for entity type managers.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * ReportWorkerBase constructor.
   *
   * @param \Drupal\Core\State\StateInterface $state
   *   The state service the instance should use.
   * @param \Drupal\Core\Entity\EntityFieldManagerInterface $entity_field_manager
   *   Provides an interface for an entity field manager.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   Provides an interface for entity type managers.
   */
  public function __construct(StateInterface $state, EntityFieldManagerInterface $entity_field_manager, EntityTypeManagerInterface $entity_type_manager) {
    $this->state = $state;
    $this->entityFieldManager = $entity_field_manager;
    $this->entityTypeManager = $entity_type_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
          $container->get('state'),
          $container->get('entity_field.manager'),
          $container->get('entity_type.manager')
      );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'bulk_author_update_admin_config_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    $data = $this->state->get('bulk_author_update_config');

    // Load previously saved config.
    if (!is_null($data)) {
      $bulk_author_update_configs = $data;
      $default_user = User::load($data['author']);
      // Get the user image.
      if (!$default_user->user_picture->isEmpty()) {
        $displayImg = file_create_url($default_user->user_picture->entity->getFileUri());
      }
      else {
        $displayImg = '';
      }
    }

    // Build form.
    $form['field_set_1']['author'] = [
      '#type' => 'entity_autocomplete',
      '#description' => $this->t('The user to be selected to be set as author of chosen nodes.'),
      '#default_value' => isset($default_user) ? $default_user : '',
      '#title' => 'Author',
      '#target_type' => 'user',
    ];

    $form['field_set_2'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('About the user'),
    ];

    $form['field_set_2']['photo'] = [
      '#prefix' => '<p>',
      '#suffix' => '</p>',
      '#markup' => isset($default_user) ? '<img src="' . $displayImg . '" width="320" height="180">' : '',
    ];

    $form['field_set_2']['username'] = [
      '#prefix' => '<p><strong>Username: </strong>',
      '#suffix' => '</p>',
      '#markup' => isset($default_user) ? $default_user->getDisplayName() : '',
    ];

    $form['field_set_2']['email'] = [
      '#prefix' => '<p><strong>Email: </strong>',
      '#suffix' => '</p>',
      '#markup' => isset($default_user) ? $default_user->getEmail() : '',
    ];

    $form['field_set_2']['user_id'] = [
      '#prefix' => '<p><strong>User ID: </strong>',
      '#suffix' => '</p>',
      '#markup' => isset($default_user) ? $data['author'] : '',
    ];

    $form['field_set_2']['roles'] = [
      '#prefix' => '<p><strong>Roles: </strong>',
      '#suffix' => '</p>',
      '#markup' => isset($default_user) ? implode(", ", $default_user->getRoles()) : '',
    ];

    $form['field_set_2']['create_time'] = [
      '#prefix' => '<p><strong>Created: </strong>',
      '#suffix' => '</p>',
      '#markup' => isset($default_user) ? date('d/m/Y H:i:s', $default_user->getCreatedTime()) : '',
    ];

    $form['field_set_2']['last_login'] = [
      '#prefix' => '<p><strong>Last Login: </strong>',
      '#suffix' => '</p>',
      '#markup' => isset($default_user) ? date('d/m/Y H:i:s', $default_user->getLastLoginTime()) : '',
    ];

    $form['field_set_2']['last_access'] = [
      '#prefix' => '<p><strong>Last Access: </strong>',
      '#suffix' => '</p>',
      '#markup' => isset($default_user) ? date('d/m/Y H:i:s', $default_user->getLastAccessedTime()) : '',
    ];

    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Save Configuration'),
    ];
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {

  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {

    // Set values in form state.
    if ($form_state->getValue('author') === NULL) {
      $bulk_author_update_configs = NULL;
    }
    else {
      $bulk_author_update_configs['author'] = $form_state->getValue('author');
    }

    // Save form state.
    $this->state->set('bulk_author_update_config', $bulk_author_update_configs);

    // Display success message.
    $this->messenger()->addStatus($this->t('Configurations successfully saved.'));
  }

}
