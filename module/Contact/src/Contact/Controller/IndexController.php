<?php

namespace Contact\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Mail\Message as Message;
use Zend\Mail\Transport\Sendmail as Sendmail;
use Zend\Mail\Transport\Smtp as SmtpTransport;
use Zend\Mail\Transport\SmtpOptions;

use Contact\Form\ContactForm;
use Contact\Form\ContactFilter;

class IndexController extends AbstractActionController
{
    public function indexAction()
    {
        $form = new ContactForm();
        $request = $this->getRequest();
        if ($request->isPost()) {
            $filter = new ContactFilter();
            $form->setInputFilter($filter);
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $data = $form->getData();

                $config = $this->getServiceLocator()->get('Config');

                $mail = new  Message();

                $subject = "Inquiry.";
                $message = "Full Name: " . $data['name'];
                $message .= "\nPhone Number: " . $data['phone'];
                $message .= "\nMessage: " . $data['message'];
                $message .= "\nIP: " . $_SERVER['REMOTE_ADDR'];

                $mail->setFrom($data['email']);
                $mail->addTo($config['email']);
                $mail->setEncoding("UTF-8");
                $mail->setSubject($subject);
                $mail->setBody($message);

                // Setup SMTP transport using LOGIN authentication
                $transport = new SmtpTransport();
                $options   = new SmtpOptions([
                    'name'              => $config['smtp']['host'],
                    'host'              => $config['smtp']['host'],
                    'port'              => $config['smtp']['port'],
                    'connection_class'  => 'login',
                    'connection_config' => [
                        'username' => $config['smtp']['connection_config']['username'],
                        'password' => $config['smtp']['connection_config']['password'],
                    ],
                ]);
                $transport->setOptions($options);
                $transport->send($mail);

                // $transport = new Sendmail();
                // $transport->send($mail);

                $this->flashMessenger()->setNamespace('success')->addMessage('Thank you. Email sent.');
                return $this->redirect()->toRoute('contact');
            } else {
                //print_r($form->getMessages());
            }
        }

        return new ViewModel([
            'form' => $form,
        ]);
    }
}
