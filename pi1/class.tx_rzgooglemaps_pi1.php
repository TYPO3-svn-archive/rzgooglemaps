<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2009-2010 Raphael Zschorsch <rafu1987@gmail.com>
*  All rights reserved
*
*  This script is part of the TYPO3 project. The TYPO3 project is
*  free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; either version 2 of the License, or
*  (at your option) any later version.
*
*  The GNU General Public License can be found at
*  http://www.gnu.org/copyleft/gpl.html.
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/
/**
* [CLASS/FUNCTION INDEX of SCRIPT]
*
* Hint: use extdeveval to insert/update function index above.
*/
require_once(PATH_tslib.'class.tslib_pibase.php');
/**
* Plugin 'Google Maps' for the 'rzgooglemaps' extension.
*
* @author	Raphael Zschorsch <rafu1987@gmail.com>
* @package	TYPO3
* @subpackage	tx_rzgooglemaps
*/

class tx_rzgooglemaps_pi1 extends tslib_pibase {
  var $prefixId      = 'tx_rzgooglemaps_pi1';		// Same as class name
  var $scriptRelPath = 'pi1/class.tx_rzgooglemaps_pi1.php';	// Path to this script relative to the extension dir.
  var $extKey        = 'rzgooglemaps';	// The extension key.
  var $pi_checkCHash = true;   

// Show route planning only if checkbox is true
function routenPlaner() {
  $this->pi_loadLL();	
  
  // Read Flexform
  $this->pi_initPIflexForm();
  $address = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'address', 'description');
  $route_act = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'route', 'description');
  $advanced = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'display_type', 'display');
  
  if ($route_act == 1 && $advanced == 0) {
    $route = "<div id=\"mapsearchcont\"><div id=\"mapsearchnav\"><a class=\"routebold\" href=\"#\" onclick=\"switchContent(\'sub1\');selected(this); return false;\">".$this->pi_getLL('tohere')."</a> <a href=\"#\" onclick=\"switchContent(\'sub2\');selected(this); return false;\">".$this->pi_getLL('fromhere')."</a></div><div id=\"sub1\"><form name=\"search_route\" class=\"rzgooglemaps_form\" method=\"get\" action=\"http://maps.google.com\" target=\"_blank\">' + '<input name=\"saddr\" type=\"text\" id=\"saddr\" value=\"\">' + '<input name=\"daddr\" type=\"hidden\" id=\"daddr\" value=\"".$address."\">' + '<input type=\"submit\" name=\"Submit\" value=\"".$this->pi_getLL('routeSubmit')."\"></form></div><div id=\"sub2\" class=\"mapsearchhide\"><form name=\"search_route\" class=\"rzgooglemaps_form\" method=\"get\" action=\"http://maps.google.com/\" target=\"_blank\">' + '<input name=\"daddr\" type=\"text\" id=\"daddr\" value=\"\">' + '<input name=\"saddr\" type=\"hidden\" id=\"saddr\" value=\"".$address."\">' + '<input type=\"submit\" name=\"Submit\" value=\"".$this->pi_getLL('routeSubmit')."\"></form></div></div>";
  }

  else if ($route_act == 1 && $advanced == 1) {
    $route = "<div id=\"mapsearchcont\"><div id=\"mapsearchnav\"><a class=\"routebold\" href=\"#\" onclick=\"switchContent(\'sub1\');selected(this); return false;\">".$this->pi_getLL('tohere')."</a> <a href=\"#\" onclick=\"switchContent(\'sub2\');selected(this); return false;\">".$this->pi_getLL('fromhere')."</a></div><div id=\"sub1\"><form action=\"#\" onsubmit=\"setDirections(this.from.value, this.to.value); return false\">' + '<input name=\"from\" type=\"text\" id=\"fromAddress\" value=\"\">' + '<input name=\"to\" type=\"hidden\" id=\"toAddress\" value=\"".$address."\">' + '<input type=\"submit\" name=\"Submit\" value=\"".$this->pi_getLL('routeSubmit')."\"></form></div><div id=\"sub2\" class=\"mapsearchhide\"><form action=\"#\" onsubmit=\"setDirections(this.from.value, this.to.value); return false\">' + '<input name=\"from\" type=\"hidden\" id=\"fromAddress\" value=\"".$address."\">' + '<input name=\"to\" type=\"text\" id=\"toAddress\" value=\"\">' + '<input type=\"submit\" name=\"Submit\" value=\"".$this->pi_getLL('routeSubmit')."\"></form></div></div>";
  } 

  else {
    $route = "";
  } 
     
  return $route;  
}

