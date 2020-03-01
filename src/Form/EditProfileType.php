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
			'attr' => [
				'required' => 'required',
				'autocomplete' => 'current-password',
			],
			'label' => 'Please enter your current password.',
			'constraints' => [
				new SecurityAssert\UserPassword(['message' => 'The password you entered is incorrect.']),
			],
		])->add('username', TextType::class, [
			'data' => $data['username'] ?? $user->getUsername(),
			'constraints' => [
				new Assert\Length([
					'min' => 2,
					'max' => 25,
					'minMessage' => "Your username must be at least {{ limit }} characters long.",
					'maxMessage' => "Your username cannot be longer than {{ limit }} characters.",
				]),
			],
		])->add('email', EmailType::class, [
			'data' => $data['email'] ?? $user->getEmail(),
			'constraints' => [
				new Assert\Email(['message' => 'Please enter a valid email address.']),
				new Assert\Length([
					'min' => 6,
					'max' => 190,
					'minMessage' => "Your email address cannot be shorter than {{ limit }} characters long.",
					'maxMessage' => "Your email address cannot be longer than {{ limit }} characters.",
				]),
			],
		])->add('plainPassword', RepeatedType::class, [
			'type' => PasswordType::class,
			'invalid_message' => 'The provided passwords did not match.',
			'first_options' => [
				'attr' => ['autocomplete' => 'new-password'],
				'label' => 'New Password',
				'constraints' => [
					new Assert\Length([
						'min' => 8,
						'max' => 4096,
						'minMessage' => "For security reasons, your password must be at least {{ limit }} characters long.",
						'maxMessage' => "Your password cannot be longer than {{ limit }} characters.",
					]),
				],
			],
			'second_options' => [
				'label' => 'New Password (again)',
			],
		]);

		if ($currentUser->hasRole('ROLE_MODERATOR'))
		{
			$roles = [
				'1. Administrator' => 'ROLE_ADMIN',
				'2. Moderator' => 'ROLE_MODERATOR',
				'3. User (normal)' => 'ROLE_USER',
				'4. Banned' => 'ROLE_BANNED',
			];

			$builder->add('role', ChoiceType::class, [
				// The user should only have one role, so...
				'data' => $data['role'] ?? $user->getRoles()[0],

				'choices' => $roles,

				'choice_attr' => function($choice, $key, $value) use ($currentUser)
				{
					if (!in_array('ROLE_ADMIN', $currentUser->getRoles()))
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
					return 'Select an Access Level:';
				},
				'constraints' => [
					new Assert\Choice([
						'choices' => array_values($roles),
						'message' => 'The access level you selected is invalid.',
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