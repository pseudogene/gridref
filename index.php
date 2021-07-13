<?php
require_once('osi.inc');

$server = 'http://localhost/';

header('X-Powered-By: '.'GridRef v1.6 (2021-07-13)');
if (!empty($_GET['reftype']) && !empty($_GET['refs']) && isset($_GET['kml'])) {
 header('Content-type: application/vnd.google-earth.kml+xml; charset=UTF-8');
 header('Content-Disposition: attachment; filename="gridref.kml"');
 print '<'.'?xml version="1.0" encoding="UTF-8"?'.">\n";
?>
<kml xmlns="http://www.opengis.net/kml/2.2" xmlns:gx="http://www.google.com/kml/ext/2.2" xmlns:kml="http://www.opengis.net/kml/2.2" xmlns:atom="http://www.w3.org/2005/Atom">
<Document>
	<name>Convert Irish/British Grid Reference points to WGS84</name>
	<Snippet maxLines="0" maxlines="0"></Snippet>
	<description><![CDATA[Script by <a href="mailto://michael.bekaert@stir.ac.uk">Micha&euml;l Bekaert</a><br />For more information, please see <a href="https://github.com/pseudogene/gridref">GitHub</a> repository.]]></description>
	<Style id="transRedPoly">
		<LineStyle>
			<width>1.5</width>
		</LineStyle>
		<PolyStyle>
			<color>5d0000ff</color>
		</PolyStyle>
	</Style>
	<Style id="nblue">
		<IconStyle>
			<scale>0.9</scale>
			<Icon>
				<href>http://maps.google.com/mapfiles/kml/paddle/blu-blank.png</href>
			</Icon>
			<hotSpot x="32" y="1" xunits="pixels" yunits="pixels"/>
		</IconStyle>
	</Style>
	<Style id="hblue">
		<IconStyle>
			<scale>1.1</scale>
			<Icon>
				<href>http://maps.google.com/mapfiles/kml/paddle/blu-blank.png</href>
			</Icon>
			<hotSpot x="32" y="1" xunits="pixels" yunits="pixels"/>
		</IconStyle>
	</Style>
	<StyleMap id="bluepoint">
		<Pair>
			<key>normal</key>
			<styleUrl>#nblue</styleUrl>
		</Pair>
		<Pair>
			<key>highlight</key>
			<styleUrl>#hblue</styleUrl>
		</Pair>
	</StyleMap>
<?php
 foreach(explode(',',rawurldecode(trim($_GET['refs']))) as $grid) {
  if (preg_match('/^\w{1,2}\d{4,10}/', strtoupper(trim($grid))) && (($os6x = getOSRefFromSixFigureReference(strtoupper(trim($grid)))) !== false)  && ((($os6x -> ostype <= 'IRNATGRID') && ($os6x -> easting <= 400000) && ($os6x -> northing <= 500000)) || (($os6x -> ostype <= 'NATGRID') && ($os6x -> easting <= 700000) && ($os6x -> northing <= 1300000)))) {
   $ref=$os6x->toLatLng();
   $os6x=$os6x->toArray();
   if(!is_object($ref)) {
    $ref[0]->OSGB36ToWGS84();
    $ref[0]=$ref[0]->toArray();
    $ref[1]->OSGB36ToWGS84();
    $ref[1]=$ref[1]->toArray();
   } else {
    $ref->OSGB36ToWGS84();
    $ref=$ref->toArray();
   }
?>
	<Placemark>
		<name><?php print trim($grid); ?></name>
		<description><![CDATA[Grid Reference: <strong><?php print trim($grid). '</strong><br />Latitude: <strong>' . (isset($ref[1])? round($ref[1]['lat'], 5) . ' / ' . round($ref[0]['lat'], 5) : round($ref['lat'], 5) ) . "</strong><br />Longitude: <strong>" . (isset($ref[1])? round($ref[0]['lon'], 5) . ' / ' . round($ref[1]['lon'], 5) : round($ref['lon'], 5) ) . "</strong><br />Accuracy: <strong>+/-" . $os6x['accuracy']; ?>m</strong>]]></description>
		<Snippet maxLines="0" maxlines="0"></Snippet>
<?php if (isset($ref[1])) { ?>
		<Region>
			<LatLonAltBox>
<?php
    print '				<north>' . $ref[0]['lat'] . "</north>\n";
    print '				<south>' . $ref[1]['lat'] . "</south>\n";
    print '				<east>' . $ref[0]['lon'] . "</east>\n";
    print '				<west>' . $ref[1]['lon'] . "</west>\n";
    print "				 <minAltitude>0</minAltitude>\n";
    print '				<maxAltitude>' . $os6x['accuracy'] . "</maxAltitude>\n";
?>
			</LatLonAltBox>
			<Lod>
				<minLodPixels>32</minLodPixels>
				<maxLodPixels>-1</maxLodPixels>
				<minFadeExtent>0</minFadeExtent>
				<maxFadeExtent>0</maxFadeExtent>
			</Lod>
		</Region>
		<styleUrl>#transRedPoly</styleUrl>
		<Polygon>
			<extrude>1</extrude>
			<altitudeMode>relativeToGround</altitudeMode>
			<outerBoundaryIs>
				<LinearRing>
					<coordinates><?php
    print $ref[0]['lon'] . ',' . $ref[1]['lat'] . ',' . $os6x['accuracy'] . ' ';
    print $ref[1]['lon'] . ',' . $ref[1]['lat'] . ',' . $os6x['accuracy'] . ' ';
    print $ref[1]['lon'] . ',' . $ref[0]['lat'] . ',' . $os6x['accuracy'] . ' ';
    print $ref[0]['lon'] . ',' . $ref[0]['lat'] . ',' . $os6x['accuracy'] . ' ';
    print $ref[0]['lon'] . ',' . $ref[1]['lat'] . ',' . $os6x['accuracy'] . ' ';
?></coordinates>
				</LinearRing>
			</outerBoundaryIs>
		</Polygon>
	</Placemark>
	<Placemark>
		<name><?php print trim($grid); ?></name>
		<description><![CDATA[Grid Reference: <strong><?php print trim($grid). '</strong><br />Latitude: <strong>' . round($ref[1]['lat'], 5) . ' / ' . round($ref[0]['lat'], 5) . "</strong><br />Longitude: <strong>" . round($ref[0]['lon'], 5) . ' / ' . round($ref[1]['lon'], 5) . "</strong><br />Accuracy: <strong>+/-" . $os6x['accuracy']; ?>m</strong>]]></description>
		<Snippet maxLines="0" maxlines="0"></Snippet>
		<Region>
			<LatLonAltBox> 
<?php
    print '				<north>' . $ref[0]['lat'] . "</north>\n";
    print '				<south>' . $ref[1]['lat'] . "</south>\n";
    print '				<east>' . $ref[0]['lon'] . "</east>\n";
    print '				<west>' . $ref[1]['lon'] . "</west>\n";
    print "				<minAltitude>0</minAltitude>\n";
    print '				<maxAltitude>' . $os6x['accuracy'] . "</maxAltitude>\n";
?>
			</LatLonAltBox>
			<Lod>
				<minLodPixels>0</minLodPixels>
				<maxLodPixels>32</maxLodPixels>
				<minFadeExtent>0</minFadeExtent>
				<maxFadeExtent>0</maxFadeExtent>
			</Lod>
		</Region>
		<styleUrl>#bluepoint</styleUrl>
		<Point>
			<coordinates><?php print (($ref[0]['lon']+$ref[1]['lon'])/2) . ',' . (($ref[0]['lat']+$ref[1]['lat'])/2); ?>,0</coordinates>
		</Point>
<?php }else { ?>
		<styleUrl>#bluepoint</styleUrl>
		<Point>
			<coordinates><?php print $ref['lon'] . ',' . $ref['lat']; ?>,0</coordinates>
		</Point>
<?php } ?>
	</Placemark>
<?php
  } elseif (preg_match('/^(\w+)\|(-?\d+\.\d+)\|(-?\d+\.\d+)$/', $grid, $matches) && (floatval($matches[2]) >= -90) && (floatval($matches[2]) <= 90) && (floatval($matches[3]) >= -180) && (floatval($matches[3]) <= 180) && (($llobj = new LatLng(floatval($matches[2]),floatval($matches[3]),($matches[1]=='IE'?'IRNATGRID':'NATGRID'))) !== false)) {
   $llobj -> WGS84ToOSGB36();
   $os2 = $llobj -> toOSRef();
   if ((($matches[1]=='IE') && ($os2 -> easting >= 0) && ($os2 -> northing >= 0) && ($os2 -> easting <= 400000) && ($os2 -> northing <= 500000)) || (($matches[1]=='GB') && ($os2 -> easting >= 0) && ($os2 -> northing >= 0) && ($os2 -> easting <= 700000) && ($os2 -> northing <= 1300000))){
    $refg['6x'] = $os2 -> toSixFigureString();
    $refg['10x'] = $os2 -> toTenFigureString();
    $os6x = getOSRefFromSixFigureReference($refg['6x']);
    $ref=$os6x->toLatLng();
    $os6x=$os6x->toArray();
    $ref[0]->OSGB36ToWGS84();
    $ref[0]=$ref[0]->toArray();
    $ref[1]->OSGB36ToWGS84();
    $ref[1]=$ref[1]->toArray();

?>
	<Placemark>
		<name><?php print $refg['6x']; ?></name>
		<description><![CDATA[Grid Reference: <strong><?php print $refg['6x']. '</strong><br />Latitude: <strong>' . (isset($ref[1])? round($ref[1]['lat'], 5) . ' / ' . round($ref[0]['lat'], 5) : round($ref['lat'], 5) ) . "</strong><br />Longitude: <strong>" . (isset($ref[1])? round($ref[0]['lon'], 5) . ' / ' . round($ref[1]['lon'], 5) : round($ref['lon'], 5) ) . "</strong><br />Accuracy: <strong>+/-" . $os6x['accuracy']; ?>m</strong>]]></description>
		<Snippet maxLines="0" maxlines="0"></Snippet>
		<Region>
			<LatLonAltBox> 
<?php
    print '				<north>' . $ref[0]['lat'] . "</north>\n";
    print '				<south>' . $ref[1]['lat'] . "</south>\n";
    print '				<east>' . $ref[0]['lon'] . "</east>\n";
    print '				<west>' . $ref[1]['lon'] . "</west>\n";
    print "				<minAltitude>0</minAltitude>\n";
    print '				<maxAltitude>' . $os6x['accuracy'] . "</maxAltitude>\n";
?>
			</LatLonAltBox>
			<Lod>
				<minLodPixels>32</minLodPixels>
				<maxLodPixels>-1</maxLodPixels>
				<minFadeExtent>0</minFadeExtent>
				<maxFadeExtent>0</maxFadeExtent>
			</Lod>
		</Region>
		<styleUrl>#transRedPoly</styleUrl>
		<Polygon>
			<extrude>1</extrude>
			<altitudeMode>relativeToGround</altitudeMode>
			<outerBoundaryIs>
				<LinearRing>
					<coordinates><?php
    print $ref[0]['lon'] . ',' . $ref[1]['lat'] . ',' . $os6x['accuracy'] . ' ';
    print $ref[1]['lon'] . ',' . $ref[1]['lat'] . ',' . $os6x['accuracy'] . ' ';
    print $ref[1]['lon'] . ',' . $ref[0]['lat'] . ',' . $os6x['accuracy'] . ' ';
    print $ref[0]['lon'] . ',' . $ref[0]['lat'] . ',' . $os6x['accuracy'] . ' ';
    print $ref[0]['lon'] . ',' . $ref[1]['lat'] . ',' . $os6x['accuracy'] . ' ';
?></coordinates>
				</LinearRing>
			</outerBoundaryIs>
		</Polygon>
	</Placemark>
	<Placemark>
		<name><?php print floatval($matches[2]).','.floatval($matches[3]); ?></name>
		<description><![CDATA[Latitude: <strong><?php print floatval($matches[2]) . '</strong><br />Longitude: <strong>'  . floatval($matches[3]) . '</strong><br />Grid: <strong>' . $refg['6x'] . '</strong> (+/-100m)<br />Grid: <strong>' . $refg['10x'] . '</strong> (+/-1m)'; ?><br />]]></description>
		<Snippet maxLines="0" maxlines="0"></Snippet>
		<styleUrl>#bluepoint</styleUrl>
			<Point>
			<coordinates><?php print floatval($matches[3]) . ',' . floatval($matches[2]); ?>,0</coordinates>
		</Point>
	</Placemark>
<?php
   }
  }
 }
?>
  </Document>
</kml>
<?php
} elseif (!empty($_GET['reftype']) && !empty($_GET['refs']) && !isset($_GET['kml'])) {
 header('Content-Type: application/xml; charset=UTF-8');
 print '<'.'?xml version="1.0" encoding="UTF-8"?'.">\n";
 if ($_GET['reftype']=='NATGRID') {
?>
<!DOCTYPE gridref [
	<!ELEMENT gridref (placemark+)>
	<!ELEMENT placemark (osgridref, gridtype, latitude, longitude, latitudemax?, longitudemax?, accuracy)>
	<!ELEMENT osgridref (#PCDATA)>
	<!ELEMENT gridtype (#PCDATA)>
	<!ELEMENT latitude (#PCDATA)>
	<!ELEMENT longitude (#PCDATA)>
	<!ELEMENT latitudemax (#PCDATA)>
	<!ELEMENT longitudemax (#PCDATA)>
	<!ELEMENT accuracy (#PCDATA)>
]>
<gridref>
<?php
  foreach(explode(',',rawurldecode(trim($_GET['refs']))) as $grid) {
   if (preg_match('/^\w{1,2}\d{4,10}/', strtoupper(trim($grid))) && (($os6x = getOSRefFromSixFigureReference(strtoupper(trim($grid)))) !== false)  && ((($os6x -> ostype <= 'IRNATGRID') && ($os6x -> easting <= 400000) && ($os6x -> northing <= 500000)) || (($os6x -> ostype <= 'NATGRID') && ($os6x -> easting <= 700000) && ($os6x -> northing <= 1300000)))) {
    $ref=$os6x->toLatLng();
    $os6x=$os6x->toArray();
    if(!is_object($ref)) {
     $ref[0]->OSGB36ToWGS84();
     $ref[0]=$ref[0]->toArray();
     $ref[1]->OSGB36ToWGS84();
     $ref[1]=$ref[1]->toArray();
    } else {
     $ref->OSGB36ToWGS84();
     $ref=$ref->toArray();
    }
?>
	<placemark>
		<osgridref><?php print trim($grid); ?></osgridref>
		<gridtype><?php print $os6x['grid']; ?></gridtype>
<?php if ( isset($ref[1]) ) { ?>
		<latitude><?php print $ref[0]['lat']; ?></latitude>
		<latitudemax><?php print $ref[1]['lat']; ?></latitudemax>
		<longitude><?php print $ref[0]['lon']; ?></longitude>
		<longitudemax><?php print $ref[1]['lon']; ?></longitudemax>
<?php } else { ?>
		<latitude><?php print $ref['lat']; ?></latitude>
		<longitude><?php print $ref['lon']; ?></longitude>
<?php } ?>
		<accuracy><?php print $os6x['accuracy']; ?></accuracy>
	</placemark>
<?php } } ?>
</gridref>
<?php } elseif ($_GET['reftype']=='GPS') {?>
<!DOCTYPE gps [
	<!ELEMENT gps (placemark+)>
	<!ELEMENT placemark (latitude, longitude, osgridref+)>
	<!ELEMENT latitude (#PCDATA)>
	<!ELEMENT longitude (#PCDATA)>
	<!ELEMENT osgridref (#PCDATA)>
	<!ATTLIST osgridref 
	accuracy CDATA #REQUIRED
	gridtype CDATA #REQUIRED>
]>
<gps>
<?php
  foreach(explode(',',rawurldecode(trim($_GET['refs']))) as $grid) {
   if (preg_match('/^(\w+)\|(-?\d+\.\d+)\|(-?\d+\.\d+)$/', $grid, $matches) && (floatval($matches[2]) >= -90) && (floatval($matches[2]) <= 90) && (floatval($matches[3]) >= -180) && (floatval($matches[3]) <= 180) && (($llobj = new LatLng(floatval($matches[2]),floatval($matches[3]),($matches[1]=='IE'?'IRNATGRID':'NATGRID'))) !== false)) {
    $llobj -> WGS84ToOSGB36();
    $os2 = $llobj -> toOSRef();
    if ((($matches[1]=='IE') && ($os2 -> easting >= 0) && ($os2 -> northing >= 0) && ($os2 -> easting <= 400000) && ($os2 -> northing <= 500000)) || (($matches[1]=='GB') && ($os2 -> easting >= 0) && ($os2 -> northing >= 0) && ($os2 -> easting <= 700000) && ($os2 -> northing <= 1300000))){
     $refg['6x'] = $os2 -> toSixFigureString();
     $refg['10x'] = $os2 -> toTenFigureString();
?>
	<placemark>
		<latitude><?php print floatval($matches[2]); ?></latitude>
		<longitude><?php print floatval($matches[3]); ?></longitude>
		<osgridref accuracy="100m" gridtype="<?php print ($matches[1]=='IE'?'IRNATGRID':'NATGRID'); ?>"><?php print $refg['6x']; ?></osgridref>
		<osgridref accuracy="1m" gridtype="<?php print ($matches[1]=='IE'?'IRNATGRID':'NATGRID'); ?>"><?php print $refg['10x']; ?></osgridref>
	</placemark>
<?php } } ?>
</gps>
<?php
  }
 }
?>
<!-- Thank you for using the 'Convert Grid Reference points to WGS84' Web Service (XML)! -->
<!-- For more information, please see: <?php print $server; ?>gridref/ -->
<?php
}else {
// header('Content-Type: application/xhtml+xml; charset=ISO-8859-1');
 print '<'.'?xml version="1.0" encoding="ISO-8859-1"?'.">\n";
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN"
    "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
<head>
  <meta http-equiv="content-type" content="application/xhtml+xml; charset=ISO-8859-1" />
  <meta name="keywords" content="os, grid, reference, distance, point, grid, WGS84, web service, XML, KML, irish, national, system, ordnance, survey, converter, latitude, osi, longitude, UK, ireland" />
  <title>Convert OS National Grid Reference points to Latitude/Longitude</title>
  <link rel="bookmark" href="<?php print $server; ?>gridref/" title="Convert OS National Grid Reference points to Latitude/Longitude" type="application/xhtml+xml" />
  <script src="<?php print $server; ?>gridref/usableforms.js" type="text/javascript"></script>
  <style type="text/css">
/*<![CDATA[*/
body {
background:white;
color:#333333;
font-family:Verdana, Arial, Helvetica, sans-serif;
font-size:75%;
line-height:1.6;
margin:1em;
}

h1 {
font:italic 4em Garamond, "Times New Roman", Times, serif;
margin-bottom:1em;
margin-top:0;
}

h2 {
color:black;
font-size:1.5em;
}

p {
margin-bottom:.8em;
margin-top:.8em;
}

a {
background:white;
color:#993300;
font-weight:bold;
text-decoration:none;
}

a:visited {
font-weight:normal;
}

a:hover {
text-decoration:underline;
}

img {
float:right;
}

a img {
border:0;
}

label {
display:block;
float:left;
padding-right:20px;
text-align:right;
width:10em;
}

tt {
font-family:"Lucida Console", "Courier New", Courier, monospace;
font-size:1em;
}

form {
border:1px solid #dddddd;
margin:1em;
padding:1em;
}

.result {
background:#eeeeee;
color:#993300;
margin:1em;
padding:1em;
}

.footer {
margin:1em;
text-align:center;
}

.footer img{
float:none;
clear:both;
}
/*]]>*/
  </style>
</head>
<body>
  <div>
    <div>
      <h2>Convert Grid Reference points to Latitude/Longitude</h2>
      <div>
        <p>In general, neither <a href="https://en.wikipedia.org/wiki/Ireland" title="Ireland">Ireland</a> nor <a href="https://en.wikipedia.org/wiki/Great_Britain" title="Great Britain">Great Britain</a> use <a href="https://en.wikipedia.org/wiki/Latitude" title="Latitude">latitude</a> or <a href="https://en.wikipedia.org/wiki/Longitude" title="Longitude">longitude</a> in describing internal geographic locations. Instead <a href="https://en.wikipedia.org/wiki/Grid_reference" title="Grid reference">grid reference</a> systems are in common usage.</p>
        <p>The national grid referencing system was devised by the <a href="https://en.wikipedia.org/wiki/Ordnance_Survey" title="Ordnance Survey">Ordnance Survey</a>, and is heavily used in their survey data, and in maps (whether published by the <a href="https://en.wikipedia.org/wiki/Ordnance_Survey_of_Ireland" title="Ordnance Survey of Ireland">Ordnance Survey of Ireland</a>, the <a href="https://en.wikipedia.org/wiki/Ordnance_Survey_of_Northern_Ireland" title="Ordnance Survey of Northern Ireland">Ordnance Survey of Northern Ireland</a> or commercial map producers) based on those surveys. Additionally grid references are commonly quoted in other publications and data sources, such as guide books or government planning documents.</p>
        <p>OS Grid References are based on 100km grid squares identified by letter-pairs (in Great Britain) or a single letter (in Ireland), followed by digits which locate a point within the grid square, as explained by the <em><a href="https://en.wikipedia.org/wiki/Irish_national_grid_reference_system" title="Irish national grid reference system">Irish national grid reference system</a></em> or <em><a href="https://en.wikipedia.org/wiki/British_national_grid_reference_system" title="British national grid reference system">British national grid reference system</a></em>. 6-digit references provide precision to 100m grid squares; 8 digits to 10m grid squares, and 10 digits to 1m.</p>
<?php
  if (!empty($_POST['reftype'])  && ($_POST['reftype']=='NATGRID') && !empty($_POST['gridref']) && preg_match('/^\w{1,2}\d{4,10}/', strtoupper(trim($_POST['gridref']))) && (($os6x = getOSRefFromSixFigureReference(strtoupper(trim($_POST['gridref'])))) !== false)  && ((($os6x -> ostype <= 'IRNATGRID') && ($os6x -> easting <= 400000) && ($os6x -> northing <= 500000)) || (($os6x -> ostype <= 'NATGRID') && ($os6x -> easting <= 700000) && ($os6x -> northing <= 1300000))) ){
    $ref=$os6x->toLatLng();
    $os6x=$os6x->toArray();
    if(!is_object($ref)) {
      $ref[0]->OSGB36ToWGS84();
      $ref[0]=$ref[0]->toArray();
      $ref[1]->OSGB36ToWGS84();
      $ref[1]=$ref[1]->toArray();
    } else {
      $ref->OSGB36ToWGS84();
      $ref=$ref->toArray();
    }
    $url= 'reftype=' . trim($_POST['reftype'])  . '&amp;refs=' . trim($_POST['gridref']);
    print '        <p class="result"><a href="' . $server . 'gridref/?kml=true&amp;' . $url . '"><img class="right" src="' . $server . 'gridref/gridref.png" alt="Download Google Earth KML file for ' . trim($_POST['gridref']) . '" /></a>Latitude: ' . (isset($ref[1])? round($ref[0]['lat'], 5) . ' / ' . round($ref[1]['lat'], 5) : round($ref['lat'], 5) ) . "<br />\n        Longitude: " . (isset($ref[1])? round($ref[0]['lon'], 5) . ' / ' . round($ref[1]['lon'], 5) : round($ref['lon'], 5) ) . "<br />\n        Accuracy: +/-" . $os6x['accuracy'] . "m</p>\n";
  } elseif (!empty($_POST['reftype']) && !empty($_POST['country']) && ($_POST['reftype']=='GPS') && isset($_POST['lat']) && isset($_POST['lon']) && (floatval($_POST['lat']) >= -90) && (floatval($_POST['lat']) <= 90) && (floatval($_POST['lon']) >= -180) && (floatval($_POST['lon']) <= 180) && (($llobj = new LatLng(floatval($_POST['lat']),floatval($_POST['lon']), ($_POST['country']=='IE'?'IRNATGRID':'NATGRID'))) !== false) ){
    $llobj -> WGS84ToOSGB36();
    $os2 = $llobj -> toOSRef();
    if ((($_POST['country']=='IE') && ($os2 -> easting >= 0) && ($os2 -> northing >= 0) && ($os2 -> easting <= 400000) && ($os2 -> northing <= 500000)) || (($_POST['country']=='GB') && ($os2 -> easting >= 0) && ($os2 -> northing >= 0) && ($os2 -> easting <= 700000) && ($os2 -> northing <= 1300000))){
      $ref[1] = $os2 -> toSixFigureString();
      $ref[2] = $os2 -> toTenFigureString();
      $url= 'reftype=' . trim($_POST['reftype'])  . '&amp;refs=' . trim($_POST['country']) . '|' . floatval($_POST['lat']) .'|' . floatval($_POST['lon']);
      print '        <p class="result"><a href="' . $server . 'gridref/?kml=true&amp;' . $url . '"><img class="right" src="' . $server . 'gridref/gridref.png" alt="Download Google Earth KML file for ' . trim($_POST['lat']) . ','. trim($_POST['lon']) .'" /></a>Grid. reference: ' . $ref[1] . " (100 m)<br />\nGrid. reference: " . $ref[2] . " (1 m)</p>\n";
    }
  }
?>
        <form action="<?php print $server; ?>gridref/" method="post">
          <div>
            <label for="reftype">Grid Type</label> <select name="reftype" id="reftype">
              <option value="NATGRID" rel="NATGRID">
                Irish or British national grid reference system
              </option>
              <option value="GPS" rel="GPS">
                Global Positioning System (decimal GPS)
              </option>
            </select><br />
            <div rel="NATGRID">
              <label for="gridref">Grid Ref.</label> <input name="gridref" id="gridref" size="12" value="O007727" /><br />
            </div>
            <div rel="GPS">
              <label for="country">Area</label> <select name="country" id="country">
                <option value="IE">
                  Ireland
                </option>
                <option value="GB">
                  Great Britain
                </option>
              </select><br />
              <label for="lat">Latitude (dec.)</label> <input name="lat" id="lat" size="12" value="53.694901" /><br />
              <label for="lon">Longitude (dec.)</label> <input name="lon" id="lon" size="12" value="-6.475464" /><br />
            </div>
            <input type="submit" value="convert" />
          </div>
        </form>
        <p>The Ordnance Survey (UK) grid is a <a href="https://en.wikipedia.org/wiki/Transverse_Mercator" title="Transverse Mercator">Transverse Mercator</a> projection (with origin at 49&deg;N, 2&deg;W) based on the Airy 1830 ellipsoid using the <a href="https://en.wikipedia.org/wiki/OSGB36" title="OSGB36">OSGB36</a> datum. Ordnance Survey of Ireland grid is also a <a href="https://en.wikipedia.org/wiki/Transverse_Mercator" title="Transverse Mercator">Transverse Mercator</a> projection (with origin at 53&deg;30&#39;N, 8&deg;W) based on a modified Airy 1830 ellipsoid using the OSGB1936 modified datum. Note that GPS is based on <a href="https://en.wikipedia.org/wiki/WGS84" rel="external">WGS84</a>/GRS80, which can vary from OSGB36 by as much as 120m or 6&quot; or arc. The latitude/longitude given here is converted in WGS84.</p>
      </div>
<?php
  if (!empty($url)) {
?>
      <h2>API / Web Service</h2>
      <div>
        <p>Want to automate convertion queries? Try out the API web service. Get an XML response, or an KML output to requests sent to:</p>
<?php
    print '        <p class="result">URL (google earth link): <tt>' . $server . 'gridref/?kml=true&amp;' . $url . '</tt><br />XML response: <tt>' . $server . 'gridref/?' . $url . "</tt></p>\n      </div>\n";
  }
?>
    </div>
    <div class="footer">
      <small>- copyright 2008-<?php print date('Y'); ?> Micha&euml;l Bekaert -
      <br />GPL-3.0 License - Source code on <a href="https://github.com/pseudogene/gridref">GitHub</a></small>
    </div>
  </div>
</body>
</html>
<?php } ?>
