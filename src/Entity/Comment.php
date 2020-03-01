<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints\DateTime;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CommentRepository")
 */
class Comment
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="text")
	 */
	private $content;

	/**
	 * @ORM\ManyToOne(targetEntity="App\Entity\Post", inversedBy="comments")
	 * @ORM\JoinColumn(nullable=false)
	 */
	private $post;

	/**
	 * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="comments")
	 * @ORM\JoinColumn(nullable=false)
	 */
	private $user;

	/**
	 * @ORM\Column(type="datetime", options={"default": "CURRENT_TIMESTAMP"})
	 */
	private $timestamp;

	/**
	 * @ORM\Column(type="boolean")
	 */
	private $deleted;

	public function getId(): ?int
	{
		return $this->id;
	}

	public function getContent(): ?string
	{
		return $this->content;
	}

	public function setContent(string $content): self
	{
		$this->content = $content;

		return $this;
	}

    public function getPost(): ?Post
    {
        return $this->post;
    }

    public function setPost(?Post $post): self
    {
        $this->post = $post;

        return $this;
    }

    public function getUser(): ?User
    {
		return $this->user;
	}

	public function setUser(?User $user): self
	{
		$this->user = $user;

		return $this;
	}

	public function getTimestamp(): ?\DateTimeInterface
	{
		return $this->timestamp;
	}

	public function setTimestamp(\DateTimeInterface $timestamp): self
	{
		$this->timestamp = $timestamp;

		return $this;
	}

	public function getDeleted(): ?bool
	{
		return $this->deleted;
	}

	// Alias of getDeleted()
	public function deleted(): ?bool
	{
		return $this->getDeleted();
	}

	public function setDeleted(bool $deleted): self
	{
		$this->deleted = $deleted;

		return $this;
	}
}
