<?php
/*
 * This file is part of the congraph/workflows package.
 *
 * (c) Nikola Plavšić <nikolaplavsic@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Congraph\Workflows\Handlers\Commands\Workflows;


use Congraph\Contracts\Workflows\WorkflowPointRepositoryContract;
use Congraph\Contracts\Workflows\WorkflowRepositoryContract;
use Congraph\Contracts\Workflows\WorkflowStepRepositoryContract;
use Congraph\Core\Bus\RepositoryCommand;
use Congraph\Core\Bus\RepositoryCommandHandler;

/**
 * WorkflowDeleteHandler class
 * 
 * Handling command for deleting workflow
 * 
 * 
 * @author  	Nikola Plavšić <nikolaplavsic@gmail.com>
 * @copyright  	Nikola Plavšić <nikolaplavsic@gmail.com>
 * @package 	congraph/workflows
 * @since 		0.1.0-alpha
 * @version  	0.1.0-alpha
 */
class WorkflowDeleteHandler extends RepositoryCommandHandler
{

	/**
	 * Repository for workflow steps
	 * 
	 * @var \Congraph\Contracts\Workflows\WorkflowStepRepositoryContract
	 */
	protected $workflowStepRepository;

	/**
	 * Repository for workflow points
	 * 
	 * @var \Congraph\Contracts\Workflows\WorkflowStepRepositoryContract
	 */
	protected $workflowPointRepository;


	/**
	 * Create new WorkflowDeleteHandler
	 * 
	 * @param Congraph\Contracts\Workflows\WorkflowRepositoryContract $repository
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
	 * @param Congraph\Core\Bus\RepositoryCommand $command
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