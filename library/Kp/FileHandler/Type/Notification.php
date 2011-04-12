<?php
/**
 * Kp_FileHandler_Type_Notification
 *
 * PHP version 5.2
 *
 * LICENSE: This source file is closed source, strictly confidential and
 * proprietary to Integraclick Inc. Viewing the contents of this file binds the
 * viewer to the NDA agreement  available by Integraclick Inc. Electronic
 * transfer of this file outside of the Integraclick corporate network is
 * strictly prohibited. Questions, comments or concerns should be directed to
 * compliance@integraclick.com
 *
 * @category  Ic
 * @package   Kp_FileHandler
 * @author    James Solomon <james@integraclick.com>
 * @author    Mark Harris <mark.harris@integraclick.com>
 * @copyright 2010 Integraclick Inc.
 * @license   http://www.integraclick.com Integraclick License
 * @link      http://adgenii.clickbooth.com
 */
class Kp_FileHandler_Type_Notification extends Kp_FileHandler_Type_Abstract
{
    /**
     *  Holds the file resource.
     *  @var resource [file pointer]
     */
    protected $_fp;

    /**
     *  Because we are dealing with only one file resource, cache the EOF offset.
     *  @var integer
     */
    protected $_eof;

    public $templates = array(
        'development' => array(
            'filePath' => '{{appRoot}}/notifications/events/{{accountId}}',
            'notificationFilePath' => '{{appRoot}}/notifications/events/{{accountId}}/notification'
        ),
        'staging' => array(
            'filePath' => '{{appRoot}}/notifications/events/{{accountId}}',
            'notificationFilePath' => '{{appRoot}}/notifications/events/{{accountId}}/notification'
        ),
        'testing' => array(
            'filePath' => '{{appRoot}}/notifications/events/{{accountId}}',
            'notificationFilePath' => '{{appRoot}}/notifications/events/{{accountId}}/notification'
        ),
        'production' => array(
            'fileName' => array(
                '{{name}}'
            ),
            'notificationFileName' => array(
                '{{notificationName}}'
            ),
            'writeLockFileName' => array(
                '{{name}}.writelock'
            ),
            'readLockFileName' => array(
                '{{name}}.readlock'
            ),
            'defaultPath' => '{{appRoot}}/notifications/events/{{accountId}}',
            'defaultFile' => '{{name}}'
        )
    );

    /**
     *  Acts like the construct, but not really. Use only when needed.
     *  
     *  @return void
     */
    public function init()
    {
        // If the file doesn't even exist, don't attempt at this stuff.
        $file = $this->getSavePath();
        if (!file_exists($file)) {
            if (file_exists(dirname($file)) && is_dir(dirname($file))) {
                touch($file);
                if ($this->_environment == 'development' || $this->_environment == 'testing') {
                    chmod($file, 0777);
                }
            } else {
                throw new Kp_FileHandler_Exception("File does not exist: `$file'");
            }
        }

        if (!is_readable($file)) {
            throw new Kp_FileHandler_Exception("Cannot read `$file'");
        }

        // Attempt to open the file 5 times.
        for ($i = 0; $i <= 5; $i ++) {
            $this->_fp = fOpen($file, 'rb+');
            // If we successfully open the file, then we're good.
            if (is_resource($this->_fp) && $this->_fp) {
                break;
            }
            // If we fail to open the file, it's possible some other resources is currently accessing it.
            // So sleep on it... for up to a second.
            usleep(mt_rand(0, 1000000));
        }

        // If we failed to open the file after 5 attempts,
        if (!is_resource($this->_fp) || !$this->_fp) {
            // Throw an exception
            throw new Kp_FileHandler_Exception("Error opening file: $file");
        }
    }

    /**
     *  Closes the file pointer and does some cleanup jobs.
     *  @return void
     */
    public function __destruct()
    {
        is_resource($this->_fp) && fClose($this->_fp);
        unset($this->_settings, $this->_options, $this->templates, $this->_fp);
    }

    /**
     *  Sets the internal pointer offset.
     *  @param offset   integer     The offset.
     *  @return void
     */
    public function setOffset($offset = 0)
    {
        fSeek($this->_fp, $offset, SEEK_SET);
    }

    /**
     *  Gets the current offset in this file.
     *  @return integer
     */
    public function getOffset()
    {
        return fTell($this->_fp);
    }

