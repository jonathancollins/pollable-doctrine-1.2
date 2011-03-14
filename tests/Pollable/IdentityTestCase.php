<?php

class Doctrine_Pollable_Identity_TestCase extends Doctrine_UnitTestCase
{
    public function testAnonymous()
    {
        $i = new Doctrine_Pollable_Identity(null, array('ip' => '198.168.1.100', 'browser' => 'Firefox'));

        $this->assertTrue($i->isAnonymous());
        $this->assertIdentical($i->getUser(), null);
    }

    public function testRegistered()
    {
        $i = new Doctrine_Pollable_Identity(1, array('ip' => '198.168.1.100', 'browser' => 'Firefox'));

        $this->assertFalse($i->isAnonymous());
        $this->assertIdentical($i->getUser(), 1);
    }

    public function testTraits()
    {
        $i = new Doctrine_Pollable_Identity(null, array('ip' => '198.168.1.100', 'browser' => 'Firefox'));

        $this->assertEqual($i->getTrait('ip'), '198.168.1.100');
        $this->assertEqual($i->getTrait('browser'), 'Firefox');
    }

    public function testPropertyAccess()
    {
        $i = new Doctrine_Pollable_Identity(1, array('ip' => '198.168.1.100', 'browser' => 'Firefox'));

        $this->assertEqual($i->user, 1);
        $this->assertEqual($i->ip, '198.168.1.100');
        $this->assertEqual($i->browser, 'Firefox');
    }
}
