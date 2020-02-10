<?php

namespace Drupal\json_content_exporter\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\system\Form\SiteInformationForm;

/**
 * Class ExtendedSiteInformationForm.
 *
 * @package Drupal\json_content_exporter\Form
 */
class ExtendedSiteInformationForm extends SiteInformationForm {

  /**
   * Add new element to the form.
   *
   * @param array $form
   *   The form object.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The form state of form.
   *
   * @return array
   *   returns the final form array.
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $site_config = $this->config('system.site');
    $form = parent::buildForm($form, $form_state);
    $form['site_information']['siteapikey'] = [
      '#type' => 'textfield',
      '#title' => t('Site API Key'),
      '#default_value' => $site_config->get('siteapikey') ?: 'No API Key yet',
      '#description' => t("Custom field to set the API Key"),
    ];

    $form['actions']['submit']['#value'] = t('Update Configuration');

    return $form;
  }

  /**
   * Submit handler for the form.
   *
   * @param array $form
   *   The form object.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The form state of form.
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $siteapikey = $form_state->getValue('siteapikey');
    $this->config('system.site')
      ->set('siteapikey', $siteapikey)
      ->save();
    parent::submitForm($form, $form_state);
    $messenger = \Drupal::messenger();
    $messenger->addMessage(t('The Site API key has been saved with the value as @siteapikey',['@siteapikey' => $siteapikey]));
  }

}
