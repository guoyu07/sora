<?php
  
  use Application\Routes\Draw as draw;

  draw::root('events#index');

  draw::match('/gallery/show', array(
    'to' => 'gallery_controller#show',
    'via' => 'get'
  ));

  draw::get('/admin/gallery/show');

  draw::resources('events');
  draw::resources('gallery_photos');

?>
