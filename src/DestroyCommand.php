<?php namespace Laravel\Liferaft;

use Laravel\Liferaft\Actions\DestroyLiferaft;
use Laravel\Liferaft\Actions\ActionInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Logger\ConsoleLogger;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Command\Command as BaseCommand;

class DestroyCommand extends BaseCommand {

	/**
	 * The action instance.
	 *
	 * @var ActionInterface
	 */
	protected $action;

	/**
	 * Create a new command instance.
	 *
	 * @param  ThrowLiferaft  $action
	 * @return void
	 */
	public function __construct(DestroyLiferaft $action)
	{
		parent::__construct();

		$this->action = $action;
	}

	/**
	 * Configure the command options.
	 *
	 * @return void
	 */
	protected function configure()
	{
		$this->setName('destroy')
					->setDescription('Destroy the Liferaft application on Github');
	}

	/**
	 * Execute the command.
	 *
	 * @param  \Symfony\Component\Console\Input\InputInterface  $input
	 * @param  \Symfony\Component\Console\Output\OutputInterface  $output
	 * @return void
	 */
	public function execute(InputInterface $input, OutputInterface $output)
	{
		$this->action->execute();
	}

}
