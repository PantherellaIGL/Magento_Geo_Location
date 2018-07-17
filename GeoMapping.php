<?php

namespace TPB\GeoLocation;

class GeoMapping
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


    public function lookup(
        string $countryCode,
        string $brand
    ) {
        $statement = $this->connection->prepare("
            SELECT 
                geomapping.country_code AS country_code,
                geomapping.brand        AS brand,
                geostores.store_domain  AS store_domain,
                geostores.store_code    AS store_code
            FROM geomapping 
            JOIN geostores ON geomapping.store_id = geostores.id
            WHERE geomapping.country_code = ? AND geomapping.brand = ?
        ");
        $statement->execute([$countryCode, $brand]);

        $result = $statement->fetch();

        if ($result === false) {
            return null;
        }

        return $result;
    }
}