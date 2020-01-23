<?php

namespace App\Twig;

use App\Utils\Parsedown;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

/**
 * This Twig extension adds a 'md' filter to transform Markdown to HTML.
 * It is adapted from the Symfony demo application.
 */
class AppExtension extends AbstractExtension
{
	private $parser;

	public function __construct(Parsedown $parser)
	{
		$this->parser = $parser;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getFilters(): array
	{
		return [
			new TwigFilter('markdown', [$this, 'markdownToHtml'], ['is_safe' => ['html']]),
			new TwigFilter('ago', [$this, 'timeAgo']),
			new TwigFilter('md5', 'md5'),
		];
	}

	/**
	 * Transforms the given Markdown content into HTML content.
	 */
	public function markdownToHtml(string $content): string
	{
		$this->parser->setSafeMode(TRUE);

		return $this->parser->text($content);
	}

	/**
	 * Converts a timestamp to "X units ago" (unless it was longggg ago).
	 */
	public function timeAgo(string $timestamp): string
	{
		$timestamp = strtotime($timestamp);

		// Conversion values.
		$units = ['second', 'minute', 'hour', 'day', 'week', 'month', 'year'];
		$length = [60, 60, 24, 7, 4, 12, 10];

		// Two years.
		$cutoff = 60 * 60 * 24 * 365 * 2;

		// If the time is in the future OR more than $cutoff seconds in the past.
		if (time() < $timestamp || time() - $timestamp > $cutoff)
		{
			return date('F j, Y', $timestamp);
		}
		else
		{
			$difference = time() - $timestamp;
			for ($i = 0; $difference >= $length[ $i ] && $i < count($length) - 1; $i++)
			{
				$difference = $difference / $length[ $i ];
			}

			$difference = floor($difference);

			if ($difference == 1)
			{
				return $difference . ' ' . $units[ $i ] . ' ago';
			}
			else
			{
				return $difference . ' ' . $units[ $i ] . 's ago';
			}
		}
	}
}
