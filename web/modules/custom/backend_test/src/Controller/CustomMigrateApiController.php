<?php

namespace Drupal\backend_test\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\user\Entity\User;

/**
 * Provides a controller for displaying user data with company information.
 */
class CustomMigrateApiController extends ControllerBase {

  /**
   * Returns a renderable array containing user data.
   *
   * @return array
   *   A renderable array containing the user data.
   */
  public function content() {
    // Load all user entities.
    $users = \Drupal::entityTypeManager()->getStorage('user')->loadMultiple();
    $users_data = [];

    // Loop through each user entity.
    foreach ($users as $user) {
      if ($user instanceof User) {
        // Check for the company reference field.
        $company_reference = $user->get('field_company_reference')->entity;
        $company_name = $company_reference ? $company_reference->getTitle() : $this->t('No company assigned');

        // Gather user data.
        $users_data[] = [
          'username' => $user->getAccountName() ?? $this->t('No username'),
          'email' => $user->getEmail() ?? $this->t('No email'),
          'company' => $company_name,
        ];
      }
    }

    // Render the data using a Twig template.
    return [
      '#theme' => 'user_data_template',
      '#users' => $users_data,
      '#cache' => ['max-age' => 0],
    ];
  }

}
