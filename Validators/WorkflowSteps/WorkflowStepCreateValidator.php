<?php
/*
 * This file is part of the cookbook/workflows package.
 *
 * (c) Nikola Plavšić <nikolaplavsic@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cookbook\Workflows\Validators\WorkflowSteps;

use Cookbook\Contracts\Workflows\WorkflowPointRepositoryContract;
use Cookbook\Contracts\Workflows\WorkflowStepRepositoryContract;
use Cookbook\Core\Bus\RepositoryCommand;
use Cookbook\Core\Validation\Validator;


/**
 * WorkflowStepCreateValidator class
 * 
 * Validating command for creating workflow step
 * 
 * 
 * @author  	Nikola Plavšić <nikolaplavsic@gmail.com>
 * @copyright  	Nikola Plavšić <nikolaplavsic@gmail.com>
 * @package 	cookbook/workflows
 * @since 		0.1.0-alpha
 * @version  	0.1.0-alpha
 */
class WorkflowStepCreateValidator extends Validator
{

	/**
	 * Repository for workflow steps
	 * 
	 * @var \Cookbook\Contracts\Workflows\WorkflowStepRepositoryContract
	 */
	protected $workflowStepRepository;

	/**
	 * Repository for workflow points
	 * 
	 * @var \Cookbook\Contracts\Workflows\WorkflowStepRepositoryContract
	 */
	protected $workflowPointRepository;

	/**
	 * Set of rules for validating workflow step
	 *
	 * @var array
	 */
	protected $rules;
	
	/**
	 * Create new WorkflowPointCreateValidator
	 * 
	 * @return void
	 */
	public function __construct(WorkflowStepRepositoryContract $workflowStepRepository, WorkflowPointRepositoryContract $workflowPointRepository)
	{
		$this->workflowStepRepository = $workflowStepRepository;
		$this->workflowPointRepository = $workflowPointRepository;
		$this->rules = [
			'workflow_id'			=> 'required|exists:workflows,id',
			'from_id'				=> 'required|exists:workflow_points,id',
			'to_id'					=> 'required|exists:workflow_points,id'
		];

		parent::__construct();

		$this->exception->setErrorKey('workflow-step');
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
		$this->validateParams($command, $this->rules, true);

		if( ! $this->exception->hasErrors() )
		{
			$fromPoint = $this->workflowPointRepository->fetch($command->params['from_id']);
			if( $fromPoint->workflow_id != $command->params['workflow_id'] )
			{
				$this->exception->addErrors(['from_id' => 'This point has to be from the same workflow']);
			}

			$toPoint = $this->workflowPointRepository->fetch($command->params['to_id']);
			if( $toPoint->workflow_id != $command->params['workflow_id'] )
			{
				$this->exception->addErrors(['to_id' => 'This point has to be from the same workflow']);
			}


			$workflowSteps = $this->workflowStepRepository->get(['from_id' => $command->params['from_id'], 'to_id' => $command->params['to_id']]);
			if( count($workflowSteps) > 0 )
			{
				$this->exception->addErrors(['This step is already defined.']);
			}
		}

		if( $this->exception->hasErrors() )
		{
			throw $this->exception;
		}
	}
}