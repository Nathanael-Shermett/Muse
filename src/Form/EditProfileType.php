<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Validator\Constraints as SecurityAssert;

class EditProfileType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$data = $builder->getData();
		$currentUser = $options['currentUser'];
		$user = $options['user'];

		$builder->add('currentPassword', PasswordType::class, [
			'mapped' => FALSE,
			'constraints' => [
				new SecurityAssert\UserPassword(['message' => 'user.edit.current_password.constraint.user_password']),
			],
			'attr' => [
				'autocomplete' => 'current-password',
				'placeholder' => 'user.edit.password_placeholder',
				'required' => 'required',
			],

		])->add('username', TextType::class, [
			'data' => $data['username'] ?? $user->getUsername(),
			'constraints' => [
				new Assert\Length([
					'min' => 2,
					'max' => 25,
					'minMessage' => 'user.edit.username.constraint.length.min',
					'maxMessage' => 'user.edit.username.constraint.length.max',
				]),
			],
			'attr' => ['placeholder' => 'Username'],

		])->add('email', EmailType::class, [
			'data' => $data['email'] ?? $user->getEmail(),
			'constraints' => [
				new Assert\Email(['message' => 'user.edit.email.constraint.email']),
				new Assert\Length([
					'min' => 6,
					'max' => 190,
					'minMessage' => 'user.edit.email.constraint.min',
					'maxMessage' => 'user.edit.email.constraint.max',
				]),
			],
			'attr' => ['placeholder' => 'Email'],

		])->add('plainPassword', RepeatedType::class, [
			'type' => PasswordType::class,
			'invalid_message' => 'user.edit.password.constraint.invalid',
			'first_options' => [
				'constraints' => [
					new Assert\Length([
						'min' => 8,
						'max' => 4096,
						'minMessage' => 'user.edit.password.constraint.length.min',
						'maxMessage' => 'user.edit.password.constraint.length.max',
					]),
				],
				'attr' => [
					'autocomplete' => 'new-password',
					'placeholder' => 'user.edit.new_password',
				],

			],
			'second_options' => [
				'attr' => ['placeholder' => 'user.edit.new_password_again'],

			],
		]);

		if ($currentUser->hasRole('ROLE_MODERATOR'))
		{
			$roles = [
				'user.edit.access_level.administrator' => 'ROLE_ADMIN',
				'user.edit.access_level.moderator' => 'ROLE_MODERATOR',
				'user.edit.access_level.user' => 'ROLE_USER',
				'user.edit.access_level.banned' => 'ROLE_BANNED',
			];

			$builder->add('role', ChoiceType::class, [

				'data' => $data['role'] ?? $user->getRoles()[0],
				'choices' => $roles,
				'choice_attr' => function($choice, $key, $value) use ($currentUser)
				{
					if (!$currentUser->hasRole('ROLE_ADMIN'))
					{
						if ($choice == 'ROLE_MODERATOR' || $choice == 'ROLE_ADMIN')
						{
							return ['disabled' => 'disabled'];
						}
					}

					return [];
				},

				'group_by' => function()
				{
					return 'user.edit.access_level.select';
				},
				'constraints' => [
					new Assert\Choice([
						'choices' => array_values($roles),
						'message' => 'user.edit.access_level.invalid',
					]),
				],
				'label' => 'Access Level',
			]);
		}
	}

	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults([
			'required' => FALSE,
			'currentUser' => NULL,
			'user' => NULL,
		]);
	}
}