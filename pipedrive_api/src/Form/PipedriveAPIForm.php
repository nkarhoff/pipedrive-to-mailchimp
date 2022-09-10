<?php 

namespace Drupal\pipedrive_api\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Implements the form to get the Cloud Convert API key.
 */

class PipeDriveAPIForm extends FormBase {

  /**
     * {@inheritdoc}
  */
  public function getFormId() {
      return 'api_key';
  }
  /**
     * {@inheritdoc}
  */
  public function buildForm(array $form, FormStateInterface $form_state) {
    // Decrypts the api key
    $ciphertext = \Drupal::config('pipedrive_api.settings')->get('api_key');
    $api_key_decode = base64_decode($ciphertext);
      $cipher = "AES-256-CBC";
      $key = \Drupal::config('system.site')->get('encrypt_drupal_variable_key');
      $iv = \Drupal::config('system.site')->get('encrypt_decrypt_iv');
      $api_key = openssl_decrypt($api_key_decode, $cipher, $key, OPENSSL_RAW_DATA, $iv);

      $company_domain = \Drupal::config('pipedrive_api.settings')->get('company_domain');

      // Mailchimp API token
      // Decrypt the API Key
      $mailchimpciphertext = \Drupal::config('pipedrive_api.settings')->get('mailchimp_api_key');
      $mail_api_key_decode = base64_decode($mailchimpciphertext);
      $mailchimp_api_key = openssl_decrypt($mail_api_key_decode, $cipher, $key, OPENSSL_RAW_DATA, $iv);

          // Mailchimp Server
      $mailchimp_server = \Drupal::config('pipedrive_api.settings')->get('mailchimp_server');

      // Mailchimp List ID
      $mailchimp_list_id = \Drupal::config('pipedrive_api.settings')->get('mailchimp_listid');

      $form['api_key'] = [
        '#type' => 'textfield',
        '#title' => $this->t('Enter your API key from Pipedrive'),
        '#default_value' => $api_key,
        '#size' => 72,
        '#maxlength' => 72,
        '#description' => $this->t(
            'The API key provided by Pipedrive.'
        ),
        '#required' => TRUE,
      ];
      $form['company_domain'] = [
        '#type' => 'textfield',
        '#title' => $this->t('Company Domain'),
        '#default_value' => $company_domain,
        '#size' => 72,
        '#maxlength' => 72,
        '#description' => $this->t(
            'The company domain in Pipedrive. Example: XXXXXXXXXX'
        ),
        '#required' => TRUE,
      ];
      $form['mailchimp_api_key'] = [
        '#type' => 'textfield',
        '#title' => $this->t('Mailchimp API Key'),
        '#default_value' => $mailchimp_api_key,
        '#size' => 72,
        '#maxlength' => 72,
        '#description' => $this->t(
            'The API key provided by Mailchimp.'
        ),
        '#required' => TRUE,
      ];
      $form['mailchimp_server'] = [
        '#type' => 'textfield',
        '#title' => $this->t('Mailchimp Serve Prefix'),
        '#default_value' => $mailchimp_server,
        '#size' => 72,
        '#maxlength' => 72,
        '#description' => $this->t(
            'Example: https://us19.admin.mailchimp.com/; the us19 part is the server prefix.'
        ),
        '#required' => TRUE,
      ];
      $form['mailchimp_listid'] = [
        '#type' => 'textfield',
        '#title' => $this->t('Mailchimp List ID'),
        '#default_value' => $mailchimp_list_id,
        '#size' => 72,
        '#maxlength' => 72,
        '#description' => $this->t(
            'Audience List ID. Can be found in Audience > Settings in Mailchimp.'
        ),
        '#required' => TRUE,
      ];
      $form['submit'] = [
        '#type' => 'submit',
        '#value' => $this->t('Submit'),
      ];
      return $form;
  }
  /**
     * {@inheritdoc}
  */
  public function submitForm(array &$form, FormStateInterface $form_state) {
      // Get variables needed for encryption
      $api_key = $form['api_key']['#value'];
      $company_domain = $form['company_domain']['#value'];
      $mailchimp_api_key = $form['mailchimp_api_key']['#value'];
      $mailchimp_server = $form['mailchimp_server']['#value'];
      $mailchimp_listid = $form['mailchimp_listid']['#value'];
      $cipher = "AES-256-CBC";
      $key = \Drupal::config('system.site')->get('encrypt_drupal_variable_key');
      $iv = \Drupal::config('system.site')->get('encrypt_decrypt_iv');

      // Encrypt the api_key
      $ciphertext = base64_encode(openssl_encrypt($api_key, $cipher, $key, OPENSSL_RAW_DATA, $iv));

      $mailchimpciphertext = base64_encode(openssl_encrypt($mailchimp_api_key, $cipher, $key, OPENSSL_RAW_DATA, $iv));


      // Set encrypted key to variable in database
      \Drupal::configFactory()->getEditable('pipedrive_api.settings')->set('api_key', $ciphertext)->save();
      \Drupal::configFactory()->getEditable('pipedrive_api.settings')->set('company_domain', $company_domain)->save();
      \Drupal::configFactory()->getEditable('pipedrive_api.settings')->set('mailchimp_api_key', $mailchimpciphertext)->save();
      \Drupal::configFactory()->getEditable('pipedrive_api.settings')->set('mailchimp_server', $mailchimp_server)->save();
      \Drupal::configFactory()->getEditable('pipedrive_api.settings')->set('mailchimp_listid', $mailchimp_listid)->save();

      // Flush caches
      drupal_flush_all_caches();
      // drupal_set_message('The form was submitted.');
    }

  

}