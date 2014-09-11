<?php namespace Laravel\Liferaft\Actions;

use Laravel\Liferaft\Git;
use Laravel\Liferaft\Contracts\Action;
use Laravel\Liferaft\Contracts\Github;
use Illuminate\Contracts\Events\Dispatcher;

class GrabLiferaft implements Action {

	use ActionTrait;

	/**
	 * The Github implementation.
	 *
	 * @var Github
	 */
	protected $github;

	/**
	 * The Git implementation.
	 *
	 * @var Git
	 */
	protected $git;

	/**
	 * The sequence of tasks to perform.
	 *
	 * @var array
	 */
	protected $tasks = [
		'verifyRepositoryIsClean',
		'checkoutProperBranch',
		'createLiferaftBrach',
		'pullLiferaftApplication',
		'reportPullInformation',
	];

	/**
	 * Create a new action instance.
	 *
	 * @param  Github  $github
	 * @param  Git  $git
	 * @param  Dispatcher  $dispatcher
	 * @return void
	 */
	public function __construct(Github $github, Git $git, Dispatcher $event)
	{
		$this->git = $git;
		$this->event = $event;
		$this->github = $github;
	}

	/**
	 * Execute the action.
	 *
	 * @return void
	 */
	public function execute($pullRequestId)
	{
		$pull = $this->getPullRequest($pullRequestId);

		foreach ($this->tasks as $task)
		{
			$this->{$task}($pull);
		}
	}

	/**
	 * Verify that the repository has no uncommited changes.
	 *
	 * @return void
	 */
	protected function verifyRepositoryIsClean()
	{
		$this->task('Verifying This Repository Is Clean...', function()
		{
			if ($this->git->hasUncommitedChanges())
			{
				$this->failAndKill('Please commit your changes in this repository first.');
			}
		});
	}

	/**
	 * Check out the proper branch for the grab.
	 *
	 * @param  array  $pull
	 * @return void
	 */
	protected function checkoutProperBranch($pull)
	{
		$this->task('Checking Out Branch ['.$pull['to_branch'].']...', function() use ($pull)
		{
			$this->git->checkout($pull['to_branch']);
		});
	}

	/**
	 * Create a branch for the Lifereaft application.
	 *
	 * @param  array  $pull
	 * @return void
	 */
	protected function createLiferaftBrach($pull)
	{
		$branch = $pull['id'].'-'.$pull['user'].'/'.$pull['from_branch'];

		$this->task('Creating Branch ['.$branch.'] For Liferaft Application...', function() use ($branch)
		{
			$this->git->checkoutNew($branch);
		});
	}

	/**
	 * Pull the Liferaft application into the branch.
	 *
	 * @param  array  $pull
	 * @return void
	 */
	protected function pullLiferaftApplication($pull)
	{
		$this->task('Pulling Liferaft Application...', function() use ($pull)
		{
			$this->git->pull($pull['user'], 'liferaft', $pull['from_branch']);
		});
	}

	/**
	 * Report the pull request information to the user.
	 *
	 * @param  array  $pull
	 * @return void
	 */
	protected function reportPullInformation($pull)
	{
		$this->event->fire('line', '<info>Issue: '.$pull['title'].' (Reported By '.$pull['user'].')</info>');
	}

	/**
	 * Get the pull request at the given ID.
	 *
	 * @return array
	 */
	protected function getPullRequest($id)
	{
		if ($id == 'random')
		{
			$id = $this->github->getRandomPullRequestId();
		}

		try
		{
			return $this->github->getPullRequest($id);
		}
		catch (\Exception $e)
		{
			$this->failAndKill('Invalid pull request ID.');
		}
	}

}