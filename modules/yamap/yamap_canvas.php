<div class="yamap" zoom="10" width="100%" height="400px" style="display:table;">
  <input class="yamap_data" name="yamap" type="hidden">
  <div class="yamap_editor form-group" data-wb-role="multiinput" name="yamap">
      <div class="col-sm-5">
        <input type="text" class="form-control form-control-sm finder" value="{{address}}" placeholder="Адрес">
        <input type="hidden" name="address">
        <input type="hidden" name="zoom">
      </div>
      <div class="col-sm-4"><input class="form-control form-control-sm" name="title"  placeholder="Описание"></div>
      <div class="col-sm-3"><input class="form-control form-control-sm" name="geopos" placeholder="Геопозиция" readonly></div>
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
        yamap_canvas(canvas);
      },function(e){
        yamap_canvas(canvas);
      }) ;
    } else {
      yamap_canvas(canvas);
    }
  });
}

function yamap_canvas(canvas) {
    $(canvas).addClass("done");
    var mid=$(canvas).attr("id");
    var editor=false;
    var points=[];
    var cc=[];
    if ($(canvas).find(".yamap_data").val()>"") {var epoints=json_decode($(canvas).find(".yamap_data").val());} else {var epoints=[];}
    if ($(canvas).attr("geopos")>"") { var ll=yamap_pos($(canvas).attr("value"));}
    if ($(canvas).attr("center")>"") {var cc=yamap_pos($(canvas).attr("center"));}

    if ($(canvas).attr("zoom")>"") { var zoom=$(canvas).attr("zoom")*1;} else { var zoom=10; $(canvas).attr("zoom",zoom);}
    if ($(canvas).attr("height")>"") {$(canvas).height($(canvas).attr("height"));}
    if ($(canvas).attr("width")>"") {$(canvas).width($(canvas).attr("width"));}
    if ($(canvas).attr("name")>"") {$(canvas).find("[name=yamap]").attr("name",$(canvas).attr("name"));}
    if ($(canvas).attr("editable")==undefined) {$(canvas).find(".yamap_editor").remove();} else {$(canvas).find(".yamap_data").remove(); editor=true;}

    if (editor==true) {
        $(epoints).each(function(i,item){
            var point={
                pos: yamap_pos(item.geopos)
              , content: item.address
              , title: item.title
              , geofld: $(canvas).find(".yamap_editor [data-wb-field=geopos]:eq("+i+")")
            };

            if (point.pos.length==2) {points.push(point); if (i==0) {cc=point.pos;}}
        });
    } else {
        $(canvas).find("[role=geopos]").each(function(i){
          var point={
              pos: yamap_pos($(this).attr("value"))
            , content: $(this).html()
            , title: $(this).attr("title")
          };
          if (point.pos.length==2) {points.push(point);if (i==0) {cc=point.pos;}}
        });
    }
    var map = new ymaps.Map(mid, {
      center: cc,
      zoom: zoom,
      controls: ["zoomControl","fullscreenControl"]
    });
    if (cc.length!==2) {var ll=yamap_pos("44.894997 37.316259");}
    map.behaviors.disable("scrollZoom");
    var clusterer = new ymaps.Clusterer();
    $(canvas).data("map",map);
    $(canvas).data("clusterer",clusterer);
    $(canvas).data("editor",editor);
    $(points).each(function(i,point){
        yamap_addPiont(canvas,point);
		});
    yamap_geo(canvas);
}
});

function yamap_addPiont(canvas,point) {
  var pos=point.pos;
  var map=$(canvas).data("map");
  var clusterer=$(canvas).data("clusterer");
  var editor=$(canvas).data("editor");
  var myPlacemark = new ymaps.Placemark(pos, {
    balloonContentHeader: point.title,
    balloonContent: point.content,
    pos: pos
  },
  {
    draggable: editor, // метку можно перемещать
  });
  if (point.geofld!==undefined) {
    $(point.geofld).data("placemark",myPlacemark);
    myPlacemark.events.add('dragend', function(e) {
      var canvasPlacemark = e.get('target');
      var pos = canvasPlacemark.geometry.getCoordinates();
      $(point.geofld).val(pos[0]+" "+pos[1]);
    });
    $(point.geofld).data("placemark",myPlacemark);
  }
  clusterer.add(myPlacemark);
  map.geoObjects.add(clusterer);
}


function yamap_pos(ll) {
  ll=trim(ll);
  ll=str_replace(","," ",ll);
  ll=str_replace("  "," ",ll);
  var tmp=explode(" ",ll);
  if (tmp.length==2) {return [tmp[0],tmp[1]]; } else {return [];}
}

function yamap_geo(canvas) {

    $(canvas).undelegate(".yamap_editor","before_remove");
    $(canvas).delegate(".yamap_editor","before_remove",function(e,line){
        var geo=$(line).find("[data-wb-field=geopos]");
        console.log(line);
        var myPlacemark=$(geo).data("placemark");
        if (myPlacemark!==undefined) {
            var map=$(canvas).data("map");
            var clusterer=$(canvas).data("clusterer");
            clusterer.remove(myPlacemark);
        }
    });

    $(canvas).find(".yamap_editor").undelegate(".wb-multiinput","click");
    $(canvas).find(".yamap_editor").delegate(".wb-multiinput","click",function(e){
        var map=$(canvas).data("map");
        var geo=$(this).find("[data-wb-field=geopos]");
        var pos=explode(" ",$(geo).val());
        var zoom=$(this).parents("[zoom]").attr("zoom");
        map.setZoom(zoom);
        map.setCenter(pos);
        e.preventDefault();
    });

    $(canvas).find(".yamap_editor").undelegate(".finder","keyup");
    $(canvas).find(".yamap_editor").delegate(".finder","keyup",function(){
        var map=$(canvas).data("map");
        var clusterer=$(canvas).data("clusterer");
        var addr=$(this).val();
        var geo=$(this).parents(".wb-multiinput").find("[data-wb-field=geopos]");
        var title=$(this).parents(".wb-multiinput").find("[data-wb-field=title]").val();
        var pos=yamap_pos($(geo).val());
        var zoom=$(this).parents("[zoom]").attr("zoom");


        ymaps.geocode(addr, {results: 1}).then(function(res) {
            var obj = res.geoObjects.get(0);
            var pos = res.geoObjects.get(0).geometry._coordinates;
            $(geo).val(implode(" ",pos));
            var point={
                pos: pos
              , content: addr
              , title: title
              , geofld: geo
            };

            var myPlacemark=$(geo).data("placemark");
            if (myPlacemark==undefined) {
              yamap_addPiont(canvas,point);
            } else {
              yamap_addPiont(canvas,point);
              clusterer.remove(myPlacemark);
            }
        }, function (err) {

            console.log(err.message);

        });

          map.setZoom(zoom);
          map.setCenter(pos);
    });

}
</script>