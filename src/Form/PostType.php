<?php

namespace App\Form;

use App\Entity\Post;
use App\Entity\PostCategory;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Validator\Constraints as Assert;

class PostType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$categories = $options['categories'];

		// Create a ChoiceType-friendly array of categories and category icons.
		$categoriesArray = [];
		foreach ($categories as $category)
		{
			$categoriesArray[ $category->getId() ] = $category;
		}

		$builder->add('title', TextType::class, [
			'constraints' => [
				new Assert\NotBlank(['message' => 'Please enter a title.']),
				new Assert\Length([
					'min'        => 5,
					'max'        => 80,
					'minMessage' => "Your post's title must be at least {{ limit }} characters long.",
					'maxMessage' => "Your post's title cannot be longer than {{ limit }} characters.",
				]),
			],
			'attr'        => ['maxlength' => 80],

		])->add('content', TextareaType::class, [
			'constraints' => [
				new Assert\NotBlank(['message' => 'Please provide a thorough abstract of your thoughts.']),
				new Assert\Length([
					'min'        => 10,
					//'min'        => 500,
					'minMessage' => 'Your post must be at least {{ limit }} characters long.',
				]),
			],

		])->add('abstract', TextType::class, [
			'constraints' => [
				new Assert\Length([
					'min'        => 25,
					'minMessage' => 'Abstracts are optional. If you wish to provide one, please make it more thorough.',
					'max'        => 150,
					'maxMessage' => 'Your abstract must be 150 characters or less.',
				]),
			],
			'attr'        => ['maxlength' => 150],
		])->add('categories', ChoiceType::class, [
			'constraints'  => [
				new Assert\Count([
					'min'        => 1,
					'max'        => 2,
					'minMessage' => 'Please select at least one category (but not more than two).',
					'maxMessage' => 'You may not select more than two categories.',
				]),
			],
			'attr'         => ['placeholder' => 'Categories (2 max)'],
			'choices'      => $categoriesArray,
			'choice_attr'  => function($category, $key, $value)
			{
				return ['data-icon' => $category->getIcon()];
			},
			'choice_label' => 'name',
			'group_by'     => function($category, $key, $value)
			{
				return ucwords($category->getSupercategory());
			},
			'multiple'     => TRUE,
		]);
	}

	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults([
			'categories' => NULL,
		]);
	}
}