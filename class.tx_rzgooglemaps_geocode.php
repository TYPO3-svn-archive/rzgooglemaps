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

class tx_rzgooglemaps_geocode {

// Functions to read the JavaScript generated Geodata and create saveable inputs
function user_latitude($PA, $fobj) {  
  return '<input class="formField2" name="'.$PA['itemFormElName'].'" value="'.htmlspecialchars($PA['itemFormElValue']).'" id="hidden_latitude" />';
}

function user_longitude($PA, $fobj) {
  return '<input class="formField2" name="'.$PA['itemFormElName'].'" value="'.htmlspecialchars($PA['itemFormElValue']).'" id="hidden_longitude" />';
}

function user_search($PA, $fobj) {  
  global $LANG; 

  $LL = $this->includeLocalLang();    

  // Read LocalLang
  $address = $LANG->getLLL('pi1_address',$LL);
  $go = $LANG->getLLL('pi1_go',$LL);
  $drag = $LANG->getLLL('pi1_drag',$LL);

  return '<form action="#">
  <br /><p>
  <input type="text" name="'.$PA['itemFormElName'].'" id="address" value="'.htmlspecialchars($PA['itemFormElValue']).'" onclick="if (this.value==\''.htmlspecialchars($PA['itemFormElValue']).'\') this.value=\'\'" onblur="if (this.value==\'\') this.value=\''.htmlspecialchars($PA['itemFormElValue']).'\'" style="width: 420px;" />
  <input type="button" onclick="showAddress(document.getElementById(\'address\').value)" value="'.$go.'" />';
}


function geoCodeMap($config) {                                  
  $this->extConf = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['rzgooglemaps']);
  $api_key_ext = $this->extConf['api_key'];

  // Read Flexform	  
  $flexFormContent = t3lib_div::xml2array($config['row']['pi_flexform']);
  
  $longitudeFlex = (is_array($flexFormContent)) ? $flexFormContent['data']['sDEF']['lDEF']['longitude']['vDEF'] : '0';
  $latitudeFlex = (is_array($flexFormContent)) ? $flexFormContent['data']['sDEF']['lDEF']['latitude']['vDEF'] : '0';	  
  
  $api_key = $api_key_ext;

  // Draggable Function thanks to Grzegorz Banka (grzegorz@grzegorzbanka.com)     	             
  $content = '

  <script src="http://maps.google.com/maps?file=api&amp;v=2&amp;key='.$api_key.'" type="text/javascript"></script>       
  <script type="text/javascript">
  //<![CDATA[
  addedGer = false;
  var map;
  
  function load() {
    if (GBrowserIsCompatible()) {
      map = new GMap2(document.getElementById("map"));
      map.setCenter(new GLatLng('.$latitudeFlex.', '.$longitudeFlex.'), 9);
      var eventHandler = new EventHandler(map);	
      geocoder = new GClientGeocoder();
  
      marker = new GMarker(new GLatLng('.$latitudeFlex.','.$longitudeFlex.'),
      {  
        draggable: true,  
        title: \''.$drag.'\'  
      }
    );
    map.addOverlay(marker);
  
    GEvent.addListener(marker,\'dragend\',function()
    {
      zmienStatus(marker.getPoint().lat(),marker.getPoint().lng());
    });
    }
  }
  
  function zmienStatus(lat,len) {
    document.getElementById("hidden_latitude").value=lat;
    document.getElementById("hidden_longitude").value=len;
  }
  
  function showAddress(address) {
    if (geocoder) {
      var staedte = address.split("\n");
      for (var i=0; i<staedte.length; i++) {
        geocoder.getLatLng(
        staedte[i],
        function(point) {
        if (marker) {
          map.removeOverlay(marker);
        } 
        marker = new GMarker(point);
        map.addOverlay(marker);
        map.setCenter(point);
      });
    }
  }
}
  
  function addCustomOverlay(what) {
    map.addOverlay(new GLayer(what));
  }
  
  function EventHandler(objMap) {
    this.map = objMap;
    GEvent.bind(this.map, "moveend", this, this.onMoveEnd);
    GEvent.bind(this.map, "click", this, this.onClick);
    GEvent.bind(this.map, "addoverlay", this, this.onAddoverlay);
  }
  
  EventHandler.prototype.onMoveEnd = function() {
    var center = this.map.getBounds();
  }
  
  EventHandler.prototype.onAddoverlay = function (overlay) {
    point = overlay.getPoint(); 
    document.getElementById("hidden_latitude").value = point.lat();
    document.getElementById("hidden_longitude").value = point.lng();
  }
  //]]>
  </script> 
  </p><br />
  <div id="map" style="width: 465px; height: 300px"></div>
  </form><br />
  <script language="javascript" type="text/javascript">
  window.onload = function () {load(); }
  </script>';

return "".$content."";            

}  

// Include LocalLang         
function includeLocalLang()	{
  $llFile = t3lib_extMgm::extPath('rzgooglemaps').'locallang.xml';
  $LOCAL_LANG = t3lib_div::readLLXMLfile($llFile, $GLOBALS['LANG']->lang);

  return $LOCAL_LANG;
}            
}

if (defined('TYPO3_MODE') && $GLOBALS['TYPO3_CONF_VARS']['XCLASS']['ext/rzgooglemaps/class.tx_rzgooglemaps_geocode.php'])	{
include_once($GLOBALS['TYPO3_CONF_VARS']['XCLASS']['ext/rzgooglemaps/class.tx_rzgooglemaps_geocode.php']);
}
?>