<?php
namespace app\music
{
  trait traitHtml_Default
  {
    // NOTE: row
    // private function rowAlbum($row)
    // {
    //   // array_intersect_key($row, array_flip(array('ALBUM')))
    //   return self::rowsAlbum(array($row));
    // }
    private function rowArtist($row)
    {
      // array_intersect_key($row, array_flip(array('ARTIST'))
      return self::rowsArtist(array($row));
    }
    private function rowTitle($row)
    {
      return self::linkTitle(array(array_intersect_key($row, array_flip(array('ALBUM','ARTIST','TITLE')))));
    }
    // NOTE: rows
    private function rowsAlbum($row)
    {
      return self::linkAlbum(array_unique(array_column($row, 'ALBUM')));
    }
    private function rowsArtist($row)
    {
      return self::linkArtist(self::wildArtist($row));
    }
    private function rowsPlays($row)
    {
      return array_sum(array_column($row, 'PLAYS'));
    }
    private function rowsYear($row)
    {
      $years = array_unique(array_column($row, 'YEAR'));
      sort($years);
      return implode('/', array_map(
          function ($v) {
              return sprintf('<a href="?year=%1$s">%2$s</a>',rawurlencode(strtolower($v)),$v);
          }, $years
      ));
    }
    private function rowsGenre($row)
    {
      return implode('/', array_map(
          function ($v) {
              return sprintf('<a href="?genre=%1$s">%2$s</a>',rawurlencode(strtolower($v)),$v);
          }, array_unique(array_column($row, 'GENRE'))
      ));
    }
    private function rowsLength($row)
    {
      return new \app\component\counthour(array_column($row, 'LENGTH'));
    }
    // NOTE: url
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
    private function urlTitle($row)
    {
      return '/music/'.implode('/', array_map(
          function ($v) {
              return rawurlencode(strtolower($v));
          }, array_reverse($row)
      ));
    }
    // NOTE: link
    private function linkAlbum($row,$sd='/')
    {
      return implode($sd, array_map(
        function ($v) {
          return sprintf('<a href="%1$s">%2$s</a>',self::urlAlbum($v),$v);
        }, is_array($row)?$row:array($row)
      ));
    }
    private function linkArtist($row,$sd=', ')
    {
      return implode($sd, array_map(
          function ($v) {
              return sprintf('<a href="%1$s">%2$s</a>',self::urlArtist($v),$v);
          }, is_array($row)?$row:array($row)
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
    // NOTE: wild
    private function wildTitle($row)
    {
      // $row = array(
      //   array(
      //     ARTIST=>Name
      //   )
      // )
      return array_map(function($v){
        return array_intersect_key($v, array_flip(array('ALBUM','ARTIST','TITLE')));
      },$row);
    }
    private function wildArtist($row)
    {
      // $row = array(
      //   array(
      //     ARTIST=>Name
      //   )
      // )
      return self::uniqueArtist(array_column($row, 'ARTIST'));
    }
    private function wildAlbum($row)
    {
      return array_column($row, 'ALBUM');
    }
    private function uniqueArtist($row)
    {
      // $row = array(
      //   Name
      // )
      return array_unique(
        array_filter(
          array_map('trim', explode(',',implode(',',$row
        ))), 'strlen')
      );
    }
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