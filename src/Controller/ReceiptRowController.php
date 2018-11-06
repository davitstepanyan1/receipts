<?php

namespace App\Controller;

use App\Entity\ReceiptRow;
use App\Form\ReceiptRowType;
use App\Repository\ReceiptRowRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * @Route("/receipt-row")
 */
class ReceiptRowController extends AbstractController
{
    /**
     * @Route("/", name="receipt_row_index", methods="GET")
     * @Security("has_role('ROLE_ADMIN', 'ROLE_USER')" )
     */
    public function index(ReceiptRowRepository $receiptRowRepository): Response
    {
        $data = $this->get('jms_serializer')->serialize($receiptRowRepository->findAll(), 'json');
        return new Response($data, 200);
    }

    /**
     * @Route("/new", name="receipt_row_new", methods="POST")
     * @Security("has_role('ROLE_ADMIN', 'ROLE_USER')" )
     */
    public function new(Request $request): Response
    {
        $receiptRow = new ReceiptRow();
        $form = $this->createForm(ReceiptRowType::class, $receiptRow, ['csrf_protection' => false]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($receiptRow);
            $em->flush();

            $data = $this->get('jms_serializer')->serialize($receiptRow, 'json');

            return new Response($data, 200);
        }
    }

    /**
     * @Route("/{id}", name="receipt_row_show", methods="GET")
     * @Security("has_role('ROLE_ADMIN', 'ROLE_USER')" )
     */
    public function show(ReceiptRow $receiptRow): Response
    {
        $data = $this->get('jms_serializer')->serialize($receiptRow, 'json');

        return new Response($data, 200);
    }

    /**
     * @Route("/{id}/edit", name="receipt_row_edit", methods="POST")
     * @Security("has_role('ROLE_ADMIN', 'ROLE_USER')" )
     */
    public function edit(Request $request, ReceiptRow $receiptRow): Response
    {
        $form = $this->createForm(ReceiptRowType::class, $receiptRow);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            $data = $this->get('jms_serializer')->serialize($receiptRow, 'json');

            return new Response($data, 200);
        }
    }

    /**
     * @Route("/{id}", name="receipt_row_delete", methods="DELETE")
     * @Security("has_role('ROLE_ADMIN')" )
     */
    public function delete(Request $request, ReceiptRow $receiptRow): Response
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($receiptRow);
        $em->flush();

        return new Response('OK');
    }
}
