<?php
/*
 * This file is part of the cookbook/workflows package.
 *
 * (c) Nikola Plavšić <nikolaplavsic@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cookbook\Workflows\Repositories;

use Cookbook\Contracts\Workflows\WorkflowPointRepositoryContract;
use Cookbook\Core\Exceptions\Exception;
use Cookbook\Core\Exceptions\NotFoundException;
use Cookbook\Core\Facades\Trunk;
use Cookbook\Core\Repositories\AbstractRepository;
use Cookbook\Core\Repositories\Collection;
use Cookbook\Core\Repositories\Model;
use Cookbook\Core\Repositories\UsesCache;
use Illuminate\Database\Connection;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;
use stdClass;

/**
 * WorkflowPointRepository class
 * 
 * Repository for workflow point database queries
 * 
 * @uses   		Illuminate\Database\Connection
 * @uses   		Cookbook\Core\Repository\AbstractRepository
 * 
 * @author  	Nikola Plavšić <nikolaplavsic@gmail.com>
 * @copyright  	Nikola Plavšić <nikolaplavsic@gmail.com>
 * @package 	cookbook/workflows
 * @since 		0.1.0-alpha
 * @version  	0.1.0-alpha
 */
class WorkflowPointRepository extends AbstractRepository implements WorkflowPointRepositoryContract//, UsesCache
{

// ----------------------------------------------------------------------------------------------
// PARAMS
// ----------------------------------------------------------------------------------------------
// 
// 
// 

	/**
	 * Create new WorkflowPointRepository
	 * 
	 * @param Illuminate\Database\Connection $db
	 * 
	 * @return void
	 */
	public function __construct(Connection $db)
	{
		$this->type = 'workflow-point';

		// AbstractRepository constructor
		parent::__construct($db);
	}

// ----------------------------------------------------------------------------------------------
// CRUD
// ----------------------------------------------------------------------------------------------
// 
// 
// 


	/**
	 * Create new workflow point
	 * 
	 * @param array $model - point params (status, action, name, description...)
	 * 
	 * @return mixed
	 * 
	 * @throws Exception
	 */
	protected function _create($model)
	{
		if( isset($model['steps']) )
		{
			$steps = $model['steps'];
			unset($model['steps']);
		}

		$model['created_at'] = $model['updated_at'] = Carbon::now('UTC')->toDateTimeString();

		// insert workflow point in database
		$workflowPointId = $this->db->table('workflow_points')->insertGetId($model);

		// get workflow point
		$workflowPoint = $this->fetch($workflowPointId);

		if(!$workflowPoint)
		{
			throw new \Exception('Failed to insert workflow point');
		}

		if( ! empty($steps) )
		{
			$this->insertSteps($workflowPoint, $steps);

			$workflowPoint = $this->fetch($workflowPointId);
		} 
		
		Cache::forget('workflowPoints');

		// and return newly created workflow point
		return $workflowPoint;
		
	}

	/**
	 * Update workflow point
	 * 
	 * @param array $model - workflow point params (status, action, name, description...)
	 *
	 * @return mixed
	 * 
	 * @throws Cookbook\Core\Exceptions\NotFoundException
	 */
	protected function _update($id, $model)
	{
		if( isset($model['steps']) )
		{
			$steps = $model['steps'];
			unset($model['steps']);
		}
		

		// find workflow point with that ID
		$workflowPoint = $this->fetch($id);

		if( ! $workflowPoint )
		{
			throw new NotFoundException(['There is no workflow point with that ID.']);
		}

		$model['updated_at'] = Carbon::now('UTC')->toDateTimeString();

		$this->db->table('workflow_points')->where('id', '=', $id)->update($model);

		Trunk::forgetType('workflow-point');
		$workflowPoint = $this->fetch($id);

		if( ! empty($steps) )
		{
			$this->updateSteps($workflowPoint, $steps);

			$workflowPoint = $this->fetch($workflowPoint->id);
		} 
		Cache::forget('workflowPoints');

		// and return workflow point
		return $workflowPoint;
	}

