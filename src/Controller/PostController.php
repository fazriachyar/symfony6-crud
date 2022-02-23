<?php

namespace App\Controller;

use App\Entity\Post;
use App\Form\PostType;
use App\Repository\PostRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;

#[Route('/post', name: 'post')]
class PostController extends AbstractController
{
    #[Route('/', name: 'index',)]
    public function index(PostRepository $postrepository): Response
    {
        $posts =  $postrepository->findAll();

        return $this->render('post/index.html.twig', [
            'posts' => $posts
        ]);
    }

    #[Route('/create', name: 'create')]
    public function create(Request $request, EntityManagerInterface $em) {
        //membuat post dengan title
        $post = new Post();
        // $post->setTitle('ini adalah judul postingan');  manual

        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        // $form->getErrors();
        if ($form->isSubmitted() ){
            //entity manager
            $em->persist($post);
            $em->flush();
            return $this->redirect($this->generateUrl(route:'postindex'));
        }
        // return new Response(content: 'Judul berhasil ditambahkan');
        return $this->render('/post/create.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/show/{id}', name:'show')]
    public function show(Post $post){
        // $post = $postRepository->find($id);
        // dump($post); die;

        return $this->render('post/show.html.twig', [
            'post' => $post
        ]);
    }

    #[Route('/edit/{id}', name:'edit')]
    public function edit(Request $request, Post $post,EntityManagerInterface $em){

        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);
        
        if($form->isSubmitted()){
            $em->persist($post);
            $em->flush();
            return $this->redirect($this->generateUrl(route:'postindex'));
        }
        return $this->render('post/edit.html.twig', [
            'post' => $post,
            'form' =>$form->createView()
        ]);
    }

    #[Route('/delete/{id}', name:'delete')]
    public function delete(Post $post, EntityManagerInterface $em){
        $em->remove($post);
        $em->flush();

        $this->addFlash(type:'success', message:'Postingan telah dihapus');
        return $this->redirect($this->generateUrl(route:'postindex'));
        
    }
}
