<?php
/*
 * This file is part of the congraph/workflows package.
 *
 * (c) Nikola Plavšić <nikolaplavsic@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Congraph\Workflows\Commands\Workflows;

use Congraph\Contracts\Workflows\WorkflowPointRepositoryContract;
use Congraph\Contracts\Workflows\WorkflowRepositoryContract;
use Congraph\Contracts\Workflows\WorkflowStepRepositoryContract;
use Congraph\Core\Bus\RepositoryCommand;

/**
 * WorkflowDeleteCommand class
 * 
 * Command for deleting workflow
 * 
 * @author  	Nikola Plavšić <nikolaplavsic@gmail.com>
 * @copyright  	Nikola Plavšić <nikolaplavsic@gmail.com>
 * @package 	congraph/workflows
 * @since 		0.1.0-alpha
 * @version  	0.1.0-alpha
 */
class WorkflowDeleteCommand extends RepositoryCommand
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
	 * Create new WorkflowDeleteCommand
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
	 * @return void
	 */
	public function handle()
	{
		$workflow = $this->repository->delete($this->id);
		$this->workflowPointRepository->deleteByWorkflow($workflow->id);

		return $workflow->id;
	}
}
