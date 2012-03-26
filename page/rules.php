<?php

class page_rules extends Page {
    function init(){
        parent::init();

        $this->api->template->trySet('page_title',$this->api->getConfig('site/full_name','Hockey Pickup Group'));

        $this->add('Text',null,'ShortName')->set($this->api->getConfig('site/short_name','HPG'));

     }
    function defaultTemplate(){
        return array('page/rules');
    }
}
