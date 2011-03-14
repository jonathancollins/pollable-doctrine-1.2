<?php

class Doctrine_Pollable_Poll_Definition_TestCase extends Doctrine_UnitTestCase
{
    private $d;

    public function prepareData()
    {
        $this->d = Doctrine_Core::getTable('NullPollPollableDefinitionTest')->getTemplate('Doctrine_Template_Pollable')->getDefinition('NullPoll');
    }

    public function testMissingStorageType()
    {
        try {
            $d = new Doctrine_Pollable_Poll_Definition('');

            $this->fail();
        }
        catch (Doctrine_Pollable_Exception $e) {
            $this->pass();
        }
    }

    public function testStorageTypeDetermination()
    {
        $d = new Doctrine_Pollable_Poll_Definition('', array(
            'storage' => array(
                'type' => 'definition_test',
            ),
        ));

        $this->assertEqual(get_class($d->getStorage()), 'Doctrine_Pollable_Storage_DefinitionTest');

    }

    public function testInvalidStorageType()
    {
        try {
            $d = new Doctrine_Pollable_Poll_Definition('', array(
                'storage' => array(
                    'type' => 'invalid_storage',
                ),
            ));

            $this->fail();
        }
        catch (Doctrine_Pollable_Exception $e) {
            $this->pass();
        }
    }

    public function testName()
    {
        $this->assertEqual($this->d->getName(), 'NullPoll');
    }

    public function testStorageType()
    {
        $this->assertEqual($this->d->getStorageType(), 'null');
    }

    public function testOption()
    {
        $this->assertEqual($this->d->getOption('testOption'), 'abc');
    }

    public function testIsOpenColumnName()
    {
        $this->assertEqual($this->d->getIsOpenColumnName(), 'NullPoll_is_open');
    }

    public function testClosesAtColumnName()
    {
        $this->assertEqual($this->d->getClosesAtColumnName(), 'NullPoll_closes_at');
    }

    public function testProxy()
    {
        $r = new NullPollPollableDefinitionTest();

        $this->assertEqual($this->d->validate($r, ''), true); //proxies to storage
    }

    public function testInvalidProxy()
    {
        try {
            $this->d->sleksd();
            $this->d->fail();
        }
        catch (Doctrine_Pollable_Exception $e) {
            $this->pass();
        }
    }

}

class NullPollPollableDefinitionTest extends Doctrine_Record
{

    public function setUp()
    {
        $this->actAs('Pollable', array(
            'polls' => array(
                'NullPoll' => array(
                    'storage' => array(
                        'type' => 'null',
                    ),
                    'testOption' => 'abc',
                ),
            ),
        ));
    }

}

class TestStoragePollableDefinitionTest extends Doctrine_Record
{

    public function setUp()
    {
        $this->actAs('Pollable', array(
            'polls' => array(
                'Poll' => array(
                    'storage' => array(
                        'type' => 'test_storage',
                    ),
                ),
            ),
        ));
    }

}

class Doctrine_Pollable_Storage_DefinitionTest extends Doctrine_Pollable_Storage
{

    protected function _validate(Doctrine_Record $record, $response)
    {

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
