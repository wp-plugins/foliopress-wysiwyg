window.kdnd_unmakeDraggable=function(source_class){
	if($type(source_class)=='array'){
		return source_class.each(kdnd_unmakeDraggable);
	}
	var els=$j('.'+source_class),i,el;
	for(i=0;i<els.length;++i){
		el=els[i];
		if(!el.kdnd_applied)continue;
		el.kdnd_applied=false;
		if(!el.dragevents)el.dragevents=[];
		if(!el.dragevents[source_class])el.dragevents[source_class]=kdnd_dragInit(el,source_class);
		$j.event.remove(el,'mousedown',el.dragevents[source_class]);
	}
}
