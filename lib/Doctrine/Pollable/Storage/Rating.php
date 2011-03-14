<?php

/**
 * Pollable storage that collects integer ratings
 * 
 * @package     Doctrine
 * @subpackage  Pollable
 * @link        http://www.doctrine-project.org/
 * @since       1.0
 * @author      Jonathan R. Collins <pollable@joncollins.name>
 */
class Doctrine_Pollable_Storage_Rating extends Doctrine_Pollable_Storage_Basic
{
    public function __construct($options = array())
    {
        parent::__construct(Doctrine_Lib::arrayDeepMerge(
            array(
		        'min'     => 1,
		        'max'     => 5,
                'inclusive' => true,
            ),
            $options
        ));

        if (!is_integer($this->_options['min'])) {
            throw new Doctrine_Pollable_Exception("Invalid value for min: {$this->_options['min']}");
        }

        if (!is_integer($this->_options['max'])) {
            throw new Doctrine_Pollable_Exception("Invalid value for max: {$this->_options['max']}");
        }

        if ($this->_options['max'] < $this->_options['min']) {
            throw new Doctrine_Pollable_Exception("Value for max {$this->_options['max']} is less than value for min {$this->_options['min']}");
        }

        //define the column
        $this->_column['type'] = 'integer';
        $this->_column['length'] = strlen($this->_options['max']);
    }

    protected function _validate(Doctrine_Record $record, $response)
    {
        if ($this->_options['inclusive'] == true) {
            if ($response < $this->_options['min']) {
                return false;
            }
            if ($response > $this->_options['max']) {
                return false;
            }
        } else {
            if ($response <= $this->_options['min']) {
                return false;
            }
            if ($response >= $this->_options['max']) {
                return false;
            }
        }

        return true;
    }

    public function average(Doctrine_Record $record)
    {

    }

}
