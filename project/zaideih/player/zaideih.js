/*
 * zotune
 * http://www.zaideih.com
 * Copyright (c) 2009 - 2014 ZOTUNE.developer
 * Author: Khen Solomon Lethil
 * Version: 1.6.0
 * Date: Feb 17, 2014
 */
(function($){
$.fn.zotune=function(e){
var z = z||{}, zd=$(this);
	z={
	album:{},
	player:{
		get:function(x,i){
			if(!i){
				var y=x.parents('.ID'), id=zj.Ai(y),i=id[0],u=id[1], x=y.find('.int');
				z.laid=i; z.uniqueid=u;
			}
			return {id:i,title:x.find('.title').text(),artist:x.find('.artist').text(),mp3:zj.url([api,'audio','play',i])};
		},
		add:function(x,i){
			z.album[i]=[];
			x.children('.ID').each(function(){ 
				z.album[i].push(z.player.get($(this),zj.Ai($(this))[0]));
			});
			return z.album[i];
		},
		ini:function(i){
			if(z.jP == null){
				z.playlist = i||[z.playlist];
				this.load();
			}else{
				if(i){
					z.jP.setPlaylist(i);
				}else{
					var is=$.map(z.jP.playlist,function(v){if(v.id==z.laid)return false;});
					if(is == false){
						if(z.jP.playing){
							z.jP.add(z.playlist,true);
						}else{
							z.jP.add(z.playlist);
						}
					}else{
						this.is();
					}
				}
			}
		},
		load:function(){
			var j={
				volume:(zj.store.g('volume')||$.jPlayer.prototype.options.volume),
				attr:{
					vs:'jp-volume-slider',ps:'jp-progress-slider',audio:'jp-audio',t:'jhpp',title:'jp-title',player:'jzmp-player',
					playlists:'playlists'
				}
			};
			$(e.z).prepend(z.h.player(j)).addClass(j.attr.audio);
			var jPs=$(zj.class(j.attr.ps)),jVs=$(zj.class(j.attr.vs)),jTs=$(zj.id(j.attr.t)),jPr=$(zj.id(j.attr.player));
			z.playlistOptions={
				playlistOptions:{
				  autoPlay: true, enableRemoveControls: false,
				  loopOnPrevious: true,
				  shuffleOnLoop: true,
				  displayTime: 'slow',
				  addTime: 'fast',
				  removeTime: 'fast',
				  shuffleTime: 'slow'
				},
				volume:j.volume,swfPath:urlproject+"player/jp/",supplied:"mp3",keyEnabled:true,preload:"auto",wmode:"window",smoothPlayBar:true,audioFullScreen:true,
				timeupdate:function(event){
					if(!z.ignore_timeupdate)jPs.slider("value", event.jPlayer.status.currentPercentAbsolute);
				},
				volumechange:function(event){
					//jVs.slider("value",(event.jPlayer.options.muted)?0:event.jPlayer.options.volume);
					jVs.slider("value",event.jPlayer.options.volume);
				},
				loadstart:function(event){
					z.current=z.jP.playlist[z.jP.current];
					zj.ga('music?',$.param({laid:z.current.id,title:z.current.title}));
				},
				ready:function(event){
					//$(this).jPlayer("play");
					jPs.mousemove(function(f) {
						var i=$(this),os=i.offset(),w=i.width(),pX=f.pageX,x=pX - os.left,p=100*x/w,jhp=(pX >= os.left+50)?pX-37:pX+18;
						if(x>0)jTs.fadeIn(200).text($.jPlayer.convertTime(p * jD.status.duration / 100)).offset({top:f.pageY+5,left:jhp});
					}).mouseout(function(f) {
						jTs.fadeOut(200);
					});
					$(zj.class(j.attr.title)).show();
					//$(zj.class(j.attr.playlists)).slideUp(1200);
					jTs.hide();
				}
			};
			z.jP = new jPlayerPlaylist({cssSelectorAncestor:e.z,jPlayer:zj.id(j.attr.player)},z.playlist,z.playlistOptions);
			var jD=jPr.data("jPlayer");
			jVs.slider({animate:"fast",max: 1,range:"min",step:0.01, value:j.volume, slide:function(event,ui){
					jPr.jPlayer({'muted':(ui.value)?false:true,'volume':ui.value});
					zj.store.s('volume',ui.value,0.8);
				}
			});
			jPs.slider({animate:"fast",max:100,range:"min",step:0.01,value:0, slide:function(event,ui){
					if(jD.status.seekPercent > 0){
						jPr.jPlayer("playHead", ui.value * (100 / jD.status.seekPercent));
					}else{
						setTimeout(function(){jPs.slider("value",0);},0);
					}
					jPr.jPlayer("option", "muted", true);
				}, stop:function(event,ui){jPr.jPlayer("option", "muted", false);}
			});
		},
		is:function(){
			$.each(z.jP.playlist, function (index, obj) {
				if (obj.id == z.laid){
					if(index == z.jP.current){
						if($.jPlayer.event.pause)z.jP.play();
					}else{
						z.jP.play(index);
					}
				}
			});
		}
	},
	fn:{
		xhr:function(x){if(z.xhr)$.each(z.xhr,function(k,v){x.setRequestHeader(k,v);});}
	},
	click:function(){
		zd.on(e.c,e.A, function(){
			//var x=$(this); var q={}; q.x1=x; q.r1=zj.Ad(x); q.c1=zj.Ac(x); q.i1=zj.Ai(x);
			var q={}; q.x1=$(this); q.r1=zj.Ad(q.x1); q.c1=zj.Ac(q.x1); q.i1=zj.Ai(q.x1);
			if($.isFunction(z.A[q.c1[0]])){
				z.A[q.c1[0]](q);
			}else if(z[q.c1[0]] && $.isFunction(z[q.c1[0]][0])){
				z[q.c1[0]][0](q);
			}
			return false;
		});
	},
	M:{
		0:function(q){
			ItemsMain=$('ol.items');
			ItemsAlbum=ItemsMain.children('li.album');
			ItemsMp3=ItemsMain.children('li.mp3');
			TaskMain=$('#task');
			TaskForm=TaskMain.children('form');
			q.Editor='#editor';
			q.Status='.status';
			q.Prefix='.prefix';
			if($.isFunction(this[q.c1[1]])){
				this[q.c1[1]](q);
			}
		},
		resetEditor:function(q){
			$(q.Editor).empty();
		},
		resetStatus:function(q){
			$(q.Status).empty();
		},
		createFile:function(q){
			q.url=zj.url([api,q.x1.attr('data-role')]);
			q.loading=ItemsMain;
			q.container=$(q.Editor);
			this.submit(q);
		},
		upgrade:function(q){
			q.url=zj.url([api,q.x1.attr('href')]);
			q.loading=ItemsMain;
			q.container=$(q.Editor);
			this.submit(q);
		},
		get:function(q){
			var p=q.x1.parent(), prefix=p.children(q.Prefix),page=(q.page || zj.Ac(q.x1)[2]);
			q.loading=p.children(q.Status);
			q.status=q.loading.empty();
			q.url=zj.url([api,page,(q.r1[0] || null),zj.Ah(prefix)]);
			q.async=false;
			q.container=$(q.Editor);
			this.submit(q);
		},
		oren:function(q){
			q.r1[0]=zj.Ac(q.x1.parent())[1];
			this.get($.extend(q,{page:zj.Ac(q.x1)[1]}));
		},
		orem:function(q){
			q.r1[0]=zj.Ac(q.x1.parent())[1];
			this.get($.extend(q,{page:zj.Ac(q.x1)[1]}));
		},
		meta:function(q){
			//this.get(q);
			this.get($.extend({},q,{page:zj.Ac(q.x1)[1]}));
			//this.message(q,' sssNo method found!');
		},
		mysql:function(q){
			if(TaskForm.length){
				if(TaskForm.is(':visible')){
					if(TaskMain.children().length > 1 ){
						TaskForm.nextAll().remove();
					}else{
						TaskForm.hide();
					}
				}else{
					TaskForm.show();
				}
			}else{
				z.ajax($.extend(q,{
						url:zj.url([api,q.c1[1],q.x1.attr('data-role')]),container:TaskMain,loading:TaskMain,click:'.fn'
					})).done(function(j){
					TaskMain.children(zj.id(j.fid)).on('submit', function() {
						var x=$(this), l=x.find('.msg');
						z.ajax({
							url:zj.url([api,x.attr('action')]),loading:l,status:l,msg:'....wait!.....',data:x.serialize(), type:x.attr('method')
						}).done(function(j){
							if(j.zj){
								zj.html(TaskMain,j.zj,'append');
							}
						});
					});
				});
			}
		},
		track:function(q){
			var p=q.x1.parent();
			if(p.hasClass('album')){
				z.M.get($.extend(q,{page:'mp3/album'})); 
			}else{
				z.M.get($.extend(q,{page:zj.Ac(p)[1]+'/tag'}));
			}
		},
		getAlbum:function(q){
			if (ItemsAlbum.length) {
				ItemsAlbum.each(function(index, value) {
					z.M.get($.extend(q,{x1:$(this).children('.get')}));
				});
			} else if(ItemsMp3.length){
				q.url=zj.url([api,'album',(q.r1[0] || null),'?prefix='+ItemsMain.attr('data-role')]);
				q.container=$(q.Editor);
				this.submit(q);
			}else{
				this.message(q,'Current directory has nothing to do!');
			}
		},
		getMp3:function(q){
			if(ItemsAlbum.length) {
				ItemsAlbum.each(function(index, value) {
					z.M.get($.extend(q,{x1:$(this).children('.get'), page:'mp3/album'}));   
				});
			} else if(ItemsMp3.length){
				ItemsMp3.each(function(index, value) {
					//var x1=$(this).children('.mp3');
					var x1=$(this).children('.get');
					z.M.get($.extend(q,{x1:x1, page:zj.Ac(x1)[2]})); 
				});
			}else{
				this.message(q);
			}
		},
		submit:function(q){
			z.ajax($.extend({},q,{click:'.fn'})).done(function(j){
				q.container.children(zj.id(j.fid)).on('submit', function() {
					var x=$(this), l=x.find('.msg');
					if(zj.Ac(x)[1] == 'mp3'){
						var data={data:JSON.stringify(x.serializeArray())};
					}else{
						var data=x.serialize();
					}
					z.ajax({
						url:zj.url([api,x.attr('action')]),
						loading:l,status:l,msg:'...',data:data, type:x.attr('method')
					});
				});
			});
		},
		message:function(q,msg){
			$(q.Editor).append(z.h.paragraph({text:(msg||'No directory found in this page!')}));
		}
	},
	P:{
		0:function(q){
			if(q.i1.length>1){
				z.playlist=z.player.get(q.x1);
				z.player.ini(); 
				//x1p=$(e.z).find(zj.id(n)).addClass(cur);
				q.x1=$(e.z).find(zj.id(q.i1[0]));
				q.obid=q.i1[2];
			}else{
				//z.current=z.jP.playlist[z.jP.current]; i[1]=z.current.id;
			}
			var m=$(e.z),i=zj.class(zj.Ac(m.children().first())[0]),l=$(z.h[q.i1[0]]());
			z.current=z.jP.playlist[z.jP.current];
			q.laid=z.current.id;
			q.ojid=zj.id(q.i1[0].substring(0,1)+'-'+q.laid); 
			q.container=m.children(zj.class(zj.Ac(l)[1])); 
			q.main=q.container.children(q.ojid);
			q.loading=q.x1;
			/*
			$(this).children().first().prop('tagName');
			q.zpl.children()[0].tagName
			*/
			if(q.main.length){
				q.loading.addClass(cur);
				if(q.main.is(':hidden') || q.container.is(':hidden') || q.container.index() > 1){
					q.container.insertAfter(i).slideDown();
					q.main.slideDown(300).siblings().hide(100);
				}else{
					if(q.loading.hasClass(cur))q.loading.removeClass(cur);
					if(q.container.next().length){
						q.container.hide().insertAfter(q.container.next());
					}else{
						q.container.hide();
					}
				}
			}else{
				if(q.container.length){
					q.container.insertAfter(i).slideDown(300).children().slideUp();
				}else{
					q.container=l.insertAfter(i).slideDown(300);
				}
				q.url=zj.url([api,q.i1[0],'get',q.laid,q.obid]);
				z.ajax($.extend(q,{position:'prepend',click:'.fn,[type=button]'})).done(function(j){
					q.loading.addClass(cur);
					q.container.children().on(e.c,q.click,function(){
						q.x2=$(this),q.r2=zj.Ad(q.x2),q.dl=q.x2.parents(q.container.children()[0].tagName);
						if($.isFunction(z[q.c1[0]][q.i1[0]][q.r2[0]]))z[q.c1[0]][q.i1[0]][q.r2[0]]($.extend(true,q,j));
					}).submit(function(){
						//q.url=zj.url([api,q.i1[0],'post',q.laid,q.obid]);
						if($.isFunction(z[q.c1[0]][q.i1[0]]['post']))z[q.c1[0]][q.i1[0]]['post']($.extend(true,q,j));
					});
				});
			}
		},
		comment:{
			form:function(q){
				//var abc=q.container.children().first();
				//console.log(abc.attr('id'));
				var container=q.container.children().children();
				container.eq(1).html(z.h.form[q.i1[0]](q.form));
				container.eq(0).children().hide().filter(':lt(8)').show(); 
			},
			post:function(q){
				var url=zj.url([api,q.i1[0],'post',q.laid]);
				var method=q.x.attr('method');
				var data=q.x.serialize();
				q.loading=q.x.find('.msg');
				q.status=q.loading;
				if (q.x.find('[name=name]').val() && q.x.find('[name=comment]').val()){
					z.ajax($.extend({},q,{url:url,container:null,data:data,type:method})).done(function(j){
						if(j.zj)zj.html(q.container.children().children().eq(0).empty(),j.zj).children().hide().filter(':lt(8)').show();
					});
				}else{
					q.status.removeClass('loading').html(q.empty);
				}
			},
			remove:function(q){
				var p=q.x2.parent().parent(), obid=zj.Ai(p)[1];
				var url=zj.url([api,q.i1[0],q.r2[0],q.laid,obid]);
				//var i=q.r2[2].split('-');
				z.ajax($.extend({},q,{url:url,container:null})).done(function(j){
					if(j.zj)zj.html(q.container.children().children().eq(0).empty(),j.zj).children().hide().filter(':lt(8)').show();
				});
			}
		},
		lyric:{
			yes:function(q){
				 var i=zj.Ac(q.x2.parent())[0], l=q.dl.find(zj.id(i)), f={
						title:l.find('.title').text()||z.current.title,
						artist:l.find('.artist').text()||z.current.artist,
						lyric:l.find('.lyric').html(),
						name:l.find('.name').text()||unm};
				q.obid=i.split('-')[1];
				q.dl.html(z.h.form[q.i1[0]]($.extend(q.form,f)));
			},
			no:function(q){
				q.dl.remove();
				if(q.container.is(':empty'))q.container.remove();
				q.loading.removeClass(cur);
			},
			post:function(q){
				q.loading=q.x.find('.msg');
				q.status=q.loading;
				q.url=zj.url([api,q.i1[0],'post',q.laid,q.obid]);
				//q.status.html(q.j.loading ||'...');
				z.ajax($.extend({},q,{container:null,data:q.x.serialize(),type:q.x.attr('method')})).done(function(j){
					//if(j.zj){zj.html(q.zpl,j.zj,'prepend'); x.remove(); } 
					if(j.is=='done'){
						q.x.find('[name=close]').val(j.close);
						q.x.find('[name=submit]').val(j.submit);
					}
				});
			},
			show:function(q){
				q.dl.find(zj.id(zj.Ac(q.x2.parent())[0])).fadeIn().siblings(0.1).fadeOut(0.1);
				q.x2.parent().addClass(cur).siblings().removeClass(cur);
			},
			remove:function(q){
				var i=zj.Ac(q.x2.parent())[0], obid=i.split('-')[1];
				var ul=q.dl.find(zj.id(i)), dt=ul.parent(),dd=q.x2.parent().parent(),is=ul.is(':visible');
				z.ajax($.extend({},q,{url:zj.url([api,q.i1[0],q.r2[0],q.laid,obid]),container:null})).done(function(j){
					ul.remove();
					q.x2.parent().remove();
					if(is){
						dt.children().eq(0).show().siblings().hide();
						dd.children().eq(0).addClass(cur).siblings().removeClass(cur);
					}
					if(q.dl.children().is(':empty'))z[q.c1[0]][q.i1[0]].no(q);
				});					
			}
		}
	},		
	A:{
		share:function(q){
			q.x1.toggleClass(cur);
			q.x1.parents('dl').children('dd').toggle();
		},
		TOG:function(q){
			q.x1.toggleClass(cur);
			$(q.r1[0]).toggle();
		},
		Atf:function(q){
			//Add to favorite, AFT 
			var id = zj.Ai(q.x1.parents('.ID'));
			if(uid)
				$.ajax({url:zj.url([api,'star','get',$.isNumeric(id[0])?id[0]:id[1]]), dataType:'json',
					success:function(j){q.x1.addClass(j.star).removeClass(q.c1.pop());}, error:z.com.error, 
				});
		},
		Ptt:function(q){
			//play this track
			z.playlist=z.player.get(q.x1);
			z.player.ini(); 
			q.x1.addClass('added');
		},
		Ptl:function(q,add){
			//play this list
			var ol=q.x1.parents('dl').children('dd').find('ol'),d=z.player.add(ol,zj.Ai(ol)[1]);
			if(add)d.unshift(add);
			z.player.ini(d);
		},
		Pta:function(q){
			//play this album
			z.player.get(q.x1);
			if(z.album[z.uniqueid]){
				z.player.ini(z.album[z.uniqueid]);				
			}else{
				z.xhr={ZRIS:z.h.r.al,ZRID:z.uniqueid};
				$.ajax({url:zj.url([api,'playlist','album']), dataType:'json',beforeSend:z.fn.xhr}).done(function(j){
					z.player.ini(j);
					z.album[z.uniqueid]=j;
					z.xhr={};
				});
			}
		},
		Pal:function(q){
			//play this and those
			this.Ptl(q,z.player.get(q.x1));
		},
		JPL:function(q){
			q.x1.next().slideToggle("fast");
		},
		ALT:function(q){
			/*
			q.x1.addClass(cur).siblings().removeClass(cur);
			q.x1.parents('dl').find('dd>ul').hide().siblings(zj.id(q.c1[1])).show();
			*/
			var p=q.x1.parents('dl'),id=q.c1[1],i=zj.id(id);
			q.loading=q.x1;
			q.x2=p.children(i);
			q.loading.addClass(cur).siblings().removeClass(cur);
			if(q.x2.length){
				//q.x2.show().siblings().hide();
				p.children('dd').hide().siblings(i).show();
			}else{
				q.url=zj.url([api,zj.Ah(q.x1)]);
				q.container=p.append($(zj.rt('dd'),{id:id})).children('dd').hide().siblings(i).show();
				z.com.load(q);
			}
		},
		CON:function(q){
			var p=q.x1.parent('li'), i=p.children(q.r1[0]);
			if(q.r1=='')i=p.children().eq(1);
			if(i.length){
				i.slideToggle("fast");
				p.toggleClass(cur);
			}else{
				//q.loading=p;
				//q.url=zj.url([api,zj.Ah(q.x1)]);
				z.ajax($.extend(q,{url:zj.url([api,zj.Ah(q.x1)]),loading:p,container:p,click:'.fn'})).done(function(j){
					q.container.addClass(cur);
				 	q.container.find('form').on('submit', function() {
						var x=$(this), l=x.find('.msg');
						z.ajax({
							url:zj.url([api,x.attr('action')]),
							loading:l,status:l,msg:'...', data:x.serialize(), type:x.attr('method')
						});
					});
				});
				
			}
		},
		PAN:function(q){
			var p=q.x1.parent().parent();
			z.ajax($.extend(q,{url:zj.url([api,zj.Ah(q.x1)]),container:p,position:'replaceWith',loading:q.x1.parent()}));
		},
		COP:function(q){
			if(!q.x1.hasClass(cur)){
				var url=zj.Ah(q.x1.parents('li').children('a'));
				q.x1.addClass('loading');
				$.ajax({url:zj.url([api,url,q.r1[1]]), dataType:'json'}).done(function(j){
						q.x1.addClass(cur).siblings().removeClass(cur);
					}).fail(function(jqXHR,textStatus){
						//...
					}).always(function(j){
						q.x1.removeClass('loading');
				});
			}
		}
	},
	TM:{
		0:function(q){
			var p=q.x1.parents('dd'),y=zj.class(q.c1[0]),i=zj.class(q.c1[1]),d='div'; //tag=x.prop("tagName");
			q.container=p.children(i);
			p.children('ul').find(y).not(q.x1.toggleClass(cur)).removeClass(cur);
			q.laid=zj.Ai(p)[1];
			p.children(d).not(i).slideUp('fast');
			if(q.container.length){
				q.container.slideToggle('fast');
			}else{
				q.loading=q.x1;
				q.url=zj.url([api,q.i1[0],q.i1[1],q.laid]);
				z.ajax($.extend(q,{container:$(zj.rt(d),{class:q.c1[1]}).appendTo(p)})).done(function(j){
				 	 q.container.on(e.c,'.fn,[type="button"]',function(){
						q.x2=$(this);q.r2=zj.Ad(q.x2);
						if($.isFunction(z[q.c1[0]][q.r2[0]][q.r2[1]]))z[q.c1[0]][q.r2[0]][q.r2[1]](q);
						return false;
					}).submit(function(){
						q.x2=$(this).find('form');q.r2=zj.Ad(q.x2);
						if($.isFunction(z[q.c1[0]][q.r2[0]][q.r2[1]]))z[q.c1[0]][q.r2[0]][q.r2[1]](q);
						return false;
					});
				});
			}
		},
		download:{
			audio:function(q){
				if(q.url)window.location=q.url;
			},
			again:function(q){
				q.x1.removeClass(cur);
				q.container.remove();
				if($.isFunction(z[q.c1[0]][0]))z[q.c1[0]][0](q);
			}
		},
		playlist:{
			post:function(q){
				var data=zj.serializeObject(q.x2), ol=$(zj.class(zj.Ac(q.container.children('ol'))[0]));
				q.url=zj.url([api,q.r2[0],q.r2[1],q.laid,data.plid.split('-')[1]]);
				q.type=q.x2.attr('method');
				q.data=$.param(data);
				if(data.name){
					z.ajax($.extend({}, q,{container:null})).done(function(j){
						if(j.zj)zj.html(ol.empty(),j.zj);
						if(data.plid)$.each(data, function(i,v){
							ol.find(zj.id(data.plid)).children(zj.class(i)).text(v);
						});
					});
					this.rs(q);
				} else{
					q.x2.find('[name="name"]').effect("highlight", {},2000);
				}
			},
			add:function(q){
				var p=q.x2.parent('li'),id=p.attr('id');
				z.ajax($.extend({}, q,{url:zj.url([api,q.r2[0],q.r2[1],q.laid,id.split('-')[1]]),container:null})).done(function(j){
					p.addClass(j.add).removeClass(j.remove);
					//$('ol').find(zj.id(id)).children('.total').text(j.total);
					$('ol').find(zj.id(id)).attr('title',j.total);
				});
			},
			desc:function(q){
				q.x2.toggleClass(cur);
				q.x2.parent('li').children(zj.class(q.r2[1])).slideToggle('fast');
			},
			fdm:function(q){
				q.x2.toggleClass(cur);
				q.x2.parent('p').next().slideToggle('fast');
			},
			edit:function(q){
				var x=q.x2.parent('li'),f=q.container.children('form');
				f.find('[name="name"]').val(x.children('.name').text());
				f.find('[name="desc"]').val(x.children('.desc').text());
				f.find('[name="plid"]').val(q.x2.parents('li').attr('id'));//q.x2.parents('li').attr('id') zj.Ai(x)[1]
				f.find('[type="submit"]').attr('name','edit');
				f.find('[name="reset"]').attr({class:'fn','data-role':'playlist rs'}).fadeIn(300);
			},
			remove:function(q){
				var p=q.x2.parent('li'),id=p.attr('id');
				q.x2.fadeOut(300);
				z.ajax($.extend({}, q,{url:zj.url([api,q.r2[0],q.r2[1],q.laid,id.split('-')[1]]),container:null})).done(function(j){
					if(j.zj){
						//q.container.children('ol').attr('class')
						zj.html($(zj.class(zj.Ac(q.container.children('ol'))[0])).empty(),j.zj);
					}else{
						$('ol').find(x).fadeOut(300).remove();
					}
				});
			},
			play:function(q){
				z.ajax($.extend({},q,{url:zj.url([api,q.r2[0],q.r2[1],q.laid,zj.Ai(q.x2.parent('li'))[1]]),container:null})).done(function(j){
					z.player.ini(j);
				});
			},
			rs:function(q){
				var x=q.container.children('form');
				x.find('input,textarea').not('[type="submit"],[name="reset"]').val('');
				x.find('[type="submit"]').attr('name','add');
				x.find('[name="reset"]').fadeOut(300);
			}			
		}
	},
	com:{
		todo:function(){
			if (typeof z.todo == 'object') {
				$.each(z.todo, function(i, t){
					$.map(t, function(p,f) {
						$(i)[f](p);
					})
				}); z.todo = {};

			}
		},
		error:function(l){
			console.log('error found...');
		},
		load:function(q){
			//{loading:loading,container:container,position:prepend,url:url}
			q.loading.addClass('loading');
			$.ajax({url:q.url, dataType:'json'}).done(function(j){
					if(j.zj)zj.html(q.container,j.zj,(q.position||null));
				}).fail(function(jqXHR,textStatus){
					//...
				}).always(function(j){
					q.loading.removeClass('loading').addClass(cur);
			});
		},
		submit:function(q){
/*
						var x=$(this), r=zj.Ad(x);
						console.log(x.serialize());
*/
			var msg=q.x3.find('.msg');
			msg.text('.....').addClass('loading');
			$.ajax({url:zj.url([api,q.r2[2]]),type:q.x3.attr('method'),data:q.x3.serialize(),dataType:'json'}).done(function(j){
					if(j.msg)msg.text(j.msg);
				}).fail(function(jqXHR,textStatus){
					msg.text(textStatus);
				}).always(function(j){
					msg.removeClass('loading');
			});
		}
	},
	ajax:function(q){
		if(q.loading)q.loading.addClass('loading');
		if(q.status && q.msg)q.status.text(q.msg);
		var request=$.ajax({url:q.url,type:q.type||'get',data:q.data||null,dataType:q.dataType||'json',async:(q.async || true)});
		request.done(function(j){
			if(q.status && j.msg)q.status.text(j.msg);
			if(q.container && j.zj){
				zj.html(q.container,j.zj,(q.position||null));
				if(q.click)q.container.children().on(e.c,q.click,function(){
					q.x=$(this);return false;
				}).submit(function(){q.x=$(this).find('form');return false;});
			}
			if(j.fn && $.isFunction(z[q.c1[0]][q.i1[0]][j.fn]))z[q.c1[0]][q.i1[0]][j.fn]($.extend(true,q,j));
		});
		request.fail(function(jqXHR, textStatus) {
			if(q.status && q.msg)q.status.text(textStatus);
		});
		request.always(function(j) {
			if(q.loading)q.loading.removeClass('loading');
		});
		return request;
		event.preventDefault();
	},
	h:{
		f:'<form>',p:'<p>',i:'<input>',l:'<label>',t:'<textarea>',d:'<div>',u:'<ul>',o:'<ol>',li:'<li>',a:'<a>',s:'<span>',
		r:{tk:"TK",al:"AL",pl:"PL",ar:"AR",fa:"FA"},
		form:{
			lyric:function(j){
				return $(z.h.f,{method:"post"}).append($(z.h.p,{class:"title"}).append($(z.h.i,{value:j.title,name:"title",type:"text"}),$(z.h.l,{text:j.Title|| "...Title"})), $(z.h.p,{class:"artist"}).append($(z.h.i,{value:j.artist,name:"artist",type:"text"}), $(z.h.l,{text:j.Artist||"...Artist"})),$(z.h.p,{class:"lyric"}).append($(z.h.t,{name:"lyric",html:j.lyric})),$(z.h.p,{class:"name"}).append($(z.h.i,{value:j.name,name:"name",type:"text"}),$(z.h.l,{text:j.yourName||"...your Name"})),$(z.h.p,{class:"submit"}).append($(z.h.i,{value:j.submit||"Post",name:"submit",type:"submit"}),$(z.h.i,{value:j.cancel||"Cancel",name:"close",type:"button","data-role":"no"})),$(z.h.p,{class:"msg",text:j.msg}));
			},
			comment:function(j){
				return $(z.h.f,{method:"post"}).append($(z.h.p,{class:"title",text:j.commentTo}), $(z.h.p,{class:"comment"}).append($(z.h.t,{name:"comment",html:j.comment})),$(z.h.p,{class:"ns",text:j.yourName||'...your Name'}), $(z.h.p,{class:"name"}).append($(z.h.i,{value:j.name,name:"name",type:"text"})),$(z.h.p,{class:"submit"}).append($(z.h.i,{value:j.submit||'Comment',name:"submit",type:"submit"})),$(z.h.p,{class:"msg",text:j.msg}));
			}
		},
		paragraph:function(j){
			return $(this.p,{class:(j.msg || 'error'),text:(j.text || 'it seems like there is no task to process')});
		},
		comment:function(){
			return $(this.d,{class:'zpl jp-comment zpm'});
		},
		lyric:function(){
			return $(this.d,{class:'zpl jp-lyric zpm'});
		},
		solution:function(){
			return $(this.d,{class:'jp-no-solution'}).append($(this.p,{html:'Update Required'}));
		},
		player:function(j){
			return $(this.d,{class:'zpp zpm'}).append(
				$(this.d,{id:j.attr.player,class:'jp-jplayer zpre'}),
				$(this.d,{class:'jp-type-playlist zpre'}).append(
					$(this.d,{class:'jp-gui jp-interface'}).append(
						$(this.u,{class:'jp-menu'}).append(
							$(this.li,{class:'zl control'}).append(
								$(this.u,{class:'fl jp-controls'}).append(
									$(this.li,{class:'jp-previous'}).append($(this.a,{tabindex:1,title:'previous'})),
									$(this.li,{class:'jp-play zb'}).append($(this.a,{tabindex:1,title:'play'})),
									$(this.li,{class:'jp-pause zb'}).append($(this.a,{tabindex:1,title:'pause'})),
									$(this.li,{class:'jp-next'}).append($(this.a,{tabindex:1,title:'next'})),
									$(this.li,{class:'jp-mute'}).append($(this.a,{tabindex:1,title:'mute'})),
									$(this.li,{class:'jp-unmute'}).append($(this.a,{tabindex:1,title:'unmute'}))
								)
							),
							$(this.li,{class:'zl volume'}).append(
								$(this.d,{class:j.attr.vs})
							),
							$(this.li,{class:'zl progress'}).append(
								$(this.u,{class:j.attr.title}).append(
									$(this.li)
								),
								$(this.d,{class:j.attr.ps}).append(
									$(this.d,{class:'jp-seek-bar'}).append(
										$(this.s,{id:j.attr.t})
									)
								),
								$(this.u,{class:'jp-times'}).append(
									$(this.li,{class:'jp-current-time'}),
									$(this.li,{class:'jp-duration'})
								)
							),
							$(this.li,{class:'zl toggle'}).append(
								$(this.u,{class:'fl jp-toggles'}).append(
									$(this.li,{class:'jp-repeat'}).append($(this.a,{tabindex:1,title:'repeat'})),
									$(this.li,{class:'jp-repeat-off'}).append($(this.a,{tabindex:1,title:'repeat off'})),
									$(this.li,{class:'jp-shuffle'}).append($(this.a,{tabindex:1,title:'shuffle'})),
									$(this.li,{class:'jp-shuffle-off'}).append($(this.a,{tabindex:1,title:'shuffle off'}))
								)
							),
							$(this.li,{class:'zl playlist jp-playlist'}).append(
								$(this.a,{class:'JPL zA',id:'playlist',title:'playlist'}),
								$(this.u,{class:j.attr.playlists})
							),
							$(this.li,{class:'zl option'}).append(
								$(this.u,{class:'fl jp-option'}).append(
									$(this.li).append($(this.a,{class:'P zA',title:'lyric P',id:'lyric'})),
									$(this.li).append($(this.a,{class:'P zA',title:'comments P',id:'comment'}))
								)
							)
						)
					)
				)
			);
		}
	}
};z.click();
}})(jQuery);
$(document).ready(function(){
	$(document).zotune({c:'click',z:'#jzmp',A:'.zA'});
	//zA
});