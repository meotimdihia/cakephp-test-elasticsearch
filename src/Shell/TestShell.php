<?php

namespace Cake\Shell;

use Cake\Console\Shell;
use Cake\ORM\TableRegistry;

class TestShell extends Shell {

	public function main()
	{
		set_time_limit(0);
		$logs = TableRegistry::get('LogLogins');
		
		$params = array();
		$params['hosts'] = array (
			'192.168.26.112:9200',
		);

		$client = new \Elasticsearch\Client($params);

		# create a index
		$params = [
			'index' => 'mobgame',
			'body' => [
				'settings' => [
					'number_of_shards' => 3,
					'number_of_replicas' => 1,
				],

				'mappings' => [
					'log_logins' => [
						'properties' => [
							'user_id' => [
								'type' => 'integer'
							],
							'created' => [
								'type' => 'date',
								'format' => 'date_time'
							],
							'modified' => [
								'type' => 'date',
								'format' => 'date_time'
							],
							'os' => [
								'type' => 'string',
								'index' => 'not_analyzed'
							],
							'resolution' => [
								'type' => 'string'
							],
							'sdk_ver' => [
								'type' => 'string',
								'index' => 'not_analyzed'
							],
							'game_id' => [
								'type' => 'integer'
							],
							'g_ver' => [
								'type' => 'string'
							],
							'device' => [
								'type' => 'string',
								'index' => 'not_analyzed'
							],
							'network' => [
								'type' => 'string',
								'index' => 'not_analyzed'
							],
							'ip' => [
								'type' => 'string',
								'index' => 'not_analyzed'
							],
							'distributor' => [
								'type' => 'string',
								'index' => 'not_analyzed'
							]
						]	
					]
				]
			]
		];
		try {
			$client->indices()->create($params);
			$params['index'] = 'mobgame';
			$params['type']  = 'log_logins';

			$ret = $client->indices()->getMapping(array(
				'index' => 'mobgame',
				'type' => 'log_logins'
			));
			
		} catch (Exception $e) {
			echo $e->getMessage();
		}

		$i = 0;
		$offset = 2;
		do {
			$this->out('Fetching data... ');
			$this->out('Offset: ' . $offset * $i);
			$query = $logs->find()->where(['id >=' => $i * $offset, 'id <' => ($i + 1) * $offset]);
			$docs = array();
			if ($query->count() == 0) {
				break;
			}
			foreach ($query as $row) {

				$data = $row->toArray();

				unset($data['id']);
				$data['created'] = $data['created']->toISO8601String();
				$data['modified'] = $data['modified']->toISO8601String();
				$docs['body'] = $data;
				$docs['index'] = 'mobgame';
				$docs['type']  = 'log_logins';
				debug($docs);
				$client->index($docs);
				
				$i++;
				die();
			}
			
		} while(true);
	}
}