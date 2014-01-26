<?php

  $d = ActiveRecord::$Schema->define();
  {
    $t = $d->create_table('events');
    {
      $t->string('name');
      $t->text('description');
    }
  }

?>
