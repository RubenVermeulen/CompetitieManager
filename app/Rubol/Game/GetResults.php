<?php


namespace Rubol\Game;


class GetResults
{
    private $url;
    private $dom;
    private $matches = [];

    private function load() {
        /*
         * Remove all possible errors because of special characters which are not escaped.
         */
        libxml_use_internal_errors(true);

        /*
         * Fetch source HTML.
         */
        $src = file_get_contents($this->url);

        $this->dom->loadHTML($src);
    }

    public function setUrl($url) {
        $this->url = $url;
        $this->dom = new \DOMDocument();
        $this->load();
    }

    public function getResults() {
        /*
         * Search for data and store it in an array.
         */
        $content = $this->dom->getElementById("content");
        $tables = $content->getElementsByTagName("table");
        $table = $tables->item(1);
        $tbodys = $table->getElementsByTagName("tbody");
        $tbody = $tbodys->item(0);
        $trs = $tbody->childNodes;

        foreach ($trs as $tr) {
            $tds = $tr->childNodes;

            $result = $tds->item(4);

            $sets = explode(" ", $result->textContent);
            $sets = str_replace("-", " ", $sets);

            $this->matches[] = $sets;
        }

        /*
         * Print JSON format or array for Javascript use.
         */
        echo json_encode($this->matches);
    }

    public function getRankings() {
        $table = $this->dom->getElementById('container_content_ctl00_cphPage_cphPage_tblPlayerInformation');
        $trs = iterator_to_array($table->childNodes);

        $rankings = [];

        $rankings[] = $trs[4]->childNodes->item(1)->textContent;
        $rankings[] = $trs[5]->childNodes->item(1)->textContent;
        $rankings[] = $trs[6]->childNodes->item(1)->textContent;

        return $rankings;
    }
}