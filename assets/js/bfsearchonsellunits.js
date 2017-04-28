		var mapBFSSell;
		var myLatlngBFSSell;
		var drawingManager;
		var selectedShape;
		var $dialog;
		var $XGooglePos;
		var $YGooglePos;
		var $googlemapsapykey;
		var $startzoom = 12;
		var inizializated = 0;
		var latlngbounds;
		var titlePopupMap = "";

		// drawing setting
		var polyOptions = {
			strokeWeight: 0,
			fillOpacity: 0.45,
			editable: true
		};
	
		// make map
		function handleApiReadyBFSSell() {
			myLatlngBFSSell = new google.maps.LatLng($Lat,$Lng );
			var myOptions = {
					zoom: $startzoom,
					center: myLatlngBFSSell,
					mapTypeId: google.maps.MapTypeId.ROADMAP
				}
			mapBFSSell = new google.maps.Map(document.getElementById("map_canvasBFSSell"), myOptions);
			// Create the search box and link it to the UI element.
					var input = document.getElementById('addresssearch');
					var searchBox = new google.maps.places.SearchBox(input);
					mapBFSSell.controls[google.maps.ControlPosition.TOP_LEFT].push(input);
// Bias the SearchBox results towards current map's viewport.
        mapBFSSell.addListener('bounds_changed', function() {
          searchBox.setBounds(mapBFSSell.getBounds());
        });

        var markers = [];
        // Listen for the event fired when the user selects a prediction and retrieve
        // more details for that place.
        searchBox.addListener('places_changed', function() {
          var places = searchBox.getPlaces();

          if (places.length == 0) {
            return;
          }

          // Clear out the old markers.
          markers.forEach(function(marker) {
            marker.setMap(null);
          });
          markers = [];

          // For each place, get the icon, name and location.
          var bounds = new google.maps.LatLngBounds();
          places.forEach(function(place) {
            var icon = {
              url: place.icon,
              size: new google.maps.Size(71, 71),
              origin: new google.maps.Point(0, 0),
              anchor: new google.maps.Point(17, 34),
              scaledSize: new google.maps.Size(25, 25)
            };

            // Create a marker for each place.
            markers.push(new google.maps.Marker({
              map: mapBFSSell,
              icon: icon,
              title: place.name,
              position: place.geometry.location
            }));

            if (place.geometry.viewport) {
              // Only geocodes have viewport.
              bounds.union(place.geometry.viewport);
            } else {
              bounds.extend(place.geometry.location);
            }
          });
          mapBFSSell.fitBounds(bounds);
        });


			drawingManager = new google.maps.drawing.DrawingManager({
								drawingControlOptions: {
									drawingControl: false,
									position: google.maps.ControlPosition.TOP_CENTER,
									drawingModes: [
									  google.maps.drawing.OverlayType.POLYGON,
									  google.maps.drawing.OverlayType.CIRCLE,
									  google.maps.drawing.OverlayType.RECTANGLE
									]},
									  rectangleOptions: polyOptions,
									  circleOptions: polyOptions,
									  polygonOptions: polyOptions
			});
			google.maps.event.addListener(drawingManager, 'overlaycomplete', function(e) {
				if (e.type != google.maps.drawing.OverlayType.MARKER) {
					// Switch back to non-drawing mode after drawing a shape.
					drawingManager.setDrawingMode(null);
					// To hide:
					drawingManager.setOptions({
						drawingControl: false
					});
					jQuery("#btnCompleta").show();
//					jQuery("#searchAdress").hide();
				}
				var newShape = e.overlay;
				newShape.type = e.type;
				google.maps.event.addListener(newShape, 'click', function() {
				  setSelection(newShape);
				});
				setSelection(newShape);
				calculateArea()
				google.maps.event.addListener(mapBFSSell, 'mouseup', function (event) {
					calculateArea();
				});
				google.maps.event.addListener(newShape, 'mouseup', function (event) {
					calculateArea();
				});
			});
        //google.maps.event.addListener(drawingManager, 'drawingmode_changed', clearSelection);
        google.maps.event.addListener(mapBFSSell, 'click', clearSelection);
        google.maps.event.addDomListener(document.getElementById('btndelete'), 'click', deleteSelectedShape);
        google.maps.event.addDomListener(document.getElementById('btnconfirm'), 'click', getShapePath);
		if (inizializated==0)
		{
		//check if there are some points...
			if(jQuery("#points")[0].value.length > 0){
				latlngbounds = new google.maps.LatLngBounds();
				var existingPoints =jQuery("#points").val();
				var typeShape = existingPoints.split("|");
				
				switch(typeShape[0]){
					case "0": // draw circle
						var coords= typeShape[1].split(" ");
						var newShape = new google.maps.Circle(polyOptions);
						newShape.type = google.maps.drawing.OverlayType.CIRCLE;
						newShape.setCenter(new google.maps.LatLng(coords[0], coords[1]));
						newShape.setRadius(parseFloat(coords[2]));
						newShape.setMap(mapBFSSell);
						setSelection(newShape);
						//mapBFSSell.fitBounds(newShape.getBounds());
						latlngbounds = newShape.getBounds();
						jQuery("#btnCompleta").show();
//						jQuery("#searchAdress").hide();
						google.maps.event.addListener(newShape, 'click', function() {
						  setSelection(newShape);
						});
						google.maps.event.addListener(newShape, 'mouseup', function (event) {
							calculateArea();
						});
						break;
					case "1": //draw poligon
						var coords= typeShape[1].split(",");
						var pts=new Array();
						jQuery.each(coords,function(i,point){
							var singlecoord= point.split(" ");
							var singleLatLng= new google.maps.LatLng(singlecoord[0], singlecoord[1]);
							pts.push(singleLatLng);
							latlngbounds.extend(singleLatLng);

						});
						var newShape = new google.maps.Polygon(polyOptions);
						newShape.type = google.maps.drawing.OverlayType.POLYGON;
						newShape.setPaths(pts);
						newShape.setMap(mapBFSSell);
						setSelection(newShape);
	//					mapBFSSell.fitBounds(pts);
						jQuery("#btnCompleta").show();
//						jQuery("#searchAdress").hide();
						google.maps.event.addListener(newShape, 'click', function() {
						  setSelection(newShape);
						});
						google.maps.event.addListener(newShape, 'mouseup', function (event) {
							calculateArea();
						});
//						mapBFSSell.fitBounds(latlngbounds);

						break;
					default:
						drawPoligon();
						break;
				}
			}else{
				drawPoligon();
			}

		}else{
			drawPoligon();
		}
		if (typeof latlngbounds !== 'undefined' && typeof latlngbounds === 'object' ){
			mapBFSSell.fitBounds(latlngbounds);
		}

		//mapBFSSell.fitBounds(latlngbounds);
	}
      
	  function calculateArea(){
        if (selectedShape) {
			if (selectedShape.type == google.maps.drawing.OverlayType.CIRCLE) {
				var radius = selectedShape.getRadius();
				var area = ( Math.round(radius*radius*Math.PI / 10000) / 100 );
				jQuery("#spanArea").html("Km&sup2;: " + area);
			}
			if (selectedShape.type == google.maps.drawing.OverlayType.POLYGON) {
				var path = selectedShape.getPath();
				var area = ( Math.round(google.maps.geometry.spherical.computeArea(path) / 10000) / 100 );// google.maps.geometry.spherical.computeArea(e.overlay.getPath());
				jQuery("#spanArea").html("Km&sup2;: " + area);

			}
        }
	  
	  }

	  function getShapePath() {
		if (selectedShape) {
			latlngbounds = new google.maps.LatLngBounds();
			if (selectedShape.type == google.maps.drawing.OverlayType.CIRCLE) {
				var circleCenter = selectedShape.getCenter();
				var radius = selectedShape.getRadius();
				var circlepoints = "0|" + circleCenter.lat() + " " + circleCenter.lng() + " " + radius;
				jQuery("#points").val(circlepoints);
				latlngbounds = selectedShape.getBounds();
			}
			if (selectedShape.type == google.maps.drawing.OverlayType.POLYGON) {
				var path = selectedShape.getPath().getArray();
				var pts=new Array();
				jQuery.each(path,function(i,point){
					pts.push(point.lat() + " "+point.lng());
					latlngbounds.extend(point);
				});
				pts.push(path[0].lat()+" "+path[0].lng());
				var stringJoin=pts.join(",");
				jQuery("#points").val("1|" + stringJoin);
			}
			//jQuery("#zoneId").val("0");
			//jQuery("#locationZonesList").find("option").prop("selected", "");
			//jQuery("#locationzones").val("");
			//jQuery("#locationZonesList").find("option[value='-1']").prop("selected", "selected");
			jQuery("#mapSearch").prop("checked", "chekced");
        }else{
			jQuery("#mapSearch").prop("checked", "");
			jQuery("#zoneId").val(currentLocation);
		}
		$dialog.dialog('close');
      }

	  function drawPoligon() {
		deleteSelectedShape()
//		jQuery(".select-figure").addClass("unactive");
//		jQuery("#btndrawpoligon").removeClass("unactive");
		jQuery(".select-figure").removeClass("active");
		jQuery("#btndrawpoligon").addClass("active");
		drawingManager.setDrawingMode(google.maps.drawing.OverlayType.POLYGON);
		drawingManager.setMap(mapBFSSell);
		drawingManager.setOptions({
			drawingControl: false
		});
      }
	  function drawCircle() {
		deleteSelectedShape()
//		jQuery(".select-figure").addClass("unactive");
//		jQuery("#btndrawcircle").removeClass("unactive");
		jQuery(".select-figure").removeClass("active");
		jQuery("#btndrawcircle").addClass("active");
		drawingManager.setDrawingMode(google.maps.drawing.OverlayType.CIRCLE);
		drawingManager.setMap(mapBFSSell);
		drawingManager.setOptions({
			drawingControl: false
		});
      }

	  function clearSelection() {
        if (selectedShape) {
          selectedShape.setEditable(false);
          selectedShape = null;
		  jQuery("#btndelete").attr("disabled", "disabled");
        }
      }
      function setSelection(shape) {
        clearSelection();
        selectedShape = shape;
        shape.setEditable(true);
		jQuery("#btndelete").removeAttr("disabled");                              

      }
      function deleteSelectedShape() {
        if (selectedShape) {
			//google.maps.event.removeListener(mapBFSSell, 'mouseup');
			//google.maps.event.removeListener(selectedShape, 'mouseup');
			
			selectedShape.setMap(null);
			selectedShape = null;
        }
        // To show:
//         drawingManager.setOptions({
//           drawingControl: true
//         });
			jQuery("#btnCompleta").hide();
//			jQuery("#searchAdress").show();

			jQuery("#btndelete").attr("disabled", "disabled");
			jQuery("#spanArea").html("");
			jQuery("#points").val("");
			jQuery("#zoneId").val(currentLocation);
      }

