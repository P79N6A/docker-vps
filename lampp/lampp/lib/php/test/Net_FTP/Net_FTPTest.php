<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Net_FTP main test file
 *
 * To run the tests either execute this from the top directory if checked out from
 * CVS:
 * $ pear run-tests -ur 
 * or if you want to run the tests on an installed version, run from within any
 * directory:
 * $ pear run-tests -pu Net_FTP
 *
 * In both cases you need PHPUnit installed
 *
 * PHP version 5
 *
 * LICENSE: This source file is subject to version 3.0 of the PHP license
 * that is available through the world-wide-web at the following URI:
 * http://www.php.net/license/3_0.txt.  If you did not receive a copy of
 * the PHP License and are unable to obtain it through the web, please
 * send a note to license@php.net so we can mail you a copy immediately.
 *
 * @category  Networking
 * @package   FTP
 * @author    Tobias Schlitt <toby@php.net>
 * @copyright 1997-2008 The PHP Group
 * @license   http://www.php.net/license/3_0.txt  PHP License 3.0
 * @version   CVS: $Id: Net_FTPTest.php,v 1.7.2.5 2008/05/19 18:08:23 jschippers Exp $
 * @link      http://pear.php.net/package/Net_FTP
 * @link      http://www.phpunit.de PHPUnit
 * @since     File available since Release 1.3.3
 */

if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Net_FTPTest::main');
}

require_once 'PHPUnit/Framework.php';
require_once 'System.php';

chdir(dirname(__FILE__));
if (substr(dirname(__FILE__), -6) == DIRECTORY_SEPARATOR.'tests') {
    include_once '../Net/FTP.php';
} else {
    include_once 'Net/FTP.php';
}

/**
 * Unit test case for Net_FTP
 *
 * @category  Networking
 * @package   FTP
 * @author    Jorrit Schippers <jschippers@php.net>
 * @copyright 1997-2008 The PHP Group
 * @license   http://www.php.net/license/3_0.txt  PHP License 3.0
 * @version   Release: 1.3.7
 * @link      http://pear.php.net/package/Net_FTP
 * @since     Class available since Release 1.3.3
 */
class Net_FTPTest extends PHPUnit_Framework_TestCase
{
    protected $ftp;
    protected $ftpdir;
    protected $setupError;
    
