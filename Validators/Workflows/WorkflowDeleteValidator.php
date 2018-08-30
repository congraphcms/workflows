<?php
/*
 * This file is part of the congraph/workflows package.
 *
 * (c) Nikola Plavšić <nikolaplavsic@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Congraph\Workflows\Validators\Workflows;

use Congraph\Contracts\Workflows\WorkflowRepositoryContract;
use Congraph\Core\Bus\RepositoryCommand;
use Congraph\Core\Validation\Validator;


/**
 * WorkflowDeleteValidator class
 * 
 * Validating command for deleting workflow
 * 
 * 
 * @author  	Nikola Plavšić <nikolaplavsic@gmail.com>
 * @copyright  	Nikola Plavšić <nikolaplavsic@gmail.com>
 * @package 	congraph/workflows
 * @since 		0.1.0-alpha
 * @version  	0.1.0-alpha
 */
class WorkflowDeleteValidator extends Validator
{


	/**
	 * Repository for workflows
	 * 
	 * @var \Congraph\Contracts\Workflows\WorkflowRepositoryContract
	 */
	protected $workflowRepository;

	/**
	 * Create new WorkflowDeleteValidator
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
	 * @param Congraph\Core\Bus\RepositoryCommand $command
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