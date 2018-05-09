<?php
namespace app
{
  class configurationController extends \letId\request\configuration
  {
    /**
    * application's directory rewrite! src, lang,temp
    */
    protected $rewrite = array(
      'src'=>'resource'
    );
    /**
    * application's directory
    */
    protected $directory = array(
      'template'=>'template',
      'language'=>'language'
    );
    /**
    * application's setting
    */
    protected $setting = array(
    );
    protected $mysqlTable = array(
      'users'=>'zu_users',
      'visited'=>'zu_visited',
      'log'=>'zu_log',
      'beh'=>'zu_beh',
      'zogam'=>'zu_zogam',
      'countries'=>'zu_countries',
      'blog'=>'zu_blog',
      'track'=>'zd_track',
      'album'=>'zd_album',
      'comment'=>'zd_comment', 'favorite'=>'zd_favorite', 'lyric'=>'zd_lyric',
      'playlist'=>'zd_playlist', 'suggestion'=>'zd_suggestion',
      'image'=>'zd_image',
      'download'=>'ze_download', 'financial'=>'ze_financial', 'paypal'=>'ze_paypal'
    );
    protected $trackLanguage = array(
      0=>'untitle',
      1=>'zola',
      2=>'myanmar',
      3=>'mizo',
      4=>'english',
      5=>'chin',
      6=>'haka',
      7=>'falam',
      8=>'korea',
      9=>'norwegian',
      10=>'collection'
    );
  }
}
