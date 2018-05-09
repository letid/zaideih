<?php
namespace app\map;
use app;
class api extends mapController
{
  public $responseType = 'json';
  public function __construct()
  {
    app\avail::log()->counter();
  }
  /*
  api
  */
  public function home()
  {
    return array(
      app\avail::$config['name']=>app\avail::$config['version']
    );
  }
  /*
  api/post
  */
  public function post()
  {
    return app\editor\suggest::request(array_filter($_POST))->post();
  }
}
