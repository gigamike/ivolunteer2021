<?php

namespace Cron\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Mail\Message as Message;
use Zend\Mime\Message as MimeMessage;
use Zend\Mime\Part as MimePart;
use Zend\Mail\Transport\Smtp as SmtpTransport;
use Zend\Mail\Transport\SmtpOptions;
use Zend\Mail\Transport\Sendmail as Sendmail;
use Zend\Console\Request as ConsoleRequest;
use Zend\Console\Response as ConsoleResponse;

class IndexController extends AbstractActionController
{
  /*
  *
  * /Applications/MAMP/bin/php/php7.1.31/bin/php /Users/michaelgerardgalon/Sites/hackathons/clashofcodes2019.gigamike.net/public_html/index.php cron-test
  * /usr/bin/php /var/www/clashofcodes2019.gigamike.net/public_html/index.php cron-test
  */
  public function indexAction()
  {
    $request = $this->getRequest();

    if (!$request instanceof ConsoleRequest){
      throw new \RuntimeException('You can only use this action from a console!');
    }

    $basePath = getcwd();

    // avoid duplicate instance
    $logfile = $basePath . "/data/temp/cron-test.txt";
    $fp = fopen($logfile, "w");
    if (flock($fp, LOCK_EX | LOCK_NB)) {
      set_time_limit(0);

      $serviceCurl = $this->getServiceLocator()->get('ServiceCurl');
      $results = $serviceCurl->curl("https://gigamike.net");
      print_r($results);

      flock($fp, LOCK_UN);
    } else {
      echo "script still running...\n";
    }
	}
}
