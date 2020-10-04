<?php

namespace Drupal\Tests\bulk_author_update\Functional;

use Drupal\Tests\BrowserTestBase;

/**
 * Test module.
 *
 * @group multiple_select
 */
class ConfigFormTest extends BrowserTestBase {

  /**
   * {@inheritdoc}
   */
  public static $modules = [
    'bulk_author_update',
    'node',
  ];

  /**
   * {@inheritdoc}
   */
  protected $defaultTheme = 'seven';

  /**
   * Test access to configuration page.
   */
  public function testCanAccessConfigPage() {
    $account = $this->drupalCreateUser([
      'access bulk author update config page',
      'access content',
    ]);

    $this->drupalLogin($account);
    $this->drupalGet('/admin/content/bulkauthorupdate/config');
    $this->assertText('Bulk Author Update Configuration');
  }

}
