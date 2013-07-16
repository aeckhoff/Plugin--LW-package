<?php

namespace lwPackage;

class PackageAutoloader
{
    public function __construct()
    {
        spl_autoload_register(array($this, 'loader'));
    }

    public function setConfig($config)
    {
        $this->config = $config;
    }
    
    private function loader($className) 
    {
        if (strstr($className, 'LWddd')) {
            $config = \lw_registry::getInstance()->getEntry('config');
            $path = $this->config['plugin_path']['lw'].'lw_ddd';
            $filename = str_replace('LWddd', $path, $className);
        }
        elseif (strstr($className, 'LWmvc')) {
            $config = \lw_registry::getInstance()->getEntry('config');
            $path = $this->config['plugin_path']['lw'].'lw_mvc';
            $filename = str_replace('LWmvc', $path, $className);
        }
        else {
            $className = str_replace("Factory", "", $className);
            $filename = $this->config['path']['package'].$className;
            //$filename = str_replace('FAB', $path.'/fab_module', $className);
        }
        $filename = str_replace('\\', '/', $filename).'.php';
        
        if (is_file($filename)) {
            include_once($filename);
        }
    }
}