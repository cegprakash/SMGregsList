<?php
namespace SMGregsList\Frontend;
use SMGregsList\Messager, SMGregsList\Frontend, SMGregsList\SearchPlayer, SMGregsList\Player, SMGregsList\SellPlayer;
class HTML extends Messager implements Frontend
{
    protected $template;
    protected $searchresults = array();
    protected $searchfor;
    protected $verifyphase = false;
    protected $confirmphase = false;
    protected $deleted = false;
    protected $body;

    function __construct(HTMLController $controller = null)
    {
        if (null === $controller) {
            $controller = new HTMLController;
        }
        $this->searchfor = new SearchPlayer;
        $this->sellplayer = new SellPlayer;
        $this->addDependency($controller);
        $this->template = new \PEAR2\Templates\Savant\Main(array(
            'template_path' => realpath(__DIR__ . '/../templates'),
            'escape' => 'htmlentities',
        ));
    }

    function getBody()
    {
        return $this->body;
    }

    function listMessages(array $newmessages)
    {
        return parent::listMessages(array('ready', 'searchResult', 'search', 'playerAdded', 'sellDetected',
                                          'verify', 'confirm', 'playerRemoved'));
    }

    function receive($message, $content)
    {
        if ($message == 'ready') {
            // display html output
            $phpSelf = filter_input(INPUT_SERVER, 'PHP_SELF', FILTER_SANITIZE_URL);
            if (strpos($phpSelf, 'sell.php')) {
                $this->discoverSell();
                $this->setupSellPage();
                $this->subtitle = 'List a Player for sale';
            } else {
                $this->discoverSearch();
                $this->body = new SearchResults($this->searchfor, $this->searchresults);
                $this->subtitle = 'Search for Players for sale';
            }
            echo $this->template->render($this);
        } elseif ($message == 'searchResult') {
            if (!is_array($content)) {
                throw new \Exception('internal Error: results of search is not an array');
            }
            foreach ($content as $player) {
                if (!($player instanceof Player)) {
                    throw new \Exception('internal Error: one of the results of search is not a Player object');
                }
            }
            $this->searchresults = $content;
        } elseif ($message == 'search') {
            $this->searchfor = $content;
        } elseif ($message == 'playerAdded' || $message == 'sellDetected') {
            $this->sellplayer = $content;
        } elseif ($message == 'verify') {
            $this->verifyphase = true;
            $this->confirmphase = false;
        } elseif ($message == 'playerRemoved') {
            $this->sellplayer = $content;
            $this->deleted = true;
        } elseif ($message == 'confirm') {
            $this->confirmphase = true;
            $this->verifyphase = false;
        }
    }

    function setupSellPage()
    {
        if ($this->verifyphase) {
            $this->body = new Verify($this->sellplayer);
        } elseif ($this->confirmphase) {
            $this->body = new Listed($this->sellplayer);
        } elseif ($this->deleted) {
            $this->body = new Deleted($this->sellplayer);
        } else {
            $this->body = new Sell($this->sellplayer);
        }
    }

    function discoverSearch()
    {
        $this->searchresults = array();
        $this->broadcast('detectSearch');
    }

    function discoverSell()
    {
        $this->broadcast('detectSell');
    }
}