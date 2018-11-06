<?php

namespace App\Controller;

use App\Entity\Product;
use App\Entity\Receipt;
use App\Entity\ReceiptRow;
use App\Repository\ReceiptRepository;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * @Route("/api/receipt")
 */
class ReceiptController extends Controller
{
    /**
     * @Route("/", name="receipt_index", methods="GET")
     * @Security("has_role('ROLE_ADMIN', 'ROLE_USER')" )
     */
    public function index(ReceiptRepository $receiptRepository): Response
    {
        $data = $this->get('jms_serializer')->serialize($receiptRepository->findAll(), 'json');
        return new Response($data, 200);
    }

    /**
     * @Route("/new", name="receipt_new", methods="POST")
     * @Security("has_role('ROLE_USER')" )
     */
    public function new(Request $request): Response
    {
        $receipt = new Receipt();

        $barcode = $request->request->get('receipt')['rows']['product'];
        $product = $this->getDoctrine()->getRepository(Product::class)->findOneBy([
            'barcode' => $barcode
        ]);
        if ($product) {
            $receiptRow = new ReceiptRow();
            $receiptRow->addProduct($product);
            $this->getDoctrine()->getManager()->persist($receiptRow);
            $receipt->addRow($receiptRow);

            $em = $this->getDoctrine()->getManager();
            $em->persist($receipt);
            $em->flush();

            $data = $this->get('jms_serializer')->serialize($receipt, 'json');

            return new Response($data, 200);
        }
        throw new Exception('Product not found!');
    }

    /**
     * @Route("/{id}", name="receipt_show", methods="GET")
     * @Security("has_role('ROLE_USER')" )
     */
    public function show(Receipt $receipt): Response
    {
        return new Response($this->get('jms_serializer')->serialize($receipt, 'json'), 200);
    }

    /**
     * @Route("/{id}/edit", name="receipt_edit", methods="POST")
     * @Security("has_role('ROLE_USER' ) or has_role('ROLE_ADMIN' )" )
     */
    public function edit(Request $request, Receipt $receipt): Response
    {
        if ($receipt->getFinished() == false) {
            $existedRows = $receipt->getRows();
            $existedProducts = [];
            foreach ($existedRows as $existedRow){
                foreach ($existedRow->getProducts() as $data){
                    $existedProducts[$data->getBarcode()] = $existedRow->getId();
                }
            }

            foreach ($request->request->get('receipt')['rows'] as $index => $data) {
                foreach ($data as $barcode => $amount){
                    if($amount != 0){
                        if(isset($existedProducts[$barcode])){
                            $receiptRow = $this->getDoctrine()->getRepository(ReceiptRow::class)->find($existedProducts[$barcode]);
                            $receiptRow->setAmount($amount == 1 ? $receiptRow->getAmount() + $amount : $amount);
                        } else{
                            if($product = $this->getDoctrine()->getRepository(Product::class)->findOneBy(['barcode' => $barcode])){
                                $receiptRow = new ReceiptRow();
                                $receiptRow->addProduct($product);
                                $receipt->addRow($receiptRow);
                            } else throw new Exception('Product not found!');
                        }
                        $this->getDoctrine()->getManager()->persist($receiptRow);
                    } elseif(in_array('ROLE_ADMIN', $this->getUser()->getRoles())){
                        $receipt->removeRow($this->getDoctrine()->getRepository(ReceiptRow::class)->find($existedProducts[$barcode]));
                        $this->getDoctrine()->getManager()->persist($receipt);
                    } else throw new Exception('You have not access to remove product from receipt. Ask admin');
                }
            }
            $this->getDoctrine()->getManager()->flush();
            return new Response($this->get('jms_serializer')->serialize($receipt, 'json'), 200);
        }
    }

    /**
     * @Route("/{id}", name="receipt_delete", methods="DELETE")
     * @Security("has_role('ROLE_ADMIN')" )
     */
    public function delete(Request $request, Receipt $receipt): Response
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($receipt);
        $em->flush();

        return new Response('OK');
    }
}
