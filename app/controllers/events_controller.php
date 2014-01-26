<?php
  
  class EventsController extends ApplicationController
  {
    function index()
    {
      $this->events = Event::all();
    }

    function show()
    {
      $this->event = Event::find($GLOBALS['_params']['id']);
    }
  }

?>
