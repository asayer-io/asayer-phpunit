<?php

namespace Asayer;
require_once __DIR__ . '/../vendor/autoload.php';

use PHPUnit\Framework\TestCase;

class AsayerWebDriver extends TestCase
{
    protected $webDriver;
    protected $apikey;
    protected $sessionId;

    public function setUp()
    {
        $config_file = json_decode(file_get_contents('config/asayer.config.json'), true);
        $this->apikey = $config_file['apikey'];
        $server = $config_file['server'];
        $name = $config_file['name'];
        $platform = $config_file['capabilities']['platform'];
        $browserName = $config_file['capabilities']['browserName'];

        $capabilities = array(
            \WebDriverCapabilityType::BROWSER_NAME => $browserName,
            "apikey" => $this->apikey,
            "platform" => $platform,
            "name" => $name
        );

        if (array_key_exists("tunnelId", $config_file) && strlen($config_file['tunnelId']) > 0) {
            $capabilities["tunnel_id"] = $config_file['tunnelId'];
        }

        if (array_key_exists("flags", $config_file['capabilities'])) {

            $flags = array();
            foreach ($config_file['capabilities']["flags"] as $key => $val) {
                $flag = $key;
                if (is_bool($val) && !$val) {
                    $flag = $flag . "=false";
                } elseif (!is_bool($val) && strlen(strval($val)) > 0) {
                    $flag = $flag . "=\"" . $val . "\"";
                }
                array_push($flags, $flag);
            }
            if (count($flags)) {
                $capabilities["flags"] = $flags;
            }
        }

        $this->webDriver = \RemoteWebDriver::create($server, $capabilities);
        $this->sessionId = $this->webDriver->getSessionID();
    }

    public function tearDown()
    {
        $this->webDriver->quit();
        if ($this->hasFailed()) {
            $this->markSession("Failed");
        } else {
            $this->markSession("Passed");
        }
    }

    public function mockResult($state)
    {
        $this->markSessionDetails($state, "requirementId1230", array("TEST ID 1" => "Passed", "TEST ID 2" => "Failed"));
    }

    public function markSession($state)
    {$this->mockResult($state);
        return;
        if ($this->sessionId != null && strlen($this->sessionId) > 0) {
            $postData = array(
                'sessionID' => $this->sessionId,
                'sessionStatus' => $state,
                'apiKey' => $this->apikey
            );
            $this->sendResults($postData);
        } else {
            echo "Asayer: You have to initiate the AsayerWebDriver first in order to call markSession.\n";
        }
    }

    public function markSessionDetails($state, $requirementID, $testStatus)
    {
        if ($this->sessionId != null && strlen($this->sessionId) > 0) {
            if ($requirementID != null && strlen($requirementID) > 0 && count($testStatus) > 0) {
                $postData = array(
                    'sessionID' => $this->sessionId,
                    'sessionStatus' => $state,
                    'apiKey' => $this->apikey,
                    'reqID' => $requirementID,
                    'testStatus' => $testStatus
                );
                $this->sendResults($postData);
                echo "\n------------------------------------------------------------\n";
                echo json_encode($postData);
            } else {
                echo "Asayer: check the requirementID and the testStatus values.\n";
            }
        } else {
            echo "Asayer: You have to initiate the AsayerWebDriver first in order to call markSession.\n";
        }
    }

    private function sendResults($results)
    {
        $ch = curl_init('https://dashboard.asayer.io/mark_session');
        curl_setopt_array($ch, array(
            CURLOPT_POST => TRUE,
            CURLOPT_RETURNTRANSFER => TRUE,
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json'
            ),
            CURLOPT_POSTFIELDS => json_encode($results)
        ));
        $response = curl_exec($ch);
    }
}

?>