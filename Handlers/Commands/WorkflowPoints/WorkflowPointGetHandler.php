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
 * WorkflowPointGetHandler class
 * 
 * Handling command for getting workflow points
 * 
 * 
 * @author  	Nikola Plavšić <nikolaplavsic@gmail.com>
 * @copyright  	Nikola Plavšić <nikolaplavsic@gmail.com>
 * @package 	congraph/workflows
 * @since 		0.1.0-alpha
 * @version  	0.1.0-alpha
 */
class WorkflowPointGetHandler extends RepositoryCommandHandler
{

	/**
	 * Create new WorkflowPointGetHandler
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
		$workflowPoints = $this->repository->get(
			(!empty($this->params['filter']))?$this->params['filter']:[],
			(!empty($this->params['offset']))?$this->params['offset']:0,
			(!empty($this->params['limit']))?$this->params['limit']:0,
			(!empty($this->params['sort']))?$this->params['sort']:[],
			(!empty($this->params['include']))?$this->params['include']:[]
		);

		return $workflowPoints;
	}
}