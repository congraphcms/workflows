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

use Congraph\Contracts\Workflows\WorkflowRepositoryContract;
use Congraph\Core\Bus\RepositoryCommand;

/**
 * WorkflowUpdateCommand class
 * 
 * Command for updating workflow
 * 
 * @author  	Nikola Plavšić <nikolaplavsic@gmail.com>
 * @copyright  	Nikola Plavšić <nikolaplavsic@gmail.com>
 * @package 	congraph/workflows
 * @since 		0.1.0-alpha
 * @version  	0.1.0-alpha
 */
class WorkflowUpdateCommand extends RepositoryCommand
{
    /**
	 * Create new WorkflowUpdateCommand
	 * 
	 * @param Congraph\Contracts\Workflows\WorkflowRepositoryContract $repository
	 * 
	 * @return void
	 */
	public function __construct(WorkflowRepositoryContract $repository)
	{
		parent::__construct($repository);
	}

	/**
	 * Handle RepositoryCommand
	 * 
	 * @return void
	 */
	public function handle()
	{
		$workflow = $this->repository->update($this->id, $this->params);

		return $workflow;
	}
}
