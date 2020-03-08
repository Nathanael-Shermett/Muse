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
				new Assert\NotBlank(['message' => 'post.new.title.constraint.not_blank']),
				new Assert\Length([
					'min' => 5,
					'max' => 80,
					'minMessage' => 'post.new.title.constraint.length.min',
					'maxMessage' => 'post.new.title.constraint.length.max',
				]),
			],
			'attr' => [
				'maxlength' => 80,
				'placeholder' => 'post.new.title.placeholder',
			],

		])->add('content', TextareaType::class, [
			'constraints' => [
				new Assert\NotBlank(['message' => 'post.new.content.constraint.not_blank']),
				new Assert\Length([
					'min' => 10,
					//'min'        => 500,
					'minMessage' => 'post.new.content.constraint.length.min',
				]),
			],
			'attr' => ['placeholder' => 'post.new.content.placeholder'],

		])->add('abstract', TextType::class, [
			'constraints' => [
				new Assert\Length([
					'min' => 25,
					'max' => 150,
					'minMessage' => 'post.new.abstract.constraint.length.min',
					'maxMessage' => 'post.new.abstract.constraint.length.max',
				]),
			],
			'attr' => [
				'maxlength' => 150,
				'placeholder' => 'post.new.abstract.placeholder',
			],
			'required' => FALSE,

		])->add('categories', EntityType::class, [
			'class' => PostCategory::class,
			'constraints' => [
				new Assert\Count([
					'min' => 1,
					'max' => 2,
					'minMessage' => 'post.new.categories.constraint.length.min',
					'maxMessage' => 'post.new.categories.constraint.length.max',
				]),
			],
			'attr' => ['placeholder' => 'post.new.categories.placeholder'],
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