<?php

/**
 * Represents the definition of a poll, as given in an actAs declaration
 * 
 * @package     Doctrine
 * @subpackage  Pollable
 * @link        http://www.doctrine-project.org/
 * @since       1.0
 * @author      Jonathan R. Collins <pollable@joncollins.name>
 */
class Doctrine_Pollable_Poll_Definition
{
    protected $_name;

    protected $_options = array(
        'storage' => array(
            'options' => array(),
        ),
        'identity' => array(
            'user' => array(
                'name' => 'user',
                'type' => 'integer',
                'length' => null,
                'options' => array(
                    'notnull' => false,
                ),
            ),
            'traits' => array(),
        ),
    );

    protected $_storage;

    public function __construct($name, array $options = array())
    {
        $this->_name = $name;
        $this->_options = Doctrine_Lib::arrayDeepMerge($this->_options, $options);

        if (!isset($this->_options['storage']['type'])) {
            throw new Doctrine_Pollable_Exception("Storage type must be specified for poll \"$name\"");
        }

        $this->_options['storage']['type'] = (string) $this->_options['storage']['type'];

        $storage_class = 'Doctrine_Pollable_Storage_'.
                Doctrine_Inflector::classify($this->_options['storage']['type']);

        if (!class_exists($storage_class)) {
            throw new Doctrine_Pollable_Exception("Unknown storage type \"$storage_class\"");
        }

        $this->_storage = new $storage_class($this->_options['storage']['options']);
    }

    /**
     * Returns an option by key, or a default if the key doesn't exist
     *
     * @param string $key the key to retrieve
     * @param mixed $default the default to return if the key doesn't exist
     */
    public function getOption($key, $default = null)
    {
        if (isset($this->_options[$key])) {
            return $this->_options[$key];
        }
        else {
          return $default;
        }
    }

    public function getName()
    {
        return $this->_name;
    }

    public function getStorageType()
    {
        return $this->_options['storage']['type'];
    }

    public function getStorage()
    {
        return $this->_storage;
    }

    public function getIsOpenColumnName()
    {
        return $this->getOption('is_open_column_name', "{$this->_name}_is_open");
    }

    public function getClosesAtColumnName()
    {
        return $this->getOption('closes_at_column_name', "{$this->_name}_closes_at");
    }

    public function getUserColumnName()
    {
        return $this->_options['identity']['user']['name'];
    }

    public function getTraits()
    {
        return $this->_options['identity']['traits'];
    }

    /**
     * Sets up this poll definition during Doctrine model setup time
     */
    public function setUp()
    {
        $this->_storage->setUp($this->_options['identity']);
    }

    /**
     * Initializes this poll definition to a Doctrine_Table instance
     *
     * @param Doctrine_Table $table the Doctrine table to initialize to
     */
    public function initialize(Doctrine_Table $table)
    {
        $this->_storage->initialize($table, $this->_name);
    }

    /**
     * Proxies any other method calls directly to $_storage
     *
     * @param string $method
     * @param array $args
     */
    public function __call($method, array $args)
    {
        if (method_exists($this->_storage, $method)) {
            return call_user_func_array(array($this->_storage, $method), $args);
        }

        throw new Doctrine_Pollable_Exception("Poll \"{$this->_name}\" does not support method \"$method\"", E_USER_ERROR);
    }
}
