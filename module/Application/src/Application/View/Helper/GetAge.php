<?php

namespace Application\View\Helper;

use Zend\View\Helper\AbstractHelper;

class GetAge extends AbstractHelper
{
	private $_sm;

  public function __construct(\Zend\ServiceManager\ServiceManager $sm) {
    $this->_sm = $sm;
  }

  public function getSm() {
    return $this->_sm;
  }

	public function __invoke($date)
	{
		$today = date("Y-m-d");
		$diff = date_diff(date_create($date), date_create($today));

	 	return $diff->format('%y');
	}
}
