<?php

namespace App\Services;

use App\Entity\News;
use App\Entity\Package;
use App\Entity\Product;
use App\Entity\Stock;
use App\Form\ProductType;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class AddProductService
{
    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $entityManager;
    private TranslatorInterface $translator;

    public function __construct(EntityManagerInterface $entityManager, TranslatorInterface $translator)
    {
        $this->entityManager = $entityManager;
        $this->translator = $translator;
    }

    public function addProduct($product,$news, $stock, $form, $request)
    {
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($form->get("conditioning")->getData() != null) {
                $package = new Package();
                $package->setConditioning($form->get("conditioning")->getData());
                $package->setQuantity($form->get("packageValue")->getData());
                $package->setUnity($form->get("unity")->getData());
            } else {
                $package = $form->get("package")->getData();
            }
            $this->entityManager->persist($package);
            $stock->setProduct($product);
            $stock->setQuantity($form->get("quantity")->getData());
            $stock->setMajAt(new DateTime());
            $product->setUpdatedAt(new DateTime());
            $product->setFilenameJpg(strtolower($form->getData()->getPictureFiles()[0]->getClientOriginalName()));
            $product->setFilenamePng(strtolower($form->getData()->getPictureFilesPng()[0]->getClientOriginalName()));
            $product->setPackage($package);
            $news->setProduct($product);
            $news->setCreatedAt(new DateTime());
            $news->setTitle($this->translator->trans("news.product.arrival", ["%product%" => $product->getName()], "NegasProjectTrans"));
            $news->setEnabled(true);
            $this->entityManager->persist($news);
            $this->entityManager->persist($product);
            $this->entityManager->persist($stock);
            $this->entityManager->flush();
        }
    }
}
