<?php
namespace Craft;

/**
 * Class SproutReports_ReportGroupService
 *
 * @package Craft
 */
class SproutReports_ReportGroupService extends BaseApplicationComponent
{
	/**
	 * @param SproutReports_ReportGroupModel &$model
	 *
	 * @return bool
	 */
	public function save(SproutReports_ReportGroupModel &$group)
	{
		$groupRecord = $this->_getGroupRecord($group);
		$groupRecord->name = $group->name;
		$groupRecord->handle = $group->handle;

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

		// $isNew  = !$model->id;
		// $record = new SproutReports_ReportGroupRecord();

		// $record->isNewRecord = $isNew;

		// if ($model->id)
		// {
		// 	$oldGroup = $this->get($model->id);
		// }

		// $record->setAttributes($model->getAttributes(), false);

		// if (!$record->validate())
		// {
		// 	$model->addErrors($record->getErrors());

		// 	return false;
		// }

		// if (!$record->save())
		// {
		// 	Craft::dd($record->save());
		// 	$model->addError('general', Craft::t('Unable to save report group.'));

		// 	return false;
		// }

		// if ($isNew)
		// {
		// 	$model->id = $record->id;
		// }

		// return true;
	}

	/**
	 * @param int $id
	 *
	 * @throws Exception
	 * @return SproutReports_ReportGroupModel
	 */
	public function get($id)
	{
		$group = SproutReports_ReportGroupRecord::model()->findByAttributes(compact('id'));

		if (!$group)
		{
			throw new Exception(Craft::t('Cannot find group with id {id}.', compact('id')));
		}

		return SproutReports_ReportGroupModel::populateModel($group);
	}

	/**
	 * @param string $handle
	 *
	 * @throws Exception
	 * @return SproutReports_ReportGroupModel
	 */
	public function getByHandle($handle)
	{
		$group = SproutReports_ReportGroupRecord::model()->findByAttributes(compact('handle'));

		if (!$group)
		{
			throw new Exception(Craft::t('Cannot find group with handle {handle}.', compact('handle')));
		}

		return SproutReports_ReportGroupModel::populateModel($group->getAttributes());
	}

	/**
	 * @return null|SproutReports_ReportGroupModel[]
	 */
	public function getAll()
	{
		$groups = SproutReports_ReportGroupRecord::model()->findAll(array('index'=>'id'));

		if ($groups)
		{
			return SproutReports_ReportGroupModel::populateModels($groups, 'id');
		}
	}

	/**
	 * @param int $id
	 *
	 * @return bool
	 */
	public function delete($id)
	{
		return (bool) SproutReports_ReportGroupRecord::model()->deleteByPk($id);
	}

	/**
	 * @param string $name
	 *
	 * @throws Exception
	 * @return SproutReports_ReportGroupModel
	 */
	public function getOrCreateByName($name)
	{
		$handle = sproutReports()->createHandle($name);

		try
		{
			return $this->getByHandle($handle);
		}
		catch (\Exception $e)
		{
			$group = new SproutReports_ReportGroupModel(compact('name', 'handle'));

			if ($this->save($group))
			{
				return $group;
			}

			throw new Exception(print_r($group->getErrors(), true));
		}
	}

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
}
