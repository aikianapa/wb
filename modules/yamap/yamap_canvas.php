<style>
.yamap {
  width:100%;
  height:400px;
}
</style>
<div class="yamap" pos="30 40" zoom="10"></div>
<script src='https://api-maps.yandex.ru/2.1/?lang=ru_RU' type='text/javascript'></script>
<script data-wb-append="body">
$(document).ready(function(){
ymaps.ready(yamap);
function yamap_pos(ll) {
  ll=trim(ll);
  ll=str_replace(","," ",ll);
  ll=str_replace("  "," ",ll);
  var tmp=explode(" ",ll);
  return [tmp[0],tmp[1]];
}
function yamap() {
  $(".yamap:not(.done)").each(function(i){
    var canvas=this;
    var mid="yamap_canvas"+i;
    $(this).attr("id",mid);

  if ($(this).attr("pos")>"") { var ll=yamap_pos($(this).attr("pos"));}
  if ($(this).attr("center")>"") {var cc=yamap_pos($(this).attr("center"));} else {var cc=ll;}
  if ($(this).attr("zoom")>"") { var zoom=$(this).attr("zoom")*1;} else { var zoom=10;}
  if ($(this).attr("height")>"") {$(this).height($(this).attr("height"));}
  if ($(this).attr("width")>"") {$(this).width($(this).attr("width"));}
  map = new ymaps.Map(mid, {
    center: cc,
    zoom: zoom,
    controls: ["zoomControl","fullscreenControl"]
  });
  map.behaviors.disable("scrollZoom");
  clusterer = new ymaps.Clusterer();
  if (ll!==undefined) {
    var myPlacemark = new ymaps.Placemark(ll, {
      hintContent: $("input[name=address]").val(),
      balloonContent: $("input[name=address]").val(),
    });
    map.geoObjects.add(myPlacemark);
    }

    $(this).find("[pos]").each(function(){
			var pos=yamap_pos($(this).attr("pos"));
			var content=$(this).html();
      var title=$(this).attr("title");
			var complex = new ymaps.Placemark(pos, {
				balloonContentHeader: title,
				balloonContent: content,
				pos: pos
			});

			//placeAddEvent(complex,"complex");
			clusterer.add(complex);
      $(this).remove();
		});
		map.geoObjects.add(clusterer);

    $(this).addClass("done");
  });

}
});
</script>
