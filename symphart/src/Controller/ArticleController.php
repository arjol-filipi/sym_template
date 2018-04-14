<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use App\Entity\Article;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class ArticleController extends Controller{
    /**
     * @Route("/",name="article_list")
     */
    public function index(){
        $articles = $this->getDoctrine()->getRepository(Article::class)->findAll();
        //return new Response('<html><body>Hello</body></html>');
        return $this->render('articles/index.html.twig',array(
            'articles'=>$articles
        ));
    }

    // /**
    //  * @Route("/save")
    //  */
    // public function save(){
    //     $entityManager = $this->getDoctrine()->getManager() ;
    //     $article = new Article();
    //     $article->setTitle('Article 2');
    //     $article->setBody('Body for article  2');
    //     $entityManager->persist($article);
    //     $entityManager->flush();
    //     return new Response('Saved in Article with the id of '. $article->getId());
    // }
        /**
         * @Route("article/{id}",name="article_show" , requirements={"id"="\d+"})
         */
        public function show($id){
            $article = $this->getDoctrine()->getRepository(Article::class)->find($id);
            return $this->render('articles/show.html.twig',array(
                'article' => $article
            ));
        }
        /**
         * @Route("/article/edit/{id}", name="edit_article")
         * Method({"GET","POST"})
         */
        public function edit(Request $request,$id){
            $article = new Article();
            $article = $this->getDoctrine()->getRepository(Article::class)->find($id);
            $form = $this->createFormBuilder($article)->add('title',TextType::class,array(
                'attr' => array(
                    'class' => 'form-control'
                )
            ))->add('body',TextareaType::class,array(
                'required' => false,
                'attr' => array(
                    'class' => 'form-control'
                )
            ) )->add('save',SubmitType::class,array(
                'label' => 'Update',
                'attr' => array(
                    'class' => 'btn btn-primary mt-3'
                )
            ))->getForm()  ;

            $form->handleRequest($request);
            if($form->isSubmitted() && $form->isValid() ){
                
                $entityManager = $this->getDoctrine()->getManager();
                
                $entityManager->flush();
                return $this->redirectToRoute("article_list");
            }
            return $this->render('articles/edit.html.twig',array(
                'form' => $form->createView()
            ));
        }
        /**
         * @Route("/article/delete/{id}",name="article_delete")
         * @Method("DELETE")
         */
        public function delete(Request $request,$id){
            $article = $this->getDoctrine()->getRepository(Article::class)->find($id);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($article);
            $entityManager->flush();
            $response = new Response;
            $response->send();
        }
         /**
         * @Route("/article/new", name="new_article")
         * Method({"GET","POST"})
         */
        public function new(Request $request){
            $article = new Article();
            $form = $this->createFormBuilder($article)->add('title',TextType::class,array(
                'attr' => array(
                    'class' => 'form-control'
                )
            ))->add('body',TextareaType::class,array(
                'required' => false,
                'attr' => array(
                    'class' => 'form-control'
                )
            ) )->add('save',SubmitType::class,array(
                'label' => 'create',
                'attr' => array(
                    'class' => 'btn btn-primary mt-3'
                )
            ))->getForm()  ;

            $form->handleRequest($request);
            if($form->isSubmitted() && $form->isValid() ){
                $article = $form->getData();
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($article);
                $entityManager->flush();
                return $this->redirectToRoute("article_list");
            }
            return $this->render('articles/new.html.twig',array(
                'form' => $form->createView()
            ));
        }
}