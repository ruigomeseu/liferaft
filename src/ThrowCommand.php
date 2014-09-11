<?php namespace Laravel\Liferaft;

use Laravel\Liferaft\Actions\ThrowLiferaft;
use Laravel\Liferaft\Actions\ActionInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Logger\ConsoleLogger;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Command\Command as BaseCommand;

class ThrowCommand extends BaseCommand {

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
	public function __construct(ThrowLiferaft $action)
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
		$this->setName('throw')
					->setDescription('Send the Liferaft application to the Laravel team')
					->addOption('dev', null, InputOption::VALUE_NONE, "Send the pull request to the 'develop' branch of Laravel.");
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
		$this->action->execute($input->getOption('dev') ? 'develop' : 'master');
	}

}
