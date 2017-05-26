<?php
require_once "vendor/autoload.php";

class AdvancedControlConfig extends PluginConfig implements PluginCustomConfig
{
    const PLUGIN_NAME = 'advanced_control';
    private $plugin_config;

    // Provide compatibility function for versions of osTicket prior to
    // translation support (v1.9.4)
    function translate() {
        if (!method_exists('Plugin', 'translate')) {
            return array(
                function($x) { return $x; },
                function($x, $y, $n) { return $n != 1 ? $y : $x; },
            );
        }
        return Plugin::translate('advanced_control');
    }

    function __construct($name)
    {
        parent::__construct(self::PLUGIN_NAME);

        $this->plugin_config = array(
            'base' => __DIR__.'/resources/views/config',
            'tab_files_pattern' => 'tab_pages/*.tab.twig',
            'pass_protection_message' => __('password is saved')
        );
    }

    function renderCustomConfig()
    {
        $loader = new Twig_Loader_Filesystem($this->plugin_config['base']);
        $page = new Twig_Environment($loader);

        chdir($this->plugin_config['base']);
        $tab_files = glob($this->plugin_config['tab_files_pattern']);

        $data = array_merge(
            $this->config,
            array('saved_pass_message' => $this->plugin_config['pass_protection_message'])
        );

        echo $page->render(
            'custom_config.html.twig',
            array(
                'tabs' => $tab_files,
                'plugin_data' => $data,
            )
        );
    }

    function saveCustomConfig()
    {
        global $msg;

        # TODO: utilizar os dados do $_POST.
        $config = $this->postParser($_POST);
        $errors = array(); // $data['errors'];

        // $f = $this->getForm();
        if (count($errors) === 0) {
            // $this->section = self::PLUGIN_SECTION;
            if ($this->updateAll($config)) {
                if (!$msg)
                    $msg = __('Successfully updated configuration');
                return true;
            }
        }

        return false;
    }

    function postParser($post)
    {
        $data = array();
        $data['transfer_db_host'] = $post['transfer_db_host'];
        $data['transfer_db_name'] = $post['transfer_db_name'];
        $data['transfer_db_user'] = $post['transfer_db_user'];

        // password parsing
        $unchanged_pass = $post['transfer_db_password'] == null;
        $data['transfer_db_password'] = base64_encode($post['transfer_db_password']);
        if ($unchanged_pass && isset($this->config['transfer_db_password'])) {
            $data['transfer_db_password'] = $this->config['transfer_db_password']->ht['value'];
        }

        $data['transfer_db_prefix'] = $post['transfer_db_prefix'];
        $data['linked_tickets'] = (isset($post['linked_tickets']) ? true : false);
        $data['system_offline'] = (isset($post['system_offline']) ? true : false);

        // $data[''] = $post[''];
        // $db = new DbConfig($db_host, $db_name, $db_user, $db_pass);

        return $data;
    }
}