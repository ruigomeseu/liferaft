<?php namespace Laravel\Liferaft;

use Laravel\Liferaft\Contracts\Action;
use Laravel\Liferaft\Actions\GrabLiferaft;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Command\Command as BaseCommand;

class GrabCommand extends BaseCommand {

	/**
	 * The action instance.
	 *
	 * @var Action
	 */
	protected $action;

	/**
	 * Create a new command instance.
	 *
	 * @param  ThrowLiferaft  $action
	 * @return void
	 */
	public function __construct(GrabLiferaft $action)
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
		$this->setName('grab')
					->setDescription('Grab a Liferaft application for debugging')
					->addArgument('id', InputArgument::OPTIONAL, 'The pull request ID of the Liferaft application.', 'random');
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
		$this->action->execute($input->getArgument('id'));
	}

}
