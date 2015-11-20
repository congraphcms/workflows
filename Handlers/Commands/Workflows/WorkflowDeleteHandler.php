<?php
/*
 * This file is part of the cookbook/workflows package.
 *
 * (c) Nikola Plavšić <nikolaplavsic@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cookbook\Workflows\Handlers\Commands\Workflows;


use Cookbook\Contracts\Workflows\WorkflowPointRepositoryContract;
use Cookbook\Contracts\Workflows\WorkflowRepositoryContract;
use Cookbook\Contracts\Workflows\WorkflowStepRepositoryContract;
use Cookbook\Core\Bus\RepositoryCommand;
use Cookbook\Core\Bus\RepositoryCommandHandler;

/**
 * WorkflowDeleteHandler class
 * 
 * Handling command for deleting workflow
 * 
 * 
 * @author  	Nikola Plavšić <nikolaplavsic@gmail.com>
 * @copyright  	Nikola Plavšić <nikolaplavsic@gmail.com>
 * @package 	cookbook/workflows
 * @since 		0.1.0-alpha
 * @version  	0.1.0-alpha
 */
class WorkflowDeleteHandler extends RepositoryCommandHandler
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
	 * Create new WorkflowDeleteHandler
	 * 
	 * @param Cookbook\Contracts\Workflows\WorkflowRepositoryContract $repository
	 * 
	 * @return void
	 */
	public function __construct(
		WorkflowRepositoryContract $repository, 
		WorkflowPointRepositoryContract $workflowPointRepository, 
		WorkflowStepRepositoryContract $workflowStepRepository
	)
	{
		$this->workflowPointRepository = $workflowPointRepository;
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
		$workflow = $this->repository->delete($command->id);
		$this->workflowPointRepository->deleteByWorkflow($workflow->id);

		return $workflow->id;
	}
}