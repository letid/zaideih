/*
 * ZOTUNE.javascript
 * Copyright (c) 2009 - 2015 ZOTUNE.developer
 * Author: Khen Solomon Lethil
 * Date: 16-01-2015
 * Version: 1.1.101
 */
var zolai='zolai',zozum='zozum',cur='cur',act='act',btn='btn';
var zj={
	tag:{
		f:'<form>',p:'<p>',i:'<input>',l:'<label>',t:'<textarea>',d:'<div>',u:'<ul>',o:'<ol>',li:'<li>',a:'<a>',s:'<span>',e:'<em>',strong:'<strong>',bold:'<b>',img:'<img>',h4:'<h4>'
	},
	data:{
		lr:'link[rel="*"]',fn:'form[name="*"]',in:'input[name="*"]',mn:'meta[name="*"]',c:'.*',i:'#*',t:'<*>',mz:'meta[name="z:*"]'
	},
	check:function(e){return (typeof e != 'undefined')?e:'';},
	fn:{
		r1:function(e,v){return $(e.replace('*',v));},
		link:function(e){
			//$.each(e,function(k,v){window[v]=$(zj.data.lr.replace('*',v)).attr('href');});
			$.each(e,function(k,v){window[v]=zj.fn.r1(zj.data.lr,v).attr('href');});
			//for(i in e){window[e[i]]=zj.fn.r1(zj.data.lr,v).attr('href');}
		},
		meta:function(e){
			$.each(e,function(k,v){window[v]=zj.fn.r1(zj.data.mz,v).attr('content');});
		}
	},
	ra:function(e,r){
		return e.join(r||'');
	},
	rt:function(e){
		return zj.data.t.replace('*',e);
	},
	rc:function(e){
		return zj.data.c.replace('*',e);
	},
	is:function(e){
		return $.map(e,function(v){return($.isNumeric(v))?String.fromCharCode(v):v;}).join('');
	},
	url:function(e){
		return $.map(e,function(v,i){if(v)return (v.slice(-1)=='/')?v.slice(0,-1):v;}).join("/").replace('=/','=').replace('/?/','?');
	},
	trip:function(e){
		var m=/[^a-zA-Z0-9 ]/g; if(e.match(m)){e=e.replace(m,'')};return e.replace(/ /g,'');
	},
	store:{
		s:function(n,v,d){
			//$.cookie(e[0], e[1],{expires:7, path:'/'});
			var expires;
		
			if (d){
				var date = new Date();
				date.setTime(date.getTime() + (d * 24 * 60 * 60 * 1000));
				expires = "; expires=" + date.toGMTString();
			}
			document.cookie = escape(n) + "=" + escape(v) + expires + "; path=/";
		},
		g:function(n){
			var nameEQ = escape(n) + "=";
			var ca = document.cookie.split(';');
			for (var i = 0; i < ca.length; i++) {
				var c = ca[i];
				while (c.charAt(0) === ' ') c = c.substring(1, c.length);
				if(c.indexOf(nameEQ) === 0) return unescape(c.substring(nameEQ.length, c.length));
			}
			return null;
		},
		r:function(n){
			zj.store.s(n, "", -1);
		}
/*
Set a cookie

$.cookie("example", "foo"); // Sample 1
$.cookie("example", "foo", { expires: 7 }); // Sample 2
$.cookie("example", "foo", { path: '/admin', expires: 7 }); // Sample 3
Get a cookie

alert( $.cookie("example") );
Delete the cookie

$.removeCookie("example");
*/
	},
	html:function(dl,d,position){//var dl = $("<dl>",{id:id});
		$.each(d, function(k, v){
			var fn = (function (item, list,position){
				if(item){
					if(item.t){
						//|| item.d.for || item.d.class && item.d.html|| item.d.value|| item.d.for
						//item.d && item.d.html || item.d && item.d.name || !item.l && item.d.text
						if (!item.l) list.append($(zj.rt(item.t), item.d));
						  else if (item.d) var cmp = item.d;
							  else var cmp = null
					}
					if(item.l && item.l.length){
						var sublist = $(zj.rt(item.t),cmp);
						for(index in item.l) fn(item.l[index], sublist);
						list[(position||'append')](sublist);
					}
				}
			}); fn(v, dl,position);
		}); return dl;//dl.appendTo("#zotune_container");
	},
	serializeObject:function(e){
		var d={};
		$.each(e.serializeArray(), function(i,v) {
			d[v.name]=v.value;
		});
		return d;
	},
	serializeJSON:function(e){
	  var o = {};
	  $.each(e.serializeArray(), function() {
		if (o[this.name] !== undefined) {
		  if (!o[this.name].push) {
			o[this.name] = [o[this.name]];
		  }
		  o[this.name].push(this.value || '');
		} else {
		  o[this.name] = this.value || '';
		}
	  });
	  return o;
	},
	form:function(e){return this.data.fn.replace('*',e);},
	input:function(e){return this.data.in.replace('*',e);},
	class:function(e){return this.data.c.replace('*',e);},
	id:function(e){return this.data.i.replace('*',e);},
	ID:function(e){return $(this.id(e));},
	CN:function(e){return $(this.class(e));},
	Rf:function(q,n){return q.substr(n||1);},
	//Rl:function(q,n){return q.substring(n||1);},
	cf:function(q,s){return q.toLowerCase().split(s||' ');},
	Ad:function(e,s){return this.check(e.attr('data-role')).split(s||' '); },//Adr
	Ai:function(e,s){return this.check(e.attr('id')).split(s||'-');},//Aid
	Ac:function(e,s){return this.check(e.attr('class')).split(' ');},//Acl
	Ah:function(e){return e.attr('href');},
	At:function(e){return e.attr('title');},
	Af:function(e,s){return this.check(e.attr('fn')).split(s||'-');},//Afn
	
	test:function(e){},
	ga:function(e,i){
		ga('send', 'pageview',e+i);
	}
};
zj.ah
zj.fn.link(['urlmain','urlproject','urlfull','api']);
zj.fn.meta(['uid','unm']);
/*
$.ajax({url:zj.url([e.url,comment]),dataType:"json",data:obj.serialize()}).done(function(j) {
	//...
}).fail(function(jqXHR,textStatus) {
	//...
}).always(function(j) {
	//...
});
*/