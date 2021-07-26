<?php

namespace App\Controller\Admin;

use App\Entity\News;
use App\Form\NewsType;
use App\Repository\NewsRepository;
use DateTime;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/news")
 */
class AdminNewsController extends AbstractController
{
    /**
     * @Route("/", name="admin_news_index", methods={"GET"})
     * @param NewsRepository $newsRepository
     * @return Response
     */
    public function index(NewsRepository $newsRepository): Response
    {
        return $this->render('Admin/news/index.html.twig', [
            'news' => $newsRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="admin_news_new", methods={"GET","POST"})
     * @param Request $request
     * @return Response
     * @throws Exception
     */
    public function new(Request $request): Response
    {
        $news = new News();
        $form = $this->createForm(NewsType::class, $news);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $news->setCreatedAt(new DateTime());
            $entityManager->persist($news);
            $entityManager->flush();
            $this->addFlash("success", "flash.news.createSuccessfully");
            return $this->redirectToRoute('admin_news_index');
        }

        return $this->render('Admin/news/new.html.twig', [
            'news' => $news,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="admin_news_show", methods={"GET"})
     * @param News $news
     * @return Response
     */
    public function show(News $news): Response
    {
        return $this->render('Admin/news/show.html.twig', [
            'news' => $news,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="admin_news_edit", methods={"GET","POST"})
     * @param Request $request
     * @param News $news
     * @return Response
     */
    public function edit(Request $request, News $news): Response
    {
        $form = $this->createForm(NewsType::class, $news);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash("success", "flash.news.updateSuccessfully");
            return $this->redirectToRoute('admin_news_index');
        }

        return $this->render('Admin/news/edit.html.twig', [
            'news' => $news,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="admin_news_delete", methods={"DELETE", "POST"})
     * @param Request $request
     * @param News $news
     * @return Response
     */
    public function delete(Request $request, News $news): Response
    {
        if ($this->isCsrfTokenValid('delete' . $news->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($news);
            $entityManager->flush();
            $this->addFlash("success", "flash.news.deleteSuccessfully");
        }

        return $this->redirectToRoute('admin_news_index');
    }
}
