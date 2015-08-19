<?php

namespace AppBundle\Controller;

use AppBundle\Model\GuestBookFileModel;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/")
 * Class GuestBookController
 * @package AppBundle\Controller
 */
class GuestBookController extends Controller {

    protected $model;

    public function __construct(){
        $this->model = new GuestBookFileModel(); //Could be replaced by a Doctrine-using Model for exmaple
    }

    /**
     * @Route("/")
     */
    public function indexAction(){
        return $this->render('guestbook/index.html.twig', [
            'entries' => (array)$this->model->getAllEntries(),
        ]);
    }

    /**
     * @Route("/add")
     */
    public function addAction(){

        return $this->render('guestbook/add.html.twig', [
            'form' => $this->getGuestBookForm()->createView(),
        ]);
    }

    /**
     * @Route("/delete/{id}")
     */
    public function deleteAction($id){
        $this->model->deleteEntry($id);
        return $this->redirectToRoute('app_guestbook_index');
    }

    /**
     * @Route("/save")
     */
    public function saveAction(){

        $objFactory = $this->getGuestBookForm();

        $objFactory->handleRequest(Request::createFromGlobals());

        if ($objFactory->isValid()) {
            $data = $objFactory->getData();

            /* creating new Entry-Object and add to Entry-index.
            Has to be replaced by a Class (e.g. Entry) with its Attributes "Username" and "Message"
            to solve it in a proper way */

            $objStd = new \stdClass();
            $objStd->Username = $data['Username'];
            $objStd->Message = $data['Message'];

            $this->model->addNewEntry($objStd);
        }
        return $this->redirectToRoute('app_guestbook_index');
    }

    /**
     * Returns an Instance of the generated Form to add an GuestBook Entry
     * @return \Symfony\Component\Form\Form
     */
    protected function getGuestBookForm(){
        return $this->createFormBuilder(null, [
            'action' => $this->generateUrl('app_guestbook_save'),
            'method' => 'POST',
        ])->add('Username', 'text')->add('Message', 'textarea')->add('save', 'submit')->getForm();
    }

}