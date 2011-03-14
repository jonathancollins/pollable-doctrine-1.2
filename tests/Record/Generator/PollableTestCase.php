<?php

class Doctrine_Record_Generator_Pollable_TestCase extends Doctrine_UnitTestCase
{
    public function testOverridenName()
    {
        $rg = new Doctrine_Record_Generator_Pollable_OverriddenName();

        $this->assertEqual($rg->getName(), 'TestName');
    }

    public function testSetName()
    {
        $rg = new Doctrine_Record_Generator_Pollable_SetName();
        $rg->setName('Other');

        $this->assertEqual($rg->getName(), 'Other');
    }

    public function testClassBasedName()
    {
        $rg = new Doctrine_Record_Generator_Pollable_ClassBased();

        $this->assertEqual($rg->getName(), 'ClassBased');
    }

    public function testPollName()
    {
        $rg = new Doctrine_Record_Generator_Pollable_Response();
        $rg->setPollName('TestPoll');

        $this->assertEqual($rg->getPollName(), 'TestPoll');
    }

    public function testRelationName()
    {
        $rg = new Doctrine_Record_Generator_Pollable_Response();
        $rg->setPollName('TestPoll');

        $this->assertEqual($rg->getRelationName(), 'TestPollResponse');
    }
}

class Doctrine_Record_Generator_Pollable_OverriddenName extends Doctrine_Record_Generator_Pollable
{
    protected $_name = 'TestName';
}

class Doctrine_Record_Generator_Pollable_SetName extends Doctrine_Record_Generator_Pollable
{

}

class Doctrine_Record_Generator_Pollable_ClassBased extends Doctrine_Record_Generator_Pollable
{

}
