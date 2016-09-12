<?php

namespace GoodLinks\BuzzStreamFeed;

class Project extends ApiResource
{
    public function getName()
    {
        if (! $this->_resourceUrl) {
            throw new \Exception("Can't getName() because resourceUrl not set on this Project object");
        }

        $this->load($this->_resourceUrl);
        return $this->_data['name'];
    }

    public function fetchHistory()
    {
        $history = History::getList($this->getId());;
        return $history;
    }

    /**
     * @return Website[]
     * @throws \Exception
     */
    public function getWebsites()
    {
        $apiResourceModel = new Website;

        $params = array(
            'offset'        => 0,
            'max_results'   => 200,
            'project'       => $this->getId(),
        );

        $url = Api::$apiUrl . '/' . 'websites';
        $url .= '?' . http_build_query($params);

        $cachedResponse = $apiResourceModel->_getCachedRequest($url);
        if ($cachedResponse) {
            return $cachedResponse;
        }

        $objects = array();
        $apiResponse = $apiResourceModel->_request($url);
        if (! isset($apiResponse['list'])) {
            throw new \Exception("No 'list' found for URL ($url): " . print_r($apiResponse, 1));
        }

        foreach ($apiResponse['list'] as $resourceUrl) {
            $objects[] = (new Website)->load($resourceUrl);
        }

        // Cache the list for 1/10th of a day
        $apiResourceModel->_putCachedRequest($url, $objects, 0.1);

        return $objects;
    }

    public function calculateOldestCommunicationDateIfNotCached()
    {
        $projectId = $this->getId();
        $cacheKey = "project_{$projectId}_oldest_communication";
        $item = $this->_getCache()->getItem($cacheKey);

        $data = $item->get();
        if (! $item->isMiss()) {
            return $data;
        }

        $websites = $this->getWebsites();
        $oldestCommunicationDate = date('Y-m-d');

        foreach ($websites as $website) {
            $lastCommunicationDate = $website->getLastCommunicationDate();
            $oldestCommunicationDate = $lastCommunicationDate ? min($oldestCommunicationDate, $lastCommunicationDate) : $oldestCommunicationDate;
        }

        // Cache it
        $projectId = $this->getId();
        $cacheKey = "project_{$projectId}_oldest_communication";
        $item = $this->_getCache()->getItem($cacheKey);

        $hours = 1;
        $minutes = $hours * 60;
        $seconds = $minutes * 60;
        $item->set($oldestCommunicationDate, $seconds);

        return $oldestCommunicationDate;
    }

    public function getOldestCommunicationDate()
    {
        $projectId = $this->getId();
        $cacheKey = "project_{$projectId}_oldest_communication";
        $item = $this->_getCache()->getItem($cacheKey);

        $data = $item->get();
        return $data;
    }
}