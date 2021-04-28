<?php

namespace User\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Mail\Message as Message;
use Zend\Mime\Message as MimeMessage;
use Zend\Mime\Part as MimePart;
use Zend\Mail\Transport\Smtp as SmtpTransport;
use Zend\Mail\Transport\SmtpOptions;
use Zend\Mail\Transport\Sendmail as Sendmail;

use User\Model\UserEntity;

use Gumlet\ImageResize;

class RegistrationController extends AbstractActionController
{
    public function getUserMapper()
    {
        $sm = $this->getServiceLocator();
        return $sm->get('UserMapper');
    }

    public function indexAction()
    {
        $config = $this->getServiceLocator()->get('Config');

        $form = $this->getServiceLocator()->get('RegistrationForm');
        $user = new UserEntity();
        $form->bind($user);

        if ($this->getRequest()->isPost()) {
            $data = $this->params()->fromPost();
            $form->setData($data);

            if ($form->isValid()) {
                $isError = false;

                $role = $this->getRequest()->getPost('role');
                $password = $this->getRequest()->getPost('password');

                if (!isset($_FILES['photo'])) {
                    $isError = true;
                    $form->get('photo')->setMessages(['Required field Company Logo.']);
                } else {
                    $allowed =  ['jpg'];
                    $ext = pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION);
                    if (!in_array($ext, $allowed)) {
                        $isError = true;
                        $form->get('photo')->setMessages(["File type not allowed. Only " . implode(',', $allowed)]);
                    }
                    switch ($_FILES['photo']['error']) {
                      case 1:
                        $isError = true;
                        $form->get('photo')->setMessages(['The file is bigger than this PHP installation allows.']);
                        break;
                      case 2:
                        $isError = true;
                        $form->get('photo')->setMessages(['The file is bigger than this form allows.']);
                        break;
                      case 3:
                        $isError = true;
                        $form->get('photo')->setMessages(['Only part of the file was uploaded.']);
                        break;
                      case 4:
                        $isError = true;
                        $form->get('photo')->setMessages(['No file was uploaded.']);
                        break;
                      default:
                    }
                }

                if (!$isError) {
                    $user->setCreatedUserId(0);
                    $user->setRole('member');
                    $user->setActive('Y');
                    $dynamicSalt = $this->getUserMapper()->dynamicSalt();
                    $user->setSalt($dynamicSalt);
                    $password = md5($config['staticSalt'] . $user->getPassword() . $dynamicSalt);
                    $user->setPassword($password);
                    $user->setReferralId($data['referral_id']);
                    $this->getUserMapper()->save($user);

                    $directory = $config['pathUserPhoto']['absolutePath'] . $user->getId();
                    if (!file_exists($directory)) {
                        mkdir($directory, 0755);
                    }

                    $ext = pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION);
                    $destination = $directory . "/photo-orig." . $ext;
                    if (!file_exists($destination)) {
                        move_uploaded_file($_FILES['photo']['tmp_name'], $destination);
                    }
                    $destination2 = $directory . "/photo-crop-750x450." . $ext;
                    if (file_exists($destination2)) {
                        unlink($destination2);
                    }
                    $image = new ImageResize($destination);
                    $image->crop(750, 450);
                    $image->save($destination2);
                    if (file_exists($destination)) {
                        unlink($destination);
                    }

                    $destination3 = $directory . "/photo-750x450." . $ext;
                    if (file_exists($destination3)) {
                        unlink($destination3);
                    }
                    $image = new ImageResize($destination2);
                    $image->resize(750, 450, $allow_enlarge = true);
                    $image->save($destination3);
                    if (file_exists($destination2)) {
                        unlink($destination2);
                    }

                    $subject = 'Thank you for registraion.';
                    $message = "Thank you for registraion " . $user->getFirstName() . "!";
                    $message .= "\nYour email is: " . $user->getEmail();
                    $message .= "\nYour passord is: " . $password;

                    /*
                    try {
                      $mail = new Message();
                      $mail->setFrom($config['email']);
                      $mail->addTo($user->getEmail());
                      $mail->setSubject($subject);
                      $mail->setBody($message);

                      // Send E-mail message
                      $transport = new Sendmail('-f'. $config['email']);
                      // $transport->send($mail);
                    } catch(\Exception $e) {
                    }
                    */

                    /*
                    $text = new MimePart($message);
                    $text->type = "text/plain";
                    $html = new MimePart($message);
                    $html->type = "text/html";
                    $body = new MimeMessage();
                    $body->setParts(array($text, $html));
                    $mail = new  Message();
                    $mail->setFrom('system@gigamike.net');
                    $mail->addTo($user->getEmail());
                    $mail->setEncoding("UTF-8");
                    $mail->setSubject($subject);
                    $mail->setBody($body);
                    $transport = new SmtpTransport();
                    $options   = new SmtpOptions($config['smtp']);
                    $transport->setOptions($options);
                    $transport->send($mail);
                    */

                    $this->flashMessenger()->setNamespace('success')->addMessage('Thank you for registraion.');
                    return $this->redirect()->toRoute('login');
                }
            }
        } else {
            $form->get('country_id')->setValue(182);
        }

        $referral_id = $this->serviceLocator->get('request')->getQuery()->r;

        return new ViewModel([
            'form' => $form,
            'config' => $config,
            'referral_id' => $referral_id,
        ]);
    }
}
