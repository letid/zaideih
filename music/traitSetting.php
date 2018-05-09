<?php
namespace app\music
{
  trait traitSetting
  {
    static private $qNr = array('album','artist','track');
    static private $qNs;
    static private $qNa;
    static private $db;
    static private $query = array(
      'all'=>array(
        's'=>'t.*', 't'=>'', 'w'=>'', 'g'=>'', 'o'=>'', 'r'=>19, 'i'=>true
      ),
      'track'=>array(
        's'=>'t.*', 't'=>'', 'w'=>'', 'g'=>'', 'o'=>'', 'r'=>27, 'i'=>true
      ),
      'album'=>array(
        's'=>'t.*', 't'=>'', 'w'=>'', 'g'=>'', 'o'=>'', 'r'=>12, 'i'=>false
      ),
      'artist'=>array(
        's'=>'t.*', 't'=>'', 'w'=>'', 'g'=>'', 'o'=>'', 'r'=>20, 'i'=>false
      )
    );
    public $PageTitle = 'Music';
    public $PageDescription ='Zaideih Music Station';
    public $PageKeywords ='zaideih, music, station';
    public $PageId = 'music';
    public $PageClass =  'music';
    public $PageContent = 'under-construction';

    private $artistsRelated = array();
    private $artistsLike = array();
    /*
      [ID] => 2389
      [PATH] => Ngaih Cim Lo.mp3
      [UNIQUEID] => db23ab53bbbceb1131b6123236e2cb38
      [TITLE] => Ngaih Cim Lo
      [ARTIST] => Tua Mung
      [ALBUM] => Khitui Gospel
      [TRACK] => 10
      [YEAR] => 1990
      [GENRE] => Gospel
      [TYPE] => 0
      [LENGTH] => 3:13
      [PLAYS] => 5300
      [COMMENT] => zaideih.com
      [LANG] => 1
      [STATUS] => 1
    */
    public function __construct($q=null)
    {
      // self::$qNs = in_array($this->get('qn'), self::$qNr)?$_GET['qn']:'all';
      // self::$info['qno'] 	= array('album','artist','track');
      // self::$data['qn'] 	= in_array($_GET['qn'], self::$info['qno'])?$_GET['qn']:'avekpi';
      // self::$data['qn.'.self::$data['qn'].'.check'] = 'checked="checked"';
      $this->table_track = $this->table('track');
      $this->table_lyric= $this->table('lyric');
      $this->table_album= $this->table('album');

      // $this->userid 	= ($uid=parent::$user['id'])?$uid:0;
      $this->q 	    = $this->get('q');
      $this->genre 	= $this->get('genre');
      $this->lang 	= $this->get('lang');
      $this->server = $this->get('server');
      $this->page 	= $this->get('page');
      $this->laid 	= $this->get('laid');
      $this->alid 	= $this->get('alid');
      $this->lyric 	= $this->get('lyric');
      $this->year 	= $this->get('year');
      $this->comment= $this->get('comment');

      $this->k1 		= $this->uri(0);
      $this->k2 		= $this->uri(1);
      $this->k3 		= $this->uri(2);
      $this->k4 		= $this->uri(3);
    }
    private function get($id,$def=null)
    {
      return isset($_GET[$id])?$_GET[$id]:$def;
    }
    private function uri($id,$def=null)
    {
      return isset(avail::$uri[$id])?avail::$uri[$id]:$def;
    }
    private function configuration($Id)
    {
      return avail::configuration($Id);
    }
    private function table($Id)
    {
      return $this->configuration('mysqlTable')->own($Id);
    }
    private function lang($Id)
    {
      return $this->configuration('trackLanguage')->own($Id);
    }
  }
}