<?php namespace Laravel\Liferaft;

use Symfony\Component\Process\Process;

class Git {

	/**
	 * Pull the given repository branch into the current directory.
	 *
	 * @param  string  $owner
	 * @param  string  $repository
	 * @param  string  $branch
	 * @return void
	 */
	public function pull($owner, $repository, $branch)
	{
		with($process = new Process('git pull https://github.com/'.$owner.'/'.$repository.'.git '.$branch))->run();
	}

	/**
	 * Checkout the given branch.
	 *
	 * @param  string  $branch
	 * @return void
	 */
	public function checkout($branch)
	{
		with($process = new Process('git checkout '.$branch, getcwd()))->run();

		if ($process->getExitCode() > 0)
		{
			throw new \Exception($process->getErrorOutput());
		}
	}

	/**
	 * Checkout the given branch.
	 *
	 * @param  string  $branch
	 * @return void
	 */
	public function checkoutNew($branch)
	{
		with($process = new Process('git checkout -b '.$branch, getcwd()))->run();

		if ($process->getExitCode() > 0)
		{
			throw new \Exception($process->getErrorOutput());
		}
	}

	/**
	 * Determine if Git has uncommited changes.
	 *
	 * @return bool
	 */
	public function hasUncommitedChanges()
	{
		with($process = new Process('git status -s', getcwd()))->run();

		return trim($process->getOutput()) != '';
	}

	/**
	 * Get the current branch of the repository.
	 *
	 * @return string
	 */
	public function getCurrentBranch()
	{
		with($process = new Process('git rev-parse --abbrev-ref HEAD', getcwd()))->run();

		return trim($process->getOutput());
	}

}