    /**
     * Runs the test methods of this class.
     *
     * @access public
     * @static
     * @return void
     */
    public static function main()
    {
        include_once 'PHPUnit/TextUI/TestRunner.php';

        $suite  = new PHPUnit_Framework_TestSuite('Net_FTPTest');
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     *
     * @access protected
     * @return void
     */
    protected function setUp()
    {
        if (!file_exists('config.php')) {
            $this->setupError = 'config.php does not exist in '.getcwd();
            return;
        }
        
        include_once 'config.php';
        
        if (!defined('FTPHOST') || !defined('FTPPORT') || !defined('FTPUSER')
            || !defined('FTPPASSWORD')) {
            $this->setupError = 'Some required constants are not defined';
            return;
        }
        
        $this->ftp = new Net_FTP(FTPHOST, FTPPORT, 30);
        $res       = $this->ftp->connect();
        
        if (PEAR::isError($res)) {
            $this->setupError = 'Could not connect to the FTP server';
            $this->ftp        = null;
            return;
        }
        
        $res = $this->ftp->login(FTPUSER, FTPPASSWORD);
        
        if (PEAR::isError($res)) {
            $this->setupError = 'Could not login to the FTP server';
            $this->ftp        = null;
            return;
        }
        
        if (defined('FTPDIR') && '' !== FTPDIR) {
            $res = $this->ftp->cd(FTPDIR);
            
            if (PEAR::isError($res)) {
                $this->setupError = 'Could switch to directory '.FTPDIR;
                $this->ftp        = null;
                return;
            }
        }
        
        $res = $this->ftp->pwd();
        
        if (PEAR::isError($res)) {
            $this->setupError = 'Could not get current directory';
            $this->ftp        = null;
            return;
        }
        
        $this->ftpdir = $res;
        
        $res = $this->ftp->mkdir('test');
        
        if (PEAR::isError($res)) {
            $this->setupError = 'Could not create a test directory';
            $this->ftp        = null;
            return;
        }
        
        $res = $this->ftp->cd('test');
        
        if (PEAR::isError($res)) {
            $this->setupError = 'Could not change to the test directory';
            $this->ftp        = null;
            return;
        }
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     *
     * @access protected
     * @return void
     */
    protected function tearDown()
    {
        if ($this->ftp != null) {
            $this->ftp->cd($this->ftpdir);
            $this->ftp->rm('test/', true);
            $this->ftp->disconnect();
            
            $this->ftpdir     = null;
            $this->ftp        = null;
            $this->setupError = null;
        }
    }
    
    /**
     * Tests functionality of Net_FTP::mkdir()
     * 
     * @return void
     * @see Net_FTP::mkdir()
     */
    public function testMkdir()
    {
        if ($this->ftp == null) {
            $this->fail('This test requires a working FTP connection. Setup '.
            'config.php with proper configuration parameters. ('.
            $this->setupError.')');
        }
        $this->ftp->mkdir('dir1', false);
        $this->ftp->mkdir('dir1/dir2/dir3/dir4', true);
        $this->assertTrue($this->ftp->cd('dir1/dir2/dir3/dir4'));
    }
    
    /**
     * Tests functionality of Net_FTP::mkdir()
     * 
     * @return void
     * @see Net_FTP::mkdir()
     */
    public function testRename()
    {
        if ($this->ftp == null) {
            $this->fail('This test requires a working FTP connection. Setup '.
            'config.php with proper configuration parameters. ('.
            $this->setupError.')');
        }
        $this->ftp->put('testfile.dat', 'testfile.dat', FTP_ASCII);
        $this->assertTrue($this->ftp->rename('testfile.dat', 'testfile2.dat'));
    }

    /**
     * Tests functionality of Net_FTP::rm()
     * 
     * @return void
     * @see Net_FTP::rm()
     */
    public function testRm()
    {
        if ($this->ftp == null) {
            $this->fail('This test requires a working FTP connection. Setup '.
            'config.php with proper configuration parameters. ('.
            $this->setupError.')');
        }
        $list1 = $this->ftp->ls();
        
        $this->ftp->put('testfile.dat', 'testfile.dat', FTP_ASCII);
        $this->ftp->mkdir('dir1/dir2/dir3/dir4', true);
        
        $this->ftp->rm('dir1/', true);
        $this->ftp->rm('testfile.dat');
        
        $list2 = $this->ftp->ls();
        
        $this->assertEquals($list1, $list2, 'Directory listing before creation and'.
            ' after creation are not equal');
    }

    /**
     * Tests functionality of Net_FTP::putRecursive()
     *
     * @return void
     * @see Net_FTP::putRecursive()
     */
    public function testPutRecursive()
    {
        if ($this->ftp == null) {
            $this->fail('This test requires a working FTP connection. Setup '.
            'config.php with proper configuration parameters. ('.
            $this->setupError.')');
        }
        
        $tmpdir    = array();
        $tmpfile   = array();
        $tmpdir[]  = System::mktemp(array('-d', 'pearnetftptest'));
        $tmpdir[]  = System::mktemp(array('-t', $tmpdir[0], '-d'));
        $tmpdir[]  = System::mktemp(array('-t', $tmpdir[1], '-d'));
        $tmpfile[] = System::mktemp(array('-t', $tmpdir[0]));
        $tmpfile[] = System::mktemp(array('-t', $tmpdir[1]));
        $tmpfile[] = System::mktemp(array('-t', $tmpdir[2]));
        
        $local  = $tmpdir[0].DIRECTORY_SEPARATOR;
        $remote = './'.$this->_getLastPart($tmpdir[0]).'/';
        
        $ret = $this->ftp->putRecursive($local, $remote);
        $this->assertFalse(PEAR::isError($ret));
        
        for ($i = 0; $i < 3; $i++) {
            $ret = $this->ftp->cd($this->_getLastPart($tmpdir[$i]).'/');
            $this->assertFalse(PEAR::isError($ret));
            
            $dirlist = $this->ftp->ls();
            $this->assertFalse(PEAR::isError($dirlist));
          
            $dirlist   = $this->_getNames($dirlist);
            $dirlistok = array($this->_getLastPart($tmpfile[$i]), '.', '..');
            if ($i < 2) {
                $dirlistok[] = $this->_getLastPart($tmpdir[$i+1]);
            }
            
            sort($dirlist);
            sort($dirlistok);
            
            $this->assertEquals($dirlist, $dirlistok);
        }
    }

    /**
     * Tests functionality of Net_FTP::_makeDirPermissions()
     * 
     * @return void
     * @see Net_FTP::_makeDirPermissions()
     */
    public function testMakeDirPermissions()
    {
        if ($this->ftp == null) {
            $this->fail('This test requires a working FTP connection. Setup '.
            'config.php with proper configuration parameters. ('.
            $this->setupError.')');
        }
        $tests = array(
            '111' => '111',
            '110' => '110',
            '444' => '555',
            '412' => '512',
            '641' => '751',
            '666' => '777',
            '400' => '500',
            '040' => '050',
            '004' => '005',
        );
        
        foreach ($tests AS $in => $out) {
            $this->assertEquals($this->ftp->_makeDirPermissions($in), $out);
        }
    }

    /**
     * Tests functionality of Net_FTP::size()
     * 
     * @return void
     * @see Net_FTP::size()
     */
    public function testSize()
    {
        if ($this->ftp == null) {
            $this->fail('This test requires a working FTP connection. Setup '.
            'config.php with proper configuration parameters. ('.
            $this->setupError.')');
        }
        // upload in binary to avoid addition/removal of characters
        $this->ftp->put('testfile.dat', 'testfile.dat', FTP_BINARY);
        $this->assertEquals($this->ftp->size('testfile.dat'),
            filesize('testfile.dat'));
    }
    
    /**
     * Tests functionality of Net_FTP::setMode(), Net_FTP::checkFileExtension(),
     * Net_FTP::addExtension() and Net_FTP::removeExtension()
     * 
     * @return void
     * @see Net_FTP::checkFileExtension(), Net_FTP::addExtension(),
     *      Net_FTP::removeExtension(), Net_FTP::setMode()
     */
    public function testExtensions()
    {
        if ($this->ftp == null) {
            $this->fail('This test requires a working FTP connection. Setup '.
            'config.php with proper configuration parameters. ('.
            $this->setupError.')');
        }
        $this->ftp->setMode(FTP_ASCII);
        $this->ftp->addExtension(FTP_BINARY, 'tst');
        $this->assertEquals($this->ftp->checkFileExtension('test.tst'), FTP_BINARY);
        $this->ftp->removeExtension('tst');
        $this->assertEquals($this->ftp->checkFileExtension('test.tst'), FTP_ASCII);
        $this->ftp->setMode(FTP_BINARY);
        $this->assertEquals($this->ftp->checkFileExtension('test.tst'), FTP_BINARY);
    }

    /**
     * Tests functionality of Net_FTP::getExtensionsFile()
     * 
     * @return void
     * @see Net_FTP::getExtensionsFile()
     */
    public function testGetExtensionsFile()
    {
        if ($this->ftp == null) {
            $this->fail('This test requires a working FTP connection. Setup '.
            'config.php with proper configuration parameters. ('.
            $this->setupError.')');
        }
        $res = $this->ftp->getExtensionsFile('extensions.ini');
        $this->assertFalse(PEAR::isError($res), 'Test extensions file could be'.
            'loaded');
        
        $this->ftp->setMode(FTP_BINARY);
        $this->assertEquals($this->ftp->checkFileExtension('test.asc'), FTP_ASCII);
        $this->ftp->setMode(FTP_ASCII);
        $this->assertEquals($this->ftp->checkFileExtension('test.gif'), FTP_BINARY);
    }

    /**
     * Tests _determineOSMatch
     * 
     * @return void
     * @see Net_FTP::_determineOSMatch()
     */
    public function testDetermineOSMatch()
    {
        if ($this->ftp == null) {
            $this->fail('This test requires a working FTP connection. Setup '.
            'config.php with proper configuration parameters. ('.
            $this->setupError.')');
        }
        $dirlist = array(
            'drwxrwsr-x  75 upl.oad  (?).         3008 Oct 30 21:09 ftp1',
        );
        
        $res = $this->ftp->_determineOSMatch($dirlist);
        $this->assertFalse(PEAR::isError($res),
            'The directory listing should be recognized');
        $this->assertEquals($res['pattern'],
            $this->ftp->_ls_match['unix']['pattern'],
            'The input should be parsed by the unix pattern');
    }
    
    /**
     * Return all name keys in the elements of an array
     *
     * @param array $in Multidimensional array
     *
     * @return array Array containing name keys
     */
    function _getNames($in)
    {
        $return = array();
        foreach ($in as $v) {
            $return[] = $v['name'];
        }
        return $return;
    }
     
    /**
     * Return the last element of a local path
     *
     * @param string $in Path
     *
     * @return array Last part of path
     */
    function _getLastPart($in)
    {
        $start = strrpos($in, DIRECTORY_SEPARATOR) + 1;
        return substr($in, $start);
    }
}

// Call Net_FTPTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == 'Net_FTPTest::main') {
    Net_FTPTest::main();
}
?>
