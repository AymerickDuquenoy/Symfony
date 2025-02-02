<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Article;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\Entity;
use App\Form\ArticleType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class ArticleController extends AbstractController
{
    #[Route('/article', name: 'app_article')]
    public function index(): Response
    {
        return $this->render('article/index.html.twig', [
            'controller_name' => 'ArticleController',
        ]);
    }

    #[Route('/article/generate', name: 'generate_article')]
    public function generateArticlet(EntityManagerInterface $entityManager): Response
    {
    $article = new Article();
    $str_now = date('Y-m-d H:i:s', time());
    $article->setTitre('Titre aleatoire #'.$str_now);
    $content = file_get_contents('http://loripsum.net/api');
    $article->setTexte($content);
    $article->setPublie(true);
    $article->setDate(\DateTimeImmutable::createFromFormat('Y-m-d H:i:s',
    $str_now));
    // tell Doctrine you want to (eventually) save the Product (no queries yet)
    $entityManager->persist($article);
    // actually executes the queries (i.e. the INSERT query)
    $entityManager->flush();
    return new Response('Saved new article with id '.$article->getId());
    }


    #[Route('/article/liste',name:'liste_article')]
    public function listeArticle(EntityManagerInterface $entityManager): Response
    {
        // Fetch all articles from the database
        $liste_articles = $entityManager->getRepository(Article::class)->findAll();
        $this->addFlash('success', 'Articles loaded !');
    
        // Render the list of articles using Twig template
        return $this->render('article/liste.html.twig',['liste_articles' => $liste_articles,]);
    }  


    #[Route('/article/show/{id}',name:'findArticle')]
    public function findArticle(EntityManagerInterface $entityManager, string $id): Response
    {
        // Fetch one articles from the database
        $findArticle = $entityManager->getRepository(Article::class)->find($id);

        $this->addFlash('success', 'Article loaded !');
    
        // Render the article using Twig template
        return $this->render('article/find.html.twig',['findArticle' => $findArticle,]);
    }  

    #[Route('/article/new', name: 'article_new')]
    public function new(EntityManagerInterface $entityManager, Request $request): Response
    {
        // Crée un objet Article et initialise certaines données pour cet exemple
        $article = new Article();
        $article->setTitre('Which Title ?');
        $article->setTexte('And which content ?');
        $now = new \DateTimeImmutable();
        $article->setDate($now); // Utilise \DateTimeImmutable directement
    
        // Crée le formulaire lié à l'objet Article
        $form = $this->createForm(ArticleType::class, $article);
    
        // Gère la soumission du formulaire
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($article);
            $entityManager->flush();
    
            // Redirige vers la liste des articles après la création
            return $this->redirectToRoute('liste_article');
        }
    
        // Renvoie la vue du formulaire
        return $this->render('article/new.html.twig', [
            'form' => $form->createView(),
            'article' => $article,
        ]);
    }

    #[Route('/article/edit/{id}', name: 'article_edit')]
    public function edit(int $id, EntityManagerInterface $entityManager, Request $request): Response
    {
        // Récupérer l'article à modifier depuis la base de données en fonction de l'ID
        $article = $entityManager->getRepository(Article::class)->find($id);
    
        // Vérifier si l'article existe
        if (!$article) {
            throw $this->createNotFoundException('Article not found');
        }
    
        // Crée le formulaire lié à l'article existant
        $form = $this->createForm(ArticleType::class, $article);
    
        // Gère la soumission du formulaire
        $form->handleRequest($request);
    
        // Si le formulaire est soumis et valide, on met à jour l'article dans la base de données
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush(); // Pas besoin de persist() car l'article est déjà dans la base
    
            // Redirige vers la liste des articles après la modification
            return $this->redirectToRoute('liste_article');
        }
    
        // Renvoie la vue du formulaire avec l'article à modifier
        return $this->render('article/edit.html.twig', [
            'form' => $form->createView(),
            'article' => $article,
        ]);
    }


    #[IsGranted('ROLE_ARTICLE_ADMIN')]
    #[Route('/article/delete/{id}', name: 'article_delete')]
public function delete(int $id, EntityManagerInterface $entityManager): Response
{
    // Récupérer l'article à supprimer depuis la base de données en fonction de l'ID
    $article = $entityManager->getRepository(Article::class)->find($id);

    // Vérifier si l'article existe
    if (!$article) {
        throw $this->createNotFoundException('Article not found');
    }

    // Supprimer l'article
    $entityManager->remove($article);
    $entityManager->flush(); // Sauvegarder la suppression

    // Rediriger vers la liste des articles après la suppression
    return $this->redirectToRoute('liste_article');
}

    
    
}
