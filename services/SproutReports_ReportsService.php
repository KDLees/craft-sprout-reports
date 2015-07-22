<?php
namespace Craft;

class SproutReports_ReportsService extends BaseApplicationComponent
{
	protected $reportRecord			= null;
	protected $commandsNotAllowed	= array('INSERT', 'UPDATE', 'DELETE', 'ALTER', 'DROP');

	// For retrieving groups
	private $_groupsById;
	private $_fetchedAllGroups = false;

	public function __construct($reportRecord = null)
	{
		$this->reportRecord = $reportRecord;
		
		if (is_null($this->reportRecord)) 
		{
			$this->reportRecord = SproutReports_ReportRecord::model();
		}
	}

	/**
	 * Get a new blank item
	 *
	 * @param  array               $attributes
	 * @return SproutReports_ReportModel
	 */
	public function newModel($attributes = array())
	{
		$model = new SproutReports_ReportModel();
		$model->setAttributes($attributes);

		return $model;
	}

	public function saveReport(SproutReports_ReportModel &$model)
	{	
		if ($id = $model->getAttribute('id')) 
		{
			if (null === ($record = $this->reportRecord->findByPk($id))) 
			{
				throw new Exception(Craft::t('Can\'t find report with ID "{id}"', array('id' => $id)));
			}
		} 
		else 
		{
			$record = $this->reportRecord->create();
		}

		// Simple validation on query string
		
		$customQuery = $this->sanitizeQueryString($model->getAttribute('customQuery'), false);

		if ($customQuery === false)
		{
			$model->addError('customQuery', Craft::t('Potentially unsafe or invalid query.'));

			return false;
		}

		$model->setAttribute('customQuery', $customQuery);

		$record->setAttributes($model->getAttributes(), false);

		if ($record->validate() && $record->save()) {
			// update id on model (for new records)
			$model->setAttribute('id', $record->getAttribute('id'));
			
			return true;
		} 
		else 
		{
			$model->addErrors($record->getErrors());
			
			return false;
		}
	}

	public function runReport($query, $report=null)
	{
		$query = $this->sanitizeQueryString($query);

		if ($query === false)
		{
			return false;
		}

		$query	= $this->parseModifierFlag($query);

		try
		{
			$result	= craft()->db->createCommand($query)->query();
		}
		catch (\CDbException $e)
		{
			if (is_null($report))
			{
				throw new \CDbException($e->getMessage());
			}

			$reportEditUrl = sprintf('/%s/sproutreports/reports/edit/%s', craft()->config->get('cpTrigger'), $report['id']);

			$response = array(
				'message'	=> 'Report could not be ran, please update query.',
				'dbMessage'	=> $e->getMessage()
			);

			
			craft()->userSession->setFlash('response', $response);

			craft()->request->redirect($reportEditUrl);
		}

		return $result;
	}

	public function deleteReportById($reportId)
	{
		if (!$reportId)
		{
			return false;
		}

		$report = new SproutReports_ReportRecord;

		return $report->deleteByPk($reportId);
	}

	public function getAllReports() 
	{
		$q = craft()->db->createCommand()->from('sproutreports_reports');

		return $q->queryAll();
	}

	public function getAllReportsByAttributes(array $attributes=array())
	{
		return $this->reportRecord->findAllByAttributes($attributes);
	}

	public function getReportsByGroupId($groupId)
	{
		$query = craft()->db->createCommand()
							->from('sproutreports_reports')
							->where('groupId=:groupId', array('groupId' => $groupId))
							->order('name')
							->queryAll();    

		return SproutReports_ReportModel::populateModels($query);
	}

	public function getReportById($reportId)
	{
        return SproutReports_ReportRecord::model()->findByPk($reportId);
	}

    public function getReportByUserOptions($reportId, $attributes)
	{
        $condition = '';
        $params = '';
        $id = array('id' => $reportId);
        $attributes = (is_array($attributes)) ? array_merge($id, $attributes) : $id;
        //TODO:

        return SproutReports_ReportRecord::model()->findByAttributes($attributes /*$condition, $params*/);
	}

	public function getReportByHandle($handle) 
	{
		if (!$handle)
		{
			return false;
		}

		return SproutReports_ReportRecord::model()->findByAttributes(array('handle' => $handle));
	}


