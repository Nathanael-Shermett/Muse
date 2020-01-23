<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Validator\Constraints as Assert;

class RegistrationFormType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder->add('username', TextType::class, [
			'constraints' => [
				new Assert\NotBlank(['message' => 'Please enter a username.']),
				new Assert\Length([
					'min'        => 2,
					'max'        => 25,
					'minMessage' => "Your username must be at least {{ limit }} characters long.",
					'maxMessage' => "Your username cannot be longer than {{ limit }} characters.",
				]),
			],
		])->add('email', EmailType::class, [
			'constraints' => [
				new Assert\NotBlank(['message' => 'Please enter an email address.']),
				new Assert\Length([
					'min'        => 6,
					'max'        => 190,
					'minMessage' => "Your email address cannot be shorter than {{ limit }} characters long.",
					'maxMessage' => "Your email address cannot be longer than {{ limit }} characters.",
				]),
				new Assert\Email(['message' => 'Please enter a valid email address.']),
			],
		])->add('plainPassword', RepeatedType::class, [
			'type'           => PasswordType::class,
			'first_options'  => [
				'label'       => 'Password',
				'constraints' => [
					new Assert\NotBlank(['message' => 'Please enter a password.']),
					new Assert\Length([
						'min'        => 8,
						'max'        => 4096,
						'minMessage' => "For security reasons, your password must be at least {{ limit }} characters long.",
						'maxMessage' => "Your password cannot be longer than {{ limit }} characters.",
					]),
				],
			],
			'second_options' => [
				'label'           => 'Password (again)',
			],
			'invalid_message' => 'The provided passwords did not match.',
		]);
	}

	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults([
			'data_class' => User::class,
		]);
	}
}