<?php
//
//  $Id: getIdByPathTest.php 320812 2011-12-09 23:49:34Z danielc $
//

require_once dirname(__FILE__) . '/TreeHelper.php';

class tests_getIdByPathTest extends TreeHelper
{
    // check if we get the right ID, for the given path
    function test_MemoryDBnested()
    {
        $tree = $this->getMemoryDBnested();        
        $id = $tree->getIdByPath('/Root/child 2/child 2_2');
        
        $this->assertEquals(5, $id);
    }

    function test_MemoryMDBnested()
    {
        if (!$this->has_mdb) {
            $this->markTestSkipped('MDB is not installed');
        }

        $tree = $this->getMemoryMDBnested();        
        $id = $tree->getIdByPath('/Root/child 2/child 2_2');
        
        $this->assertEquals(5, $id);
    }

    // do this for XML
            
    // do this for Filesystem

    // do this for DBsimple
    
    // do this for DynamicDBnested
    function test_DynamicDBnested()
    {
        $tree = $this->getDynamicDBnested();
        $id = $tree->getIdByPath('/Root/child 2/child 2_2');

        $this->assertEquals(5, $id,'This is not implemented, yet!!! (This test should fail ... for now)');
    }
    
    function test_DynamicMDBnested()
    {
        if (!$this->has_mdb) {
            $this->markTestSkipped('MDB is not installed');
        }

        $tree = $this->getDynamicMDBnested();
        $id = $tree->getIdByPath('/Root/child 2/child 2_2');

        $this->assertEquals(5, $id,'This is not implemented, yet!!! (This test should fail ... for now)');
    }

}

?>
