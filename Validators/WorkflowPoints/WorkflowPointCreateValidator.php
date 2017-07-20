<?php
/*
 * This file is part of the cookbook/workflows package.
 *
 * (c) Nikola Plavšić <nikolaplavsic@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cookbook\Workflows\Validators\WorkflowPoints;

use Cookbook\Contracts\Workflows\WorkflowPointRepositoryContract;
use Cookbook\Core\Bus\RepositoryCommand;
use Cookbook\Core\Validation\Validator;


/**
 * WorkflowPointCreateValidator class
 * 
 * Validating command for creating workflow point
 * 
 * 
 * @author  	Nikola Plavšić <nikolaplavsic@gmail.com>
 * @copyright  	Nikola Plavšić <nikolaplavsic@gmail.com>
 * @package 	cookbook/workflows
 * @since 		0.1.0-alpha
 * @version  	0.1.0-alpha
 */
class WorkflowPointCreateValidator extends Validator
{

	/**
	 * Repository for workflow points
	 * 
	 * @var \Cookbook\Contracts\Workflows\WorkflowPointRepositoryContract
	 */
	protected $workflowPointRepository;

	/**
	 * Set of rules for validating workflow point
	 *
	 * @var array
	 */
	protected $rules;

	/**
	 * Set of rules for validating workflow point steps
	 *
	 * @var array
	 */
	protected $stepRules;
	
	/**
	 * Create new WorkflowPointCreateValidator
	 * 
	 * @return void
	 */
	public function __construct(WorkflowPointRepositoryContract $workflowPointRepository)
	{
		$this->workflowPointRepository = $workflowPointRepository;
		$this->rules = [
			'workflow_id'			=> 'required|exists:workflows,id',
			'status'				=> ['required', 'min:3', 'max:50', 'regex:/^[0-9a-zA-Z-_]*$/'],
			'endpoint'				=> ['required', 'min:3', 'max:50', 'regex:/^[0-9a-zA-Z-_]*$/'],
			'action'				=> 'required|min:3|max:250',
			'name'					=> 'required|min:3|max:250',
			'public'				=> 'sometimes|boolean',
			'deleted'				=> 'sometimes|boolean',
			'description'			=> 'sometimes',
			'sort_order'			=> 'sometimes|integer',
			'steps'					=> 'sometimes|array'
		];

		$this->stepRules = [
			'id'		=> 'required|exists:workflow_points,id',
			'type' 		=> 'in:workflow-point'
		];

		parent::__construct();

		$this->exception->setErrorKey('workflow-point');
	}


	/**
	 * Validate RepositoryCommand
	 * 
	 * @param Cookbook\Core\Bus\RepositoryCommand $command
	 * 
	 * @todo  Create custom validation for all db related checks (DO THIS FOR ALL VALIDATORS)
	 * @todo  Check all db rules | make validators on repositories
	 * 
	 * @return void
	 */
	public function validate(RepositoryCommand $command)
	{
		$validator = $this->newValidator($command, $this->rules);

		if( isset($command->params['steps']) )
		{
			$validator->each('steps', $this->stepRules);
		}
		$this->setValidator($validator);

		$this->validateParams($command, null, true);

		if( ! $this->exception->hasErrors() )
		{
			$workflowPoints = $this->workflowPointRepository->get(['status' => $command->params['status'], 'workflow_id' => $command->params['workflow_id']]);
			if( count($workflowPoints) > 0 )
			{
				$this->exception->addErrors(['status' => 'This field needs to be unique.']);
			}

			$workflowPoints = $this->workflowPointRepository->get(['endpoint' => $command->params['endpoint'], 'workflow_id' => $command->params['workflow_id']]);
			if( count($workflowPoints) > 0 )
			{
				$this->exception->addErrors(['endpoint' => 'This field needs to be unique.']);
			}
		}

		if( $this->exception->hasErrors() )
		{
			throw $this->exception;
		}
	}
}