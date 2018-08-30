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

use Congraph\Contracts\Workflows\WorkflowStepRepositoryContract;
use Congraph\Core\Bus\RepositoryCommand;
use Congraph\Core\Validation\Validator;


/**
 * WorkflowStepDeleteValidator class
 * 
 * Validating command for deleting workflow step
 * 
 * 
 * @author  	Nikola Plavšić <nikolaplavsic@gmail.com>
 * @copyright  	Nikola Plavšić <nikolaplavsic@gmail.com>
 * @package 	congraph/workflows
 * @since 		0.1.0-alpha
 * @version  	0.1.0-alpha
 */
class WorkflowStepDeleteValidator extends Validator
{

	/**
	 * Repository for workflow steps
	 * 
	 * @var \Congraph\Contracts\Workflows\WorkflowStepRepositoryContract
	 */
	protected $workflowStepRepository;
	
	/**
	 * Create new WorkflowStepDeleteValidator
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
	}
}