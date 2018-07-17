<?php

namespace TPB\GeoLocation;

class GeoLocate
{
    const PRIVATE_RANGES = [
        '10.0.0.0'    => '10.255.255.255',
        '172.16.0.0'  => '172.31.255.255',
        '192.168.0.0' => '192.168.255.255',
        '169.254.0.0' => '169.254.255.255',
        '127.0.0.0'   => '127.255.255.255',
    ];

    private $apiKey = null;

    public function __construct(string $apiKey)
    {
        $this->apiKey = $apiKey;
    }

    /**
     * @param string $ip
     *
     * @return \stdClass
     *
     * @throws \ErrorException
     * @throws \InvalidArgumentException
     */
    public function lookup(string $ip)
    {
        if (empty($this->apiKey) === true) {
            throw new \InvalidArgumentException('You must provide an API key to IPStack');
        }

        try{
            $isPrivate = $this->isPrivate($ip);
            if ($isPrivate === false) {
                $contents = file_get_contents('http://api.ipstack.com/'. $ip .'?access_key=' . $this->apiKey);
                if (empty($contents) === false) {
                    $json = json_decode($contents);

                    $response = [
                        'ip_address'   => $ip,
                        'country_code' => $json->country_code,
                        'is_eu'        => $json->location->is_eu,
                    ];

                    return $response;
                }
            }
        } catch (\Exception $exception) {
            throw new \ErrorException('Unknown error during Geolocation lookup');
        }

        throw new \ErrorException('Nothing found in lookup');
    }

    /**
     * @param string $ip
     *
     * @return bool
     */
    private function isPrivate (string $ip) :bool
    {
        $longIP = ip2long ($ip);
        if ($longIP != -1) {
            foreach (self::PRIVATE_RANGES as $start => $end) {
                if ($longIP >= ip2long($start) && $longIP <= ip2long($end)) {
                    return true;
                }
            }
        }

        return false;
    }
}