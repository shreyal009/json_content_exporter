<?php

namespace Drupal\json_content_exporter\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\node\NodeInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Drupal\Core\Logger\LoggerChannelFactoryInterface;
use Drupal\Core\Config\ConfigFactoryInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class SiteApiKeyController.
 *
 * @package Drupal\json_content_exporter\Controller
 */
class SiteApiKeyController extends ControllerBase {

  /**
   * Logger object.
   *
   * @var \Drupal\Core\Logger\LoggerChannelFactoryInterface
   */
  protected $logger;

  /**
   * Config factory object.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected  $config;

  /**
   * SiteApiKeyController constructor.
   *
   * @param \Drupal\Core\Logger\LoggerChannelFactoryInterface $logger
   *   The logger object.
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config
   *   The config object.
   */
  public function __construct(LoggerChannelFactoryInterface $logger, ConfigFactoryInterface $config) {
    $this->logger = $logger;
    $this->config = $config;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static (
      $container->get('logger.factory'),
      $container->get('config.factory')
    );
  }

  /**
   * Method to get node in json format.
   *
   * @param string $site_api_key
   *   The site api key.
   * @param \Drupal\node\NodeInterface $node
   *   The node object to get content.
   *
   * @return \Symfony\Component\HttpFoundation\JsonResponse
   *   The content in json format.
   */
  public function content($site_api_key, NodeInterface $node) {
    try {
      // Site API Key configuration value.
      $site_api_key_saved = $this->config('system.site')->get('siteapikey');

      // Make sure the configuration key matches the supplied key.
      if ($node->getType() == 'page' && $site_api_key_saved != 'No API Key yet' && $site_api_key_saved == $site_api_key) {
        // Respond with the json representation of the node.
        $node_data = $node->toArray();
        $response_code = 200;
      }
      else {
        $node_data = ["error" => "access denied"];
        $response_code = 401;
      }

      return new JsonResponse($node_data, $response_code, ['Content-Type' => 'application/json']);
    }
    catch (\Exception $e) {
      $this->logger->warning($e->getMessage());
    }
  }

}
