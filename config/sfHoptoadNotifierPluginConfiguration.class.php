<?php

/**
 * sfHoptoadNotifierPlugin configuration.
 *
 * @package     sfHoptoadNotifierPlugin
 */
class sfHoptoadNotifierPluginConfiguration extends sfPluginConfiguration
{

    /**
     * Services_Hoptoad object.
     *
     * @var Services_Hoptoad
     */
    private $hoptoadClient = array();

    private static $hoptoadClients = array();
  
    /**
     * Return HoptoadClient.
     *
     * @param array $params API Key and client parameters (optionnal)
     *
     * @return Services_Hoptoad
     */
    public static function getHoptoadClient($params = array()) {
        $values = '';
        foreach($params as $value) {
            $values .= $value;
        }
        $idx = md5($values);

        if (isset(self::$hoptoadClients[$idx]) == false) {
            $apiKey = null;
            $env    = null;
            $client = null;

            // API Key
            if (isset($params['api_key']) == true && empty($params['api_key']) == false) {
                $apiKey = $params['api_key'];
            } else {
                $apiKey = sfConfig::get('app_sf_hoptoad_notifier_plugin_api_key', false);
            }

            if (empty($apiKey) == true)
            {
                return null;
                //throw new sfConfigurationException('You must provide an "api_key"');
            }

            // Client
            if (isset($params['client']) == true && empty($params['client']) == false) {
                $client = $params['client'];
            } else {
                $client = sfConfig::get('app_sf_hoptoad_notifier_plugin_client', 'curl');
            }

            // Environnement
            if (isset($params['env']) == true && empty($params['env']) == false) {
                $env = $params['env'];
            } else {
                $env = sfConfig::get('sf_environment', 'prod');
            }

            require_once(dirname(__FILE__) . '/../lib/rich-php-hoptoad-notifier/Hoptoad.php');
            self::$hoptoadClients[$idx] = new Services_Hoptoad($apiKey, $env, $client);
        }

        return self::$hoptoadClients[$idx];
    }
  
  /**
   * @see sfPluginConfiguration
   */
  public function initialize()
  {
    // Environment (prod, test, ...)
    $env = 'cli';
    try {
      $env = $this->configuration->getEnvironment();
    } catch (Exception $e) {}

    // Instanciate the service
    $this->hoptoadClient = self::getHoptoadClient(array('env' => $env));
    
    // handle general php errors
    // (commented, because we will do it through sf handlers)
    // $hoptoad->installNotifierHandlers();

    // handle sf exceptions
    $this->dispatcher->connect(
      'application.throw_exception',
      array('sfHoptoadNotifier', 'handleExceptionEvent')
    );

    // handle log errors, ...
    $this->dispatcher->connect(
      'application.log',
      array('sfHoptoadNotifier', 'handleLogEvent')
    );
  }

  public function configure()
  {
    // in this method, config (app.yml) is not already loaded, so we must not initialize the hoptoad object.
  }
}
