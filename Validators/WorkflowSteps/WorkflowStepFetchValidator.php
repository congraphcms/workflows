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

use Cookbook\Contracts\Workflows\WorkflowStepRepositoryContract;
use Cookbook\Core\Bus\RepositoryCommand;
use Cookbook\Core\Validation\Validator;


/**
 * WorkflowStepFetchValidator class
 * 
 * Validating command for deleting workflow step
 * 
 * 
 * @author  	Nikola Plavšić <nikolaplavsic@gmail.com>
 * @copyright  	Nikola Plavšić <nikolaplavsic@gmail.com>
 * @package 	cookbook/workflows
 * @since 		0.1.0-alpha
 * @version  	0.1.0-alpha
 */
class WorkflowStepFetchValidator extends Validator
{

	/**
	 * Repository for workflow steps
	 * 
	 * @var \Cookbook\Contracts\Workflows\WorkflowStepRepositoryContract
	 */
	protected $workflowStepRepository;
	
	/**
	 * Create new WorkflowStepFetchValidator
	 * 
	 * @return void
	 */
	public function __construct(WorkflowStepRepositoryContract $workflowStepRepository)
	{
		$this->workflowStepRepository = $workflowStepRepository;
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
		$workflowStep = $this->workflowStepRepository->fetch($command->id);
	}
}