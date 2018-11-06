<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\ProductType;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * @Route("/api/product")
 */
class ProductController extends Controller
{
    /**
     * @Route("/", name="product_index", methods="GET")
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function index(ProductRepository $productRepository): Response
    {
        $serializer = $this->get('jms_serializer');
        $products = $productRepository->findAll();
        $data = $serializer->serialize($products, 'json');
        return new Response($data, 200);
    }

    /**
     * @Route("/new", name="product_new", methods="POST")
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function new(Request $request): Response
    {
        $product = new Product();
        $form = $this->createForm(ProductType::class, $product, ['csrf_protection' => false]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($product);
            $em->flush();
            $data = $this->get('jms_serializer')->serialize($product, 'json');

            return new Response($data, 200);
        }
    }

    /**
     * @Route("/{id}", name="product_show", methods="GET")
     */
    public function show(Product $product): Response
    {
        $data = $this->get('jms_serializer')->serialize($product, 'json');
        return new Response($data, 200);
    }

    /**
     * @Route("/{id}/edit", name="product_edit", methods="POST")
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function edit(Request $request, Product $product): Response
    {
        $form = $this->createForm(ProductType::class, $product, ['csrf_protection' => false]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            $data = $this->get('jms_serializer')->serialize($product, 'json');
            return new Response($data, 200);
        }
    }

    /**
     * @Route("/{id}", name="product_delete", methods="DELETE")
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function delete(Request $request, Product $product): Response
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($product);
        $em->flush();

        return new Response('OK');
    }
}
