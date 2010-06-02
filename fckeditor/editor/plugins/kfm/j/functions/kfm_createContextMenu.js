window.kfm_createContextMenu=function(m,show_category_headers){
	var li,category,cat,cat_size,sublist,issub,contextlink,d,lContainer,lClass;
	var list=$j('<ul class="contextmenu"></ul>');
	var firsthead=true;
	for(category in context_categories){
		cat=context_categories[category];
		cat_size=cat.size();
		if(!cat_size)continue;
		var head=$j('<li class="contextmenu_head_'+cat.name+'"><span>'+cat.title+'</span></li>');
		if(cat_size>kfm_vars.subcontext_size || kfm_inArray(cat.name, kfm_vars.subcontext_categories)){
			issub=true;
			firsthead=true; // a head after a submenu is also a first one
			sublist=$j('<ul class="subcontextmenu"></ul>');
			head.append(sublist);
			head.addClass('contextmenu_subhead');
			head.hover(
				function(){
					$j(this).children('ul').each(function(i){
						var top=$j(this).siblings('span').position().top;
						if((m.x+list.width()+$j(this).width())>$j(document).width())this.style.left=(-$j(this).width())+'px';
						$j(this).css('top',top).show();
					}).addClass('contextmenu_subhead_hover');
				},
				function(){
					$j(this).children('ul').hide().removeClass('contextmenu_subhead_hover');
				}
			);
		}
		else{
			if(firsthead){
				head.addClass('contextmenu_head_first');
				firsthead=false;
			}
			issub=false;
			head.addClass('contextmenu_head');
			head.hover(
				function(){
					$j(this).addClass('contextmenu_head_hover');
				},
				function(){
					$j(this).removeClass('contextmenu_head_hover')
				}
			);
		}
		if(show_category_headers || issub)list.append(head);
		if(issub){
			lContainer=sublist;
			lClass='subcontextmenu_link';
		}
		else{
			lContainer=list;
			lClass='contextmenu_link';
		}
		for(var i=0;i<cat_size;i++){
			if(cat.items[i].nocontextmenu)continue;
			li=$j('<li class="kfm_plugin_'+cat.items[i].name+'_contexticon kfm_plugin_'+cat.items[i].name+'_'+kfm_theme+'_contexticon '+lClass+'"></li>');
			li[0].appendChild(_(cat.items[i].title));
			$j.event.add(li[0],'mouseover',function(){
				$j(this).addClass('hover');
				if(!this.hasActionEvents){ // no need to add the click/mouseout events until mouse is hovered
					$j.event.add(this,'mouseout',function(){
						$j(this).removeClass('hover');
					});
					$j.event.add(this,'click',function(){
						kfm_closeContextMenu();
						this.doFunction(this.doParameter);
					});
					this.hasActionEvents=true;
				}
			});
			li[0].doFunction=cat.items[i].doFunction;
			li[0].doParameter=cat.items[i].doParameter;
			lContainer[0].appendChild(li[0]);
		}
		cat.clear();
	}
	d=$j(document);
	document.body.appendChild(list[0]);
	if((m.y+list.height())>d.height())list[0].style.top=(m.y-list.height())+'px';
	else list[0].style.top=m.y+'px';
	if((m.x+list.width())>d.width())list[0].style.left=(m.x-list.width())+'px';
	else list[0].style.left=m.x+'px';
	list.find('.subcontextmenu').css('left',list.width());
	list.show('normal');
}
