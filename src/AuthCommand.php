<?php namespace Laravel\Liferaft;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Command\Command as BaseCommand;

class AuthCommand extends BaseCommand {

	/**
	 * Configure the command options.
	 *
	 * @return void
	 */
	protected function configure()
	{
		$this->setName('auth')
					->setDescription('Set your Github personal access token')
					->addArgument('token', InputArgument::REQUIRED, 'Your Github personal access token.');
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
		file_put_contents(__DIR__.'/../liferaft.json', json_encode(['token' => $input->getArgument('token')]));

		$output->writeln('<comment>Storing Github Token...</comment> <info>✔</info>');
	}

}
