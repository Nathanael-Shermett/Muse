<?php

namespace App\Form;

use App\Entity\Post;
use App\Entity\PostCategory;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
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
		$builder->add('title', TextType::class, [
			'constraints' => [
				new Assert\NotBlank(['message' => 'Please enter a title.']),
				new Assert\Length([
					'min' => 5,
					'max' => 80,
					'minMessage' => "Your post's title must be at least {{ limit }} characters long.",
					'maxMessage' => "Your post's title cannot be longer than {{ limit }} characters.",
				]),
			],
			'attr' => [
				'maxlength' => 80,
				'placeholder' => 'Title',
			],

		])->add('content', TextareaType::class, [
			'constraints' => [
				new Assert\NotBlank(['message' => 'This field cannot be blank.']),
				new Assert\Length([
					'min' => 10,
					//'min'        => 500,
					'minMessage' => 'Your post must be at least {{ limit }} characters long.',
				]),
			],
			'attr' => ['placeholder' => 'Please provide a thorough explanation of your thoughts.'],

		])->add('abstract', TextType::class, [
			'constraints' => [
				new Assert\Length([
					'min' => 25,
					'minMessage' => 'Abstracts are optional. However, if you wish to provide one, please make it more thorough.',
					'max' => 150,
					'maxMessage' => 'Your abstract must be 150 characters or less.',
				]),
			],
			'attr' => [
				'maxlength' => 150,
				'placeholder' => 'Abstract (optional; 150 characters or less)',
			],
			'required' => FALSE,

		])->add('categories', EntityType::class, [
			'class' => PostCategory::class,
			'constraints' => [
				new Assert\Count([
					'min' => 1,
					'max' => 2,
					'minMessage' => 'Please select at least one category (but not more than two).',
					'maxMessage' => 'You may not select more than two categories.',
				]),
			],
			'attr' => ['placeholder' => 'Categories (2 max)'],
			//'choices' => $categoriesArray,
			'choice_attr' => function($category, $key, $value)
			{
				return ['data-icon' => $category->getIcon()];
			},
			'choice_label' => function($category)
			{
				return ucwords($category->getName());
			},
			'group_by' => function($category, $key, $value)
			{
				return ucwords($category->getSupercategory());
			},
			'multiple' => TRUE,
		]);
	}

	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults([]);
	}
}