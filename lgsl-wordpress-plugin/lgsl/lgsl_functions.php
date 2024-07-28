<?php
//------------------------------------------------------------------------------------------------------------
//  Converts a hostname to an IP address
//------------------------------------------------------------------------------------------------------------

if (!function_exists('lgsl_server_dns')) {
    function lgsl_server_dns($address)
    {
        if (empty($address)) {
            return "";
        }

        $ip = gethostbyname($address);

        if ($ip == $address) {
            return "";
        }

        return $ip;
    }
}

//------------------------------------------------------------------------------------------------------------
//  Checks the connection to a server and gets the server data
//------------------------------------------------------------------------------------------------------------

if (!function_exists('lgsl_query_cached')) {
    function lgsl_query_cached($type, $ip, $c_port, $q_port, $s_port, $request)
    {
        global $lgsl_config, $lgsl_protocol_list;

        $timeout = $lgsl_config['cache_time'] * 60;

        $protocol = "lgsl_query_{$type}";

        if (!function_exists($protocol)) {
            return array();
        }

        $cached_file = LGSL_ROOT . "lgsl_files/cache/{$type}_{$ip}_{$q_port}.txt";

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
}

//------------------------------------------------------------------------------------------------------------
//  Attempts to connect to a server
//------------------------------------------------------------------------------------------------------------

if (!function_exists('lgsl_query_direct')) {
    function lgsl_query_direct($type, $ip, $c_port, $q_port, $s_port, $request)
    {
        global $lgsl_protocol_list;

        $protocol = "lgsl_query_{$type}";

        if (!function_exists($protocol)) {
            return array();
        }

        return $protocol($ip, $c_port, $q_port, $s_port, $request);
    }
}

//------------------------------------------------------------------------------------------------------------
//  Get server data and optionally cache it
//------------------------------------------------------------------------------------------------------------

if (!function_exists('lgsl_query')) {
    function lgsl_query($type, $ip, $c_port, $q_port, $s_port, $request)
    {
        global $lgsl_config;

        if ($lgsl_config['cache_time']) {
            return lgsl_query_cached($type, $ip, $c_port, $q_port, $s_port, $request);
        } else {
            return lgsl_query_direct($type, $ip, $c_port, $q_port, $s_port, $request);
        }
    }
}

//------------------------------------------------------------------------------------------------------------
//  Get data from a URL using cURL
//------------------------------------------------------------------------------------------------------------

if (!function_exists('lgsl_get_url')) {
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
}

//------------------------------------------------------------------------------------------------------------
//  Get data from a URL using file_get_contents
//------------------------------------------------------------------------------------------------------------

if (!function_exists('lgsl_get_url_fgc')) {
    function lgsl_get_url_fgc($url)
    {
        if (!function_exists('file_get_contents')) {
            return false;
        }

        return file_get_contents($url);
    }
}

//------------------------------------------------------------------------------------------------------------
//  Get data from a URL
//------------------------------------------------------------------------------------------------------------

if (!function_exists('lgsl_get_url_data')) {
    function lgsl_get_url_data($url)
    {
        $data = lgsl_get_url($url);

        if ($data === false) {
            $data = lgsl_get_url_fgc($url);
        }

        return $data;
    }
}

//------------------------------------------------------------------------------------------------------------
//  Save data to a file
//------------------------------------------------------------------------------------------------------------

if (!function_exists('lgsl_save_file')) {
    function lgsl_save_file($file, $data)
    {
        if (!function_exists('file_put_contents')) {
            return false;
        }

        return file_put_contents($file, $data);
    }
}

//------------------------------------------------------------------------------------------------------------
//  Load data from a file
//------------------------------------------------------------------------------------------------------------

if (!function_exists('lgsl_load_file')) {
    function lgsl_load_file($file)
    {
        if (!function_exists('file_get_contents')) {
            return false;
        }

        return file_get_contents($file);
    }
}

//------------------------------------------------------------------------------------------------------------
//  Simple server list parser
//------------------------------------------------------------------------------------------------------------

if (!function_exists('lgsl_server_list_parse')) {
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
}

//------------------------------------------------------------------------------------------------------------
//  Get server list data from a URL
//------------------------------------------------------------------------------------------------------------

if (!function_exists('lgsl_server_list_get')) {
    function lgsl_server_list_get($url)
    {
        $data = lgsl_get_url_data($url);

        if ($data === false) {
            return false;
        }

        return lgsl_server_list_parse($data);
    }
}
?>
