<?php
/*
 * This file is part of the congraph/workflows package.
 *
 * (c) Nikola Plavšić <nikolaplavsic@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Congraph\Workflows\Handlers\Commands\WorkflowPoints;


use Congraph\Contracts\Workflows\WorkflowPointRepositoryContract;
use Congraph\Core\Bus\RepositoryCommandHandler;
use Congraph\Core\Bus\RepositoryCommand;

/**
 * WorkflowPointFetchHandler class
 * 
 * Handling command for fetching workflow point
 * 
 * 
 * @author  	Nikola Plavšić <nikolaplavsic@gmail.com>
 * @copyright  	Nikola Plavšić <nikolaplavsic@gmail.com>
 * @package 	congraph/workflows
 * @since 		0.1.0-alpha
 * @version  	0.1.0-alpha
 */
class WorkflowPointFetchHandler extends RepositoryCommandHandler
{

	/**
	 * Create new WorkflowPointFetchHandler
	 * 
	 * @param Congraph\Contracts\Workflows\WorkflowPointRepositoryContract $repository
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
	 * 
	 * @return void
	 */
	public function handle()
	{
		$workflowPoint = $this->repository->fetch(
			$this->id,
			(!empty($this->params['include']))?$this->params['include']:[]
		);

		return $workflowPoint;
	}
}