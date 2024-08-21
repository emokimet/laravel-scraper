<?php

error_reporting(E_ERROR | E_PARSE);

// Concatenate and parse args into $_REQUEST if called using CLI
if (php_sapi_name() === 'cli') {
    parse_str(implode('&', array_slice($argv, 1)), $_REQUEST);
}

require __DIR__ . '/../vendor/autoload.php';
use GuzzleHttp\Cookie\FileCookieJar;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\RequestOptions;

class VivaStreet {
    
    private $guzzleClient, $cookieJar;
    private $credential;

    // vars
    public $endpoint_Login, $htmlBodyObject, $loginStatus;

    // Key name constants
    const cookieFile = __DIR__ . '/cookies.txt';

    function __construct($credential, $printVerbose = false)
    {
        $this->credential = $credential;

        // endpoints
        $this->endpoint_Login = 'https://www.vivastreet.co.uk/login.php';

        // guzzle
        unlink(self::cookieFile);
        $this->cookieJar = new FileCookieJar(self::cookieFile, TRUE);
        $this->guzzleClient = new \GuzzleHttp\Client(
            array(
                'cookies' => $this->cookieJar
            )
        );

        // check session by sending generate csv request
        $this->loginStatus = false;
        if ($this->login()) {
            $this->loginStatus = true;
            echo "Logged in successfully!" . PHP_EOL;
        } else {
            echo "Login failed!" . PHP_EOL;
        }

    }

    function login() {
        
        $date = new DateTime("now", new DateTimeZone('Asia/Karachi'));
        echo $date->format('Y-m-d h:i:s') . PHP_EOL;
        echo "Logging in to: " . $this->credential->username . PHP_EOL;
        $post_data_array = array();
        $post_data_array['refer']       = 'https://www.vivastreet.co.uk/account_classifieds.php';
        $post_data_array['logged_in']   = 0;
        $post_data_array['email']       = $this->credential->username;
        $post_data_array['password']    = $this->credential->password;
        $loginResponse = $this->postRequest($this->endpoint_Login, $post_data_array);
        $html = str_get_html($loginResponse);
        $logoutLink = $html->find('li[id=vs_user_menu_logout_link_container]', -1);
        if ($logoutLink) {
            return true;
        }
        return false;
    }

    function repostAds() {

        echo "Fetching ads...".PHP_EOL;
        $adsResponse = $this->getRequest('https://www.vivastreet.co.uk/ .php');
        $this->htmlBodyObject = str_get_html($adsResponse);
        $ads = $this->htmlBodyObject->find('.vs-classified-info');
        $adIDs = array();
        foreach ($ads as $ad) {
            $adID = trim(explode('Ad ID:', $ad->innertext)[1]);
            $adIDs[] = $adID;
        }

        if (count($adIDs) < 1) {
            echo "No Ad found!" . PHP_EOL . PHP_EOL;
            return;
        }
        
        echo "Found " . count($adIDs) . " ads." . PHP_EOL;

        echo "Reposting...".PHP_EOL;
        foreach ($adIDs as $adID) {

            echo "Processing $adID" . PHP_EOL;
            $post_data_array = array();
            $post_data_array['id'] = $adID;
            $post_data_array['captchaRespond'] = '';
            $result = $this->postRequest('https://www.vivastreet.co.uk/ajax/manual_repost.php', $post_data_array);
            echo $result . PHP_EOL;

        }
        echo PHP_EOL;

    }

    function getRequest($url) {
        try {
            $proxy = 'http://scraperapi:'.$this->credential->apikey.'@proxy-server.scraperapi.com:8001';
            $headers = [
                RequestOptions::PROXY => $proxy, // Setting the proxy
                RequestOptions::VERIFY => false,
                RequestOptions::TIMEOUT => 70000,
                'headers' => [
                    'Content-Type' => 'application/x-www-form-urlencoded'
                ],
            ];
            $response   = $this->guzzleClient->request("get", $url, $headers);
            echo $response->getStatusCode();
            // $responseLogon  = $this->guzzleClient->get($this->endpoint_Logon);
        } catch (Exception $e) {
            return $e;
        }
        return $response->getBody()->getContents();
    }
    
    function postRequest($url, $options) {
        try {
            $proxy = 'http://scraperapi:'.$this->credential->apikey.'@proxy-server.scraperapi.com:8001';
            $headers = [
                'form_params' => $options,
                RequestOptions::PROXY => $proxy, // Setting the proxy
                RequestOptions::VERIFY => false,
                RequestOptions::TIMEOUT => 70000,
                RequestOptions::CONNECT_TIMEOUT => 70000,
                'headers' => [
                    'Content-Type' => 'application/x-www-form-urlencoded'
                ],
            ];
            $responseInit   = $this->guzzleClient->request("post", $url, $headers);
        } catch (Exception $e) {
            die ("Request exception: " . $e);
        }
        return $responseInit->getBody()->getContents();
    }
}

