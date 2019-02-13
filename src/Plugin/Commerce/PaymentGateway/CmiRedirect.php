<?php
namespace Drupal\commerce_cmi\Plugin\Commerce\PaymentGateway;

use Drupal\commerce_order\Entity\OrderInterface;
use Drupal\commerce_payment\Plugin\Commerce\PaymentGateway\OffsitePaymentGatewayBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\HttpFoundation\Request;
use Drupal\Core\Url;
use Drupal\commerce_payment\Exception\PaymentGatewayException;

/**
 * Provides the CMI offsite Checkout payment gateway.
 *
 * @CommercePaymentGateway(
 *   id = "cmi_redirect_checkout",
 *   label = @Translation("CMI (Redirect to cmi)"),
 *   display_label = @Translation("CMI"),
 *    forms = {
 *     "offsite-payment" = "Drupal\commerce_cmi\PluginForm\OffsiteRedirect\CmiForm",
 *   },
 * )
 */

class CmiRedirect extends OffsitePaymentGatewayBase {

  /**
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state)
  {
    $form = parent::buildConfigurationForm($form, $form_state);

    $api_url = !empty($this->configuration['api_url']) ? $this->configuration['api_url'] : '';
    $api_key = !empty($this->configuration['api_key']) ? $this->configuration['api_key'] : '';
    $secret_key = !empty($this->configuration['secret_key']) ? $this->configuration['secret_key'] : '';

    $form['api_url'] = [
      '#type' => 'textfield',
      '#title' => $this->t('API URL'),
      '#default_value' => $api_url,
      '#description' => $this->t('API URL from CMI Commerce.'),
      '#required' => TRUE
    ];
    $form['api_key'] = [
      '#type' => 'textfield',
      '#title' => $this->t('API Key'),
      '#default_value' => $api_key,
      '#description' => $this->t('API Key from CMI Commerce.'),
      '#required' => TRUE
    ];
    $form['secret_key'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Shared Secret'),
      '#default_value' => $secret_key,
      '#description' => $this->t('Shared Secret Key from CMI Commerce subscriptions.'),
      '#required' => TRUE
    ];
    $form['mode']['#access'] = FALSE;

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration()
  {
    return [
        'api_key' => '',
        'secret_key' => '',
        'api_url' => '',
      ] + parent::defaultConfiguration();
  }

  /**
   * {@inheritdoc}
   */
  public function validateConfigurationForm(array &$form, FormStateInterface $form_state)
  {
    parent::validateConfigurationForm($form, $form_state);
    if (!$form_state->getErrors() && $form_state->isSubmitted()) {
      $values = $form_state->getValue($form['#parents']);
      $this->configuration['api_key'] = $values['api_key'];
      $this->configuration['secret_key'] = $values['secret_key'];
      $this->configuration['api_url'] = $values['api_url'];
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitConfigurationForm(array &$form, FormStateInterface $form_state)
  {
    parent::submitConfigurationForm($form, $form_state);
    if (!$form_state->getErrors()) {
      $values = $form_state->getValue($form['#parents']);
      $this->configuration['api_key'] = $values['api_key'];
      $this->configuration['secret_key'] = $values['secret_key'];
      $this->configuration['api_url'] = $values['api_url'];
    }
  }

  /**
   * {@inheritdoc}
   */
  public function onReturn(OrderInterface $order, Request $request)
  {
    try {
      $chargeId = $order->getData('charge_id');
      kpr($chargeId);die;
    } catch (\Exception $exception) {
      throw new PaymentGatewayException('Payment failed!');
    }
    return true;
  }
}