	/**
	 * Delete workflow point from database
	 * 
	 * @param integer $id - ID of workflow point that will be deleted
	 * 
	 * @return boolean
	 * 
	 * @throws Cookbook\Core\Exceptions\NotFoundException
	 */
	protected function _delete($id)
	{
		// get the workflow point
		$workflowPoint = $this->fetch($id);
		if(!$workflowPoint)
		{
			throw new NotFoundException(['There is no workflow point with that ID.']);
		}
		
		$this->db->table('workflow_steps')
				 ->where('from_id', '=', $id)
				 ->orWhere('to_id', '=', $id)
				 ->delete();

		// delete the workflow point
		$this->db->table('workflow_points')->where('id', '=', $workflowPoint->id)->delete();
		Trunk::forgetType('workflow-point');
		Cache::forget('workflowPoints');
		return $workflowPoint;
	}

	/**
	 * Delete workflow points by workflow from database
	 * 
	 * @param integer $workflowId - ID of workflow
	 * 
	 * @return boolean
	 */
	public function deleteByWorkflow($workflowId)
	{	
		// delete the workflow points
		$this->db->table('workflow_points')->where('workflow_id', '=', $workflowId)->delete();
		$this->db->table('workflow_steps')->where('workflow_id', '=', $workflowId)->delete();
		Trunk::forgetType('workflow-point');
		Cache::forget('workflowPoints');
		return true;
	}
	
	protected function insertSteps($workflowPoint, $steps)
	{
		$stepsParams = [];
		foreach ($steps as $step)
		{
			$stepParams = [
				'workflow_id' => $workflowPoint->workflow->id,
				'from_id' => $workflowPoint->id,
				'to_id' => $step['id']
			];
			$stepsParams[] = $stepParams;
		}

		if( ! empty($stepsParams) )
		{
			$this->db->table('workflow_steps')->insert($stepsParams);
		}

		Trunk::forgetType('workflow-point');
	}

	protected function updateSteps($workflowPoint, $steps)
	{
		$insertSteps = [];
		$deleteStepsIds = [];
		foreach ($steps as $step)
		{
			$exists = false;
			foreach ($workflowPoint->steps as $point)
			{
				if($point->id == $step['id'])
				{
					$exists = true;
					break;
				}
			}
			if(!$exists)
			{
				$insertSteps[] = $step;
			}
		}

		foreach ($workflowPoint->steps as $point)
		{
			$exists = false;
			foreach ($steps as $step)
			{
				if($point->id == $step['id'])
				{
					$exists = true;
					break;
				}
			}
			if(!$exists)
			{
				$deleteStepsIds[] = $point->id;
			}
		}

		if( ! empty($insertSteps) )
		{
			$this->insertSteps($workflowPoint, $insertSteps);
		}

		if( ! empty($deleteStepsIds) )
		{
			$this->db->table('workflow_steps')
					 ->where('from_id', '=', $workflowPoint->id)
					 ->whereIn('to_id', $deleteStepsIds)
					 ->delete();
		}

		Trunk::forgetType('workflow-point');
	}


// ----------------------------------------------------------------------------------------------
// GETTERS
// ----------------------------------------------------------------------------------------------
// 
// 
// 

