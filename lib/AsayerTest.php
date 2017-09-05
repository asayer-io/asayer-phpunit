<?php
namespace Asayer;
require_once __DIR__ . '/../vendor/autoload.php';
use PHPUnit\Framework\TestCase;

class AsayerTest extends TestCase{

    /**
     * @var \RemoteWebDriver
     */
    protected $webDriver;

    public function setUp()
    {

        $config_file = json_decode(file_get_contents('config/asayer.config.json'), true);
        $apikey = $config_file['apikey'];
        $server = $config_file['server'];
        $name=$config_file['name'];
        $platform=$config_file['capabilities']['platform'];
        $browserName=$config_file['capabilities']['browserName'];
    
$capabilities = array(
        \WebDriverCapabilityType::BROWSER_NAME => $browserName,
        "apikey"=>$apikey,
        "platform"=>$platform,
        "name"=>$name
        );     

        if(array_key_exists("tunnelId",$config_file) && strlen($config_file['tunnelId'])>0){            
            $capabilities["tunnel_id"]=$config_file['tunnelId'];
        }

        $this->webDriver = \RemoteWebDriver::create($server, $capabilities);
    }
    public function tearDown()
    {
        $this->webDriver->quit();
    }


}
?>