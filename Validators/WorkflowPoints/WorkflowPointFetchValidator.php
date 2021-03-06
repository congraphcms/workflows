<?php
/*
 * This file is part of the congraph/workflows package.
 *
 * (c) Nikola Plavšić <nikolaplavsic@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Congraph\Workflows\Validators\WorkflowPoints;

use Congraph\Contracts\Workflows\WorkflowPointRepositoryContract;
use Congraph\Core\Bus\RepositoryCommand;
use Congraph\Core\Validation\Validator;


/**
 * WorkflowPointFetchValidator class
 * 
 * Validating command for fetching workflow point
 * 
 * 
 * @author  	Nikola Plavšić <nikolaplavsic@gmail.com>
 * @copyright  	Nikola Plavšić <nikolaplavsic@gmail.com>
 * @package 	congraph/workflows
 * @since 		0.1.0-alpha
 * @version  	0.1.0-alpha
 */
class WorkflowPointFetchValidator extends Validator
{

	/**
	 * Repository for workflow points
	 * 
	 * @var \Congraph\Contracts\Workflows\WorkflowPointRepositoryContract
	 */
	protected $workflowPointRepository;
	
	/**
	 * Create new WorkflowPointFetchValidator
	 * 
	 * @return void
	 */
	public function __construct(WorkflowPointRepositoryContract $workflowPointRepository)
	{
		$this->workflowPointRepository = $workflowPointRepository;
		parent::__construct();

		$this->exception->setErrorKey('workflow-point');
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
		$workflowPoint = $this->workflowPointRepository->fetch($command->id);
	}
}