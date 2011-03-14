<?php

class Doctrine_Pollable_Storage_Null_TestCase extends Doctrine_UnitTestCase
{
    public function testAlwaysTrue()
    {
        $storage = new Doctrine_Pollable_Storage_Null();

        $r = new NullRecord();

        $this->assertTrue($storage->validate($r, ''));
        $this->assertTrue($storage->validate($r, 'string'));
        $this->assertTrue($storage->validate($r, 0));//);//, 'ResponseTypeNull::validate() is always true: 0');
        $this->assertTrue($storage->validate($r, 16));//, 'ResponseTypeNull::validate() is always true: 16');
        $this->assertTrue($storage->validate($r, 0.0));//, 'ResponseTypeNull::validate() is always true: 0.0');
        $this->assertTrue($storage->validate($r, 1.0));//, 'ResponseTypeNull::validate() is always true: 1.0');
        $this->assertTrue($storage->validate($r, true));//, 'ResponseTypeNull::validate() is always true: true');
        $this->assertTrue($storage->validate($r, false));//, 'ResponseTypeNull::validate() is always true: false');
        $this->assertTrue($storage->validate($r, null));//, 'ResponseTypeNull::validate() is always true: null');
    }
}

class NullRecord extends Doctrine_Record
{

}
