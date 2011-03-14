<?php

/**
 * Pollable storage that collects yes or no votes
 * 
 * @package     Doctrine
 * @subpackage  Pollable
 * @link        http://www.doctrine-project.org/
 * @since       1.0
 * @author      Jonathan R. Collins <pollable@joncollins.name>
 */
class Doctrine_Pollable_Storage_YesNo extends Doctrine_Pollable_Storage_Discrete
{
    const YES = 'yes';

    const NO = 'no';

    /**
     * Sets discrete choices to self::YES and self::NO
     *
     * @param array $options
     */
    public function __construct(array $options = array())
    {
        $options['choices'] = array(self::YES, self::NO);
        parent::__construct($options);
    }

    public function yes(Doctrine_Record $record)
    {
        $this->store($record, self::YES);
    }

    public function no(Doctrine_Record $record) {
        $this->store($record, self::NO);
    }

    public function approved(Doctrine_Record $record)
    {
        return $this->count($record, self::YES) > $this->count($record, self::NO);
    }
}
