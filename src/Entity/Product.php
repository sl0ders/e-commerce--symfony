<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ProductRepository")
 * @Vich\Uploadable
 * @ApiResource(
 *      normalizationContext={"groups"={"products_read"}},
 * )
 */
class Product
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"products_read"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"products_read"})
     */
    private $name;

    /**
     * @ORM\Column(type="text")
     * @Groups({"products_read"})
     */
    private $description;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"products_read"})
     */
    private $filenamePng;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"products_read"})
     */
    private $filenameJpg;

    /**
     * @ORM\Column(type="float")
     * @Groups({"products_read"})
     */
    private $price;

    /**
     * @Assert\All({
     *   @Assert\Image(mimeTypes="image/jpeg")
     * })
     */
    private $pictureFiles;

    /**
     * @Assert\All({
     *   @Assert\Image(mimeTypes="image/png")
     * })
     */
    private $pictureFilesPng;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Stock", mappedBy="product", cascade={"persist", "remove"})
     */
    private $stock;

    /**
     * @ORM\Column(type="datetime")
     * @Groups({"products_read"})
     */
    private $updated_at;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\News", mappedBy="product")
     */
    private $news;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Picture", mappedBy="product", orphanRemoval=true, cascade={"persist"})
     */
    private $pictures;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Groups({"products_read"})
     */
    private $quantity;

    /**
     * @ORM\Column(type="boolean")
     * @Groups({"products_read"})
     */
    private $enabled = true;

    /**
     * @ORM\ManyToOne(targetEntity=Package::class, inversedBy="products")
     */
    private $package;

    public function __construct()
    {
        $this->news = new ArrayCollection();
        $this->pictures = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getStock(): ?Stock
    {
        return $this->stock;
    }

    public function setStock(Stock $stock): self
    {
        $this->stock = $stock;

        // set the owning side of the relation if necessary
        if ($stock->getProduct() !== $this) {
            $stock->setProduct($this);
        }

        return $this;
    }

    /**
     * @return Collection|News[]
     */
    public function getNews(): Collection
    {
        return $this->news;
    }

    public function addNews(News $news): self
    {
        if (!$this->news->contains($news)) {
            $this->news[] = $news;
            $news->setProduct($this);
        }

        return $this;
    }

    public function removeNews(News $news): self
    {
        if ($this->news->contains($news)) {
            $this->news->removeElement($news);
            // set the owning side to null (unless already changed)
            if ($news->getProduct() === $this) {
                $news->setProduct(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Picture[]
     */
    public function getPictures(): Collection
    {
        return $this->pictures;
    }

    /**
     *
     */
    public function getPicture(): ?Picture
    {
        if ($this->pictures->isEmpty()) {
            return null;
        }
        return $this->pictures->first();
    }

    public function addPicture(Picture $picture): self
    {
        if (!$this->pictures->contains($picture)) {
            $this->pictures[] = $picture;
            $picture->setProduct($this);
        }

        return $this;
    }

    public function removePicture(Picture $picture): self
    {
        if ($this->pictures->contains($picture)) {
            $this->pictures->removeElement($picture);
            // set the owning side to null (unless already changed)
            if ($picture->getProduct() === $this) {
                $picture->setProduct(null);
            }
        }

        return $this;
    }

    /**
     * @return mixed
     */
    public function getPictureFiles()
    {
        return $this->pictureFiles;
    }

    /**
     * @param mixed $pictureFiles
     * @return Product
     */
    public function setPictureFiles($pictureFiles): self
    {
        foreach ($pictureFiles as $pictureFile) {
            $picture = new Picture();
            $picture->setImageFile($pictureFile);
            $this->addPicture($picture);
        }
        $this->pictureFiles = $pictureFiles;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPictureFilesPng()
    {
        return $this->pictureFilesPng;
    }

    /**
     * @param mixed $pictureFilesPng
     * @return Product
     */
    public function setPictureFilesPng($pictureFilesPng): self
    {
        foreach ($pictureFilesPng as $pictureFilePng) {
            $picture = new Picture();
            $picture->setImageFile($pictureFilePng);
            $this->addPicture($picture);
        }
        $this->pictureFilesPng = $pictureFilesPng;
        return $this;
    }

    public function getUpdatedAt(): ?DateTimeInterface
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(DateTimeInterface $updated_at): self
    {
        $this->updated_at = $updated_at;

        return $this;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(?int $quantity): self
    {
        $this->quantity = $quantity;

        return $this;
    }

    /**
     * @param mixed $filenamePng
     * @return Product
     */
    public function setFilenamePng($filenamePng)
    {
        $this->filenamePng = $filenamePng;
        return $this;
    }

    /**
     * @param mixed $filenameJpg
     * @return Product
     */
    public function setFilenameJpg($filenameJpg)
    {
        $this->filenameJpg = $filenameJpg;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getFilenamePng()
    {
        return $this->filenamePng;
    }

    /**
     * @return mixed
     */
    public function getFilenameJpg()
    {
        return $this->filenameJpg;
    }

    public function __toString(): string
    {
        return $this->name;
    }

    public function getEnabled(): ?bool
    {
        return $this->enabled;
    }

    public function setEnabled(bool $enabled): self
    {
        $this->enabled = $enabled;

        return $this;
    }

    public function getPackage(): ?package
    {
        return $this->package;
    }

    public function setPackage(?package $package): self
    {
        $this->package = $package;

        return $this;
    }
}
