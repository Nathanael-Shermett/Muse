<?php

namespace App\Form;

use App\Entity\User;
use App\Form\Model\EditProfileModel;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\Validator\Constraints as SecurityAssert;

class EditProfileType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$data = $builder->getData();
		$user = $options['user'];

		$builder->add('currentPassword', PasswordType::class, [
			'mapped'      => FALSE,
			'attr'        => ['required' => 'required', 'autocomplete' => 'current-password'],
			'label'       => 'Please enter your current password.',
			'constraints' => [
				new SecurityAssert\UserPassword(['message' => 'The password you entered is incorrect.']),
			],
		])->add('username', TextType::class, [
			'data'        => $data['username'] ?? $user->getUsername(),
			'constraints' => [
				new Assert\Length([
					'min'        => 2,
					'max'        => 25,
					'minMessage' => "Your username must be at least {{ limit }} characters long.",
					'maxMessage' => "Your username cannot be longer than {{ limit }} characters.",
				]),
			],
		])->add('email', EmailType::class, [
			'data'        => $data['email'] ?? $user->getEmail(),
			'constraints' => [
				new Assert\Email(['message' => 'Please enter a valid email address.']),
				new Assert\Length([
					'min'        => 6,
					'max'        => 190,
					'minMessage' => "Your email address cannot be shorter than {{ limit }} characters long.",
					'maxMessage' => "Your email address cannot be longer than {{ limit }} characters.",
				]),
			],
		])->add('plainPassword', RepeatedType::class, [
			'type'            => PasswordType::class,
			'invalid_message' => 'The provided passwords did not match.',
			'first_options'   => [
				'attr'        => ['autocomplete' => 'new-password'],
				'label'       => 'New Password',
				'constraints' => [
					new Assert\Length([
						'min'        => 8,
						'max'        => 4096,
						'minMessage' => "For security reasons, your password must be at least {{ limit }} characters long.",
						'maxMessage' => "Your password cannot be longer than {{ limit }} characters.",
					]),
				],
			],
			'second_options'  => [
				'label' => 'New Password (again)',
			],
		]);
	}

	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults([
			'data_class' => EditProfileModel::class,
			'required'   => FALSE,
			'user'       => User::class,
		]);
	}
}