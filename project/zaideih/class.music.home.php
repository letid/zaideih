<?php
class search extends music
{
	public function track($i)
	{
		$lg=($g=$this->total_group and $g > 4)?$g-rand(1, 4):($g > 3)?$g-rand(1, 3):2;
		foreach($i as $e):
			$fg +=1;
			self::$data['tracks.list']=NULL;
			$artists=NULL;
			$www_album 				= rawurlencode($e[0]['ALBUM']);
			$e[0]['www_album'] 		= self::$data['www.album'].'/'.$www_album;
			foreach($e as $d):
				$artists .= $d['ARTIST'].',';
				$www_artist 		= rawurlencode($d['ARTIST']);
				$d['www_artist'] 	= self::$data['www.artist'].'/'.$www_artist;
				$d['www_track'] 	= self::$data['www.music'].'/'.$www_album.'/'.$www_artist.'/'.rawurlencode($d['TITLE']);
				parent::getFavorites_summary($d['ID']);
				parent::getComments_summary($d['ID']);
				parent::ztf('tracks.list',true,$d);
			endforeach;
			if($fg == 1 or $fg == $lg)parent::ztf('tracks.list.ads.728x15','tracks.list');
			$this->album_artists_www = $this->getLink($artists,self::$data['www.artist']);
			parent::ztf('container.album.tracks',true,$e[0]);
		endforeach;
		parent::ztf('page.tracks','page.data');
		parent::getMeta($e[0],($this->q)?'qll':'all');
	}
	public function album($i)
	{
		foreach($i as $e):
			$www_album 				= rawurlencode($e[0]['ALBUM']);
			$e[0]['www_album'] 		= self::$data['www.album'].'/'.$www_album;
			parent::ztf('container.album.albums',true,$e[0]);
		endforeach;
		parent::ztf('page.albums','page.data');
		parent::getMeta($e[0],($this->q)?'qlbums':'albums');
	}
	public function artist($i)
	{
		foreach($i as $e):
			$www_artist 				= rawurlencode($e[0]['ARTIST']);
			$e[0]['www_artist'] 		= self::$data['www.artist'].'/'.$www_artist;
			parent::ztf('container.artist.artists',true,$e[0]);
		endforeach;
		parent::ztf('page.artists','page.data');
	}
	public function detail_track($i)
	{
		//$e=array_values($i)[0][0];
		$n=array_keys($i);
		$d=$i[$n[0]][0];
		$where=implode(' OR ',array_map(function($v){return sprintf("t.ARTIST LIKE '%%%s%%'",addslashes(trim($v)));}, explode(',',$d['ARTIST'])));
		$query="SELECT
			t.*,
			COUNT(c.ID) as ct,
			IF(f.USER=$this->userid, f.STAR, 0) AS fc, COUNT(f.STAR) AS ft
				FROM $this->db_track AS t
					LEFT JOIN $this->db_comment AS c ON c.CODE = t.ID
					LEFT JOIN $this->db_favorite AS f ON f.TRACK = t.ID
						WHERE $where GROUP BY t.ID ORDER BY t.YEAR, t.PLAYS DESC";

		parent::getArtisttrack($d,$query);
		//echo $this->artist_tracks_total;
		if($this->artist_tracks_total > 9 && $this->artist_albums_total > 1){
			parent::ztf('menu.artist.found');
			parent::ztf('menu.track.ads.300x250');
		}
		if($this->artist_tracks_total > 9){
			parent::ztf('menu.track.ads.300x250','menu.track.ads.300x250.2');
		}
		parent::ztf('tracks.list.ads.468x15');
		$d['ARTIST']=get::sentence($this->isArray($d['ARTIST']));
		parent::ztf('page.track','page.data',$d);
		parent::getMeta($d,'track');
	}
	public function detail_album($i)
	{
		//$e=array_values($i)[0][0];
		//self:$data['ads.930x180']='ok';
		//parent::$init['ads']['930x180']='asdfadf';
		/*
		*/
		//self::$Template['page.board'] = 'abcdef.board';
		//self::$data['page.board'] = 'abcdef.board';
		//self::$Template['page.board'] = NULL;
		//self::$data['page.content'] = 'abcdef.board';
		$n=array_keys($i);
		$d=$i[$n[0]][0];

		$search = addslashes($d['ALBUM']);
		$query="SELECT t.*, COUNT(c.ID) as ct,
			IF(f.USER=$this->userid, f.STAR, 0) AS fc, COUNT(f.STAR) AS ft
				FROM $this->db_track AS t
					LEFT JOIN $this->db_comment AS c ON c.CODE = t.ID
					LEFT JOIN $this->db_favorite AS f ON f.TRACK = t.ID
						WHERE t.ALBUM LIKE '$search' GROUP BY t.ID ORDER BY t.YEAR, t.PLAYS DESC";
		parent::getArtisttrack($d,$query,true,false);
		parent::ztf('page.album','page.data',$d);
		parent::getMeta($d,'album');
	}
	public function detail_artist($i)
	{
		//$e=array_values($i)[0][0];
		$n=array_keys($i);
		$d=$i[$n[0]][0];
		$where=implode(' OR ',array_map(function($v){return sprintf("t.ARTIST LIKE '%%%s%%'",addslashes(trim($v)));}, explode(',',$d['ARTIST'])));
		$query="SELECT t.*, COUNT(c.ID) as ct,
			IF(f.USER=$this->userid, f.STAR, 0) AS fc, COUNT(f.STAR) AS ft
				FROM $this->db_track AS t
					LEFT JOIN $this->db_comment AS c ON c.CODE = t.ID
					LEFT JOIN $this->db_favorite AS f ON f.TRACK = t.ID
						WHERE $where GROUP BY t.ID ORDER BY t.YEAR, t.PLAYS DESC";

		parent::getArtisttrack($d,$query,false);
		if($this->artist_tracks_total > 9 && $this->artist_albums_total > 1 || $this->artist_albums_total > 3){
			//parent::ztf('menu.artist.found',array('key'=>true,'page'=>true));
			parent::ztf('menu.track.ads.300x250',true);
			if($this->artist_tracks_total > 25)parent::ztf('menu.track.ads.300x250','menu.track.adsense');
		}
		if($this->artist_albums_total > 1){
			//parent::ztf('tracks.list.ads.468x15',array('key'=>true,'page'=>true));

		}
		parent::ztf('page.artist','page.data',$d);
		parent::getMeta($d,'artist');
	}
	public function suggestion($q)
	{
		$s=new sql($q,'fetch_array');
		if ($s->total):
			$artists=NULL; $albums=NULL;
			foreach($s->rows as $d):
				$artists .= $d['ARTIST'].",";
				$albums .= $d['ALBUM'].",";
				$title[] = $this->link($d['TITLE'],'music?laid='.$d['ID']);
			endforeach;
			$sug['sqs']['::title'] = new html('li',array('text'=>get::sentence($title),
					'attr'=> array(
						'class'=>'titles'
					)
				));
			$sug['sqs']['::title'] = new html('li',array('text'=>$this->getLink($artists,self::$data['www.artist'],'&'),
					'attr'=> array(
						'class'=>'artists'
					)
				));
			$sug['sqs']['::title'] = new html('li',array('text'=>$this->getLink($albums,self::$data['www.album'],'&'),
					'attr'=> array(
						'class'=>'albums'
					)
				));
			/*
			$li = new html("li");
			$sug['sqs']['::title'] = $li->innerHTML(get::sentence($title))->attributes(array('class'=>'titles'))->output();
			$sug['sqs']['::artist'] = $li->innerHTML($this->getLink($artists,self::$data['www.artist'],'&'))->attributes(array('class'=>'artists'))->output();
			$sug['sqs']['::album'] = $li->innerHTML($this->getLink($albums,self::$data['www.album'],'&'))->attributes(array('class'=>'albums'))->output();
			*/
			$this->get_list_objecting($sug,'selected','::'.parent::$data['qn']);
			$this->match_count = $s->total;
			$qp='suggestion';
		else:
			$qp='noresult';
		endif;
		parent::ztf("page.$qp",'page.data',$d);
		parent::getMeta($d,$qp);
	}
}