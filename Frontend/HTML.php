<?php
namespace SMGregsList\Frontend;
use SMGregsList\Messager, SMGregsList\Frontend, SMGregsList\SearchPlayer;
class HTML extends Messager implements Frontend
{
    protected $template;
    function __construct(HTMLController $controller = null)
    {
        if (null === $controller) {
            $controller = new HTMLController;
        }
        $this->addDependency($controller);
        $this->template = new \PEAR2\Templates\Savant\Main(array(
            'template_path' => realpath(__DIR__ . '/../templates'),
            'escape' => 'htmlentities',
        ));
    }

    function listMessages()
    {
        return parent::listMessages(array('ready', 'searchResults'));
    }

    function receive($message, $content)
    {
        if ($message == 'ready') {
            // display html output
            $this->displayMainPage();
        } elseif ($message == 'searchResults') {
            if (!is_array($content)) {
                throw new \Exception('internal Error: results of search is not an array');
            }
            foreach ($content as $player) {
                if (!($player instanceof Player)) {
                    throw new \Exception('internal Error: one of the results of search is not a Player object');
                }
            }
            $this->displaySearchResults($content);
        }
    }

    function displayMainPage()
    {
        $this->discoverSearch();
        $this->displaySearchForm();
    }

    function discoverSearch()
    {
        $this->broadcast('detectSearch');
    }

    function displaySearchForm()
    {
        echo $this->template->render(new SearchPlayer);
    }

    function displaySearchResults(array $results)
    {
        if (!count($results)) {
            echo "<p><strong>No results</strong></p>\n";
        }
    }
}