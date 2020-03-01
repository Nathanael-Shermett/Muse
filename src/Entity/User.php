<?php
// src/Entity/User.php
namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Table(name="`user`")
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @UniqueEntity(
 *     fields="username",
 *     message="This username is already taken."
 * )
 * @UniqueEntity(
 *     fields="email",
 *     message="This email address is already in use."
 * )
 */
class User implements UserInterface, \Serializable
{
	/**
	 * @ORM\Column(type="integer")
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	private $id;

	/**
	 * @ORM\Column(type="string", length=190, unique=true)
	 */
	private $email;

	/**
	 * @ORM\Column(type="string", length=25, unique=true)
	 */
	private $username;

	/**
	 * @ORM\Column(type="string", length=64)
	 */
	private $password;

	/**
	 */
	private $plainPassword;

	/**
	 * @ORM\Column(type="array")
	 */
	private $roles;

	/**
	 * @ORM\Column(name="is_active", type="boolean")
	 */
	private $isActive;

	/**
	 * @ORM\OneToMany(targetEntity="App\Entity\Post", mappedBy="user", orphanRemoval=true)
	 */
	private $posts;

	/**
	 * @ORM\OneToMany(targetEntity="App\Entity\Comment", mappedBy="user")
	 */
	private $comments;

	/**
	 * @ORM\Column(type="datetimetz")
	 */
	private $user_since;

	/**
	 * User constructor.
	 */
	public function __construct()
	{
		$this->roles = ['ROLE_USER'];
		$this->isActive = TRUE;
		$this->posts = new ArrayCollection();
		$this->comments = new ArrayCollection();
	}

	/**
	 * @return integer
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * @return string
	 */
	public function getEmail()
	{
		return $this->email;
	}

	public function setEmail($email)
	{
		$this->email = $email;
	}

	/**
	 * @return string
	 */
	public function getUsername()
	{
		return $this->username;
	}

	public function setUsername($username)
	{
		$this->username = $username;
	}

	/**
	 * @return null
	 */
	public function getSalt()
	{
		// No salt needed since we use bcrypt.
		return NULL;
	}

	/**
	 * @return string
	 */
	public function getPassword()
	{
		return $this->password;
	}

	public function setPassword($password)
	{
		$this->password = $password;
	}

	/**
	 * @return string
	 */
	public function getPlainPassword()
	{
		return $this->plainPassword;
	}

	public function setPlainPassword($password)
	{
		$this->plainPassword = $password;
	}

	/**
	 * @return array
	 */
	public function getRoles()
	{
		return $this->roles;
	}

	/**
	 * Returns TRUE if the user has a role, otherwise FALSE. Utilizes hierarchical role structure, such that:
	 * - `ROLE_ADMIN` = [`ROLE_ADMIN`, `ROLE_MODERATOR`, `ROLE_USER`]
	 * - `ROLE_MODERATOR` = [`ROLE_MODERATOR`, `ROLE_USER`]
	 * - `ROLE_USER` = [`ROLE_USER`]
	 * - `ROLE_BANNED` = [`ROLE_BANNED`]
	 *
	 * @return boolean
	 */
	public function hasRole($role)
	{
		// If the user is an admin, they're also moderators and users.
		if (in_array('ROLE_ADMIN', $this->roles))
		{
			return ($role == 'ROLE_ADMIN' || $role == 'ROLE_MODERATOR' || $role == 'ROLE_USER') ? TRUE : FALSE;
		}

		// If the user is a moderator, then're also a user.
		if (in_array('ROLE_MODERATOR', $this->roles))
		{
			return ($role == 'ROLE_MODERATOR' || $role == 'ROLE_USER') ? TRUE : FALSE;
		}

		// If the user is a user, then... you get the picture.
		if (in_array('ROLE_USER', $this->roles))
		{
			return ($role == 'ROLE_USER') ? TRUE : FALSE;
		}

		// If the user is a user, then... you get the picture.
		if (in_array('ROLE_BANNED', $this->roles))
		{
			return ($role == 'ROLE_BANNED') ? TRUE : FALSE;
		}
	}

	public function setRole($role)
	{
		$this->roles = (is_array($role)) ? $role : [$role];
	}

	/**
	 *
	 */
	public function eraseCredentials()
	{
	}

	/**
	 * @see \Serializable::serialize()
	 */
	public function serialize()
	{
		return serialize([
			$this->id,
			$this->username,
			$this->password,
		]);
	}

	/**
	 * @param string $serialized
	 * @see \Serializable::unserialize()
	 */
	public function unserialize($serialized)
	{
		[
			$this->id,
			$this->username,
			$this->password,
		] = unserialize($serialized, ['allowed_classes' => FALSE]);
	}

	/**
	 * @return Collection|Post[]
	 */
	public function getPosts(): Collection
	{
		return $this->posts;
	}

	public function addPost(Post $post): self
	{
		if (!$this->posts->contains($post))
		{
			$this->posts[] = $post;
			$post->setUser($this);
		}

		return $this;
	}

	public function removePost(Post $post): self
	{
		if ($this->posts->contains($post))
		{
			$this->posts->removeElement($post);
			// set the owning side to null (unless already changed)
			if ($post->getUser() === $this)
			{
				$post->setUser(NULL);
			}
		}

		return $this;
	}

	/**
	 * @return Collection|Comment[]
	 */
	public function getComments(): Collection
	{
		return $this->comments;
	}

	public function addComment(Comment $comment): self
	{
		if (!$this->comments->contains($comment))
		{
			$this->comments[] = $comment;
			$comment->setUser($this);
		}

		return $this;
	}

	public function removeComment(Comment $comment): self
	{
		if ($this->comments->contains($comment))
		{
			$this->comments->removeElement($comment);
			// set the owning side to null (unless already changed)
			if ($comment->getUser() === $this)
			{
				$comment->setUser(NULL);
			}
		}

		return $this;
	}

	public function getUserSince(): ?\DateTimeInterface
	{
		return $this->user_since;
	}

	public function setUserSince(\DateTimeInterface $user_since): self
	{
		$this->user_since = $user_since;

		return $this;
	}

}