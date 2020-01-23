<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PostRepository")
 */
class Post
{
	/**
	 * @ORM\Id()
	 * @ORM\GeneratedValue()
	 * @ORM\Column(type="integer")
	 */
	private $id;

	/**
	 * @ORM\Column(type="string", length=80)
	 */
	private $title;

	/**
	 * @ORM\Column(type="text")
	 */
	private $content;

	/**
	 * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="posts")
	 * @ORM\JoinColumn(nullable=false)
	 */
	private $user;

	/**
	 * @ORM\OneToMany(targetEntity="App\Entity\Comment", mappedBy="post", orphanRemoval=true)
	 */
	private $comments;

	/**
	 * @ORM\Column(type="text", nullable=true)
	 */
	private $abstract;

	/**
	 * @ORM\ManyToMany(targetEntity="App\Entity\PostCategory", inversedBy="posts")
	 * @ORM\JoinTable(name="`post|post_category`")
	 * @Assert\Count(
	 *      min = 1,
	 *      max = 2,
	 *      minMessage = "You must choose at least one (1) category.",
	 *      maxMessage = "You may not choose more than two (2) categories."
	 * )
	 */
	private $categories;

    /**
     * @ORM\Column(type="datetime")
     */
    private $timestamp;

	public function __construct()
         	{
         		$this->comments = new ArrayCollection();
         		$this->categories = new ArrayCollection();
         	}

	public function getId(): ?int
         	{
         		return $this->id;
         	}

	public function getTitle(): ?string
         	{
         		return $this->title;
         	}

	public function setTitle(string $title): self
         	{
         		$this->title = $title;
         
         		return $this;
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

	public function getUser(): ?User
         	{
         		return $this->user;
         	}

	public function setUser(?User $user): self
         	{
         		$this->user = $user;
         
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
         			$comment->setPost($this);
         		}
         
         		return $this;
         	}

	public function removeComment(Comment $comment): self
         	{
         		if ($this->comments->contains($comment))
         		{
         			$this->comments->removeElement($comment);
         			// set the owning side to null (unless already changed)
         			if ($comment->getPost() === $this)
         			{
         				$comment->setPost(NULL);
         			}
         		}
         
         		return $this;
         	}

	public function getAbstract(): ?string
         	{
         		return $this->abstract;
         	}

	public function setAbstract(?string $abstract): self
         	{
         		$this->abstract = $abstract;
         
         		return $this;
         	}

	/**
	 * @return Collection|PostCategory[]
	 */
	public function getCategories(): Collection
         	{
         		return $this->categories;
         	}

	public function addCategories(PostCategory $categories): self
         	{
         		if (!$this->categories->contains($categories))
         		{
         			$this->categories[] = $categories;
         		}
         
         		return $this;
         	}

	public function removeCategories(PostCategory $categories): self
         	{
         		if ($this->categories->contains($categories))
         		{
         			$this->categories->removeElement($categories);
         		}
         
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
}
