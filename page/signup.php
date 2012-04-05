<?php

class page_signup extends Page {

  function init() {
    parent::init();

    //set page title
    $this->api->template->trySet('page_title',$this->api->getConfig('site/full_name','Hockey Pickup Group'));

    // set up headers with "no scrimmage" message initially
    $hf=$this->add('H3',null,'SignupForm')->setText('Signups Closed');
    $hl=$this->add('H3')->setText('No Scrimmage Signup This Week');
    
    // get the id of the current scrimmage
    $scrimmage=$this->add('Model_Scrimmage')
      ->addCondition('current_start', '<'.date('Y-m-d H:i:s'),true)
      ->addCondition('current_end',   '>'.date('Y-m-d H:i:s'),true)
      ->loadBy('id like', '%');

    // add a view for the current scrimmage
    $v=$this->add('View',null,null,array('view/scrimmage'));
    $v->setModel('Scrimmage')->loadData($scrimmage->get('id'));
    // pretty up thte date and time
    $v->template->set('start_time', date('h:i A', strtotime($v->template->get('start_time'))));
    $v->template->set('date', date('m/d/Y', strtotime($v->template->get('date'))));
    
    // populate the page accordingly
    if ( !$scrimmage->isInstanceLoaded() ) {
      // no scrimmage; get rid of the view
      $v->destroy();
    } else {
      // there's a current scrimmage, show the full page
      
    // ** signup list ** \\
      
      $hl->setText('Current Scrimmage Signup');
      
      $sk=$this->add('Model_Signup_Skater');
      $subq1=$sk
        ->addCondition('scrimmage_id',$scrimmage->get('id'))
        ->resetQuery('subq')
        ->setQueryFields('subq',array('player','priority','signup_dts','position'))
        ->dsql('subq')
        ->where('is_before_deadline','Y')
        ->order('priority','desc')
        ->order('signup_dts')
        ->do_getAllHash();

      $subq2=$sk
        ->resetQuery('subq')
        ->setQueryFields('subq',array('player','priority','signup_dts','position'))
        ->dsql('subq')
        ->where('is_before_deadline','N')
        ->order('signup_dts')
        ->do_getAllHash();

      // We just needed the Model_Signup_Skater instance to generate queries. destroy it.
      $sk->destroy();
      
      // put together our two subqueries
      $signups = array_merge($subq1, $subq2);      
      // add numbers for the signup list
      $cnt=0;
      foreach ($signups as $i => $row) {
        $signups[$i]['number'] = ++$cnt;
      }
      
      $this->add('H4')->setText('Skater Playing List');
      $ppg=$this->add('MVCGrid');
      $ppg->setModel('Signup_Skater', array('player','priority','signup_dts','position'));
      $ppg->addColumn('number')
        ->add('Order')
        ->useArray($ppg->columns)
        ->move('number', 'first')
        ->now()
        ;
      $ppg->setStaticSource(array_slice($signups, 0, $this->api->getConfig('signup/max_players',22)));

      $this->add('H4')->setText('Goalie Playing List');
      $gpg=$this->add('MVCGrid');
      $gpg->setModel('Signup_Goalie', array('player','priority','signup_dts','position'))
        ->addCondition('scrimmage_id',$scrimmage->get('id'))
        ->setOrder(null,'priority','desc')
        ->setOrder(null,'signup_dts');
      $gpg->dq->limit(2);

      $this->add('H4')->setText('Skater Waiting List');
      $pwg=$this->add('MVCGrid');
      $pwg->setModel('Signup_Skater', array('player','priority','signup_dts','position'));
      $pwg->setStaticSource(array_slice($signups, $this->api->getConfig('signup/max_players',22)));

      $this->add('H4')->setText('Goalie Waiting List');
      $gwg=$this->add('MVCGrid');
      $gwg->setModel('Signup_Goalie', array('player','priority','signup_dts','position'))
        ->addCondition('scrimmage_id',$scrimmage->get('id'))
        ->setOrder(null,'priority','desc')
        ->setOrder(null,'signup_dts');
      $gwg->dq->limit(100,2);

      // but only allow signups before the close_time deadline
      $s_datetime=$scrimmage->get('date') .' '.$scrimmage->get('start_time');      
      if (time() < strtotime($this->api->getConfig('signup/close_time', 'now'), strtotime($s_datetime))) {
    // ** signup form ** \\
    
        $hf->setText('Scrimmage Signup');
        
        $form = $this->add('Form',null,'SignupForm');
        $form->setFormClass('inline');
        
        $form->addField('line','email');
        $form->addField('password','password');

        $form->addField('dropdown','position')->setValueList(array('skater'=>'skater','goalie'=>'goalie'));
        $form->addField('dropdown','action')->setValueList(array('signup'=>'Sign Up','cancel'=>'Cancel Sign-Up'));

        $form->addSubmit('OK');

        $this->add('View_Disclaimer',null,'Disclaimer');

    // ** form handling ** \\
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
                  $scrimmage->signPlayerUp($player->get('id'), $form->get('position'));
                  $js[]=$ppg->js()->reload();
                  $js[]=$gpg->js()->reload();
                  $js[]=$pwg->js()->reload();
                  $js[]=$gwg->js()->reload();
                  $js[]=$form->js()->univ()->successMessage('Signup Successful');
                  $this->js(null,$js)->execute();
                  break;
                case 'cancel':
                  $scrimmage->cancelSignup($player->get('id'), $form->get('position'));
                  $js[]=$ppg->js()->reload();
                  $js[]=$gpg->js()->reload();
                  $js[]=$pwg->js()->reload();
                  $js[]=$gwg->js()->reload();
                  $js[]=$form->js()->univ()->successMessage('Signup Canceled');
                  $this->js(null,$js)->execute();
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
    } 
  }
  function defaultTemplate(){
    return array('page/signup');
  }
}
