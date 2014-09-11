<?php namespace Laravel\Liferaft\Actions;

use Laravel\Liferaft\Contracts\Action;
use Laravel\Liferaft\Contracts\Github;
use Illuminate\Contracts\Events\Dispatcher;

class DestroyLiferaft implements Action {

	use ActionTrait;

	/**
	 * The Github implementation.
	 *
	 * @var Github
	 */
	protected $github;

	/**
	 * Create a new action instance.
	 *
	 * @param  Github  $github
	 * @param  Dispatcher  $dispatcher
	 * @return void
	 */
	public function __construct(Github $github, Dispatcher $event)
	{
		$this->event = $event;
		$this->github = $github;
	}

	/**
	 * Execute the action.
	 *
	 * @return void
	 */
	public function execute()
	{
		$this->task('Deleting Liferaft Repository...', function()
		{
			try
			{
				$this->github->deleteRepository($this->github->getUsername(), 'liferaft');
			}
			catch (\Exception $e)
			{
				$this->failAndKill('Unable to delete repository. Does your GitHub token have enough permissions?');
			}

			@unlink(getcwd().'/liferaft.md');
		});

		$this->info('Done! This directory may be deleted.');
	}

}