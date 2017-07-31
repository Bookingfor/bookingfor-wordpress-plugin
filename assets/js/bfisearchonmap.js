		var bfiMapMapDrawer;
		var bfiMyLatlngMapDrawer;
		var bfiDrawingManager;
		var bfiSelectedShape;
		var bfiCurrDialog;
		var bfiCurrXGooglePos;
		var bfiCurrYGooglePos;
		var $googlemapsapykey;
		var bfiCurrStartzoom = 12;
		var bfiCurrInizializated = 0;
		var bfiCurrLatLngbounds;
		var bfiCurrTitlePopupMap = "";
		
		var bfiCurrForm;
		var bfiCurrPoint;
		var bfiCurrMapDrawer;
		var bfiCurrMapCanvas;
		var bfiCurrMapCanvas;
		var bfiCurrBtndelete;
		var bfiCurrBtnconfirm;
		var bfiCurrbtnCompleta;
		var bfiCurrAddresssearch;
		var bfiCurrSpanArea;
		var bfiCurrDrawpoligon;
		var bfiCurrDrawcircle;

		// drawing setting
		var polyOptions = {
			strokeWeight: 0,
			fillOpacity: 0.45,
			editable: true
		};
	
		// make map
		function handleApiReadyMapDrawer() {
			bfiMyLatlngMapDrawer = new google.maps.LatLng($Lat,$Lng );
			var myOptions = {
					zoom: bfiCurrStartzoom,
					center: bfiMyLatlngMapDrawer,
					mapTypeId: google.maps.MapTypeId.ROADMAP
				}
			bfiMapMapDrawer = new google.maps.Map(bfiCurrMapCanvas[0], myOptions);
			// Create the search box and link it to the UI element.
			var searchBox = new google.maps.places.SearchBox(bfiCurrAddresssearch[0]);
			bfiMapMapDrawer.controls[google.maps.ControlPosition.TOP_LEFT].push(bfiCurrAddresssearch[0]);
			// Bias the SearchBox results towards bfiCurrent map's viewport.
			bfiMapMapDrawer.addListener('bounds_changed', function() {
				searchBox.setBounds(bfiMapMapDrawer.getBounds());
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
						map: bfiMapMapDrawer,
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
				bfiMapMapDrawer.fitBounds(bounds);
			});


			bfiDrawingManager = new google.maps.drawing.DrawingManager({
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
			google.maps.event.addListener(bfiDrawingManager, 'overlaycomplete', function(e) {
				if (e.type != google.maps.drawing.OverlayType.MARKER) {
					// Switch back to non-drawing mode after drawing a shape.
					bfiDrawingManager.setDrawingMode(null);
					// To hide:
					bfiDrawingManager.setOptions({
						drawingControl: false
					});
					bfiCurrbtnCompleta.show();
//					jQuery("#searchAdress").hide();
				}
				var newShape = e.overlay;
				newShape.type = e.type;
				google.maps.event.addListener(newShape, 'click', function() {
				  setSelection(newShape);
				});
				setSelection(newShape);
				calculateArea()
				google.maps.event.addListener(bfiMapMapDrawer, 'mouseup', function (event) {
					calculateArea();
				});
				google.maps.event.addListener(newShape, 'mouseup', function (event) {
					calculateArea();
				});
			});
		//google.maps.event.addListener(bfiDrawingManager, 'drawingmode_changed', clearSelection);
		google.maps.event.addListener(bfiMapMapDrawer, 'click', clearSelection);
		google.maps.event.addDomListener(bfiCurrBtndelete[0], 'click', deleteSelectedShape);
		google.maps.event.addDomListener(bfiCurrBtnconfirm[0], 'click', function() {
			bfiCurrDialog.dialog('close');

		});
//---------------------------------------------------------
	drawShape();
	//------------------------------------------------------------
		if (typeof bfiCurrLatLngbounds !== 'undefined' && typeof bfiCurrLatLngbounds === 'object' ){
			bfiMapMapDrawer.fitBounds(bfiCurrLatLngbounds);
		}

		//bfiMapMapDrawer.fitBounds(bfiCurrLatLngbounds);
	}
	function drawShape(){
		if (bfiCurrInizializated==0)
		{
		//check if there are some points...
			if (bfiSelectedShape) {
			//google.maps.event.removeListener(bfiMapMapDrawer, 'mouseup');
			//google.maps.event.removeListener(bfiSelectedShape, 'mouseup');
			
			bfiSelectedShape.setMap(null);
			bfiSelectedShape = null;
			}

			if(bfiCurrPoint[0].value.length > 0){
				bfiCurrLatLngbounds = new google.maps.LatLngBounds();
				var existingPoints =bfiCurrPoint.val();
				var typeShape = existingPoints.split("|");
				
				switch(typeShape[0]){
					case "0": // draw circle
						var coords= typeShape[1].split(" ");
						var newShape = new google.maps.Circle(polyOptions);
						newShape.type = google.maps.drawing.OverlayType.CIRCLE;
						newShape.setCenter(new google.maps.LatLng(coords[0], coords[1]));
						newShape.setRadius(parseFloat(coords[2]));
						newShape.setMap(bfiMapMapDrawer);
						setSelection(newShape);
						//bfiMapMapDrawer.fitBounds(newShape.getBounds());
						bfiCurrLatLngbounds = newShape.getBounds();
						bfiCurrbtnCompleta.show();
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
							bfiCurrLatLngbounds.extend(singleLatLng);

						});
						var newShape = new google.maps.Polygon(polyOptions);
						newShape.type = google.maps.drawing.OverlayType.POLYGON;
						newShape.setPaths(pts);
						newShape.setMap(bfiMapMapDrawer);
						setSelection(newShape);
	//					bfiMapMapDrawer.fitBounds(pts);
						bfiCurrbtnCompleta.show();
//						jQuery("#searchAdress").hide();
						google.maps.event.addListener(newShape, 'click', function() {
						  setSelection(newShape);
						});
						google.maps.event.addListener(newShape, 'mouseup', function (event) {
							calculateArea();
						});
//						bfiMapMapDrawer.fitBounds(bfiCurrLatLngbounds);

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
	}

	function calculateArea(){
		if (bfiSelectedShape) {
			if (bfiSelectedShape.type == google.maps.drawing.OverlayType.CIRCLE) {
				var radius = bfiSelectedShape.getRadius();
				var area = ( Math.round(radius*radius*Math.PI / 10000) / 100 );
				bfiCurrSpanArea.html("Km&sup2;: " + area);
			}
			if (bfiSelectedShape.type == google.maps.drawing.OverlayType.POLYGON) {
				var path = bfiSelectedShape.getPath();
				var area = ( Math.round(google.maps.geometry.spherical.computeArea(path) / 10000) / 100 );// google.maps.geometry.spherical.computeArea(e.overlay.getPath());
				bfiCurrSpanArea.html("Km&sup2;: " + area);

			}
		}
	}

	function getShapePath() {
		if (bfiSelectedShape) {
			bfiCurrLatLngbounds = new google.maps.LatLngBounds();
			if (bfiSelectedShape.type == google.maps.drawing.OverlayType.CIRCLE) {
				var circleCenter = bfiSelectedShape.getCenter();
				var radius = bfiSelectedShape.getRadius();
				var circlepoints = "0|" + circleCenter.lat() + " " + circleCenter.lng() + " " + radius;
				bfiCurrPoint.val(circlepoints);
				bfiCurrLatLngbounds = bfiSelectedShape.getBounds();
			}
			if (bfiSelectedShape.type == google.maps.drawing.OverlayType.POLYGON) {
				var path = bfiSelectedShape.getPath().getArray();
				var pts=new Array();
				jQuery.each(path,function(i,point){
					pts.push(point.lat() + " "+point.lng());
					bfiCurrLatLngbounds.extend(point);
				});
				pts.push(path[0].lat()+" "+path[0].lng());
				var stringJoin=pts.join(",");
				bfiCurrPoint.val("1|" + stringJoin);
			}
			//jQuery("#zoneId").val("0");
			//jQuery("#locationZonesList").find("option").prop("selected", "");
			//jQuery("#locationzones").val("");
			//jQuery("#locationZonesList").find("option[value='-1']").prop("selected", "selected");
//			jQuery("#mapSearch").prop("checked", "checked");

			bfiCurrForm.find("[name=stateIds],[name=regionIds],[name=cityIds],[name=locationzone],[name=searchterm],[name=searchTermValue]").val("");
			bfiCurrForm.find("[name=searchType]").val("1");
			bfiCurrForm.find(".bfi-mapsearchbtn").addClass("bfi-alternative");
			bfiCurrForm.find(".bfi-mapsearchbtn").removeClass("bfi-alternative4");

		}else{
//			jQuery("#mapSearch").prop("checked", "");
			bfiCurrForm.find("[name=searchType]").val("0");
			bfiCurrForm.find(".bfi-mapsearchbtn").removeClass("bfi-alternative");
			bfiCurrForm.find(".bfi-mapsearchbtn").addClass("bfi-alternative4");
		}
	}

	function drawPoligon() {
		deleteSelectedShape()
//		jQuery(".bfi-select-figure").addClass("unactive");
//		bfiCurrDrawpoligon.removeClass("unactive");
		jQuery(".bfi-select-figure").addClass("bfi-alternative3");
		bfiCurrDrawpoligon.removeClass("bfi-alternative3");
		bfiDrawingManager.setDrawingMode(google.maps.drawing.OverlayType.POLYGON);
		bfiDrawingManager.setMap(bfiMapMapDrawer);
		bfiDrawingManager.setOptions({
			drawingControl: false
		});
	}
	function drawCircle() {
		deleteSelectedShape()
//		jQuery(".bfi-select-figure").addClass("unactive");
//		bfiCurrDrawcircle.removeClass("unactive");
		jQuery(".bfi-select-figure").addClass("bfi-alternative3");
		bfiCurrDrawcircle.removeClass("bfi-alternative3");
		bfiDrawingManager.setDrawingMode(google.maps.drawing.OverlayType.CIRCLE);
		bfiDrawingManager.setMap(bfiMapMapDrawer);
		bfiDrawingManager.setOptions({
			drawingControl: false
		});
	}

	function clearSelection() {
		if (bfiSelectedShape) {
			bfiSelectedShape.setEditable(false);
			bfiSelectedShape = null;
			bfiCurrBtndelete.attr("disabled", "disabled");
			bfiCurrBtndelete.addClass("bfi-not-active");                              
		}
	}
	function setSelection(shape) {
		clearSelection();
		bfiSelectedShape = shape;
		shape.setEditable(true);
		bfiCurrBtndelete.removeAttr("disabled");                              
		bfiCurrBtndelete.removeClass("bfi-not-active");                              
	}
	
	function deleteSelectedShape() {
		if (bfiSelectedShape) {
		//google.maps.event.removeListener(bfiMapMapDrawer, 'mouseup');
		//google.maps.event.removeListener(bfiSelectedShape, 'mouseup');
		
		bfiSelectedShape.setMap(null);
		bfiSelectedShape = null;
		}

		bfiCurrbtnCompleta.hide();

		bfiCurrBtndelete.attr("disabled", "disabled");
		bfiCurrBtndelete.addClass("bfi-not-active");                              

		bfiCurrSpanArea.html("");
		bfiCurrPoint.val("");
	}

		function bfiOpenGoogleMapDrawer(bfiCurrFormId, bfiCurrModID) {
			
			bfiCurrForm = jQuery("#"+bfiCurrFormId);
			bfiCurrPoint = bfiCurrForm.find("input[name='points']").first() ;
			bfiCurrMapDrawer = jQuery("#bfi_MapDrawer"+bfiCurrModID);
			bfiCurrMapCanvas = bfiCurrMapDrawer.find(".bfi-map-canvas").first() ;
			bfiCurrBtndelete = bfiCurrMapDrawer.find(".bfi-btndelete").first() ;
			bfiCurrBtnconfirm = bfiCurrMapDrawer.find(".bfi-btnconfirm").first() ;
			bfiCurrbtnCompleta = bfiCurrMapDrawer.find(".bfi-btnCompleta").first() ;
			bfiCurrAddresssearch = bfiCurrMapDrawer.find(".bfi-map-addresssearch").first() ;
			bfiCurrSpanArea = bfiCurrMapDrawer.find(".bfi-spanarea").first() ;
			bfiCurrDrawpoligon = bfiCurrMapDrawer.find(".bfi-drawpoligon").first() ;
			bfiCurrDrawcircle = bfiCurrMapDrawer.find(".bfi-drawcircle").first() ;
			

			var width = jQuery(window).width()*0.9;
			var height = jQuery(window).height()*0.9;
			if (typeof google === "undefined" || typeof google !== 'object' || typeof google.maps !== 'object'){
				var script = document.createElement("script");
				script.type = "text/javascript";
				script.src = "https://maps.google.com/maps/api/js?key=" + $googlemapsapykey +"&libraries=drawing,places&callback=handleApiReadyMapDrawer";
				document.body.appendChild(script);
			}else if (typeof bfiMapMapDrawer !== 'object'){
//					bfiCurrInizializated = 1;
					handleApiReadyMapDrawer();
			}else{
				drawShape();
			}
			bfiCurrDialog = bfiCurrMapDrawer.dialog({
					autoOpen: false,
					modal: true,
					resize: function( event, ui ) {
						google.maps.event.trigger(bfiMapMapDrawer, 'resize');
						bfiMapMapDrawer.setCenter(bfiMyLatlngMapDrawer);
					},
					height:height,
					width: width,
					fluid: true, //new option
					title: bfiCurrTitlePopupMap,
					dialogClass: 'bfi-dialog bfi-dialog-map',
					close: getShapePath
				});
			bfiCurrDialog.dialog('open');
			bfiCurrMapCanvas.css("height", height-130);
			if (typeof google !== 'undefined' && typeof google === 'object' ){
				if (typeof bfiMapMapDrawer !== 'undefined' && typeof bfiMapMapDrawer === 'object' ){
					google.maps.event.trigger(bfiMapMapDrawer, 'resize');
					bfiMapMapDrawer.setCenter(bfiMyLatlngMapDrawer);
					if (typeof bfiCurrLatLngbounds !== 'undefined' && typeof bfiCurrLatLngbounds === 'object' ){
						bfiMapMapDrawer.fitBounds(bfiCurrLatLngbounds);
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
		try
		{
			if (bfiCurrMapDrawer.length >0 && bfiCurrMapDrawer.dialog( "isOpen" ))
			{
				var wWidth = jQuery(window).width();
				var dWidth = wWidth * 0.9;
				var wHeight = jQuery(window).height();
				var dHeight = wHeight * 0.9;
				bfiCurrMapDrawer.dialog("option", "width", dWidth);
				bfiCurrMapDrawer.dialog("option", "height", dHeight);
				bfiCurrMapDrawer.dialog("option", "position", "center");
				bfiCurrMapCanvas.css("height", dHeight-110);
				if (typeof google !== "undefined")
				{
					if (typeof google === 'object' && typeof google.maps === 'object' && typeof bfiMapMapDrawer !== "undefined"){
						google.maps.event.trigger(bfiMapMapDrawer, 'resize');
						if (typeof bfiMapMapDrawer !== 'undefined' && typeof bfiMapMapDrawer === 'object' ){
							bfiMapMapDrawer.setCenter(bfiMyLatlngMapDrawer);
						}
						if (typeof bfiCurrLatLngbounds !== 'undefined' && typeof bfiCurrLatLngbounds === 'object' ){
							bfiMapMapDrawer.fitBounds(bfiCurrLatLngbounds);
						}
	//					bfiMapMapDrawer.setCenter(bfiMyLatlngMapDrawer);
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
//						bfiMapMapDrawer.setCenter(results[0].geometry.location);
//						var marker = new google.maps.Marker({
//							map: bfiMapMapDrawer,
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