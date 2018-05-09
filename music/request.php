<?php
namespace app\music
{
  // use app\avail;
  class request
  {
    // use traitSetting, traitQuery, traitEngine, traitMeaning, traitWordweb, traitMathematic, traitMoby, traitUtility, traitHtml;
    use traitSetting, traitQuery, traitSeed, traitHtml;
    static function search()
    {
      return new self();
    }
    public function definition()
    {
      $this->queriesInitiate();
      if($this->queriesResponse()) {
        $qNa = $this->resultType;
        $groups = ($qNa == self::$qNr[1])?strtoupper($qNa):'UNIQUEID';
        self::$db->fetchGroup($groups);
        if(self::$db->rowsCount == 1) {
          // NOTE: detail track/artist/album
          call_user_func_array(array($this, $qNa.'Detail'), array());
        } elseif (count(self::$db->rows) == 1) {
          // NOTE: detail album
          return $this->albumDetail();
        } else {
          // NOTE: track/artist/album
          return $this->$qNa();
        }
      } else {
        // NOTE: suggest/notfound
        return $this->suggest();
      }
    }
    private function track()
    {
      // $this->PageTitle = 'Music';
      // $this->PageDescription = 'found '.$this->totalTracks.' track';
      // $this->PageDescription = 'Found match';
      // $this->PageDescription = avail::language('found {resultTotal} {resultType} for {q}.')->get();
      // $this->PageDescription = avail::language('found {resultTotal} {resultType}.')->get();
      // $this->PageKeywords='zaideih, music, station';
      $this->PageId = 'track';
      // $this->PageClass =  'music';

      $this->PageContent = array(
        'music/tracks'=>array_map(function($v){
          return array(
            'music/container.album'=>array(
              'music/container.album.row'=>$this->seedAlbumRow($v)
            ),
            'music/container.title'=>array(
              'music/container.title.row'=>$this->seedTrackRow($v)
            )
          );
        },self::$db->rows,array())
      );
    }
    private function artist()
    {
      $this->PageTitle = 'Artists';
      $this->PageContent = array(
        'music/artists'=>array(
          'music/container.artist'=>array(
            'music/container.artist.row'=>array_map(function($v){
              return $this->seedArtistRow($v);
            },self::$db->rows,array())
          )
        )
      );
    }
    private function album()
    {
      $this->PageTitle = 'Albums';
      $this->PageContent = array(
        'music/albums'=>array(
          'music/container.album'=>array(
            'music/container.album.row'=>array_map(function($v){
              return $this->seedAlbumDetail($v);
              // return $this->seedAlbumRow($v);
            },self::$db->rows,array())
          )
        )
      );
    }
    private function suggest()
    {
      // $this->PageContent = 'suggestion is under-construction!';
      $suggestion= array();

      $query = $this->queriesSuggestion();
      $db = avail::$database->query($query)->execute()->rowsCount();
      if ($db->rowsCount) {
        $db->fetchAll();
        $this->qMatch = explode(' ', $this->q);

        // $artists = self::wildArtist($db->rows);
        $artists = array_filter(self::wildArtist($db->rows), function($k) {
          return array_filter($this->qMatch, function($e) use($k){
            return strripos($k,$e) !== false;
          });
        });
        if ($artists) {
          // echo self::linkArtist($artists);
          $suggestion['music/suggest.artist']=array(
            'rawArtist'=>self::linkArtist($artists)
          );
        }
        // $albums = self::wildAlbum($db->rows);
        $albums = array_filter(self::wildAlbum($db->rows), function($k) {
          return array_filter($this->qMatch, function($e) use($k){
            return strripos($k,$e) !== false;
          });
        });
        if ($albums) {
          $suggestion['music/suggest.album']=array(
            'rawAlbum'=>self::linkAlbum($albums)
          );
        }

        // $titles = self::wildTitle($db->rows);
        $titles = array_filter(self::wildTitle($db->rows), function($k) {
          return array_filter($this->qMatch, function($e) use($k){
            return strripos($k['TITLE'],$e) !== false;
          });
        });
        if ($titles) {
          $suggestion['music/suggest.title']= array(
            'rawTitle'=>self::linkTitle($titles)
          );
        }
        $this->PageContent = array(
          'music/suggest'=>array(
            'music/suggest.related'=>$suggestion
          )
        );
      } else {
        $this->PageContent = array(
          'music/suggest'=>$suggestion
        );
      }
    }
    private function trackDetail()
    {
      $contents = array();
      $rawMain = array_reduce(self::$db->rows,'array_merge',array());
      $rowMain = $rawMain[0];

      $this->PageTitle = avail::language('Track title')->get($rowMain);
      $this->PageDescription = avail::language('Track description')->get($rowMain);

      $rowRemoveId = $rowMain['ID'];
      $dbArtist = $this->queriesArtist($rowMain['ARTIST']);
      if ($dbArtist->rowsCount) {
        $dbArtist->fetchGroup('UNIQUEID');
        $totalAlbum = count($dbArtist->rows);
        $rowTrack = self::wildReduce($dbArtist->rows);
        // NOTE: you may like artists
        $this->artistsLike = self::wildArtist($rowTrack);
        $this->PageKeywords = implode(',',$this->artistsLike);
      }
      // $contents['music/detail.track']=$this->seedTrackDetail(self::$db->rows[$rowMainUniqueid]);
      $contents['music/detail.track']=$this->seedTrackDetail($rawMain);
      if ($dbArtist->rowsCount > 1) {
        $contents['music/container.title']=array(
          // 'music/container.title.row'=>$this->seedTrackRow($rowTrack,$rowRemoveId)
          'music/container.title.row'=>$this->seedTrackRow($rowTrack)
        );
      }
      $contents['music/container.album']=array(
        'music/container.album.row'=>array_map(function($v){
          return $this->seedAlbumDetail($v);
          // return $this->seedAlbumRow($v);
        },$dbArtist->rows,array())
      );
      $this->artistsLike = array_diff($this->artistsLike,self::uniqueArtist(array($rowMain['ARTIST'])));
      if ($this->artistsLike) {
        // NOTE: you may like artists
        $contents['music/container.artist.like']=array(
          'classArtist'=>'maylike',
          'nameArtist'=>'You may like',
          'rawArtist'=>self::linkArtist($this->artistsLike)
        );
      }
      if ($this->artistsRelated) {
        // NOTE: related artists, data generated from seedAlbumDetail()
        $contents['music/container.artist.related']=array(
          'classArtist'=>'related',
          'nameArtist'=>'Related artists',
          'rawArtist'=>self::linkArtist($this->artistsRelated)
        );
      }
      $this->PageContent=array(
        'music/track'=>$contents
      );
    }
    private function albumDetail()
    {
      $contents = array();
      $rowMain = array_reduce(self::$db->rows,'array_merge',array())[0];

      $dbAlbum =  $this->queriesAlbum($rowMain['UNIQUEID']);
      $dbAlbum->fetchAll();

      $this->PageTitle = avail::language('Album title')->get($rowMain);
      $this->PageDescription = avail::language('Album description')->get($rowMain);
      $this->PageKeywords = implode(',',self::wildArtist($dbAlbum->rows));

      $contents['music/detail.album']=$this->seedAlbumRow($dbAlbum->rows);
      $contents['music/container.title']=array(
        'music/container.title.row'=>$this->seedTrackRow($dbAlbum->rows)
      );
      $this->PageContent=array(
        'music/album'=>$contents
      );
    }
    private function artistDetail()
    {
      $contents = array();
      $rowMainWild = array_reduce(self::$db->rows,'array_merge',array());
      $rowMain = $rowMainWild[0];

      $rowMainArtist = self::wildArtist($rowMainWild);
      if (count($rowMainArtist)>1){
        $rowMainArtistTest = avail::arrays($this->q)->search_value($rowMainArtist);
        if ($rowMainArtistTest->result) {
          // TODO: if rowMainArtistTest->result empty then Artist name have to look up manually in the table
          $rowMain['ARTIST'] = implode($rowMainArtistTest->result);
        }
      }
      $dbArtist = $this->queriesArtist($rowMain['ARTIST']);
      if ($dbArtist->rowsCount) {
        $dbArtist->fetchGroup('UNIQUEID');
        $totalAlbum = count($dbArtist->rows);
        $rowTrack = self::wildReduce($dbArtist->rows);
        // NOTE: you may like artists
        $this->artistsLike = self::wildArtist($rowTrack);
      }

      $this->PageTitle = avail::language('Artist title')->get($rowMain);
      $this->PageDescription = avail::language('Artist description')->get($rowMain);
      $this->PageKeywords = implode(',',$this->artistsLike);


      $contents['music/detail.artist']=$this->seedArtistDetail($rowTrack,$rowMain);
      $contents['music/container.title']=array(
        'music/container.title.row'=>$this->seedTrackRow($rowTrack)
      );

      $contents['music/container.album']=array(
        'music/container.album.row'=>array_map(function($v){
          return $this->seedAlbumDetail($v);
          // return $this->seedAlbumRow($v);
        },$dbArtist->rows,array())
      );

      $this->artistsLike = array_diff($this->artistsLike,self::uniqueArtist(array($rowMain['ARTIST'])));
      if ($this->artistsLike) {
        // NOTE: you may like artists
        $contents['music/container.artist.like']=array(
          'classArtist'=>'maylike',
          'nameArtist'=>'You may like',
          'rawArtist'=>self::linkArtist($this->artistsLike)
        );
      }
      if ($this->artistsRelated) {
        // NOTE: related artists, data generated from seedAlbumDetail()
        $contents['music/container.artist.related']=array(
          'classArtist'=>'related',
          'nameArtist'=>'Related artists',
          'rawArtist'=>self::linkArtist($this->artistsRelated)
        );
      }
      $this->PageContent=array(
        'music/artist'=>$contents
      );
    }
  }
}