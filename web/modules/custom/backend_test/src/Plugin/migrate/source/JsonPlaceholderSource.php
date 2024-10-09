<?php

namespace Drupal\backend_test\Plugin\migrate\source;

use Drupal\migrate\Plugin\migrate\source\SourcePluginBase;
use Drupal\migrate\Row;

/**
 * Source plugin for JSONPlaceholder data.
 *
 * @MigrateSource(
 *   id = "json_placeholder_source"
 * )
 */
class JsonPlaceholderSource extends SourcePluginBase {

  /**
   * {@inheritdoc}
   */
  public function fields() {
    return [
      'id' => $this->t('User ID'),
      'name' => $this->t('Name'),
      'username' => $this->t('Username'),
      'email' => $this->t('Email'),
      'address' => $this->t('Address'),
      'phone' => $this->t('Phone'),
      'website' => $this->t('Website'),
      'company_name' => $this->t('Company Name'),
      'company_catchphrase' => $this->t('Company Catchphrase'),
      'company_bs' => $this->t('Company BS'),
      'json_filename' => $this->t('Source JSON filename'),
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function prepareRow(Row $row) {
    $name = $row->getSourceProperty('name');
    if (strlen($name) > 255) {
      $row->setSourceProperty('name', substr($name, 0, 255));
    }

    return parent::prepareRow($row);
  }

  /**
   * {@inheritdoc}
   */
  public function getIds() {
    return [
      'id' => [
        'type' => 'integer',
      ],
    ];
  }

  /**
   * Initializes the iterator with the source data.
   *
   * @return \Iterator
   *   An iterator containing the data for this source.
   */
  protected function initializeIterator() {
    $url = $this->getSourceUrl();
    $json = file_get_contents($url);
    $parser = json_decode($json, TRUE);

    $rows = [];
    $count = 0;

    foreach ($parser as $data) {
      if ($count >= 3) {
        break;
      }

      $data['address_street'] = $data['address']['street'] ?? '';
      $data['address_suite'] = $data['address']['suite'] ?? '';
      $data['address_city'] = $data['address']['city'] ?? '';
      $data['address_zipcode'] = $data['address']['zipcode'] ?? '';
      $data['address_geo_lat'] = $data['address']['geo']['lat'] ?? '';
      $data['address_geo_lng'] = $data['address']['geo']['lng'] ?? '';

      if (!empty($data['company'])) {
        $data['company_name'] = $data['company']['name'];
        $data['company_catchphrase'] = $data['company']['catchPhrase'];
        $data['company_bs'] = $data['company']['bs'];
      }

      $rows[] = $data;
      $count++;
    }

    return new \ArrayIterator($rows);
  }

  /**
   * Returns the source URL for the data.
   *
   * @return string
   *   The source URL.
   */
  public function getSourceUrl() {
    return 'https://jsonplaceholder.typicode.com/users';
  }

  /**
   * {@inheritdoc}
   */
  public function __toString() {
    return "JSON Placeholder data";
  }

}
