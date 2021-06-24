<?php
/*
 * This file is part of the congraph/workflows package.
 *
 * (c) Nikola Plavšić <nikolaplavsic@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Congraph\Workflows\Commands\WorkflowSteps;

use Congraph\Contracts\Workflows\WorkflowStepRepositoryContract;
use Congraph\Core\Bus\RepositoryCommand;

/**
 * WorkflowStepFetchCommand class
 * 
 * Command for fetching workflow step
 * 
 * @author  	Nikola Plavšić <nikolaplavsic@gmail.com>
 * @copyright  	Nikola Plavšić <nikolaplavsic@gmail.com>
 * @package 	congraph/workflows
 * @since 		0.1.0-alpha
 * @version  	0.1.0-alpha
 */
class WorkflowStepFetchCommand extends RepositoryCommand
{

    /**
	 * Create new WorkflowStepFetchCommand
	 * 
	 * @param Congraph\Contracts\Workflows\WorkflowStepRepositoryContract $repository
	 * 
	 * @return void
	 */
	public function __construct(WorkflowStepRepositoryContract $repository)
	{
		parent::__construct($repository);
	}

	/**
	 * Handle RepositoryCommand
	 *
	 * 
	 * @return void
	 */
	public function handle()
	{
		$workflowStep = $this->repository->fetch(
			$this->id,
			(!empty($this->params['include']))?$this->params['include']:[]
		);

		return $workflowStep;
	}
}
