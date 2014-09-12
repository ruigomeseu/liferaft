<?php namespace Laravel\Liferaft;

use Laravel\Liferaft\Contracts\Action;
use Laravel\Liferaft\Actions\CreateLiferaft;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Command\Command as BaseCommand;

class NewCommand extends BaseCommand {

	/**
	 * The action instance.
	 *
	 * @var Action
	 */
	protected $action;

	/**
	 * Create a new command instance.
	 *
	 * @param  CreateLiferaft  $action
	 * @return void
	 */
	public function __construct(CreateLiferaft $action)
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
		$this->setName('new')
					->setDescription('Create a new Laravel Liferaft application')
					->addArgument('name', InputArgument::REQUIRED, 'The name of the Liferaft application.')
					->addOption('dev', null, InputOption::VALUE_NONE, "Clone the 'develop' branch of Laravel.");
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
		$question = new Question("<fg=cyan>What title should be assigned to your bug report?</fg=cyan> ");

		$title = $this->getHelper('question')->ask($input, $output, $question);

		$branch = $input->getOption('dev') ? 'develop' : 'master';

		$this->action->execute($input->getArgument('name'), $branch, $title);
	}

}
