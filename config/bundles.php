<?php

return [
	Symfony\Bundle\FrameworkBundle\FrameworkBundle::class                => ['all' => true],
	Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle::class => ['all' => true],
	Doctrine\Bundle\DoctrineBundle\DoctrineBundle::class                 => ['all' => TRUE],
	Doctrine\Bundle\MigrationsBundle\DoctrineMigrationsBundle::class     => ['all' => TRUE],
	Symfony\Bundle\SecurityBundle\SecurityBundle::class                  => ['all' => TRUE],
	Symfony\Bundle\SwiftmailerBundle\SwiftmailerBundle::class            => ['all' => TRUE],
	Symfony\Bundle\WebProfilerBundle\WebProfilerBundle::class            => [
		'dev'  => TRUE,
		'test' => TRUE,
	],
	Symfony\Bundle\TwigBundle\TwigBundle::class                          => ['all' => TRUE],
	Symfony\Bundle\MonologBundle\MonologBundle::class                    => ['all' => TRUE],
	Symfony\Bundle\DebugBundle\DebugBundle::class                        => [
		'dev'  => TRUE,
		'test' => TRUE,
	],
	Symfony\Bundle\MakerBundle\MakerBundle::class                        => ['dev' => TRUE],
	Symfony\WebpackEncoreBundle\WebpackEncoreBundle::class               => ['all' => TRUE],
	Knp\Bundle\TimeBundle\KnpTimeBundle::class                           => ['all' => TRUE],
];
