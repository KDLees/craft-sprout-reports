<?php
abstract class SproutReportsBaseReport {
	/*
	 * Get report group
	 * @return string
	 */
	abstract public function getGroup();

	/*
	 * Get report name
	 * @return string
	 */
	abstract public function getName();

	/*
	 * SQL query to be used for generating reports
	 * @return string
	 */
	abstract public function getQuery();

	/*
	 * Define user options for report
	 * @return array
	 */
	abstract public function userOptions();

	/*
	 * List of available user options
	 */
	private function _availableUserOptions() {
		//restrict to predefined options
	}
}