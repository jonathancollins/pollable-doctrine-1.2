<?php

/**
 * Pollable storage that collects floating point ratings
 *
 * @package     Doctrine
 * @subpackage  Pollable
 * @link        http://www.doctrine-project.org/
 * @since       1.0
 * @author      Jonathan R. Collins <pollable@joncollins.name>
 */
class Doctrine_Pollable_Storage_RatingFloat extends Doctrine_Pollable_Storage_Rating
{
    public function __construct($options = array())
    {
        parent::__construct(Doctrine_Lib::arrayDeepMerge(
            array(
                'min'       => 0.0,
                'max'       => 10.0,
                'inclusive' => true,
            ),
            $options
        ));

        if (!is_float($this->_options['min'])) {
            throw new Doctrine_Pollable_Exception("Invalid value for min: {$this->_options['min']}");
        }

        if (!is_float($this->_options['max'])) {
            throw new Doctrine_Pollable_Exception("Invalid value for max: {$this->_options['max']}");
        }

        if ($this->_options['max'] < $this->_options['min']) {
            throw new Doctrine_Pollable_Exception("Value for max {$this->_options['max']} is less than value for min {$this->_options['min']}");
        }

        //define the column
        $this->_column['type'] = 'float';
        $this->_column['length'] = null;
    }
}
