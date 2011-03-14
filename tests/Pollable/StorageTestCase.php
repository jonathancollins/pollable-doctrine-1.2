<?php

class Doctrine_Pollable_Storage_TestCase extends Doctrine_UnitTestCase
{
    private $s;

    public function prepareData()
    {
        //$this->s = new Doctrine_Pollable_Storage_StorageTest();
    }

}

class PollableStorageTest extends Doctrine_Record
{

}

class Doctrine_Pollable_Storage_StorageTest extends Doctrine_Pollable_Storage
{
    protected function _validate(Doctrine_Record $record, $response)
    {
        return false;
    }

    protected function _store(Doctrine_Record $record, $response)
    {

    }

    public function responses(Doctrine_Record $record)
    {

    }

    public function count(Doctrine_Record $record, $response)
    {

    }

    public function percentage(Doctrine_Record $record, $response)
    {

    }

    public function total(Doctrine_Record $record)
    {

    }

    public function setUp($identity)
    {

    }
}
