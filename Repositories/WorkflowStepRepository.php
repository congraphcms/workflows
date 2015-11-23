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

use Cookbook\Contracts\Workflows\WorkflowStepRepositoryContract;
use Cookbook\Core\Exceptions\Exception;
use Cookbook\Core\Exceptions\NotFoundException;
use Cookbook\Core\Facades\Trunk;
use Cookbook\Core\Repositories\AbstractRepository;
use Cookbook\Core\Repositories\Collection;
use Cookbook\Core\Repositories\Model;
use Cookbook\Core\Repositories\UsesCache;
use Illuminate\Database\Connection;
use Illuminate\Support\Facades\Config;
use Carbon\Carbon;
use StdClass;

/**
 * WorkflowStepRepository class
 * 
 * Repository for step database queries
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
class WorkflowStepRepository extends AbstractRepository implements WorkflowStepRepositoryContract//, UsesCache
{

// ----------------------------------------------------------------------------------------------
// PARAMS
// ----------------------------------------------------------------------------------------------
// 
// 
// 

	/**
	 * Create new WorkflowStepRepository
	 * 
	 * @param Illuminate\Database\Connection $db
	 * 
	 * @return void
	 */
	public function __construct(Connection $db)
	{
		$this->type = 'workflow-step';

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
	 * Create new workflow step
	 * 
	 * @param array $model - step params (status, action, name, description...)
	 * 
	 * @return mixed
	 * 
	 * @throws Exception
	 */
	protected function _create($model)
	{
		$model['created_at'] = $model['updated_at'] = Carbon::now('UTC')->toDateTimeString();

		// insert workflow in database
		$workflowStepId = $this->db->table('workflow_steps')->insertGetId($model);

		// get workflow step
		$workflowStep = $this->fetch($workflowStepId);

		if(!$workflowStep)
		{
			throw new \Exception('Failed to insert workflow step');
		}

		// and return newly created workflow step
		return $workflowStep;
		
	}

	/**
	 * Update workflow step
	 * 
	 * @param array $model - workflow step params (status, action, name, description...)
	 *
	 * @return mixed
	 * 
	 * @throws Cookbook\Core\Exceptions\NotFoundException
	 */
	protected function _update($id, $model)
	{

		// find workflow step with that ID
		$workflowStep = $this->fetch($id);

		if( ! $workflowStep )
		{
			throw new NotFoundException(['There is no workflow step with that ID.']);
		}

		$model['updated_at'] = Carbon::now('UTC')->toDateTimeString();

		$this->db->table('workflow_steps')->where('id', '=', $id)->update($model);

		Trunk::forgetType('workflow-step');
		$workflowStep = $this->fetch($id);

		// and return workflow step
		return $workflowStep;
	}

	/**
	 * Delete workflow step from database
	 * 
	 * @param integer $id - ID of workflow step that will be deleted
	 * 
	 * @return boolean
	 * 
	 * @throws Cookbook\Core\Exceptions\NotFoundException
	 */
	protected function _delete($id)
	{
		// get the workflow step
		$workflowStep = $this->fetch($id);
		if(!$workflowStep)
		{
			throw new NotFoundException(['There is no workflow step with that ID.']);
		}
		
		// delete the workflow
		$this->db->table('workflow_steps')->where('id', '=', $workflowStep->id)->delete();
		Trunk::forgetType('workflow-step');
		return $workflowStep;
	}

	/**
	 * Delete workflow steps by workflow from database
	 * 
	 * @param integer $workflowId - ID of workflow
	 * 
	 * @return boolean
	 */
	public function deleteByWorkflow($workflowId)
	{	
		// delete the workflow steps
		$this->db->table('workflow_steps')->where('workflow_id', '=', $workflowId)->delete();
		Trunk::forgetType('workflow-step');
		return true;
	}

	/**
	 * Delete workflow steps by workflow point
	 * 
	 * @param integer $pointId - ID of workflow point
	 * 
	 * @return boolean
	 */
	public function deleteByPoint($pointId)
	{	
		// delete the workflow steps
		$this->db->table('workflow_steps')
				 ->where('from_id', '=', $pointId)
				 ->orWhere('to_id', '=', $pointId)
				 ->delete();
		Trunk::forgetType('workflow-step');
		return true;
	}
	


// ----------------------------------------------------------------------------------------------
// GETTERS
// ----------------------------------------------------------------------------------------------
// 
// 
// 

	/**
	 * Get workflow step by ID
	 * 
	 * @param int $id - ID of workflow step to be fetched
	 * 
	 * @return array
	 */
	protected function _fetch($id, $include = [])
	{
		$params = func_get_args();
		$params['function'] = __METHOD__;
		
		if(Trunk::has($params, 'workflow-step'))
		{
			$workflowStep = Trunk::get($id, 'workflow-step');
			$workflowStep->clearIncluded();
			$workflowStep->load($include);
			$meta = ['id' => $id, 'include' => $include];
			$workflowStep->setMeta($meta);
			return $workflowStep;
		}

		$workflowStep = $this->db->table('workflow_steps')->find($id);
		
		if( ! $workflowStep )
		{
			throw new NotFoundException(['There is no workflow step with that ID.']);
		}

		$workflowStep->type = 'workflow-step';
		$workflowStep->workflow = new StdClass();
		$workflowStep->workflow->id = $workflowStep->workflow_id;
		$workflowStep->workflow->type = 'workflow';

		$workflowStep->from = new StdClass();
		$workflowStep->from->id = $workflowStep->from_id;
		$workflowStep->from->type = 'workflow-point';

		$workflowStep->to = new StdClass();
		$workflowStep->to->id = $workflowStep->to_id;
		$workflowStep->to->type = 'workflow-point';

		$timezone = (Config::get('app.timezone'))?Config::get('app.timezone'):'UTC';
		$workflowStep->created_at = Carbon::parse($workflowStep->created_at)->tz($timezone);
		$workflowStep->updated_at = Carbon::parse($workflowStep->updated_at)->tz($timezone);

		$result = new Model($workflowStep);
		
		$result->setParams($params);
		$meta = ['id' => $id, 'include' => $include];
		$result->setMeta($meta);
		$result->load($include);
		return $result;
	}

	/**
	 * Get workflow steps
	 * 
	 * @return array
	 */
	protected function _get($filter = [], $offset = 0, $limit = 0, $sort = [], $include = [])
	{
		$params = func_get_args();
		$params['function'] = __METHOD__;

		if(Trunk::has($params, 'workflow-step'))
		{
			$workflowSteps = Trunk::get($params, 'workflow-step');
			$workflowSteps->clearIncluded();
			$workflowSteps->load($include);
			$meta = [
				'include' => $include
			];
			$workflowSteps->setMeta($meta);
			return $workflows;
		}

		$query = $this->db->table('workflow_steps');

		$query = $this->parseFilters($query, $filter);

		$total = $query->count();

		$query = $this->parsePaging($query, $offset, $limit);

		$query = $this->parseSorting($query, $sort);
		
		$workflowSteps = $query->get();

		if( ! $workflowSteps )
		{
			$workflowSteps = [];
		}
		
		foreach ($workflowSteps as &$workflowStep) {
			$workflowStep->type = 'workflow-step';

			$workflowStep->workflow = new StdClass();
			$workflowStep->workflow->id = $workflowStep->workflow_id;
			$workflowStep->workflow->type = 'workflow';

			$workflowStep->from = new StdClass();
			$workflowStep->from->id = $workflowStep->from_id;
			$workflowStep->from->type = 'workflow-point';

			$workflowStep->to = new StdClass();
			$workflowStep->to->id = $workflowStep->to_id;
			$workflowStep->to->type = 'workflow-point';

			$timezone = (Config::get('app.timezone'))?Config::get('app.timezone'):'UTC';
			$workflowStep->created_at = Carbon::parse($workflowStep->created_at)->tz($timezone);
			$workflowStep->updated_at = Carbon::parse($workflowStep->updated_at)->tz($timezone);
		}

		$result = new Collection($workflowSteps);
		
		$result->setParams($params);

		$meta = [
			'count' => count($workflowSteps), 
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