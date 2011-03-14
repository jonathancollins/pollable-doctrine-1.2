<?php

/**
 * Pollable storage that collects "write-in" text responses
 *
 * Responses must pass the given Doctrine validators
 * 
 * @package     Doctrine
 * @subpackage  Pollable
 * @link        http://www.doctrine-project.org/
 * @since       1.0
 * @author      Jonathan R. Collins <pollable@joncollins.name>
 */
class Doctrine_Pollable_Storage_WriteIn extends Doctrine_Pollable_Storage_Basic
{
    protected $_validators = array();

    /**
     * Sets default options for write-in storage, defines the default response
     * column, and sets up validators
     *
     * @param array $options
     */
    public function __construct(array $options = array())
    {
        parent::__construct(Doctrine_Lib::arrayDeepMerge(
            array(
                'length' => 255,
                'validators' => array(),
            ),
            $options
        ));

        if (is_array($this->_options['validators'])) {
            foreach ($this->_options['validators'] as $validatorName => $args) {
                // borrowed from Doctrine_Table
                // can't really get at that code without being subject to
                // Doctrine_Core::ATTR_VALIDATE

                if ( ! is_string($validatorName)) {
                    $validatorName = $args;
                    $args = array();
                }

                if ($validatorName == 'readonly') {
                    continue;
                }

                $validator = Doctrine_Validator::getValidator($validatorName);
                $validator->invoker = null;
                $validator->field = $this->_column['name'];
                $validator->args = $args;

                $this->_validators[] = $validator;
            }
        }

        //define the column
        $this->_column['length'] = $this->_options['length'];
    }

    /**
     * Validates a write-in response
     *
     * @param Doctrine_Record $record
     * @param mixed $response
     * @return boolean
     */
    protected function _validate(Doctrine_Record $record, $response)
    {
        if (!is_string($response)) {
            return false;
        }

        if (strlen($response) > $this->_options['length'])
        {
            return false;
        }

        foreach ($this->_validators as $validator) {
            if (false == $validator->validate($response)) {
                return false;
            }
        }

        return true;
    }
}