// Show InfoWindow only if checkbox is true  
function infoWindow() {
  $this->pi_initPIflexForm();
  $info_window = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'text_act', 'description');  
  $text = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'text', 'description');

  // stdWrap Function for the description
  $text_wrap = $this->cObj->stdWrap($text,$this->conf['textStdWrap.']);

  if ($info_window == 1) {
    $window_begin = "var html = '";
    $window_end = "';";      
    $window_replace = "".preg_replace('/\r\n|\r|\n/', '', $text_wrap)."";
    $window_replace_new = str_replace("'","\'",$window_replace);
    $window_act = "".$window_begin."".$window_replace_new."".$this->routenPlaner()."".$window_end."";             
  }
  
  else {
    $window_act = "";
  }  
             
  return $window_act;
}

// Function to add the InfoWindow JS, if activated
function showInfoWindow() {
  $this->pi_initPIflexForm();
  $info_window = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'text_act', 'description');
  
  if ($info_window == '1') {
    $show_window = 'marker.openInfoWindowHtml(html);';
  }  
  
  else {
    $show_window = '';  
  }
  
  return $show_window;  
}

// Function to include the map search JavaScripts
function includeMapSearch() {
  $this->pi_initPIflexForm();
  $map_search = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'map_search', 'options');

  if ($map_search == 1) {
    return "<script src=\"http://www.google.com/uds/api?file=uds.js&amp;v=1.0\" type=\"text/javascript\"></script>
    <script src=\"typo3conf/ext/rzgooglemaps/res/js/gmlocalsearch.js\" type=\"text/javascript\"></script>
    <link rel=\"stylesheet\" type=\"text/css\" href=\"typo3conf/ext/rzgooglemaps/res/css/gsearch.css\" />
    <link rel=\"stylesheet\" type=\"text/css\" href=\"typo3conf/ext/rzgooglemaps/res/css/gmlocalsearch.css\" />";  
  }

  else {
 
  }
}

function includeRoutejs() {
  $this->pi_initPIflexForm();
  $route = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'route', 'description');
  
  if ($route == 1) {
    return "<script src=\"typo3conf/ext/rzgooglemaps/res/js/maproute.js\" type=\"text/javascript\"></script>";  
  }
  
  else {
  
  }
}

// Function to include the map search Form
function includeMapForm() {
  $this->pi_initPIflexForm();
  $map_search = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'map_search', 'options');
  $map_search_anchor = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'map_search_anchor', 'options');
  
  if ($map_search == 1) {
    return "map.addControl(new google.maps.LocalSearch(), new GControlPosition(".$map_search_anchor.", new GSize(10,20)));";  
  }
  
  else {
  
  }
}

// Function to initialize the map search
function initMapSearch() {
  $this->pi_initPIflexForm();
  $map_search = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'map_search', 'options');
  
  if ($map_search == 1) {
    return "GSearch.setOnLoadCallback(initialize);";  
  }
  
  else {
  
  }
}

function wikiDe() {
  $this->pi_initPIflexForm();
  $wiki_de = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'wikide', 'options');
  
  if ($wiki_de == 1) {
    return "var wikide = new GLayer(\"org.wikipedia.de\");
    map.addOverlay(wikide);";
  } 
    
  else {
  
  }
}

