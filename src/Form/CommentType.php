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
				new Assert\Length([
					'min' => 10,
					'minMessage' => 'comment.new.constraint.length.min',
				]),
				new Assert\NotBlank(['message' => 'comment.new.cannot_be_blank']),
			],
			'attr' => ['placeholder' => 'comment.new.placeholder'],
		]);
	}

	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults([
			'data_class' => Comment::class,
		]);
	}
}