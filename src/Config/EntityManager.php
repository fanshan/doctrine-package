<?php
/**
 * This file is part of the Objective PHP project
 *
 * More info about Objective PHP on www.objective-php.org
 *
 * @license http://opensource.org/licenses/GPL-3.0 GNU GPL License 3.0
 */

namespace ObjectivePHP\Package\Doctrine\Config;

use ObjectivePHP\Config\SingleValueDirectiveGroup;

class EntityManager extends SingleValueDirectiveGroup
{
    public function __construct($identifier, array $value = [])
    {
        parent::__construct($identifier, $value);
    }

    /**
     * Set the driver of the doctrine connection
     *
     * @param string $driver
     *
     * @return EntityManager
     */
    public function setDriver(string $driver) : EntityManager
    {
        $this->value['driver'] = $driver;

        return $this;
    }

    /**
     * Set the host of the database
     *
     * @param string $host
     *
     * @return EntityManager
     */
    public function setHost(string $host) : EntityManager
    {
        $this->value['host'] = $host;

        return $this;
    }

    /**
     * Set the user of the database
     *
     * @param string $user
     *
     * @return EntityManager
     */
    public function setUser(string $user) : EntityManager
    {
        $this->value['user'] = $user;

        return $this;
    }

    /**
     * Set the password of the database
     *
     * @param string $password
     *
     * @return EntityManager
     */
    public function setPassword(string $password) : EntityManager
    {
        $this->value['password'] = $password;

        return $this;
    }

    /**
     * Set the name of the database
     *
     * @param string $dbname
     *
     * @return EntityManager
     */
    public function setDbname(string $dbname) : EntityManager
    {
        $this->value['dbname'] = $dbname;

        return $this;
    }

    /**
     * Set the port of the database
     *
     * @param int $port
     *
     * @return EntityManager
     */
    public function setPort(int $port) : EntityManager
    {
        $this->value['port'] = $port;

        return $this;
    }

    /**
     * Set the mapping types used by doctrine
     *
     * @param array $mapping_types
     *
     * @return EntityManager
     */
    public function setMappingTypes(array $mapping_types) : EntityManager
    {
        $this->value['mapping_types'] = $mapping_types;

        return $this;
    }

    /**
     * Add one mapping type that'll used by doctrine
     *
     * @param string $name the name of the mapping type
     * @param string $type the type of the mapping type
     *
     * @return EntityManager
     */
    public function addMappingType(string $name, string $type) : EntityManager
    {
        $this->value['mapping_types'][$name] = $type;

        return $this;
    }

    /**
     * Set the entities locations used by doctrine
     *
     * @param array $entities_locations
     *
     * @return EntityManager
     */
    public function setEntitiesLocations(array $entities_locations) : EntityManager
    {
        $this->value['entities.locations'] = $entities_locations;

        return $this;
    }

    /**
     * Add one entities location that'll used by doctrine
     *
     * @param string $location the path of the entities
     *
     * @return EntityManager
     */
    public function addEntitiesLocation(string $location) : EntityManager
    {
        $this->value['entities.locations'] = $this->value['entities.locations'] ?? [];

        if (!in_array($location, $this->value['entities.locations'])) {
            $this->value['entities.locations'][] = $location;
        }

        return $this;
    }
}
