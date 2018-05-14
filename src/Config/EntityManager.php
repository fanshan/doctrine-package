<?php

namespace ObjectivePHP\Package\Doctrine\Config;

use ObjectivePHP\Config\Directive\AbstractMultiComplexDirective;

/**
 * Class EntityManager
 * @package ObjectivePHP\Package\Doctrine\Config
 */
class EntityManager extends AbstractMultiComplexDirective
{
    const KEY = 'doctrine';

    protected $key = self::KEY;

    /**
     * @var string $driver
     */
    protected $driver;

    /**
     * @var string $host
     */
    protected $host;

    /**
     * @var string $user
     */
    protected $user;

    /**
     * @var string $password
     */
    protected $password;

    /**
     * @var string $dbname
     */
    protected $dbname;

    /**
     * @var int $port
     */
    protected $port;

    /**
     * @var string $entities
     */
    protected $entities;

    /**
     * @return string
     */
    public function getDriver(): string
    {
        return $this->driver;
    }

    /**
     * @param string $driver
     * @return EntityManager
     */
    public function setDriver(string $driver): EntityManager
    {
        $this->driver = $driver;
        return $this;
    }

    /**
     * @return string
     */
    public function getHost(): string
    {
        return $this->host;
    }

    /**
     * @param string $host
     * @return EntityManager
     */
    public function setHost(string $host): EntityManager
    {
        $this->host = $host;
        return $this;
    }

    /**
     * @return string
     */
    public function getUser(): string
    {
        return $this->user;
    }

    /**
     * @param string $user
     * @return EntityManager
     */
    public function setUser(string $user): EntityManager
    {
        $this->user = $user;
        return $this;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @param string $password
     * @return EntityManager
     */
    public function setPassword(string $password): EntityManager
    {
        $this->password = $password;
        return $this;
    }

    /**
     * @return string
     */
    public function getDbname(): string
    {
        return $this->dbname;
    }

    /**
     * @param string $dbname
     * @return EntityManager
     */
    public function setDbname(string $dbname): EntityManager
    {
        $this->dbname = $dbname;
        return $this;
    }

    /**
     * @return int
     */
    public function getPort(): int
    {
        return $this->port;
    }

    /**
     * @param int $port
     * @return EntityManager
     */
    public function setPort(int $port): EntityManager
    {
        $this->port = $port;
        return $this;
    }

    /**
     * @return string
     */
    public function getEntities(): string
    {
        return $this->entities;
    }

    /**
     * @param string $entities
     * @return EntityManager
     */
    public function setEntities(string $entities): EntityManager
    {
        $this->entities = $entities;
        return $this;
    }
}
