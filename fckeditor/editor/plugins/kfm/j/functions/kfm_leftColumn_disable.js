window.kfm_leftColumn_disable=function(){
	var left_column=document.getElementById('kfm_left_column');
	var left_column_hider=document.createElement('div');
	left_column.id='kfm_left_column_hider';
	left_column_hider.style.position='absolute';
	left_column_hider.style.left      =0;
	left_column_hider.style.top       =0;
	left_column_hider.style.width     =left_column.offsetWidth+'px';
	left_column_hider.style.height    =left_column.offsetHeight+'px';
	left_column_hider.style.opacity   ='.7';
	left_column_hider.style.background='#fff';
	document.body.appendChild(left_column_hider);
	kfm_resizeHandler_addMaxHeight('kfm_left_column_hider');
}