//		function addPoint(e) {
//			var vertices = shape.getPath();
//			vertices.push(e.latLng);
//		}
		function openGoogleMapBFSSell() {
			var width = jQuery(window).width()*0.9;
			var height = jQuery(window).height()*0.9;
			if (typeof google !== "undefined" || typeof google !== 'object' || typeof google.maps !== 'object'){
				var script = document.createElement("script");
				script.type = "text/javascript";
				script.src = "https://maps.google.com/maps/api/js?key=" + $googlemapsapykey +"&libraries=drawing,places&callback=handleApiReadyBFSSell";
				document.body.appendChild(script);
			}else{
				if (typeof mapBFSSell !== 'object'){
					inizializated = 1;
					handleApiReadyBFSSell();
				}
			}

			$dialog = jQuery("#divBFSSell").dialog({
					autoOpen: false,
					modal: true,
					resize: function( event, ui ) {
						google.maps.event.trigger(mapBFSSell, 'resize');
						mapBFSSell.setCenter(myLatlngBFSSell);
					},
					height:height,
					width: width,
					fluid: true, //new option
					title: titlePopupMap
				});
			$dialog.dialog('open');
			jQuery("#map_canvasBFSSell").css("height", height-110);
			if (typeof google !== 'undefined' && typeof google === 'object' ){
				if (typeof mapBFSSell !== 'undefined' && typeof mapBFSSell === 'object' ){
					google.maps.event.trigger(mapBFSSell, 'resize');
					mapBFSSell.setCenter(myLatlngBFSSell);
					if (typeof latlngbounds !== 'undefined' && typeof latlngbounds === 'object' ){
						mapBFSSell.fitBounds(latlngbounds);
					}
				}
			}
		}
		
