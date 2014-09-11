<?php namespace Laravel\Liferaft\Actions;

use Laravel\Liferaft\Git;
use Illuminate\Events\Dispatcher;
use Laravel\Liferaft\Contracts\Action;
use Laravel\Liferaft\Contracts\Github;
use Symfony\Component\Process\Process;

class ThrowLiferaft implements Action {

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
	 * @param  string  $toBranch
	 * @return void
	 */
	public function execute($toBranch)
	{
		$url = $this->task('Sending Application To Laravel Team...', function() use ($toBranch)
		{
			return $this->sendPullRequest(
				$this->github->getUsername(), $this->git->getCurrentBranch(), $toBranch, $this->getLiferaftFile()
			);
		});

		$this->info(['Pull request sent! Thank you for the bug report!', 'URL: '.$url]);
	}

	/**
	 * Send the pull request to the Laravel repository.
	 *
	 * @param  string  $username
	 * @param  string  $liferaftFile
	 * @return string
	 */
	protected function sendPullRequest($username, $branch, $toBranch, $liferaftFile)
	{
		try
		{
			return $this->github->sendPullRequest($username, $branch, $toBranch, $this->getLiferaftFile());
		}
		catch (\Exception $e)
		{
			$this->failed();

			$this->kill('Unable to send pull request. Do you already have one open?');
		}
	}

	/**
	 * Get the contents of the Liferaft file.
	 *
	 * @return string
	 */
	protected function getLiferaftFile()
	{
		if ( ! file_exists($path = getcwd().'/liferaft.md'))
		{
			$this->failAndKill('Liferaft file not found.');
		}

		$contents = file_get_contents($path);

		if (trim(strtok($contents, "\n")) == 'Issue Report Title')
		{
			$this->failAndKill('Issue title has not been set.');
		}

		return $contents;
	}

}