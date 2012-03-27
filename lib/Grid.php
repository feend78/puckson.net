<?php

class Grid extends Grid_Basic {
  function format_priority($field) {
    $p = array(0=>'Guest', 1=>'Associate', 2=>'Member');
    $this->current_row[$field]=$p[$this->current_row[$field]];
  }
  function format_mbrtype($field) {
    $mt=array('Guest','Associate','Full','Student','Goalie');
    $this->current_row[$field]=$mt[$this->current_row[$field]];
  }
}