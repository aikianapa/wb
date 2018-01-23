<div class="yamap" zoom="10" width="100%" height="400px">
  <div class="yamap_editor" data-wb-role="multiinput" name="yamap">
    <input name="address">
    <input name="geopos">
  </div>
</div>

<script data-wb-append="body" defer>
$(document).ready(function(){

if ($(document).data("yamap")==undefined) {
  var fileref=document.createElement("script");
  fileref.type="text/javascript";
  fileref.src='https://api-maps.yandex.ru/2.1/?lang=ru_RU';
  document.getElementsByTagName("head")[0].appendChild(fileref);
  $(document).data("yamap",true);
  setTimeout(function(){ymaps.ready(yamap);},1000);
} else {
  ymaps.ready(yamap);
}


function yamap() {
  $(".yamap:not(.done)").each(function(i){
    var canvas=this;
    var mid="yamap_canvas"+i;
    var editor=$(this).find(".yamap_editor").clone();
    $(this).attr("id",mid);
  if ($(this).attr("center")==undefined) {
    ymaps.geolocation.get().then(function (res) {
      $(canvas).attr("center",res.geoObjects["position"][0]+" "+res.geoObjects["position"][1]);
      yamap_canvas(ymaps,canvas);
    },function(e){
      console.log(e);
      yamap_canvas(canvas);
    }) ;
} else {
  yamap_canvas(canvas);
}

  });

}

function yamap_canvas(ymaps,canvas) {

    if ($(canvas).attr("value")>"") { var ll=yamap_pos($(canvas).attr("value"));}
    if ($(canvas).attr("center")>"") {var cc=yamap_pos($(canvas).attr("center"));} else {var cc=ll;}
    if ($(canvas).attr("zoom")>"") { var zoom=$(canvas).attr("zoom")*1;} else { var zoom=10;}
    if ($(canvas).attr("height")>"") {$(canvas).height($(canvas).attr("height"));}
    if ($(canvas).attr("width")>"") {$(canvas).width($(canvas).attr("width"));}

    map = new ymaps.Map(mid, {
      center: cc,
      zoom: zoom,
      controls: ["zoomControl","fullscreenControl"]
    });
    map.behaviors.disable("scrollZoom");
    clusterer = new ymaps.Clusterer();


      $(canvas).find("[role=geopos]").each(function(){
  			var pos=yamap_pos($(canvas).attr("value"));
  			var content=$(canvas).html();
        var title=$(canvas).attr("title");
  			var complex = new ymaps.Placemark(pos, {
  				balloonContentHeader: title,
  				balloonContent: content,
  				pos: pos
  			},
        {
      		draggable: true, // метку можно перемещать
      	});
        complex.events.add('dragend', function(e) {
      		var canvasPlacemark = e.get('target');
      		var pos = canvasPlacemark.geometry.getCoordinates();
      		getAddress(pos);
      		$("input[name=position]").val(pos[1]+" "+pos[0]);
      		$("input[name=zoom]").val(map.getZoom());
      	});
  			//placeAddEvent(complex,"complex");
  			clusterer.add(complex);
        $(canvas).remove();
  		});
  		map.geoObjects.add(clusterer);
      $(canvas).addClass("done");

}

function yamap_pos(ll) {
  ll=trim(ll);
  ll=str_replace(","," ",ll);
  ll=str_replace("  "," ",ll);
  var tmp=explode(" ",ll);
  return [tmp[0],tmp[1]];
}
});

function yamap_geo() {

}
</script>
