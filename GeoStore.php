<?php

namespace TPB\GeoLocation;

class GeoStore
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
     * @param string $domain
     *
     * @return mixed|null
     */
    public function lookup(
        string $domain
    ) {
        $statement = $this->connection->prepare("
            SELECT 
                store_domain  AS store_domain,
                store_code    AS store_code,
                is_enabled    AS is_enabled
            FROM geostores
            WHERE store_domain = ?
        ");
        $statement->execute([$domain]);

        $result = $statement->fetch();

        if ($result === false) {
            return null;
        }

        $result['is_enabled'] = (bool)$result['is_enabled'];

        return $result;
    }
}