<?php
require_once(__DIR__ . '/config.php');

class AdvancedControlPlugin extends Plugin
{
    var $config_class = 'AdvancedControlConfig';

    /**
     * Main interface for plugins. Called at the beginning of every request
     * for each installed plugin. Plugins should register functionality and
     * connect to signals, etc.
     */
    function bootstrap()
    {
        if (!$this->isConfigured()) {
            Messages::error(
                '<b>' . $this->getName() . '</b>: ' .
                __('Configure the plugin before enabling it!')
            );

            $this->disable();
            return false;
        }

        # TODO: Implement bootstrap() method.
    }

    /**
     * Checks if the plugin is correctly configured
     * @return boolean
     */
    protected function isConfigured()
    {
        $sql = "SELECT `value` 
                FROM " . CONFIG_TABLE . "
                WHERE 
                    lcase(`namespace`) like '%advanced_control%' AND 
                    `key` = 'transfer_db_host'";
        $res = db_query($sql);

        return (db_num_rows($res)!=0);
    }

    /**
     * @Override
     */
    public function enable()
    {
        if ($this->isConfigured()) {
            return parent::enable();
        }

        Messages::warning(
            '<b>' . $this->getName() . '</b>: ' .
            __('Configure the plugin before enabling it!')
        );
    }
}