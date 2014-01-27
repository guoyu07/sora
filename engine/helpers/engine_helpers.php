<?php
  
  // http://stackoverflow.com/a/1252717/1825792
  function replace_first($find, $replace, $subject)
  {
    return implode($replace, explode($find, $subject, 2));
  }

?>