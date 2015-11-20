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
use Cookbook\Contracts\Workflows\WorkflowStepRepositoryContract;
use Cookbook\Core\Bus\RepositoryCommand;
use Cookbook\Core\Bus\RepositoryCommandHandler;

/**
 * WorkflowPointDeleteHandler class
 * 
 * Handling command for deleting workflow point
 * 
 * 
 * @author  	Nikola Plavšić <nikolaplavsic@gmail.com>
 * @copyright  	Nikola Plavšić <nikolaplavsic@gmail.com>
 * @package 	cookbook/workflows
 * @since 		0.1.0-alpha
 * @version  	0.1.0-alpha
 */
class WorkflowPointDeleteHandler extends RepositoryCommandHandler
{

	/**
	 * Repository for workflow steps
	 * 
	 * @var \Cookbook\Contracts\Workflows\WorkflowStepRepositoryContract
	 */
	protected $workflowStepRepository;

	/**
	 * Create new WorkflowPointDeleteHandler
	 * 
	 * @param Cookbook\Contracts\Workflows\WorkflowPointRepositoryContract $repository
	 * 
	 * @return void
	 */
	public function __construct(
		WorkflowPointRepositoryContract $repository,
		WorkflowStepRepositoryContract $workflowStepRepository
	)
	{
		$this->workflowStepRepository = $workflowStepRepository;
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
		$workflowPoint = $this->repository->delete($command->id);

		return $workflowPoint->id;
	}
}