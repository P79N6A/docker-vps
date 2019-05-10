<?php
//
//  $Id: TreeHelper.php 320806 2011-12-09 19:37:22Z danielc $
//

if (!defined('TABLE_TREENESTED')) {
    if ($fp = @fopen('PHPUnit/Autoload.php', 'r', true)) {
        require_once 'PHPUnit/Autoload.php';
    } elseif ($fp = @fopen('PHPUnit/Framework.php', 'r', true)) {
        require_once 'PHPUnit/Framework.php';
    } else {
        die("skip could not find PHPUnit");
    }
    fclose($fp);

    if ('/opt/lampp/lib/php' == '@'.'include_path'.'@') {
        /*
        * This package hasn't been installed.
        * Adjust path so includes find files in working directory.
        */
        ini_set('include_path',
            dirname(dirname(__FILE__))
            . PATH_SEPARATOR . ini_get('include_path'));
    }

    if (empty($_ENV['MYSQL_TEST_USER'])) {
        $dsn = 'mysqli://root:hamstur@localhost/test';
    } else {
        $dsn = array(
            'phptype' => 'mysqli',
            'username' => $_ENV['MYSQL_TEST_USER'],
            'password' => $_ENV['MYSQL_TEST_PASSWD'],
            'database' => $_ENV['MYSQL_TEST_DB'],

            'hostspec' => empty($_ENV['MYSQL_TEST_HOST'])
                    ? null : $_ENV['MYSQL_TEST_HOST'],

            'port' => empty($_ENV['MYSQL_TEST_PORT'])
                    ? null : $_ENV['MYSQL_TEST_PORT'],

            'socket' => empty($_ENV['MYSQL_TEST_SOCKET'])
                    ? null : $_ENV['MYSQL_TEST_SOCKET'],
        );
    }

    define('DB_DSN', serialize($dsn));
    define('TABLE_TREENESTED', 'TreeNested');

    require_once 'DB.php';
    require_once 'Tree/Tree.php';
}


class TreeHelper extends PHPUnit_Framework_TestCase
{
    public $dsn;
    public $has_mdb = false;
    public $has_mdb2 = false;

    function setUp()
    {
        // common setup, setup the table structure and data in the db
        // (this actually also does the tearDown, since we have the DROP TABLE queries in the setup too
        require 'sql.php'; 
        $this->dsn = unserialize(DB_DSN);
        $db = DB::connect($this->dsn);
        if (PEAR::isError($db)) {
            $this->markTestSkipped($db->getMessage());
        }

        if (!array_key_exists($db->phptype, $dbStructure)) {
            $this->markTestSkipped($db->phptype . ' not set in sql.php');
        }
        if (!array_key_exists('setup', $dbStructure[$db->phptype])) {
            $this->markTestSkipped($db->phptype . ' lacks setup in sql.php');
        }

        foreach ($dbStructure[$db->phptype]['setup'] as $aQuery) {
            if (DB::isError($ret = $db->query($aQuery))) {
                $this->markTestSkipped($db->getUserInfo());
            }
        }

        if ($fp = @fopen('MDB.php', 'r', true)) {
            fclose($fp);
            $this->has_mdb = true;
        }
        if ($fp = @fopen('MDB2.php', 'r', true)) {
            fclose($fp);
            $this->has_mdb2 = true;
        }
    }

    function tearDown()
    {
/*        global $dbStructure;

        $querytool = new Common();
        foreach ($dbStructure[$querytool->db->phptype]['tearDown'] as $aQuery) {
//print "$aQuery<br><br>";        
            if (DB::isError($ret=$querytool->db->query($aQuery))) {
                die($ret->getUserInfo());
            }
        }
*/        
    }
    
    function &getMemoryDBnested()
    {
        $tree = Tree::setup('Memory_DBnested', $this->dsn, array('table' => TABLE_TREENESTED));
        $tree->setup();
        return $tree;
    }
    
    function &getDynamicDBnested()
    {
        $tree = Tree::setup('Dynamic_DBnested', $this->dsn, array('table' => TABLE_TREENESTED));
        return $tree;
    }
 
    function &getMemoryMDBnested()
    {
        $tree = Tree::setup('Memory_MDBnested', $this->dsn, array('table' => TABLE_TREENESTED));
        $tree->setup();
        return $tree;
    }
    
    function &getDynamicMDBnested()
    {
        $tree = Tree::setup('Dynamic_MDBnested', $this->dsn, array('table' => TABLE_TREENESTED));
        return $tree;
    } 
    
}

?>
