/*
 * MyOrdbok
 * http://www.myordbok.com
 * Copyright (c) 2008 - 2014 ZOTUNE.developer
 * Author: Khen Solomon Lethil
 * Version: 1.0.18
 * Date: Sept 14, 2014
 */
(function($){
$.fn.MyOrdbok=function(is){
  //var z=z||{},q=q||{},app=$(this);
  //if(!window.z){ z={}; }
  //if(!window.extended){ extended={}; }
  //window.iz = is;
  if(!window.q){ q={}; }
  if(!window.app){ app=$(this); }
  z=$.extend(true,{
	fn:{
		xhr:function(x){if(z.xhr)$.each(z.xhr,function(k,v){x.setRequestHeader(k,v);});}
	},
	click:function(){
		app.on(is.Click,is.Action, function(event){
			q.x1=$(this); q.r1=zj.Ad(q.x1); q.c1=zj.Ac(q.x1); q.i1=zj.Ai(q.x1);
			if(z[q.c1[0]] && $.isFunction(z[q.c1[0]][q.c1[1]])) z[q.c1[0]][q.c1[1]](); 
				else if(z[q.c1[0]] && $.isFunction(z[q.c1[0]][0])) z[q.c1[0]][0](); 
			 		else if($.isFunction(z.A[q.c1[0]])) z.A[q.c1[0]](); 
			event.preventDefault();
			event.stopPropagation();
		});
	},
	A:{
		test:function(){
		}
	},
	com:{
		error:function(l){
			console.log('error found...');
		},
		load:function(q){
		},
		submit:function(q){
		}
	}
  },window.extended || {});
  try{$.each(is.Q,function(i,f){if(z[f] && $.isFunction(z[f])){z[f]();}else if(f.indexOf(".") > 0){ try{eval('z.'+f+'()');} catch(e){}}});}catch(e){}
}})(jQuery);
//$(function(){$(document).MyOrdbok({Click:'click',Action:'.zA'});});
//$(document).ready(function(){ $(document).MyOrdbok({Click:'click',Action:'.zA'}); });
//$(function(){$(document).MyOrdbok({Click:'click',Action:'.zA',Q:['te','abc','c.td','td']});});