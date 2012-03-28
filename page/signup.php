<?php

class page_signup extends Page {

  function init() {
    parent::init();

    //set page title
    $this->api->template->trySet('page_title',$this->api->getConfig('site/full_name','Hockey Pickup Group'));

    $this->add('Text',null,'ShortName')->set($this->api->getConfig('site/short_name','HPG'));
    $this->add('View_Disclaimer',null,'DisclaimerLink');

    // initial signup form definition
    $form = $this->add('Form',null,'SignupForm');
    $form->setFormClass('inline');
    $form->addField('line','email');
    $form->addField('password','password');

    $form->addField('dropdown','position')->setValueList(array('skater'=>'skater','goalie'=>'goalie'));
    $form->addField('dropdown','action')->setValueList(array('signup'=>'Sign Up','cancel'=>'Cancel Sign-Up'));

    $form->addSubmit('OK');

    // add a view for the current scrimmage
    $v=$this->add('View',null,null,array('view/scrimmage'));
    // get the id of the current scrimmage
    $m=$v->setModel('Scrimmage');
    $s=$m
      ->addCondition('current_start', '<'.date('Y-m-d H:i:s'),true)
      ->addCondition('current_end',   '>'.date('Y-m-d H:i:s'),true)
      ->getRows()
      ;
    // load the model with the data we found
    $m->loadData($s[0]['id']);
    // re-format time
    
    // TODO: fix!
    // this has to be handled somewhere else... noob hack
    $t=explode(':', $m->get('start_time'));
    if ($t[0] > 12) {
      $t[0]=$t[0]-12;
      $t[2]='PM';
    }
    else $t[2]='AM';
    $v->template->trySet('start_time', $t[0].':'.$t[1].' '.$t[2]);
         
    $ppg=$this->add('MVCGrid',null,'PlayingList');
    $ppg->setModel('Signup_Skater', array('number','player','priority','signup_dts','position'))
      ->addCondition('scrimmage_id',$m->get('id'))
      ->setOrder(null,'priority','desc')
      ->setOrder(null,'signup_dts')
      ;
    $ppg->dq->limit($this->api->getConfig('signup/max_players',22));

    $gpg=$this->add('MVCGrid',null,'GoalieList');
    $gpg->setModel('Signup_Goalie', array('player','priority','signup_dts','position'))
      ->addCondition('scrimmage_id',$m->get('id'))
      ->setOrder(null,'priority','desc')
      ->setOrder(null,'signup_dts')
      ;
    $gpg->dq->limit(2);
    
    $pwg=$this->add('MVCGrid',null,'SWaitingList');
    $pwg->setModel('Signup_Skater', array('player','priority','signup_dts','position'))
      ->addCondition('scrimmage_id',$m->get('id'))
      ->setOrder(null,'priority','desc')
      ->setOrder(null,'signup_dts')
      ;
    $pwg->dq->limit(100,$this->api->getConfig('signup/max_players',22));
    
    $gwg=$this->add('MVCGrid',null,'GWaitingList');
    $gwg->setModel('Signup_Goalie', array('player','priority','signup_dts','position'))
      ->addCondition('scrimmage_id',$m->get('id'))
      ->setOrder(null,'priority','desc')
      ->setOrder(null,'signup_dts')
      ;
    $gwg->dq->limit(100,2);

    if($form->isSubmitted()) {

      $auth = $this->add('SQLAuth')->usePasswordEncryption('sha256/salt');
      $auth->setSource('player','email','password');

      $e=strtolower($form->get('email'));
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
              $m->signPlayerUp($player->get('id'), $form->get('position'));
              $doit[]=$gpg->js()->reload();
              $doit[]=$ppg->js()->reload();
              $doit[]=$pwg->js()->reload();
              $doit[]=$gwg->js()->reload();
              $doit[]=$form->js()->univ()->successMessage('Signup Successful');
              $this->js(null,$doit)->execute();
              break;
            case 'cancel':
              $m->cancelSignup($player->get('id'), $form->get('position'));
              $doit[]=$ppg->js()->reload();
              $doit[]=$gpg->js()->reload();
              $doit[]=$pwg->js()->reload();
              $doit[]=$gwg->js()->reload();
              $doit[]=$form->js()->univ()->successMessage('Signup Canceled');
              $this->js(null,$doit)->execute();
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