    /**
     *  Gets the offset byte to the last full line in the open file resource. Returns the file pointer
     *  to its original value.
     *  @return integer
     */
    public function getEOF()
    {
        if (!empty($this->_eof)) {
            return $this->_eof;
        }
        $fp = $this->_acquireReadLock();

        $offset = fTell($this->_fp);
        fSeek($this->_fp, 0, SEEK_END);
        $eof = fTell($this->_fp);

        // The last character could be a newline, so don't read the very last 2 bytes.
        for ($i = $eof -2; fTell($this->_fp) >= 2; $i--, fSeek($this->_fp, $i, SEEK_SET)) {
            $c = fGetc($this->_fp);
            /**
             *  If we read the file in reverse and find a newline, mark the position before it as the
             *  EOF mark. This file could be in the middle of a write, so we don't want to return
             *  half of a newsfeed drip.
             */
            if ($c = PHP_EOL || $c == chr(10) || $c == chr(13)) {
                $this->_eof = fTell($this->_fp) -1;
                break;
            }
        }

        fSeek($this->_fp, $offset, SEEK_SET);

        $this->_releaseLock($fp, 'read');
        return $this->_eof;
    }

    public function saveFileContents($contents)
    {
        throw new Kp_FileHandler_Exception('You cannot use "' . __FUNCTION__ . '" function with a Notification file.');
    }

    /**
     *  Appends the contents of a string to the given file.
     *  @param content      string      The content to append to the file.
     *  @return void
     */
    public function appendToFile($content)
    {
        try {
            // First check if directory exists; create if it doesn't
            $this->_createSaveDirectory('filePath');

            // Acquire write lock
            $lock = $this->_acquireLock('writeLockFileName');

            $fp = fopen($this->getSavePath(), 'a');
            //file was locked so now we can store information
            fseek($fp, 0, SEEK_END);
            fwrite($fp, trim($content) . chr(10));
            fclose($fp);

            // Release lock
            $this->_releaseLock($lock);
        } catch (Exception $e) {
            $this->_releaseLock($lock);
            throw $e;
        }
    }

