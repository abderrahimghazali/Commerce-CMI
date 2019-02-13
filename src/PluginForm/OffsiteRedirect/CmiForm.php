<?php

namespace Drupal\commerce_cmi\PluginForm\OffsiteRedirect;

use Drupal\commerce_payment\PluginForm\PaymentOffsiteForm;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;

/**
 * Class CmiForm
 * @package Drupal\commerce_cmi\PluginForm\OffsiteRedirect
 */
class CmiForm extends PaymentOffsiteForm
{
  /**
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state)
  {
    $form = parent::buildConfigurationForm($form, $form_state);

    /** @var \Drupal\commerce_payment\Entity\PaymentInterface $payment */
    $payment = $this->entity;
    /** @var \Drupal\commerce_payment\Plugin\Commerce\PaymentGateway\OffsitePaymentGatewayInterface $paymentGatewayPlugin */
    $paymentGatewayPlugin = $payment->getPaymentGateway()->getPlugin();
    $paymentConfiguration = $paymentGatewayPlugin->getConfiguration();
    /** @var \Drupal\commerce_order\Entity\Order $order */
    $order = $payment->getOrder();
    $entity_manager = \Drupal::entityTypeManager();
    $totalPrice = $order->getTotalPrice();
    $rnd = microtime();
    $current_lang = \Drupal::languageManager()->getCurrentLanguage()->getId();

    $redirect_url = $paymentConfiguration['api_url'];
    $redirect_method = 'post';

    $data = [
      'clientid'         => $paymentConfiguration['api_key'],
      'amount'           => number_format($totalPrice->getNumber(), 2),
      'okUrl'            => $form['#return_url'],
      'failUrl'          => $form['#cancel_url'],
      'TranType'         => 'PreAuth',
      'callbackUrl'      => Url::fromRoute('commerce_cmi.callback', [], ['absolute' => true])->toString(),
      'shopurl'          => Url::fromRoute('<front>', [], ['absolute' => TRUE])->toString(),
      'currency'         => '504',
      'rnd'              => $rnd,
      'storetype'        => '3D_PAY_HOSTING',
      'hashAlgorithm'    => 'ver3',
      'lang'             => $current_lang,
      'refreshtime'      => '5',
      'BillToName'       => $order->getBillingProfile()->get('address')->given_name . ' ' . $order->getBillingProfile()->get('address')->family_name,
      'BillToCompany'    => $order->getBillingProfile()->get('address')->organization,
      'BillToStreet1'    => $order->getBillingProfile()->get('address')->address_line1 . ' ' . $order->getBillingProfile()->get('address')->address_line2,
      'BillToCity'       => $order->getBillingProfile()->get('address')->locality,
      'BillToStateProv'  => $order->getBillingProfile()->get('address')->administrative_area,
      'BillToPostalCode' => $order->getBillingProfile()->get('address')->postal_code,
      'BillToCountry'    => $order->getBillingProfile()->get('address')->country_code,
      'email'            => $order->getEmail(),
      'encoding'         => 'UTF-8',
      'oid'              => $order->id(),
      //'tel'              => '',
      //'DIMCRITERIA1'     => '',
      'symbolCur'        => $totalPrice->getCurrencyCode(),
      // 'amountCur'        => '',
    ];
    //kpr($form['#cancel_url']);die;
    $data['HASH'] = $this->generate_hash($data ,$paymentConfiguration['secret_key']);

    return $this->buildRedirectForm($form, $form_state, $redirect_url, $data, $redirect_method);
  }

  /**
   * @param $data
   * @param $storeKey
   * @return string
   */
  public function generate_hash($data, $storeKey) {

    $postParams = array();
    foreach ($data as $key => $value){
      array_push($postParams, $key);
    }

    natcasesort($postParams);
    $hashval = "";
    foreach ($postParams as $param){
      $paramValue = trim($data[$param]);
      $escapedParamValue = str_replace("|", "\\|", str_replace("\\", "\\\\", $paramValue));

      $lowerParam = strtolower($param);
      if($lowerParam != "hash" && $lowerParam != "encoding" )	{
        $hashval = $hashval . $escapedParamValue . "|";
      }
    }

    $escapedStoreKey = str_replace("|", "\\|", str_replace("\\", "\\\\", $storeKey));
    $hashval = $hashval . $escapedStoreKey;

    $calculatedHashValue = hash('sha512', $hashval);
    $hash = base64_encode (pack('H*',$calculatedHashValue));

    return $hash;
  }
}