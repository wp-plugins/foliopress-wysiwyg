window.kdnd_dragFinish=function(e,notest){
	clearTimeout(window.dragTrigger);
	if(!window.kdnd_dragging)return;
	if(!notest){ // check for targets and run functions if found
		var a,b,els,m={x:e.pageX,y:e.pageY},el;
		$each(kdnd_targets[window.kdnd_drag_class],function(fn,a){
			$j(a).each(function(key,el){
				if(getOffset(el,'Left')<=m.x&&m.x<getOffset(el,'Left')+el.offsetWidth&&getOffset(el,'Top')<=m.y&&m.y<getOffset(el,'Top')+el.offsetHeight){
					e.sourceElement=kdnd_source_el;
					e.targetElement=el;
					fn(e);
				}
			});
		});
		if($j(kdnd_source_el).hasClass('drag_this')){
			kdnd_source_el.style.left      =(m.x+window.kdnd_offset.x)+'px';
			kdnd_source_el.style.top       =(m.y+window.kdnd_offset.y)+'px';
			kdnd_source_el.style.visibility='visible';
		}
	}
	{ // cleanup
		window.kdnd_dragging=false;
		$j.event.remove(document,'mousemove',kdnd_drag);
		$j.event.remove(document,'mouseup',kdnd_dragFinish);
		$j(window.kdnd_drag_wrapper).remove();
		window.kdnd_drag_wrapper=null;
		window.kdnd_source_el=null;
	}
}
