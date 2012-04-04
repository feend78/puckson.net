<?php

class View_Disclaimer extends View {
  function init() {
    parent::init();
    
    $this->template->trySet('ShortName', $this->api->getConfig('site/short_name','HPG'));
    $this->add('HtmlElement',null,'DisclaimerLink')
      ->setElement('a')
      ->setAttr('href', $this->api->getDestinationURL('waiver'))
      ->setAttr('target', '_new')
      ->setText('release of liability and assumption of risk agreement');
  }
  function defaultTemplate(){
    return array('view/disclaimer');
  } 
}
