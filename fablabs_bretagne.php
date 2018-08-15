<?PHP
	/*	La carte des FabLabs & autres lieux de dissémination des usages numériques en Bretagne
		======================================================================================
		Auteur : Guillaume Rouan | @grouan
		Blog   : https://guillaume-rouan.net/blog/
		--------------------------------------------------------------------------------------
		=> La carte des FabLabs de Bretagne : guillaume-rouan.net/blog/2015/10/10/carte-des-fablab-de-bretagne/
		=> La liste des FabLabs de Bretagne : guillaume-rouan.net/blog/fablabzh_map/fablabs_bzh_liste.php
		=> Sources                          : github.com/grouan/bzh_fablab
		--------------------------------------------------------------------------------------
	*/

	// Ouverture du fichier GEOjson depuis Github
	$fichier = 'https://raw.githubusercontent.com/grouan/bzh_fablab/master/map_bzh_fablab.geojson';
	$fp = fopen ($fichier, "r");
    	$contenu_du_fichier = stream_get_contents($fp); // en cas de fichier source local, utiliser : fread ($fp, filesize($fichier));
	fclose ($fp);
	
	// Décodage
	$json = json_decode ($contenu_du_fichier, true);
?>

<html>
<head>

	<!-- 
       
       =============================================================================
       	CARTE DES FABLABS & TIERS-LIEUX DE BRETAGNE
       =============================================================================
       	Auteur : Guillaume ROUAN | https://guillaume-rouan.net/blog/ | @grouan
       -----------------------------------------------------------------------------
       	Licence : CC BY
              Création : Octobre 2015 | v.4
              Technos : OpenStreetMap + Mapbox + Leaflet
              Thématiques : FabLab, MakerSpace, HackerSpace, FrenchTech,
              		Cantine numérique, Tiers Lieux, Usages numériques,
              		Formation / Recherche
       -----------------------------------------------------------------------------
       
       -->
	<title>Carte des Fablabs & Tiers-Lieux de Bretagne</title>
       <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
       
       <!-- Feuille de style Leaflet + zone de carte -->
	   <link rel="stylesheet" href="https://unpkg.com/leaflet@1.3.3/dist/leaflet.css" />
       <style type="text/css">
		#map { 
			margin:0px;
			height:100%;
		}
	</style>
       
       <!-- Bibliothèques Javascript Leaflet + OpenLayers -->
       <script src="https://unpkg.com/leaflet@1.3.3/dist/leaflet.js"></script>
       <script src="https://cdn.rawgit.com/openlayers/openlayers.github.io/master/en/v5.1.3/build/ol.js"></script>
       
       <!-- Paramétrages de la couche Mapbox + légende de la carte -->
       <script>
		ACCESS_TOKEN = 'XXX'; /* Voir https://www.mapbox.com pour en créer un */
		
		CM_ATTR = 'Donn&eacute;es carte &copy; contributeurs <a href="https://openstreetmap.org">OpenStreetMap</a>, ' +
			'<a href="https://creativecommons.org/licenses/by-sa/2.0/fr/">CC-BY-SA</a>, ' +
			'Imagery © <a href="https://cloudmade.com">CloudMade</a>';
		
		CM_URL = 'https://{s}.tile.cloudmade.com/d4fc77ea4a63471cab2423e66626cbb6/{styleId}/256/{z}/{x}/{y}.png';
		
		MB_ATTR = '<a href="https://openstreetmap.org" target="_blank">OpenStreetMap</a> + ' +
			'<a href="https://mapbox.com" target="_blank">Mapbox</a> | ' +
			'<a href="https://guillaume-rouan.net/blog/" target="_blank">Guillaume Rouan</a> <a href="https://creativecommons.fr/licences/" target="_blank">CC BY</a>';
		
		MB_URL = 'https://api.tiles.mapbox.com/v4/{id}/{z}/{x}/{y}.png?access_token=' + ACCESS_TOKEN;
		
		OSM_URL = 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png';
		
		OSM_ATTRIB = '&copy; contributeurs <a href="https://openstreetmap.org/copyright">OpenStreetMap</a> ';
	</script>
       
</head>

<body style="margin:0px;padding:0px;">
	
  <!-- LA CARTE -->
  <div id="map"></div>
  
  <!-- LES PARAMETRES DE LA CARTE -->
  <script>
	
/* === CENTRAGE DE LA CARTE SUR LA BRETAGNE === */
	var map = L.map('map').setView([48.05, -3.00], 8); /* zoom = 8 */
	
