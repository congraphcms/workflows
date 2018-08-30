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


use Congraph\Contracts\Workflows\WorkflowRepositoryContract;
use Congraph\Core\Bus\RepositoryCommandHandler;
use Congraph\Core\Bus\RepositoryCommand;

/**
 * WorkflowGetHandler class
 * 
 * Handling command for getting workflows
 * 
 * 
 * @author  	Nikola Plavšić <nikolaplavsic@gmail.com>
 * @copyright  	Nikola Plavšić <nikolaplavsic@gmail.com>
 * @package 	congraph/workflows
 * @since 		0.1.0-alpha
 * @version  	0.1.0-alpha
 */
class WorkflowGetHandler extends RepositoryCommandHandler
{

	/**
	 * Create new WorkflowGetHandler
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
	 * @param Congraph\Core\Bus\RepositoryCommand $command
	 * 
	 * @return void
	 */
	public function handle(RepositoryCommand $command)
	{
		$workflows = $this->repository->get(
			(!empty($command->params['filter']))?$command->params['filter']:[],
			(!empty($command->params['offset']))?$command->params['offset']:0,
			(!empty($command->params['limit']))?$command->params['limit']:0,
			(!empty($command->params['sort']))?$command->params['sort']:[],
			(!empty($command->params['include']))?$command->params['include']:[]
		);

		return $workflows;
	}
}