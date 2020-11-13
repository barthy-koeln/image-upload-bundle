<?php

$loader = @include __DIR__.'/../vendor/autoload.php';

if (!$loader) {
    die(
    <<<'EOT'
You must set up the project dependencies, run the following commands:
wget http://getcomposer.org/composer.phar
php composer.phar install
EOT
    );
}

require_once __DIR__.'/AppKernel.php';

function bootstrap(): void
{
    $kernel = new AppKernel('test', true);
    $kernel->boot();

    $application = new \Symfony\Bundle\FrameworkBundle\Console\Application($kernel);
    $application->setAutoExit(false);

    $application->run(new \Symfony\Component\Console\Input\ArrayInput([
        'command'     => 'doctrine:database:drop',
        '--force' => true
    ]));

    $application->run(new \Symfony\Component\Console\Input\ArrayInput([
        'command' => 'doctrine:database:create',
    ]));

    $application->run(new \Symfony\Component\Console\Input\ArrayInput([
        'command' => 'doctrine:schema:update',
        '--force' => true
    ]));

    $kernel->shutdown();
}

bootstrap();