/* === ID MAPBOX === */
	L.tileLayer(MB_URL, {attribution: MB_ATTR, id: 'YYY'}).addTo(map);
	
/* === PARAMETRAGE DES MARKERS === */
	var 	LeafIcon = L.Icon.extend({ options: { iconSize: [35, 35]} });
	var 	hakerspace_Icon = new LeafIcon({iconUrl: 'https://raw.githubusercontent.com/grouan/bzh_fablab/master/img/osm_marker_hakerspace.png'}), /* HackerSpace */
		makerspace_Icon = new LeafIcon({iconUrl: 'https://raw.githubusercontent.com/grouan/bzh_fablab/master/img/osm_marker_makerspace.png'}), /* MakerSpace */
		tierslieu_Icon = new LeafIcon({iconUrl: 'https://raw.githubusercontent.com/grouan/bzh_fablab/master/img/osm_marker_tierslieu.png'}); /* TiersLieu */
		usages_Icon = new LeafIcon({iconUrl: 'https://raw.githubusercontent.com/grouan/bzh_fablab/master/img/osm_marker_usages.png'}); /* Usages */
		frenchtech_Icon = new LeafIcon({iconUrl: 'https://raw.githubusercontent.com/grouan/bzh_fablab/master/img/osm_marker_frenchtech.png'}); /* FrenchTech */
		fablabMIT_Icon = new LeafIcon({iconUrl: 'https://raw.githubusercontent.com/grouan/bzh_fablab/master/img/osm_marker_fablabMIT.png'}); /* FabLab MIT */
		fablab_Icon = new LeafIcon({iconUrl: 'https://raw.githubusercontent.com/grouan/bzh_fablab/master/img/osm_marker_fablab.png'}); /* FabLab */
		cantine_Icon = new LeafIcon({iconUrl: 'https://raw.githubusercontent.com/grouan/bzh_fablab/master/img/osm_marker_cantine.png'}); /* Cantine */
		formation_Icon = new LeafIcon({iconUrl: 'https://raw.githubusercontent.com/grouan/bzh_fablab/master/img/osm_marker_formation.png'}); /* Formation / Recherche
	
/* === AFFICHAGE DES MARQUEURS GEOLOCALISÉS === */
	
<?PHP	
	// Affichage
	foreach ($json['features'] as $data) {
		
		// Paramétrage
		$longitude = $data['geometry']['coordinates'][0];
		$latitude = $data['geometry']['coordinates'][1];
		if ($data['properties']['web'] != "") { $web = '<a href=\''.$data['properties']['web'].'\' target=\'_blank\'><img src=\'https://raw.githubusercontent.com/grouan/bzh_fablab/master/img/ico_30x30px_WEB.png\' title=\'Visitez '.$data['properties']['web'].'\' style=\'border-radius:3px;margin:3px;\' /></a>'; } else { $web = ''; }
		if ($data['properties']['facebook'] != "") { $facebook = '<a href=\'https://facebook.com/'.$data['properties']['facebook'].'\' target=\'_blank\'><img src=\'https://raw.githubusercontent.com/grouan/bzh_fablab/master/img/ico_30x30px_FB.png\' title=\'Suivez '.$data['properties']['facebook'].'\' style=\'border-radius:3px;margin:3px;\' /></a>'; } else { $facebook = ''; }
		if ($data['properties']['twitter'] != "") { $twitter = '<a href=\'https://twitter.com/'.$data['properties']['twitter'].'\' target=\'_blank\'><img src=\'https://raw.githubusercontent.com/grouan/bzh_fablab/master/img/ico_30x30px_TW.png\' title=\'Suivez '.$data['properties']['twitter'].'\' style=\'border-radius:3px;margin:3px;\' /></a>'; } else { $twitter = ''; }
		
		// Création des marqueurs
		echo 'L.marker(['.$latitude.','.$longitude.'], {icon: '.$data['properties']['structure'].'_Icon}).bindPopup("<b style=\'font-size:1.2em;text-transform:uppercase;\'>'.$data['properties']['name'].'</b><br />'.$data['properties']['adresse'].'<br />'.$data['properties']['cp'].' '.$data['properties']['ville'].'<br />'.$web.' '.$facebook.' '.$twitter.'").addTo(map);';
		echo "\n";
		
	}
?>
     
	
	</script>
       
<?PHP       //<iframe src="fablabzh_map.php" style="border:0px;width:100%;height:400px;border-radius:5px;"></iframe>	?>

</body>
</html>
