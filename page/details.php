<?php

class page_details extends Page {
    function init(){
        parent::init();

        $this->api->template->trySet('page_title',$this->api->getConfig('site/full_name','Hockey Pickup Group'));

        $this->add('Text',null,'ShortName')->set($this->api->getConfig('site/short_name','HPG'));
        $this->add('Text',null,'AdminEmail')->set($this->api->getConfig('site/admin_email','admin@puckson.net'));

     }
    function defaultTemplate(){
        return array('page/details');
    }
}
