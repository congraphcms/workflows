<?php
/*
 * This file is part of the congraph/workflows package.
 *
 * (c) Nikola Plavšić <nikolaplavsic@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Congraph\Workflows\Validators\WorkflowSteps;

use Congraph\Contracts\Workflows\WorkflowPointRepositoryContract;
use Congraph\Contracts\Workflows\WorkflowStepRepositoryContract;
use Congraph\Core\Bus\RepositoryCommand;
use Congraph\Core\Validation\Validator;


/**
 * WorkflowStepUpdateValidator class
 * 
 * Validating command for updating workflow step
 * 
 * 
 * @author  	Nikola Plavšić <nikolaplavsic@gmail.com>
 * @copyright  	Nikola Plavšić <nikolaplavsic@gmail.com>
 * @package 	congraph/workflows
 * @since 		0.1.0-alpha
 * @version  	0.1.0-alpha
 */
class WorkflowStepUpdateValidator extends Validator
{

	/**
	 * Repository for workflows
	 * 
	 * @var \Congraph\Contracts\Workflows\WorkflowStepRepositoryContract
	 */
	protected $workflowStepRepository;

	/**
	 * Repository for workflow points
	 * 
	 * @var \Congraph\Contracts\Workflows\WorkflowStepRepositoryContract
	 */
	protected $workflowPointRepository;

	/**
	 * Set of rules for validating workflow step
	 *
	 * @var array
	 */
	protected $rules;
	
	/**
	 * Create new WorkflowStepUpdateValidator
	 * 
	 * @return void
	 */
	public function __construct(WorkflowStepRepositoryContract $workflowStepRepository, WorkflowPointRepositoryContract $workflowPointRepository)
	{
		$this->workflowStepRepository = $workflowStepRepository;
		$this->workflowPointRepository = $workflowPointRepository;
		$this->rules = [
			'from_id'				=> 'sometimes|required|exists:workflow_points,id',
			'to_id'					=> 'sometimes|required|exists:workflow_points,id'
		];

		parent::__construct();

		$this->exception->setErrorKey('workflow-step');
	}


	/**
	 * Validate RepositoryCommand
	 * 
	 * @param Congraph\Core\Bus\RepositoryCommand $command
	 * 
	 * @todo  Create custom validation for all db related checks (DO THIS FOR ALL VALIDATORS)
	 * @todo  Check all db rules | make validators on repositories
	 * 
	 * @return void
	 */
	public function validate(RepositoryCommand $command)
	{
		$workflowStep = $this->workflowStepRepository->fetch($command->id);
		$this->validateParams($command->params, $this->rules, true);

		if( ! $this->exception->hasErrors() )
		{
			if( isset($command->params['from_id']) )
			{
				$fromPoint = $this->workflowPointRepository->fetch($command->params['from_id']);
				if( $fromPoint->workflow_id != $workflowStep->workflow_id )
				{
					$this->exception->addErrors(['from_id' => 'This point has to be from the same workflow']);
				}

				$workflowSteps = $this->workflowStepRepository->get(
					[
						'from_id' => $command->params['from_id'], 
						'to_id' => (isset($command->params['to_id']))?$command->params['to_id']:$workflowStep->to_id,
						'id' => ['ne' => $workflowStep->id]
					]
				);
				if( count($workflowSteps) > 0 )
				{
					$this->exception->addErrors(['This step is already defined.']);
				}
			}

			if( isset($command->params['to_id']) )
			{
				$toPoint = $this->workflowPointRepository->fetch($command->params['to_id']);
				if( $toPoint->workflow_id != $workflowStep->workflow_id )
				{
					$this->exception->addErrors(['to_id' => 'This point has to be from the same workflow']);
				}

				$workflowSteps = $this->workflowStepRepository->get(
					[
						'from_id' => (isset($command->params['from_id']))?$command->params['from_id']:$workflowStep->from_id,
						'to_id' => $command->params['to_id'],
						'id' => ['ne' => $workflowStep->id]
					]
				);
				if( count($workflowSteps) > 0 )
				{
					$this->exception->addErrors(['This step is already defined.']);
				}
			}
		}

		if( $this->exception->hasErrors() )
		{
			throw $this->exception;
		}
	}
}