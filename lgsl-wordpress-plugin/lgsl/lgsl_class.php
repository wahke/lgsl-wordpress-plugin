<?php
// Überprüfe und definiere Konfigurationsvariablen
if (defined('LGSL_DB_HOST') && defined('LGSL_DB_NAME') && defined('LGSL_DB_USER') && defined('LGSL_DB_PASS')) {
    // Initialisiere die Datenbankverbindung
    global $lgsl_db;
    if (!isset($lgsl_db)) {
        $lgsl_db = new mysqli(LGSL_DB_HOST, LGSL_DB_USER, LGSL_DB_PASS, LGSL_DB_NAME);
        if ($lgsl_db->connect_error) {
            die("Connection failed: " . $lgsl_db->connect_error);
        }
    }

    // Sicherstellen, dass notwendige Arrays initialisiert sind
    if (!isset($lgsl_config)) {
        $lgsl_config = array();
    }

    if (!isset($lgsl_protocol_list)) {
        $lgsl_protocol_list = array();
    }

    //------------------------------------------------------------------------------------------------------------
    //  CACHING / LOGGING
    //------------------------------------------------------------------------------------------------------------
    function lgsl_query_cached($type, $ip, $c_port, $q_port, $s_port, $request)
    {
        global $lgsl_config, $lgsl_protocol_list;

        $timeout = isset($lgsl_config['cache_time']) ? $lgsl_config['cache_time'] * 60 : 300;

        $protocol = "lgsl_query_{$type}";

        if (!function_exists($protocol)) {
            return array();
        }

        $cached_file = plugin_dir_path(__FILE__) . "lgsl_files/cache/{$type}_{$ip}_{$q_port}.txt";

        if (file_exists($cached_file) && filemtime($cached_file) > time() - $timeout) {
            $server = unserialize(file_get_contents($cached_file));
        } else {
            $server = $protocol($ip, $c_port, $q_port, $s_port, $request);

            if (!empty($server)) {
                file_put_contents($cached_file, serialize($server));
            }
        }

        return $server;
    }

    function lgsl_query_direct($type, $ip, $c_port, $q_port, $s_port, $request)
    {
        global $lgsl_protocol_list;

        $protocol = "lgsl_query_{$type}";

        if (!function_exists($protocol)) {
            return array();
        }

        return $protocol($ip, $c_port, $q_port, $s_port, $request);
    }

    function lgsl_query($type, $ip, $c_port, $q_port, $s_port, $request)
    {
        global $lgsl_config;

        if (isset($lgsl_config['cache_time']) && $lgsl_config['cache_time']) {
            return lgsl_query_cached($type, $ip, $c_port, $q_port, $s_port, $request);
        } else {
            return lgsl_query_direct($type, $ip, $c_port, $q_port, $s_port, $request);
        }
    }

    //------------------------------------------------------------------------------------------------------------
    //  GET URL
    //------------------------------------------------------------------------------------------------------------
    function lgsl_get_url($url)
    {
        if (!function_exists('curl_init')) {
            return false;
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);

        $data = curl_exec($ch);

        curl_close($ch);

        return $data;
    }

    function lgsl_get_url_fgc($url)
    {
        if (!function_exists('file_get_contents')) {
            return false;
        }

        return file_get_contents($url);
    }

    function lgsl_get_url_data($url)
    {
        $data = lgsl_get_url($url);

        if ($data === false) {
            $data = lgsl_get_url_fgc($url);
        }

        return $data;
    }

    //------------------------------------------------------------------------------------------------------------
    //  SAVE AND LOAD FILE
    //------------------------------------------------------------------------------------------------------------
    function lgsl_save_file($file, $data)
    {
        if (!function_exists('file_put_contents')) {
            return false;
        }

        return file_put_contents($file, $data);
    }

    function lgsl_load_file($file)
    {
        if (!function_exists('file_get_contents')) {
            return false;
        }

        return file_get_contents($file);
    }

    //------------------------------------------------------------------------------------------------------------
    //  SERVER LIST PARSER
    //------------------------------------------------------------------------------------------------------------
    function lgsl_server_list_parse($list)
    {
        $servers = array();

        foreach (explode("\n", $list) as $line) {
            $line = trim($line);

            if ($line && substr($line, 0, 1) != "#") {
                list($type, $ip, $c_port, $q_port, $s_port) = explode(":", $line);

                $servers[] = array("type" => $type, "ip" => $ip, "c_port" => $c_port, "q_port" => $q_port, "s_port" => $s_port);
            }
        }

        return $servers;
    }

    //------------------------------------------------------------------------------------------------------------
    //  GET SERVER LIST
    //------------------------------------------------------------------------------------------------------------
    function lgsl_server_list_get($url)
    {
        $data = lgsl_get_url_data($url);

        if ($data === false) {
            return false;
        }

        return lgsl_server_list_parse($data);
    }

    //------------------------------------------------------------------------------------------------------------
    //  CUSTOM FUNCTIONS FOR LGSL
    //------------------------------------------------------------------------------------------------------------

    // Beispiel: Serverinformationen anzeigen
    function lgsl_display_server_info($server) {
        echo "Server Name: " . $server['name'] . "<br>";
        echo "Server IP: " . $server['ip'] . "<br>";
        echo "Server Port: " . $server['c_port'] . "<br>";
        echo "Map: " . $server['map'] . "<br>";
        echo "<hr>";
    }

    // Beispiel: Datenbankabfrage durchführen und Serverinformationen anzeigen
    function lgsl_show_all_servers() {
        global $lgsl_db;
        
        $query = "SELECT * FROM " . LGSL_DB_TABLE;
        $result = $lgsl_db->query($query);
        
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                lgsl_display_server_info($row);
            }
        } else {
            echo "Keine Server gefunden.";
        }
    }

    // Beispielaufruf der Funktion, um alle Serverinformationen anzuzeigen
    lgsl_show_all_servers();
}
?>