//		jQuery(function() {
//			jQuery("#addresssearch").keypress(function(event){
//				if(event.keyCode == 13){
//					event.preventDefault(); 
//					codeAddress()
//				}
//			});
//		});

		jQuery(window).resize(function() {
		var BFSSel = jQuery("#divBFSSell");
		try
		{
			if (BFSSel.length >0 && BFSSel.dialog( "isOpen" ))
			{
				var wWidth = jQuery(window).width();
				var dWidth = wWidth * 0.9;
				var wHeight = jQuery(window).height();
				var dHeight = wHeight * 0.9;
				BFSSel.dialog("option", "width", dWidth);
				BFSSel.dialog("option", "height", dHeight);
				BFSSel.dialog("option", "position", "center");
				jQuery("#map_canvasBFSSell").css("height", dHeight-110);
				if (typeof google !== "undefined")
				{
					if (typeof google === 'object' && typeof google.maps === 'object' && typeof mapBFSSell !== "undefined"){
						google.maps.event.trigger(mapBFSSell, 'resize');
						if (typeof mapBFSSell !== 'undefined' && typeof mapBFSSell === 'object' ){
							mapBFSSell.setCenter(myLatlngBFSSell);
						}
						if (typeof latlngbounds !== 'undefined' && typeof latlngbounds === 'object' ){
							mapBFSSell.fitBounds(latlngbounds);
						}
	//					mapBFSSell.setCenter(myLatlngBFSSell);
					}
				}
			}			
		}
		catch (err)
		{
		}

		}
		
		);

//		function codeAddress() {
//				var geocoder = new google.maps.Geocoder();
//				geocoder.geocode({ 'address': jQuery("#addresssearch").val() }, function (results, status) {
//					if (status == google.maps.GeocoderStatus.OK) {
//						mapBFSSell.setCenter(results[0].geometry.location);
//						var marker = new google.maps.Marker({
//							map: mapBFSSell,
//							position: results[0].geometry.location
//						});
////						latitude = results[0].geometry.location.lat();
////						longitude = results[0].geometry.location.lng();
//						drawPoligon()
//					} else {
//						alert("Geocode was not successful for the following reason: " + status);
//					}
//				});
//			}