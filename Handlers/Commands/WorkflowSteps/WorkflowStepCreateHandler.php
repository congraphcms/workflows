<?php
/*
 * This file is part of the congraph/workflows package.
 *
 * (c) Nikola Plavšić <nikolaplavsic@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Congraph\Workflows\Handlers\Commands\WorkflowSteps;


use Congraph\Contracts\Workflows\WorkflowStepRepositoryContract;
use Congraph\Core\Bus\RepositoryCommandHandler;
use Congraph\Core\Bus\RepositoryCommand;

/**
 * WorkflowStepCreateHandler class
 * 
 * Handling command for creating workflow step
 * 
 * 
 * @author  	Nikola Plavšić <nikolaplavsic@gmail.com>
 * @copyright  	Nikola Plavšić <nikolaplavsic@gmail.com>
 * @package 	congraph/workflows
 * @since 		0.1.0-alpha
 * @version  	0.1.0-alpha
 */
class WorkflowStepCreateHandler extends RepositoryCommandHandler
{

	/**
	 * Create new WorkflowStepCreateHandler
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
	 * @param Congraph\Core\Bus\RepositoryCommand $command
	 * 
	 * @return void
	 */
	public function handle(RepositoryCommand $command)
	{
		$workflowStep = $this->repository->create($command->params);

		return $workflowStep;
	}
}