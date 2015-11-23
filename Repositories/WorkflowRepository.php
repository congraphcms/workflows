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

use Cookbook\Contracts\Workflows\WorkflowRepositoryContract;
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
use stdClass;

/**
 * WorkflowRepository class
 * 
 * Repository for workflow database queries
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
class WorkflowRepository extends AbstractRepository implements WorkflowRepositoryContract//, UsesCache
{

// ----------------------------------------------------------------------------------------------
// PARAMS
// ----------------------------------------------------------------------------------------------
// 
// 
// 

	/**
	 * Create new WorkflowRepository
	 * 
	 * @param Illuminate\Database\Connection $db
	 * 
	 * @return void
	 */
	public function __construct(Connection $db)
	{
		$this->type = 'workflow';

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
	 * Create new workflow
	 * 
	 * @param array $model - workflow params (code, name, description...)
	 * 
	 * @return mixed
	 * 
	 * @throws Exception
	 */
	protected function _create($model)
	{
		$model['created_at'] = $model['updated_at'] = Carbon::now('UTC')->toDateTimeString();

		// insert workflow in database
		$workflowId = $this->db->table('workflows')->insertGetId($model);

		// get workflow
		$workflow = $this->fetch($workflowId);

		if(!$workflow)
		{
			throw new \Exception('Failed to insert workflow');
		}

		// and return newly created workflow
		return $workflow;
		
	}

	/**
	 * Update workflow
	 * 
	 * @param array $model - workflow params (code, name, description...)
	 *
	 * @return mixed
	 * 
	 * @throws Cookbook\Core\Exceptions\NotFoundException
	 */
	protected function _update($id, $model)
	{

		// find workflow with that ID
		$workflow = $this->fetch($id);

		if( ! $workflow )
		{
			throw new NotFoundException(['There is no workflow with that ID.']);
		}

		$model['updated_at'] = Carbon::now('UTC')->toDateTimeString();

		$this->db->table('workflows')->where('id', '=', $id)->update($model);

		Trunk::forgetType('workflow');
		$workflow = $this->fetch($id);

		// and return workflow
		return $workflow;
	}

	/**
	 * Delete workflow from database
	 * 
	 * @param integer $id - ID of workflow that will be deleted
	 * 
	 * @return boolean
	 * 
	 * @throws Cookbook\Core\Exceptions\NotFoundException
	 */
	protected function _delete($id)
	{
		// get the workflow
		$workflow = $this->fetch($id);
		if(!$workflow)
		{
			throw new NotFoundException(['There is no workflow with that ID.']);
		}
		
		// delete the workflow
		$this->db->table('workflows')->where('id', '=', $workflow->id)->delete();
		Trunk::forgetType('workflow');
		return $workflow;
	}
	


// ----------------------------------------------------------------------------------------------
// GETTERS
// ----------------------------------------------------------------------------------------------
// 
// 
// 

	/**
	 * Get workflow by ID
	 * 
	 * @param int $id - ID of workflow to be fetched
	 * 
	 * @return array
	 */
	protected function _fetch($id, $include = [])
	{
		$params = func_get_args();
		$params['function'] = __METHOD__;
		
		if(Trunk::has($params, 'workflow'))
		{
			$workflow = Trunk::get($id, 'workflow');
			$workflow->clearIncluded();
			$workflow->load($include);
			$meta = ['id' => $id, 'include' => $include];
			$workflow->setMeta($meta);
			return $workflow;
		}

		if(is_string($id) && preg_match('/^[a-z]{2}(_[A-Z]{1}[a-z]{3})?(_[A-Z]{2})?$/', $id))
		{
			return $this->fetchByCode($id, $include);
		}

		$workflow = $this->db->table('workflows')->find($id);
		
		if( ! $workflow )
		{
			throw new NotFoundException(['There is no workflow with that ID.']);
		}

		$points = $this->db->table('workflow_points')
						  ->select('id')
						  ->where('workflow_id', '=', $id)
						  ->orderBy('sort_order')
						  ->orderBy('id')
						  ->get();
		
		$workflow->points = [];
		foreach ($points as $point) 
		{
			$workflowPoint = new stdClass();
			$workflowPoint->id = $point->id;
			$workflowPoint->type = 'workflow-point';
			
			$workflow->points[] = $workflowPoint;
		}

		$workflow->type = 'workflow';

		$timezone = (Config::get('app.timezone'))?Config::get('app.timezone'):'UTC';
		$workflow->created_at = Carbon::parse($workflow->created_at)->tz($timezone);
		$workflow->updated_at = Carbon::parse($workflow->updated_at)->tz($timezone);

		$result = new Model($workflow);
		
		$result->setParams($params);
		$meta = ['id' => $id, 'include' => $include];
		$result->setMeta($meta);
		$result->load($include);
		return $result;
	}

	/**
	 * Get workflows
	 * 
	 * @return array
	 */
	protected function _get($filter = [], $offset = 0, $limit = 0, $sort = [], $include = [])
	{
		$params = func_get_args();
		$params['function'] = __METHOD__;

		if(Trunk::has($params, 'workflow'))
		{
			$workflows = Trunk::get($params, 'workflow');
			$workflows->clearIncluded();
			$workflows->load($include);
			$meta = [
				'include' => $include
			];
			$workflows->setMeta($meta);
			return $workflows;
		}

		$query = $this->db->table('workflows');

		$query = $this->parseFilters($query, $filter);

		$total = $query->count();

		$query = $this->parsePaging($query, $offset, $limit);

		$query = $this->parseSorting($query, $sort);
		
		$workflows = $query->get();

		if( ! $workflows )
		{
			$workflows = [];
		}

		$workflowIds = [];
		
		foreach ($workflows as &$workflow) {
			$workflow->type = 'workflow';
			$timezone = (Config::get('app.timezone'))?Config::get('app.timezone'):'UTC';
			$workflow->created_at = Carbon::parse($workflow->created_at)->tz($timezone);
			$workflow->updated_at = Carbon::parse($workflow->updated_at)->tz($timezone);
			$workflowIds[] = $workflow->id;
		}

		$points = [];
		
		if( ! empty($workflowIds) )
		{
			$points = $this->db->table('workflow_points')
						  ->select('id', 'workflow_id')
						  ->whereIn('workflow_id', $workflowIds)
						  ->orderBy('sort_order')
						  ->orderBy('id')
						  ->get();
		}
		
		
		foreach ($points as $point) 
		{
			$workflowPoint = new stdClass();
			$workflowPoint->id = $point->id;
			$workflowPoint->type = 'workflow-point';

			foreach ($workflows as &$workflow)
			{
				if($workflow->id == $point->workflow_id)
				{
					$workflow->points[] = $workflowPoint;
					break;
				}
			}
		}

		$result = new Collection($workflows);
		
		$result->setParams($params);

		$meta = [
			'count' => count($workflows), 
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