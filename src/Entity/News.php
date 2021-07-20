<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Entity(repositoryClass="App\Repository\NewsRepository")
 * @Vich\Uploadable()
 * @ApiResource(
 *      normalizationContext={"groups"={"news_read"}},
 * )
 */
#[ApiResource]
class News
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"news_read"})
     */
    private $title;

    /**
     * @ORM\Column(type="text")
     * @Groups({"news_read"})
     */
    private $content;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"news_read"})
     */
    private $filename;

    /**
     * @Assert\Image(mimeTypes="image/jpeg")
     * @Vich\UploadableField(mapping="product_image", fileNameProperty="filename")
     * @Groups({"news_read"})
     * @var File|null
     */
    private $image;

    /**
     * @ORM\Column(type="datetime")
     * @Groups({"news_read"})
     */
    private $created_at;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Product", inversedBy="news")
     * @Groups({"news_read"})
     */
    private $product;

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

    public function getImage(): ?File
    {
        return $this->image;
    }

    public function setImage(?File $image = null): self
    {
        $this->image = $image;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeInterface $created_at): self
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getProduct(): ?Product
    {
        return $this->product;
    }

    public function setProduct(?Product $product): self
    {
        $this->product = $product;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getFilename()
    {
        return $this->filename;
    }

    /**
     * @param mixed $filename
     */
    public function setFilename($filename): void
    {
        $this->filename = $filename;
    }
}
