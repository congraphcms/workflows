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
 * WorkflowUpdateValidator class
 * 
 * Validating command for creating workflow
 * 
 * 
 * @author  	Nikola Plavšić <nikolaplavsic@gmail.com>
 * @copyright  	Nikola Plavšić <nikolaplavsic@gmail.com>
 * @package 	cookbook/workflows
 * @since 		0.1.0-alpha
 * @version  	0.1.0-alpha
 */
class WorkflowUpdateValidator extends Validator
{
	/**
	 * Repository for workflows
	 * 
	 * @var \Cookbook\Contracts\Workflows\WorkflowRepositoryContract
	 */
	protected $workflowRepository;

	/**
	 * Set of rules for validating workflow
	 *
	 * @var array
	 */
	protected $rules;

	/**
	 * Create new WorkflowUpdateValidator
	 * 
	 * @return void
	 */
	public function __construct(WorkflowRepositoryContract $workflowRepository)
	{

		$this->rules = [
			'name'					=> 'required|min:3|max:250',
			'description'			=> 'sometimes'
		];

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
		$this->validateParams($command->params, $this->rules, true);

		if( $this->exception->hasErrors() )
		{
			throw $this->exception;
		}
	}
}