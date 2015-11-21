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
 * WorkflowPointUpdateValidator class
 * 
 * Validating command for updating workflow point
 * 
 * 
 * @author  	Nikola Plavšić <nikolaplavsic@gmail.com>
 * @copyright  	Nikola Plavšić <nikolaplavsic@gmail.com>
 * @package 	cookbook/workflows
 * @since 		0.1.0-alpha
 * @version  	0.1.0-alpha
 */
class WorkflowPointUpdateValidator extends Validator
{

	/**
	 * Repository for workflows
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
	 * Create new WorkflowPointUpdateValidator
	 * 
	 * @return void
	 */
	public function __construct(WorkflowPointRepositoryContract $workflowPointRepository)
	{
		$this->workflowPointRepository = $workflowPointRepository;
		$this->rules = [
			'status'				=> ['sometimes', 'required', 'min:3', 'max:50', 'regex:/^[0-9a-z-_]*$/'],
			'endpoint'				=> ['sometimes', 'required', 'min:3', 'max:50', 'regex:/^[0-9a-z-_]*$/'],
			'action'				=> 'sometimes|required|min:3|max:250',
			'name'					=> 'sometimes|required|min:3|max:250',
			'description'			=> 'sometimes',
			'public'				=> 'sometimes|boolean',
			'deleted'				=> 'sometimes|boolean',
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
		$workflowPoint = $this->workflowPointRepository->fetch($command->id);
		$validator = $this->newValidator($command->params, $this->rules);

		if( isset($command->params['steps']) )
		{
			$validator->each('steps', $this->stepRules);
		}
		$this->setValidator($validator);

		$this->validateParams($command->params, null, true);

		if( ! $this->exception->hasErrors() )
		{
			if( isset($command->params['steps']) )
			{
				$validator->each('steps', $this->stepRules);
				foreach ($command->params['steps'] as $key => $step)
				{
					if($step['id'] == $command->id)
					{
						$this->exception->addErrors(['steps.' . $key => 'Invalid step point.']);
					}
				}
			}

			if( isset($command->params['status']) )
			{
				$workflowPoints = $this->workflowPointRepository->get(
					[
						'status' => $command->params['status'], 
						'workflow_id' => $workflowPoint->workflow_id,
						'id' => ['ne' => $workflowPoint->id]
					]
				);
				if( count($workflowPoints) > 0 )
				{
					$this->exception->addErrors(['status' => 'This field needs to be unique.']);
				}
			}

			if( isset($command->params['endpoint']) )
			{
				$workflowPoints = $this->workflowPointRepository->get(
					[
						'endpoint' => $command->params['endpoint'], 
						'workflow_id' => $workflowPoint->workflow_id,
						'id' => ['ne' => $command->id]
					]
				);
				if( count($workflowPoints) > 0 )
				{
					$this->exception->addErrors(['endpoint' => 'This field needs to be unique.']);
				}
			}
		}

		if( $this->exception->hasErrors() )
		{
			throw $this->exception;
		}
	}
}