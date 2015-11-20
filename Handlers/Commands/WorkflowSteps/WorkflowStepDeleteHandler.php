<?php
/*
 * This file is part of the cookbook/workflows package.
 *
 * (c) Nikola Plavšić <nikolaplavsic@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cookbook\Workflows\Handlers\Commands\WorkflowSteps;


use Cookbook\Contracts\Workflows\WorkflowStepRepositoryContract;
use Cookbook\Core\Bus\RepositoryCommandHandler;
use Cookbook\Core\Bus\RepositoryCommand;

/**
 * WorkflowStepDeleteHandler class
 * 
 * Handling command for deleting workflow step
 * 
 * 
 * @author  	Nikola Plavšić <nikolaplavsic@gmail.com>
 * @copyright  	Nikola Plavšić <nikolaplavsic@gmail.com>
 * @package 	cookbook/workflows
 * @since 		0.1.0-alpha
 * @version  	0.1.0-alpha
 */
class WorkflowStepDeleteHandler extends RepositoryCommandHandler
{

	/**
	 * Create new WorkflowStepDeleteHandler
	 * 
	 * @param Cookbook\Contracts\Workflows\WorkflowStepRepositoryContract $repository
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
	 * @param Cookbook\Core\Bus\RepositoryCommand $command
	 * 
	 * @return void
	 */
	public function handle(RepositoryCommand $command)
	{
		$workflowStep = $this->repository->delete($command->id);

		return $workflowStep->id;
	}
}