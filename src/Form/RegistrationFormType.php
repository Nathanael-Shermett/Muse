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
				new Assert\NotBlank(['message' => 'user.register.username.constraint.not_blank']),
				new Assert\Length([
					'min' => 2,
					'max' => 25,
					'minMessage' => 'user.register.username.constraint.length.min',
					'maxMessage' => 'user.register.username.constraint.length.max',
				]),
			],
			'attr' => ['placeholder' => 'Username'],

		])->add('email', EmailType::class, [
			'constraints' => [
				new Assert\NotBlank(['message' => 'user.register.email.constraint.not_blank']),
				new Assert\Length([
					'min' => 6,
					'max' => 190,
					'minMessage' => 'user.register.email.constraint.length.min',
					'maxMessage' => 'user.register.email.constraint.length.max',
				]),
				new Assert\Email(['message' => 'user.register.email.constraint.email']),
			],
			'attr' => ['placeholder' => 'Email'],

		])->add('plainPassword', RepeatedType::class, [
			'type' => PasswordType::class,
			'first_options' => [
				'label' => 'Password',
				'constraints' => [
					new Assert\NotBlank(['message' => 'user.register.password.constraint.not_blank']),
					new Assert\Length([
						'min' => 8,
						'max' => 4096,
						'minMessage' => 'user.register.password.constraint.length.min',
						'maxMessage' => 'user.register.password.constraint.length.max',
					]),
				],
				'attr' => ['placeholder' => 'user.register.password.placeholder'],
			],
			'second_options' => [
				'attr' => ['placeholder' => 'user.register.password.placeholder_again'],

			],
			'invalid_message' => 'user.register.password.constraint.invalid',
		]);
	}

	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults([
			'data_class' => User::class,
		]);
	}
}