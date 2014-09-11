<?php namespace Laravel\Liferaft\Actions;

use Closure;
use Symfony\Component\Process\Process;

trait ActionTrait {

	/**
	 * The event dispatcher instance.
	 *
	 * @param  Dispatcher
	 */
	protected $event;

	/**
	 * Execute a task with a stauts update.
	 *
	 * @param  string  $message
	 * @param  \Closure  $callback
	 * @return mixed
	 */
	protected function task($message, Closure $callback)
	{
		$this->progress($message);

		try
		{
			$response = $callback();
		}
		catch (\Exception $e)
		{
			$this->failAndKill($e->getMessage());
		}

		$this->done();

		return $response;
	}

	/**
	 * Run the given process.
	 *
	 * @param  Process  $process
	 * @return void
	 */
	protected function runProcess(Process $process)
	{
		$process->run();

		if ($process->getExitCode() > 0)
		{
			$this->event->fire('line', '<fg=red>✗</fg=red>');
			$this->event->fire('line', $process->getErrorOutput());

			exit(1);
		}
	}

	/**
	 * Send an informational update event.
	 *
	 * @param  array|string  $info
	 * @return void
	 */
	public function info($infos)
	{
		foreach ((array) $infos as $info)
		{
			$this->event->fire('line', '<info>'.$info.'</info>');
		}
	}

	/**
	 * Send a progress update event.
	 *
	 * @param  string  $comment
	 * @return void
	 */
	public function progress($comment)
	{
		$this->event->fire('write', '<comment>'.$comment.'</comment> ');
	}

	/**
	 * Send a "done" update event.
	 *
	 * @return void
	 */
	public function done()
	{
		$this->event->fire('line', '<info>✔</info>');
	}

	/**
	 * Send a "failed" update event.
	 *
	 * @return void
	 */
	public function failed()
	{
		$this->event->fire('line', '<fg=red>✗</fg=red>');
	}

	/**
	 * Send a "failed" update event and kill the application.
	 *
	 * @param  string  $message
	 * @return void
	 */
	public function failAndKill($message)
	{
		$this->failed();

		$this->kill($message);
	}

	/**
	 * Send an error message and kill the application.
	 *
	 * @param  string  $message
	 * @return void
	 */
	public function kill($message)
	{
		$this->event->fire('line', '<fg=red>'.$message.'</fg=red>');

		exit(1);
	}

}
