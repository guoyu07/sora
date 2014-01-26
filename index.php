<?php
  
  // Some important paths
  define(ROOT_PATH,   dirname(__FILE__));
  define(API_PATH,    ROOT_PATH . '/api');
  define(APP_PATH,    ROOT_PATH . '/app');
  define(CONFIG_PATH, ROOT_PATH . '/config');
  define(ENGINE_PATH, ROOT_PATH . '/engine');

  // Set up params
  $GLOBALS['_params'] = array();

  // Bootstrap the application
  require(ENGINE_PATH . '/application.php');

?>
