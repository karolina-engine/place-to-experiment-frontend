<?php

namespace Karolina;

Class WpAPI {

	private $apiUrl;
	private $client;

	public function __construct ($wpUrl) {

		$this->client = new \GuzzleHttp\Client();
		$this->apiUrl = $wpUrl."/wp-json/wp/v2";
	}

	public function getPostsIndex ($limit = 20) {
	
		$limit = (int) $limit;
		$res = $this->client->request('GET', $this->apiUrl.'/posts/?_embed=1&per_page='.$limit)->getBody();
		$wpPostsData = json_decode($res, true);

		$posts = array();

		foreach ($wpPostsData as $wpPost) {

			$post['title'] = $wpPost['title']['rendered'];
			$wpExcerpt = strip_tags($wpPost['excerpt']['rendered']);
			$wpExcerpt = str_replace("&hellip;", '[SPLITME]', $wpExcerpt);
			$wpExcerptSplit = explode("[SPLITME]", $wpExcerpt);
			$post['excerpt'] = $wpExcerptSplit[0];
			$post['id'] = $wpPost['id'];
			$post['date'] = $wpPost['date'];
			$post['date_formatted'] = date( "d.m.Y", strtotime($wpPost['date']) );
			$post['slug'] = $wpPost['slug'];

			if (isset($wpPost['_embedded']['wp:featuredmedia'][0]['media_details']['sizes']['full']['source_url'])) {

				$post['featured_image'] = $wpPost['_embedded']['wp:featuredmedia'][0]['media_details']['sizes']['full']['source_url'];

			} else {

				$post['featured_image'] = '';

			}

			$posts[] = $post;
		
		}

		return $posts;

	}

	public function getPost ($id) {

		$id = (int) $id;

		$res = $this->client->request('GET', $this->apiUrl.'/posts/'.$id.'?_embed=1');

		$wpData = json_decode($res->getBody(), true);

		if (isset($wpData['_embedded']['wp:featuredmedia'][0]['media_details']['sizes']['full']['source_url'])) {
			$data['featured_image'] = $wpData['_embedded']['wp:featuredmedia'][0]['media_details']['sizes']['full']['source_url'];
		} else {
			$data['featured_image'] = "";
		}
		$data['title'] = $wpData['title']['rendered'];
		$data['content'] = $wpData['content']['rendered'];

		return $data;

	}
}