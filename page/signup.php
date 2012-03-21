<?php

class page_signup extends Page {
    function init(){
        parent::init();
        $this->api->auth->check();

        $this->add('MVCGrid')->setModel('Scrimmage');
    }
}
