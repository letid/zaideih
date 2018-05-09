<?php
namespace app\music
{
  trait traitQuery
  {
    private function queriesInitiate()
    {

      $this->genre_rd = rawurldecode($this->genre);
      $this->alid_rd	= rawurldecode($this->alid);
      $this->laid_rd	= rawurldecode($this->laid);

      $this->k1rd 	= rawurldecode($this->k1);
      $this->k2rd 	= rawurldecode($this->k2);
      $this->k3rd 	= rawurldecode($this->k3);
      $this->k4rd 	= rawurldecode($this->k4);

      if ($this->k4) {
        /*
        music/itna ngaih/thawn kham/zogam
        track
        */
        $q = $this->k4rd;
        self::$qNs = self::$qNr[2];
      } elseif($this->k3) {
        /*
        music/itna ngaih/thawn kham/
        artist
        */
        $q = $this->k3rd;
        self::$qNs = in_array($this->k2, self::$qNr)?$this->k2:self::$qNr[1];
      } elseif(in_array($this->k2, self::$qNr)) {
        /*
        music/album
        music/artist
        */
        $q = $this->k3rd;
        self::$qNs = $this->k2;
      } elseif(in_array($this->k1, self::$qNr)) {
        /*
        album
        artist
        */
        $q = $this->k2rd;
        self::$qNs = $this->k1;
      } else {
        $q = $this->k2rd;
        self::$qNs = ($this->k2)?self::$qNr[0]:'all';
        // self::$qNs = in_array($this->k2, self::$qNr)?$this->k2:self::$qNr[2];
      }
      $this->q = ($q)?$q:rawurldecode($this->q);
      // $this->q = trim(preg_replace('/\s+/', ' ',$this->q));
      $this->queriesWatch();
    }
    private function queriesIDSearch($sqlWhere,$name)
    {
      // $sqlWhere = array_unique(array_filter(explode(',',$sqlWhere)));
      // explode(',',implode(',OR,',$sqlWhere))
      $sqlWhere = array_unique(array_filter(explode(',',$sqlWhere)));
      if ($sqlWhere){
        return array_map(
          function ($v) use($name){
            return ($v =='OR')?$v:array($name=>$v);
          }, explode(',',implode(',OR,',$sqlWhere))
        );
      }
    }
    private function queriesWatch()
    {
      $sqlWhere = array(); $q=null;
      if($this->q) {
        // $q = addslashes($this->q);
        $q = $this->q;
        if (self::$qNs == self::$qNr[2]) {
          $sqlWhere = array('t.TITLE',"%$q%");
        } else if(self::$qNs == self::$qNr[1]) {
          $sqlWhere=array(
            // array('t.ARTIST',$q)
            array('t.ARTIST',$q),'OR',
            array('t.ARTIST',"$q%"),'OR',
            array('t.ARTIST',"%$q%")
          );
        } else if(self::$qNs == self::$qNr[0]) {
          // echo 'abc';
          $sqlWhere=array(
            array('t.UNIQUEID',$q),'OR', array('t.ALBUM',"%$q%")
          );
        } else {
          $sqlWhere=array(
            array('t.TITLE',"%$q%"), 'OR',
            array('t.ARTIST',"%$q%"), 'OR',
            array('t.ALBUM',"%$q%"), 'OR',
            array('t.GENRE',"%$q%"), 'OR',
            array('t.COMMENT',"%$q%")
          );
        }
      }
      if($this->genre_rd){
        $sqlWhere[]=array(
          't.GENRE'=>$this->genre_rd.'%'
        );
      }
      if($this->year){
        $sqlWhere[]=array(
          't.YEAR'=>$this->year
        );
      }
      // if($this->comment);
      // if($this->lyric);

      if($this->lang and $langId=array_search(strtolower($this->lang),avail::configuration('trackLanguage')->own())){
        $sqlWhere[]=array('t.LANG'=>$langId);
      }
      if($this->alid) {
        $sqlWhere[]=self::queriesIDSearch($this->alid,'t.UNIQUEID');
      }
      if($this->laid) {
        $sqlWhere[]=self::queriesIDSearch($this->laid,'t.ID');
      }
      $TableTrackAsT=$this->table_track.' AS t';
      $TableLyricAsT=$this->table_lyric.' AS l';
      switch (self::$qNs) {
        case 'track':
          self::$query['track']['w']=array(
            array(array('t.UNIQUEID',$this->k2rd),'OR', array('t.ALBUM',$this->k2rd)), array('t.TITLE',"$q%"),array('t.ARTIST',"%$this->k3rd%")
          );
          self::$query['track']['t']=$TableTrackAsT;
          break;
        case 'album':
          if($q) {
            // echo 'what';
            self::$query['album']['w']=array(
              array('t.UNIQUEID',$q),'OR', array('t.ALBUM',"%$q")
            );
            self::$query['album']['o']="CASE WHEN t.ALBUM='$q' THEN 0 WHEN t.ALBUM LIKE '$q%' THEN 1 WHEN t.ALBUM LIKE '%$q' THEN 2 WHEN t.ALBUM LIKE '%$q%' THEN 3 ELSE 4 END, t.ALBUM ASC";
          } else {
            self::$query['album']['o']="totalPlays DESC, t.ALBUM ASC";
          }
          // self::$query['album']['r']=1;
          self::$query['album']['g']='t.UNIQUEID';
          self::$query['album']['t']=$TableTrackAsT;
          self::$query['album']['s']='t.*,SUM(t.PLAYS) as totalPlays,COUNT(t.ID) as totalTracks';
          break;
        case 'artist':
          if($q) {
            self::$query['artist']['w']=$sqlWhere;
            self::$query['artist']['o']="CASE WHEN t.ARTIST='$q' THEN 0 WHEN t.ARTIST LIKE '$q%' THEN 1 WHEN t.ARTIST LIKE '%$q' THEN 2 WHEN t.ARTIST LIKE '%$q%' THEN 3 ELSE 4 END, t.ARTIST ASC, totalPlays DESC";
            self::$query['artist']['r']=1;
          } else {
            self::$query['artist']['o']="totalPlays DESC";
          }
          self::$query['artist']['s']='t.*,SUM(t.PLAYS) as totalPlays,COUNT(t.ID) as totalTracks';
          self::$query['artist']['g']='t.ARTIST';
          self::$query['artist']['t']=$TableTrackAsT;
          break;
        default:
          if($sqlWhere) {
            if($q){
              $sqlWhere[]='OR';
              $sqlWhere[]=array(
                array('l.TITLE',$q), 'OR', array('l.ARTIST',$q), 'OR', array('l.LYRIC',"%$q%")
              );
            }
            self::$query['all']['w']=$sqlWhere;
            self::$query['all']['t']="$TableTrackAsT LEFT JOIN $TableLyricAsT ON l.TRACK = t.ID";
          } else {
            self::$query['all']['t']=$TableTrackAsT;
          }
          self::$query['all']['o']="t.PLAYS DESC";
      }
    }
    private function queriesSuggestion()
    {
      if ($this->q):
        // self::$data['q']=$this->q;
        $q=$this->q;
      elseif ($this->genre):
        // self::$data['q']=$this->genre_rd;
        $q=$this->genre;
      else:
        if ($this->k4) $q=$this->k4;
          elseif($this->genre) $q=$this->genre;
          elseif($this->alid) $q=$this->alid;
          elseif($this->laid) $q=$this->laid;
            else $q=$this->q;
      endif;
      $q = explode(' ',addslashes($q));
      $r = array_filter($q);
      //$rs = implode(").*')+(ALBUM REGEXP '.*(",$r);
      $rw = implode('|',$r);
      if (self::$qNs == self::$qNr[0]) {
        avail::content('match_in')->set(avail::arrays(array('Album'))->to_sentence());
        return "SELECT (ALBUM REGEXP '.*(".implode(").*')+(ALBUM REGEXP '.*(",$r).").*') as _matches, l.* FROM zd_track AS l WHERE ALBUM REGEXP '.*($rw).*' GROUP BY UNIQUEID ORDER BY _matches DESC LIMIT 12 OFFSET 0";
      } else if(self::$qNs== self::$qNr[1]) {
        avail::content('match_in')->set(avail::arrays(array('Artist'))->to_sentence());
        return "SELECT (ARTIST REGEXP '.*(".implode(").*')+(ARTIST REGEXP '.*(",$r).").*') as _matches, l.* FROM zd_track AS l WHERE ARTIST REGEXP '.*($rw).*' GROUP BY ARTIST ORDER BY _matches DESC LIMIT 13 OFFSET 0";
      } else if(self::$qNs == self::$qNr[2]) {
        avail::content('match_in')->set(avail::arrays(array('Track'))->to_sentence());
        return "SELECT (TITLE REGEXP '.*(".implode(").*')+(TITLE REGEXP '.*(",$r).").*') as _matches, l.* FROM zd_track AS l WHERE TITLE REGEXP '.*($rw).*' ORDER BY _matches DESC LIMIT 12 OFFSET 0";
      } else {
        if($this->genre_rd) {
          avail::content('match_in')->set(avail::arrays(array('Genre'))->to_sentence());
          return "SELECT (GENRE REGEXP '.*(".implode(").*')+(GENRE REGEXP '.*(",$r).").*') as _matches, l.* FROM zd_track AS l WHERE GENRE REGEXP '.*($rw).*' ORDER BY _matches DESC LIMIT 5 OFFSET 0";
        } elseif($this->alid) {
          avail::content('match_in')->set(avail::arrays(array('AlbumID'))->to_sentence());
          return "SELECT (ALBUM REGEXP '.*(".implode(").*')+(ALBUM REGEXP '.*(",$r).").*') as _matches, l.* FROM zd_track AS l WHERE ALBUM REGEXP '.*($rw).*' ORDER BY _matches DESC LIMIT 5 OFFSET 0";
        } elseif($this->laid) {
          avail::content('match_in')->set(avail::arrays(array('TrackID'))->to_sentence());
          return "SELECT (ID REGEXP '.*(".implode(").*')+(ID REGEXP '.*(",$r).").*') as _matches, l.* FROM zd_track AS l WHERE ID REGEXP '.*($rw).*' ORDER BY _matches DESC LIMIT 5 OFFSET 0";
        } else {
          avail::content('match_in')->set(avail::arrays(array('Title','Artist'))->to_sentence());
          // return "SELECT (TITLE REGEXP '.*(".implode(").*')+(TITLE REGEXP '.*(",$r).").*') as _matches, l.* FROM zd_track AS l WHERE (TITLE REGEXP '.*($rw).*') OR (ARTIST REGEXP '.*($rw).*') ORDER BY _matches DESC LIMIT 7 OFFSET 0";
          return "SELECT (TITLE REGEXP '.*(".implode(").*')+(TITLE REGEXP '.*(",$r).").*') as _matches, l.* FROM zd_track AS l WHERE (TITLE REGEXP '.*($rw).*') OR (ARTIST REGEXP '.*($rw).*') ORDER BY _matches DESC LIMIT 7 OFFSET 0";
        }
      }
    }
    private function queriesArtist($name)
    {
      return avail::$database->select()
        ->from($this->table_track)
        ->where('ARTIST','%'.$name.'%')
        ->order_by('PLAYS DESC')->execute()->rowsCount();
    }
    private function queriesAlbum($name)
    {
      return avail::$database->select()
        ->from($this->table_track)
        ->where(array(array('UNIQUEID',$name),'OR',array('ALBUM',$name)))
        ->order_by('TRACK ASC')->execute()->rowsCount();
    }
    protected function queriesResponse($d=array())
    {
      // $i=array_merge(array(
      //   't'=>self::$query[self::$qNs]['t'],
      //   's'=>self::$query[self::$qNs]['s'],
      //   'w'=>self::$query[self::$qNs]['w'],
      //   'o'=>self::$query[self::$qNs]['o'],
      //   'i'=>self::$query[self::$qNs]['i'],
      //   'rowperpage'=>self::$query[self::$qNs]['r'],
      //   'numperpage'=>11
      //   ),(array)$d
      // );
      $i = self::$query[self::$qNs];
      $result = avail::$database->rowsCalc()->from($i['t'])->where($i['w'])->group_by($i['g'])->execute()->rowsTotal();
      $this->resultTotal=$result->rowsTotal;
      $this->resultType = in_array(self::$qNs, self::$qNr)?self::$qNs:self::$qNr[2];
      avail::content('resultTotal')->set($result->rowsTotal);
      avail::content('resultType')->set($this->resultType);
      avail::content('q')->set($this->q);

      if ($result->rowsTotal) {
        if ($this->q) {
          $this->PageDescription = avail::language('Found match for')->get();
        } else {
          $this->PageDescription = avail::language('Found match')->get();
        }
        $p = new \app\component\pagination($result->rowsTotal,$i['r']);
        $p->page();
        $p->navigator();
        self::$db = avail::$database->select($i['s'])->from($i['t'])->where($i['w'])->group_by($i['g'])->order_by($i['o'])->limit($i['r'])->offset($p->currentOffset)->execute()->rowsCount();
        return self::$db->rowsCount;
      }
    }
  }
}