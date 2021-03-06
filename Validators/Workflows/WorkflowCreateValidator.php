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

use Congraph\Core\Bus\RepositoryCommand;
use Congraph\Core\Validation\Validator;


/**
 * WorkflowCreateValidator class
 * 
 * Validating command for creating workflow
 * 
 * 
 * @author  	Nikola Plavšić <nikolaplavsic@gmail.com>
 * @copyright  	Nikola Plavšić <nikolaplavsic@gmail.com>
 * @package 	congraph/workflows
 * @since 		0.1.0-alpha
 * @version  	0.1.0-alpha
 */
class WorkflowCreateValidator extends Validator
{


	/**
	 * Set of rules for validating workflow
	 *
	 * @var array
	 */
	protected $rules;

	/**
	 * Create new WorkflowCreateValidator
	 * 
	 * @return void
	 */
	public function __construct()
	{

		$this->rules = [
			'name'					=> 'required|min:3|max:250',
			'description'			=> 'sometimes'
		];

		parent::__construct();

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
		$this->validateParams($command->params, $this->rules, true);

		if( $this->exception->hasErrors() )
		{
			throw $this->exception;
		}
	}
}