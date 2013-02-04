<?php

/**************************************************************************
*  Copyright notice
*
*  Copyright 2013 Logic Works GmbH
*
*  Licensed under the Apache License, Version 2.0 (the "License");
*  you may not use this file except in compliance with the License.
*  You may obtain a copy of the License at
*
*  http://www.apache.org/licenses/LICENSE-2.0
*  
*  Unless required by applicable law or agreed to in writing, software
*  distributed under the License is distributed on an "AS IS" BASIS,
*  WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
*  See the License for the specific language governing permissions and
*  limitations under the License.
*  
***************************************************************************/

class lw_package extends lw_plugin
{
    protected $request;
    protected $config;

    public function __construct()
    {
        lw_plugin::__construct();
        include_once(dirname(__FILE__).'/PackageAutoloader.php');
        $autoloader = new \lwPackage\PackageAutoloader();
        $autoloader->setConfig($this->config);
    }
    
    public function buildPageOutput()
    {
        /*
        $controllerClass = "\\".$this->params['package']."\\Controller\\".$this->params['Controller']."Controller";
        $controller = new $controllerClass();
        $controller->setCommand($this->request->getAlnum("cmd"));
        $controller->init();
        $response = $controller->execute();
        */ 
        
        if ($this->request->getAlnum("cmd")) {
            $cmd = $this->request->getAlnum("cmd");
        }
        else {
            $cmd = $this->params['default'];
        }
        
        $actionClass = "\\".$this->params['package']."\\Controller\\".$this->params['Controller']."\\".$cmd;
        $action = new $actionClass();
        $response = $action->execute();
        
        if ($response->getParameterByKey('reloadParent') == 1) {
            die('<script>parent.location.reload();</script>');
        }
        if ($response->getParameterByKey('cmd')) {
            $url = lw_page::getInstance()->getUrl($response->getParameterArray());
            $this->pageReload($url);
        }
        else {
            if ($response->getParameterByKey('die') == 1){
                die($response->getOutputByKey('output'));
            }
            return $response->getOutputByKey('output');
        }        
    }    
}
