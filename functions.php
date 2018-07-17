<?php

function locate()
{
    try{
        $run = true;

        $geoStore = new \TPB\GeoLocation\GeoStore(
            getenv('GEO_HOST'),
            getenv('GEO_DB'),
            getenv('GEO_USER'),
            getenv('GEO_PASS')
        );

        $domain = $geoStore->lookup($_SERVER['HTTP_HOST']);

        if (empty($domain) === true) {
            $run = false;
        } else {
            if ($domain['is_enabled'] === false) {
                $run = false;
            }
        }

        if ($run === true) {
            $doGeolocation = true;
            $foundGetVar = false;
            $foundCookie = false;

            if (isset($_GET['__store']) === true) {
                $doGeolocation === false;
                $foundGetVar = true;
            }

            if (isset($_COOKIE['disable_geolocation']) === true) {
                $doGeolocation = false;
                $foundCookie = true;
            }

            if ($doGeolocation === true) {
                $ipAddress = $_SERVER['REMOTE_ADDR'];
                $spoofedIP = getenv('GEO_SPOOF_IP');
                if (empty($spoofedIP) === false) {
                    $ipAddress = $spoofedIP;
                }
                $apiKey = getenv('GEO_API_KEY');

                $geoCache = new \TPB\GeoLocation\GeoCache(
                    getenv('GEO_HOST'),
                    getenv('GEO_DB'),
                    getenv('GEO_USER'),
                    getenv('GEO_PASS')
                );
                $record = $geoCache->lookup($ipAddress);

                if ($geoCache->isCacheable($record) === true) {
                    $geoLocation = new \TPB\GeoLocation\GeoLocate($apiKey);
                    $record = $geoLocation->lookup($ipAddress);
                    $geoCache->cache(
                        $ipAddress,
                        $record['is_eu'],
                        $record['country_code']
                    );
                }

                $mapping = new \TPB\GeoLocation\GeoMapping(
                    getenv('GEO_HOST'),
                    getenv('GEO_DB'),
                    getenv('GEO_USER'),
                    getenv('GEO_PASS')
                );

                $storeInfo = $mapping->lookup($record['country_code'], getenv('GEO_BRAND'));

                if ($_SERVER['HTTP_HOST'] !== $storeInfo['store_domain']) {
                    $protocol = 'https://www.';
                    $host = $storeInfo['store_domain'];
                    $path = preg_replace('/\?.*/', '', $_SERVER['REQUEST_URI']);
                    $queryString = $_SERVER['QUERY_STRING'];

                    if (empty($queryString) === true) {
                        $queryString = '__store=' . $storeInfo['store_code'];
                    } else {
                        parse_str($queryString, $queryArray);
                        $queryArray['__store'] = $storeInfo['store_code'];
                        $queryString = http_build_query($queryArray);
                    }

                    $redirectURI = $protocol . $host . $path . '?' . $queryString;

                    header('Location: ' . $redirectURI);
                    die();
                }

                setcookie("disable_geolocation", 'true', time() + 3600);
            }

            if ($doGeolocation === false) {
                setcookie("disable_geolocation", 'true', time() + 3600);
            }
        }
    } catch(ErrorException | InvalidArgumentException | PDOException $exception) {

    }
}