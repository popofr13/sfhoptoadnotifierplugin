<?php

/**
 * sfHoptoadLogger logs messages with the Hotpoad.
 *
 * @package    sfHoptoadPlugin.lib
 * @subpackage log
 *
 * @subpackage log
 * @author     Sébastien Porati (http://sebastien.porati.me/blog/)
 */
class sfHoptoadLogger extends sfLogger {

  protected $apiKey = null,
            $client = 'curl';

  /* instance of rich's client */
  private $hoptoadClient;

  /**
   * Logs a message.
   *
   * @param string $message   Message
   * @param string $priority  Message priority
   */
  protected function doLog($message, $priority) {
      $exception = new Exception($message);
      
      $this->hoptoadClient->exceptionHandler($exception);
  }

  /**
   * Initializes this logger.
   *
   * Available options:
   *
   * - api_key:     The project API key
   * - client:      Call method (pear, curl, or zend)
   *
   * @param  sfEventDispatcher $dispatcher  A sfEventDispatcher instance
   * @param  array             $options     An array of options.
   *
   * @return Boolean      true, if initialization completes successfully, otherwise false.
   */
  public function initialize(sfEventDispatcher $dispatcher, $options = array())
  {
    parent::initialize($dispatcher, $options);

    // load the lib
    require_once(dirname(__FILE__) . '/../rich-php-hoptoad-notifier/Hoptoad.php');

    // API key
    if (!isset($options['api_key']))
    {
      throw new sfConfigurationException('You must provide an "api_key" parameter for this logger.');
    }
    $this->apiKey = $options['api_key'];

    // Client (pear, curl, or zend)
    if (isset($options['client']))
    {
      $this->client = $options['client'];
    }
    
    // Environment (prod, test, ...)
    $env = sfConfig::get('sf_environment', 'prod');

    // Instanciate the service
    $this->hoptoadClient = new Services_Hoptoad($this->apiKey, $env, $this->client);
  }

}
