<?php

class pz_sabre_dav_server extends Sabre_DAV_Server
{
  public function __construct($treeOrNode = null, $base = '/')
  {
    parent::__construct($treeOrNode);

    $uri = rtrim($_SERVER['REQUEST_URI'], '/') .'/';
    if(strpos($uri, $base) === false)
      $base = '/';
    if(strpos($uri, '/.well-known/') !== false)
      $base = '/.well-known'. $base;
    $this->setBaseUri($base);

    $authBackend = new pz_sabre_auth_backend();
    $authPlugin = new Sabre_DAV_Auth_Plugin($authBackend, 'prozer');
    $this->addPlugin($authPlugin);

    #$browser = new Sabre_DAV_Browser_Plugin();
    #$this->addPlugin($browser);
  }

  public function exec()
  {
    error_reporting(E_ALL ^ E_NOTICE);

    set_error_handler(function($errno, $errstr, $errfile, $errline ) {
      if((error_reporting() & $errno) == $errno)
        throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
      return true;
    });

    while(ob_end_clean());

    parent::exec();

    exit;
  }
}