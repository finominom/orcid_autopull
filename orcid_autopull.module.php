<?php

use Drupal\orcid_autopull\Service\OrcidApi;

/**
 * Implements hook_entity_presave().
 */
function orcid_autopull_entity_presave(\Drupal\Core\Entity\EntityInterface $entity) {
  if ($entity->getEntityTypeId() === 'node' && $entity->bundle() === 'author') {
    if ($entity->hasField('field_orcid_id') && !$entity->get('field_orcid_id')->isEmpty()) {
      $orcid = $entity->get('field_orcid_id')->value;

      /** @var \Drupal\orcid_autopull\Service\OrcidApi $api */
      $api = \Drupal::service('orcid_autopull.api');

      // Keywords
      $keywords = $api->fetchKeywords($orcid);
      if (!empty($keywords) && $entity->hasField('field_author_keywords')) {
        $entity->set('field_author_keywords', implode(', ', $keywords));
      }

      // Employment
      $jobs = $api->fetchEmployment($orcid);
      if (!empty($jobs) && $entity->hasField('field_author_employment')) {
        $entity->set('field_author_employment', implode("\n", $jobs));
      }

      // Education
      $schools = $api->fetchEducation($orcid);
      if (!empty($schools) && $entity->hasField('field_author_education')) {
        $entity->set('field_author_education', implode("\n", $schools));
      }
    }
  }
}
