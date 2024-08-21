<?php

require __DIR__ . '/helper.php';
require __DIR__ . '/simple_html_dom.php';

$accounts = file(__DIR__ . '/accounts.txt');

// Infinite loop to run the task every 20 minutes
while (true) {
    //Get account information randomly.
    $account = $accounts[array_rand($accounts)];
    $account = trim($account);
    $accountExploded = explode(':', $account);

    //Set user information.
    $credential = new stdClass;
    $credential->username = $accountExploded[0];
    $credential->password = $accountExploded[1];
    $credential->apikey = $accountExploded[2];

    if ($credential->apikey && $credential->password && $credential->username) {
        $vivaStreet = new VivaStreet($credential);
    } else {
        echo "Please enter your information in accounts.txt";
        break;
    }

    if ($vivaStreet->loginStatus) {
        $vivaStreet->repostAds();
        echo "Ads reposted successfully for {$credential->username}." . PHP_EOL;
    } else {
        echo 'Sorry. Failed to repost.' . PHP_EOL;
    }

    // Wait for 20 minutes before the next execution
    sleep(1200); // 1200 seconds = 20 minutes
}