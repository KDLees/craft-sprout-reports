<?php
/**
 * Sprout Reports plugin for Craft CMS 3.x
 *
 * Powerful custom reports.
 *
 * @link      barrelstrengthdesign.com
 * @copyright Copyright (c) 2017 Barrelstrength
 */

namespace barrelstrength\sproutreports\services;

use barrelstrength\sproutcore\services\sproutreports\DataSources;
use barrelstrength\sproutcore\services\sproutreports\Exports;
use barrelstrength\sproutcore\services\sproutreports\Reports;
use craft\base\Component;

/**
 * App Service
 *
 * All of your plugin’s business logic should go in services, including saving data,
 * retrieving data, etc. They provide APIs that your controllers, template variables,
 * and other plugins can interact with.
 *
 * https://craftcms.com/docs/plugins/services
 *
 * @author    Barrelstrength
 * @package   SproutReports
 * @since     3
 */
class App extends Component
{
	/**
	 * @var ReportGroups
	 */
	public $reportGroups;

	/**
	 * @var DataSources
	 */
	public $dataSources;

	/**
	 * @var Reports
	 */
	public $reports;

	/**
	 * @var Exports
	 */
	public $exports;

	/**
	 * @var Settings
	 */
	public $settings;

	public function init()
	{
		$this->reportGroups    = new ReportGroups();
		$this->dataSources     = new DataSources();
		$this->reports         = new Reports();
		$this->exports         = new Exports();
		$this->settings        = new Settings();
	}
}