function wikiEn() {
  $this->pi_initPIflexForm();
  $wiki_en = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'wikien', 'options');
  
  if ($wiki_en == 1) {
    return "var wikien = new GLayer(\"org.wikipedia.en\");
    map.addOverlay(wikien);";
  }
  else {
  
  }
}

function panoramio() {
  $this->pi_initPIflexForm();
  $panoramio = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'panoramio', 'options');
  
  if ($panoramio == 1) {
    return "var panoramio = new GLayer(\"com.panoramio.all\");
    map.addOverlay(panoramio);";
  }
  
  else {
  }
}     

// Load JavaScript
function includeJavaScript() { 
  $this->extConf = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['rzgooglemaps']);
  $api_key_ext = $this->extConf['api_key'];

  $this->pi_initPIflexForm();
  $api_key_ff = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'api_key', 'sDEF');
  $latitude = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'latitude', 'sDEF');
  $longitude = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'longitude', 'sDEF');
  $zoom = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'zoom', 'sDEF');
  $type = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'maptype', 'options');
  $controls = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'controls', 'options');
  $controls_top = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'controls_top', 'options');
  $marker_show = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'marker_show', 'options');
  $marker_color = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'marker_color', 'options');
  $marker_type = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'marker_type', 'options');
  $draggable = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'draggable', 'options');
  $bouncy = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'bouncy', 'options');
  $bounce_gravity = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'bounce_gravity', 'options');
  $advanced = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'display_type', 'display');
  $address = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'address', 'description');
  $directlang = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'directlang', 'advanced_view');
  $not_in_map = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'not_in_map', 'advanced_view');
  $info_window = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'text_act', 'description'); 

  if ($api_key_ff == '') {
    $api_key = $api_key_ext;
  }
      
  else {
    $api_key = $api_key_ff;
  }

  // Map Search Functions   
  $mapsearchShow = $this->includeMapSearch();
  $mapsearchForm = $this->includeMapForm();
  $mapsearchInit = $this->initMapSearch();
  $routeJs = $this->includeRoutejs();

  // Extra Functions
  $wikide = $this->wikiDe();
  $wikien = $this->wikiEn();
  $pano = $this->panoramio();

  $infoWindowShow = $this->infoWindow();
  $window_js = $this->showInfoWindow();

  // Normal Route Planning
  if($advanced == '0' && $not_in_map == '0') {  

    $js .= "
    <script src=\"http://maps.google.com/maps?file=api&amp;v=2&amp;key=".$api_key."\" type=\"text/javascript\"></script>
    ".$routeJs."

    ".$mapsearchShow."	  

    <script type=\"text/javascript\">
    
    function initialize() {
      if (GBrowserIsCompatible()) {
        var map = new GMap2(document.getElementById(\"map_canvas\"));
    
        var iconColor = new GIcon(); 
        iconColor.image = 'typo3conf/ext/rzgooglemaps/res/images/".$marker_color."-".$marker_type.".png';
        iconColor.shadow = 'typo3conf/ext/rzgooglemaps/res/images/shadow-".$marker_type.".png';
        iconColor.iconSize = new GSize(32, 32);
        iconColor.shadowSize = new GSize(59, 32);
        iconColor.iconAnchor = new GPoint(6, 20);
        iconColor.infoWindowAnchor = new GPoint(5, 1);
    
        var options = {
          draggable: $draggable, 
          icon: iconColor, 
          bouncy: $bouncy, 
          bounceGravity: $bounce_gravity, 
          hide: $marker_show
        };
    
        var marker = new GMarker(new GLatLng(".$latitude.", ".$longitude."), options);
        $infoWindowShow                  
    
        $controls
        $controls_top
        $mapsearchForm 
        
        $wikide
        $wikien
        $pano       
    
        map.setMapType(".$type.");
        var point = new GLatLng(".$latitude.", ".$longitude.");
        var counter = 0;
        map.setCenter(point, ".$zoom.");
        
        map.addOverlay(marker);
        $window_js
        GEvent.addListener(marker, \"click\", function() {
          $infoWindowShow 
        $window_js            
        });                                  
      }                                         
    }
    $mapsearchInit	  
    </script>    
    <script language=\"javascript\" type=\"text/javascript\">
      window.onload = function () {initialize(); }
    </script>";
    
    // Activate JavaScript
    $GLOBALS['TSFE']->additionalHeaderData[$this->extKey] = $js;   
  }

  // Advanced Route Planning 
  // Kudos to Christof Hagedorn (christof.hagedorn@kinea.de), having the idea for the implementation.
  else if($advanced == '1' && $not_in_map == '0') {

    $js .= '
    <script src="http://maps.google.com/maps?file=api&amp;v=2&amp;key='.$api_key.'" type="text/javascript"></script>
    '.$routeJs.'
    '.$mapsearchShow.'
    <script type="text/javascript"> 
    
    var map;
    var gdir;
    var geocoder = null;
    var addressMarker;
    
    function initialize() {
      if (GBrowserIsCompatible()) {      
        map = new GMap2(document.getElementById("map_canvas"));
    
        var iconColor = new GIcon(); 
        iconColor.image = \'typo3conf/ext/rzgooglemaps/res/images/'.$marker_color.'-'.$marker_type.'.png\';
        iconColor.shadow = \'typo3conf/ext/rzgooglemaps/res/images/shadow-'.$marker_type.'.png\';
        iconColor.iconSize = new GSize(32, 32);
        iconColor.shadowSize = new GSize(59, 32);
        iconColor.iconAnchor = new GPoint(6, 20);
        iconColor.infoWindowAnchor = new GPoint(5, 1);
    
        var options = {
          draggable: '.$draggable.', 
          icon: iconColor, 
          bouncy: '.$bouncy.', 
          bounceGravity: '.$bounce_gravity.',
          hide: '.$marker_show.' 
        };
    
        var marker = new GMarker(new GLatLng('.$latitude.', '.$longitude.'), options);
        '.$infoWindowShow.'
    
        '.$controls.'
        '.$controls_top.'
        '.$mapsearchForm.' 
        
        '.$wikide.'
        '.$wikien.'
        '.$pano.'    
        
        map.setMapType('.$type.');    		
        var point = new GLatLng('.$latitude.', '.$longitude.');
        var counter = 0;
        map.setCenter(point, '.$zoom.');
        map.addOverlay(marker);   
    
        marker.openInfoWindowHtml(html);
        GEvent.addListener(marker, "click", function() {
          '.$infoWindowShow.' 
          marker.openInfoWindowHtml(html);              
        });                 
        
        gdir = new GDirections(map, document.getElementById("directions"));
        GEvent.addListener(gdir, "load", onGDirectionsLoad);
        GEvent.addListener(gdir, "error", handleErrors);
      }
    }
    
    function setDirections(fromAddress, toAddress, locale) {
      gdir.load("from: " + fromAddress + " to: " + toAddress,
      { "locale": "'.$directlang.'" });
    }                          
    
    function handleErrors(){
      if (gdir.getStatus().code == G_GEO_UNKNOWN_ADDRESS)
      alert("'.$this->pi_getLL('error_one').'\nError code: " + gdir.getStatus().code);
      else if (gdir.getStatus().code == G_GEO_SERVER_ERROR)
      alert("'.$this->pi_getLL('error_two').'\n Error code: " + gdir.getStatus().code);
      
      else if (gdir.getStatus().code == G_GEO_MISSING_QUERY)
      alert("'.$this->pi_getLL('error_three').'\n Error code: " + gdir.getStatus().code);
      
      else if (gdir.getStatus().code == G_GEO_BAD_KEY)
      alert("'.$this->pi_getLL('error_four').' \n Error code: " + gdir.getStatus().code);
      
      else if (gdir.getStatus().code == G_GEO_BAD_REQUEST)
      alert("'.$this->pi_getLL('error_five').'\n Error code: " + gdir.getStatus().code);
      
      else alert("'.$this->pi_getLL('error_six').'");
    }
    
    function onGDirectionsLoad(){ 
    
    }
    
    '.$mapsearchInit.'
    </script>
    <script language="javascript" type="text/javascript">
      window.onload = function () {initialize(); }
    </script>';
    
    // Activate JavaScript
    $GLOBALS['TSFE']->additionalHeaderData[$this->extKey] = $js; 
  }

  else if($advanced == '1' && $not_in_map == '1') {

  }         
}

