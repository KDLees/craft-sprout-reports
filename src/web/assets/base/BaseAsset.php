<?php

namespace barrelstrength\sproutreports\web\assets\base;

use barrelstrength\sproutcore\web\sproutreports\SproutReportsAsset;
use craft\web\AssetBundle;

class BaseAsset extends AssetBundle
{
	public function init()
	{
		$this->sourcePath = "@barrelstrength/sproutreports/web/assets/base/dist";

		$this->depends = [
			SproutReportsAsset::class,
		];

		$this->js = [
			'js/groups.js',
		];

		parent::init();
	}
}