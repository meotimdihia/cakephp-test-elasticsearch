<?php

namespace App\Controller;

use Cake\ORM\TableRegistry;

class TestController extends AppController {

	public function index()
	{
		$articles = TableRegistry::get('LogLogins');
		$i = 0;

		while($query = $articles->find()->where(['id >=' => $i * 100, 'id <' => ($i + 1) * 100])) {
			foreach ($query as $row) {
				debug($row);
			}
			$i++;
		}
	}

}


?>