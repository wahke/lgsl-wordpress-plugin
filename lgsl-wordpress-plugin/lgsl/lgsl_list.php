<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

// Sicherstellen, dass die Datenbankverbindung vorhanden ist
global $lgsl_db;
if (!isset($lgsl_db)) {
    echo "Keine Datenbankverbindung verfügbar.";
    return;
}

// IDs und Anzeigeoptionen aus dem Shortcode-Parameter extrahieren
$ids = isset($atts['id']) ? array_filter(array_map('intval', explode(',', $atts['id']))) : [];
$show_servername = filter_var($atts['show_servername'], FILTER_VALIDATE_BOOLEAN);
$show_ip = filter_var($atts['show_ip'], FILTER_VALIDATE_BOOLEAN);
$show_port = filter_var($atts['show_port'], FILTER_VALIDATE_BOOLEAN);
$show_game = filter_var($atts['show_game'], FILTER_VALIDATE_BOOLEAN);
$show_map = filter_var($atts['show_map'], FILTER_VALIDATE_BOOLEAN);
$show_players = filter_var($atts['show_players'], FILTER_VALIDATE_BOOLEAN);
$show_status = filter_var($atts['show_status'], FILTER_VALIDATE_BOOLEAN);

$base_url = get_option('lgsl_base_url', '');

$query = "SELECT * FROM " . LGSL_DB_TABLE;
if (!empty($ids)) {
    $ids_list = implode(',', $ids);
    $query .= " WHERE id IN ($ids_list)";
}

$result = $lgsl_db->query($query);

if ($result->num_rows > 0) {
    echo "<table>";
    echo "<tr>";
    if ($show_servername) echo "<th>Server Name</th>";
    if ($show_ip) echo "<th>IP</th>";
    if ($show_port) echo "<th>Port</th>";
    if ($show_game) echo "<th>Game</th>";
    if ($show_map) echo "<th>Map</th>";
    if ($show_players) echo "<th>Spieler</th>";
    if ($show_status) echo "<th>Status</th>";
    echo "</tr>";

    while ($row = $result->fetch_assoc()) {
        // Cache-Inhalt Base64-dekodieren
        $cache_base64 = base64_decode($row['cache']);
        // Cache-Inhalt deserialisieren
        $cache = @unserialize($cache_base64);

        if (is_array($cache)) {
            $server_name = isset($cache['s']['name']) ? esc_html($cache['s']['name']) : 'N/A';
            $game = isset($cache['s']['game']) ? esc_html($cache['s']['game']) : 'N/A';
            $map = isset($cache['s']['map']) ? esc_html($cache['s']['map']) : 'N/A';
            $players = isset($cache['s']['players']) ? esc_html($cache['s']['players']) : 'N/A';
            $playersmax = isset($cache['s']['playersmax']) ? esc_html($cache['s']['playersmax']) : 'N/A';
            $status = isset($cache['b']['status']) && $cache['b']['status'] == 1 ? 'Online' : 'Offline';
            $ip = isset($row['ip']) ? esc_html($row['ip']) : 'N/A';
            $port = isset($row['c_port']) ? esc_html($row['c_port']) : 'N/A';

            $server_link = $base_url ? $base_url . "?ip={$ip}&port={$port}" : '#';

            echo "<tr>";
            if ($show_servername) echo "<td><a href='{$server_link}' target='_blank'>{$server_name}</a></td>";
            if ($show_ip) echo "<td>{$ip}</td>";
            if ($show_port) echo "<td>{$port}</td>";
            if ($show_game) echo "<td>{$game}</td>";
            if ($show_map) echo "<td>{$map}</td>";
            if ($show_players) echo "<td>{$players} / {$playersmax}</td>";
            if ($show_status) echo "<td>{$status}</td>";
            echo "</tr>";
        } else {
            echo "<tr>";
            echo "<td colspan='7'>Ungültiger Cache-Inhalt</td>";
            echo "</tr>";
        }
    }
    echo "</table>";
} else {
    echo "Keine Server gefunden.";
}
?>
