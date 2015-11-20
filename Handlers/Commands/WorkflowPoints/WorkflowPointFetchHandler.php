<?php
/*
 * This file is part of the cookbook/workflows package.
 *
 * (c) Nikola Plavšić <nikolaplavsic@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cookbook\Workflows\Handlers\Commands\WorkflowPoints;


use Cookbook\Contracts\Workflows\WorkflowPointRepositoryContract;
use Cookbook\Core\Bus\RepositoryCommandHandler;
use Cookbook\Core\Bus\RepositoryCommand;

/**
 * WorkflowPointFetchHandler class
 * 
 * Handling command for fetching workflow point
 * 
 * 
 * @author  	Nikola Plavšić <nikolaplavsic@gmail.com>
 * @copyright  	Nikola Plavšić <nikolaplavsic@gmail.com>
 * @package 	cookbook/workflows
 * @since 		0.1.0-alpha
 * @version  	0.1.0-alpha
 */
class WorkflowPointFetchHandler extends RepositoryCommandHandler
{

	/**
	 * Create new WorkflowPointFetchHandler
	 * 
	 * @param Cookbook\Contracts\Workflows\WorkflowPointRepositoryContract $repository
	 * 
	 * @return void
	 */
	public function __construct(WorkflowPointRepositoryContract $repository)
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
		$workflowPoint = $this->repository->fetch(
			$command->id,
			(!empty($command->params['include']))?$command->params['include']:[]
		);

		return $workflowPoint;
	}
}