    /**
     *  
     *  
     *  
     */
    public function saveSubscriptionNotification(array $lines)
    {
        try {
            // First check if directory exists; create if it doesn't
            $this->_createSaveDirectory('notificationFilePath');

            $fp = fopen($this->_getSubscriptionNotificationPath(), 'a');

            print 'saving subnot: ' . $this->_getSubscriptionNotificationPath() . PHP_EOL;

            fseek($fp, 0, SEEK_END);
            foreach ($lines as &$line) {
                fwrite($fp, $line);
            }
            fclose($fp);

            return true;

        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     *  
     *  
     *  
     */
    public function readSubscriptionNotification()
    {
        $lines = array();

        $fp = fopen($this->_getSubscriptionNotificationPath(), 'r');
        while ($line = fgets($fp)) {
            $lines[] = $line;
        }
        fclose($fp);

        return $lines;
    }

    /**
     *  Return lines from a file.
     *  @param nextReadLine     integer     The offset to start seeking files.
     *  @param fend             integer     The end of the file, or when we should stop reading.
     *  @return array
     */
    public function readFromFile($nextReadLine, &$fend)
    {
        $file = $this->getSavePath();
        if (!file_exists($file)) {
            throw new Kp_FileHandler_Exception('File does not exist: ' . $file);
        }
        print 'opening file ' . $file . PHP_EOL;

        try {

            // Acquire read lock
            $lock = $this->_acquireLock('readLockFileName');

            if (!($fp = fopen($file, 'r'))) {
                throw new Kp_FileHandler_Exception('Error opening file: ' . $file);
            }

            // Go to end of file and determine last line to read
            $fend = $this->_findReadEndLine($fp);

            print 'reading from: ' . $nextReadLine . ' to ' . $fend . PHP_EOL;

            // Read lines
            $lines = $this->_readLinesFromFile($fp, $nextReadLine, $fend);

            fclose($fp);

            // Release read lock
            $this->_releaseLock($lock);

        } catch (Exception $e) {
            // Release read lock
            $this->_releaseLock($lock);
            throw $e;
        }

        return $lines;
    }


    /**
     *  Reads lines repeatedly in reverse until we obtain the desired number of lines.
     *  @param lines    Integer     The line count to obtain.
     *  @param offset   Integer     The physical file offset to start reading in reverse.
     *  @notes Reads a file in reverse order, but returns the string in LTR mode.
     *  @return Array
     */
    public function readLinesReverse($lines, $offset = null)
    {
        $result = array();
        if ($offset === null) {
            $offset = $this->getEOF();
        }
        for ($i = $lines; $i >= 0; $i--, $offset = $this->getOffset()) {
            $result[] = $this->readLineReverse($offset);
        }
        return $result;
    }

    /**
     *  Reads a line from a file in reverse.
     *  @notes Reads a file in reverse order, but returns the string in LTR mode.
     *  @param offset   integer     The offset to start the read.
     *  @return string
     */
    public function readLineReverse($offset = null)
    {
        // First check to make sure we have some file to retrace...
        if ($offset <= 0 && $offset !== null) {
            return false;
        }
        $result = null;
        // If the offset was not provided, then assume to start from the end of the file.
        if ($offset === null) {
            $offset = $this->getEOF();
        }
        try {
            // Lock this file down for reading...
            $lock = $this->_acquireReadLock();
            // Iterate through each byte of the file until we reach the beginning.
            for (
                $i = $offset, fSeek($this->_fp, $offset, SEEK_SET);
                fTell($this->_fp) >= 0;
                $i--, fSeek($this->_fp, ($i >= 0) ? $i: 0, SEEK_SET)
            ) {
                $c = fGetc($this->_fp);

                // If we run into a line break, break out of this loop.
                if (fTell($this->_fp) && ($c == PHP_EOL || $c == chr(10) || $c == chr(13))) {
                    fSeek($this->_fp, $i -1, SEEK_SET);
                    break;
                }
                $result = $c . $result;
                // If we've gone too far back in the file, break out of this loop!
                if (fTell($this->_fp) <= 1) {
                    // Without this line, PHP will timeout the connection, please do not remove, or else <.<
                    fSeek($this->_fp, 0, SEEK_SET);
                    break;
                }
            }
            $this->_releaseLock($lock, 'read');
        } catch (Exception $e) {
            $this->_releaseLock($lock, 'read');
            throw $e;
        }
        return $result;
    }

    /**
     *  Locks this resource down for writing.
     *  @return resource
     */
    protected function _acquireWriteLock()
    {
        $lock = $this->_buildPath('filePath', 'writeLockFileName');
        $fp = fopen($lock, 'a+');
        // Only for local/development purposes do we want to change the permissions so my worker can
        // access the file in addition to apache.
        if ($this->_environment == 'development' || $this->_environment == 'testing') {
            chmod($lock, 0777);
        }
        // Waiting until file will be locked for writing
        for ($canWrite = fLock($fp, LOCK_EX | LOCK_NB); !$canWrite; $canWrite = fLock($fp, LOCK_EX | LOCK_NB)) {
            //Sleep for 0 - 500 miliseconds, to avoid colision
            usleep(mt_rand(1, 5000000));
        }
        return $fp;
    }

    /**
     *  Locks this resource down for reading.
     *  @return resource
     */
    protected function _acquireReadLock()
    {
        $lock = $this->_buildPath('filePath', 'readLockFileName');
        $fp = fopen($lock, 'wb');
        // Only for local/development purposes do we want to change the permissions so my worker can
        // access the file in addition to apache.
        if ($this->_environment == 'development' || $this->_environment == 'testing') {
            chmod($lock, 0777);
        }
        // Waiting until file will be locked for reading
        for ($canRead = fLock($fp, LOCK_SH | LOCK_NB); !$canRead; $canRead = fLock($fp, LOCK_SH | LOCK_NB)) {
            //Sleep for 0 - 500 miliseconds, to avoid colision
            usleep(mt_rand(1, 5000000));
        }
        return $fp;
    }

    /**
     *  Read lines from the file.
     *  @param fp           resource        File pointer opened by fOpen().
     *  @param nextReadLine integer         The byte to seek to in the file.
     *  @param fend         integer         The end of the file.
     *  @return array
     */
    protected function _readLinesFromFile($fp, $nextReadLine, $fend)
    {
        fseek($fp, $nextReadLine);
        $lines = array();
        while ($line = fgets($fp)) {
            $lines[] = $line;

            // If we have read enough, then stop
            $pointer = ftell($fp);
            if ($pointer >= $fend) {
                break;
            }
        }
        return $lines;
    }

    /**
     * Get the last complete line (\r\n) file pointer offset in a file
     * Returns the internal location to the file pointer.
     * @param $fp   resource    The file pointer to use.
     * @return integer
     */
    protected function _findReadEndLine($fp)
    {
        $cursor = -1;
        fseek($fp, $cursor, SEEK_END);
        $char = fgetc($fp);

        // Find a new line
        while (($cursor >= 0) && (($char != "\n") || ($char != "\r"))) {
            //print $cursor; print $char . PHP_EOL;
            fseek($fp, $cursor--, SEEK_END);
            $char = fgetc($fp);
        }

        return ftell($fp);
    }

    /**
     *  Creates the directory in which we save our data.
     *  @param type     string      The path template type to use.
     *  @return boolean
     */
    protected function _createSaveDirectory($type)
    {
        $pathTemplate = $this->getSetting($type);
        if (!$pathTemplate)
            return false;

        $path = $this->_processTemplate($pathTemplate, null);

        if (!file_exists($path))
            mkdir($path, 0777, true);

        return true;
    }

    /**
     *  Locks a file down for writing.
     *  @param lockFileName     string      The file to lock down.
     *  @return resource
     */
    protected function _acquireLock($lockFileName)
    {
        $fp = fopen($this->getLockPath($lockFileName), 'a');

        // If the lock file requested is a write request, use exclusive locking,
        // else, if the lock requested is a read lock, use shared locking.
        $lock = strPos('read', $lockFileName) === false? LOCK_EX | LOCK_NB: LOCK_SH | LOCK_NB;
        //Waiting until file will be locked for writing
        $canWrite = flock($fp, $lock);
        while (!$canWrite) {
            //Sleep for 0 - 500 miliseconds, to avoid colision
            $miliSeconds = mt_rand(1, 50); //1 u = 100 miliseconds
            usleep(round($miliSeconds * 10000));
            $canWrite = flock($fp, LOCK_EX | LOCK_NB);
        }

        return $fp;
    }

    /**
     *  Releases a lock on a file.
     *  @param fp   resource    the file pointer opened by fOpen().
     *  @return void
     */
    protected function _releaseLock($fp)
    {
        is_resource($this->_fp) && fLock($this->_fp, LOCK_UN);
        flock($fp, LOCK_UN);
        fclose($fp);
    }

    /**
     *  Gets the path to the file which has a lock.
     *  @param writeFileName     string     The file who's lock to obtain.
     *  @return string
     */
    public function getLockPath($writeFileName)
    {
        return $this->_buildPath('filePath', $writeFileName);
    }

    /**
     *  
     *  
     *  
     */
    protected function _getSubscriptionNotificationPath()
    {
        return $this->_buildPath('notificationFilePath', 'notificationFileName');
    }

    /**
     * Prunes the subscription file where raw notifications are written.
     * @param <type> $minFp
     */
    public function pruneSubscription($minFp)
    {
        $file = $this->getSavePath();
        $tempFile = $this->getSavePath() . ".tmp";
        if (!file_exists($file)) {
            //throw new Kp_FileHandler_Exception('File does not exist: ' . $file);
            print 'File does not exist: ' . $file . PHP_EOL;
            return;
        }
        print 'opening file to prune: ' . $file . PHP_EOL;

        try {

            // Acquire locks
            $readlock  = $this->_acquireLock('readLockFileName');
            $writelock = $this->_acquireLock('writeLockFileName');

            // Open two files
            if (!($readfp = fopen($file, 'r'))) {
                throw new Kp_FileHandler_Exception('Error opening file: ' . $file);
            }
            if (!($writefp = fopen($tempFile, 'w'))) {
                throw new Kp_FileHandler_Exception('Error opening file: ' . $tempFile);
            }

            //print "seeking to $minFp" . PHP_EOL;

            // Go to appropriate line
            fseek($readfp, $minFp);

            // Reverted back to one line reading at a time; other was not working
            while ($line = fgets($readfp)) {
                //print ' got ' . $line . PHP_EOL;
                fwrite($writefp, $line);
            }

            //$lines = fread($readfp, filesize($file));
            //print_r($lines);
            //fwrite($writefp, $lines);

            // Delete original file
            fclose($readfp);
            unlink($file);

            // Rename temp file
            fclose($writefp);
            rename($tempFile, $file);

            // Release locks
            $this->_releaseLock($readlock);
            $this->_releaseLock($writelock);

        } catch (Exception $e) {
            // Release locks
            $this->_releaseLock($readlock);
            $this->_releaseLock($writelock);
            throw $e;
        }

        return $lines;
    }
}