	/**
	 * Get workflow point by ID
	 * 
	 * @param int $id - ID of workflow point to be fetched
	 * 
	 * @return array
	 */
	protected function _fetch($id, $include = [])
	{
		$params = func_get_args();
		$params['function'] = __METHOD__;
		
		if(Trunk::has($params, 'workflow-point'))
		{
			$workflowPoint = Trunk::get($id, 'workflow-point');
			$workflowPoint->clearIncluded();
			$workflowPoint->load($include);
			$meta = ['id' => $id, 'include' => $include];
			$workflowPoint->setMeta($meta);
			return $workflowPoint;
		}

		$workflowPoint = $this->db->table('workflow_points')->find($id);
		
		if( ! $workflowPoint )
		{
			throw new NotFoundException(['There is no workflow point with that ID.']);
		}

		$workflowPoint->type = 'workflow-point';
		$workflowPoint->workflow = new stdClass();
		$workflowPoint->workflow->id = $workflowPoint->workflow_id;
		$workflowPoint->workflow->type = 'workflow';
		$workflowPoint->steps = [];

		$workflowSteps = $this->db->table('workflow_steps')
								  ->select('to_id')
								  ->where('from_id', '=', $workflowPoint->id)
								  ->get();

		if( ! empty($workflowSteps) )
		{
			foreach ($workflowSteps as $step)
			{
				$workflowStep = new stdClass();
				$workflowStep->id = $step->to_id;
				$workflowStep->type = 'workflow-point';

				$workflowPoint->steps[] = $workflowStep;
			}
		}

		$timezone = (Config::get('app.timezone'))?Config::get('app.timezone'):'UTC';
		$workflowPoint->created_at = Carbon::parse($workflowPoint->created_at)->tz($timezone);
		$workflowPoint->updated_at = Carbon::parse($workflowPoint->updated_at)->tz($timezone);

		$result = new Model($workflowPoint);
		
		$result->setParams($params);
		$meta = ['id' => $id, 'include' => $include];
		$result->setMeta($meta);
		$result->load($include);
		return $result;
	}

	/**
	 * Get workflow points
	 * 
	 * @return array
	 */
	protected function _get($filter = [], $offset = 0, $limit = 0, $sort = [], $include = [])
	{
		$params = func_get_args();
		$params['function'] = __METHOD__;

		if(Trunk::has($params, 'workflow-point'))
		{
			$workflowPoints = Trunk::get($params, 'workflow-point');
			$workflowPoints->clearIncluded();
			$workflowPoints->load($include);
			$meta = [
				'include' => $include
			];
			$workflowPoints->setMeta($meta);
			return $workflowPoints;
		}

		$query = $this->db->table('workflow_points');

		$query = $this->parseFilters($query, $filter);

		$total = $query->count();

		$query = $this->parsePaging($query, $offset, $limit);

		$query = $this->parseSorting($query, $sort);
		
		$workflowPoints = $query->get();

		if( ! $workflowPoints )
		{
			$workflowPoints = [];
		}



		
		$ids = [];
		foreach ($workflowPoints as &$workflowPoint)
		{
			$workflowPoint->type = 'workflow-point';
			$workflowPoint->workflow = new stdClass();
			$workflowPoint->workflow->id = $workflowPoint->workflow_id;
			$workflowPoint->workflow->type = 'workflow';
			$workflowPoint->steps = [];

			$ids[] = $workflowPoint->id;

			$timezone = (Config::get('app.timezone'))?Config::get('app.timezone'):'UTC';
			$workflowPoint->created_at = Carbon::parse($workflowPoint->created_at)->tz($timezone);
			$workflowPoint->updated_at = Carbon::parse($workflowPoint->updated_at)->tz($timezone);
		}

		$workflowSteps = [];

		if( ! empty($ids) )
		{
			$workflowSteps = $this->db->table('workflow_steps')
								  ->select('from_id', 'to_id')
								  ->whereIn('from_id', $ids)
								  ->get();
		}
		

		if( ! empty($workflowSteps) )
		{
			foreach ($workflowSteps as $step)
			{
				foreach ($workflowPoints as &$workflowPoint)
				{
					if($step->from_id == $workflowPoint->id)
					{
						$workflowStep = new stdClass();
						$workflowStep->id = $step->to_id;
						$workflowStep->type = 'workflow-point';

						$workflowPoint->steps[] = $workflowStep;
						break;
					}
				}
				
			}
		}

		$result = new Collection($workflowPoints);
		
		$result->setParams($params);

		$meta = [
			'count' => count($workflowPoints), 
			'offset' => $offset, 
			'limit' => $limit, 
			'total' => $total, 
			'filter' => $filter, 
			'sort' => $sort, 
			'include' => $include
		];
		$result->setMeta($meta);

		$result->load($include);
		
		return $result;
	}


}