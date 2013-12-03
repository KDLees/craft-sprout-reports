<?php
namespace Craft;

class SproutReportsPlugin extends BasePlugin
{
    function init()
    {
        require CRAFT_PLUGINS_PATH.'reports/vendor/autoload.php';
    }

    function getName()
    {
        return Craft::t('Reports');
    }

    function getVersion()
    {
        return '0.3.1';
    }

    function getDeveloper()
    {
        return 'Barrel Strength Design';
    }

    function getDeveloperUrl()
    {
        return 'http://straightupcraft.com';
    }

    public function hasCpSection()
    {
        return true;
    }

    protected function defineSettings()
    {
        return array(
            'asetting'   => array(AttributeType::String, 'required' => true, 'label' => 'A Setting'),
        );
    }

    public function getSettingsHtml()
    {
        return craft()->templates->render('reports/settings', array(
            'settings' => $this->getSettings()
        ));
    }


    public function hookRegisterCpRoutes()
    {
        return array(
            'reports/query' => 'reports/reports/query',
        );
    }

}