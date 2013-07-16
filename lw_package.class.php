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
    
    protected function executeControllerAction($package, $controller, $cmd)
    {
        $bootstrapClass = "\\".$this->params['package']."\\Controller\\Bootstrap";
        if (class_exists($bootstrapClass, true)) {
            $bootstrap = new $bootstrapClass();
            $bootstrap->execute();
        }
        $ControllerClass = "\\".$this->params['package']."\\Controller\\".$this->params['Controller'];
        $Controller = new $ControllerClass($cmd, $this->params['oid']);
        try {
            return $Controller->execute();
        }
        catch (Exception $e) {
            die($e->getMessage());
        }
    }
    
    public function buildPageOutput()
    {
        if ($this->request->getAlnum("cmd")) {
            $cmd = $this->request->getAlnum("cmd");
        }
        else {
            $cmd = $this->params['default'];
        }
        $response = $this->executeControllerAction($this->params['package'], $this->params['Controller'], $cmd);
        
        if ($response->getParameterByKey('reloadParent') == 1) {
            die('<script>parent.location.reload();</script>');
        }
        if ($response->getParameterByKey('cmd')) {
            if (intval($response->getParameterByKey('redirectIndex')) > 0 ) {
                $url = lw_page::getInstance(intval($response->getParameterByKey('redirectIndex')))->getUrl($response->getParameterArray());
            }
            else {
                $url = lw_page::getInstance()->getUrl($response->getParameterArray());
            }
            $this->pageReload($url);
        }
        elseif(intval($response->getParameterByKey('redirectIndex')) > 0 ) {
            $url = lw_page::getInstance(intval($response->getParameterByKey('redirectIndex')))->getUrl($response->getParameterArray());
        }
        else {
            if ($response->getParameterByKey('die') == 1){
                die($response->getOutputByKey('output'));
            }
            return $response->getOutputByKey('output');
        }        
    }
}
