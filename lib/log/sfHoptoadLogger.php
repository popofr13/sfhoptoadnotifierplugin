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

    /**
     * Services_Hoptoad object.
     *
     * @var Services_Hoptoad
     */
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

    $params = $options;
    
    // Environment (prod, test, ...)
    $params['env'] = sfConfig::get('sf_environment', 'cli');

    // Instanciate the service
    $this->hoptoadClient = sfHoptoadNotifierPluginConfiguration::getHoptoadClient($params);
  }

}
