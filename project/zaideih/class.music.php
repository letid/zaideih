<?php
class music extends zotune
{
	public function page_ini()
	{
		$this->userid 	= ($uid=parent::$user['id'])?$uid:0;
		$this->genre 	= $_GET['genre'];
		$this->lang 	= $_GET['lang'];
		$this->server 	= $_GET['server'];
		$this->page 	= $_GET['page'];
		$this->laid 	= $_GET['laid'];
		$this->alid 	= $_GET['alid'];
		$this->lyric 	= $_GET['lyric'];
		$this->year 	= $_GET['year'];
		$this->comment 	= $_GET['comment'];

		$this->genre_rd = rawurldecode($this->genre);
		//$this->alid_rd	= rawurldecode($this->alid);
		//$this->laid_rd	= rawurldecode($this->laid);

		$this->q1 		= $this->uri[0];
		$this->q2 		= $this->uri[1];
		$this->q3 		= $this->uri[2];
		$this->q4 		= $this->uri[3];


		$this->q1rd 	= rawurldecode($this->q1);
		$this->q2rd 	= rawurldecode($this->q2);
		$this->q3rd 	= rawurldecode($this->q3);
		$this->q4rd 	= rawurldecode($this->q4);
		$this->sql=array(
			'all'=>array(
				's'=>'t.*', 't'=>NULL, 'w'=>NULL, 'o'=>NULL, 'r'=>19, 'i'=>true
			),
			'track'=>array(
				's'=>'t.*', 't'=>NULL, 'w'=>NULL, 'o'=>NULL, 'r'=>27, 'i'=>true
			),
			'album'=>array(
				's'=>'t.*', 't'=>NULL, 'w'=>NULL, 'o'=>NULL, 'r'=>12, 'i'=>false
			),
			'artist'=>array(
				's'=>'t.*', 't'=>NULL, 'w'=>NULL, 'o'=>NULL, 'r'=>20, 'i'=>false
			)
		);
		if($this->q4){
			/*
			music/itna ngaih/thawn kham/zogam
			track
			*/
			$q = $this->q4rd;
			self::$data['qn'] = self::$info['qno'][2];
			$this->qp = self::$data['qn'];
		}elseif($this->q3){
			/*
			music/itna ngaih/thawn kham/
			artist
			*/
			$q = $this->q3rd;
			self::$data['qn'] = in_array($this->q2, self::$info['qno'])?$this->q2:self::$info['qno'][1];
			$this->qp = self::$data['qn'];
		}elseif(in_array($this->q2, self::$info['qno'])){
			/*
			music/album
			music/artist
			*/
			$q = $this->q3rd;
			self::$data['qn'] = $this->q2;
			$this->qp = self::$data['qn'];
		}elseif(in_array($this->q1, self::$info['qno'])){
			/*
			album
			artist
			*/
			$q = $this->q2rd;
			self::$data['qn'] = $this->q1;
			$this->qp = self::$data['qn'];
		}else{
			$q = $this->q2rd;
			$this->qp = ($this->q2)?self::$info['qno'][0]:'all';
		}
		self::$data['q'] = ($q)?$q:rawurldecode($this->q);
		$this->q = trim(preg_replace('/\s+/', ' ',self::$data['q']));
	}
	public function query_search()
	{
		$sql = array();
		if($this->q) {
			$q = addslashes($this->q);
			if(self::$data['qn'] == self::$info['qno'][2]) {
				$sql[] = "t.TITLE LIKE '%$q%'";
			} else if(self::$data['qn'] == self::$info['qno'][1]) {
				$sql[] = "t.ARTIST LIKE '%$q%' ";
			} else if(self::$data['qn'] == self::$info['qno'][0]) {
				$sql[] = "t.ALBUM LIKE '%$q%' OR t.UNIQUEID = '$q'";
			} else {
				$sql[] = "t.TITLE LIKE '%$q%') OR (t.ARTIST LIKE '%$q%') OR (t.ALBUM LIKE '%$q%') OR (t.GENRE LIKE '%$q%') OR (t.COMMENT LIKE '%$q%'";
			}
		}
		if($this->genre_rd){
			$sql[]="t.GENRE LIKE '$this->genre_rd%'";
		}
		if($this->year){
			$sql[]="t.YEAR = '$this->year'";
		}
		if($this->comment);
		if($this->lyric);

		if($this->lang and $lang_key=array_search(strtolower($this->lang),parent::$init['tracklanguages']))$sql[]="t.LANG='$lang_key'";
		if($this->alid)$sql[]=implode(' OR ',array_map(function($v){return sprintf("t.UNIQUEID='%s'",addslashes(trim($v)));}, explode(',',$this->alid)));
		if($this->laid)$sql[]=implode(' OR ',array_map(function($v){return sprintf("t.ID='%s'",addslashes(trim($v)));}, explode(',',$this->laid)));
		$TrackAsT="$this->db_track AS t";
		switch ($this->qp) {
			case 'track':
				$q1ra = addslashes($this->q1rd);
				$q2ra = addslashes($this->q2rd);
				$q3ra = addslashes($this->q3rd);
				$q4ra = addslashes($this->q4rd);
				$this->sql['track']['w']="WHERE (t.UNIQUEID='$q2ra' OR t.ALBUM LIKE '$q2ra') AND (t.TITLE LIKE '$q%') AND (t.ARTIST LIKE '%$q3ra%')";
				$this->sql['track']['t']=$TrackAsT;
				break;
			case 'album':
				if($q):
					$this->sql['album']['w']="WHERE (t.UNIQUEID='$q') OR (t.ALBUM LIKE '$q%') GROUP BY t.UNIQUEID";
					$this->sql['album']['o']="ORDER BY CASE
												WHEN t.ALBUM='$q' THEN 0
												WHEN t.ALBUM LIKE '$q%' THEN 1
												WHEN t.ALBUM LIKE '%$q' THEN 2
												WHEN t.ALBUM LIKE '%$q%'
												THEN 3 ELSE 4 END, t.ALBUM ASC";
				else:
					$this->sql['album']['w']='GROUP BY t.UNIQUEID';
				endif;
				$this->sql['album']['t']=$TrackAsT;
				$this->sql['album']['s']='t.*,SUM(t.PLAYS) as total_plays,COUNT(t.ID) as total_tracks';
				break;
			case 'artist':
				if($q):
					$where=implode(' OR ',array_map(function($v){return sprintf("t.ARTIST LIKE '%%%s%%'",addslashes(trim($v)));}, explode(',',$q)));
					$this->sql['artist']['w']="WHERE $where GROUP BY t. ARTIST";
					$this->sql['artist']['o']="ORDER BY CASE
												WHEN t.ARTIST='$q' THEN 0
												WHEN t.ARTIST LIKE '$q%' THEN 1
												WHEN t.ARTIST LIKE '%$q%' THEN 2
												WHEN t.ARTIST LIKE '%$q' THEN 3 ELSE 4 END, t.ARTIST ASC";
					$this->sql['artist']['r']=1;
				else:
					$this->sql['artist']['s']='t.*,SUM(t.PLAYS) as total_plays,COUNT(t.ID) as total_tracks';
					$this->sql['artist']['w']='GROUP BY t.ARTIST';
				endif;
				$this->sql['artist']['t']=$TrackAsT;
				break;
			default:
				if($sql):
					$where=implode(') AND (',$sql);
					$this->sql['all']['w']="WHERE ($where) ";
					if($q)$this->sql['all']['w'].="OR (l.TITLE='$q' OR l.ARTIST='$q' OR l.LYRIC LIKE '%$q%')";
					$this->sql['all']['t']="$this->db_track AS t LEFT JOIN $this->db_lyric AS l ON l.TRACK = t.ID";
				else:
					$this->sql['all']['t']=$TrackAsT;
				endif;
				$this->sql['all']['o']="ORDER BY t.PLAYS DESC";
		}
	}
	public function query_suggestion()
	{
		if ($this->q):
			self::$data['q']=$this->q;
		elseif ($this->genre_rd):
			self::$data['q']=$this->genre_rd;
		else:
			if ($this->q4rd) self::$data['q']=$this->q4rd;
				elseif($this->genre_rd) self::$data['q']=$this->genrerd;
			 	elseif($this->alid) self::$data['q']=$this->alid;
				elseif($this->laid) self::$data['q']=$this->laid;
					else self::$data['q']=$this->q;
		endif;
		$q = explode(' ',addslashes(self::$data['q']));
		$r = array_filter($q);
		//$rs = implode(").*')+(ALBUM REGEXP '.*(",$r);
		$rw = implode('|',$r);
		if (self::$data['qn'] == self::$info['qno'][0]) {
			$this->recommended_in = get::sentence(array(Track,Artist,Genre),_or); $this->match_in = get::sentence(array(Album));
			return "SELECT (ALBUM REGEXP '.*(".implode(").*')+(ALBUM REGEXP '.*(",$r).").*') as _matches, l.* FROM zd_track AS l WHERE ALBUM REGEXP '.*($rw).*' GROUP BY UNIQUEID ORDER BY _matches DESC LIMIT 12 OFFSET 0";
		} else if(self::$data['qn']== self::$info['qno'][1]) {
			$this->recommended_in = get::sentence(array(Track,Album,Genre),_or); $this->match_in = get::sentence(array(Artist));
			return "SELECT (ARTIST REGEXP '.*(".implode(").*')+(ARTIST REGEXP '.*(",$r).").*') as _matches, l.* FROM zd_track AS l WHERE ARTIST REGEXP '.*($rw).*' GROUP BY ARTIST ORDER BY _matches DESC LIMIT 13 OFFSET 0";
		} else if(self::$data['qn'] == self::$info['qno'][2]) {
			$this->recommended_in = get::sentence(array(Artist,Album,Genre),_or); $this->match_in = get::sentence(array(Track));
			return "SELECT (TITLE REGEXP '.*(".implode(").*')+(TITLE REGEXP '.*(",$r).").*') as _matches, l.* FROM zd_track AS l WHERE TITLE REGEXP '.*($rw).*' ORDER BY _matches DESC LIMIT 12 OFFSET 0";
		} else {
			if($this->genre_rd) {
				$this->recommended_in = get::sentence(array(Title)); $this->match_in = get::sentence(array(Genre));
				return "SELECT (GENRE REGEXP '.*(".implode(").*')+(GENRE REGEXP '.*(",$r).").*') as _matches, l.* FROM zd_track AS l WHERE GENRE REGEXP '.*($rw).*' ORDER BY _matches DESC LIMIT 5 OFFSET 0";
			} elseif($this->alid) {
				$this->recommended_in = get::sentence(array(Album,Title,Artist),_or); $this->match_in = get::sentence(array(AlbumID));
				return "SELECT (ALBUM REGEXP '.*(".implode(").*')+(ALBUM REGEXP '.*(",$r).").*') as _matches, l.* FROM zd_track AS l WHERE ALBUM REGEXP '.*($rw).*' ORDER BY _matches DESC LIMIT 5 OFFSET 0";
			} elseif($this->laid) {
				$this->recommended_in = get::sentence(array(Title,Artist),_or); $this->match_in = get::sentence(array(TrackID));
				return "SELECT (ID REGEXP '.*(".implode(").*')+(ID REGEXP '.*(",$r).").*') as _matches, l.* FROM zd_track AS l WHERE ID REGEXP '.*($rw).*' ORDER BY _matches DESC LIMIT 5 OFFSET 0";
			} else {
				$this->recommended_in = get::sentence(array(Album,Genre),_or); $this->match_in = get::sentence(array(Title,Artist),_and);
				return "SELECT (TITLE REGEXP '.*(".implode(").*')+(TITLE REGEXP '.*(",$r).").*') as _matches, l.* FROM zd_track AS l WHERE (TITLE REGEXP '.*($rw).*') OR (ARTIST REGEXP '.*($rw).*') ORDER BY _matches DESC LIMIT 7 OFFSET 0";
			}
		}
	}
	public function pagination($d=array())
	{
		$i=array_merge(array(
			't'=>$this->sql[$this->qp]['t'],
			's'=>$this->sql[$this->qp]['s'],
			'w'=>$this->sql[$this->qp]['w'],
			'o'=>$this->sql[$this->qp]['o'],
			'i'=>$this->sql[$this->qp]['i'],
			'rowperpage'=>$this->sql[$this->qp]['r'],
			'numperpage'=>11
			),(array)$d
		);
		$sql=new sql(sprintf('SELECT COUNT(*) as total_rows FROM %s %s',$i['t'],$i['w']),'fetch_row');
		parent::$info['total_row']=($i['i'])?$sql->rows[0]:$sql->total;
		if (parent::$info['total_row']){
			$p=new pagination(parent::$info['total_row'],$i['rowperpage'],$i['numperpage']);
			$p->get_page();
			self::$data['page.pagination'] = $p->navigator();
			return sprintf('SELECT %s FROM %s %s %s LIMIT %s, %s',$i['s'],$i['t'],$i['w'],$i['o'],$p->sql,$p->row_per_page);
		}
	}
	public function home()
	{
		require_once('class.music.home.php');
		$page=new search();
		$page->page_ini();
		$page->query_search();
		if($q=$page->pagination()):
			if(in_array($page->qp, self::$info['qno']))$p=$page->qp; else $p=self::$info['qno'][2];
			$s=new sql($q);
			// echo $q."<br>";
			$s->fetch_assoc('UNIQUEID');
			//print_r($s->rows);
			$page->total_group=count($s->rows);
			// print_r($s);
			if($s->total == 1):
				$page->{"detail_$p"}($s->rows);
			elseif ($page->total_group == 1):
				$page->detail_album($s->rows);
			else:
				$page->$p($s->rows);
			endif;
		else:
			$page->suggestion($page->query_suggestion());
		endif;
	}
	public function getFavorites_summary($id)
	{
		$css=0; $num=0;
		$s=new sql("SELECT IF(USER=$this->userid, STAR, 0) AS u, COUNT(STAR) AS t FROM {$this->db_favorite} WHERE TRACK='$id'",'fetch_this');
		if($s->total){
			$num=$s->rows['t'];
			$css=$s->rows['u'];
		}
		$this->favorite_track_total = number_format($num,0);
		$this->favorite_track_css=parent::$init['trackfavorite'][$css];
	}
	public function getComments_summary($id)
	{
		$comments = new sql("SELECT * FROM {$this->db_comment} WHERE TRACK=$id");
		$this->comment_track_total = number_format($comments->total,0);
		$this->comment_track_css = ($this->comment_track_total)?'yes':'no';
	}
	public function getLyrics_summary($id,$detail=false)
	{
		$s = new sql("SELECT * FROM {$this->db_lyric} WHERE TRACK='$id'");
		if($s->total){
			$this->lyric_track_total = $s->total;
			$this->lyric_track_css='yes';
			$this->lyric_track_name=self::ztl('Lyric available!');
			if($detail){
				$s->fetch_array();
				foreach($s->rows as $d){
					$d['day']=date("M d, yy", strtotime($d['DATE']));
					$d['ago']=$this->ago($d['DATE']);
					self::ztf('menu.track.lyric',true,$d);
				}
			}
		}else{
			$this->lyric_track_css = 'no';
			$this->lyric_track_total = 0;
			$this->lyric_track_name=self::ztl('Lyric not available!');
			if($detail)self::ztf('menu.track.lyric.unavailable','menu.track.lyric');
		}
	}
	public function getArtisttrack($q,$query,$album=true,$detail=true)
	{
		$artist_plays=0;
		$s= new sql($query);
		$this->artist_tracks_total=$s->total;
		$s->fetch_assoc('UNIQUEID');
		$artist_tracks_current=0;$artist_tracks_limit=isset($_GET['show'])?$this->artist_tracks_total:37;
		foreach($s->rows as $uniqueid => $t){
			$this->artist_albums_total += 1;
			$www_album 				= rawurlencode($t[0]['ALBUM']);
			$t[0]['www_album'] 		= self::$data['www.album'].'/'.$www_album;
			//$artist_uniqueid[]		= $uniqueid;
			$name=isset($artist_albums[$t[0]['YEAR']])?$this->artist_albums_total+$t[0]['YEAR']:$t[0]['YEAR'];

			foreach($t as $d){
				$artist_year[] = $d['YEAR'];
				$artist_length[] = $d['LENGTH'];
				$artists .= $d['ARTIST'].',';

				$artist_plays 		+= $d['PLAYS'];
				//$this->artist_comments_total += $d['total_comments'];
				$www_artist 		= rawurlencode($d['ARTIST']);
				$d['www_artist'] 	= self::$data['www.artist'].'/'.$www_artist;
				$d['www_track'] 	= self::$data['www.music'].'/'.$www_album.'/'.$www_artist.'/'.rawurlencode($d['TITLE']);

				$d['favorite_track_total'] 	=number_format($d['ft'],0);
				$d['favorite_track_css'] 	=parent::$init['trackfavorite'][$d['fc']];

				$d['comment_track_total'] 	=number_format($d['ct'],0);
				$d['comment_track_css'] 	=($d['ct'])?'yes':'no';

				if($q['UNIQUEID'] == $d['UNIQUEID'] and $album==true){
					if($q['ID'] == $d['ID'] and $detail==true){
						$this->favorite_track_total = $d['favorite_track_total'];
						$this->favorite_track_css = $d['favorite_track_css'];

						$this->comment_track_total = $d['comment_track_total'];
						$this->comment_track_css = $d['comment_track_css'];

						$this->www_artist = $d['www_artist'];
						$this->www_track = $d['www_track'];
						self::getLyrics_summary($d['ID'],true);
					}else{
						parent::ztf('tracks.list','album.tracks',$d);
					}
				}else{
					if($artist_tracks_limit > $artist_tracks_current){
						$artist_tracks_current +=1;
						parent::ztf('tracks.list','artist.tracks',$d);
					}else{
						$artist_tracks_more[]=1;
					}
				}
			}
			//$this->album_artists_www=$this->getLink($artists,self::$data['www.artist']);
			$artist_uniqueid=$this->getAlbumartist($uniqueid);
			$artists_all[]= $artist_uniqueid;

			$this->album_artists_www=$this->getLink($artist_uniqueid,self::$data['www.artist']);
			$artist_albums[$name]=parent::ztf('container.artist.albums',array('key'=>false),$t[0]);
		}
		$this->artist_plays_total = number_format($artist_plays);

		if(isset($artist_length)){
			$this->artist_tracks_length = new counthour($artist_length);
		}
		if(isset($artist_year) and ksort($artist_year)){
			$this->artist_year = $this->linkYear(array_unique($artist_year));
		}
		if(isset($artist_tracks_more)){
			$this->artist_tracks_more_www = http_build_query(array_merge($_GET, array('show'=>'all')));
			$this->artist_tracks_more_total = count($artist_tracks_more);
			parent::ztf('tracks.list.more',true,$q);
		}
		if(isset(self::$data['artist.tracks'])){
			if(!$this->www_artist=self::$data['www.artist'].'/'.rawurlencode($q['ARTIST'])){
				//$this->ARTIST=get::sentence($this->isArray($q['ARTIST']));
				//$this->www_artist=self::$data['www.artist'].'/'.rawurlencode($q['ARTIST']);
				//$q['ARTIST']=get::sentence($this->isArray($q['ARTIST']));;
			}
			parent::ztf('container.artist.tracks',true,$q);
		}
		if(isset($artist_albums) and krsort($artist_albums)){
			self::$data['container.artist.albums']= implode($artist_albums);
		}
		/*
		if(isset($album_tracks)){
			parent::ztf('container.album.track','album_track_container',$q);
		}
		*/
		$A1=array_filter(array_unique(array_map('trim',explode(',',$q['ARTIST']))));
		$A2=array_filter(array_unique(array_map('trim',explode(',',$artists))));
		$this->album_artists=implode(',',$A2);
		$this->artist = $this->getLink($A1,self::$data['www.artist'],'&');
		$artist_encode= ($album)?$A2:$A1;
		$this->artist_encode = implode(',',array_map(function($v){
			return rawurlencode(trim($v));
			},$artist_encode
		));
		foreach($artists_all as $d)foreach($d as $f)$A3[$f]=$f;

		if($artist_friend = array_diff($A2, $A1)){
			$this->artist_friend = $this->getLink($artist_friend,self::$data['www.artist'],'&');
			parent::ztf('menu.artist.friend');
			$A3i=$A2;
		}else{
			$A3i=$A1;
		}
		if($artist_related=array_diff($A3, $A3i)){
			$this->artist_related=$this->getLink($artist_related,self::$data['www.artist'],'&');
			parent::ztf('menu.artist.related');
		}
	}
	public function getAlbumartist($q)
	{
		$s=new sql("SELECT GROUP_CONCAT(ARTIST) as artists FROM {$this->db_track} WHERE UNIQUEID='$q' ORDER BY YEAR DESC",'fetch_this');
		return array_filter(array_unique(array_map('trim',explode(',',$s->rows['artists']))));
	}
	public function getMeta($d,$is=NULL)
	{
		$page=($is)?$is:$this->qp;
		self::$data['page title'] = parent::ztl("{$page} title",false,$d);
		self::$data['page keywords'] = parent::ztl("{$page} keywords",false,$d);
		self::$data['page description'] = parent::ztl("{$page} description",false,$d);
		/*
		self::$Meta['script']['jplayer'] = array('src'=>'{dir.player}jp/jquery.jplayer.min');
		self::$Meta['script']['jplaylist'] = array('src'=>'{dir.player}jp/jplayer.playlist.min');
		self::$Meta['link']['player'] = array('href'=>'{dir.player}zaideih','type'=>'text/css');
		*/
		self::$Meta['script']['jplayer'] = array('src'=>'{dir.project}player/jp/jquery.jplayer.min');
		self::$Meta['script']['jplaylist'] = array('src'=>'{dir.project}player/jp/jplayer.playlist.min');
		self::$Meta['link']['player'] = array('href'=>'{dir.project}player/zaideih','type'=>'text/css');
	}
	public function getLink($d,$l,$s=NULL)
	{
		if($d = $this->isArray($d)){
			$r=array_map(function($e)use($l){
				return $this->link($e,$l.'/'.rawurlencode($e));
				},$d);
			return ($s)?get::sentence($r,$s):implode($r);
		}else{
			return $this->link('Unknown',$l.'/unknown');
		}
	}
	public function linkYear($d)
	{
		return implode(array_map(function($e){
			return $this->link($e,self::$data['www.music'].'?year='.$e,'year');
			},$this->isArray($d))
		);
	}
	public function isArray($d)
	{
		//return is_array($d)?$d:array_filter(array_unique(explode(',',$d)));
		return is_array($d)?$d:array_filter(array_unique(array_map('trim', explode(',', $d))));
		/*
		return is_array($d)?$d:array_filter(array_unique(explode(',',$d)), function($v) {
		  return trim($v);
		  //return (!empty($v) && str_word_count($val) >= 2);
		});
		*/
	}
}