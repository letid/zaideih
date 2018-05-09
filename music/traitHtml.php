<?php
namespace app\music
{
  trait traitHtml
  {
    // NOTE: title
    private function urlTitle($row)
    {
      return '/music/'.implode('/', array_map(
          function ($v) {
              return rawurlencode(strtolower($v));
          }, array_reverse($row)
      ));
    }
    private function linkTitle($row,$sd='/')
    {
      return implode($sd, array_map(
          function ($v) {
              return sprintf('<a href="%1$s">%2$s</a>',self::urlTitle($v),$v['TITLE']);
          }, $row
      ));
    }
    private function rawTitle($raw)
    {
      // array(
      //   TITLE=>Name,
      //   ARTIST=>Name,
      //   ALBUM=>Name
      // )
      // return self::linkTitle(array(array_intersect_key($raw, array_flip(array('ALBUM','ARTIST','TITLE')))));
      return self::linkTitle(array($this->reduceTitle($raw)));
    }
    private function rowTitle($row)
    {
      // array(
      //   array(
      //     TITLE=>Name,
      //     ARTIST=>Name,
      //     ALBUM=>Name
      //   )
      // )
      return array_map(
        function ($raw) {
          return $this->rawTitle($raw);
        }, $row
      );
    }
    private function wildTitle($row)
    {
      // array(
      //   array(
      //     ARTIST=>Name
      //   )
      // )
      return array_map(function($raw){
        return $this->reduceTitle($raw);
        // return array_intersect_key($v, array_flip(array('ALBUM','ARTIST','TITLE')));
      },$row);
    }
    private function reduceTitle($raw)
    {
      return array_intersect_key($raw, array_flip(array('ALBUM','ARTIST','TITLE')));
    }

    // NOTE: ARTIST
    private function urlArtist($row)
    {
      if (is_array($row)) {
        return '/artist/'.implode('/', array_map(
            function ($v) {
                return rawurlencode(strtolower($v));
            }, $row
        ));
      } else {
        return '/artist/'.rawurlencode(strtolower(trim($row)));
      }
    }
    private function linkArtist($row,$sd=', ')
    {
      return implode($sd, array_map(
          function ($v) {
              return sprintf('<a href="%1$s">%2$s</a>',self::urlArtist($v),$v);
          }, is_array($row)?$row:array($row)
      ));
    }
    private function rawArtist($row)
    {
      // return self::linkArtist(self::wildArtist($row));
      return self::linkArtist(self::wildArtist(array($row)));
    }
    private function rowArtist($row)
    {
      return array_map(
        function ($raw) {
          return $this->rawArtist($raw);
        }, $row
      );
    }
    private function wildArtist($row)
    {
      // array(
      //   array(
      //     ARTIST=>Name
      //   )
      // )
      return self::uniqueArtist(array_column($row, 'ARTIST'));
    }
    private function uniqueArtist($row)
    {
      // array(
      //   Name
      // )
      return array_unique(
        array_filter(
          array_map('trim', explode(',',implode(',',$row
        ))), 'strlen')
      );
    }


    // NOTE: ALBUM
    private function urlAlbum($row)
    {
      if (is_array($row)) {
        return '/album/'.implode('/', array_map(
          function ($v) {
            return rawurlencode(strtolower($v));
          }, $row
        ));
      } else {
        return '/album/'.rawurlencode(strtolower($row));
      }
    }
    private function linkAlbum($row,$sd='/')
    {
      return implode($sd, array_map(
        function ($v) {
          return sprintf('<a href="%1$s">%2$s</a>',self::urlAlbum($v),$v);
        }, is_array($row)?$row:array($row)
      ));
    }
    private function rawAlbum($raw)
    {
      return self::linkAlbum(self::wildAlbum($raw));
    }
    private function rowAlbum($row)
    {
      return array_map(
        function ($raw) {
          return $this->rawAlbum($raw);
        }, $row
      );
    }
    private function wildAlbum($row)
    {
      return array_unique(array_column($row, 'ALBUM'));
    }
    private function typeAlbum($id='common')
    {
      if (is_numeric($id)) {
        if ($id > 2) {
          return 'various';
        } elseif ($id == 2){
          return 'duet';
        } else {
          return 'single';
        }
      } else {
        return $id;
      }
    }
    // NOTE: PLAYS
    private function rawPlays($row)
    {
      return array_sum(array_column($row, 'PLAYS'));
    }
    // NOTE: YEAR
    private function rawYear($row)
    {
      $years = array_unique(array_column($row, 'YEAR'));
      sort($years);
      return implode('/', array_map(
          function ($v) {
              return sprintf('<a href="?year=%1$s">%2$s</a>',rawurlencode(strtolower($v)),$v);
          }, $years
      ));
    }
    // NOTE: GENRE
    private function rawGenre($row)
    {
      return implode('/', array_map(
          function ($v) {
              return sprintf('<a href="?genre=%1$s">%2$s</a>',rawurlencode(strtolower($v)),$v);
          }, array_unique(array_column($row, 'GENRE'))
      ));
    }

    private function rawLength($row)
    {
      return new \app\component\counthour(array_column($row, 'LENGTH'));
    }
    // NOTE: common
    private function wildReduce($row)
    {
      // $row = array(
      //   array(
      //     array(
      //       key=>value
      //     )
      //   )
      // )
      return array_reduce($row,'array_merge',array());
    }
  }
}