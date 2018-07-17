<?php

namespace TPB\GeoLocation;

class GeoCache
{
    /** @var string */
    private $host;
    /** @var string */
    private $db;
    /** @var string */
    private $username;
    /** @var string */
    private $password;

    /** @var \PDO */
    private $connection = null;

    /**
     * GeoCache constructor.
     *
     * @param string $host
     * @param string $db
     * @param string $username
     * @param string $password
     */
    public function __construct(
        string $host,
        string $db,
        string $username,
        string $password
    ) {
        $this->host     = $host;
        $this->db       = $db;
        $this->username = $username;
        $this->password = $password;

        $this->connect();
    }

    /**
     * @return void
     */
    private function connect()
    {
        $this->connection = new \PDO('mysql:host='.$this->host.';dbname='.$this->db, $this->username, $this->password);
    }

    /**
     * @param string $ip
     *
     * @return mixed
     */
    public function lookup(string $ip)
    {

        $statement = $this->connection->prepare("
            SELECT * FROM geolocation WHERE ip_address = ?
        ");
        $statement->execute([$ip]);

        $result = $statement->fetch();

        if ($result === false) {
            return null;
        }

        $result['is_eu'] = (bool) $result['is_eu'];

        return $result;
    }

    /**
     * @param string $ip
     * @param bool   $isEU
     * @param string $countryCode
     */
    public function cache(
        string $ip,
        bool   $isEU,
        string $countryCode
    ) {
        $sanityCheck = $this->lookup($ip);
        if (empty($sanityCheck) === false) {
            $this->remove($ip);
        }

        $statement = $this->connection->prepare("
            INSERT INTO geolocation (
              ip_address, 
              country_code,
              is_eu,
              created_at
            ) VALUES (?, ?, ?, ?)
        ");

        $date = new \DateTime();
        $statement->execute([
            $ip,
            $countryCode,
            (int)$isEU,
            $date->format('Y-m-d H:i:s')
        ]);
    }

    /**
     * @param string $ip
     */
    private function remove(string $ip)
    {
        $statement = $this->connection->prepare("
            DELETE FROM geolocation WHERE ip_address = ?
        ");
        $statement->execute([$ip]);
    }

    /**
     * @param array|null $record
     * @return bool
     */
    public function isCacheable(array $record = null) :bool
    {
        if (empty($record) === true) {
            return true;
        } else {
            $dateCheck = new \DateTime();
            $dateCheck->modify('-1 month');
            $storedDate = new \DateTime($record['created_at']);

            if ($storedDate < $dateCheck || $record['created_at'] === null) {
                return true;
            }
        }

        return false;
    }
}