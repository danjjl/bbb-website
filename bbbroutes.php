<?php
/**
 * Shortcode to display BBB routes on WP website.
 * 
 * Usage :
 * - [bbbroutestable folder='long']  // Displays routes table
 * - [bbbroutesmap folder='long']    // Displays map with routes
 */


/**
* Build HTML table containing BBB Routes.
*
* The function scans a directory for GPX files and returns an HTML table
* containing the columns RouteName, Distance, Elevation, Direction, Download.
* The GPX routes are sorted by distance (descending order).
*
* @param string   $folder   contains the GPX directory to be scanned with base
*                           path WP_ROOT/wp-content/uploads/gpx/$folder.
*
* @return string  $table    HTML table containing GPX routes.
*/
function bbbroutestable_function( $atts = array() ) {
    // set up default parameters
    extract(shortcode_atts(array(
     'folder' => 'long'
    ), $atts));

    // Sort by distance
    $files = glob($ABSPATH."wp-content/uploads/gpx/$folder/*.gpx");
    $dist = array();
    for ($i=0; $i < count($files); $i++) {
        $filename = $files[$i];
        $filename = basename($filename);
        $fileparts = explode("-", $filename);
        $distance = substr($fileparts[2], 0, -2);
        array_push($dist, (int) $distance);
    }
    asort($dist);
    $sorteddistance = array_keys($dist);

    // Build table
    $table = "<table>\n";
    $table .= "<tr>\n";
    $table .= "<th>Route</th>\n";
    $table .= "<th>Distance</th>\n";
    $table .= "<th>Elevation</th>\n";
    $table .= "<th>Direction</th>\n";
    $table .= "<th>Download</th>\n";
    $table .= "</tr>\n";
    // Populate table
    for ($i=0; $i < count($files); $i++) {
        $filename = $files[$sorteddistance[$i]];
        $filename = basename($filename);
        $fileparts = explode("-", $filename);
        $direction = str_replace('_', '-',$fileparts[1]);
        $distance = $fileparts[2];
        $elevation = $fileparts[3];
        $routeName = str_replace('_', ' ', substr($fileparts[4],0, -4));
        $downloadURL = get_site_url()."/wp-content/uploads/gpx/$folder/$filename";

        $table .= "<tr>\n";
        $table .= "<td>$routeName</td>\n";
        $table .= "<td>$distance</td>\n";
        $table .= "<td>$elevation</td>\n";
        $table .= "<td>$direction</td>\n";
        $table .= "<td><a href=\"".$downloadURL."\">Download</a></td>\n";
        $table .= "</tr>\n";
    }
    $table .= "</table>";

    return $table;
}
// Takes argument 'folder' which contains path to GPX folder
add_shortcode('bbbroutestable', 'bbbroutestable_function');


/**
* Build Leaflet map BBB Routes.
*
* The function scans a directory for GPX files and returns a Leaflet map will
* all routes overlaid on the map.
*
* @param string   $folder   contains the GPX directory to be scanned with base
*                           path WP_ROOT/wp-content/uploads/gpx/$folder.
*
* @return string  $shortcode  HTML/JS Leaflet map.
*/
function bbbroutesmap_function( $atts = array() ) {
    // set up default parameters
    extract(shortcode_atts(array(
     'folder' => 'long'
    ), $atts));
    
    $colours = ['#e6194b', '#3cb44b', '#ffe119', '#4363d8', '#f58231', '#911eb4', '#46f0f0', '#f032e6', '#bcf60c', '#fabebe', '#008080', '#e6beff', '#9a6324', '#fffac8', '#800000', '#aaffc3', '#808000', '#ffd8b1', '#000075', '#808080', '#000000'];

    // Sort by direction
    $files = glob($ABSPATH."wp-content/uploads/gpx/$folder/*.gpx");
    $dir = array();
    for ($i=0; $i < count($files); $i++) {
        $filename = $files[$i];
        $filename = basename($filename);
        $fileparts = explode("-", $filename);
        $direction = str_replace('_', '-',$fileparts[1]);
        array_push($dir, (int) $direction);
    }
    asort($dir);
    $sortedDir = array_keys($dir);

    //Build map
    $shortcode = "[leaflet-map]";
    for ($i=0; $i < count($files); $i++){
        $filename = basename($files[$sortedDir[$i]]);
        $fileparts = explode("-", $filename);
        $downloadURL = get_site_url()."/wp-content/uploads/gpx/$folder/$filename";
        $routeName = str_replace('_', ' ', substr($fileparts[4],0, -4));
        $colourId = $i%count($colours);
        $shortcode .= "[leaflet-gpx src=$downloadURL color=$colours[$colourId]] <a href=\"".$downloadURL."\">".$routeName."</a>[/leaflet-gpx]";
    }

    return do_shortcode($shortcode);
}
// Takes argument 'folder' which contains path to GPX folder
add_shortcode('bbbroutesmap', 'bbbroutesmap_function');
?>
