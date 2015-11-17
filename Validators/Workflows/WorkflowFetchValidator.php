<?php
/*
 * This file is part of the cookbook/workflows package.
 *
 * (c) Nikola Plavšić <nikolaplavsic@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cookbook\Workflows\Validators\Workflows;

use Cookbook\Core\Bus\RepositoryCommand;
use Cookbook\Core\Validation\Validator;


/**
 * WorkflowFetchValidator class
 * 
 * Validating command for fetching workflow
 * 
 * 
 * @author  	Nikola Plavšić <nikolaplavsic@gmail.com>
 * @copyright  	Nikola Plavšić <nikolaplavsic@gmail.com>
 * @package 	cookbook/workflows
 * @since 		0.1.0-alpha
 * @version  	0.1.0-alpha
 */
class WorkflowFetchValidator extends Validator
{


	/**
	 * Repository for workflows
	 * 
	 * @var \Cookbook\Contracts\Workflows\WorkflowRepositoryContract
	 */
	protected $workflowRepository;

	/**
	 * Create new WorkflowFetchValidator
	 * 
	 * @return void
	 */
	public function __construct(WorkflowRepositoryContract $workflowRepository)
	{
		parent::__construct();
		$this->workflowRepository = $workflowRepository;
		$this->exception->setErrorKey('workflow');
	}


	/**
	 * Validate RepositoryCommand
	 * 
	 * @param Cookbook\Core\Bus\RepositoryCommand $command
	 * 
	 * @todo  Create custom validation for all db related checks (DO THIS FOR ALL VALIDATORS)
	 * @todo  Check all db rules | make validators on repositories
	 * 
	 * @return void
	 */
	public function validate(RepositoryCommand $command)
	{
		$workflow = $this->workflowRepository->fetch($command->id);
	}
}