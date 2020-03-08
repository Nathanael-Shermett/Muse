<?php

namespace App\Form;

use App\Entity\Comment;
use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints as Assert;

class CommentType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder->add('content', TextareaType::class, [
			'constraints' => [
				new Assert\NotBlank(['message' => 'Your comment cannot be blank.']),
				new Assert\Length([
					'min' => 10,
					'minMessage' => 'Your comment must be at least {{ limit }} characters long.',
				]),
			],
			'attr' => ['placeholder' => 'Write your comment here.'],
		]);
	}

	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults([
			'data_class' => Comment::class,
		]);
	}
}