<?php
namespace app\map;
use app;
class home extends mapController
{
  public function __construct()
  {
    $this->timeCounter = app\avail::timer();
    // app\avail::log('visits')->counter();
    app\avail::log()->counter();
  }
  public function classConcluded()
  {
    app\verso::request('page')->menu();
    app\verso::request('privacy')->menu();
    app\verso::request('user')->menu();
    app\verse::request()->menu();
    $this->timerfinish = $this->timeCounter->finish();
    // app\avail::assist()->error_get_last();
  }
  public function home()
  {
    return array(
      'layout'=>array(
        'Title'=>'Zaideih Music Station',
        'Description'=>'online Myanmar dictionaries, available in 24 languages.',
        'Keywords'=>'Myanmar dictionary, Myanmar definition, Burmese, norsk ordbok, burmissk',
        'page.id'=>'home',
        'page.class'=>'home',
        'page.content'=>array(
          'layout.bar'=>array(),
          // 'layout.header'=>array(),
          // 'layout.board'=>array(),
          'home'=>array(),
          // 'layout.footer'=>array(),
        )
      )
    );
  }
  public function music()
  {
    // return app\music\request::search()->definition();
    $music = new app\music\request;
    $music->definition();
    return array(
      'layout'=>array(
        'Title'=>$music->PageTitle,
        'Description'=>$music->PageDescription,
        'Keywords'=>$music->PageKeywords,
        'page.id'=>$music->PageId,
        'page.class'=>$music->PageClass,
        'page.content'=>array(
          'layout.bar'=>array(),
          'music/page'=>array(
            'page.music'=>$music->PageContent
          ),
          'layout.footer'=>array()
        )
      )
    );
  }
  public function about()
  {
    // app\verso::request()->requestCount();
    app\verse::request()->requestCount();
    return array(
      'layout'=>array(
        'Title'=>'About {name}, Free online Myanmar dictionaries',
        'Description'=>'{lang.name} - Myanmar dictionary.',
        'Keywords'=>'Myanmar dictionary, Burmesisk ordbok, Myanmar definition, Burmese, norsk ordbok, burmissk',
        'page.id'=>'about-us',
        'page.class'=>'about-us',
        'page.content'=>array(
          'layout.bar'=>array(),
          'aboutus'=>array(
            // 'locale.total'=>'3',
            // 'dictionaries.total'=>'24'
          ),
          'layout.footer'=>array()
        )
      )
    );
  }
  public function terms()
  {
    return array(
      'layout'=>array(
        'Title'=>'Terms',
        'Description'=>'Terms of service, User license, Content, Proprietary Rights, Fees',
        'Keywords'=>'using the {name} service',
        'page.id'=>'terms',
        'page.class'=>'terms',
        'page.content'=>array(
          'layout.bar'=>array(),
          'terms'=>array(
            'Heading'=>'Terms of service'
          ),
          'layout.footer'=>array()
        )
      )
    );
  }
  public function privacy()
  {
    return array(
      'layout'=>array(
        'Title'=>'Privacy',
        'Description'=>'Your privacy is very important to us. Accordingly, we have developed this policy in order for you to understand how we collect, use, communicate and disclose and make use of personal information. The following outlines our privacy policy.',
        'Keywords'=>'privacy, policy',
        'page.id'=>'privacy',
        'page.class'=>'privacy',
        'page.content'=>array(
          'layout.bar'=>array(),
          'privacy'=>array(
            'Heading'=>'Privacy Policy'
          ),
          'layout.footer'=>array()
        )
      )
    );
  }
}
