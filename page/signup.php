<?php

class page_signup extends Page {

  function init() {
    parent::init();

    //set page title
    $this->api->template->trySet('page_title',$this->api->getConfig('site/full_name','Hockey Pickup Group'));

    $this->add('Text',null,'ShortName')->set($this->api->getConfig('site/short_name','HPG'));

    // initial signup form definition
    $form = $this->add('Form',null,'SignupForm');
    $form->setFormClass('inline');
    $form->addField('line','email');
    $form->addField('password','password');

    $form->addField('dropdown','position')->setValueList(array('skater'=>'skater','goalie'=>'goalie','either'=>'either'));
    $form->addField('dropdown','action')->setValueList(array('signup'=>'Sign Up','cancel'=>'Cancel Sign-Up'));

    $form->addSubmit('Login');

    // determine the current scrimmage
    $scrimmage = $this->add('Model_Scrimmage');
    $s = $scrimmage
      ->addCondition('current_start', '<'.date('Y-m-d H:i:s'),true)
      ->addCondition('current_end',   '>'.date('Y-m-d H:i:s'),true)
      ->getRows()
      ;
    $scrimmage->loadData($s[0]['id']);

    // add headings and grids to display lists
    $this->add('H3',null,'PlayingList')->setText('Playing List');
    $pg=$this->add('MVCGrid',null,'PlayingList');
    $pg->setModel('Signup_Playing', array('player','signup_dts','position','is_playing'))
      ->addCondition('scrimmage_id',$scrimmage->get('id'))
      ;

    $this->add('H3',null,'WaitingList')->setText('Waiting List');
    $wg=$this->add('MVCGrid',null,'WaitingList');
    $wg->setModel('Signup_Waiting', array('player','signup_dts','position','is_playing'))
      ->addCondition('scrimmage_id',$scrimmage->get('id'))
      ;

    // remove is_playing fields
    $pg->removeColumn('is_playing');
    $wg->removeColumn('is_playing');
    
    if($form->isSubmitted()) {

      $auth = $this->add('SQLAuth')->usePasswordEncryption('sha256/salt');
      $auth->setSource('player','email','password');

      $e=$form->get('email');
      $p=$form->get('password');

      // check email was not empty
      if (empty($e)) $form->getElement('email')->displayFieldError('Incorrect login');
      else {
        // Manually encrypt password
        $enc_p = $auth->encryptPassword($p,$e);

        // Manually verify login
        if(!$auth->verifyCredintials($e,$enc_p)) $form->getElement('password')->displayFieldError('Incorrect login');
        else {
          // load the player who is signing up
          $player = $this->add('Model_Player')->loadBy('email', $e);
          
          switch ($form->get('action')) {
            case 'signup':   
              //$this->js()->univ()->alert('debug: '.$form->get('action'))->execute();
              $scrimmage->signPlayerUp($player->get('id'), $form->get('position'));
              $pg->js()->reload()->execute();
              $wg->js()->reload()->execute();
              $form->js()->univ()->successMessage('Signup Successful')->execute();
              break;
            case 'cancel':
              $scrimmage->cancelSignup($player->get('id'));
              $pg->js()->reload()->execute();
              $wg->js()->reload()->execute();
              $form->js()->univ()->successMessage('Signup Canceled')->execute();
              break;
            case 'guest':
            case 'cancel_guest':
              break;
          }
          // we don't need the player any more
          $player->destroy();
        } 
      }
    }
  }
  function defaultTemplate(){
    return array('page/signup');
  }
}
