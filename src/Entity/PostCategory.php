<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PostCategoryRepository")
 */
class PostCategory
{
	/**
	 * @ORM\Id()
	 * @ORM\GeneratedValue()
	 * @ORM\Column(type="integer")
	 */
	private $id;

	/**
	 * @ORM\Column(type="string", length=30)
	 */
	private $supercategory;

	/**
	 * @ORM\Column(type="string", length=190)
	 */
	private $name;

	/**
	 * @ORM\Column(type="string", length=190, nullable=true)
	 */
	private $icon;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Post", mappedBy="categories")
     */
    private $posts;

	public function __construct()
             {
                 $this->posts = new ArrayCollection();
             }

	public function getId(): ?int
               	{
               		return $this->id;
               	}

	public function getSupercategory(): ?string
               	{
               		return $this->supercategory;
               	}

	public function setSupercategory(string $supercategory): self
               	{
               		$this->supercategory = $supercategory;
               
               		return $this;
               	}

	public function getName(): ?string
               	{
               		return $this->name;
               	}

	public function setName(string $name): self
               	{
               		$this->name = $name;
               
               		return $this;
               	}

	public function getIcon(): ?string
               	{
               		return $this->icon;
               	}

	public function setIcon(?string $icon): self
               	{
               		$this->icon = $icon;
               
               		return $this;
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
        if (!$this->posts->contains($post)) {
            $this->posts[] = $post;
            $post->addCategories($this);
        }

        return $this;
    }

    public function removePost(Post $post): self
    {
        if ($this->posts->contains($post)) {
            $this->posts->removeElement($post);
            $post->removeCategories($this);
        }

        return $this;
    }
}