	/**
	 * Returns all report groups.
	 *
	 * @param string|null $indexBy
	 * @return array
	 */
	public function getAllReportGroups($indexBy = null)
	{
			if (!$this->_fetchedAllGroups)
			{
					$groupRecords = SproutReports_ReportGroupRecord::model()->ordered()->findAll();
					$this->_groupsById = SproutReports_ReportGroupModel::populateModels($groupRecords, 'id');
					$this->_fetchedAllGroups = true;
			}

			if ($indexBy == 'id')
			{
					$groups = $this->_groupsById;
			}
			else if (!$indexBy)
			{
					$groups = array_values($this->_groupsById);
			}
			else
			{
					$groups = array();
					foreach ($this->_groupsById as $group)
					{
							$groups[$group->$indexBy] = $group;
					}
			}

			return $groups;
	}

	/**
	 * Saves a group group.
	 *
	 * @param ReportGroupModel $group
	 * @return bool
	 */
	public function saveGroup(SproutReports_ReportGroupModel $group)
	{		
			$groupRecord = $this->_getGroupRecord($group);
			$groupRecord->name = $group->name;

			if ($groupRecord->validate())
			{
					$groupRecord->save(false);

					// Now that we have an ID, save it on the model & models
					if (!$group->id)
					{
							$group->id = $groupRecord->id;
					}

					return true;
			}
			else
			{
					$group->addErrors($groupRecord->getErrors());
					return false;
			}
	}

	/**
	 * Deletes a group
	 *
	 * @param int $groupId
	 * @return bool
	 */
	public function deleteGroupById($groupId)
	{
			$groupRecord = SproutReports_ReportGroupRecord::model()->findById($groupId);

			if (!$groupRecord)
			{
					return false;
			}

			$affectedRows = craft()->db->createCommand()->delete('sproutreports_reportgroups', array('id' => $groupId));

			return (bool) $affectedRows;
	}

	/**
	 * Gets a group record or creates a new one.
	 *
	 * @access private
	 * @param ReportGroupModel $group
	 * @throws Exception
	 * @return ReportGroupRecord
	 */
	private function _getGroupRecord(SproutReports_ReportGroupModel $group)
	{
		if ($group->id)
		{
			$groupRecord = SproutReports_ReportGroupRecord::model()->findById($group->id);

			if (!$groupRecord)
			{
				throw new Exception(Craft::t('No field group exists with the ID “{id}”', array('id' => $group->id)));
			}
		}
		else
		{
			$groupRecord = new SproutReports_ReportGroupRecord();
		}

		return $groupRecord;
	}

	/**
	 * Validates a query string and attempts to make it as safe as possible
	 *
	 * @param	string	$query	The SQL string
	 *
	 * @return	mixed			The sanitized query string or false if not safe enough
	 */
	public function sanitizeQueryString($query)
	{
		// Must be a string
		// Must be at least 15 characters long
		if (!is_string($query) || strlen($query) < 15)
		{
			return false;
		}

		
		// Must start with the SELECT command
		if (stripos(trim($query), 'SELECT') !== 0)
		{
			return false;
		}

		// Must not contain any of these potentially unsafe strings/commands
		// May escape command with @ (one at sign) if required
		foreach ($this->commandsNotAllowed as $command)
		{
			if (preg_match('/\s?[^@]+\b'.$command.'\b\s+/i', $query))
			{
				return false;
			}
		}

		// Remove possible CRLF injection
		$query	= str_replace('\n', '', $query);

		return $query;
	}

	/**
	 * The @ sign is our modifier flag
	 * Its purpose is to allow table prefix replacement and command escaping
	 *
	 * 1. Dynamic table prefix replacement with @_table
	 * 2. Dynamic command escaping with @command
	 *
	 * @example
	 *
	 * Flagging
	 * > SELECT * FROM @_actions WHERE action = 'execute @delete command.' LIMIT 1
	 *
	 * Yields
	 * > SELECT * FROM craft_actions WHERE action = 'execute delete command.' LIMIT 1
	 */

	public function parseModifierFlag($query)
	{
		if (stripos($query, '@') !== false)
		{
			if (stripos($query, '@_') !== false)
			{
				$query = str_replace('@_', craft()->config->getDbItem('tablePrefix').'_', $query);
			}

			foreach ($this->commandsNotAllowed as $command)
			{
				$foundAtposition = stripos($query, '@'.$command);

				if ($foundAtposition !== false)
				{
					$commandAsWritten = substr($query, $foundAtposition + 1, strlen($command));

					$query = str_replace('@'.$commandAsWritten, $commandAsWritten, $query);
				}
			}
		}

		return $query;
	}
}