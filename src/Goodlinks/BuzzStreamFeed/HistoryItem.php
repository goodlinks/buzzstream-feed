<?php

namespace Goodlinks\BuzzStreamFeed;

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