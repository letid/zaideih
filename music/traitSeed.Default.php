<?php
namespace app\music
{
  trait traitSeed_Default
  {
    private function seedTrackRow($rows,$Ids=null)
    {
      if ($Ids) {
        $rows = array_filter($rows, function($e) use($Ids) {
            if (is_array($Ids)) {
              return !in_array($e['ID'], $Ids);
            } else {
              return $Ids != $e['ID'];
            }
        });
      }
      return array_map(array($this, 'seedTrackRow_temp'), $rows);
      // return array_map(
      //   function ($raw) {
      //     return $this->seedTracksRow_temp($raw);
      //   }, $rows
      // );
    }
    private function seedTrackRow_temp($raw)
    {
      // print_r($row);
      // $raw = array();
      // $v['urlTitle']=self::urlTitle(array_reverse(array_intersect_key($raw, array_flip(array('ALBUM','ARTIST','TITLE')))));
      // $v['urlArtist']=self::urlArtist(array_intersect_key($raw, array_flip(array('ARTIST'))));
        $raw['rawTitle']=self::rowTitle($raw);
        $raw['rawArtist']=self::rowArtist($raw);
        return $raw;
    }
    private function seedTrackDetail($rows)
    {
      $raw = $rows[0];
      $raw['rawTitle']=self::rowTitle($raw);
      $raw['rawAlbum']=self::rowsAlbum($rows);
      $raw['rawArtist']=self::rowsArtist($rows);
      $raw['rawYear']=self::rowsYear($rows);
      $raw['rawGenre']=self::rowsGenre($rows);
      // $raw['totalPlays']=self::rowsPlays($rows);
      // $raw['totalLength']=self::rowsLength($rows);
      return $raw;
    }
    private function seedAlbumRow($rows)
    {
      $raw = $rows[0];
      $raw['rawAlbum']=self::rowsAlbum($rows);
      $raw['rawArtist']=self::rowsArtist($rows);
      $raw['rawYear']=self::rowsYear($rows);
      $raw['rawGenre']=self::rowsGenre($rows);

      $raw['totalPlays']=self::rowsPlays($rows);
      $raw['totalLength']=self::rowsLength($rows);
      return $raw;
    }
    private function seedAlbumDetail($rows)
    {
      $raw = $rows[0];
      $db = $this->queriesAlbum($raw['UNIQUEID']);
      if ($db->rowsCount) {
        $db->fetchAll();
        $raw['rawAlbum']=self::rowsAlbum($db->rows);
        // NOTE: related artists
        // $raw['rawArtist']=self::rowsArtist($db->rows);
        $artists = self::wildArtist($db->rows);
        // $this->artistsRelated = self::uniqueArtist(array_merge($artists,$this->artistsRelated));
        $artistsRelated = self::uniqueArtist(array_merge($artists,$this->artistsRelated));
        $this->artistsRelated = array_diff($artistsRelated,$this->artistsLike);
        $raw['rawArtist']=self::linkArtist($artists);

        $raw['rawYear']=self::rowsYear($db->rows);
        $raw['rawGenre']=self::rowsGenre($db->rows);
        if (!isset($raw['totalTracks']))$raw['totalTracks']=$db->rowsCount;//$db->rows[0]['totalTracks'];
        if (!isset($raw['totalPlays']))$raw['totalPlays']=self::rowsPlays($db->rows);
        $raw['totalLength']=self::rowsLength($db->rows);
      }
      return $raw;
    }
    private function seedArtistRow($rows)
    {
      $raw = $rows[0];
      $raw['rawArtist']=self::rowsArtist($rows);
      return $raw;
    }
    private function seedArtistDetail($rows,$vel)
    {
      $raw = $rows[0];
      $raw['ARTIST']=$vel['ARTIST'];
      $raw['rawYear']=self::rowsYear($rows);
      $raw['rawGenre']=self::rowsGenre($rows);
      $raw['totalPlays']=self::rowsPlays($rows);
      $raw['totalLength']=self::rowsLength($rows);
      return $raw;
    }
  }
}