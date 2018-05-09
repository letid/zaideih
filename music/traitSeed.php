<?php
namespace app\music
{
  trait traitSeed
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
      return array_map(
        function ($raw) {
          $raw['rawTitle']=self::rawTitle($raw);
          $raw['rawArtist']=self::rawArtist($raw);
          return $raw;
        }, $rows
      );
    }
    private function seedTrackDetail($rows)
    {
      $raw = $rows[0];
      $raw['rawTitle']=self::rawTitle($raw);
      $raw['rawAlbum']=self::rawAlbum($rows);
      $raw['rawArtist']=self::rawArtist($rows);
      $raw['rawYear']=self::rawYear($rows);
      $raw['rawGenre']=self::rawGenre($rows);
      return $raw;
    }
    private function seedAlbumRow($rows,$makeup=null)
    {
      $raw = $rows[0];
      $raw['rawAlbum']=self::rawAlbum($rows);

      $raw['rawYear']=self::rawYear($rows);
      $raw['rawGenre']=self::rawGenre($rows);

      $raw['totalLength']=self::rawLength($rows);

      if (!isset($raw['totalTracks']))$raw['totalTracks']=count($rows);
      if (!isset($raw['totalPlays']))$raw['totalPlays']=self::rawPlays($rows);

      if ($makeup) {
        $artists = self::wildArtist($rows);
        $raw['classAlbumType'] = self::typeAlbum(count($artists));
        // $this->artistsRelated = self::uniqueArtist(array_merge($artists,$this->artistsRelated));
        $artistsRelated = self::uniqueArtist(array_merge($artists,$this->artistsRelated));
        $this->artistsRelated = array_diff($artistsRelated,$this->artistsLike);
        $raw['rawArtist']=self::linkArtist($artists);
      } else {
        $raw['rawArtist']=self::rawArtist($rows);
        $raw['classAlbumType']=self::typeAlbum($rows);
      }
      return $raw;
    }
    private function seedAlbumDetail($rows)
    {
      $raw = $rows[0];
      $db = $this->queriesAlbum($raw['UNIQUEID']);
      if ($db->rowsCount) {
        $db->fetchAll();
        return $this->seedAlbumRow($db->rows,true);
      }
      return $raw;
    }
    private function seedArtistRow($rows)
    {
      $raw = $rows[0];
      $raw['rawArtist']=self::rawArtist($raw);
      return $raw;
    }
    private function seedArtistDetail($rows,$rw=array())
    {

      $raw = $rows[0];
      $raw['ARTIST']=$rw['ARTIST'];
      $raw['rawYear']=self::rawYear($rows);
      $raw['rawGenre']=self::rawGenre($rows);
      $raw['totalPlays']=self::rawPlays($rows);
      $raw['totalLength']=self::rawLength($rows);
      return $raw;
    }
  }
}