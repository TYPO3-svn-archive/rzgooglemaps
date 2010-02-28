function switchContent(obj) {     
  obj = (!obj) ? 'sub1' : obj; 
	var contentDivs = document.getElementById('mapsearchcont').getElementsByTagName('div');
		for (i=0; i<contentDivs.length; i++) {
			if (contentDivs[i].id && contentDivs[i].id.indexOf('sub') != -1) {
				contentDivs[i].className = 'mapsearchhide';
	   	}
		}
		document.getElementById(obj).className = '';	
}
	
function selected(obj){
  var lilist = document.getElementById('mapsearchnav');
  var alist = lilist.getElementsByTagName('a');
  for (i=0; i<alist.length; i++ )
  {
    alist[i].className="";
  }
  obj.className="routebold";
}    