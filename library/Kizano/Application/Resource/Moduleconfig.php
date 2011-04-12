<?php
/**
 *  Kizano_Application_Resource_ModuleConfig
 *
 *  LICENSE
 *
 *  This source file is subject to the new BSD license that is bundled
 *  with this package in the file LICENSE.txt.
 *  It is also available through the world-wide-web at this URL:
 *  http://framework.zend.com/license/new-bsd
 *  If you did not receive a copy of the license and are unable to
 *  obtain it through the world-wide-web, please send an email
 *  to license@zend.com so we can send you a copy immediately.
 *
 *  @category   Kizano
 *  @package    Application
 *  @copyright  Copyright (c) 2009-2011 Markizano Draconus <markizano@markizano.net>
 *  @license    http://framework.zend.com/license/new-bsd     New BSD License
 *  @author     Markizano Draconus <markizano@markizano.net>
 */

/**
 *  Application resource to load configs on a module basis.
 *
 *  @category   Kizano
 *  @package    Application
 *  @copyright  Copyright (c) 2009-2011 Markizano Draconus <markizano@markizano.net>
 *  @license    http://framework.zend.com/license/new-bsd     New BSD License
 *  @author     Markizano Draconus <markizano@markizano.net>
 */
class Kizano_Application_Resource_ModuleConfig
    extends Zend_Application_Resource_ResourceAbstract
{

    /**
     *    Initializes this resource loader.
     *    @return void
     */
    public function init()
    {
        if (Zend_Registry::isRegistered('cachemanager')) {
            $cache = Zend_Registry::get('cachemanager')->getCache('generic');
            $moduleCache = $cache->load('moduleCache');
            if ($moduleCache) {
                $this->_bootstrap->getApplication()->setOptions($moduleCache);
            } else {
                $moduleCache = $this->_getModuleSetup();
                $cache->save($moduleCache, 'moduleCache');
            }
        } else {
            return $this->_getModuleSetup();
        }
    }

    /**
     * Load the module's ini files
     *
     * @return void
     */
    protected function _getModuleSetup()
    {
        $bootstrap = $this->getBootstrap();

        if (!($bootstrap instanceof Zend_Application_Bootstrap_Bootstrap)) {
            throw new Zend_Application_Exception('Invalid bootstrap class');
        }

        # We need to obtain a few things before we go iterating thru all of the directories.
        $bootstrap->bootstrap('frontcontroller');
        $config = $this->_bootstrap->getApplication()->getOptions();
        $front = $bootstrap->getResource('frontcontroller');
        # Iterate thru each of the modules defined by the config
        foreach (array_keys($config['resources']['modules']) as $module) {
            # Attempt to get the configuration directory for this module
            $configPath  = $front->getModuleDirectory($module) . DS . 'configs';
            if (file_exists($configPath)) {
                # If the directory exists, then create a directory iterator from it
                $cfgdir = new DirectoryIterator($configPath);
                // Iterate thru each of the files in the directory and attempt
                // to load the config from each directory.
                foreach ($cfgdir as $file) {
                    if ($file->isFile()) {
                        $filename = $file->getFilename();
                        $options = $this->_loadOptions($configPath . DS . $filename);
                        if (($len = strpos($filename, '.')) !== false) {
                            $cfgtype = substr($filename, 0, $len);
                        } else{
                            $cfgtype = $filename;
                        }

                        if (!isset($config['resources']['modules'][$module]) ||
                            !is_array($config['resources']['modules'][$module]) )
                        {
                            $config['resources']['modules'][$module] = array();
                        }

                        if (strtolower($cfgtype) == 'module') {
                            if (array_key_exists($module, $config['resources']['modules'])) {
                                if (is_array($config['resources']['modules'][$module])) {
                                    $config['resources']['modules'][$module] =
                                        array_merge($config['resources']['modules'][$module], $options);
                                } else {
                                    $config['resources']['modules'][$module] = $options;
                                }
                            } else {
                                $config['resources']['modules'][$module] = $options;
                            }
                        } else {
                            $config['resources']['modules'][$module]['resources'][$cfgtype] = $options;
                        }
                    }
                }
            }
        }

        # Reset the options and config back to the bootstrap/config-registry objects
        $this->getBootstrap()->getApplication()->setOptions($config);
        Zend_Registry::set('config', $config);

        return $config;
    }

    /**
     * Load the config file
     *
     * @param string $fullpath
     * @return array
     */
    protected function _loadOptions($fullpath) {
        if (file_exists($fullpath)) {
            switch(substr(trim(strtolower($fullpath)), -3)) {
                case 'ini':
                    $cfg = new Zend_Config_Ini($fullpath, ENVIRONMENT);
                    break;
                case 'xml':
                    $cfg = new Zend_Config_Xml($fullpath, ENVIRONMENT);
                    break;
                default:
                    throw new Zend_Config_Exception('Invalid format for config file');
            }
        } else{
            throw new Zend_Application_Resource_Exception('File does not exist');
        }

        return $cfg->toArray();
    }
}
