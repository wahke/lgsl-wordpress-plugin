<?php
class LGSL_Update_Checker {
    private $plugin_slug;
    private $version;
    private $update_url;

    public function __construct($plugin_slug, $version, $update_url) {
        $this->plugin_slug = $plugin_slug;
        $this->version = $version;
        $this->update_url = $update_url;

        add_filter('pre_set_site_transient_update_plugins', array($this, 'check_for_update'));
        add_filter('plugins_api', array($this, 'plugin_info'), 20, 3);
    }

    public function check_for_update($transient) {
        if (empty($transient->checked)) {
            return $transient;
        }

        $response = wp_remote_get($this->update_url);
        if (!is_wp_error($response) && isset($response['body'])) {
            $update_info = json_decode($response['body']);
            if (version_compare($this->version, $update_info->new_version, '<')) {
                $plugin = new stdClass();
                $plugin->slug = $this->plugin_slug;
                $plugin->new_version = $update_info->new_version;
                $plugin->package = $update_info->package;
                $plugin->url = $update_info->url;
                $transient->response[$this->plugin_slug . '/' . $this->plugin_slug . '.php'] = $plugin;
            }
        }
        return $transient;
    }

    public function plugin_info($res, $action, $args) {
        if ($action !== 'plugin_information' || $args->slug !== $this->plugin_slug) {
            return false;
        }

        $response = wp_remote_get($this->update_url);
        if (!is_wp_error($response) && isset($response['body'])) {
            $update_info = json_decode($response['body']);
            $res = new stdClass();
            $res->name = 'LGSL WordPress Plugin';
            $res->slug = $this->plugin_slug;
            $res->version = $update_info->new_version;
            $res->homepage = $update_info->url;
            $res->download_link = $update_info->package;
            $res->sections = array(
                'description' => 'A WordPress plugin to integrate LGSL (Live Game Server List) using an existing LGSL installation\'s MySQL database.',
                'changelog' => isset($update_info->changelog) ? $update_info->changelog : '',
            );
        }
        return $res;
    }
}

new LGSL_Update_Checker('lgsl-wordpress-plugin', '1.2', 'https://github.com/wahke/lgsl-wordpress-plugin/raw/main/lgsl-version.json');
