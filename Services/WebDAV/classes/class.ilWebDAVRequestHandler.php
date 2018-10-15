<?php

use function Sabre\HTTP\decodePath;
use Sabre\DAV\Exception\BadRequest;

require_once 'libs/composer/vendor/autoload.php';

// Include all needed classes for a webdav-request
include_once "Services/WebDAV/classes/auth/class.ilWebDAVAuthentication.php";
include_once "Services/WebDAV/classes/db/class.ilWebDAVDBManager.php";
include_once "Services/WebDAV/classes/class.ilWebDAVObjDAVHelper.php";
include_once "Services/WebDAV/classes/class.ilWebDAVRepositoryHelper.php";
include_once "Services/WebDAV/classes/dav/class.ilObjectDAV.php";
include_once "Services/WebDAV/classes/dav/class.ilObjContainerDAV.php";
include_once "Services/WebDAV/classes/dav/class.ilObjFileDAV.php";
include_once "Services/WebDAV/classes/dav/class.ilObjCategoryDAV.php";
include_once "Services/WebDAV/classes/dav/class.ilObjCourseDAV.php";
include_once "Services/WebDAV/classes/dav/class.ilObjGroupDAV.php";
include_once "Services/WebDAV/classes/dav/class.ilObjFolderDAV.php";
include_once "Services/WebDAV/classes/dav/class.ilMountPointDAV.php";
include_once "Services/WebDAV/classes/dav/class.ilClientNodeDAV.php";
include_once "Services/WebDAV/classes/dav/class.ilObjRepositoryRootDAV.php";


class ilWebDAVRequestHandler
{
    private static $instance;
    
    public static function getInstance()
    {
        return self::$instance ? self::$instance : self::$instance = new ilWebDAVRequestHandler();
    }

    public function handleRequest()
    {
        $this->runWebDAVServer();
    }
    
    protected function runWebDAVServer()
    {
        try {
            $server = new Sabre\DAV\Server($this->getRootDir());
            $this->setPlugins($server);
            $server->exec();
        } catch (Exception $e)
        {
            echo "Something went wrong with setting up the server: '" . $e->getMessage() . "' in File " . $e->getFile() . ':' . $e->getLine();
            exit;
        }

    }
    
    
    /**
     * Set server plugins
     */    
    protected function setPlugins($server)
    {
        global $DIC;

        // Set authentication plugin
        $webdav_auth = new ilWebDAVAuthentication();
        $cal = new Sabre\DAV\Auth\Backend\BasicCallBack(array($webdav_auth, 'authenticate'));
        $plugin = new Sabre\DAV\Auth\Plugin($cal);
        $server->addPlugin($plugin);

        // Set Lock Plugin
        $db_manager = new ilWebDAVDBManager($DIC->database());
        $lock_backend = new ilWebDAVLockBackend($db_manager, $DIC->user(), $DIC->access());
        $lock_plugin = new Sabre\DAV\Locks\Plugin($lock_backend);
        $server->addPlugin($lock_plugin);

        // Set Browser Plugin
        $browser_plugin =  new Sabre\DAV\Browser\Plugin();
        $server->addPlugin($browser_plugin);
         
    }
    
    /**
     * Return the first object to mount on WebDAV
     * 
     * @return ilMountPointDAV
     */
    protected function getRootDir()
    {
        global $DIC;

        $repo_helper = new ilWebDAVRepositoryHelper($DIC->access(), $DIC->repositoryTree());
        $dav_helper = new ilWebDAVObjDAVHelper($repo_helper);
        return new ilMountPointDAV($repo_helper, $dav_helper);
    }
}