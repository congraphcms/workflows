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


use Cookbook\Contracts\Workflows\WorkflowStepRepositoryContract;
use Cookbook\Core\Bus\RepositoryCommandHandler;
use Cookbook\Core\Bus\RepositoryCommand;

/**
 * WorkflowStepGetHandler class
 * 
 * Handling command for getting workflow steps
 * 
 * 
 * @author  	Nikola Plavšić <nikolaplavsic@gmail.com>
 * @copyright  	Nikola Plavšić <nikolaplavsic@gmail.com>
 * @package 	cookbook/workflows
 * @since 		0.1.0-alpha
 * @version  	0.1.0-alpha
 */
class WorkflowStepGetHandler extends RepositoryCommandHandler
{

	/**
	 * Create new WorkflowStepGetHandler
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
		$workflowSteps = $this->repository->get(
			(!empty($command->params['filter']))?$command->params['filter']:[],
			(!empty($command->params['offset']))?$command->params['offset']:0,
			(!empty($command->params['limit']))?$command->params['limit']:0,
			(!empty($command->params['sort']))?$command->params['sort']:[],
			(!empty($command->params['include']))?$command->params['include']:[]
		);

		return $workflowSteps;
	}
}