// FE Rendering       
function main($content, $conf) {
  $this->conf = $conf;
  $this->pi_setPiVarDefaults();
  $this->pi_loadLL();	

  $this->pi_initPIflexForm();
  $width = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'width', 'sDEF');
  $height = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'height', 'sDEF');
  $advanced = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'display_type', 'display');
  $directwidth = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'directwidth', 'advanced_view');
  $address = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'address', 'description');
  $template_ff = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'template_file', 'advanced_view');  
  $not_in_map = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'not_in_map', 'advanced_view');

  if($width == '') {
    $width = '200px';
  }
  
  else {
    $width = $width;
  }                 

  if($height == '') {
    $height = '200px';
  } 
                  
  else {
    $height = $height;
  }

  if($directwidth == '') {
    $directwidth = '200px';
  }

  else {
    $directwidth = $directwidth;
  }

  if($advanced <= '0' && $not_in_map <= '0') {
    $map = '<div id="map_canvas" style="width:'.$width.'; height:'.$height.';"></div>';
  }
  
  else if($advanced == '1') {
    $search = $this->routenPlaner();
    
      if($template_ff == '') {
        // Get Template defined in TS
        // Template Path
        $templatePath = $this->conf['templatePath'];

        if($not_in_map <= '0') {
          // Template File
          $templateType = $this->conf['templateFile'];
        }
        
        else if($not_in_map == '1') {
          // Template File
          $templateType = $this->conf['templateFileRoute'];
        }

        // Load Template		
        $vTemplateFile = $templateType;
        $vTemplateFile = $vTemplateFile ? (''.$templatePath.'' . $vTemplateFile) : trim($this->conf['templateFile']);
        $this->lConf['templateFile'] = $vTemplateFile; 	
        $this->templateCode = $this->cObj->fileResource($this->lConf['templateFile']);
      }

      else if($template_ff != '') {
        // Get Template defined in FF
        // Template Path
        $templatePath = 'uploads/tx_rzgooglemaps/';
        // Template File
        $templateType = $template_ff;
        
        // Load Template		
        $vTemplateFile = $templateType;
        $vTemplateFile = $vTemplateFile ? (''.$templatePath.'' . $vTemplateFile) : trim($this->conf['templateFile']);
        $this->lConf['templateFile'] = $vTemplateFile; 	
        $this->templateCode = $this->cObj->fileResource($this->lConf['templateFile']);
      }

    $template['total'] = $this->cObj->getSubpart($this->templateCode,'###TEMPLATE###');

    // Set Markers
    $markerArray['###DIRECTION_STYLES###'] = 'style="width:'.$directwidth.';"';
    $markerArray['###MAP_STYLES###'] = 'style="width:'.$width.';height:'.$height.';"';
    $markerArray['###DIRECTIONS_HEADLINE###'] = $this->pi_getLL('directions_headline');
    $markerArray['###MAP_HEADLINE###'] = $this->pi_getLL('map_headline');

    $map = $this->cObj->substituteMarkerArrayCached($template['total'], $markerArray, array());
  }  

  $content = ''.$this->includeJavaScript().'

  '.$map.'

  ';

  return $this->pi_wrapInBaseClass($content);
}  	
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/rzgooglemaps/pi1/class.tx_rzgooglemaps_pi1.php'])	{
include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/rzgooglemaps/pi1/class.tx_rzgooglemaps_pi1.php']);
}

?>