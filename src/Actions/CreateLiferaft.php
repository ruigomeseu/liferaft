<?php namespace Laravel\Liferaft\Actions;

use Illuminate\Events\Dispatcher;
use Laravel\Liferaft\Contracts\Action;
use Laravel\Liferaft\Contracts\Github;
use Symfony\Component\Process\Process;

class CreateLiferaft implements Action {

	use ActionTrait;

	/**
	 * The Github implementation.
	 *
	 * @var Github
	 */
	protected $github;

	/**
	 * The sequence of tasks to perform.
	 *
	 * @var array
	 */
	protected $tasks = [
		'forkLaravel',
		'renameForkedRepository',
		'cloneLiferaftApplication',
		'installComposerDependencies',
		'writeStubLiferaftFile',
	];

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
	 * @param  string  $name
	 * @param  string  $branch
	 * @return void
	 */
	public function execute($name, $branch)
	{
		if (is_dir(getcwd().'/'.$name))
		{
			$this->kill('That directory already exists!');
		}

		$username = $this->github->getUsername();

		foreach ($this->tasks as $task)
		{
			$this->{$task}($username, $name, $branch);
		}

		$this->info('Done! Thank you for your contributions!');
	}

	/**
	 * Fork Laravel onto the user's Github account.
	 *
	 * @return void
	 */
	protected function forkLaravel()
	{
		$this->task('Forking Laravel...', function()
		{
			$this->github->fork('laravel', 'laravel');
		});
	}

	/**
	 * Rename the forked repository to "liferaft".
	 *
	 * @param  string  $username
	 * @return void
	 */
	protected function renameForkedRepository($username)
	{
		$this->task('Renaming Repository...', function() use ($username)
		{
			$this->renameRepository($username, 'laravel', 'liferaft');
		});
	}

	/**
	 * Clone the forked repository to the local machine.
	 *
	 * @param  string  $username
	 * @param  string  $name
	 * @param  string  $branch
	 * @return void
	 */
	protected function cloneLiferaftApplication($username, $name, $branch)
	{
		$this->task('Cloning Liferaft Application...', function() use ($username, $name, $branch)
		{
			$this->cloneRepository($username, $name, $branch);
		});
	}

	/**
	 * Install the cloned repository's dependencies.
	 *
	 * @param  string  $username
	 * @param  string  $name
	 * @return void
	 */
	protected function installComposerDependencies($username, $name)
	{
		$this->task('Installing Composer Dependencies...', function() use ($name)
		{
			$this->installDependencies($name);
		});
	}

	/**
	 * Write the stub Liferaft file to the cloned directory.
	 *
	 * @param  string  $username
	 * @param  string  $name
	 * @return void
	 */
	protected function writeStubLiferaftFile($username, $name)
	{
		$this->task('Writing Stub Liferaft File...', function() use ($name)
		{
			$this->createLiferaftFile($name);
		});
	}

	/**
	 * Rename the given repository.
	 *
	 * @param  string  $username
	 * @param  string  $repository
	 * @param  string  $name
	 * @return void
	 */
	protected function renameRepository($username, $repository, $name)
	{
		try
		{
			$this->github->rename($username, $repository, $name);
		}
		catch (\Exception $e)
		{
			sleep(3);

			$this->renameRepository($uesrname, $repository, $name);
		}
	}

	/**
	 * Clone the Liferaft repository.
	 *
	 * @param  string  $owner
	 * @param  string  $name
	 * @param  string  $branch
	 * @return void
	 */
	protected function cloneRepository($owner, $name, $branch)
	{
		$clone = 'git@github.com:'.$owner.'/liferaft.git '.$name;

		with($process = new Process('git clone -b '.$branch.' '.$clone, getcwd()))->run();

		if ($process->getExitCode() > 0)
		{
			sleep(3);

			$this->failed();

			$this->task('Retrying Clone...', function() use ($owner, $name)
			{
				$this->cloneRepository($owner, $name);
			});
		}
	}

	/**
	 * Install the application's dependencies.
	 *
	 * @param  string  $name
	 * @return void
	 */
	protected function installDependencies($name)
	{
		$this->runProcess((new Process('composer install', getcwd().'/'.$name))->setTimeout(null));
	}

	/**
	 * Create a stub Liferaft file in the cloned repository.
	 *
	 * @param  string  $name
	 * @return void
	 */
	protected function createLiferaftFile($name)
	{
		copy(__DIR__.'/stubs/liferaft.md', getcwd().'/'.$name.'/liferaft.md');
	}

}