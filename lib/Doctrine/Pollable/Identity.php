<?php

/**
 * The identifying traits of the entity responding to a poll
 *
 * @package     Doctrine
 * @subpackage  Pollable
 * @link        http://www.doctrine-project.org/
 * @since       1.0
 * @author      Jonathan R. Collins <pollable@joncollins.name>
 */
class Doctrine_Pollable_Identity
{
    /**
     * User identifier
     *
     * @var mixed User identifier, or null if anonymous
     */
    private $_user;

    /**
     * Throttlable traits traits
     *
     * @var array Throttlable traits
     */
    private $_traits;

    /**
     * Defaults to an anonymous identity with no traits
     *
     * @param mixed $user user identifier, or null if anonymous
     * @param array $traits associative array of throttlable traits
     */
    public function __construct($user = null, $traits = array())
    {
        $this->_user = $user;
        $this->_traits = $traits;
        $this->_traits['user'] = $user;
    }

    /**
     * Check if the identity is anonymous
     *
     * @return boolean true if anonymous, false if identified
     */
    public function isAnonymous()
    {
        if ($this->_user == null) {
            return true;
        }

        return false;
    }

    /**
     * Returns the user identifier, or null if anonymous
     *
     * @return mixed
     */
    public function getUser()
    {
        return $this->_user;
    }

    /**
     * Returns the given trait
     *
     * @param string $trait
     * @return string
     */
    public function getTrait($trait)
    {
        return $this->_traits[$trait];
    }

    /**
     * Proxies to getTrait()
     *
     * @param string $trait
     * @return string
     */
    public function __get($trait)
    {
        return $this->getTrait($trait);
    }
}
