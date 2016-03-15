<?php

namespace GoodLinks\BuzzStreamFeed;



class HistoryItem extends ApiResource
{
    public function getWebsiteNamesCsv()
    {
        $names = array();

        if (! $this->_resourceUrl) {
            throw new \Exception("Can't getWebsiteUrl() because resourceUrl not set on this HistoryItem object");
        }

        $this->load($this->_resourceUrl);
        foreach ($this->_data['associatedWebsites'] as $websiteResourceUrl) {
            $website = new Website();
            $website->load($websiteResourceUrl);
            $names[] = $website->_data['name'];
        }

        return implode(", ", $names);
    }

    public function getBuzzstreamProjectIds()
    {
        if (! $this->_resourceUrl) {
            throw new \Exception("Can't getBuzzstreamProjectIds() because resourceUrl not set on this HistoryItem object");
        }

        $this->load($this->_resourceUrl);
        $projectIds = array();

        /**
         * Relationship Stage changes and Notes should only be associated to the project they
         * belong to.  But things like emails and tweets get associated to any project
         * that the website is in
         */
        if (in_array($this->getType(), array('Stage', 'Note'))) {
            $projectUrl = $this->_data['project'];
            $projectId = $this->_resourceUrlToId($projectUrl);
            return array($projectId);
        }

        foreach ($this->_data['associatedWebsites'] as $websiteResourceUrl) {
            $website = new Website();
            $website->load($websiteResourceUrl);

            if ($website->_data['projectStates']) {
                $projectStates = new ProjectStates();
                $projectStates->load($website->_data['projectStates']);

                foreach ($projectStates->_data as $projectStateData) {
                    $projectId = $this->_resourceUrlToId($projectStateData['project']);
                    $projectIds[] = $projectId;
                    $projectIds = array_unique($projectIds);
                }
            }
        }

        return $projectIds;
    }

    public function getBuzzstreamWebsiteIds()
    {
        if (! $this->_resourceUrl) {
            throw new \Exception("Can't getBuzzstreamWebsiteIds() because resourceUrl not set on this HistoryItem object");
        }

        $this->load($this->_resourceUrl);
        $websiteIds = array();

        foreach ($this->_data['associatedWebsites'] as $websiteResourceUrl) {
            $websiteId = $this->_resourceUrlToId($websiteResourceUrl);
            $websiteIds[] = $websiteId;
        }

        return $websiteIds;
    }

    public function getBuzzstreamOwnerId()
    {
        if (! $this->_resourceUrl) {
            throw new \Exception("Can't getBuzzstreamOwnerId() because resourceUrl not set on this HistoryItem object");
        }

        $this->load($this->_resourceUrl);
        $ownerApiUrl = $this->_data['owner'];

        $buzzStreamUserId =  $this->_resourceUrlToId($ownerApiUrl);
        return $buzzStreamUserId;
    }

    public function getAvatarUrl()
    {
        if (! $this->_resourceUrl) {
            throw new \Exception("Can't getWebsiteUrl() because resourceUrl not set on this HistoryItem object");
        }

        $this->load($this->_resourceUrl);
        foreach ($this->_data['associatedWebsites'] as $websiteResourceUrl) {
            $website = new Website();
            $website->load($websiteResourceUrl);
            $avatarUrl = $website->getAvatarUrl();
            if ($avatarUrl) {
                return $avatarUrl;
            }
        }

        return "https://app.buzzstream.com/img/default_avatar_media.png";
    }

    public function getDate()
    {
        $this->load($this->_resourceUrl);
        $date = date('M d, Y', $this->_data['createdDate'] / 1000);

        return $date;
    }

    public function getSummary()
    {
        $this->load($this->_resourceUrl);
        return $this->_data['summary'];
    }

    public function getBody()
    {
        $this->load($this->_resourceUrl);
        return $this->_data['body'];
    }

    public function getHistoryItemApiUrl()
    {
        $this->load($this->_resourceUrl);
        return $this->_data['uri'];
    }

    public function getCreatedAt()
    {
        $this->load($this->_resourceUrl);
        $date = date("Y-m-d G:i:s", $this->_data['createdDate'] / 1000);

        return $date;
    }

    public function getType()
    {
        $this->load($this->_resourceUrl);
        return $this->_data['type'];
    }

    public function getBuzzstreamId()
    {
        $apiUrl = $this->getResourceUrl();
        return $this->_resourceUrlToId($apiUrl);
    }

    protected function _resourceUrlToId($url)
    {
        $parts = explode("/", $url);
        if (empty($parts)) {
            throw new \Exception("Problem parsing api url for history item: $url");
        }

        return $parts[count($parts) - 1];
    }

    public function getProjectName()
    {
        if (! $this->_resourceUrl) {
            throw new \Exception("Can't getProjectName() because resourceUrl not set on this HistoryItem object");
        }

        $this->load($this->_resourceUrl);
        if (! $this->_data['project']) {
            return "(No Project Associated)";
        }

        $project = new Project();
        $project->load($this->_data['project']);

        return $project->getName();
    }
}