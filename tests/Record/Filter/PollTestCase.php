<?php

class Doctrine_Record_Filter_Poll_TestCase extends Doctrine_UnitTestCase
{
    public function testFilterSet()
    {
        $d = new Doctrine_Pollable_Poll_Definition('PollName', array(
            'storage' => array(
                'type' => 'null',
            ),
        ));

        $fp = new Doctrine_Record_Filter_Poll($d);

        try {
            $fp->filterSet(new PollableFilterTest(), 'PollName', true);
            $this->fail();
        }
        catch (Doctrine_Record_UnknownPropertyException $e) {
            $this->pass();
        }
    }

    public function testFilterGet()
    {
        $d = new Doctrine_Pollable_Poll_Definition('PollName', array(
            'storage' => array(
                'type' => 'null',
            ),
        ));

        $fp = new Doctrine_Record_Filter_Poll($d);

        try {
            $poll = $fp->filterGet(new PollableFilterTest(), 'PollName');
            $this->pass();
        }
        catch (Doctrine_Record_UnknownPropertyException $e) {
            $this->fail();
        }

        try {
            $poll = $fp->filterGet(new PollableFilterTest(), 'PollNam'); //misspell
            $this->fail();
        }
        catch (Doctrine_Record_UnknownPropertyException $e) {
            $this->pass();
        }
    }

    public function testPollCache()
    {
        $d = new Doctrine_Pollable_Poll_Definition('PollName', array(
            'storage' => array(
                'type' => 'null',
            ),
        ));

        $fp = new Doctrine_Record_Filter_Poll($d);

        try {
            $r = new PollableFilterTest();

            $polla = $fp->filterGet($r, 'PollName');
            $pollb = $fp->filterGet($r, 'PollName');

            $this->assertIdentical($polla, $pollb);
        }
        catch (Doctrine_Record_UnknownPropertyException $e) {
            $this->fail();
        }

        try {
            $r1 = new PollableFilterTest();
            $polla = $fp->filterGet($r1, 'PollName');

            $r2 = new PollableFilterTest();
            $pollb = $fp->filterGet($r2, 'PollName'); //request same poll on
                                                      //different record

            $this->assertFalse($polla === $pollb);
        }
        catch (Doctrine_Record_UnknownPropertyException $e) {
            $this->fail();
        }
    }
}

class PollableFilterTest extends Doctrine_Record
{

}
